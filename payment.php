<?php
session_start();
if(!isset($_SESSION['P_ID'])) die("Unauthorized");

$doctor_id   = $_GET['doctor_id'];
$date        = $_GET['date'];
$time        = $_GET['time'];
$schedule_id = $_GET['schedule_id'];
?>
<!DOCTYPE html>
<html>
<head><title>Payment</title></head>
<body style="font-family:Arial;text-align:center;padding:50px;">

<h2>Pay ₹500</h2>

<form method="POST" action="payment_success.php">
<input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">
<input type="hidden" name="date" value="<?= $date ?>">
<input type="hidden" name="time" value="<?= $time ?>">
<input type="hidden" name="schedule_id" value="<?= $schedule_id ?>">

<button style="padding:12px 30px;">Pay Now</button>
</form>

</body>
</html>
