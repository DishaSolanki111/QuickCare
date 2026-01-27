<?php
// patient_header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="header">
    <div>
        Welcome, <?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?>
    </div>

    <div class="notification-bell" onclick="toggleNotifications()">
        <i class="fas fa-bell"></i>

        <?php
        $reminder_count = mysqli_num_rows($reminder_query);
        if ($reminder_count > 0) {
            echo '<span class="notification-badge">' . $reminder_count . '</span>';
        }
        ?>

        <div class="notification-dropdown" id="notificationDropdown">
            <?php if ($reminder_count > 0): ?>
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
                                echo date(
                                    'M d, Y h:i A',
                                    strtotime($reminder['APPOINTMENT_DATE'] . ' ' . $reminder['APPOINTMENT_TIME'])
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-notifications">No new notifications</div>
            <?php endif; ?>
        </div>
    </div>
</div>
