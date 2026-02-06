<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';

// Use the same session variable name as in your login
 $doctor_id = $_SESSION['DOCTOR_ID'] ?? null;
 $doctor_name = 'Doctor';
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
}
?>
<!-- ✅ HEADER HTML YAHI RAHEGA -->
<header class="topbar">
    <h2><?php echo htmlspecialchars($page_title ?? 'Welcome back'); ?></h2>

    <div class="topbar-right">
        <div class="user-info">
            <img src="<?php echo $profile_image; ?>" alt="Doctor" class="user-avatar">

            <div class="user-details">
                <div class="name-row">
                    <span class="doctor-name">
                        <?php echo $doctor_name; ?>
                    </span>
                </div>

                <span class="date">
                    <?php echo date("F d, Y"); ?>
                </span>
            </div>
            
            <!-- 🔔 Bell - MOVED TO RIGHT SIDE -->
            <?php include 'appointment_notification.php'; ?>
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

.name-row {
    display: flex;
    align-items: center;
}

.doctor-name {
    font-weight: 600;
    color: #1a3a5f;
    font-size: 16px;
}

.date {
    color: #6b7280;
    font-size: 14px;
}

/* Notification bell styles - adjust as needed */
.notification-container {
    margin-left: 100px;
}
</style>