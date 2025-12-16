<?php
session_start();
include 'config.php';

/* TEMP: hardcoded patient for demo */
$patient_id = 1;

$doctor_id = $_GET['doctor_id'];
$date      = $_GET['date'];
$time      = $_GET['time'];

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
    echo "<h2 style='color:green'>Appointment Confirmed</h2>";
    echo "<p><b>Date:</b> $date</p>";
    echo "<p><b>Time:</b> $time</p>";
} else {
    echo "DB Error: " . mysqli_error($conn);
}
?>
