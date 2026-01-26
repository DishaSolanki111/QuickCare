<?php
session_start();

// Check if user is logged in as a receptionist
if (!isset($_SESSION['RECEPTIONIST_ID'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';
include 'recept_sidebar.php'; 
 $receptionist_id = $_SESSION['RECEPTIONIST_ID'];

// Fetch receptionist data from database
 $receptionist_query = mysqli_query($conn, "SELECT * FROM receptionist_tbl WHERE RECEPTIONIST_ID = '$receptionist_id'");
 $receptionist = mysqli_fetch_assoc($receptionist_query);

// Fetch receptionist statistics
 $appointment_count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM appointment_tbl a 
INNER JOIN doctor_schedule_tbl ds ON a.SCHEDULE_ID = ds.SCHEDULE_ID 
WHERE ds.RECEPTIONIST_ID = '$receptionist_id'");
 $appointment_count = mysqli_fetch_assoc($appointment_count_query);

 $today_appointments_query = mysqli_query($conn, "SELECT COUNT(*) as today FROM appointment_tbl a 
INNER JOIN doctor_schedule_tbl ds ON a.SCHEDULE_ID = ds.SCHEDULE_ID 
WHERE ds.RECEPTIONIST_ID = '$receptionist_id' AND a.APPOINTMENT_DATE = CURDATE()");
 $today_appointments = mysqli_fetch_assoc($today_appointments_query);

 $medicine_count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM medicine_tbl WHERE RECEPTIONIST_ID = '$receptionist_id'");
 $medicine_count = mysqli_fetch_assoc($medicine_count_query);

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Only update phone, email, and address
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['name'] != '') {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                $update_image_query = "UPDATE receptionist_tbl SET PROFILE_IMAGE = '$target_file' WHERE RECEPTIONIST_ID = '$receptionist_id'";
                mysqli_query($conn, $update_image_query);
                $receptionist['PROFILE_IMAGE'] = $target_file;
            }
        }
    }
    
    // Only update phone, email, and address
    $update_query = "UPDATE receptionist_tbl SET 
                   PHONE = '$phone',
                   EMAIL = '$email',
                   ADDRESS = '$address'
                   WHERE RECEPTIONIST_ID = '$receptionist_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $success_message = "Profile updated successfully!";
        
        // Refresh receptionist data
        $receptionist_query = mysqli_query($conn, "SELECT * FROM receptionist_tbl WHERE RECEPTIONIST_ID = '$receptionist_id'");
        $receptionist = mysqli_fetch_assoc($receptionist_query);
    } else {
        $error_message = "Error updating profile: " . mysqli_error($conn);
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    $password_query = "SELECT PSWD FROM receptionist_tbl WHERE RECEPTIONIST_ID = '$receptionist_id'";
    $password_result = mysqli_query($conn, $password_query);
    $password_data = mysqli_fetch_assoc($password_result);
    
    if (password_verify($current_password, $password_data['PSWD'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            $update_password_query = "UPDATE receptionist_tbl SET PSWD = '$hashed_password' WHERE RECEPTIONIST_ID = '$receptionist_id'";
            
            if (mysqli_query($conn, $update_password_query)) {
                $password_success = "Password changed successfully!";
            } else {
                $password_error = "Error changing password: " . mysqli_error($conn);
            }
        } else {
            $password_error = "New passwords do not match!";
        }
    } else {
        $password_error = "Current password is incorrect!";
    }
}

// Handle 2FA toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_2fa'])) {
    $enable_2fa = isset($_POST['enable_2fa']) ? 1 : 0;
    
    // In a real application, you would save this to the database
    // For now, we'll just set a session variable
    $_SESSION['2FA_ENABLED'] = $enable_2fa;
    
    $security_success = "Two-factor authentication " . ($enable_2fa ? "enabled" : "disabled") . " successfully!";
}

