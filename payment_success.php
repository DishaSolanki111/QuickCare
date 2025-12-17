<?php
session_start();
include 'config.php';

if (!isset($_SESSION['PATIENT_ID'])) {
die("Unauthorised Access");
}

/* REQUIRED DATA */
$patient_id = $_SESSION['PATIENT_ID'];
$doctor_id = $_POST['doctor_id'];
$schedule_id = $_POST['schedule_id'];
$date        = $_POST['date'];
$time        = $_POST['time'];

// ✅ CORRECT INSERT — MATCHES TABLE STRUCTURE
$q = "
INSERT INTO appointment_tbl
(PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, CREATED_AT, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS)
VALUES
('$patient_id', '$doctor_id', '$schedule_id', NOW(), '$date', '$time', 'scheduled')
";

if (mysqli_query($conn, $sql)) {
?>
<!DOCTYPE html>
<html>
<head>
<title>Payment Success</title>
<style>
body{
font-family:Arial;
background:#f5f8ff;
display:flex;
justify-content:center;
align-items:center;
height:100vh;
}
.box{
background:white;
padding:40px;
border-radius:12px;
text-align:center;
box-shadow:0 4px 15px rgba(0,0,0,.1);
}
h2{color:green;}
</style>
</head>
<body>

<div class="box">
<h2>Payment Successful ✅</h2>
<p><b>Appointment Confirmed</b></p>
<p>Date: <?= htmlspecialchars($date) ?></p>
<p>Time: <?= htmlspecialchars($time) ?></p>
<p>Status: Scheduled</p>
</div>

</body>
</html>
<?php
} else {
echo "<pre>";
echo mysqli_error($conn);
echo "</pre>";
}
?>
