<?php
session_start();
include 'config.php';

/* TEMP: assume patient_id = 1 for now */
$patient_id = 1;

$doctor_id = $_GET['doctor_id'];
$date      = $_GET['date'];
$time      = $_GET['time'];

$q = "
INSERT INTO appointment_tbl
(doctor_id, patient_id, appointment_date, appointment_time, status, created_at)
VALUES
('$doctor_id','$patient_id','$date','$time','scheduled',NOW())
";

if(mysqli_query($conn,$q)){
    echo "<h2 style='color:green'>Appointment Confirmed</h2>";
    echo "<p>Date: $date</p>";
    echo "<p>Time: $time</p>";
}else{
    echo "Error booking appointment";
}
