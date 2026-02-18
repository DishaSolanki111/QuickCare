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

// Optional notifications (e.g. reminders or tasks) if a query is provided
 $notif_count = isset($reminder_query) && $reminder_query
    ? mysqli_num_rows($reminder_query)
    : 0;
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
                    <?php if ($notif_count > 0 && isset($reminder_query) && $reminder_query): ?>
                        <?php while ($row = mysqli_fetch_assoc($reminder_query)): ?>
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
                        <?php endwhile; ?>
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