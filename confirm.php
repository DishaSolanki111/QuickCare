<?php
@include 'config.php';

$doctor_id = $_GET['doctor_id'];
$date = $_GET['date'];
$time = $_GET['time'];
$schedule_id = $_GET['schedule_id'];

$patient_id = 1; // Replace after login system

$query = "
INSERT INTO appointment_tbl (doctor_id, patient_id, schedule_id, appointment_date, appointment_time, status)
VALUES ('$doctor_id', '$patient_id', '$schedule_id', '$date', '$time', 'scheduled')
";

if(mysqli_query($conn, $query)){
    echo "Appointment confirmed!";
} else {
    echo "Error: " . mysqli_error($conn);
}
