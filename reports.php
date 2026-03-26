<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// ─── DB CONFIG ────────────────────────────────────────────────────────────────
$host   = "localhost";
$dbname = "quick_care";
$user   = "root";
$pass   = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

// ─── ROUTING ──────────────────────────────────────────────────────────────────
$type       = $_GET['type']       ?? '';
$period     = $_GET['period']     ?? 'monthly';
$date_from  = $_GET['date_from']  ?? date('Y-m-01');
$date_to    = $_GET['date_to']    ?? date('Y-m-d');
$doctor_id  = $_GET['doctor_id']  ?? null;
$status     = $_GET['status']     ?? null;

function buildDateWhere($field, $period, $date_from, $date_to) {
    if ($date_from && $date_to && $date_from !== date('Y-m-01') || $date_to !== date('Y-m-d')) {
        return "AND $field BETWEEN '$date_from' AND '$date_to'";
    }
    switch ($period) {
        case 'daily':   return "AND DATE($field) = CURDATE()";
        case 'weekly':  return "AND $field >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        case 'monthly': return "AND YEAR($field) = YEAR(CURDATE()) AND MONTH($field) = MONTH(CURDATE())";
        default:        return "AND $field BETWEEN '$date_from' AND '$date_to'";
    }
}

switch ($type) {
    case 'appointment': getAppointmentReport($pdo, $period, $date_from, $date_to, $doctor_id, $status); break;
    case 'doctor':      getDoctorReport($pdo, $period, $date_from, $date_to); break;
    case 'payment':     getPaymentReport($pdo, $period, $date_from, $date_to); break;
    case 'patient':     getPatientReport($pdo, $period, $date_from, $date_to, $doctor_id); break;
    case 'doctors_list':getDoctorsList($pdo); break;
    default:
        echo json_encode(["error" => "Invalid report type. Use: appointment, doctor, payment, patient"]);
}

// ─── APPOINTMENT REPORT ───────────────────────────────────────────────────────
function getAppointmentReport($pdo, $period, $date_from, $date_to, $doctor_id, $status) {
    $dateWhere  = buildDateWhere('a.APPOINTMENT_DATE', $period, $date_from, $date_to);
    $docFilter  = $doctor_id ? "AND a.DOCTOR_ID = :doctor_id" : "";
    $statFilter = $status    ? "AND a.STATUS = :status" : "";

    // Summary counts
    $sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN a.STATUS='COMPLETED'  THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN a.STATUS='SCHEDULED'  THEN 1 ELSE 0 END) as scheduled,
                SUM(CASE WHEN a.STATUS='CANCELLED'  THEN 1 ELSE 0 END) as cancelled
            FROM appointment_tbl a
            WHERE 1=1 $dateWhere $docFilter $statFilter";
    $stmt = $pdo->prepare($sql);
    if ($doctor_id) $stmt->bindParam(':doctor_id', $doctor_id);
    if ($status)    $stmt->bindParam(':status', $status);
    $stmt->execute();
    $summary = $stmt->fetch();

    // Doctor-wise breakdown
    $sql2 = "SELECT 
                CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) as doctor_name,
                s.SPECIALISATION_NAME as specialisation,
                COUNT(*) as total,
                SUM(CASE WHEN a.STATUS='COMPLETED' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN a.STATUS='SCHEDULED' THEN 1 ELSE 0 END) as scheduled,
                SUM(CASE WHEN a.STATUS='CANCELLED' THEN 1 ELSE 0 END) as cancelled
             FROM appointment_tbl a
             JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
             JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
             WHERE 1=1 $dateWhere $docFilter $statFilter
             GROUP BY a.DOCTOR_ID, doctor_name, specialisation
             ORDER BY total DESC";
    $stmt2 = $pdo->prepare($sql2);
    if ($doctor_id) $stmt2->bindParam(':doctor_id', $doctor_id);
    if ($status)    $stmt2->bindParam(':status', $status);
    $stmt2->execute();
    $by_doctor = $stmt2->fetchAll();

    // Detailed rows
    $sql3 = "SELECT 
                a.APPOINTMENT_ID,
                CONCAT(p.FIRST_NAME,' ',p.LAST_NAME) as patient_name,
                CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) as doctor_name,
                s.SPECIALISATION_NAME as specialisation,
                a.APPOINTMENT_DATE,
                a.APPOINTMENT_TIME,
                a.STATUS,
                a.CREATED_AT
             FROM appointment_tbl a
             JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
             JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
             JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
             WHERE 1=1 $dateWhere $docFilter $statFilter
             ORDER BY a.APPOINTMENT_DATE DESC, a.APPOINTMENT_TIME DESC";
    $stmt3 = $pdo->prepare($sql3);
    if ($doctor_id) $stmt3->bindParam(':doctor_id', $doctor_id);
    if ($status)    $stmt3->bindParam(':status', $status);
    $stmt3->execute();
    $rows = $stmt3->fetchAll();

    // Trend (daily counts for chart)
    $sql4 = "SELECT DATE(a.APPOINTMENT_DATE) as appt_date, COUNT(*) as count
             FROM appointment_tbl a
             WHERE 1=1 $dateWhere $docFilter
             GROUP BY DATE(a.APPOINTMENT_DATE)
             ORDER BY appt_date ASC";
    $stmt4 = $pdo->prepare($sql4);
    if ($doctor_id) $stmt4->bindParam(':doctor_id', $doctor_id);
    $stmt4->execute();
    $trend = $stmt4->fetchAll();

    echo json_encode(compact('summary', 'by_doctor', 'rows', 'trend'));
}

