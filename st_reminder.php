<?php
session_start();

// Check if user is logged in as a receptionist
if (!isset($_SESSION['RECEPTIONIST_ID'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';
 $receptionist_id = $_SESSION['RECEPTIONIST_ID'];

// Fetch receptionist data from database
 $receptionist_query = mysqli_query($conn, "SELECT * FROM receptionist_tbl WHERE RECEPTIONIST_ID = '$receptionist_id'");
 $receptionist = mysqli_fetch_assoc($receptionist_query);

// Handle prescription reminder creation (per medicine, with date range and multiple times)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_prescription_reminder'])) {
    $prescription_id = mysqli_real_escape_string($conn, $_POST['prescription_id']);
    $medicine_id     = isset($_POST['medicine_id']) ? (int) $_POST['medicine_id'] : 0;
    $start_date      = isset($_POST['start_date']) ? mysqli_real_escape_string($conn, $_POST['start_date']) : '';
    $end_date        = isset($_POST['end_date']) ? mysqli_real_escape_string($conn, $_POST['end_date']) : '';
    $times_raw       = isset($_POST['reminder_time']) ? $_POST['reminder_time'] : [];
    $time_remarks    = isset($_POST['reminder_remarks']) ? $_POST['reminder_remarks'] : [];
    $global_remarks  = mysqli_real_escape_string($conn, $_POST['remarks'] ?? '');

    if (!is_array($times_raw)) {
        $times_raw = [$times_raw];
    }
    if (!is_array($time_remarks)) {
        $time_remarks = [$time_remarks];
    }

    // Get patient ID from prescription
    $prescription_query = mysqli_query($conn, "SELECT p.PATIENT_ID
                                                FROM prescription_tbl pr 
                                                JOIN appointment_tbl a ON pr.APPOINTMENT_ID = a.APPOINTMENT_ID 
                                                JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID 
                                                WHERE pr.PRESCRIPTION_ID = '$prescription_id'");
    $prescription_data = mysqli_fetch_assoc($prescription_query);
    $patient_id = $prescription_data['PATIENT_ID'] ?? null;

    if ($patient_id && $start_date && $end_date) {
        // Base remarks with IDs and date range (shared for all times)
        $base_remarks = "PRESCRIPTION_ID:{$prescription_id}. MEDICINE_ID:{$medicine_id}. ";
        $base_remarks .= "Range: {$start_date} to {$end_date}. ";
        if ($global_remarks) {
            $base_remarks .= $global_remarks . ' ';
        }

        $insert_ok = true;
        foreach ($times_raw as $idx => $time_val) {
            $time_val = trim($time_val);
            if ($time_val === '') continue;

            $time_esc = mysqli_real_escape_string($conn, $time_val);
            // Per-time remark (default if none provided)
            $per_time_raw = '';
            if (isset($time_remarks[$idx])) {
                $per_time_raw = trim($time_remarks[$idx]);
            }
            $per_time_final = $per_time_raw !== '' ? $per_time_raw : 'Time to take your medicine';
            $per_time_esc   = mysqli_real_escape_string($conn, $per_time_final);

            $full_remarks = $base_remarks . " Time: {$time_val}. Message: {$per_time_esc}.";

            // Insert one row per (medicine, time) with the same date range
            $create_query = "INSERT INTO medicine_reminder_tbl (MEDICINE_ID, CREATOR_ROLE, CREATOR_ID, PATIENT_ID, START_DATE, END_DATE, REMINDER_TIME, REMARKS) 
                             VALUES ($medicine_id, 'RECEPTIONIST', '$receptionist_id', '$patient_id', '$start_date', '$end_date', '$time_esc', '$full_remarks')";

            if (!mysqli_query($conn, $create_query)) {
                $insert_ok = false;
                $error_message = "Error creating medicine reminder: " . mysqli_error($conn);
                break;
            }
        }

        if ($insert_ok) {
            $success_message = "Medicine reminders created successfully!";
        }
    } else {
        $error_message = "Missing patient or date range for reminder.";
    }
}

// Handle medicine reminder update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_medicine_reminder'])) {
    $reminder_id = mysqli_real_escape_string($conn, $_POST['reminder_id']);
    $reminder_time = mysqli_real_escape_string($conn, $_POST['reminder_time']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
    
    // Get appointment date from reminder (need to find associated appointment)
    $reminder_query = mysqli_query($conn, "SELECT mr.PATIENT_ID, mr.REMARKS FROM medicine_reminder_tbl mr WHERE mr.MEDICINE_REMINDER_ID = '$reminder_id'");
    $reminder_data = mysqli_fetch_assoc($reminder_query);
    
    if ($reminder_data) {
        $patient_id = $reminder_data['PATIENT_ID'];
        $existing_remarks = $reminder_data['REMARKS'] ?? '';
        $prefix_parts = [];
        if (preg_match('/PRESCRIPTION_ID:\s*(\d+)/', $existing_remarks, $m)) {
            $prefix_parts[] = 'PRESCRIPTION_ID:' . $m[1] . '.';
        }
        if (preg_match('/MEDICINE_ID:\s*(\d+)/', $existing_remarks, $m2)) {
            $prefix_parts[] = 'MEDICINE_ID:' . $m2[1] . '.';
        }
        $id_prefix = '';
        if (!empty($prefix_parts)) {
            $id_prefix = implode(' ', $prefix_parts) . ' ';
        }
        $duration_days = (int)$duration;
        $full_remarks = $id_prefix . "Duration: $duration days after appointment. " . ($remarks ? $remarks : '');
        
        $update_query = "UPDATE medicine_reminder_tbl SET REMINDER_TIME = '$reminder_time', REMARKS = '$full_remarks' WHERE MEDICINE_REMINDER_ID = '$reminder_id'";
            
        if (mysqli_query($conn, $update_query)) {
            $success_message = "Medicine reminder updated successfully!";
        } else {
            $error_message = "Error updating reminder: " . mysqli_error($conn);
        }
    } else {
        $error_message = "Reminder not found.";
    }
}

// Handle medicine reminder deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_medicine_reminder'])) {
    $reminder_id = mysqli_real_escape_string($conn, $_POST['reminder_id']);
    
    $delete_query = "DELETE FROM medicine_reminder_tbl WHERE MEDICINE_REMINDER_ID = '$reminder_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        $success_message = "Medicine reminder deleted successfully!";
    } else {
        $error_message = "Error deleting medicine reminder: " . mysqli_error($conn);
    }
}

// Search filters (from header form)
$search_type = isset($_POST['search_type']) ? mysqli_real_escape_string($conn, $_POST['search_type']) : '';
$search_term = isset($_POST['search_term']) ? mysqli_real_escape_string($conn, trim($_POST['search_term'])) : '';
$search_where = '';
if ($search_term !== '') {
    if ($search_type === 'patient') {
        $search_where = " AND (p.FIRST_NAME LIKE '%$search_term%' OR p.LAST_NAME LIKE '%$search_term%')";
    } elseif ($search_type === 'doctor') {
        $search_where = " AND (d.FIRST_NAME LIKE '%$search_term%' OR d.LAST_NAME LIKE '%$search_term%')";
    } elseif ($search_type === 'specialization') {
        $search_where = " AND s.SPECIALISATION_NAME LIKE '%$search_term%'";
    }
}

// Fetch completed appointments with prescription details (with search filter)
$completed_appointments_query = mysqli_query($conn, "
    SELECT a.APPOINTMENT_ID, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, a.DOCTOR_ID,
           p.PATIENT_ID, p.FIRST_NAME as PAT_FNAME, p.LAST_NAME as PAT_LNAME, p.PHONE, p.EMAIL,
           d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME,
           pr.PRESCRIPTION_ID, pr.ISSUE_DATE, pr.HEIGHT_CM, pr.WEIGHT_KG, pr.BLOOD_PRESSURE, 
           pr.DIABETES, pr.SYMPTOMS, pr.DIAGNOSIS, pr.ADDITIONAL_NOTES
    FROM appointment_tbl a
    JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    LEFT JOIN prescription_tbl pr ON a.APPOINTMENT_ID = pr.APPOINTMENT_ID
    WHERE a.STATUS = 'COMPLETED' AND pr.PRESCRIPTION_ID IS NOT NULL
    $search_where
    ORDER BY d.DOCTOR_ID, p.PATIENT_ID, a.APPOINTMENT_DATE DESC
");

// Fetch medicine reminders with appointment info
$medicine_reminders_query = mysqli_query($conn, "
    SELECT mr.*, p.FIRST_NAME as PAT_FNAME, p.LAST_NAME as PAT_LNAME,
           (SELECT MAX(a.APPOINTMENT_DATE) 
            FROM appointment_tbl a 
            JOIN prescription_tbl pr ON a.APPOINTMENT_ID = pr.APPOINTMENT_ID
            WHERE a.PATIENT_ID = mr.PATIENT_ID AND a.STATUS = 'COMPLETED'
           ) AS APPOINTMENT_DATE
    FROM medicine_reminder_tbl mr
    JOIN patient_tbl p ON mr.PATIENT_ID = p.PATIENT_ID
    WHERE mr.CREATOR_ROLE = 'RECEPTIONIST'
    ORDER BY mr.REMINDER_TIME
");

// Build map: [prescription_id][medicine_id] => reminder row (for reminders that have IDs in REMARKS)
$reminder_by_prescription_medicine = [];
// Track patients who have any active reminders
$patients_with_active_reminders = [];
$reminders_list = [];
while ($mr = mysqli_fetch_assoc($medicine_reminders_query)) {
    $reminders_list[] = $mr;
    // Track patients with any active reminders
    $patient_id = (int)$mr['PATIENT_ID'];
    $patients_with_active_reminders[$patient_id] = true;
    
    if (!empty($mr['REMARKS']) && preg_match('/PRESCRIPTION_ID:\s*(\d+)\. MEDICINE_ID:\s*(\d+)/', $mr['REMARKS'], $m)) {
        $pres_id = (int)$m[1];
        $med_id = (int)$m[2];
        if (!isset($reminder_by_prescription_medicine[$pres_id])) {
            $reminder_by_prescription_medicine[$pres_id] = [];
        }
        $reminder_by_prescription_medicine[$pres_id][$med_id] = $mr;
    }
}

// Group appointments by Doctor -> Patient -> Appointments (for card layout)
$by_doctor = [];
while ($row = mysqli_fetch_assoc($completed_appointments_query)) {
    $did = (int)$row['DOCTOR_ID'];
    $pid = (int)$row['PATIENT_ID'];
    if (!isset($by_doctor[$did])) {
        $by_doctor[$did] = [
            'doc_name' => $row['DOC_FNAME'] . ' ' . $row['DOC_LNAME'],
            'specialization' => $row['SPECIALISATION_NAME'],
            'patients' => []
        ];
    }
    if (!isset($by_doctor[$did]['patients'][$pid])) {
        $by_doctor[$did]['patients'][$pid] = [
            'name' => $row['PAT_FNAME'] . ' ' . $row['PAT_LNAME'],
            'phone' => $row['PHONE'],
            'email' => $row['EMAIL'],
            'appointments' => []
        ];
    }
    $by_doctor[$did]['patients'][$pid]['appointments'][] = $row;
}

// Get reminder data for editing
$edit_reminder_data = null;
if (isset($_POST['edit_reminder_id'])) {
    $edit_reminder_id = (int)$_POST['edit_reminder_id'];
    $edit_query = mysqli_query($conn, "SELECT * FROM medicine_reminder_tbl WHERE MEDICINE_REMINDER_ID = $edit_reminder_id");
    if ($edit_query && mysqli_num_rows($edit_query) > 0) {
        $edit_reminder_data = mysqli_fetch_assoc($edit_query);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Reminder - QuickCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --dark-blue: #072D44;
            --mid-blue: #064469;
            --soft-blue: #072D44;
            --light-blue: #9CCDD8;
            --gray-blue: #D0D7E1;
            --white: #ffffff;
            --card-bg: #F6F9FB;
            --primary-color: #1a3a5f;
            --secondary-color: #3498db;
            --accent-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
        }
        
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #F5F8FA;
            display: flex;
        }
        
        .main-content {
            margin-left: 240px;
            padding: 20px;
            width: calc(100% - 240px);
        }
        
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-blue);
            padding: 15px 20px;
            font-weight: 600;
            color: var(--primary-color);
            border-radius: 10px 10px 0 0 !important;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .btn-success {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-success:hover {
            background-color: #27ae60;
            border-color: #27ae60;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            padding: 8px 16px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-warning:hover {
            background-color: #e67e22;
            border-color: #e67e22;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        /* Set Reminder button - modern medical style */
        .btn-set-reminder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 18px;
            font-size: 0.9rem;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, #0d7a9c 0%, #064469 100%);
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(6, 68, 105, 0.35);
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .btn-set-reminder i {
            font-size: 1rem;
            line-height: 1;
        }
        .btn-set-reminder:hover {
            background: linear-gradient(135deg, #0e8fb5 0%, #072D44 100%);
            box-shadow: 0 6px 20px rgba(6, 68, 105, 0.45);
            transform: translateY(-1px);
        }
        .btn-set-reminder:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(6, 68, 105, 0.3);
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.875rem;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: none;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            color: #27ae60;
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            color: #c0392b;
        }
        
        .form-group {
            margin-bottom: 14px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .form-control[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
            opacity: 0.8;
        }
        
        .form-control select {
            cursor: pointer;
        }
        
        .form-text {
            display: block;
            margin-top: 5px;
            font-size: 0.875rem;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        /* Make create reminder modal a bit wider so content fits without scrolling */
        .modal-dialog.modal-reminder-lg {
            max-width: 720px;
            width: 90%;
        }
        
        .modal-header {
            border-radius: 15px 15px 0 0;
            border-bottom: none;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .patient-card {
            border-left: 5px solid var(--secondary-color);
            margin-bottom: 25px;
            padding: 25px;
            background: linear-gradient(to right, var(--white) 0%, var(--card-bg) 100%);
            border-radius: 0 12px 12px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .patient-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-left-color: var(--accent-color);
        }
        
        .patient-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .patient-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .patient-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .patient-detail {
            display: flex;
            align-items: center;
            color: #666;
        }
        
        .patient-detail i {
            margin-right: 8px;
            color: var(--secondary-color);
        }
        
        .prescription-section {
            background-color: var(--card-bg);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .prescription-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .prescription-title {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .prescription-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .prescription-detail {
            display: flex;
            align-items: center;
            color: #666;
        }
        
        .prescription-detail i {
            margin-right: 8px;
            color: var(--secondary-color);
        }
        
        .medicines-list {
            margin-top: 15px;
        }
        
        .medicine-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: white;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .medicine-info {
            flex-grow: 1;
        }
        
        .medicine-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 4px;
        }
        
        .medicine-dosage {
            color: #666;
            font-size: 14px;
        }
        
        .reminder-card {
            border-left: 5px solid var(--warning-color);
            margin-bottom: 20px;
            padding: 20px;
            background: linear-gradient(to right, var(--white) 0%, var(--card-bg) 100%);
            border-radius: 0 12px 12px 0;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }
        
        .reminder-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.12);
        }
        
        .reminder-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .reminder-title {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .reminder-type {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background-color: rgba(243, 156, 18, 0.1);
            color: var(--warning-color);
        }
        
        .reminder-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .reminder-detail {
            display: flex;
            align-items: center;
            color: #666;
        }
        
        .reminder-detail i {
            margin-right: 8px;
            color: var(--secondary-color);
        }
        
        .reminder-remarks {
            margin-top: 10px;
            padding: 10px;
            background-color: var(--card-bg);
            border-radius: 5px;
            color: #666;
        }
        
        .reminder-remarks strong {
            color: var(--primary-color);
        }
        
        .reminder-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #777;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }
        
        .nav-tabs {
            border-bottom: 2px solid var(--gray-blue);
            margin-bottom: 20px;
        }
        
        .nav-tabs .nav-link {
            color: var(--primary-color);
            border: none;
            border-bottom: 3px solid transparent;
            border-radius: 0;
            padding: 12px 20px;
            margin-right: 5px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--secondary-color);
            border-bottom-color: var(--secondary-color);
            background: none;
        }
        
        .nav-tabs .nav-link:hover {
            border-bottom-color: #eee;
        }
        
        .doctor-card-header {
            font-size: 1.1rem;
        }
        .doctor-card .patient-block {
            border-left: 3px solid var(--soft-blue);
            padding-left: 15px;
            margin-left: 5px;
        }
        .patient-name-row {
            display: flex;
            align-items: flex-start;
            margin-bottom: 10px;
            color: var(--primary-color);
        }
        .patient-info-stack {
            display: block;
        }
        .patient-info-stack .patient-contact {
            font-size: 0.9rem;
            color: #6b7280;
            margin-top: 2px;
        }
        .appointment-block {
            background: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 8px;
            padding: 12px 15px;
            margin-bottom: 12px;
        }
        .appointment-date-row {
            font-size: 0.95rem;
            color: #555;
        }
        .medicine-list-label {
            font-size: 1.4rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 8px;
        }
        .reminder-action-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }
        .reminder-set-badge {
            font-size: 0.85rem;
            color: var(--accent-color);
            font-weight: 500;
        }
        
        /* Search bar - same design as recep_doctor.php filter-container */
        .reminder-search-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .reminder-search-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        .reminder-search-form .search-type-select,
        .reminder-search-form .search-term-input {
            padding: 10px;
            border: 1px solid #D0D7E1;
            border-radius: 5px;
        }
        .reminder-search-form .btn-search {
            padding: 10px 15px;
            background: #5790AB;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .reminder-search-form .btn-clear {
            padding: 10px 15px;
            background: #D0D7E1;
            color: #1a3a5f;
            border: 1px solid #D0D7E1;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }
        
        @media (max-width: 768px) {
            
            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
            
            .patient-details {
                grid-template-columns: 1fr;
            }
            
            .prescription-details {
                grid-template-columns: 1fr;
            }
            
            .patient-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include 'recept_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
    <?php include 'receptionist_header.php'; ?>
        
        <!-- Search Bar Section (same design as recep_doctor.php filter) -->
        <div class="reminder-search-section">
            <form method="post" action="st_reminder.php" class="reminder-search-form">
                <select name="search_type" class="search-type-select" required>
                    <option value="">Search by...</option>
                    <option value="patient" <?php echo (isset($_POST['search_type']) && $_POST['search_type'] === 'patient') ? 'selected' : ''; ?>>By Patient</option>
                    <option value="doctor" <?php echo (isset($_POST['search_type']) && $_POST['search_type'] === 'doctor') ? 'selected' : ''; ?>>By Doctor</option>
                </select>
                <input type="text" name="search_term" class="search-term-input" placeholder="Search..." value="<?php echo isset($_POST['search_term']) ? htmlspecialchars($_POST['search_term']) : ''; ?>">
                <button type="submit" class="btn-search">Search</button>
                <?php if (!empty($_POST['search_type']) || !empty($_POST['search_term'])): ?>
                <a href="st_reminder.php" class="btn-clear">Clear</a>
                <?php endif; ?>
            </form>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill me-2"></i><?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Reminder Card -->
        <div class="card">
            
            <div class="card-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="reminderTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="prescriptions-tab" data-bs-toggle="tab" data-bs-target="#prescriptions" type="button" role="tab" aria-controls="prescriptions" aria-selected="true">Completed Prescriptions</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reminders-tab" data-bs-toggle="tab" data-bs-target="#reminders" type="button" role="tab" aria-controls="reminders" aria-selected="false">Active Reminders</button>
                    </li>
                </ul>
                
                <!-- Tab Content -->
                <div class="tab-content" id="reminderTabContent">
                    <!-- Completed Prescriptions Tab: Doctor → Patient → Appointment → Medicine → Reminder -->
                    <div class="tab-pane fade show active" id="prescriptions" role="tabpanel" aria-labelledby="prescriptions-tab">
                        <?php
                        if (!empty($by_doctor)) {
                            foreach ($by_doctor as $doctor_id => $doctor_data) {
                                ?>
                                <div class="doctor-card card mb-4">
                                    <div class="card-header doctor-card-header">
                                        <i class="bi bi-person-badge me-2"></i>
                                        <strong>Dr. <?php echo htmlspecialchars($doctor_data['doc_name']); ?></strong>
                                        <span class="badge bg-primary ms-2"><?php echo htmlspecialchars($doctor_data['specialization']); ?></span>
                                    </div>
                                    <div class="card-body">
                                        <?php foreach ($doctor_data['patients'] as $patient_id => $patient_data): ?>
                                        <div class="patient-block mb-4">
                                            <div class="patient-name-row" style="background-color: #e0f2fe; padding: 15px; border-radius: 8px;">
                                                <div style="font-size: 1.1rem;"><i class="bi bi-person me-2"></i><strong><?php echo htmlspecialchars($patient_data['name']); ?></strong></div>
                                                <div class="text-muted small ms-4 mt-2"><i class="bi bi-telephone me-2"></i><?php echo htmlspecialchars($patient_data['phone']); ?></div>
                                                <div class="text-muted small ms-4 mt-1"><i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars($patient_data['email']); ?></div>
                                            </div>
                                            <?php foreach ($patient_data['appointments'] as $apt): 
                                                $prescription_id = (int)$apt['PRESCRIPTION_ID'];
                                                $medicines_query = mysqli_query($conn, "
                                                    SELECT m.MEDICINE_ID, m.MED_NAME, pm.DOSAGE, pm.DURATION, pm.FREQUENCY
                                                    FROM prescription_medicine_tbl pm
                                                    JOIN medicine_tbl m ON pm.MEDICINE_ID = m.MEDICINE_ID
                                                    WHERE pm.PRESCRIPTION_ID = '$prescription_id'
                                                ");
                                            ?>
                                            <div class="appointment-block">
                                                <div class="appointment-date-row">
                                                    <i class="bi bi-calendar-event me-2"></i>
                                                    <strong>Appointment:</strong> <?php echo date('F d, Y', strtotime($apt['APPOINTMENT_DATE'])); ?> at <?php echo date('h:i A', strtotime($apt['APPOINTMENT_TIME'])); ?>
                                                </div>
                                                <div class="medicines-list mt-2">
                                                    <div class="medicine-list-label">Medicines:</div>
                                                    <?php
                                                    if ($medicines_query && mysqli_num_rows($medicines_query) > 0) {
                                                        while ($med = mysqli_fetch_assoc($medicines_query)) {
                                                            $med_id = (int)$med['MEDICINE_ID'];
                                                            $med_duration_days = 0;
                                                            if (!empty($med['DURATION']) && preg_match('/(\d+)/', $med['DURATION'], $matches)) {
                                                                $med_duration_days = (int)$matches[1];
                                                            }
                                                            $reminder_row = isset($reminder_by_prescription_medicine[$prescription_id][$med_id])
                                                                ? $reminder_by_prescription_medicine[$prescription_id][$med_id]
                                                                : null;
                                                            ?>
                                                            <div class="medicine-item">
                                                                <div class="medicine-info">
                                                                    <div class="medicine-name"><?php echo htmlspecialchars($med['MED_NAME']); ?></div>
                                                                    <div class="medicine-dosage">
                                                                        Dosage: <?php echo htmlspecialchars($med['DOSAGE']); ?> ·
                                                                        Duration: <?php echo htmlspecialchars($med['DURATION']); ?> ·
                                                                        Frequency: <?php echo htmlspecialchars($med['FREQUENCY']); ?>
                                                                    </div>
                                                                </div>
                                                                <div class="reminder-action-row">
                                                                    <?php 
                                                                    // Check if this patient has ANY active reminder
                                                                    $patient_has_active_reminder = isset($patients_with_active_reminders[$apt['PATIENT_ID']]);
                                                                    if ($reminder_row): ?>
                                                                        <span class="reminder-set-badge me-2"><i class="bi bi-bell-fill me-1"></i>Reminder set</span>
                                                                        <button type="button" class="btn btn-warning btn-sm" onclick="openEditReminderModal(
                                                                            <?php echo (int)$reminder_row['MEDICINE_REMINDER_ID']; ?>,
                                                                            '<?php echo addslashes($reminder_row['REMINDER_TIME']); ?>',
                                                                            '<?php echo addslashes($reminder_row['REMARKS']); ?>',
                                                                            '<?php echo isset($reminder_row['APPOINTMENT_DATE']) ? addslashes($reminder_row['APPOINTMENT_DATE']) : ''; ?>'
                                                                        )">
                                                                            <i class="bi bi-pencil me-1"></i> Edit
                                                                        </button>
                                                                        <form method="POST" class="d-inline">
                                                                            <input type="hidden" name="reminder_id" value="<?php echo (int)$reminder_row['MEDICINE_REMINDER_ID']; ?>">
                                                                            <button type="submit" name="delete_medicine_reminder" class="btn btn-danger btn-sm" onclick="return confirm('Delete this reminder?');">
                                                                                <i class="bi bi-trash me-1"></i> Delete
                                                                            </button>
                                                                        </form>
                                                                    <?php elseif (!$patient_has_active_reminder): ?>
                                                                        <button class="btn-set-reminder" data-bs-toggle="modal" data-bs-target="#createPrescriptionReminderModal" 
                                                                                data-patient-id="<?php echo (int)$apt['PATIENT_ID']; ?>"
                                                                                data-patient-name="<?php echo htmlspecialchars($apt['PAT_FNAME'] . ' ' . $apt['PAT_LNAME']); ?>"
                                                                                data-prescription-id="<?php echo $prescription_id; ?>"
                                                                                data-medicine-id="<?php echo $med_id; ?>"
                                                                                data-medicine-name="<?php echo htmlspecialchars($med['MED_NAME']); ?>"
                                                                                data-appointment-date="<?php echo htmlspecialchars($apt['APPOINTMENT_DATE']); ?>"
                                                                                data-medicine-duration="<?php echo $med_duration_days; ?>">
                                                                            <i class="bi bi-bell-fill"></i><span>Set Reminder</span>
                                                                        </button>
                                                                    <?php else: ?>
                                                                        <!-- Patient has active reminder but not for this specific medicine -->
                                                                        <span class="text-muted small"><i class="bi bi-bell-fill me-1"></i>Patient has active reminder</span>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                    } else {
                                                        echo '<p class="small text-muted">No medicines prescribed.</p>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="empty-state">
                                <i class="bi bi-clipboard-x"></i>
                                <h4>No completed appointments found</h4>
                                <p>No patients with completed appointments and prescriptions found.' . ($search_term ? ' Try a different search.' : '') . '</p>
                            </div>';
                        }
                        ?>
                    </div>
                    
                    <!-- Active Reminders Tab -->
                    <div class="tab-pane fade" id="reminders" role="tabpanel" aria-labelledby="reminders-tab">
                        <?php
                        if (!empty($reminders_list)) {
                            foreach ($reminders_list as $reminder) {
                                ?>
                                <div class="reminder-card">
                                    <div class="reminder-header">
                                        <div class="reminder-title">
                                            Reminder for <?php echo htmlspecialchars($reminder['PAT_FNAME'] . ' ' . $reminder['PAT_LNAME']); ?>
                                        </div>
                                        <span class="reminder-type">Medicine</span>
                                    </div>
                                    
                                    <div class="reminder-details">
                                        <div class="reminder-detail">
                                            <i class="bi bi-clock"></i>
                                            <span>Time: <?php echo date('h:i A', strtotime($reminder['REMINDER_TIME'])); ?></span>
                                        </div>
                                        <?php
                                        // Prefer showing explicit date range if available
                                        $has_range = !empty($reminder['START_DATE']) && !empty($reminder['END_DATE']);
                                        if ($has_range): ?>
                                            <div class="reminder-detail">
                                                <i class="bi bi-calendar-range"></i>
                                                <span>From: <?php echo date('F d, Y', strtotime($reminder['START_DATE'])); ?> 
                                                    to <?php echo date('F d, Y', strtotime($reminder['END_DATE'])); ?></span>
                                            </div>
                                        <?php else:
                                            // Fallback: Extract duration from remarks for older reminders
                                            $duration_text = 'Not specified';
                                            if (preg_match('/Duration:\s*(\d+)\s*days?/i', $reminder['REMARKS'], $matches)) {
                                                $days = (int)$matches[1];
                                                if ($days == 0) {
                                                    $duration_text = 'On appointment day';
                                                } else {
                                                    $duration_text = $days . ' day' . ($days > 1 ? 's' : '') . ' after appointment';
                                                }
                                            }
                                            ?>
                                            <div class="reminder-detail">
                                                <i class="bi bi-calendar-range"></i>
                                                <span>Duration: <?php echo $duration_text; ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (isset($reminder['APPOINTMENT_DATE']) && $reminder['APPOINTMENT_DATE']): ?>
                                        <div class="reminder-detail">
                                            <i class="bi bi-calendar-check"></i>
                                            <span>Appointment: <?php echo date('F d, Y', strtotime($reminder['APPOINTMENT_DATE'])); ?></span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="reminder-remarks">
                                        <?php
                                        // Extract actual remarks (remove duration part)
                                        $actual_remarks = preg_replace('/Duration:\s*\d+\s*days?\s*(before|after)\s*appointment\.?\s*/i', '', $reminder['REMARKS']);
                                        if (trim($actual_remarks)): ?>
                                            <strong>Remarks:</strong> <?php echo htmlspecialchars(trim($actual_remarks)); ?>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="reminder-actions">
                                        <button class="btn btn-warning btn-sm" onclick="openEditReminderModal(
                                            <?php echo $reminder['MEDICINE_REMINDER_ID']; ?>,
                                            '<?php echo $reminder['REMINDER_TIME']; ?>',
                                            '<?php echo addslashes($reminder['REMARKS']); ?>',
                                            '<?php echo isset($reminder['APPOINTMENT_DATE']) ? $reminder['APPOINTMENT_DATE'] : ''; ?>'
                                        )">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="reminder_id" value="<?php echo $reminder['MEDICINE_REMINDER_ID']; ?>">
                                            <button type="submit" name="delete_medicine_reminder" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this reminder?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="empty-state">
                                <i class="bi bi-bell-slash"></i>
                                <h4>No reminders found</h4>
                                <p>No medicine reminders have been set yet.</p>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Create Prescription Reminder Modal -->
    <div class="modal fade" id="createPrescriptionReminderModal" tabindex="-1" aria-labelledby="createPrescriptionReminderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-reminder-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--secondary-color), var(--soft-blue)); color: white;">
                    <h5 class="modal-title" id="createPrescriptionReminderModalLabel">
                        <i class="bi bi-bell-fill me-2"></i>Create Prescription Reminder
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" id="createReminderForm">
                        <input type="hidden" name="create_prescription_reminder" value="1">
                        <input type="hidden" id="prescription_id" name="prescription_id">
                        <input type="hidden" id="medicine_id" name="medicine_id">
                        
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <div>
                                <strong>Patient:</strong> <span id="patient_info"></span><br>
                                <small id="appointment_info" class="text-muted"></small><br>
                                <small id="medicine_info" class="text-muted"></small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="start_date">
                                <i class="bi bi-calendar-plus me-2"></i>Start Date
                            </label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>

                        <div class="form-group">
                            <label for="end_date">
                                <i class="bi bi-calendar-minus me-2"></i>End Date
                            </label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>

                        <div class="form-group">
                            <label for="duration">
                                <i class="bi bi-calendar-range me-2"></i>Duration After Appointment (Days)
                            </label>
                            <input type="number" class="form-control" id="duration" name="duration" min="0" required readonly>
                            <small class="form-text text-muted">Duration fetched from selected medicine</small>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <i class="bi bi-clock me-2"></i>Reminder Times
                            </label>
                            <div id="reminder-times-container">
                                <div class="d-flex align-items-center mb-2 reminder-time-row">
                                    <input type="time" class="form-control" name="reminder_time[]" required>
                                    <input type="text" class="form-control ms-2" name="reminder_remarks[]" placeholder="Time to take your medicine">
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="add-reminder-time-btn">
                                <i class="bi bi-plus-circle me-1"></i>Add another time
                            </button>
                            <small class="form-text text-muted d-block mt-1">You can set multiple reminder times per day.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="remarks">
                                <i class="bi bi-chat-left-text me-2"></i>Remarks
                            </label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Add any additional notes for the reminder"></textarea>
                        </div>
                        
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i> Create Reminder
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-2"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Reminder Modal -->
    <div class="modal fade" id="editReminderModal" tabindex="-1" aria-labelledby="editReminderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--warning-color), #e67e22); color: white;">
                    <h5 class="modal-title" id="editReminderModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Edit Reminder
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" id="editReminderForm">
                        <input type="hidden" name="update_medicine_reminder" value="1">
                        <input type="hidden" id="edit_reminder_id" name="reminder_id">
                        
                        <div class="form-group">
                            <label for="edit_duration">
                                <i class="bi bi-calendar-range me-2"></i>Duration After Appointment (Days)
                            </label>
                            <input type="number" class="form-control" id="edit_duration" name="duration" min="0" required>
                            <small class="form-text text-muted">Enter number of days after the appointment to send the reminder</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_reminder_time">
                                <i class="bi bi-clock me-2"></i>Reminder Time
                            </label>
                            <input type="time" class="form-control" id="edit_reminder_time" name="reminder_time" required>
                            <small class="form-text text-muted">Time of day to send the reminder</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_remarks">
                                <i class="bi bi-chat-left-text me-2"></i>Remarks
                            </label>
                            <textarea class="form-control" id="edit_remarks" name="remarks" rows="3" placeholder="Add any additional notes for the reminder"></textarea>
                        </div>
                        
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i> Update Reminder
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-2"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle prescription reminder modal
            const prescriptionReminderModal = document.getElementById('createPrescriptionReminderModal');
            
            if (prescriptionReminderModal) {
                prescriptionReminderModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const patientName = button.getAttribute('data-patient-name');
                    const prescriptionId = button.getAttribute('data-prescription-id');
                    const medicineId = button.getAttribute('data-medicine-id');
                    const medicineName = button.getAttribute('data-medicine-name');
                    
                    // Update modal content
                    document.getElementById('patient_info').textContent = patientName;
                    
                    // Get appointment date from button data attribute
                    const appointmentDateStr = button.getAttribute('data-appointment-date');
                    if (appointmentDateStr) {
                        const appointmentDate = new Date(appointmentDateStr);
                        const formattedDate = appointmentDate.toLocaleDateString('en-US', { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        });
                        document.getElementById('appointment_info').textContent = `Appointment Date: ${formattedDate}`;

                        // Default start/end dates based on appointment + duration
                        const medicineDuration = parseInt(button.getAttribute('data-medicine-duration') || '0', 10);
                        const startDateInput = document.getElementById('start_date');
                        const endDateInput = document.getElementById('end_date');
                        if (startDateInput && endDateInput) {
                            const startDate = new Date(appointmentDate);
                            const endDate = new Date(appointmentDate);
                            if (!isNaN(medicineDuration) && medicineDuration > 0) {
                                endDate.setDate(endDate.getDate() + medicineDuration);
                            }
                            const toYMD = d => d.toISOString().slice(0, 10);
                            startDateInput.value = toYMD(startDate);
                            endDateInput.value = toYMD(endDate);
                        }

                        // Keep duration field in sync (read-only info)
                        const durationInput = document.getElementById('duration');
                        if (durationInput) {
                            durationInput.value = isNaN(medicineDuration) ? '0' : medicineDuration.toString();
                        }
                    }
                    
                    document.getElementById('prescription_id').value = prescriptionId;
                    document.getElementById('medicine_id').value = medicineId;
                    
                    // Show medicine info
                    const medicineInfoEl = document.getElementById('medicine_info');
                    if (medicineInfoEl && medicineName) {
                        medicineInfoEl.textContent = `Medicine: ${medicineName}`;
                    }
                    
                    // Reset reminder times (one default at 09:00 with default remark)
                    const timesContainer = document.getElementById('reminder-times-container');
                    if (timesContainer) {
                        timesContainer.innerHTML = '';
                        const row = document.createElement('div');
                        row.className = 'd-flex align-items-center mb-2 reminder-time-row';
                        row.innerHTML = '<input type="time" class="form-control" name="reminder_time[]" value="09:00" required>' +
                            '<input type="text" class="form-control ms-2" name="reminder_remarks[]" placeholder="Time to take your medicine">';
                        timesContainer.appendChild(row);
                    }
                    
                    // Reset remarks
                    document.getElementById('remarks').value = '';
                });
            }
            
            // Add more reminder time inputs
            const addTimeBtn = document.getElementById('add-reminder-time-btn');
            if (addTimeBtn) {
                addTimeBtn.addEventListener('click', function() {
                    const container = document.getElementById('reminder-times-container');
                    if (!container) return;
                    const row = document.createElement('div');
                    row.className = 'd-flex align-items-center mb-2 reminder-time-row';
                    row.innerHTML = '<input type="time" class="form-control" name="reminder_time[]" required>' +
                        '<input type="text" class="form-control ms-2" name="reminder_remarks[]" placeholder="Time to take your medicine">' +
                        '<button type="button" class="btn btn-link text-danger ms-2 remove-reminder-time-btn"><i class="bi bi-x-circle"></i></button>';
                    container.appendChild(row);
                });

                document.addEventListener('click', function(e) {
                    if (e.target.closest('.remove-reminder-time-btn')) {
                        const row = e.target.closest('.reminder-time-row');
                        const container = document.getElementById('reminder-times-container');
                        if (row && container && container.children.length > 1) {
                            row.remove();
                        }
                    }
                });
            }

            // Function to open edit reminder modal
            window.openEditReminderModal = function(reminderId, reminderTime, remarks, appointmentDate) {
                try {
                    document.getElementById('edit_reminder_id').value = reminderId;
                    document.getElementById('edit_reminder_time').value = reminderTime;
                    
                    // Extract duration from remarks
                    let duration = '';
                    if (remarks) {
                        const durationMatch = remarks.match(/Duration:\s*(\d+)\s*days?/i);
                        if (durationMatch) {
                            duration = durationMatch[1];
                        }
                    }
                    document.getElementById('edit_duration').value = duration || '1';
                    
                    // Extract actual remarks (remove duration part)
                    let actualRemarks = '';
                    if (remarks) {
                        actualRemarks = remarks.replace(/Duration:\s*\d+\s*days?\s*(before|after)\s*appointment\.?\s*/i, '').trim();
                    }
                    document.getElementById('edit_remarks').value = actualRemarks;
                    
                    // Show the modal
                    const editModalElement = document.getElementById('editReminderModal');
                    if (editModalElement) {
                        const editModal = new bootstrap.Modal(editModalElement);
                        editModal.show();
                    } else {
                        console.error('Edit modal element not found');
                        alert('Error: Could not open edit modal. Please refresh the page.');
                    }
                } catch (error) {
                    console.error('Error opening edit modal:', error);
                    alert('Error opening edit form. Please refresh the page and try again.');
                }
            };
        });
    </script>
</body>
</html>