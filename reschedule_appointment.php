<?php
session_start();

// Ensure patient is logged in
if (!isset($_SESSION['PATIENT_ID'])) {
    header("Location: login_for_all.php");
    exit;
}

include 'config.php';

$patient_id = $_SESSION['PATIENT_ID'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id    = isset($_POST['appointment_id']) ? (int) $_POST['appointment_id'] : 0;
    $new_date_raw      = $_POST['new_date'] ?? '';
    $new_time_raw      = $_POST['new_time'] ?? '';
    $reschedule_reason = $_POST['reschedule_reason'] ?? '';

    // Basic validation
    if ($appointment_id <= 0 || empty($new_date_raw) || empty($new_time_raw)) {
        $_SESSION['APPOINTMENT_ERROR'] = "Invalid reschedule request.";
        header("Location: manage_appointments.php");
        exit;
    }

    $new_date = mysqli_real_escape_string($conn, $new_date_raw);
    $new_time = mysqli_real_escape_string($conn, $new_time_raw);

    // Make sure the appointment belongs to this patient and is still scheduled
    $appt_sql = "
        SELECT APPOINTMENT_ID, DOCTOR_ID 
        FROM appointment_tbl 
        WHERE APPOINTMENT_ID = ? 
          AND PATIENT_ID = ? 
          AND STATUS = 'SCHEDULED'
        LIMIT 1
    ";
    $appt_stmt = $conn->prepare($appt_sql);
    $appt_stmt->bind_param("ii", $appointment_id, $patient_id);
    $appt_stmt->execute();
    $appt_result = $appt_stmt->get_result();

    if ($appt_result->num_rows === 0) {
        $appt_stmt->close();
        $_SESSION['APPOINTMENT_ERROR'] = "Appointment cannot be rescheduled.";
        header("Location: manage_appointments.php");
        exit;
    }

    $appt = $appt_result->fetch_assoc();
    $doctor_id = (int) $appt['DOCTOR_ID'];
    $appt_stmt->close();

    // Optional: prevent double‑booking the same doctor/date/time
    $conflict_sql = "
        SELECT COUNT(*) AS cnt
        FROM appointment_tbl
        WHERE DOCTOR_ID = ?
          AND APPOINTMENT_DATE = ?
          AND APPOINTMENT_TIME = ?
          AND STATUS IN ('SCHEDULED','COMPLETED')
          AND APPOINTMENT_ID <> ?
    ";
    $conflict_stmt = $conn->prepare($conflict_sql);
    $conflict_stmt->bind_param("issi", $doctor_id, $new_date, $new_time, $appointment_id);
    $conflict_stmt->execute();
    $conflict_res = $conflict_stmt->get_result();
    $conflict_row = $conflict_res->fetch_assoc();
    $conflict_stmt->close();

    if (!empty($conflict_row['cnt']) && (int)$conflict_row['cnt'] > 0) {
        $_SESSION['APPOINTMENT_ERROR'] = "Selected slot is already booked. Please choose another time.";
        header("Location: manage_appointments.php");
        exit;
    }

    // Perform the reschedule (update date/time only; status remains SCHEDULED)
    $update_sql = "
        UPDATE appointment_tbl
        SET APPOINTMENT_DATE = ?, APPOINTMENT_TIME = ?
        WHERE APPOINTMENT_ID = ? AND PATIENT_ID = ?
    ";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssii", $new_date, $new_time, $appointment_id, $patient_id);

    if ($update_stmt->execute()) {
        $_SESSION['APPOINTMENT_SUCCESS'] = "Appointment rescheduled successfully.";
    } else {
        $_SESSION['APPOINTMENT_ERROR'] = "Failed to reschedule appointment.";
    }

    $update_stmt->close();
}

header("Location: manage_appointments.php");
exit;

