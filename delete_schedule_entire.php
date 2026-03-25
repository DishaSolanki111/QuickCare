<?php
session_start();
include 'config.php';

if (!isset($_SESSION['LOGGED_IN']) || $_SESSION['LOGGED_IN'] !== true ||
    !isset($_SESSION['USER_TYPE']) || $_SESSION['USER_TYPE'] !== 'doctor' ||
    !isset($_SESSION['DOCTOR_ID'])) {
    header("Location: login.php");
    exit();
}

$doctor_id = (int) $_SESSION['DOCTOR_ID'];
$schedule_id = isset($_POST['schedule_id']) ? (int) $_POST['schedule_id'] : 0;

if ($schedule_id <= 0) {
    header("Location: mangae_schedule_doctor.php");
    exit();
}

$chk = $conn->prepare("SELECT SCHEDULE_ID, AVAILABLE_DAY, START_TIME, END_TIME FROM doctor_schedule_tbl WHERE SCHEDULE_ID = ? AND DOCTOR_ID = ?");
$chk->bind_param("ii", $schedule_id, $doctor_id);
$chk->execute();
$chk_res = $chk->get_result();
if (!$chk_res || $chk_res->num_rows === 0) {
    $chk->close();
    header("Location: mangae_schedule_doctor.php");
    exit();
}
$schedule_row = $chk_res->fetch_assoc();
$chk->close();

// Fetch appointments: only from current date to 1 month ahead (for that day's schedule)
$apt_sql = "SELECT a.APPOINTMENT_ID, a.PATIENT_ID, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, p.FIRST_NAME, p.LAST_NAME 
            FROM appointment_tbl a
            JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
            WHERE a.SCHEDULE_ID = ? AND a.STATUS = 'SCHEDULED'
            AND a.APPOINTMENT_DATE >= CURDATE()
            AND a.APPOINTMENT_DATE <= DATE_ADD(CURDATE(), INTERVAL 1 MONTH)";
$apt_stmt = $conn->prepare($apt_sql);
$apt_stmt->bind_param("i", $schedule_id);
$apt_stmt->execute();
$apt_result = $apt_stmt->get_result();

$deleted_appointments = [];
$patient_ids = [];

while ($row = $apt_result->fetch_assoc()) {
    $deleted_appointments[] = $row;
    $patient_ids[$row['PATIENT_ID']] = true;
}
$apt_stmt->close();

// Notify patients using medicine_reminder_tbl
$rec_id = 1;
$rec_r = $conn->query("SELECT RECEPTIONIST_ID FROM receptionist_tbl ORDER BY RECEPTIONIST_ID ASC LIMIT 1");
if ($rec_r && $rec_row = $rec_r->fetch_assoc()) {
    $rec_id = (int) $rec_row['RECEPTIONIST_ID'];
}
$med_id = 1;
$med_r = $conn->query("SELECT MEDICINE_ID FROM medicine_tbl ORDER BY MEDICINE_ID ASC LIMIT 1");
if ($med_r && $med_row = $med_r->fetch_assoc()) {
    $med_id = (int) $med_row['MEDICINE_ID'];
}
$doc_r = $conn->query("SELECT FIRST_NAME, LAST_NAME FROM doctor_tbl WHERE DOCTOR_ID = " . (int)$doctor_id);
$doctor_name = "Dr.";
if ($doc_r && $doc_row = $doc_r->fetch_assoc()) {
    $doctor_name = "Dr. " . trim($doc_row['FIRST_NAME'] . ' ' . $doc_row['LAST_NAME']);
}

$today = date('Y-m-d');
$now = date('H:i:s');
$ins = $conn->prepare("INSERT INTO medicine_reminder_tbl (MEDICINE_ID, CREATOR_ROLE, CREATOR_ID, PATIENT_ID, START_DATE, END_DATE, REMINDER_TIME, REMARKS) VALUES (?, 'RECEPTIONIST', ?, ?, ?, ?, ?, ?)");
$day_names = ['MON' => 'Monday', 'TUE' => 'Tuesday', 'WED' => 'Wednesday', 'THUR' => 'Thursday', 'FRI' => 'Friday', 'SAT' => 'Saturday', 'SUN' => 'Sunday'];
$day_name = $day_names[$schedule_row['AVAILABLE_DAY']] ?? $schedule_row['AVAILABLE_DAY'];
foreach ($deleted_appointments as $a) {
    $date_time = date('M d, Y', strtotime($a['APPOINTMENT_DATE'])) . ' at ' . date('h:i A', strtotime($a['APPOINTMENT_TIME']));
    $msg = "[CANCELLED] Your appointment with " . $doctor_name . " on " . $date_time . " was cancelled. Please reschedule your visit.";
    $patient_id = (int)$a['PATIENT_ID']; // ✅ Fixed: assigned to variable so it can be passed by reference
    $ins->bind_param("iiissss", $med_id, $rec_id, $patient_id, $today, $today, $now, $msg);
    $ins->execute();
}
$ins->close();

