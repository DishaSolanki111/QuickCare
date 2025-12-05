<?php
session_start();
@include 'config.php';

$doctor_id   = $_GET['doctor_id'];
$date        = $_GET['date'];
$time        = $_GET['time'];
$schedule_id = $_GET['schedule_id'];

$patient_id = $_SESSION['patient_id'];  // IMPORTANT

$q = "INSERT INTO appointment_tbl
      (doctor_id, patient_id, schedule_id, appointment_date, appointment_time, status, created_at)
      VALUES
      ('$doctor_id', '$patient_id', '$schedule_id', '$date', '$time', 'scheduled', NOW())";

if(mysqli_query($conn, $q)){
    echo "
    <h2 style='color:green;'>Appointment Confirmed</h2>
    <p><b>Date:</b> $date</p>
    <p><b>Time:</b> $time</p>
    <p>You will receive a reminder later.</p>
    ";
} else {
    echo "<p style='color:red;'>Failed to book appointment.</p>";
}
?>
