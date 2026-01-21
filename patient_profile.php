<?php
session_start();

// Check if user is logged in as a patient
if (!isset($_SESSION['PATIENT_ID'])) {
    header("Location: login_for_all.php");
    exit;
}

include 'config.php';
 $patient_id = $_SESSION['PATIENT_ID'];

// Fetch patient data from database
 $patient_query = mysqli_query($conn, "SELECT * FROM patient_tbl WHERE PATIENT_ID = '$patient_id'");
 $patient = mysqli_fetch_assoc($patient_query);

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['firstName']);
    $last_name = mysqli_real_escape_string($conn, $_POST['lastName']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $blood_group = mysqli_real_escape_string($conn, $_POST['bloodGroup']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    $update_query = "UPDATE patient_tbl SET 
                   FIRST_NAME = '$first_name',
                   LAST_NAME = '$last_name',
                   DOB = '$dob',
                   GENDER = '$gender',
                   BLOOD_GROUP = '$blood_group',
                   PHONE = '$phone',
                   EMAIL = '$email',
                   ADDRESS = '$address'
                   WHERE PATIENT_ID = '$patient_id'";
    
    if (mysqli_query($conn, $update_query)) {
        // Update session variables
        $_SESSION['PATIENT_NAME'] = $first_name . ' ' . $last_name;
        
        // Refresh patient data
        $patient_query = mysqli_query($conn, "SELECT * FROM patient_tbl WHERE PATIENT_ID = '$patient_id'");
        $patient = mysqli_fetch_assoc($patient_query);
        
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile: " . mysqli_error($conn);
    }
}

// Get appointment reminders for notification
 $reminder_query = mysqli_query($conn, "
    SELECT ar.REMARKS, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, d.FIRST_NAME, d.LAST_NAME
    FROM appointment_reminder_tbl ar
    JOIN appointment_tbl a ON ar.APPOINTMENT_ID = a.APPOINTMENT_ID
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    WHERE a.PATIENT_ID = '$patient_id'
    AND a.APPOINTMENT_DATE >= CURDATE()
    AND ar.REMINDER_TIME <= CURTIME()
    AND ar.REMINDER_TIME > DATE_SUB(CURTIME(), INTERVAL 1 HOUR)
    ORDER BY ar.REMINDER_TIME DESC
    LIMIT 5
");
 $reminder_count = mysqli_num_rows($reminder_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a3a5f;
            --secondary-color: #3498db;
            --accent-color: #2ecc71;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #17a2b8;
             --dark-blue: #072D44;
            --mid-blue: #064469;
            --soft-blue: #5790AB;
            --light-blue: #9CCDD8;
            --gray-blue: #D0D7E1;
            --white: #ffffff;
            --card-bg: #F6F9FB;
            --primary-color: #1a3a5f;
      
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
   body {
    background-color: #f5f7fa;
    color: #333;
    line-height: 1.6;
    margin: 0;
    padding: 0;
    height: 100vh;
    overflow-y: scroll;
}


.container {
    display: flex;
    min-height: 100vh;
    height: 100%;
}

.main-content {
    flex: 1;
    margin-left: 250px;
    padding: 20px;
    height: 100%;
    overflow-y: auto;
}
html {
    height: 100%;
    
}

        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }
        
        .welcome-msg {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .user-actions {
            display: flex;
            align-items: center;
        }
        
        /* Notification Bell Styles */
        .notification-bell {
            position: relative;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--dark-color);
            margin-right: 20px;
            cursor: pointer;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }
        
        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            width: 350px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        
        .notification-dropdown.show {
            display: block;
        }
        
        .notification-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: flex-start;
        }
        
        .notification-item:last-child {
            border-bottom: none;
        }
        
        .notification-icon {
            color: #28a745;
            margin-right: 10px;
            margin-top: 2px;
        }
        
        .notification-content {
            flex: 1;
        }
        
        .notification-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #072D44;
        }
        
        .notification-message {
            font-size: 14px;
            color: #666;
        }
        
        .notification-time {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        
        .no-notifications {
            padding: 20px;
            text-align: center;
            color: #666;
        }
        
        /* Notification popup */
        .notification-popup {
            position: fixed;
            top: 20px;
            right: 20px;
            width: 350px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease-out;
        }
        
        .notification-popup.show {
            transform: translateX(0);
        }
        
        .notification-popup-content {
            display: flex;
            align-items: center;
            padding: 15px;
        }
        
        .notification-popup-icon {
            color: #28a745;
            font-size: 20px;
            margin-right: 12px;
        }
        
        .notification-popup-message {
            flex-grow: 1;
            font-size: 14px;
            color: #333;
        }
        
        .notification-popup-close {
            background: none;
            border: none;
            font-size: 18px;
            color: #999;
            cursor: pointer;
            padding: 0;
            margin-left: 10px;
        }
        
        .notification-popup-close:hover {
            color: #333;
        }
        
        .user-dropdown {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .profile-section {
            display: flex;
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .profile-card {
            flex: 1;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 25px;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: bold;
            margin-right: 20px;
        }
        
        .profile-title h2 {
            font-size: 28px;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .profile-title p {
            color: #777;
            font-size: 16px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .info-item {
            margin-bottom: 15px;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
            display: block;
        }
        
        .info-value {
            color: #555;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-success {
            background-color: var(--accent-color);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #27ae60;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .edit-profile-form {
            display: none;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 200px;
            }
            
            .info-grid, .form-row {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .profile-section {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Import Sidebar -->
        <?php include 'patient_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="welcome-msg">Personal Information</div>
                <div class="user-actions">
                    <button class="notification-bell" onclick="toggleNotifications()">
                        <i class="far fa-bell"></i>
                        <?php if ($reminder_count > 0): ?>
                            <span class="notification-badge"><?php echo $reminder_count; ?></span>
                        <?php endif; ?>
                        <div class="notification-dropdown" id="notificationDropdown">
                            <?php if ($reminder_count > 0): ?>
                                <?php 
                                // Reset the result pointer to beginning
                                mysqli_data_seek($reminder_query, 0);
                                while ($reminder = mysqli_fetch_assoc($reminder_query)): 
                                ?>
                                    <div class="notification-item">
                                        <div class="notification-icon">
                                            <i class="fas fa-calendar-check"></i>
                                        </div>
                                        <div class="notification-content">
                                            <div class="notification-title">Appointment Reminder</div>
                                            <div class="notification-message"><?php echo htmlspecialchars($reminder['REMARKS']); ?></div>
                                            <div class="notification-time"><?php echo date('M d, Y h:i A', strtotime($reminder['APPOINTMENT_DATE'] . ' ' . $reminder['APPOINTMENT_TIME'])); ?></div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="no-notifications">No new notifications</div>
                            <?php endif; ?>
                        </div>
                    </button>
                    <div class="user-dropdown">
                        <div class="user-avatar"><?php echo strtoupper(substr($patient['FIRST_NAME'], 0, 1) . substr($patient['LAST_NAME'], 0, 1)); ?></div>
                        <span><?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></span>
                        <i class="fas fa-chevron-down" style="margin-left: 8px;"></i>
                    </div>
                </div>
            </div>
            
            <!-- Success/Error Messages -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Profile Section -->
            <div class="profile-section">
                <!-- Personal Information Card -->
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar"><?php echo strtoupper(substr($patient['FIRST_NAME'], 0, 1) . substr($patient['LAST_NAME'], 0, 1)); ?></div>
                        <div class="profile-title">
                            <h2><?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></h2>
                    
                        </div>
                    </div>
                    
                    <!-- Profile View -->
                    <div id="profileView">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">First Name</span>
                                <span class="info-value"><?php echo htmlspecialchars($patient['FIRST_NAME']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Last Name</span>
                                <span class="info-value"><?php echo htmlspecialchars($patient['LAST_NAME']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Date of Birth</span>
                                <span class="info-value"><?php echo date('F d, Y', strtotime($patient['DOB'])); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Gender</span>
                                <span class="info-value"><?php echo htmlspecialchars($patient['GENDER']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Blood Group</span>
                                <span class="info-value"><?php echo htmlspecialchars($patient['BLOOD_GROUP']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Phone Number</span>
                                <span class="info-value"><?php echo htmlspecialchars($patient['PHONE']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email Address</span>
                                <span class="info-value"><?php echo htmlspecialchars($patient['EMAIL']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Address</span>
                                <span class="info-value"><?php echo htmlspecialchars($patient['ADDRESS']); ?></span>
                            </div>
                        </div>
                        
                        <div class="btn-group">
                            <button class="btn btn-primary" id="editProfileBtn">
                                <i class="fas fa-edit"></i> Edit Profile
                            </button>
                            <button class="btn btn-danger">
                                <i class="fas fa-key"></i> Change Password
                            </button>
                        </div>
                    </div>
                    
                    <!-- Edit Profile Form -->
                    <div id="editProfileForm" class="edit-profile-form">
                        <form method="POST" action="patient_profile.php">
                            <input type="hidden" name="update_profile" value="1">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($patient['FIRST_NAME']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($patient['LAST_NAME']); ?>" disabled>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($patient['DOB']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" name="gender" disabled>
                                        <option value="MALE" <?php echo $patient['GENDER'] == 'MALE' ? 'selected' : ''; ?>>Male</option>
                                        <option value="FEMALE" <?php echo $patient['GENDER'] == 'FEMALE' ? 'selected' : ''; ?>>Female</option>
                                        <option value="OTHER" <?php echo $patient['GENDER'] == 'OTHER' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="bloodGroup">Blood Group</label>
                                    <select class="form-control" id="bloodGroup" name="bloodGroup" disabled>
                                        <option value="A+" <?php echo $patient['BLOOD_GROUP'] == 'A+' ? 'selected' : ''; ?>>A+</option>
                                        <option value="A-" <?php echo $patient['BLOOD_GROUP'] == 'A-' ? 'selected' : ''; ?>>A-</option>
                                        <option value="B+" <?php echo $patient['BLOOD_GROUP'] == 'B+' ? 'selected' : ''; ?>>B+</option>
                                        <option value="B-" <?php echo $patient['BLOOD_GROUP'] == 'B-' ? 'selected' : ''; ?>>B-</option>
                                        <option value="O+" <?php echo $patient['BLOOD_GROUP'] == 'O+' ? 'selected' : ''; ?>>O+</option>
                                        <option value="O-" <?php echo $patient['BLOOD_GROUP'] == 'O-' ? 'selected' : ''; ?>>O-</option>
                                        <option value="AB+" <?php echo $patient['BLOOD_GROUP'] == 'AB+' ? 'selected' : ''; ?>>AB+</option>
                                        <option value="AB-" <?php echo $patient['BLOOD_GROUP'] == 'AB-' ? 'selected' : ''; ?>>AB-</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($patient['PHONE']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($patient['EMAIL']); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($patient['ADDRESS']); ?></textarea>
                            </div>
                            
                            <div class="btn-group">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-danger" id="cancelEditBtn">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Notification Popup -->
    <div class="notification-popup" id="notificationPopup">
        <div class="notification-popup-content">
            <div class="notification-popup-icon">
                <i class="fas fa-bell"></i>
            </div>
            <div class="notification-popup-message" id="notificationPopupMessage">
                <!-- Message will be inserted here -->
            </div>
            <button class="notification-popup-close" onclick="closeNotificationPopup()">&times;</button>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Edit profile functionality
            const editProfileBtn = document.getElementById('editProfileBtn');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const profileView = document.getElementById('profileView');
            const editProfileForm = document.getElementById('editProfileForm');
            
            editProfileBtn.addEventListener('click', () => {
                profileView.style.display = 'none';
                editProfileForm.style.display = 'block';
            });
            
            cancelEditBtn.addEventListener('click', () => {
                profileView.style.display = 'block';
                editProfileForm.style.display = 'none';
            });
            
            // Check for new reminders
            checkReminders();
            
            // Check for reminders every 5 minutes
            setInterval(checkReminders, 5 * 60 * 1000);
        });
        
        // Toggle notification dropdown
        function toggleNotifications() {
            const dropdown = document.getElementById('notificationDropdown');
            dropdown.classList.toggle('show');
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function closeDropdown(e) {
                if (!e.target.closest('.notification-bell')) {
                    dropdown.classList.remove('show');
                    document.removeEventListener('click', closeDropdown);
                }
            });
        }
        
        // Close notification popup
        function closeNotificationPopup() {
            const popup = document.getElementById('notificationPopup');
            popup.classList.remove('show');
        }
        
        // Check for new reminders
        function checkReminders() {
            fetch('check_reminders.php')
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success' && data.reminders.length > 0) {
                        // Show each reminder as a popup notification
                        data.reminders.forEach(reminder => {
                            showNotificationPopup(reminder.message);
                        });
                        
                        // Update notification badge
                        const badge = document.querySelector('.notification-badge');
                        if (badge) {
                            badge.textContent = data.reminders.length;
                            badge.style.display = 'flex';
                        } else {
                            const bell = document.querySelector('.notification-bell');
                            const newBadge = document.createElement('span');
                            newBadge.className = 'notification-badge';
                            newBadge.textContent = data.reminders.length;
                            bell.appendChild(newBadge);
                        }
                    }
                })
                .catch(error => console.error('Error checking reminders:', error));
        }
        
        // Show notification popup
        function showNotificationPopup(message) {
            const popup = document.getElementById('notificationPopup');
            const messageElement = document.getElementById('notificationPopupMessage');
            
            messageElement.textContent = message;
            popup.classList.add('show');
            
            // Auto hide after 5 seconds
            setTimeout(() => {
                popup.classList.remove('show');
            }, 5000);
        }
    </script>
</body>
</html>