// Always notify receptionist via receptionist_notifications
$conn->query("CREATE TABLE IF NOT EXISTS receptionist_notifications (
    RECEPTIONIST_NOTIFICATION_ID int(11) NOT NULL AUTO_INCREMENT,
    MESSAGE text NOT NULL,
    TYPE varchar(50) DEFAULT 'schedule_deleted',
    CREATED_AT timestamp NULL DEFAULT current_timestamp(),
    PRIMARY KEY (RECEPTIONIST_NOTIFICATION_ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
$list_txt = !empty($deleted_appointments)
    ? implode('; ', array_map(function($a) {
        return trim($a['FIRST_NAME'] . ' ' . $a['LAST_NAME']) . ' - ' . date('M d, Y', strtotime($a['APPOINTMENT_DATE'])) . ' at ' . date('h:i A', strtotime($a['APPOINTMENT_TIME']));
    }, $deleted_appointments))
    : 'None';
$rec_msg = "[SCHEDULE_DELETED_BY_DOCTOR] " . $doctor_name . "'s " . $day_name . " schedule was deleted. Cancelled appointments: " . $list_txt;
$ins_rec = $conn->prepare("INSERT INTO receptionist_notifications (MESSAGE, TYPE) VALUES (?, 'schedule_deleted')");
$ins_rec->bind_param("s", $rec_msg);
$ins_rec->execute();
$ins_rec->close();

// Delete payments for ALL appointments under this schedule
$all_apt = $conn->query("SELECT APPOINTMENT_ID FROM appointment_tbl WHERE SCHEDULE_ID = " . (int)$schedule_id);
if ($all_apt && $all_apt->num_rows > 0) {
    $app_ids = [];
    while ($r = $all_apt->fetch_assoc()) {
        $app_ids[] = (int)$r['APPOINTMENT_ID'];
    }
    $app_ids_str = implode(',', $app_ids);
    $conn->query("DELETE FROM payment_tbl WHERE APPOINTMENT_ID IN ($app_ids_str)");
}

// Delete ALL appointments for this schedule
$conn->query("DELETE FROM appointment_tbl WHERE SCHEDULE_ID = " . (int)$schedule_id);

// Delete the schedule itself
$conn->query("DELETE FROM doctor_schedule_tbl WHERE SCHEDULE_ID = " . (int)$schedule_id . " AND DOCTOR_ID = " . (int)$doctor_id);

$conn->close();

$day_names = ['MON' => 'Monday', 'TUE' => 'Tuesday', 'WED' => 'Wednesday', 'THUR' => 'Thursday', 'FRI' => 'Friday', 'SAT' => 'Saturday', 'SUN' => 'Sunday'];
$day_name = $day_names[$schedule_row['AVAILABLE_DAY']] ?? $schedule_row['AVAILABLE_DAY'];
$list = [];
foreach ($deleted_appointments as $a) {
    $list[] = $a['FIRST_NAME'] . ' ' . $a['LAST_NAME'] . ' - ' . date('M d, Y', strtotime($a['APPOINTMENT_DATE'])) . ' at ' . date('h:i A', strtotime($a['APPOINTMENT_TIME']));
}
$success_msg = "Entire schedule for " . $day_name . " has been deleted. Affected patients have been notified and receptionist has been informed.";
if (!empty($list)) {
    $success_msg .= " Cancelled appointments: " . implode("; ", $list);
} else {
    $success_msg .= " No appointments were booked.";
}

$_SESSION['schedule_success_message'] = $success_msg;
header("Location: mangae_schedule_doctor.php");
exit();