// ─── DOCTOR REPORT ────────────────────────────────────────────────────────────
function getDoctorReport($pdo, $period, $date_from, $date_to) {
    $dateWhere = buildDateWhere('a.APPOINTMENT_DATE', $period, $date_from, $date_to);

    // Per doctor summary
    $sql = "SELECT 
                d.DOCTOR_ID,
                CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) as doctor_name,
                s.SPECIALISATION_NAME as specialisation,
                d.EDUCATION,
                d.GENDER,
                COUNT(a.APPOINTMENT_ID) as total_appointments,
                COUNT(DISTINCT a.PATIENT_ID) as total_patients,
                SUM(CASE WHEN a.STATUS='COMPLETED' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN a.STATUS='SCHEDULED' THEN 1 ELSE 0 END) as scheduled,
                SUM(CASE WHEN a.STATUS='CANCELLED' THEN 1 ELSE 0 END) as cancelled
            FROM doctor_tbl d
            LEFT JOIN appointment_tbl a ON d.DOCTOR_ID = a.DOCTOR_ID $dateWhere
            JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
            WHERE d.STATUS = 'approved'
            GROUP BY d.DOCTOR_ID, doctor_name, s.SPECIALISATION_NAME, d.EDUCATION, d.GENDER
            ORDER BY total_appointments DESC";
    $rows = $pdo->query($sql)->fetchAll();

    // Specialisation-wise
    $sql2 = "SELECT 
                s.SPECIALISATION_NAME as specialisation,
                COUNT(DISTINCT d.DOCTOR_ID) as doctor_count,
                COUNT(a.APPOINTMENT_ID) as total_appointments,
                COUNT(DISTINCT a.PATIENT_ID) as total_patients
             FROM specialisation_tbl s
             LEFT JOIN doctor_tbl d ON s.SPECIALISATION_ID = d.SPECIALISATION_ID AND d.STATUS='approved'
             LEFT JOIN appointment_tbl a ON d.DOCTOR_ID = a.DOCTOR_ID $dateWhere
             GROUP BY s.SPECIALISATION_ID, s.SPECIALISATION_NAME";
    $by_specialisation = $pdo->query($sql2)->fetchAll();

    echo json_encode(compact('rows', 'by_specialisation'));
}

// ─── PAYMENT REPORT ───────────────────────────────────────────────────────────
function getPaymentReport($pdo, $period, $date_from, $date_to) {
    $dateWhere = buildDateWhere('py.PAYMENT_DATE', $period, $date_from, $date_to);

    // Summary
    $sql = "SELECT 
                COUNT(*) as total_transactions,
                SUM(CASE WHEN py.STATUS='COMPLETED' THEN py.AMOUNT ELSE 0 END) as total_revenue,
                SUM(CASE WHEN py.STATUS='FAILED'    THEN 1 ELSE 0 END) as failed_count,
                AVG(CASE WHEN py.STATUS='COMPLETED' THEN py.AMOUNT END) as avg_amount
            FROM payment_tbl py
            WHERE 1=1 $dateWhere";
    $summary = $pdo->query($sql)->fetch();

    // Payment mode breakdown
    $sql2 = "SELECT PAYMENT_MODE, COUNT(*) as count, SUM(AMOUNT) as revenue
             FROM payment_tbl py WHERE STATUS='COMPLETED' $dateWhere
             GROUP BY PAYMENT_MODE ORDER BY revenue DESC";
    $by_mode = $pdo->query($sql2)->fetchAll();

    // Daily revenue trend
    $sql3 = "SELECT DATE(PAYMENT_DATE) as pay_date, SUM(AMOUNT) as revenue, COUNT(*) as count
             FROM payment_tbl py WHERE STATUS='COMPLETED' $dateWhere
             GROUP BY DATE(PAYMENT_DATE) ORDER BY pay_date ASC";
    $trend = $pdo->query($sql3)->fetchAll();

    // Detailed rows
    $sql4 = "SELECT 
                py.PAYMENT_ID,
                py.TRANSACTION_ID,
                CONCAT(p.FIRST_NAME,' ',p.LAST_NAME) as patient_name,
                CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) as doctor_name,
                py.AMOUNT,
                py.PAYMENT_DATE,
                py.PAYMENT_MODE,
                py.STATUS
             FROM payment_tbl py
             JOIN appointment_tbl a ON py.APPOINTMENT_ID = a.APPOINTMENT_ID
             JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
             JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
             WHERE 1=1 $dateWhere
             ORDER BY py.PAYMENT_DATE DESC, py.CREATED_AT DESC";
    $rows = $pdo->query($sql4)->fetchAll();

    echo json_encode(compact('summary', 'by_mode', 'trend', 'rows'));
}

