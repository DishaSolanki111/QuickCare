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
$schedule_id = isset($_GET['schedule_id']) ? (int) $_GET['schedule_id'] : (isset($_POST['schedule_id']) ? (int) $_POST['schedule_id'] : 0);

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
$schedule = $chk_res->fetch_assoc();
$chk->close();

$day_names = ['MON' => 'Monday', 'TUE' => 'Tuesday', 'WED' => 'Wednesday', 'THUR' => 'Thursday', 'FRI' => 'Friday', 'SAT' => 'Saturday', 'SUN' => 'Sunday'];
$day_label = $day_names[$schedule['AVAILABLE_DAY']] ?? $schedule['AVAILABLE_DAY'];

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete_slots'])) {
    $appointment_ids = isset($_POST['appointment_ids']) && is_array($_POST['appointment_ids']) ? array_map('intval', array_filter($_POST['appointment_ids'])) : [];

    $deleted_list = [];
    $patient_ids = [];

    if (!empty($appointment_ids)) {
        $placeholders = implode(',', array_fill(0, count($appointment_ids), '?'));
        $types = str_repeat('i', count($appointment_ids) + 2);
        $params = array_merge($appointment_ids, [$schedule_id, $doctor_id]);
        $sel = $conn->prepare("SELECT a.APPOINTMENT_ID, a.PATIENT_ID, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, CONCAT(p.FIRST_NAME,' ',p.LAST_NAME) as PATIENT_NAME 
                               FROM appointment_tbl a 
                               JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID 
                               WHERE a.APPOINTMENT_ID IN ($placeholders) AND a.SCHEDULE_ID = ? AND a.DOCTOR_ID = ? AND a.STATUS = 'SCHEDULED'");
        $sel->bind_param($types, ...$params);
        $sel->execute();
        $sel_res = $sel->get_result();
        while ($row = $sel_res->fetch_assoc()) {
            $deleted_list[] = $row;
            $patient_ids[$row['PATIENT_ID']] = true;
        }
        $sel->close();

        // Notify patients via medicine_reminder_tbl
        $rec_id = 1;
        $rec_r = $conn->query("SELECT RECEPTIONIST_ID FROM receptionist_tbl ORDER BY RECEPTIONIST_ID ASC LIMIT 1");
        if ($rec_r && $rec_row = $rec_r->fetch_assoc()) $rec_id = (int) $rec_row['RECEPTIONIST_ID'];
        $med_id = 1;
        $med_r = $conn->query("SELECT MEDICINE_ID FROM medicine_tbl ORDER BY MEDICINE_ID ASC LIMIT 1");
        if ($med_r && $med_row = $med_r->fetch_assoc()) $med_id = (int) $med_row['MEDICINE_ID'];
        $doc_r = $conn->query("SELECT FIRST_NAME, LAST_NAME FROM doctor_tbl WHERE DOCTOR_ID = " . (int)$doctor_id);
        $doctor_name = "Dr.";
        if ($doc_r && $doc_row = $doc_r->fetch_assoc()) $doctor_name = "Dr. " . trim($doc_row['FIRST_NAME'] . ' ' . $doc_row['LAST_NAME']);
        $today = date('Y-m-d');
        $now = date('H:i:s');
        $ins = $conn->prepare("INSERT INTO medicine_reminder_tbl (MEDICINE_ID, CREATOR_ROLE, CREATOR_ID, PATIENT_ID, START_DATE, END_DATE, REMINDER_TIME, REMARKS) VALUES (?, 'RECEPTIONIST', ?, ?, ?, ?, ?, ?)");
        foreach ($deleted_list as $a) {
            $date_time = date('M d, Y', strtotime($a['APPOINTMENT_DATE'])) . ' at ' . date('h:i A', strtotime($a['APPOINTMENT_TIME']));
            $msg = "[CANCELLED] Your appointment with " . $doctor_name . " on " . $date_time . " was cancelled. Please reschedule your visit.";
            $ins->bind_param("iiissss", $med_id, $rec_id, (int)$a['PATIENT_ID'], $today, $today, $now, $msg);
            $ins->execute();
        }
        $ins->close();

        $app_ids_str = implode(',', array_column($deleted_list, 'APPOINTMENT_ID'));
        $conn->query("DELETE FROM payment_tbl WHERE APPOINTMENT_ID IN ($app_ids_str)");
        $conn->query("DELETE FROM appointment_tbl WHERE APPOINTMENT_ID IN ($app_ids_str)");
    }

    $list_str = array_map(function($a) {
        return $a['PATIENT_NAME'] . ' - ' . date('M d, Y', strtotime($a['APPOINTMENT_DATE'])) . ' at ' . date('h:i A', strtotime($a['APPOINTMENT_TIME']));
    }, $deleted_list);
    $success_msg = "Selected appointments cancelled. Affected patients have been notified.";
    if (!empty($list_str)) $success_msg .= " Deleted: " . implode("; ", $list_str);
    $_SESSION['schedule_success_message'] = $success_msg;
    header("Location: mangae_schedule_doctor.php");
    exit();
}

