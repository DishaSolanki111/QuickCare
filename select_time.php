<?php
session_start();
include 'config.php';

if (!isset($_SESSION['PATIENT_ID'])) {
    header("Location: login_for_all.php");
    exit;
}

if (!isset($_GET['doctor']) || !isset($_GET['day'])) {
    header("Location: doctor_schedule.php");
    exit;
}

$doctor_id = $_GET['doctor'];
$day = $_GET['day'];

/* Get doctor schedule */
$q = mysqli_query($conn, "
SELECT * FROM doctor_schedule_tbl
WHERE DOCTOR_ID = '$doctor_id'
AND AVAILABLE_DAY = '$day'
");

if (mysqli_num_rows($q) == 0) {
    die("Invalid schedule");
}

$schedule = mysqli_fetch_assoc($q);

/* Get doctor name */
$doc = mysqli_query($conn, "SELECT FIRST_NAME, LAST_NAME FROM doctor_tbl WHERE DOCTOR_ID='$doctor_id'");
$d = mysqli_fetch_assoc($doc);

?>

<!DOCTYPE html>
<html>
<head>
<title>Select Time</title>
<style>
body{font-family:Arial;background:#f5f8ff;display:flex;justify-content:center;align-items:center;height:100vh;}
.box{background:white;padding:40px;border-radius:10px;width:400px;}
input,button{width:100%;padding:12px;margin-top:10px;}
button{background:#28a745;color:white;border:none;}
</style>
</head>
<body>

<div class="box">
<h2>Dr. <?= $d['FIRST_NAME'].' '.$d['LAST_NAME'] ?></h2>
<p><?= $day ?> (<?= date('h:i A',strtotime($schedule['START_TIME'])) ?> - <?= date('h:i A',strtotime($schedule['END_TIME'])) ?>)</p>

<form method="post">
<label>Select Date</label>
<input type="date" name="date" required min="<?= date('Y-m-d') ?>">

<label>Select Time</label>
<input type="time" name="time" required>

<button type="submit">Continue to Payment</button>
</form>
</div>

<?php
if ($_SERVER['REQUEST_METHOD']=='POST') {
    $_SESSION['PENDING_APPOINTMENT'] = [
        'doctor_id'   => $doctor_id,
        'schedule_id' => $schedule['SCHEDULE_ID'],
        'date'        => $_POST['date'],
        'time'        => $_POST['time']
    ];

    header("Location: payment.php");
    exit;
}
?>

</body>
</html>
