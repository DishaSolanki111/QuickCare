<?php
// receptionist_header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Name resolution strategy:
 * 1. If a full $receptionist array with FIRST_NAME/LAST_NAME is provided by the page, use that.
 * 2. Else if a simple $receptionist_name string is provided (like in receptionist.php), use it.
 * 3. Otherwise fall back to 'Receptionist'.
 */

// 1) Try array from DB (e.g. receptionist_profile.php)
if (isset($receptionist) && (isset($receptionist['FIRST_NAME']) || isset($receptionist['LAST_NAME']))) {
    $receptionist_full_name = trim(($receptionist['FIRST_NAME'] ?? '') . ' ' . ($receptionist['LAST_NAME'] ?? ''));
} 
// 2) Try pre-built name string
elseif (isset($receptionist_name) && $receptionist_name !== '') {
    $receptionist_full_name = $receptionist_name;
} 
// 3) Fallback
else {
    $receptionist_full_name = 'Receptionist';
}

// Simple initials avatar from name string
$initials = '';
$name_for_initials = trim($receptionist_full_name);
if ($name_for_initials !== '' && $name_for_initials !== 'Receptionist') {
    $parts = explode(' ', $name_for_initials);
    if (!empty($parts[0])) {
        $initials .= strtoupper(substr($parts[0], 0, 1));
    }
    if (!empty($parts[1])) {
        $initials .= strtoupper(substr($parts[1], 0, 1));
    }
}
if ($initials === '') {
    $initials = 'RC';
}

