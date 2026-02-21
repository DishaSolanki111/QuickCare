<?php
session_start();

if (!isset($_SESSION['PATIENT_ID'])) {
    header("Location: login_for_all.php");
    exit;
}

// Fetch patient data from database
include 'config.php';
 $patient_id = $_SESSION['PATIENT_ID'];

// Get patient details
 $patient_query = mysqli_query($conn, "SELECT * FROM patient_tbl WHERE PATIENT_ID = '$patient_id'");
 $patient = mysqli_fetch_assoc($patient_query);

// Get upcoming appointments - FIXED QUERY
 $appointment_query = mysqli_query($conn, "
    SELECT a.*, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME as SPECIALIZATION 
    FROM appointment_tbl a
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE a.PATIENT_ID = '$patient_id' AND a.APPOINTMENT_DATE >= CURDATE()
    ORDER BY a.APPOINTMENT_DATE ASC
    LIMIT 1
");
 $upcoming_appointment = mysqli_fetch_assoc($appointment_query);

// Get appointment counts
 $total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM appointment_tbl WHERE PATIENT_ID = '$patient_id'");
 $total_result = mysqli_fetch_assoc($total_query);
 $total_appointments = $total_result['total'];

 $completed_query = mysqli_query($conn, "SELECT COUNT(*) as completed FROM appointment_tbl WHERE PATIENT_ID = '$patient_id' AND STATUS = 'COMPLETED'");
 $completed_result = mysqli_fetch_assoc($completed_query);
 $completed_appointments = $completed_result['completed'];

// Get upcoming medicines count
 $medicine_query = mysqli_query($conn, "
    SELECT COUNT(*) as count FROM medicine_reminder_tbl 
    WHERE PATIENT_ID = '$patient_id' AND REMINDER_TIME >= CURTIME()
");
 $medicine_result = mysqli_fetch_assoc($medicine_query);
 $upcoming_medicines = $medicine_result['count'];

// Get appointment reminders
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
                --dark-blue: #072D44;
                --mid-blue: #064469;
                --soft-blue: #5790AB;
                --light-blue: #9CCDD8;
                --gray-blue: #D0D7E1;
                --white: #ffffff;
                --card-bg: #F6F9FB;
                --primary-color: #1a3a5f;
                --secondary-color: #3498db;
                --accent-color: #2ecc71;
                --danger-color: #e74c3c;
                --warning-color: #f39c12;
                --info-color: #17a2b8;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #D0D7E1;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Container for the entire layout */
        .container {
            display: flex;
            width: 100%;
            height: 100%;
        }

        /* Main content */
        .main {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            height: 100vh;
            overflow-y: auto;
        }

      
        /* Top bar */
        .header {
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            font-size: 28px;
            font-weight: 700;
            color: #064469;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
        }

        /* Notification Bell */
        .notification-bell {
            position: relative;
            cursor: pointer;
            color: #072D44;
            font-size: 24px;
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: #ff4d4d;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
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

        /* Cards */
        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            border-left: 8px solid #5790AB;
        }

        /* Grid Layout for Patient Panel */
        .grid {
            display: flex;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 35px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        /* Upcoming Appointment */
        .up-title {
            font-size: 16px;
            font-weight: 600;
            color: #072D44;
        }

        .up-box {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
        }

        .date-circle {
            width: 65px;
            height: 65px;
            background: #6c8e9d;        
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            color: white;
        }

        .status {
            margin-left:25px;
            background: #6c8e9d; 
            padding: 10px 14px;
            border-radius: 8px;
            font-weight: 700;
            height: fit-content;
            color: white;
        }

        /* Stat Box */
        .stat .num {
            font-size: 32px;
            font-weight: 800;
            color: #064469;
            margin-top: 10px;
        }

        /* Button Row */
        .actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            width:95%;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .btn {
            width: 80%;
            flex: 1;
            background: #072D44;
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            font-weight: 700;
            color: white;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn:hover {
            background: #064469;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #9CCDD8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #072D44;
        }

        .profile-details h3 {
            margin: 0;
            color: #072D44;
        }

        .profile-details p {
            margin: 5px 0;
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
        
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .main {
                margin-left: 200px;
                width: calc(100% - 200px);
            }
            
            .grid {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Import Sidebar -->
    <?php include 'patient_sidebar.php'; ?>

    <!-- MAIN -->
    <div class="main">
        <?php include 'patient_header.php'; ?>

        <!-- Profile Info -->
        <div class="card profile-info">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($patient['FIRST_NAME'], 0, 1) . substr($patient['LAST_NAME'], 0, 1)); ?>
            </div>
            <div class="profile-details">
                <h3><?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></h3>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($patient['EMAIL']); ?></p>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($patient['PHONE']); ?></p>

            </div>
        </div>
        
        <!-- GRID -->
        <div class="grid">
            <!-- UPCOMING APPOINTMENT -->
            <div class="card">
                <div class="up-title">Upcoming Appointment</div>
                <?php if ($upcoming_appointment): ?>
                    <div class="up-box">
                        <div style="display:flex; gap:15px;">
                            <div class="date-circle"><?php echo date('d', strtotime($upcoming_appointment['APPOINTMENT_DATE'])); ?></div>
                            <div>
                                <div><?php echo date('M d, Y', strtotime($upcoming_appointment['APPOINTMENT_DATE'])); ?></div>
                                <div style="margin-top:4px; color:#8ea7b5;"><?php echo date('h:i A', strtotime($upcoming_appointment['APPOINTMENT_TIME'])); ?> • Dr. <?php echo htmlspecialchars($upcoming_appointment['DOC_FNAME'] . ' ' . $upcoming_appointment['DOC_LNAME']); ?></div>
                                <div style="margin-top:4px; color:#8ea7b5;"><?php echo htmlspecialchars($upcoming_appointment['SPECIALIZATION']); ?></div>
                            </div>
                        </div>
                        <div class="status"><?php echo ucfirst($upcoming_appointment['STATUS']); ?></div>
                    </div>
                <?php else: ?>
                    <div style="padding: 20px 0; color: #666;">No upcoming appointments</div>
                <?php endif; ?>
            </div>
            
            <!-- STAT 1 -->
            <div class="card stat">
                <div>Total Appointments</div>
                <div class="num"><?php echo $total_appointments; ?></div>
            </div>
            
            <!-- STAT 2 -->
            <div class="card stat">
                <div>Completed Appointments</div>
                <div class="num"><?php echo $completed_appointments; ?></div>
            </div>
            
            <!-- STAT 3 -->
            <div class="card stat">
                <div>Medicines Reminders</div>
                <div class="num"><?php echo $upcoming_medicines; ?></div>
            </div>
        </div>

        <!-- BUTTONS -->
        <div class="actions">
            <a href="appointment.php" class="btn">Book Appointment</a>
            <a href="patinet_prescriptions.php" class="btn">View Prescription</a>
            <a href="patient_feedback.php" class="btn">Give Feedback</a>
            <a href="patient_payments.php" class="btn">Payment History</a>
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
    
    // Check for reminders when page loads
    document.addEventListener('DOMContentLoaded', function() {
        checkReminders();
        
        // Check for reminders every 5 minutes
        setInterval(checkReminders, 5 * 60 * 1000);
    });
</script>
</body>
</html>