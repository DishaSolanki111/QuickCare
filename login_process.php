<?php
session_start();
include 'config.php';

 $user = $_POST['username'] ?? '';
 $pass = $_POST['password'] ?? '';

 $doctor_id   = $_POST['doctor_id']   ?? null;
 $date        = $_POST['date']        ?? null;
 $time        = $_POST['time']        ?? null;
 $schedule_id = $_POST['schedule_id'] ?? null;

if ($user === '' || $pass === '') {
    die("Invalid request");
}

 $user = mysqli_real_escape_string($conn, $user);

// fetch patient
 $q = mysqli_query($conn,"
    SELECT PATIENT_ID, PSWD, FIRST_NAME, LAST_NAME, EMAIL, PHONE
    FROM patient_tbl
    WHERE USERNAME='$user'
");

if (!$q || mysqli_num_rows($q) !== 1) {
    die("Invalid username or password");
}

 $row = mysqli_fetch_assoc($q);

// verify password
if (!password_verify($pass, $row['PSWD'])) {
    die("Invalid username or password");
}

// login success - store patient data in session
 $_SESSION['PATIENT_ID'] = $row['PATIENT_ID'];
 $_SESSION['PATIENT_NAME'] = $row['FIRST_NAME'] . ' ' . $row['LAST_NAME'];
 $_SESSION['PATIENT_EMAIL'] = $row['EMAIL'];
 $_SESSION['PATIENT_PHONE'] = $row['PHONE'];
 $_SESSION['LOGGED_IN'] = true;

// Store appointment data if available
if ($doctor_id && $date && $time && $schedule_id) {
    $_SESSION['PENDING_APPOINTMENT'] = [
        'doctor_id' => $doctor_id,
        'date' => $date,
        'time' => $time,
        'schedule_id' => $schedule_id
    ];
    // Redirect to payment if appointment data exists
    header("Location: payment.php?" . http_build_query([
        'doctor_id'   => $doctor_id,
        'date'        => $date,
        'time'        => $time,
        'schedule_id' => $schedule_id
    ]));
} else {
    // Redirect to dashboard if no appointment data
    header("Location: payment.php");
}
exit;