// Check if 2FA is enabled (in a real app, this would come from the database)
 $two_fa_enabled = isset($_SESSION['2FA_ENABLED']) ? $_SESSION['2FA_ENABLED'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - QuickCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
        }
        
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #F5F8FA;
            display: flex;
        }
        
        .main-content {
            margin-left: 240px;
            padding: 20px;
            width: calc(100% - 240px);
        }
        
        .topbar {
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .topbar h1 {
            margin: 0;
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
        
        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--light-blue);
            margin-right: 25px;
        }
        
        .profile-info h2 {
            margin: 0 0 10px 0;
            color: var(--primary-color);
        }
        
        .profile-info p {
            margin: 5px 0;
            color: #666;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 24px;
            color: white;
        }
        
        .stat-icon.appointments {
            background: linear-gradient(135deg, #3498db, #2980b9);
        }
        
        .stat-icon.today {
            background: linear-gradient(135deg, #2ecc71, #27ae60);
        }
        
        .stat-icon.medicines {
            background: linear-gradient(135deg, #9b59b6, #8e44ad);
        }
        
        .stat-info h3 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .stat-info p {
            margin: 0;
            color: #666;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-blue);
            padding: 15px 20px;
            font-weight: 600;
            color: var(--primary-color);
            border-radius: 10px 10px 0 0 !important;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .btn-success {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-success:hover {
            background-color: #27ae60;
            border-color: #27ae60;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: none;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            color: #27ae60;
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            color: #c0392b;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .nav-tabs {
            border-bottom: 2px solid var(--gray-blue);
            margin-bottom: 20px;
        }
        
        .nav-tabs .nav-link {
            color: var(--primary-color);
            border: none;
            border-bottom: 3px solid transparent;
            border-radius: 0;
            padding: 12px 20px;
            margin-right: 5px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--secondary-color);
            border-bottom-color: var(--secondary-color);
            background: none;
        }
        
        .nav-tabs .nav-link:hover {
            border-bottom-color: #eee;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .info-item {
            margin-bottom: 15px;
            padding: 15px;
            background: var(--card-bg);
            border-radius: 8px;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 5px;
            display: block;
            font-size: 14px;
        }
        
        .info-value {
            color: #555;
            font-size: 16px;
        }
        
        .activity-item {
            display: flex;
            align-items: flex-start;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        
        .activity-item:last-child {
            border-bottom: none;
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            color: white;
            flex-shrink: 0;
        }
        
        .activity-icon.appointment {
            background: var(--secondary-color);
        }
        
        .activity-icon.medicine {
            background: var(--accent-color);
        }
        
        .activity-icon.system {
            background: var(--primary-color);
        }
        
        .activity-details h4 {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .activity-details p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        
        .activity-time {
            margin-left: auto;
            color: #999;
            font-size: 14px;
        }
        
        .security-option {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px;
            border-radius: 8px;
            background: var(--card-bg);
            margin-bottom: 15px;
        }
        
        .security-option h4 {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .security-option p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }
        
        .form-check-input:checked {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .profile-image-upload {
            position: relative;
            display: inline-block;
            cursor: pointer;
        }
        
        .profile-image-upload .upload-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .profile-image-upload:hover .upload-overlay {
            opacity: 1;
        }
        
        .profile-image-upload .upload-overlay i {
            color: white;
            font-size: 24px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar h2, .sidebar a span {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-avatar {
                margin-right: 0;
                margin-bottom: 15px;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <h1>My Profile</h1>
            <p>Welcome, <?php echo htmlspecialchars($receptionist['FIRST_NAME'] . ' ' . $receptionist['LAST_NAME']); ?></p>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill me-2"></i><?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($password_success)): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i><?php echo $password_success; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($password_error)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill me-2"></i><?php echo $password_error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($security_success)): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i><?php echo $security_success; ?>
            </div>
        <?php endif; ?>
        
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-image-upload" onclick="document.getElementById('profile_image_input').click()">
                <img src="<?php echo !empty($receptionist['PROFILE_IMAGE']) ? $receptionist['PROFILE_IMAGE'] : 'https://picsum.photos/seed/receptionist/120/120.jpg'; ?>" alt="Profile" class="profile-avatar">
                <div class="upload-overlay">
                    <i class="bi bi-camera-fill"></i>
                </div>
                <input type="file" id="profile_image_input" name="profile_image" style="display: none;" accept="image/*" onchange="handleImageUpload(this)">
            </div>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($receptionist['FIRST_NAME'] . ' ' . $receptionist['LAST_NAME']); ?></h2>
                <p><i class="bi bi-envelope-fill me-2"></i><?php echo htmlspecialchars($receptionist['EMAIL']); ?></p>
                <p><i class="bi bi-telephone-fill me-2"></i><?php echo htmlspecialchars($receptionist['PHONE']); ?></p>
                <p><i class="bi bi-geo-alt-fill me-2"></i><?php echo htmlspecialchars($receptionist['ADDRESS']); ?></p>
            </div>
        </div>
        
        <!-- Statistics Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon appointments">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $appointment_count['total']; ?></h3>
                    <p>Total Appointments</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon today">
                    <i class="bi bi-calendar-day"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $today_appointments['today']; ?></h3>
                    <p>Today's Appointments</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon medicines">
                    <i class="bi bi-capsule"></i>
                </div>
                <div class="stat-info">
                    <h3><?php echo $medicine_count['total']; ?></h3>
                    <p>Medicines Managed</p>
                </div>
            </div>
        </div>
        
        <!-- Profile Card -->
        <div class="card">
            <div class="card-header">
                <h3>Account Details</h3>
            </div>
            <div class="card-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-pane" type="button" role="tab" aria-controls="profile-pane" aria-selected="true">Profile</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="activity-tab" data-bs-toggle="tab" data-bs-target="#activity-pane" type="button" role="tab" aria-controls="activity-pane" aria-selected="false">Activity</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security-pane" type="button" role="tab" aria-controls="security-pane" aria-selected="false">Security</button>
                    </li>
                </ul>
                
                <!-- Tab Content -->
                <div class="tab-content" id="profileTabContent">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="profile-pane" role="tabpanel" aria-labelledby="profile-tab">
                        <!-- Profile View Form -->
                        <div id="profileViewForm">
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">First Name</span>
                                    <span class="info-value"><?php echo htmlspecialchars($receptionist['FIRST_NAME']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Last Name</span>
                                    <span class="info-value"><?php echo htmlspecialchars($receptionist['LAST_NAME']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Date of Birth</span>
                                    <span class="info-value"><?php echo date('F d, Y', strtotime($receptionist['DOB'])); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Gender</span>
                                    <span class="info-value"><?php echo htmlspecialchars($receptionist['GENDER']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Phone Number</span>
                                    <span class="info-value"><?php echo htmlspecialchars($receptionist['PHONE']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Email Address</span>
                                    <span class="info-value"><?php echo htmlspecialchars($receptionist['EMAIL']); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Date of Joining</span>
                                    <span class="info-value"><?php echo date('F d, Y', strtotime($receptionist['DOJ'])); ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Username</span>
                                    <span class="info-value"><?php echo htmlspecialchars($receptionist['USERNAME']); ?></span>
                                </div>
                                <div class="info-item" style="grid-column: 1 / -1;">
                                    <span class="info-label">Address</span>
                                    <span class="info-value"><?php echo htmlspecialchars($receptionist['ADDRESS']); ?></span>
                                </div>
                            </div>
                            
                            <div class="text-end mt-4">
                                <button type="button" class="btn btn-primary" id="editProfileBtn">
                                    <i class="bi bi-pencil"></i> Edit Profile
                                </button>
                            </div>
                        </div>
                        
                        <!-- Edit Profile Form (Hidden by default) -->
                        <form method="POST" action="recep_profile.php" id="editProfileForm" style="display: none;" enctype="multipart/form-data">
                            <input type="hidden" name="update_profile" value="1">
                            
                            <div class="info-grid">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($receptionist['PHONE']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($receptionist['EMAIL']); ?>" required>
                                </div>
                                <div class="form-group" style="grid-column: 1 / -1;">
                                    <label for="address">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($receptionist['ADDRESS']); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-danger" id="cancelEditBtn">
                                    <i class="bi bi-x"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <!-- Activity Tab -->
                    <div class="tab-pane fade" id="activity-pane" role="tabpanel" aria-labelledby="activity-tab">
                        <div class="activity-list">
                            <?php
                            // Fetch recent activities
                            $recent_appointments = mysqli_query($conn, "SELECT a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, p.FIRST_NAME, p.LAST_NAME, d.FIRST_NAME as DOC_FIRST_NAME, d.LAST_NAME as DOC_LAST_NAME 
                            FROM appointment_tbl a 
                            INNER JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID 
                            INNER JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID 
                            INNER JOIN doctor_schedule_tbl ds ON a.SCHEDULE_ID = ds.SCHEDULE_ID 
                            WHERE ds.RECEPTIONIST_ID = '$receptionist_id' 
                            ORDER BY a.CREATED_AT DESC 
                            LIMIT 5");
                            
                            if (mysqli_num_rows($recent_appointments) > 0) {
                                while ($appointment = mysqli_fetch_assoc($recent_appointments)) {
                                    echo '<div class="activity-item">
                                        <div class="activity-icon appointment">
                                            <i class="bi bi-calendar-plus"></i>
                                        </div>
                                        <div class="activity-details">
                                            <h4>Appointment Scheduled</h4>
                                            <p>Appointment for ' . htmlspecialchars($appointment['FIRST_NAME'] . ' ' . $appointment['LAST_NAME']) . ' with Dr. ' . htmlspecialchars($appointment['DOC_FIRST_NAME'] . ' ' . $appointment['DOC_LAST_NAME']) . '</p>
                                        </div>
                                        <div class="activity-time">' . date('M d, Y', strtotime($appointment['APPOINTMENT_DATE'])) . '</div>
                                    </div>';
                                }
                            } else {
                                echo '<p>No recent activities found.</p>';
                            }
                            
                            $recent_medicines = mysqli_query($conn, "SELECT m.MED_NAME, m.DESCRIPTION, m.CREATED_AT 
                            FROM medicine_tbl m 
                            WHERE m.RECEPTIONIST_ID = '$receptionist_id' 
                            ORDER BY m.CREATED_AT DESC 
                            LIMIT 3");
                            
                            if (mysqli_num_rows($recent_medicines) > 0) {
                                while ($medicine = mysqli_fetch_assoc($recent_medicines)) {
                                    echo '<div class="activity-item">
                                        <div class="activity-icon medicine">
                                            <i class="bi bi-capsule"></i>
                                        </div>
                                        <div class="activity-details">
                                            <h4>Medicine Added</h4>
                                            <p>' . htmlspecialchars($medicine['MED_NAME']) . ' - ' . htmlspecialchars($medicine['DESCRIPTION']) . '</p>
                                        </div>
                                        <div class="activity-time">' . date('M d, Y', strtotime($medicine['CREATED_AT'])) . '</div>
                                    </div>';
                                }
                            }
                            ?>
                            
                            <div class="activity-item">
                                <div class="activity-icon system">
                                    <i class="bi bi-person-plus"></i>
                                </div>
                                <div class="activity-details">
                                    <h4>Account Created</h4>
                                    <p>Your account was created on <?php echo date('F d, Y', strtotime($receptionist['DOJ'])); ?></p>
                                </div>
                                <div class="activity-time"><?php echo date('M d, Y', strtotime($receptionist['DOJ'])); ?></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Security Tab -->
                    <div class="tab-pane fade" id="security-pane" role="tabpanel" aria-labelledby="security-tab">
                        <div class="security-options">
                            <div class="security-option">
                                <div>
                                    <h4>Change Password</h4>
                                    <p>Regularly update your password to keep your account secure</p>
                                </div>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    Change Password
                                </button>
                            </div>
                            
                            <div class="security-option">
                                <div>
                                    <h4>Two-Factor Authentication</h4>
                                    <p>Add an extra layer of security to your account</p>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="2faSwitch" <?php echo $two_fa_enabled ? 'checked' : ''; ?> onchange="toggle2FA(this)">
                                    <label class="form-check-label" for="2faSwitch"></label>
                                </div>
                            </div>
                            
                            <div class="security-option">
                                <div>
                                    <h4>Login History</h4>
                                    <p>View your recent login activity</p>
                                </div>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#loginHistoryModal">
                                    View History
                                </button>
                            </div>
                        </div>
                        
                        <div class="mt-4">
                            <h4 class="mb-3">Password Requirements</h4>
                            <ul class="list-group">
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Minimum 8 characters
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    At least one uppercase letter
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    At least one lowercase letter
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    At least one number
                                </li>
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    At least one special character
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Change Password Modal -->
    <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="recep_profile.php">
                    <div class="modal-body">
                        <input type="hidden" name="change_password" value="1">
                        
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check"></i> Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Login History Modal -->
    <div class="modal fade" id="loginHistoryModal" tabindex="-1" aria-labelledby="loginHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginHistoryModalLabel">Login History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>IP Address</th>
                                    <th>Device</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo date('M d, Y H:i', time() - 3600); ?></td>
                                    <td>192.168.1.1</td>
                                    <td>Chrome / Windows</td>
                                    <td><span class="badge bg-success">Success</span></td>
                                </tr>
                                <tr>
                                    <td><?php echo date('M d, Y H:i', time() - 86400); ?></td>
                                    <td>192.168.1.1</td>
                                    <td>Chrome / Windows</td>
                                    <td><span class="badge bg-success">Success</span></td>
                                </tr>
                                <tr>
                                    <td><?php echo date('M d, Y H:i', time() - 172800); ?></td>
                                    <td>192.168.1.2</td>
                                    <td>Safari / macOS</td>
                                    <td><span class="badge bg-success">Success</span></td>
                                </tr>
                                <tr>
                                    <td><?php echo date('M d, Y H:i', time() - 259200); ?></td>
                                    <td>192.168.1.1</td>
                                    <td>Chrome / Windows</td>
                                    <td><span class="badge bg-danger">Failed</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            
            // Edit Profile Button Handler
            const editProfileBtn = document.getElementById('editProfileBtn');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const profileViewForm = document.getElementById('profileViewForm');
            const editProfileForm = document.getElementById('editProfileForm');
            
            if (editProfileBtn) {
                editProfileBtn.addEventListener('click', function() {
                    profileViewForm.style.display = 'none';
                    editProfileForm.style.display = 'block';
                    editProfileBtn.style.display = 'none';
                });
            }
            
            if (cancelEditBtn) {
                cancelEditBtn.addEventListener('click', function() {
                    profileViewForm.style.display = 'block';
                    editProfileForm.style.display = 'none';
                    editProfileBtn.style.display = 'block';
                });
            }
        });
        
        // Handle 2FA toggle
        function toggle2FA(checkbox) {
            const formData = new FormData();
            formData.append('toggle_2fa', '1');
            formData.append('enable_2fa', checkbox.checked ? '1' : '0');
            
            fetch('recep_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        // Handle profile image upload
        function handleImageUpload(input) {
            const file = input.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('update_profile', '1');
                formData.append('profile_image', file);
                
                fetch('recep_profile.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }
    </script>
</body>
</html>