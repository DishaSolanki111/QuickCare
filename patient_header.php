<?php
// patient_header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'notification_seen.php';

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
    if (!isset($cancellation_query)) {
        $cancellation_query = @mysqli_query($conn, "
            SELECT REMARKS, REMINDER_TIME, START_DATE
            FROM medicine_reminder_tbl
            WHERE PATIENT_ID = $pid AND REMARKS LIKE '[CANCELLED]%'
            ORDER BY START_DATE DESC, REMINDER_TIME DESC
            LIMIT 10
        ");
    }

    // ── NEW: Refund notifications ─────────────────────────────────────────────
    // Show a refund notification for up to 7 days after the refund was processed.
    // Joins refund_tbl → payment_tbl → appointment_tbl so we have all the detail.
    if (!isset($refund_query)) {
        $refund_query = @mysqli_query($conn, "
            SELECT  r.REFUND_ID,
                    r.REFUND_TXN_ID,
                    r.REFUND_AMOUNT,
                    r.REFUND_DATE,
                    r.REFUND_STATUS,
                    r.CREATED_AT,
                    a.APPOINTMENT_DATE,
                    a.APPOINTMENT_TIME,
                    d.FIRST_NAME  AS DOC_FIRST,
                    d.LAST_NAME   AS DOC_LAST
            FROM   refund_tbl r
            JOIN   payment_tbl     p ON p.PAYMENT_ID     = r.PAYMENT_ID
            JOIN   appointment_tbl a ON a.APPOINTMENT_ID = r.APPOINTMENT_ID
            JOIN   doctor_tbl      d ON d.DOCTOR_ID      = a.DOCTOR_ID
            WHERE  r.PATIENT_ID    = $pid
            AND    r.REFUND_STATUS = 'PROCESSED'
            AND    r.REFUND_DATE  >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
            ORDER  BY r.CREATED_AT DESC
            LIMIT  5
        ");
    }
    // ─────────────────────────────────────────────────────────────────────────
}

$patient_full_name = isset($patient)
    ? trim(($patient['FIRST_NAME'] ?? '') . ' ' . ($patient['LAST_NAME'] ?? ''))
    : 'Patient';
$patient_full_name = $patient_full_name !== '' ? $patient_full_name : 'Patient';

$initials = '';
if (!empty($patient['FIRST_NAME']) || !empty($patient['LAST_NAME'])) {
    $initials .= strtoupper(substr($patient['FIRST_NAME'] ?? '', 0, 1));
    $initials .= strtoupper(substr($patient['LAST_NAME'] ?? '', 0, 1));
} else {
    $initials = 'PT';
}

$pidForSeen = (int)($patient['PATIENT_ID'] ?? 0);
$appointment_items  = [];
$medicine_items     = [];
$cancellation_items = [];
$refund_items       = [];   // ← NEW
$allKeys            = [];

if (isset($reminder_query) && $reminder_query) {
    while ($row = mysqli_fetch_assoc($reminder_query)) {
        $k = qc_notification_make_key(
            'appointment',
            $pidForSeen . '|' . ($row['APPOINTMENT_DATE'] ?? '') . '|' . ($row['APPOINTMENT_TIME'] ?? '') . '|' . ($row['REMARKS'] ?? '')
        );
        $row['_notif_key'] = $k;
        $appointment_items[] = $row;
        $allKeys[] = $k;
    }
}
if (isset($medicine_reminder_query) && $medicine_reminder_query) {
    while ($row = mysqli_fetch_assoc($medicine_reminder_query)) {
        $k = qc_notification_make_key(
            'medicine',
            $pidForSeen . '|' . ($row['REMINDER_DATE'] ?? date('Y-m-d')) . '|' . ($row['REMINDER_TIME'] ?? '') . '|' . ($row['MED_NAME'] ?? '') . '|' . ($row['REMARKS'] ?? '')
        );
        $row['_notif_key'] = $k;
        $medicine_items[] = $row;
        $allKeys[] = $k;
    }
}
if (isset($cancellation_query) && $cancellation_query) {
    while ($row = mysqli_fetch_assoc($cancellation_query)) {
        $k = qc_notification_make_key(
            'cancellation',
            $pidForSeen . '|' . ($row['START_DATE'] ?? '') . '|' . ($row['REMINDER_TIME'] ?? '') . '|' . ($row['REMARKS'] ?? '')
        );
        $row['_notif_key'] = $k;
        $cancellation_items[] = $row;
        $allKeys[] = $k;
    }
}

// ── NEW: Build refund items using the same seen-key pattern ──────────────────
if (isset($refund_query) && $refund_query) {
    while ($row = mysqli_fetch_assoc($refund_query)) {
        $k = qc_notification_make_key(
            'refund',
            $pidForSeen . '|' . ($row['REFUND_ID'] ?? '') . '|' . ($row['REFUND_TXN_ID'] ?? '') . '|' . ($row['REFUND_DATE'] ?? '')
        );
        $row['_notif_key'] = $k;
        $refund_items[] = $row;
        $allKeys[] = $k;
    }
}
// ─────────────────────────────────────────────────────────────────────────────

$seenMap    = qc_notification_seen_map($conn, 'patient', $pidForSeen, $allKeys);
$toMarkSeen = [];

$appointment_items = array_values(array_filter($appointment_items, function ($row) use ($seenMap, &$toMarkSeen) {
    $k = $row['_notif_key'] ?? '';
    if ($k !== '' && isset($seenMap[$k])) return false;
    if ($k !== '') $toMarkSeen[] = $k;
    return true;
}));
$medicine_items = array_values(array_filter($medicine_items, function ($row) use ($seenMap, &$toMarkSeen) {
    $k = $row['_notif_key'] ?? '';
    if ($k !== '' && isset($seenMap[$k])) return false;
    if ($k !== '') $toMarkSeen[] = $k;
    return true;
}));
$cancellation_items = array_values(array_filter($cancellation_items, function ($row) use ($seenMap, &$toMarkSeen) {
    $k = $row['_notif_key'] ?? '';
    if ($k !== '' && isset($seenMap[$k])) return false;
    if ($k !== '') $toMarkSeen[] = $k;
    return true;
}));

// ── NEW: Filter refund items through seen map ─────────────────────────────────
$refund_items = array_values(array_filter($refund_items, function ($row) use ($seenMap, &$toMarkSeen) {
    $k = $row['_notif_key'] ?? '';
    if ($k !== '' && isset($seenMap[$k])) return false;
    if ($k !== '') $toMarkSeen[] = $k;
    return true;
}));
// ─────────────────────────────────────────────────────────────────────────────

if (!empty($toMarkSeen)) {
    qc_notification_mark_seen($conn, 'patient', $pidForSeen, $toMarkSeen);
}

$reminder_count = count($appointment_items)
                + count($medicine_items)
                + count($cancellation_items)
                + count($refund_items);   // ← NEW: refunds counted in badge
?>

<header class="topbar">
    <h2><?php echo htmlspecialchars($page_title ?? 'Welcome back'); ?></h2>

    <div class="topbar-right">
        <div class="user-info">
            <div class="user-avatar">
                <?php echo htmlspecialchars($initials); ?>
            </div>

            <div class="user-details">
                <span class="user-name">
                    <?php echo htmlspecialchars($patient_full_name); ?>
                </span>
            </div>

            <!-- Notification bell + dropdown -->
            <div class="notification-bell" onclick="toggleNotifications()">
                <i class="fas fa-bell"></i>

                <?php if ($reminder_count > 0): ?>
                    <span class="notification-badge"><?php echo $reminder_count; ?></span>
                <?php endif; ?>

                <div class="notification-dropdown" id="notificationDropdown">
                    <?php if ($reminder_count > 0): ?>

                        <!-- Appointment reminders -->
                        <?php if (!empty($appointment_items)): ?>
                            <?php foreach ($appointment_items as $reminder): ?>
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
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Medicine reminders -->
                        <?php if (!empty($medicine_items)): ?>
                            <?php foreach ($medicine_items as $med): ?>
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
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- Cancellation notifications -->
                        <?php if (!empty($cancellation_items)): ?>
                            <?php foreach ($cancellation_items as $cn): ?>
                                <div class="notification-item">
                                    <div class="notification-icon" style="background: #fee2e2; color: #dc2626;">
                                        <i class="fas fa-calendar-times"></i>
                                    </div>
                                    <div class="notification-content">
                                        <div class="notification-title">Appointment Cancelled</div>
                                        <div class="notification-message">
                                            <?php echo htmlspecialchars(str_replace('[CANCELLED] ', '', $cn['REMARKS'])); ?>
                                        </div>
                                        <div class="notification-time">
                                            <?php echo date('M d, Y', strtotime($cn['START_DATE'])); ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <!-- ── NEW: Refund notifications ───────────────────── -->
                        <?php if (!empty($refund_items)): ?>
                            <?php foreach ($refund_items as $refund): ?>
                                <div class="notification-item refund-notification-item">
                                    <div class="notification-icon" style="background: #dcfce7; color: #16a34a;">
                                        <i class="fas fa-undo-alt"></i>
                                    </div>
                                    <div class="notification-content">
                                        <div class="notification-title" style="color: #15803d;">
                                            Refund Processed ✅
                                        </div>
                                        <div class="notification-message">
                                            ₹<?php echo number_format((float)$refund['REFUND_AMOUNT'], 2); ?> refunded for your
                                            cancelled appointment with
                                            Dr. <?php echo htmlspecialchars($refund['DOC_FIRST'] . ' ' . $refund['DOC_LAST']); ?>
                                            on <?php echo date('M d, Y', strtotime($refund['APPOINTMENT_DATE'])); ?>.
                                        </div>
                                        <div class="notification-message" style="margin-top: 3px; color: #6b7280; font-size: 11px;">
                                            Txn: <?php echo htmlspecialchars($refund['REFUND_TXN_ID']); ?>
                                        </div>
                                        <div class="notification-time">
                                            Refunded on <?php echo date('M d, Y', strtotime($refund['REFUND_DATE'])); ?>
                                            &bull; Reflects in 5–7 business days
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <!-- ───────────────────────────────────────────────── -->

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

.user-name {
    font-weight: 600;
    color: #1a3a5f;
    font-size: 16px;
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
    min-width: 300px;
    background: #ffffff;
    box-shadow: 0 10px 20px rgba(0,0,0,0.12);
    border-radius: 8px;
    padding: 10px 0;
    display: none;
    z-index: 50;
    max-height: 420px;
    overflow-y: auto;
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

/* Subtle green left-border for refund items to make them stand out */
.refund-notification-item {
    border-left: 3px solid #16a34a;
    background: #f0fdf4;
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
    flex-shrink: 0;
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
    line-height: 1.5;
}

.notification-time {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 3px;
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
    max-width: 300px;
}
.notification-popup.show {
    display: block;
}

/* Green variant for refund popup */
.notification-popup.refund-popup {
    border-left: 4px solid #16a34a;
    background: #f0fdf4;
}
</style>

<!-- Notification popup (reused for reminders AND refunds) -->
<div class="notification-popup" id="notificationPopup">
    <div style="display:flex; align-items:flex-start; gap:10px;">
        <i class="fas fa-bell" id="notificationPopupIcon" style="font-size:18px; color:#1a3a5f;"></i>
        <div style="flex:1;">
            <div id="notificationPopupTitle" style="font-weight:600; font-size:13px; color:#333; margin-bottom:3px;"></div>
            <div id="notificationPopupMessage" style="font-weight:400; font-size:13px; color:#555;"></div>
            <button onclick="document.getElementById('notificationPopup').classList.remove('show','refund-popup')" style="margin-top:8px; background:#f3f4f6; border:1px solid #e5e7eb; color:#374151; padding:4px 10px; border-radius:4px; cursor:pointer; font-size:12px;">Close</button>
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

    // ── Show a popup notification ─────────────────────────────────────────────
    function showPopup(title, message, isRefund) {
        var pop   = document.getElementById('notificationPopup');
        var icon  = document.getElementById('notificationPopupIcon');
        var ttl   = document.getElementById('notificationPopupTitle');
        var msg   = document.getElementById('notificationPopupMessage');
        if (!pop || !ttl || !msg) return;

        ttl.textContent = title;
        msg.textContent = message;

        if (isRefund) {
            pop.classList.add('refund-popup');
            icon.className      = 'fas fa-undo-alt';
            icon.style.color    = '#16a34a';
        } else {
            pop.classList.remove('refund-popup');
            icon.className      = 'fas fa-bell';
            icon.style.color    = '#1a3a5f';
        }

        pop.classList.add('show');
        setTimeout(function(){ pop.classList.remove('show', 'refund-popup'); }, 6000);
    }

    // ── Poll check_reminders.php (your existing endpoint) ────────────────────
    function checkReminders() {
        fetch('check_reminders.php')
            .then(function(r){ return r.json(); })
            .then(function(data) {
                if (data.status === 'success' && data.reminders && data.reminders.length > 0) {
                    data.reminders.forEach(function(rem) {
                        // Detect if this is a refund message from the server
                        var isRefund = rem.type === 'refund' || (rem.message && rem.message.indexOf('Refund') !== -1);
                        showPopup(
                            isRefund ? 'Refund Processed ✅' : 'Reminder',
                            rem.message,
                            isRefund
                        );
                    });
                }
            })
            .catch(function(e){ console.error('Error checking reminders:', e); });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            checkReminders();
            setInterval(checkReminders, 60 * 1000);
        });
    } else {
        checkReminders();
        setInterval(checkReminders, 60 * 1000);
    }
})();
</script>