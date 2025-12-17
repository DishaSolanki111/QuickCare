<?php
session_start();
include 'config.php';

$patient_id = $_SESSION['P_ID'];

$q = "
INSERT INTO appointment_tbl
(doctor_id, patient_id, schedule_id, appointment_date, appointment_time, status, created_at)
VALUES
('{$_POST['doctor_id']}','$patient_id','{$_POST['schedule_id']}',
'{$_POST['date']}','{$_POST['time']}','scheduled',NOW())
";

mysqli_query($conn,$q);

echo "<h2 style='color:green'>Appointment Confirmed</h2>";
