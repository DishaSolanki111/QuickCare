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
    'doctor_id'   => $_POST['doctor_id'],
    'date'        => $_POST['date'],
    'time'        => $_POST['time'],
    'schedule_id' => $_POST['schedule_id']
]));
exit;
