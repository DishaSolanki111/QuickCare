<?php
$doctor_id   = $_GET['doctor_id'];
$date        = $_GET['date'];
$time        = $_GET['time'];
$schedule_id = $_GET['schedule_id'];
?>
<!DOCTYPE html>
<html>
<head>
<title>Register</title>
<style>
body{font-family:Arial;background:#f5f8ff;display:flex;justify-content:center;}
form{background:white;padding:30px;width:400px;margin-top:40px;}
input,select,button{width:100%;padding:8px;margin:6px 0;}
button{background:#2e6ad6;color:white;border:none;}
</style>
</head>
<body>

<form method="POST" action="register_process.php">
<h2>Patient Registration</h2>

<input name="FIRST_NAME" placeholder="First Name" required>
<input name="LAST_NAME" placeholder="Last Name" required>
<input name="USERNAME" placeholder="Username" required>
<input name="PSW" placeholder="Password" required>
<input type="date" name="DOB" required>
<select name="GENDER"><option>MALE</option><option>FEMALE</option></select>
<input name="BLOOD_GROUP">
<input name="DIABETES">
<input name="PHONE" required>
<input name="EMAIL">
<input name="ADDRESS">

<input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">
<input type="hidden" name="date" value="<?= $date ?>">
<input type="hidden" name="time" value="<?= $time ?>">
<input type="hidden" name="schedule_id" value="<?= $schedule_id ?>">

<button>Register & Continue</button>
</form>

</body>
</html>
