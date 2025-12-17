<?php
session_start();
include 'config.php';

if (!isset($_SESSION['PATIENT_ID'])) {
    die("Unauthorised");
}

$patient_id  = $_SESSION['PATIENT_ID'];
$doctor_id   = $_POST['doctor_id'];
$schedule_id = $_POST['schedule_id'];
$date        = $_POST['date'];
$time        = $_POST['time'];

// ✅ CORRECT INSERT — MATCHES TABLE STRUCTURE
$q = "
INSERT INTO appointment_tbl
(PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, CREATED_AT, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS)
VALUES
('$patient_id','$doctor_id','$schedule_id',NOW(),'$date','$time','CONFIRMED')
";

if (mysqli_query($conn, $q)) {
    echo "<h2 style='color:green'>Payment Successful</h2>";
    echo "<p>Appointment Confirmed</p>";
    echo "<p>Date: $date</p>";
    echo "<p>Time: $time</p>";
} else {
    echo mysqli_error($conn);
}
