<?php
/**
 * Handles booking form submission from manage_appointments.php modal.
 * Sets session and redirects to book_appointment_confirm so the full flow
 * (confirm -> book_appointment_payment) creates both appointment and payment.
 */
session_start();

if (!isset($_SESSION['PATIENT_ID'])) {
    header("Location: login_for_all.php");
    exit;
}

$doctor_id = isset($_POST['doctor_id']) ? (int) $_POST['doctor_id'] : 0;
$date      = isset($_POST['appointment_date']) ? trim($_POST['appointment_date']) : '';
$time      = isset($_POST['appointment_time']) ? trim($_POST['appointment_time']) : '';
$reason    = isset($_POST['reason']) ? trim($_POST['reason']) : '';

if ($doctor_id <= 0 || empty($date) || empty($time)) {
    header("Location: manage_appointments.php?error=missing_booking_data");
    exit;
}

include 'config.php';
$conn = $GLOBALS['conn'] ?? null;

// Fetch doctor name and specialization for session (optional, confirm page fetches from DB)
$doc_row = null;
$q = mysqli_query($conn, "
    SELECT d.FIRST_NAME, d.LAST_NAME, s.SPECIALISATION_NAME
    FROM doctor_tbl d
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE d.DOCTOR_ID = " . (int) $doctor_id . " LIMIT 1
");
if ($q && mysqli_num_rows($q) > 0) {
    $doc_row = mysqli_fetch_assoc($q);
}

$_SESSION['booking_doctor_id']   = $doctor_id;
$_SESSION['booking_date']       = $date;
$_SESSION['booking_time']       = $time;
$_SESSION['booking_reason']     = $reason;
$_SESSION['booking_doctor_name'] = $doc_row ? ($doc_row['FIRST_NAME'] . ' ' . $doc_row['LAST_NAME']) : '';
$_SESSION['booking_specialization'] = $doc_row ? $doc_row['SPECIALISATION_NAME'] : '';

header("Location: book_appointment_confirm.php");
exit;