// ─── PATIENT REPORT ───────────────────────────────────────────────────────────
function getPatientReport($pdo, $period, $date_from, $date_to, $doctor_id) {
    $dateWhere = buildDateWhere('a.APPOINTMENT_DATE', $period, $date_from, $date_to);
    $docFilter = $doctor_id ? "AND a.DOCTOR_ID = :doctor_id" : "";

    // New patients in period
    $sql = "SELECT 
                COUNT(DISTINCT a.PATIENT_ID) as total_patients,
                COUNT(a.APPOINTMENT_ID) as total_appointments,
                ROUND(COUNT(a.APPOINTMENT_ID)/NULLIF(COUNT(DISTINCT a.PATIENT_ID),0),1) as avg_visits_per_patient
            FROM appointment_tbl a
            WHERE 1=1 $dateWhere $docFilter";
    $stmt = $pdo->prepare($sql);
    if ($doctor_id) $stmt->bindParam(':doctor_id', $doctor_id);
    $stmt->execute();
    $summary = $stmt->fetch();

    // Patient visit frequency
    $sql2 = "SELECT 
                CONCAT(p.FIRST_NAME,' ',p.LAST_NAME) as patient_name,
                p.GENDER,
                p.BLOOD_GROUP,
                COUNT(a.APPOINTMENT_ID) as visit_count,
                SUM(CASE WHEN a.STATUS='COMPLETED' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN a.STATUS='CANCELLED' THEN 1 ELSE 0 END) as cancelled,
                MAX(a.APPOINTMENT_DATE) as last_visit,
                GROUP_CONCAT(DISTINCT CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) SEPARATOR ', ') as doctors_visited
             FROM patient_tbl p
             JOIN appointment_tbl a ON p.PATIENT_ID = a.PATIENT_ID
             $docFilter
             WHERE 1=1 $dateWhere
             GROUP BY p.PATIENT_ID, patient_name, p.GENDER, p.BLOOD_GROUP
             ORDER BY visit_count DESC";
    $stmt2 = $pdo->prepare($sql2);
    if ($doctor_id) $stmt2->bindParam(':doctor_id', $doctor_id);
    $stmt2->execute();
    $rows = $stmt2->fetchAll();

    // Medicines prescribed (top medicines)
    $sql3 = "SELECT 
                mt.MEDICINE_NAME, mt.CATEGORY,
                COUNT(pm.MEDICINE_ID) as prescription_count
             FROM prescription_tbl pr
             JOIN appointment_tbl a ON pr.APPOINTMENT_ID = a.APPOINTMENT_ID
             JOIN prescription_medicine_tbl pm ON pr.PRESCRIPTION_ID = pm.PRESCRIPTION_ID
             JOIN medicine_tbl mt ON pm.MEDICINE_ID = mt.MEDICINE_ID
             WHERE 1=1 $dateWhere
             GROUP BY mt.MEDICINE_ID, mt.MEDICINE_NAME, mt.CATEGORY
             ORDER BY prescription_count DESC LIMIT 10";
    $top_medicines = $pdo->query($sql3)->fetchAll();

    // Doctor-wise patient visits
    $sql4 = "SELECT 
                CONCAT(d.FIRST_NAME,' ',d.LAST_NAME) as doctor_name,
                s.SPECIALISATION_NAME as specialisation,
                COUNT(DISTINCT a.PATIENT_ID) as unique_patients,
                COUNT(a.APPOINTMENT_ID) as total_visits
             FROM appointment_tbl a
             JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
             JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
             WHERE a.STATUS='COMPLETED' $dateWhere
             GROUP BY a.DOCTOR_ID, doctor_name, specialisation
             ORDER BY unique_patients DESC";
    $by_doctor = $pdo->query($sql4)->fetchAll();

    // Gender distribution
    $sql5 = "SELECT p.GENDER, COUNT(DISTINCT p.PATIENT_ID) as count
             FROM patient_tbl p
             JOIN appointment_tbl a ON p.PATIENT_ID = a.PATIENT_ID
             WHERE 1=1 $dateWhere
             GROUP BY p.GENDER";
    $gender_dist = $pdo->query($sql5)->fetchAll();

    // Blood group distribution
    $sql6 = "SELECT p.BLOOD_GROUP, COUNT(DISTINCT p.PATIENT_ID) as count
             FROM patient_tbl p
             JOIN appointment_tbl a ON p.PATIENT_ID = a.PATIENT_ID
             WHERE 1=1 $dateWhere
             GROUP BY p.BLOOD_GROUP ORDER BY count DESC";
    $blood_dist = $pdo->query($sql6)->fetchAll();

    echo json_encode(compact('summary', 'rows', 'top_medicines', 'by_doctor', 'gender_dist', 'blood_dist'));
}

// ─── DOCTORS LIST ─────────────────────────────────────────────────────────────
function getDoctorsList($pdo) {
    $rows = $pdo->query("SELECT DOCTOR_ID, CONCAT(FIRST_NAME,' ',LAST_NAME) as name FROM doctor_tbl WHERE STATUS='approved' ORDER BY name")->fetchAll();
    echo json_encode($rows);
}
?>
