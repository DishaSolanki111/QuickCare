<?php
session_start();

if (!isset($_SESSION['PATIENT_ID'])) {
    die("Login required");
}

$doctor_id   = $_GET['doctor_id'];
$date        = $_GET['date'];
$time        = $_GET['time'];
$schedule_id = $_GET['schedule_id'];
?>

<!DOCTYPE html>
<html>
<head>
<title>Payment</title>
<style>
body{
    font-family:Arial;
    background:#f5f8ff;
    display:flex;
    justify-content:center;
    padding:50px;
}
.box{
    background:white;
    padding:30px;
    width:350px;
    border-radius:12px;
    text-align:center;
}
button{
    background:#28a745;
    color:white;
    border:none;
    padding:12px;
    width:100%;
    font-size:16px;
    cursor:pointer;
}
</style>
</head>
<body>

<div class="box">
    <h2>Make Payment</h2>
    <p><strong>Amount:</strong> ₹300</p>

    <form action="payment_success.php" method="post">
        <input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">
        <input type="hidden" name="date" value="<?= $date ?>">
        <input type="hidden" name="time" value="<?= $time ?>">
        <input type="hidden" name="schedule_id" value="<?= $schedule_id ?>">
        <input type="hidden" name="amount" value="300">
        <button type="submit">Pay ₹300</button>
    </form>
</div>

</body>
</html>
