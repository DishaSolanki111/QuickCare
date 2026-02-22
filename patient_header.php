<?php
// patient_header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fetch reminder queries if not set (so reminders work on every page)
if ((!isset($reminder_query) || !isset($medicine_reminder_query) || !isset($cancellation_query)) && isset($patient, $conn) && !empty($patient['PATIENT_ID'])) {
    $pid = (int) $patient['PATIENT_ID'];
    if (!isset($reminder_query)) {
        $reminder_query = @mysqli_query($conn, "
            SELECT ar.REMARKS, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, d.FIRST_NAME, d.LAST_NAME
            FROM appointment_reminder_tbl ar
            JOIN appointment_tbl a ON ar.APPOINTMENT_ID = a.APPOINTMENT_ID
            JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
            WHERE a.PATIENT_ID = $pid
            AND a.APPOINTMENT_DATE >= CURDATE()
            AND ar.REMINDER_TIME <= CURTIME()
            AND ar.REMINDER_TIME > DATE_SUB(CURTIME(), INTERVAL 1 HOUR)
            ORDER BY ar.REMINDER_TIME DESC
            LIMIT 5
        ");
    }
    if (!isset($medicine_reminder_query)) {
        $medicine_reminder_query = @mysqli_query($conn, "
            SELECT mr.REMARKS, mr.REMINDER_TIME, m.MED_NAME, CURDATE() as REMINDER_DATE
            FROM medicine_reminder_tbl mr
            JOIN medicine_tbl m ON mr.MEDICINE_ID = m.MEDICINE_ID
            WHERE mr.PATIENT_ID = $pid
            AND CURDATE() BETWEEN mr.START_DATE AND mr.END_DATE
            AND mr.REMINDER_TIME <= CURTIME()
            AND mr.REMINDER_TIME > DATE_SUB(CURTIME(), INTERVAL 1 HOUR)
            ORDER BY mr.REMINDER_TIME DESC
            LIMIT 5
        ");
    }
    // Cancellation notifications (stored in medicine_reminder_tbl with [CANCELLED] prefix)
    if (!isset($cancellation_query)) {
        $cancellation_query = @mysqli_query($conn, "
            SELECT REMARKS, REMINDER_TIME, START_DATE
            FROM medicine_reminder_tbl
            WHERE PATIENT_ID = $pid AND REMARKS LIKE '[CANCELLED]%'
            ORDER BY START_DATE DESC, REMINDER_TIME DESC
            LIMIT 10
        ");
    }
}

// Expect $patient (and optional $page_title, $reminder_query, $medicine_reminder_query) to be set
$patient_full_name = isset($patient)
    ? trim(($patient['FIRST_NAME'] ?? '') . ' ' . ($patient['LAST_NAME'] ?? ''))
    : 'Patient';
$patient_full_name = $patient_full_name !== '' ? $patient_full_name : 'Patient';

// Simple initials avatar from name
$initials = '';
if (!empty($patient['FIRST_NAME']) || !empty($patient['LAST_NAME'])) {
    $initials .= strtoupper(substr($patient['FIRST_NAME'] ?? '', 0, 1));
    $initials .= strtoupper(substr($patient['LAST_NAME'] ?? '', 0, 1));
} else {
    $initials = 'PT';
}

// Notification count (appointment + medicine + cancellation reminders)
$reminder_count = isset($reminder_query) && $reminder_query ? mysqli_num_rows($reminder_query) : 0;
$medicine_reminder_count = isset($medicine_reminder_query) && $medicine_reminder_query ? mysqli_num_rows($medicine_reminder_query) : 0;
$cancellation_count = isset($cancellation_query) && $cancellation_query ? mysqli_num_rows($cancellation_query) : 0;
$reminder_count += $medicine_reminder_count + $cancellation_count;
?>

<header class="topbar">
    <h2><?php echo htmlspecialchars($page_title ?? 'Welcome back'); ?></h2>

    <div class="topbar-right">
        <div class="user-info">
            <div class="user-avatar">
                <?php echo htmlspecialchars($initials); ?>
            </div>

            <div class="user-details">
                <div class="name-row">
                    <span class="user-name">
                        <?php echo htmlspecialchars($patient_full_name); ?>
                    </span>
                </div>

                <span class="date">
                    <?php echo date("F d, Y"); ?>
                </span>
            </div>

            <!-- Notification bell + dropdown, aligned like doctor header -->
            <div class="notification-bell" onclick="toggleNotifications()">
                <i class="fas fa-bell"></i>

                <?php if ($reminder_count > 0): ?>
                    <span class="notification-badge"><?php echo $reminder_count; ?></span>
                <?php endif; ?>

                <div class="notification-dropdown" id="notificationDropdown">
                    <?php if ($reminder_count > 0): ?>
                        <?php if (isset($reminder_query) && $reminder_query): ?>
                            <?php while ($reminder = mysqli_fetch_assoc($reminder_query)): ?>
                                <div class="notification-item">
                                    <div class="notification-icon">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="notification-content">
                                        <div class="notification-title">Appointment Reminder</div>
                                        <div class="notification-message">
                                            <?php echo htmlspecialchars($reminder['REMARKS']); ?>
                                        </div>
                                        <div class="notification-time">
                                            <?php
                                            echo date('M d, Y h:i A', strtotime($reminder['APPOINTMENT_DATE'] . ' ' . $reminder['APPOINTMENT_TIME']));
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                        <?php if (isset($medicine_reminder_query) && $medicine_reminder_query): ?>
                            <?php while ($med = mysqli_fetch_assoc($medicine_reminder_query)): ?>
                                <div class="notification-item">
                                    <div class="notification-icon">
                                        <i class="fas fa-pills"></i>
                                    </div>
                                    <div class="notification-content">
                                        <div class="notification-title">Medicine Reminder</div>
                                        <div class="notification-message">
                                            Take <?php echo htmlspecialchars($med['MED_NAME']); ?><?php echo !empty(trim($med['REMARKS'])) ? ' - ' . htmlspecialchars($med['REMARKS']) : ''; ?>
                                        </div>
                                        <div class="notification-time">
                                            <?php echo date('M d, Y h:i A', strtotime($med['REMINDER_DATE'] . ' ' . $med['REMINDER_TIME'])); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                        <?php if (isset($cancellation_query) && $cancellation_query): ?>
                            <?php while ($cn = mysqli_fetch_assoc($cancellation_query)): 
                                $msg = str_replace('[CANCELLED] ', '', $cn['REMARKS']);
                                $date_time = '';
                                if (preg_match('/ on (.+?) was cancelled/', $msg, $m)) {
                                    $date_time = trim($m[1]);
                                }
                            ?>
                                <div class="notification-item">
                                    <div class="notification-icon" style="background: #fee2e2; color: #dc2626;">
                                        <i class="fas fa-calendar-times"></i>
                                    </div>
                                    <div class="notification-content">
                                        <div class="notification-title">Appointment Cancelled</div>
                                        <div class="notification-message">
                                            <?php echo htmlspecialchars($msg); ?>
                                        </div>
                                        <?php if ($date_time): ?>
                                        <div class="notification-time" style="font-weight:600; color:#dc2626;">
                                            <i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($date_time); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
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
    padding: 18px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
    margin-bottom: 10px;
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
    white-space: normal;
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

.notification-dropdown.show {
    display: block;
}

.notification-popup {
    display: none;
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: #ffffff;
    color: #333;
    padding: 12px 18px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    border: 1px solid #e5e7eb;
    z-index: 9999;
    max-width: 280px;
}
.notification-popup.show {
    display: block;
}
</style>

<!-- Notification popup (appointment + medicine reminders) -->
<div class="notification-popup" id="notificationPopup">
    <div style="display:flex; align-items:flex-start; gap:10px;">
        <i class="fas fa-bell" style="font-size:18px; color:#1a3a5f;"></i>
        <div style="flex:1;">
            <div id="notificationPopupMessage" style="font-weight:500; font-size:13px; color:#333;"></div>
            <button onclick="document.getElementById('notificationPopup').classList.remove('show')" style="margin-top:8px; background:#f3f4f6; border:1px solid #e5e7eb; color:#374151; padding:4px 10px; border-radius:4px; cursor:pointer; font-size:12px;">Close</button>
        </div>
    </div>
</div>

<script>
(function() {
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
    window.toggleNotifications = toggleNotifications;
})();
</script>
