<?php
// ================== SESSION & ACCESS CONTROL ==================
session_start();

if (
    !isset($_SESSION['LOGGED_IN']) ||
    $_SESSION['LOGGED_IN'] !== true ||
    !isset($_SESSION['USER_TYPE']) ||
    $_SESSION['USER_TYPE'] !== 'doctor'
) {
    header("Location: login.php");
    exit();
}

// ================== DATABASE CONNECTION ==================
include 'config.php';

// ================== DOCTOR INFO ==================
 $doctor_id = $_SESSION['DOCTOR_ID'];
 $doctor = [];

 $doc_sql = "SELECT d.*, s.SPECIALISATION_NAME FROM doctor_tbl d 
            JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID 
            WHERE d.DOCTOR_ID = ?";
 $doc_stmt = $conn->prepare($doc_sql);
 $doc_stmt->bind_param("i", $doctor_id);
 $doc_stmt->execute();
 $doc_result = $doc_stmt->get_result();

if ($doc_result->num_rows === 1) {
    $doctor = $doc_result->fetch_assoc();
}
 $doc_stmt->close();

// ================== HANDLE PROFILE UPDATE ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $update_sql = "UPDATE doctor_tbl SET 
                   FIRST_NAME = ?, 
                   LAST_NAME = ?, 
                   PHONE = ?, 
                   EMAIL = ? 
                   WHERE DOCTOR_ID = ?";
    
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $first_name, $last_name, $phone, $email, $doctor_id);
    
    if ($update_stmt->execute()) {
        $success_message = "Profile updated successfully!";
        
        // Refresh doctor data
        $doc_sql = "SELECT d.*, s.SPECIALISATION_NAME FROM doctor_tbl d 
                    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID 
                    WHERE d.DOCTOR_ID = ?";
        $doc_stmt = $conn->prepare($doc_sql);
        $doc_stmt->bind_param("i", $doctor_id);
        $doc_stmt->execute();
        $doc_result = $doc_stmt->get_result();
        
        if ($doc_result->num_rows === 1) {
            $doctor = $doc_result->fetch_assoc();
        }
        $doc_stmt->close();
    } else {
        $error_message = "Error updating profile: " . $conn->error;
    }
    $update_stmt->close();
}