// Auto-fetch appointment cancel/reschedule and schedule delete/reschedule notifications for receptionist when not set
if (!isset($reminder_query) && isset($conn)) {
    $receptionist_reminders = [];
    $ar_q = @mysqli_query($conn, "
        SELECT ar.APPOINTMENT_REMINDER_ID, ar.REMARKS, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME,
               CASE
                   WHEN ar.REMARKS LIKE '[RESCHEDULED_BY_PATIENT]%' THEN 'Rescheduled by Patient'
                   WHEN ar.REMARKS LIKE '[CANCELLED_BY_PATIENT]%' THEN 'Cancelled by Patient'
                   WHEN ar.REMARKS LIKE '[RESCHEDULED_BY_DOCTOR]%' THEN 'Rescheduled by Doctor'
                   WHEN ar.REMARKS LIKE '[CANCELLED_BY_DOCTOR]%' THEN 'Cancelled by Doctor'
                   ELSE 'Appointment Update'
               END AS TITLE
        FROM appointment_reminder_tbl ar
        JOIN appointment_tbl a ON ar.APPOINTMENT_ID = a.APPOINTMENT_ID
        WHERE ar.REMARKS LIKE '[RESCHEDULED_BY_PATIENT]%'
           OR ar.REMARKS LIKE '[CANCELLED_BY_PATIENT]%'
           OR ar.REMARKS LIKE '[RESCHEDULED_BY_DOCTOR]%'
           OR ar.REMARKS LIKE '[CANCELLED_BY_DOCTOR]%'
        ORDER BY ar.APPOINTMENT_REMINDER_ID DESC
        LIMIT 20
    ");
    if ($ar_q) {
        while ($r = mysqli_fetch_assoc($ar_q)) {
            $receptionist_reminders[] = $r;
        }
    }
    $mr_q = @mysqli_query($conn, "
        SELECT REMARKS, START_DATE AS APPOINTMENT_DATE, REMINDER_TIME AS APPOINTMENT_TIME,
               CASE
                   WHEN REMARKS LIKE '[SCHEDULE_DELETED_BY_DOCTOR]%' THEN 'Schedule Deleted by Doctor'
                   WHEN REMARKS LIKE '[SLOTS_DELETED_BY_DOCTOR]%' THEN 'Slots Deleted by Doctor'
                   WHEN REMARKS LIKE '[SCHEDULE_RESCHEDULED_BY_DOCTOR]%' THEN 'Schedule Rescheduled by Doctor'
                   ELSE 'Schedule Update'
               END AS TITLE
        FROM medicine_reminder_tbl
        WHERE REMARKS LIKE '[SCHEDULE_DELETED_BY_DOCTOR]%'
           OR REMARKS LIKE '[SLOTS_DELETED_BY_DOCTOR]%'
           OR REMARKS LIKE '[SCHEDULE_RESCHEDULED_BY_DOCTOR]%'
        ORDER BY MEDICINE_REMINDER_ID DESC
        LIMIT 20
    ");
    if ($mr_q) {
        while ($r = mysqli_fetch_assoc($mr_q)) {
            $receptionist_reminders[] = $r;
        }
    }
    $rn_q = @mysqli_query($conn, "
        SELECT MESSAGE AS REMARKS, DATE(CREATED_AT) AS APPOINTMENT_DATE, TIME(CREATED_AT) AS APPOINTMENT_TIME, 'Schedule Deleted by Doctor' AS TITLE
        FROM receptionist_notifications
        WHERE MESSAGE LIKE '[SCHEDULE_DELETED_BY_DOCTOR]%'
        ORDER BY RECEPTIONIST_NOTIFICATION_ID DESC
        LIMIT 20
    ");
    if ($rn_q) {
        while ($r = mysqli_fetch_assoc($rn_q)) {
            $receptionist_reminders[] = $r;
        }
    }

    // Add vitals reminders: only at or after the exact appointment time (e.g. 5 PM on 14th),
    // not before. Only for appointments where vitals are not yet recorded (reminder removed once added).
    $vitals_q = @mysqli_query($conn, "
        SELECT a.APPOINTMENT_DATE, a.APPOINTMENT_TIME,
               CONCAT(p.FIRST_NAME, ' ', p.LAST_NAME) AS PATIENT_NAME
        FROM appointment_tbl a
        JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
        LEFT JOIN prescription_tbl pr ON pr.APPOINTMENT_ID = a.APPOINTMENT_ID
        WHERE a.STATUS = 'SCHEDULED'
          AND a.APPOINTMENT_DATE = CURDATE()
          AND TIMESTAMP(a.APPOINTMENT_DATE, a.APPOINTMENT_TIME) <= NOW()
          AND (pr.PRESCRIPTION_ID IS NULL
               OR (TRIM(COALESCE(pr.BLOOD_PRESSURE,'')) = '' AND pr.WEIGHT_KG IS NULL AND pr.HEIGHT_CM IS NULL))
        ORDER BY a.APPOINTMENT_TIME ASC
    ");
    if ($vitals_q) {
        while ($r = mysqli_fetch_assoc($vitals_q)) {
            $time_str = date('h:i A', strtotime($r['APPOINTMENT_TIME']));
            $receptionist_reminders[] = [
                'APPOINTMENT_DATE' => $r['APPOINTMENT_DATE'],
                'APPOINTMENT_TIME' => $r['APPOINTMENT_TIME'],
                'REMARKS'          => 'Add vitals for ' . $r['PATIENT_NAME'] . ' (appointment at ' . $time_str . ')',
                'TITLE'            => 'Add Vitals'
            ];
        }
    }
    usort($receptionist_reminders, function ($a, $b) {
        $dt_a = ($a['APPOINTMENT_DATE'] ?? '') . ' ' . ($a['APPOINTMENT_TIME'] ?? '');
        $dt_b = ($b['APPOINTMENT_DATE'] ?? '') . ' ' . ($b['APPOINTMENT_TIME'] ?? '');
        return strcmp($dt_b, $dt_a);
    });
    $reminder_query = $receptionist_reminders;
}

// Optional notifications (e.g. reminders or tasks) if a query is provided
$notif_count = isset($reminder_query)
    ? (is_array($reminder_query) ? count($reminder_query) : mysqli_num_rows($reminder_query))
    : 0;
$reminder_items = [];
if (isset($reminder_query) && $reminder_query) {
    $reminder_items = is_array($reminder_query) ? $reminder_query : [];
    if (!is_array($reminder_query)) {
        while ($r = mysqli_fetch_assoc($reminder_query)) $reminder_items[] = $r;
    }
}
?>

<?php
$current_script = basename($_SERVER['PHP_SELF'] ?? '');
$show_reminder_search = ($current_script === 'st_reminder.php');
?>
<header class="topbar">
    <h2>Welcome back</h2>

    
    <div class="topbar-right">
        <div class="user-info">
            <div class="user-avatar">
                <?php echo htmlspecialchars($initials); ?>
            </div>

            <div class="user-details">
                <div class="name-row">
                    <span class="user-name">
                        <?php echo htmlspecialchars($receptionist_full_name); ?>
                    </span>
                </div>

                <span class="date">
                    <?php echo date("F d, Y"); ?>
                </span>
            </div>

            <!-- Notification bell + dropdown, aligned like patient header -->
            <div class="notification-bell" onclick="toggleNotifications()">
                <i class="fas fa-bell"></i>

                <?php if ($notif_count > 0): ?>
                    <span class="notification-badge"><?php echo $notif_count; ?></span>
                <?php endif; ?>

                <div class="notification-dropdown" id="notificationDropdown">
                    <?php if ($notif_count > 0 && !empty($reminder_items)): ?>
                        <?php foreach ($reminder_items as $row): ?>
                            <div class="notification-item">
                                <div class="notification-icon">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">
                                        <?php echo htmlspecialchars($row['TITLE'] ?? 'Reminder'); ?>
                                    </div>
                                    <?php if (!empty($row['REMARKS'] ?? '')): ?>
                                        <div class="notification-message">
                                            <?php echo htmlspecialchars($row['REMARKS']); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($row['APPOINTMENT_DATE'] ?? '') && !empty($row['APPOINTMENT_TIME'] ?? '')): ?>
                                        <div class="notification-time">
                                            <?php
                                            echo date(
                                                'M d, Y h:i A',
                                                strtotime($row['APPOINTMENT_DATE'] . ' ' . $row['APPOINTMENT_TIME'])
                                            );
                                            ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-notifications">No new notifications</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
.topbar {
    background: #ffffff;
    padding: 12px 30px;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    margin-bottom: 10px;
}

.reminder-search-bar {
    flex: 1;
    min-width: 280px;
    max-width: 520px;
}

.reminder-search-form {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.reminder-search-form .search-type-select {
    width: 160px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    padding: 8px 12px;
    font-size: 14px;
}

.reminder-search-form .search-term-input {
    flex: 1;
    min-width: 120px;
    max-width: 200px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    padding: 8px 12px;
    font-size: 14px;
}

.reminder-search-form .btn-search {
    background: #1a3a5f;
    color: #fff;
    border: none;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 14px;
    cursor: pointer;
}

.reminder-search-form .btn-search:hover {
    background: #064469;
    color: #fff;
}

.reminder-search-form .btn-clear {
    background: #f3f4f6;
    color: #4b5563;
    border: 1px solid #d1d5db;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 14px;
    text-decoration: none;
}

.reminder-search-form .btn-clear:hover {
    background: #e5e7eb;
    color: #111;
}

.topbar-right {
    display: flex;
    align-items: center;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #1a3a5f;
    color: #ffffff;
    font-weight: 600;
    font-size: 16px;
}

.user-details {
    display: flex;
    flex-direction: column;
}

.name-row {
    display: flex;
    align-items: center;
}

.user-name {
    font-weight: 600;
    color: #1a3a5f;
    font-size: 16px;
}

.date {
    color: #6b7280;
    font-size: 14px;
}

.notification-bell {
    position: relative;
    margin-left: 24px;
    cursor: pointer;
    color: #1a3a5f;
}

.notification-bell i.fas.fa-bell {
    font-size: 18px;
}

.notification-badge {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #e11d48;
    color: #ffffff;
    border-radius: 999px;
    font-size: 10px;
    padding: 2px 6px;
}

.notification-dropdown {
    position: absolute;
    right: 0;
    top: 28px;
    min-width: 260px;
    background: #ffffff;
    box-shadow: 0 10px 20px rgba(0,0,0,0.12);
    border-radius: 8px;
    padding: 10px 0;
    display: none;
    z-index: 50;
}

.notification-dropdown.show {
    display: block;
}

.notification-item {
    display: flex;
    padding: 8px 14px;
    gap: 10px;
    border-bottom: 1px solid #f3f4f6;
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-icon {
    width: 28px;
    height: 28px;
    border-radius: 999px;
    background: #eff6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #1d4ed8;
    font-size: 14px;
}

.notification-content {
    flex: 1;
}

.notification-title {
    font-weight: 600;
    font-size: 13px;
    color: #111827;
    margin-bottom: 2px;
}

.notification-message {
    font-size: 12px;
    color: #4b5563;
}

.notification-time {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 2px;
}

.no-notifications {
    padding: 10px 14px;
    font-size: 13px;
    color: #6b7280;
}
</style>
<script>
function toggleNotifications() {
    var d = document.getElementById('notificationDropdown');
    if (d) d.classList.toggle('show');
    document.addEventListener('click', function close(e) {
        if (!e.target.closest('.notification-bell')) {
            if (d) d.classList.remove('show');
            document.removeEventListener('click', close);
        }
    });
}
</script>