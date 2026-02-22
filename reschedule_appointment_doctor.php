<?php
session_start();

if (
    !isset($_SESSION['LOGGED_IN']) ||
    $_SESSION['LOGGED_IN'] !== true ||
    !isset($_SESSION['USER_TYPE']) ||
    $_SESSION['USER_TYPE'] !== 'doctor' ||
    !isset($_SESSION['DOCTOR_ID'])
) {
    header("Location: login_for_all.php");
    exit;
}

include 'config.php';

$doctor_id = (int) $_SESSION['DOCTOR_ID'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: appointment_doctor.php");
    exit;
}

$appointment_id = isset($_POST['appointment_id']) ? (int) $_POST['appointment_id'] : 0;
$new_date = isset($_POST['new_date']) ? mysqli_real_escape_string($conn, $_POST['new_date']) : '';
$new_time = isset($_POST['new_time']) ? mysqli_real_escape_string($conn, $_POST['new_time']) : '';

if ($appointment_id <= 0 || empty($new_date) || empty($new_time)) {
    $_SESSION['RESCHEDULE_ERROR'] = "Invalid reschedule request. Date and time are required.";
    header("Location: appointment_doctor.php");
    exit;
}

// Ensure time format HH:MM or HH:MM:SS
$new_time_formatted = (strlen($new_time) <= 5) ? $new_time . ':00' : substr($new_time, 0, 8);

// Verify appointment belongs to this doctor and is SCHEDULED; get patient_id and old date/time for notification
$check = $conn->prepare("SELECT APPOINTMENT_ID, SCHEDULE_ID, PATIENT_ID, APPOINTMENT_DATE, APPOINTMENT_TIME FROM appointment_tbl WHERE APPOINTMENT_ID = ? AND DOCTOR_ID = ? AND STATUS = 'SCHEDULED'");
$check->bind_param("ii", $appointment_id, $doctor_id);
$check->execute();
$res = $check->get_result();
if (!$res || $res->num_rows === 0) {
    $check->close();
    $_SESSION['RESCHEDULE_ERROR'] = "Appointment not found or cannot be rescheduled.";
    header("Location: appointment_doctor.php");
    exit;
}
$app_row = $res->fetch_assoc();
$patient_id = (int) $app_row['PATIENT_ID'];
$old_date = $app_row['APPOINTMENT_DATE'];
$old_time = $app_row['APPOINTMENT_TIME'];
$check->close();

// Get SCHEDULE_ID for new date (day of week may change)
$day_map = [1=>'MON', 2=>'TUE', 3=>'WED', 4=>'THUR', 5=>'FRI', 6=>'SAT', 7=>'SUN'];
$day_of_week = $day_map[(int) date('N', strtotime($new_date))] ?? '';

$sch = $conn->prepare("SELECT SCHEDULE_ID FROM doctor_schedule_tbl WHERE DOCTOR_ID = ? AND AVAILABLE_DAY = ? LIMIT 1");
$sch->bind_param("is", $doctor_id, $day_of_week);
$sch->execute();
$sch_res = $sch->get_result();
if (!$sch_res || $sch_res->num_rows === 0) {
    $sch->close();
    $_SESSION['RESCHEDULE_ERROR'] = "You are not available on the selected date. Please choose a day you have a schedule.";
    header("Location: appointment_doctor.php");
    exit;
}
$row = $sch_res->fetch_assoc();
$new_schedule_id = (int) $row['SCHEDULE_ID'];
$sch->close();

// Check for double-booking (exclude current appointment)
$conflict = $conn->prepare("
    SELECT COUNT(*) AS cnt FROM appointment_tbl
    WHERE DOCTOR_ID = ? AND APPOINTMENT_DATE = ? AND APPOINTMENT_TIME = ?
    AND STATUS IN ('SCHEDULED','COMPLETED') AND APPOINTMENT_ID != ?
");
$conflict->bind_param("issi", $doctor_id, $new_date, $new_time_formatted, $appointment_id);
$conflict->execute();
$conf_res = $conflict->get_result();
$conf_row = $conf_res->fetch_assoc();
$conflict->close();

if ($conf_row['cnt'] > 0) {
    $_SESSION['RESCHEDULE_ERROR'] = "Selected slot is already booked. Please choose another time.";
    header("Location: appointment_doctor.php");
    exit;
}

// Update appointment
$upd = $conn->prepare("UPDATE appointment_tbl SET APPOINTMENT_DATE = ?, APPOINTMENT_TIME = ?, SCHEDULE_ID = ? WHERE APPOINTMENT_ID = ? AND DOCTOR_ID = ?");
$upd->bind_param("ssiii", $new_date, $new_time_formatted, $new_schedule_id, $appointment_id, $doctor_id);

if ($upd->execute()) {
    $_SESSION['RESCHEDULE_SUCCESS'] = "Appointment rescheduled successfully.";
    
    // Get doctor name for notification message
    $doc_stmt = $conn->prepare("SELECT FIRST_NAME, LAST_NAME FROM doctor_tbl WHERE DOCTOR_ID = ?");
    $doc_stmt->bind_param("i", $doctor_id);
    $doc_stmt->execute();
    $doc_res = $doc_stmt->get_result();
    $doc_name = 'Your doctor';
    if ($doc_res && $doc_row = $doc_res->fetch_assoc()) {
        $doc_name = 'Dr. ' . $doc_row['FIRST_NAME'] . ' ' . $doc_row['LAST_NAME'];
    }
    $doc_stmt->close();
    
    $old_date_fmt = date('F d, Y', strtotime($old_date));
    $old_time_fmt = date('g:i A', strtotime($old_time));
    $new_date_fmt = date('F d, Y', strtotime($new_date));
    $new_time_fmt = date('g:i A', strtotime($new_time_formatted));
    $msg = "Your appointment with {$doc_name} has been rescheduled from {$old_date_fmt} at {$old_time_fmt} to {$new_date_fmt} at {$new_time_fmt}.";
    
    // Use existing appointment_reminder_tbl for notification (RECEPTIONIST_ID 1 as system)
    $recep_id = 1;
    $reminder_time = date('H:i:s');
    $ins = $conn->prepare("INSERT INTO appointment_reminder_tbl (RECEPTIONIST_ID, APPOINTMENT_ID, REMINDER_TIME, REMARKS) VALUES (?, ?, ?, ?)");
    $ins->bind_param("iiss", $recep_id, $appointment_id, $reminder_time, $msg);
    $ins->execute();
    $ins->close();
} else {
    $_SESSION['RESCHEDULE_ERROR'] = "Failed to reschedule appointment.";
}
$upd->close();

// Clear reschedule session vars
unset($_SESSION['reschedule_appointment_id']);

header("Location: appointment_doctor.php");
exit;
