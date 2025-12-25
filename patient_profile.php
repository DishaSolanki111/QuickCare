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

// Fetch appointments data
 $appointments_query = mysqli_query($conn, "
    SELECT a.*, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME as SPECIALIZATION 
    FROM appointment_tbl a
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE a.PATIENT_ID = '$patient_id'
    ORDER BY a.APPOINTMENT_DATE DESC
");

// Fetch prescriptions data
 $prescriptions_query = mysqli_query($conn, "
    SELECT p.*, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME
    FROM prescription_tbl p
    JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    WHERE a.PATIENT_ID = '$patient_id'
    ORDER BY p.ISSUE_DATE DESC
");

// Fetch medicine reminders
 $reminders_query = mysqli_query($conn, "
    SELECT mr.*, m.MED_NAME
    FROM medicine_reminder_tbl mr
    JOIN medicine_tbl m ON mr.MEDICINE_ID = m.MEDICINE_ID
    WHERE mr.PATIENT_ID = '$patient_id'
    ORDER BY mr.REMINDER_TIME
");
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
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
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
            padding: 15px 25px;
            color: #D0D7E1;
            text-decoration: none;
            font-size: 17px;
            border-left: 4px solid transparent;
            background: #082637;
            text-align: center;
            margin-top: auto;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
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
        
        .notification-btn {
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
        
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 12px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            color: #777;
            transition: all 0.3s ease;
        }
        
        .tab.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--secondary-color);
        }
        
        .tab:hover {
            color: var(--primary-color);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .appointment-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .appointment-info h3 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .appointment-details {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        
        .appointment-detail {
            display: flex;
            align-items: center;
            color: #666;
        }
        
        .appointment-detail i {
            margin-right: 5px;
            color: var(--secondary-color);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-confirmed {
            background-color: rgba(46, 204, 113, 0.2);
            color: var(--accent-color);
        }
        
        .status-completed {
            background-color: rgba(52, 152, 219, 0.2);
            color: var(--secondary-color);
        }
        
        .status-cancelled {
            background-color: rgba(231, 76, 60, 0.2);
            color: var(--danger-color);
        }
        
        .prescription-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .prescription-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .prescription-header h3 {
            color: var(--primary-color);
        }
        
        .prescription-date {
            color: #777;
            font-size: 14px;
        }
        
        .medicine-list {
            margin-top: 15px;
        }
        
        .medicine-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .medicine-name {
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .medicine-details {
            color: #666;
            font-size: 14px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #777;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }
        
        .reminder-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .reminder-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 20px;
        }
        
        .reminder-content {
            flex: 1;
        }
        
        .reminder-content h4 {
            margin-bottom: 5px;
            color: var(--primary-color);
        }
        
        .reminder-time {
            color: #666;
            font-size: 14px;
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
            .sidebar {
                width: 70px;
            }
            
            .logo h1 span, .nav-item span {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .info-grid, .form-row {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .profile-section {
                flex-direction: column;
            }
            
            .appointment-card {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .appointment-details {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <img src="./uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">
            <h2>QuickCare</h2>
            <div class="nav">
                <a href="patient.php">Dashboard</a>
                <a class="active">My Profile</a>
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
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="welcome-msg">Personal Information</div>
                <div class="user-actions">
                    <button class="notification-btn">
                        <i class="far fa-bell"></i>
                        <span class="notification-badge">3</span>
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
                            <p>Patient ID: <?php echo htmlspecialchars($patient['PATIENT_ID']); ?></p>
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
                                    <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($patient['FIRST_NAME']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($patient['LAST_NAME']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($patient['DOB']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" name="gender" required>
                                        <option value="MALE" <?php echo $patient['GENDER'] == 'MALE' ? 'selected' : ''; ?>>Male</option>
                                        <option value="FEMALE" <?php echo $patient['GENDER'] == 'FEMALE' ? 'selected' : ''; ?>>Female</option>
                                        <option value="OTHER" <?php echo $patient['GENDER'] == 'OTHER' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="bloodGroup">Blood Group</label>
                                    <select class="form-control" id="bloodGroup" name="bloodGroup" required>
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
            
            <!-- Tabs Section -->
            <div class="tabs">
                <div class="tab active" data-tab="appointments">Appointments</div>
                <div class="tab" data-tab="prescriptions">Prescriptions</div>
                <div class="tab" data-tab="reminders">Medicine Reminders</div>
            </div>
            
            <!-- Tab Content -->
            <div class="tab-content active" id="appointments">
                <h3 style="margin-bottom: 20px;">Upcoming Appointments</h3>
                
                <?php
                $upcoming_appointments = [];
                $past_appointments = [];
                
                if (mysqli_num_rows($appointments_query) > 0) {
                    while ($appointment = mysqli_fetch_assoc($appointments_query)) {
                        if ($appointment['APPOINTMENT_DATE'] >= date('Y-m-d')) {
                            $upcoming_appointments[] = $appointment;
                        } else {
                            $past_appointments[] = $appointment;
                        }
                    }
                }
                
                // Display upcoming appointments
                if (count($upcoming_appointments) > 0) {
                    foreach ($upcoming_appointments as $appointment) {
                        $status_class = '';
                        if ($appointment['STATUS'] == 'SCHEDULED') {
                            $status_class = 'status-confirmed';
                        } elseif ($appointment['STATUS'] == 'COMPLETED') {
                            $status_class = 'status-completed';
                        } elseif ($appointment['STATUS'] == 'CANCELLED') {
                            $status_class = 'status-cancelled';
                        }
                        ?>
                        <div class="appointment-card">
                            <div class="appointment-info">
                                <h3>Dr. <?php echo htmlspecialchars($appointment['DOC_FNAME'] . ' ' . $appointment['DOC_LNAME']); ?></h3>
                                <p><?php echo htmlspecialchars($appointment['SPECIALIZATION']); ?></p>
                                <div class="appointment-details">
                                    <div class="appointment-detail">
                                        <i class="far fa-calendar"></i>
                                        <span><?php echo date('F d, Y', strtotime($appointment['APPOINTMENT_DATE'])); ?></span>
                                    </div>
                                    <div class="appointment-detail">
                                        <i class="far fa-clock"></i>
                                        <span><?php echo date('h:i A', strtotime($appointment['APPOINTMENT_TIME'])); ?></span>
                                    </div>
                                    <div class="appointment-detail">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Main Hospital, Room 204</span>
                                    </div>
                                </div>
                            </div>
                            <div class="appointment-actions">
                                <span class="status-badge <?php echo $status_class; ?>"><?php echo ucfirst(strtolower($appointment['STATUS'])); ?></span>
                                <div class="btn-group" style="margin-top: 15px;">
                                    <button class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Reschedule
                                    </button>
                                    <button class="btn btn-danger">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="empty-state">
                        <i class="far fa-calendar-times"></i>
                        <p>No upcoming appointments</p>
                    </div>';
                }
                ?>
                
                <h3 style="margin: 30px 0 20px;">Past Appointments</h3>
                
                <?php
                // Display past appointments
                if (count($past_appointments) > 0) {
                    foreach ($past_appointments as $appointment) {
                        ?>
                        <div class="appointment-card">
                            <div class="appointment-info">
                                <h3>Dr. <?php echo htmlspecialchars($appointment['DOC_FNAME'] . ' ' . $appointment['DOC_LNAME']); ?></h3>
                                <p><?php echo htmlspecialchars($appointment['SPECIALIZATION']); ?></p>
                                <div class="appointment-details">
                                    <div class="appointment-detail">
                                        <i class="far fa-calendar"></i>
                                        <span><?php echo date('F d, Y', strtotime($appointment['APPOINTMENT_DATE'])); ?></span>
                                    </div>
                                    <div class="appointment-detail">
                                        <i class="far fa-clock"></i>
                                        <span><?php echo date('h:i A', strtotime($appointment['APPOINTMENT_TIME'])); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="appointment-actions">
                                <span class="status-badge status-completed">Completed</span>
                                <div class="btn-group" style="margin-top: 15px;">
                                    <button class="btn btn-primary">
                                        <i class="fas fa-file-medical"></i> View Prescription
                                    </button>
                                    <button class="btn btn-success">
                                        <i class="fas fa-star"></i> Leave Feedback
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="empty-state">
                        <i class="far fa-calendar-check"></i>
                        <p>No past appointments</p>
                    </div>';
                }
                ?>
            </div>
            
            <div class="tab-content" id="prescriptions">
                <h3 style="margin-bottom: 20px;">Recent Prescriptions</h3>
                
                <?php
                if (mysqli_num_rows($prescriptions_query) > 0) {
                    while ($prescription = mysqli_fetch_assoc($prescriptions_query)) {
                        // Get medicines for this prescription
                        $medicines_query = mysqli_query($conn, "
                            SELECT pm.MEDICINE_ID, pm.DOSAGE, pm.DURATION, pm.FREQUENCY, m.MED_NAME
                            FROM prescription_medicine_tbl pm
                            JOIN medicine_tbl m ON pm.MEDICINE_ID = m.MEDICINE_ID
                            WHERE pm.PRESCRIPTION_ID = " . $prescription['PRESCRIPTION_ID']
                        );
                        ?>
                        <div class="prescription-card">
                            <div class="prescription-header">
                                <h3>Dr. <?php echo htmlspecialchars($prescription['DOC_FNAME'] . ' ' . $prescription['DOC_LNAME']); ?></h3>
                                <span class="prescription-date"><?php echo date('F d, Y', strtotime($prescription['ISSUE_DATE'])); ?></span>
                            </div>
                            
                            <div class="prescription-details">
                                <p><strong>Diagnosis:</strong> <?php echo htmlspecialchars($prescription['DIAGNOSIS']); ?></p>
                                <p><strong>Symptoms:</strong> <?php echo htmlspecialchars($prescription['SYMPTOMS']); ?></p>
                            </div>
                            
                            <h4 style="margin: 15px 0 10px;">Medications</h4>
                            <div class="medicine-list">
                                <?php
                                if (mysqli_num_rows($medicines_query) > 0) {
                                    while ($medicine = mysqli_fetch_assoc($medicines_query)) {
                                        ?>
                                        <div class="medicine-item">
                                            <div>
                                                <div class="medicine-name"><?php echo htmlspecialchars($medicine['MED_NAME']); ?></div>
                                                <div class="medicine-details"><?php echo htmlspecialchars($medicine['DOSAGE']); ?> - <?php echo htmlspecialchars($medicine['FREQUENCY']); ?></div>
                                            </div>
                                            <div class="medicine-details"><?php echo htmlspecialchars($medicine['DURATION']); ?></div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                            
                            <div class="btn-group" style="margin-top: 15px;">
                                <button class="btn btn-primary">
                                    <i class="fas fa-download"></i> Download PDF
                                </button>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="empty-state">
                        <i class="fas fa-file-medical"></i>
                        <p>No prescriptions found</p>
                    </div>';
                }
                ?>
            </div>
            
            <div class="tab-content" id="reminders">
                <h3 style="margin-bottom: 20px;">Medicine Reminders</h3>
                
                <?php
                if (mysqli_num_rows($reminders_query) > 0) {
                    while ($reminder = mysqli_fetch_assoc($reminders_query)) {
                        ?>
                        <div class="reminder-card">
                            <div class="reminder-icon">
                                <i class="fas fa-pills"></i>
                            </div>
                            <div class="reminder-content">
                                <h4><?php echo htmlspecialchars($reminder['MED_NAME']); ?></h4>
                                <p><?php echo htmlspecialchars($reminder['REMARKS']); ?></p>
                                <div class="reminder-time">
                                    <i class="far fa-clock"></i> Daily at <?php echo date('h:i A', strtotime($reminder['REMINDER_TIME'])); ?>
                                </div>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-primary">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="empty-state">
                        <i class="fas fa-bell-slash"></i>
                        <p>No medicine reminders set</p>
                    </div>';
                }
                ?>
                
                <div style="text-align: center; margin-top: 30px;">
                    <button class="btn btn-success">
                        <i class="fas fa-plus"></i> Add New Reminder
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabId = tab.getAttribute('data-tab');
                    
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    tab.classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                });
            });
            
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
        });
    </script>
</body>
</html>