// ================== HANDLE PASSWORD CHANGE ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Verify current password
    $password_sql = "SELECT PSWD FROM doctor_tbl WHERE DOCTOR_ID = ?";
    $password_stmt = $conn->prepare($password_sql);
    $password_stmt->bind_param("i", $doctor_id);
    $password_stmt->execute();
    $password_result = $password_stmt->get_result();
    
    if ($password_result->num_rows === 1) {
        $password_data = $password_result->fetch_assoc();
        
        if (password_verify($current_password, $password_data['PSWD'])) {
            if ($new_password === $confirm_password) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                
                $update_password_sql = "UPDATE doctor_tbl SET PSWD = ? WHERE DOCTOR_ID = ?";
                $update_password_stmt = $conn->prepare($update_password_sql);
                $update_password_stmt->bind_param("si", $hashed_password, $doctor_id);
                
                if ($update_password_stmt->execute()) {
                    $password_success = "Password changed successfully!";
                } else {
                    $password_error = "Error changing password: " . $conn->error;
                }
                $update_password_stmt->close();
            } else {
                $password_error = "New passwords do not match!";
            }
        } else {
            $password_error = "Current password is incorrect!";
        }
    }
    $password_stmt->close();
}

 $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0066cc;
            --primary-dark: #0052a3;
            --primary-light: #e6f2ff;
            --secondary: #00a8cc;
            --accent: #00a86b;
            --warning: #ff6b6b;
            --dark: #1a3a5f;
            --light: #f8fafc;
            --white: #ffffff;
            --text: #2c5282;
            --text-light: #4a6fa5;
            --gradient-1: linear-gradient(135deg, #0066cc 0%, #00a8cc 100%);
            --gradient-2: linear-gradient(135deg, #00a8cc 0%, #00a86b 100%);
            --gradient-3: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --shadow-xl: 0 20px 25px rgba(0,0,0,0.1);
            --shadow-2xl: 0 25px 50px rgba(0,0,0,0.25);
            --dark-blue: #072D44;
            --mid-blue: #064469;
            --soft-blue: #5790AB;
            --light-blue: #9CCDD8;
            --gray-blue: #D0D7E1;
            --white: #ffffff;
            --card-bg: #F6F9FB;
            --primary-color: #1a3a5f;
            --secondary-color: #3498db;
         
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
        
       
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            margin-top:-15px;
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
        
        .container {
            display: flex;
            min-height: 100vh;
        }

    

            .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding: 10px;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow-md);
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 25px;
            border: 4px solid var(--primary-light);
        }

        .profile-info h2 {
            font-size: 28px;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .profile-info p {
            color: var(--text-light);
            margin-bottom: 10px;
        }

        .profile-tabs {
            display: flex;
            margin-bottom: 25px;
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .tab {
            flex: 1;
            padding: 15px 20px;
            text-align: center;
            cursor: pointer;
            background: var(--white);
            color: var(--text-light);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .tab.active {
            background: var(--primary);
            color: var(--white);
        }

        .tab-content {
            display: none;
            background: var(--white);
            border-radius: 12px;
            padding: 25px;
            box-shadow: var(--shadow-md);
        }

        .tab-content.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 102, 204, 0.2);
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
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
            color: var(--dark);
            margin-bottom: 5px;
            display: block;
        }

        .info-value {
            color: #555;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar-header h2 {
                display: none;
            }
            
            .sidebar-nav a span {
                display: none;
            }
            
            .sidebar-nav a {
                justify-content: center;
            }
            
            .sidebar-nav a i {
                margin: 0;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .topbar {
                padding: 15px 20px;
            }
            
            .profile-content {
                padding: 20px;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-avatar {
                margin-right: 0;
                margin-bottom: 20px;
            }
        }

        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--dark);
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="container">
    <!-- Sidebar -->
   <?php include 'doctor_sidebar.php'; ?>
   <!-- $page_title = "Your Profile"; -->
    
    <!-- Main Content -->
    <div class="main-content">
         <?php include 'doctor_header.php'; ?>
        <!-- Profile Content -->
        <div class="profile-content">
            <!-- Profile Header -->
           
            <!-- <div class="profile-header">
                <?php if (!empty($doctor['PROFILE_IMAGE'])): ?>
                    <img src="<?php echo htmlspecialchars($doctor['PROFILE_IMAGE']); ?>" alt="Profile" class="profile-avatar">
                <?php else: ?>
                    <div class="profile-avatar" style="background-color: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 36px; font-weight: bold;">
                        <?php echo strtoupper(substr($doctor['FIRST_NAME'], 0, 1) . substr($doctor['LAST_NAME'], 0, 1)); ?>
                    </div>
                <?php endif; ?>
                
                <div class="profile-info">
                    <h2>Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></h2>
                    <p><?php echo htmlspecialchars($doctor['SPECIALISATION_NAME']); ?></p>
                    <p><?php echo htmlspecialchars($doctor['EDUCATION']); ?></p>
                </div>
            </div> -->
            
            <!-- Profile Tabs -->
            <div class="profile-tabs">
                <div class="tab active" data-tab="personal-info">Personal Information</div>
                <div class="tab" data-tab="professional-info">Professional Information</div>
                <div class="tab" data-tab="security">Security</div>
            </div>
            
            <!-- Tab Content -->
            <div class="tab-content active" id="personal-info">
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
                
                <form method="POST" action="d_profile.php">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="info-grid">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($doctor['FIRST_NAME']); ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($doctor['LAST_NAME']); ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($doctor['DOB']); ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <input type="text" class="form-control" id="gender" name="gender" value="<?php echo htmlspecialchars($doctor['GENDER']); ?>" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($doctor['PHONE']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($doctor['EMAIL']); ?>" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
            
            <div class="tab-content" id="professional-info">
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Specialization</span>
                        <span class="info-value"><?php echo htmlspecialchars($doctor['SPECIALISATION_NAME']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Education</span>
                        <span class="info-value"><?php echo htmlspecialchars($doctor['EDUCATION']); ?></span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Date of Joining</span>
                        <span class="info-value"><?php echo date('F d, Y', strtotime($doctor['DOJ'])); ?></span>
                    </div>
                    
                    <!-- <div class="info-item">
                        <span class="info-label">Years of Experience</span>
                        <span class="info-value"><?php echo date('Y') - date('Y', strtotime($doctor['DOJ'])); ?> years</span>
                    </div> -->
                </div>
            </div>
            
            <div class="tab-content" id="security">
                <?php if (isset($password_success)): ?>
                    <div class="alert alert-success">
                        <?php echo $password_success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($password_error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $password_error; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="d_profile.php">
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
                    
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-key"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
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
            
            // Mobile menu toggle
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                        sidebar.classList.remove('active');
                    }
                }
            });
        });
    </script>
</body>
</html>