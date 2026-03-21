<?php
// Use the same session handling as doctor_header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';
require_once 'notification_seen.php';

$doctor_id = $_SESSION['DOCTOR_ID'] ?? 0;
if (!$doctor_id) {
    $appointments = [];
    $rescheduleNotifications = [];
    $cancelledByPatientNotifications = [];
    $reminderCount = 0;
    $rescheduleCount = 0;
    $cancelledByPatientCount = 0;
} else {
    $sql = "
    SELECT 
        a.APPOINTMENT_ID,
        p.FIRST_NAME AS patient_name,
        TIMESTAMP(a.APPOINTMENT_DATE, a.APPOINTMENT_TIME) AS appointment_datetime
    FROM appointment_tbl a
    JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
    WHERE a.DOCTOR_ID = ?
    AND a.STATUS IN ('SCHEDULED', 'APPROVED')
    AND TIMESTAMP(a.APPOINTMENT_DATE, a.APPOINTMENT_TIME)
        BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
    ORDER BY appointment_datetime ASC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $appointments = $result->fetch_all(MYSQLI_ASSOC);
    $reminderCount = count($appointments);

    // Reschedule notifications (when patient reschedules from manage_appointments.php)
    $reschedule_sql = "
    SELECT ar.APPOINTMENT_REMINDER_ID, ar.REMARKS, ar.REMINDER_TIME, ar.APPOINTMENT_ID,
           p.FIRST_NAME AS patient_fname, p.LAST_NAME AS patient_lname,
           a.APPOINTMENT_DATE, a.APPOINTMENT_TIME
    FROM appointment_reminder_tbl ar
    JOIN appointment_tbl a ON ar.APPOINTMENT_ID = a.APPOINTMENT_ID
    JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
    WHERE a.DOCTOR_ID = ?
    AND ar.REMARKS LIKE '[RESCHEDULED_BY_PATIENT]%'
    ORDER BY ar.APPOINTMENT_REMINDER_ID DESC
    LIMIT 20
    ";
    $reschedule_stmt = $conn->prepare($reschedule_sql);
    $reschedule_stmt->bind_param("i", $doctor_id);
    $reschedule_stmt->execute();
    $reschedule_result = $reschedule_stmt->get_result();
    $rescheduleNotifications = $reschedule_result->fetch_all(MYSQLI_ASSOC);
    $rescheduleCount = count($rescheduleNotifications);

    // Cancelled-by-patient notifications (when patient cancels from manage_appointments.php)
    $cancelled_sql = "
    SELECT ar.APPOINTMENT_REMINDER_ID, ar.REMARKS, ar.REMINDER_TIME, ar.APPOINTMENT_ID,
           p.FIRST_NAME AS patient_fname, p.LAST_NAME AS patient_lname,
           a.APPOINTMENT_DATE, a.APPOINTMENT_TIME
    FROM appointment_reminder_tbl ar
    JOIN appointment_tbl a ON ar.APPOINTMENT_ID = a.APPOINTMENT_ID
    JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
    WHERE a.DOCTOR_ID = ?
    AND ar.REMARKS LIKE '[CANCELLED_BY_PATIENT]%'
    ORDER BY ar.APPOINTMENT_REMINDER_ID DESC
    LIMIT 20
    ";
    $cancelled_stmt = $conn->prepare($cancelled_sql);
    $cancelled_stmt->bind_param("i", $doctor_id);
    $cancelled_stmt->execute();
    $cancelled_result = $cancelled_stmt->get_result();
    $cancelledByPatientNotifications = $cancelled_result->fetch_all(MYSQLI_ASSOC);
    $cancelledByPatientCount = count($cancelledByPatientNotifications);
}
$totalBadgeCount = $reminderCount + $rescheduleCount + $cancelledByPatientCount;