// Fetch SCHEDULED appointments for this schedule
$apt_sql = "SELECT a.APPOINTMENT_ID, a.PATIENT_ID, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, CONCAT(p.FIRST_NAME,' ',p.LAST_NAME) as PATIENT_NAME
            FROM appointment_tbl a JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
            WHERE a.SCHEDULE_ID = ? AND a.DOCTOR_ID = ? AND a.STATUS = 'SCHEDULED'
            ORDER BY a.APPOINTMENT_DATE, a.APPOINTMENT_TIME";
$apt_stmt = $conn->prepare($apt_sql);
$apt_stmt->bind_param("ii", $schedule_id, $doctor_id);
$apt_stmt->execute();
$appointments = $apt_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$apt_stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Schedule Slots - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #0066cc; --danger: #e74c3c; --warning: #f39c12; --dark: #1a3a5f; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: #f5f7fa; min-height: 100vh; }
        .main-content { margin-left: 250px; padding: 20px; }
        .card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-bottom: 20px; max-width: 700px; }
        h1 { color: var(--dark); margin-bottom: 10px; font-size: 24px; }
        .subtitle { color: #666; margin-bottom: 20px; font-size: 14px; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #c0392b; }
        .btn-secondary { background: #6c757d; color: white; }
        .btn-secondary:hover { background: #5a6268; }
        .slot-row { display: flex; align-items: center; padding: 12px 15px; border: 1px solid #eee; border-radius: 8px; margin-bottom: 8px; }
        .slot-row:hover { background: #f8f9fa; }
        .slot-row input[type="checkbox"] { margin-right: 15px; width: 18px; height: 18px; }
        .slot-info { flex: 1; }
        .slot-name { font-weight: 600; color: var(--dark); }
        .slot-datetime { color: #666; font-size: 14px; margin-top: 4px; }
        .empty-state { text-align: center; padding: 40px; color: #777; }
        .empty-state i { font-size: 48px; margin-bottom: 15px; color: #ddd; }
        .actions { margin-top: 20px; display: flex; gap: 10px; flex-wrap: wrap; }
    </style>
</head>
<body>
    <?php include 'doctor_sidebar.php'; ?>
    <div class="main-content">
        <?php include 'doctor_header.php'; ?>
        <div class="card">
            <h1><i class="fas fa-calendar-times"></i> Delete Particular Slots</h1>
            <p class="subtitle">Schedule: <?php echo htmlspecialchars($day_label); ?> (<?php echo date('h:i A', strtotime($schedule['START_TIME'])); ?> - <?php echo date('h:i A', strtotime($schedule['END_TIME'])); ?>)</p>
            <p class="subtitle">Select the appointments you want to cancel. Affected patients will be notified.</p>

            <?php if (empty($appointments)): ?>
                <div class="empty-state">
                    <i class="far fa-calendar-check"></i>
                    <p>No scheduled appointments found for this schedule.</p>
                    <a href="mangae_schedule_doctor.php" class="btn btn-secondary" style="margin-top: 15px;"><i class="fas fa-arrow-left"></i> Back to Schedule</a>
                </div>
            <?php else: ?>
                <form method="POST" action="delete_schedule_slots.php">
                    <input type="hidden" name="schedule_id" value="<?php echo (int)$schedule_id; ?>">
                    <input type="hidden" name="confirm_delete_slots" value="1">
                    <?php foreach ($appointments as $a): ?>
                        <label class="slot-row" style="cursor: pointer;">
                            <input type="checkbox" name="appointment_ids[]" value="<?php echo (int)$a['APPOINTMENT_ID']; ?>">
                            <div class="slot-info">
                                <div class="slot-name"><?php echo htmlspecialchars($a['PATIENT_NAME']); ?></div>
                                <div class="slot-datetime"><?php echo date('l, M d, Y', strtotime($a['APPOINTMENT_DATE'])); ?> at <?php echo date('h:i A', strtotime($a['APPOINTMENT_TIME'])); ?></div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                    <div class="actions">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Cancel selected appointments? Affected patients will be notified.');">
                            <i class="fas fa-trash"></i> Confirm Delete Selected
                        </button>
                        <a href="mangae_schedule_doctor.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
