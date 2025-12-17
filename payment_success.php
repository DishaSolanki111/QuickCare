<?php
session_start();
include 'config.php';

if (!isset($_SESSION['PATIENT_ID'])) {
    die("Unauthorised");
}

$patient_id  = $_SESSION['PATIENT_ID'];
$doctor_id   = $_POST['doctor_id'];
$date        = $_POST['date'];
$time        = $_POST['time'];
$schedule_id = $_POST['schedule_id'];
$amount      = $_POST['amount'];

// INSERT APPOINTMENT
$q = "
INSERT INTO appointment_tbl
(doctor_id, patient_id, schedule_id, appointment_date, appointment_time, amount, payment_status, status, created_at)
VALUES
('$doctor_id','$patient_id','$schedule_id','$date','$time','$amount','PAID','CONFIRMED',NOW())
";

if(mysqli_query($conn,$q)){
    echo "<h2 style='color:green'>Payment Successful</h2>";
    echo "<p>Appointment Confirmed</p>";
    echo "<p>Date: $date</p>";
    echo "<p>Time: $time</p>";
} else {
    echo mysqli_error($conn);
}
