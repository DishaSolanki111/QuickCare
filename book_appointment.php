<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Entry point when coming from schedule.php with doctor_id, date, time
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doctor_id']) && isset($_POST['date']) && isset($_POST['time'])) {
    $_SESSION['booking_doctor_id'] = (int) $_POST['doctor_id'];
    $_SESSION['booking_date'] = $_POST['date'];
    $_SESSION['booking_time'] = $_POST['time'];
    $_SESSION['booking_reason'] = isset($_POST['reason']) ? $_POST['reason'] : '';
}

if (isset($_SESSION['PATIENT_ID']) && isset($_SESSION['booking_doctor_id'])) {
    header("Location: book_appointment_confirm.php");
    exit();
}

if (isset($_SESSION['booking_doctor_id'])) {
    header("Location: book_appointment_login.php");
    exit();
}

header("Location: doctors.php");
exit();
