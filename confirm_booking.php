<?php
session_start();

// Prevent doctors from accessing booking confirmation
if (isset($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE'] === 'doctor') {
    header("Location: doctor_dashboard.php");
    exit();
}

include 'config.php';

/* TEMP: hardcoded patient for demo */
 $patient_id = 1;

$doctor_id = $_POST['doctor_id'] ?? null;
$date      = $_POST['date'] ?? null;
$time      = $_POST['time'] ?? null;

/* 1️⃣ Find day name from date (MON, TUE...) */
 $day = strtoupper(date('D', strtotime($date))); // MON, TUE, WED

/* 2️⃣ Fetch correct schedule_id */
 $sq = mysqli_query($conn, "
    SELECT SCHEDULE_ID
    FROM doctor_schedule_tbl
    WHERE DOCTOR_ID = '$doctor_id'
      AND AVAILABLE_DAY = '$day'
    LIMIT 1
");

if(mysqli_num_rows($sq) == 0){
    die("No schedule found for selected date.");
}

 $row = mysqli_fetch_assoc($sq);
 $schedule_id = $row['SCHEDULE_ID'];

/* 3️⃣ Insert appointment (SYNTAX FIXED) */
 $q = "
INSERT INTO appointment_tbl
(doctor_id, patient_id, schedule_id, appointment_date, appointment_time, status, created_at)
VALUES
('$doctor_id','$patient_id','$schedule_id','$date','$time','scheduled',NOW())
";

if(mysqli_query($conn,$q)){
    $appointment_id = mysqli_insert_id($conn);
    // Payment is mandatory: insert payment_tbl record
    $amount = 0;
    $fee_res = mysqli_query($conn, "SELECT AMOUNT FROM payment_tbl WHERE STATUS='COMPLETED' ORDER BY PAYMENT_ID DESC LIMIT 1");
    if ($fee_res && $row = mysqli_fetch_assoc($fee_res)) {
        $amount = (float) $row['AMOUNT'];
    }
    if ($amount > 0 && $appointment_id > 0) {
        $txn_id = 'TXN_' . uniqid();
        mysqli_query($conn, "INSERT INTO payment_tbl (APPOINTMENT_ID, AMOUNT, PAYMENT_DATE, PAYMENT_MODE, STATUS, TRANSACTION_ID) 
            VALUES ($appointment_id, $amount, CURDATE(), 'CREDIT CARD', 'COMPLETED', '$txn_id')");
    }
    header("Location: appointment_success.php?id=" . $appointment_id);
    exit;
} else {
    echo "DB Error: " . mysqli_error($conn);
}
?>