if ($doctor_id) {
    $allKeys = [];
    foreach ($appointments as $row) {
        $row['_notif_key'] = qc_notification_make_key(
            'doctor_upcoming',
            $doctor_id . '|' . ($row['APPOINTMENT_ID'] ?? '') . '|' . ($row['appointment_datetime'] ?? '')
        );
        $allKeys[] = $row['_notif_key'];
    }
    foreach ($rescheduleNotifications as $row) {
        $row['_notif_key'] = qc_notification_make_key(
            'doctor_rescheduled',
            $doctor_id . '|' . ($row['APPOINTMENT_REMINDER_ID'] ?? '') . '|' . ($row['REMARKS'] ?? '')
        );
        $allKeys[] = $row['_notif_key'];
    }
    foreach ($cancelledByPatientNotifications as $row) {
        $row['_notif_key'] = qc_notification_make_key(
            'doctor_cancelled',
            $doctor_id . '|' . ($row['APPOINTMENT_REMINDER_ID'] ?? '') . '|' . ($row['REMARKS'] ?? '')
        );
        $allKeys[] = $row['_notif_key'];
    }

    $seenMap = qc_notification_seen_map($conn, 'doctor', $doctor_id, $allKeys);
    $toMarkSeen = [];

    $appointments = array_values(array_filter($appointments, function ($row) use ($seenMap, &$toMarkSeen) {
        $k = $row['_notif_key'] ?? '';
        if ($k !== '' && isset($seenMap[$k])) return false;
        if ($k !== '') $toMarkSeen[] = $k;
        return true;
    }));
    $rescheduleNotifications = array_values(array_filter($rescheduleNotifications, function ($row) use ($seenMap, &$toMarkSeen) {
        $k = $row['_notif_key'] ?? '';
        if ($k !== '' && isset($seenMap[$k])) return false;
        if ($k !== '') $toMarkSeen[] = $k;
        return true;
    }));
    $cancelledByPatientNotifications = array_values(array_filter($cancelledByPatientNotifications, function ($row) use ($seenMap, &$toMarkSeen) {
        $k = $row['_notif_key'] ?? '';
        if ($k !== '' && isset($seenMap[$k])) return false;
        if ($k !== '') $toMarkSeen[] = $k;
        return true;
    }));

    if (!empty($toMarkSeen)) {
        qc_notification_mark_seen($conn, 'doctor', $doctor_id, $toMarkSeen);
    }
}

$reminderCount = count($appointments);
$rescheduleCount = count($rescheduleNotifications);
$cancelledByPatientCount = count($cancelledByPatientNotifications);
$totalBadgeCount = $reminderCount + $rescheduleCount + $cancelledByPatientCount;
?>

<div class="qc-notification">
    <i class="fa fa-bell" id="qcBell"></i>

    <?php if ($totalBadgeCount > 0): ?>
        <span class="qc-badge"><?= $totalBadgeCount ?></span>
    <?php endif; ?>

    <div class="qc-dropdown" id="qcDropdown">
        <h4>Upcoming Appointments</h4>

        <?php if ($reminderCount === 0): ?>
            <p class="qc-empty">No upcoming appointments in next 24 hours</p>
        <?php else: ?>
            <?php foreach ($appointments as $row): ?>
                <div class="qc-item">
                    <strong><?= htmlspecialchars($row['patient_name']) ?></strong>
                    <span>
                        <?= date("d M Y, h:i A", strtotime($row['appointment_datetime'])) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($rescheduleCount > 0): ?>
            <h4 class="qc-reschedule-title">Rescheduled Appointments</h4>
            <?php foreach ($rescheduleNotifications as $res): ?>
                <?php
                $msg = preg_replace('/^\[RESCHEDULED_BY_PATIENT\]\s*/', '', $res['REMARKS']);
                $patient_name = htmlspecialchars(trim($res['patient_fname'] . ' ' . $res['patient_lname']));
                ?>
                <div class="qc-item qc-item-reschedule">
                    <strong><?= $patient_name ?></strong>
                    <span class="qc-reschedule-msg"><?= htmlspecialchars($msg) ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if ($cancelledByPatientCount > 0): ?>
            <h4 class="qc-cancelled-title">Cancelled by Patient</h4>
            <?php foreach ($cancelledByPatientNotifications as $res): ?>
                <?php
                $msg = preg_replace('/^\[CANCELLED_BY_PATIENT\]\s*/', '', $res['REMARKS']);
                $patient_name = htmlspecialchars(trim($res['patient_fname'] . ' ' . $res['patient_lname']));
                ?>
                <div class="qc-item qc-item-cancelled">
                    <strong><?= $patient_name ?></strong>
                    <span class="qc-reschedule-msg"><?= htmlspecialchars($msg) ?></span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById("qcBell").onclick = function () {
    const dropdown = document.getElementById("qcDropdown");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
};
</script>

<style>
/* Put this in a main CSS file if you want globally */
.qc-notification {
    position: relative;
    cursor: pointer;
    margin-right: 20px;
}
.qc-notification i { font-size: 22px; color: #0066cc; }
.qc-badge {
    position: absolute;
    top: -6px;
    right: -8px;
    background: red;
    color: #fff;
    font-size: 11px;
    padding: 3px 6px;
    border-radius: 50%;
}
.qc-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 30px;
    width: 280px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    padding: 12px;
    z-index: 999;
}
.qc-dropdown h4 { margin-bottom: 10px; font-size: 16px; }
.qc-reschedule-title { margin-top: 12px; margin-bottom: 8px; font-size: 14px; color: #0066cc; }
.qc-cancelled-title { margin-top: 12px; margin-bottom: 8px; font-size: 14px; color: #b91c1c; }
.qc-item { padding: 8px; border-bottom: 1px solid #eee; }
.qc-item span { display: block; font-size: 13px; color: #666; }
.qc-item-reschedule { background: #f0f8ff; }
.qc-item-cancelled { background: #fef2f2; }
.qc-reschedule-msg { font-size: 12px; color: #333; }
.qc-empty { text-align: center; color: #888; }
</style>