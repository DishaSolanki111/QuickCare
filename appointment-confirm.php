<?php
session_start();
if (!isset($_SESSION['logged_in'])) {
    header("Location: doctor-appointment.html");
    exit;
}

$date = isset($_GET['date']) ? htmlspecialchars($_GET['date']) : '';
$slot = isset($_GET['slot']) ? htmlspecialchars($_GET['slot']) : '';
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Appointment Confirmed</title></head>
<body>
  <h2>Appointment Confirmed</h2>
  <p><strong>Date:</strong> <?= $date ?></p>
  <p><strong>Time:</strong> <?= $slot ?></p>
</body>
</html>
