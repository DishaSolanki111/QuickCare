<?php
session_start();

$doctor_id = $_GET['doctor_id'];
$date = $_GET['date'];
$time = $_GET['time'];
$schedule_id = $_GET['schedule_id'];
?>
<!DOCTYPE html>
<html>
<head>
<title>Patient Login</title>
<style>
body{font-family:Arial;background:#f5f8ff;display:flex;justify-content:center;padding:50px;}
.box{background:white;padding:30px;width:350px;border-radius:12px;}
input,button{width:100%;padding:10px;margin:10px 0;}
button{background:#2e6ad6;color:white;border:none;}
</style>
</head>
<body>

<div class="box">
<h2>Patient Login</h2>

<form action="login_process.php" method="post">

<input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">
<input type="hidden" name="date" value="<?= $date ?>">
<input type="hidden" name="time" value="<?= $time ?>">
<input type="hidden" name="schedule_id" value="<?= $schedule_id ?>">

<input name="username" placeholder="Username" required>
<input name="password" type="password" placeholder="Password" required>

<button type="submit">Login</button>

<p>
<a href="patientform.php">Register Yourself</a><br>
<a href="forgot_password.php">Forgot Password</a>
</p>

</form>
</div>

</body>
</html>
login.php

<?php
session_start();
include 'config.php';

$user = $_POST['username'] ?? '';
$pass = $_POST['password'] ?? '';

if ($user === '' || $pass === '') {
die("Invalid request");
}

$user = mysqli_real_escape_string($conn, $user);

// 1️⃣ Fetch user
$q = mysqli_query($conn, "
SELECT PATIENT_ID, PSWD
FROM patient_tbl
WHERE USERNAME='$user'
");


if (!$q || mysqli_num_rows($q) !== 1) {
die("Invalid username or password");
}

$row = mysqli_fetch_assoc($q);

// 2️⃣ Verify password
if (!password_verify($pass, $row['PSWD'])) {
die("Invalid username or password");
}

// 3️⃣ Login success
$_SESSION['PATIENT_ID'] = $row['PATIENT_ID'];

// 4️⃣ Redirect to payment
header("Location: payment.php?" . http_build_query([
'doctor_id' => $_POST['doctor_id'],
'date' => $_POST['date'],
'time' => $_POST['time'],
'schedule_id' => $_POST['schedule_id']
]));
exit;
