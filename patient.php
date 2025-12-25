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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #D0D7E1;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background: #072D44;
            min-height: 100vh;
            color: white;
            padding-top: 30px;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #9CCDD8;
        }

        .sidebar a {
            display: block;
            padding: 15px 25px;
            color: #D0D7E1;
            text-decoration: none;
            font-size: 17px;
            border-left: 4px solid transparent;
        }

        .sidebar a:hover, .sidebar a.active {
            background: #064469;
            border-left: 4px solid #9CCDD8;
            color: white;
        }

        .logout {
            display: flex;
            padding: 10px 95px;
            color: #021e45;
            text-decoration: none;
            font-size: 17px;
            border-left: 4px solid transparent;
            background: #c5dbe8;
            text-align: center;
            margin-top: auto;
        }

        /* Main content */
        .main {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
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
        }

        .logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
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
    </style>
</head>
<body>
<div class="container">
    <!-- SIDEBAR -->
    <div class="sidebar">
        <img src="./uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">
        <h2>QuickCare</h2>
        <div class="nav">
            <a class="active">Dashboard</a>
            <a href="patient_profile.php"> My Profile</a>
            <a href="manage_appointments.php">Manage Appointments</a>
            <a href="doctor_schedule.php">View Doctor Schedule</a>
            <a href="prescriptions.php">My Prescriptions</a>
            <a href="medicine_reminder.php">Medicine Reminder</a>
            <a href="payments.php">Payments</a>
            <a href="feedback.php">Feedback</a>
            <a href="doctor_profiles.php">View Doctor Profile</a>
            <a href="logout.php">Logout</a>
        </div>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <!-- MAIN -->
    <div class="main">
        <div class="header">Welcome, <?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></div>
        
        <!-- Profile Info -->
        <div class="card profile-info">
            <div class="profile-avatar">
                <?php echo strtoupper(substr($patient['FIRST_NAME'], 0, 1) . substr($patient['LAST_NAME'], 0, 1)); ?>
            </div>
            <div class="profile-details">
                <h3><?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></h3>
                <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($patient['EMAIL']); ?></p>
                <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($patient['PHONE']); ?></p>
                <p><i class="fas fa-id-card"></i> Patient ID: <?php echo htmlspecialchars($patient['PATIENT_ID']); ?></p>
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
                <div>Upcoming Medicines</div>
                <div class="num"><?php echo $upcoming_medicines; ?></div>
            </div>
        </div>

        <!-- BUTTONS -->
        <div class="actions">
            <a href="book_appointment.php" class="btn">Book Appointment</a>
            <a href="prescriptions.php" class="btn">View Prescription</a>
            <a href="feedback.php" class="btn">Give Feedback</a>
            <a href="payments.php" class="btn">Make Payment</a>
        </div>
    </div>
</div>
</body>
</html>