<?php
// doctor_header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';
require_once 'notification_seen.php';

$doctor_id    = $_SESSION['DOCTOR_ID'] ?? null;
$doctor_name  = 'Doctor';
$profile_image = 'uploads/.png';

if ($doctor_id) {
    $stmt = $conn->prepare("SELECT FIRST_NAME, LAST_NAME, PROFILE_IMAGE FROM doctor_tbl WHERE DOCTOR_ID = ?");
    $stmt->bind_param("i", $doctor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $doc = $result->fetch_assoc();
        $doctor_name = 'Dr. ' . htmlspecialchars($doc['FIRST_NAME'] . ' ' . $doc['LAST_NAME']);
        $profile_image = !empty($doc['PROFILE_IMAGE'])
            ? htmlspecialchars($doc['PROFILE_IMAGE'])
            : $profile_image;
    }

    // Auto-fetch notifications for doctor
    if (!isset($reminder_query)) {
        $doctor_reminders = [];
        
        // 1. Rescheduled
        $reschedule_sql = "
            SELECT ar.APPOINTMENT_REMINDER_ID, ar.REMARKS, p.FIRST_NAME AS patient_fname, p.LAST_NAME AS patient_lname,
                   a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, 'Rescheduled by Patient' AS TITLE
            FROM appointment_reminder_tbl ar
            JOIN appointment_tbl a ON ar.APPOINTMENT_ID = a.APPOINTMENT_ID
            JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
            WHERE a.DOCTOR_ID = ? AND ar.REMARKS LIKE '[RESCHEDULED_BY_PATIENT]%'
            ORDER BY ar.APPOINTMENT_REMINDER_ID DESC LIMIT 10
        ";
        $rs_stmt = $conn->prepare($reschedule_sql);
        $rs_stmt->bind_param("i", $doctor_id);
        $rs_stmt->execute();
        $rs_res = $rs_stmt->get_result();
        while($r = $rs_res->fetch_assoc()) {
            $r['REMARKS'] = preg_replace('/^\[RESCHEDULED_BY_PATIENT\]\s*/', '', $r['REMARKS']);
            $doctor_reminders[] = $r;
        }

        // 2. Cancelled
        $cancel_sql = "
            SELECT ar.APPOINTMENT_REMINDER_ID, ar.REMARKS, p.FIRST_NAME AS patient_fname, p.LAST_NAME AS patient_lname,
                   a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, 'Cancelled by Patient' AS TITLE
            FROM appointment_reminder_tbl ar
            JOIN appointment_tbl a ON ar.APPOINTMENT_ID = a.APPOINTMENT_ID
            JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
            WHERE a.DOCTOR_ID = ? AND ar.REMARKS LIKE '[CANCELLED_BY_PATIENT]%'
            ORDER BY ar.APPOINTMENT_REMINDER_ID DESC LIMIT 10
        ";
        $cl_stmt = $conn->prepare($cancel_sql);
        $cl_stmt->bind_param("i", $doctor_id);
        $cl_stmt->execute();
        $cl_res = $cl_stmt->get_result();
        while($r = $cl_res->fetch_assoc()) {
            $r['REMARKS'] = preg_replace('/^\[CANCELLED_BY_PATIENT\]\s*/', '', $r['REMARKS']);
            $doctor_reminders[] = $r;
        }

        // 3. Upcoming
        $upcoming_sql = "
            SELECT a.APPOINTMENT_ID, 'Upcoming Appointment' AS TITLE, CONCAT('Appointment with ', p.FIRST_NAME, ' ', p.LAST_NAME) AS REMARKS,
                   a.APPOINTMENT_DATE, a.APPOINTMENT_TIME
            FROM appointment_tbl a
            JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
            WHERE a.DOCTOR_ID = ? AND a.STATUS IN ('SCHEDULED', 'APPROVED')
            AND TIMESTAMP(a.APPOINTMENT_DATE, a.APPOINTMENT_TIME) BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
            ORDER BY a.APPOINTMENT_DATE ASC, a.APPOINTMENT_TIME ASC LIMIT 10
        ";
        $up_stmt = $conn->prepare($upcoming_sql);
        $up_stmt->bind_param("i", $doctor_id);
        $up_stmt->execute();
        $up_res = $up_stmt->get_result();
        while($r = $up_res->fetch_assoc()) {
            $doctor_reminders[] = $r;
        }

        $allKeys  = [];
        foreach ($doctor_reminders as &$row) {
            $key = $row['APPOINTMENT_REMINDER_ID'] ?? $row['APPOINTMENT_ID'] ?? '';
            $k = qc_notification_make_key(
                'doctor',
                $doctor_id . '|' . $row['TITLE'] . '|' . $row['APPOINTMENT_DATE'] . '|' . $row['APPOINTMENT_TIME'] . '|' . $key
            );
            $row['_notif_key'] = $k;
            $allKeys[] = $k;
        }
        unset($row);

        $seenMap = qc_notification_seen_map($conn, 'doctor', $doctor_id, $allKeys);
        $toMarkSeen = [];
        $reminder_query = [];

        foreach ($doctor_reminders as $row) {
            $k = $row['_notif_key'];
            if (isset($seenMap[$k])) {
                continue;
            }
            $toMarkSeen[] = $k;
            $reminder_query[] = $row;
        }

        if (!empty($toMarkSeen)) {
            qc_notification_mark_seen($conn, 'doctor', $doctor_id, $toMarkSeen);
        }
    }
}

$notif_count = isset($reminder_query) ? count($reminder_query) : 0;
?>

<header class="topbar">
    <h2><?php echo htmlspecialchars($page_title ?? 'Welcome '); ?></h2>

    <div class="topbar-right">
        <div class="user-info">
            <img src="<?php echo $profile_image; ?>" alt="Doctor" class="user-avatar">

            <div class="user-details">
                <span class="doctor-name">
                    <?php echo $doctor_name; ?>
                </span>
            </div>

            <!-- Notification bell + dropdown -->
            <div class="notification-bell" onclick="toggleNotifications()">
                <i class="fas fa-bell"></i>

                <?php if ($notif_count > 0): ?>
                    <span class="notification-badge"><?php echo $notif_count; ?></span>
                <?php endif; ?>

                <div class="notification-dropdown" id="notificationDropdown">
                    <?php if ($notif_count > 0 && !empty($reminder_query)): ?>
                        <?php foreach ($reminder_query as $row): ?>
                            <div class="notification-item">
                                <div class="notification-icon" <?php echo ($row['TITLE'] == 'Cancelled by Patient') ? 'style="background: #fee2e2; color: #dc2626;"' : ''; ?>>
                                    <i class="fas <?php echo ($row['TITLE'] == 'Upcoming Appointment') ? 'fa-user-md' : 'fa-calendar-check'; ?>"></i>
                                </div>
                                <div class="notification-content">
                                    <div class="notification-title">
                                        <?php echo htmlspecialchars($row['TITLE']); ?>
                                    </div>
                                    <div class="notification-message">
                                        <?php echo htmlspecialchars($row['REMARKS']); ?>
                                    </div>
                                    <div class="notification-time">
                                        <?php echo date('M d, Y h:i A', strtotime($row['APPOINTMENT_DATE'] . ' ' . $row['APPOINTMENT_TIME'])); ?>
                                    </div>
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
    object-fit: cover;
    border: 2px solid #f0f0f0;
}

.user-details {
    display: flex;
    flex-direction: column;
}

.doctor-name {
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