<?php
// Defensive input handling (no warnings, no stupidity)
$doctor_id   = $_GET['doctor_id']   ?? '';
$date        = $_GET['date']        ?? '';
$time        = $_GET['time']        ?? '';
$schedule_id = $_GET['schedule_id'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Patient Registration</title>

<style>
/* ===== RESET & BOX MODEL ===== */
* {
  box-sizing: border-box;
  font-family: Arial, sans-serif;
}

/* ===== PAGE WRAPPER ===== */
body {
  margin: 0;
  background: #f5f8ff;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  min-height: 100vh;
  padding: 20px;
}

/* ===== FORM CARD ===== */
form {
  background: #fff;
  width: 100%;
  max-width: 420px;
  padding: 28px;
  border-radius: 10px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

/* ===== HEADING ===== */
h2 {
  text-align: center;
  margin-bottom: 20px;
  color: #2e6ad6;
}

/* ===== INPUTS ===== */
input,
select {
  width: 100%;
  padding: 11px;
  margin-bottom: 14px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

/* ===== FOCUS ===== */
input:focus,
select:focus {
  outline: none;
  border-color: #2e6ad6;
}

/* ===== BUTTON ===== */
button {
  width: 100%;
  padding: 12px;
  background: #2e6ad6;
  color: #fff;
  border: none;
  border-radius: 6px;
  font-size: 15px;
  font-weight: bold;
  cursor: pointer;
}

button:hover {
  background: #2458b8;
}

/* ===== MOBILE SAFE ===== */
@media (max-height: 600px) {
  body {
    align-items: flex-start;
  }
}
</style>
</head>

<body>

<form method="POST" action="register_process.php">
  <h2>Patient Registration</h2>

  <input type="text" name="FIRST_NAME" placeholder="First Name" required>
  <input type="text" name="LAST_NAME" placeholder="Last Name" required>
  <input type="text" name="USERNAME" placeholder="Username" required>
  <input type="password" name="PSW" placeholder="Password" required>

  <input type="date" name="DOB" required>

  <select name="GENDER" required>
    <option value="">Select Gender</option>
    <option value="MALE">Male</option>
    <option value="FEMALE">Female</option>
  </select>

  <input type="text" name="BLOOD_GROUP" placeholder="Blood Group">
  <input type="text" name="DIABETES" placeholder="Diabetes (Yes / No)">
  <input type="text" name="PHONE" placeholder="Phone Number" required>
  <input type="email" name="EMAIL" placeholder="Email">
  <input type="text" name="ADDRESS" placeholder="Address">

  <!-- Hidden booking data -->
  <input type="hidden" name="doctor_id" value="<?= htmlspecialchars($doctor_id) ?>">
  <input type="hidden" name="date" value="<?= htmlspecialchars($date) ?>">
  <input type="hidden" name="time" value="<?= htmlspecialchars($time) ?>">
  <input type="hidden" name="schedule_id" value="<?= htmlspecialchars($schedule_id) ?>">

  <button type="submit">Register & Continue</button>
</form>

</body>
</html>