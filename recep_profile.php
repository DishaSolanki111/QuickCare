<?php
session_start();

// Check if user is logged in as a receptionist
if (!isset($_SESSION['RECEPTIONIST_ID'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';
 $receptionist_id = $_SESSION['RECEPTIONIST_ID'];

// Fetch receptionist data from database
 $receptionist_query = mysqli_query($conn, "SELECT * FROM receptionist_tbl WHERE RECEPTIONIST_ID = '$receptionist_id'");
 $receptionist = mysqli_fetch_assoc($receptionist_query);

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    $update_query = "UPDATE receptionist_tbl SET 
                   FIRST_NAME = '$first_name',
                   LAST_NAME = '$last_name',
                   DOB = '$dob',
                   GENDER = '$gender',
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
    --warning-color: #f39c12;
    --info-color: #17a2b8;
        }
        
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            font-weight: bold;
            background: #F5F8FA;
            display: flex;
        }
        
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

        .logout-btn:hover{
            background-color: var(--light-blue);
        }
        .logout-btn {
            display: block;
            width: 80%;
            margin: 20px auto 0 auto;
            padding: 10px;
            background-color: var(--soft-blue);
            color: var(--white);    
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            transition: background-color 0.3s;
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
            color: #064469;
        }
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-blue);
            padding: 15px 20px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .card-body {
            padding: 20px;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .btn-success {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-success:hover {
            background-color: #27ae60;
            border-color: #27ae60;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
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
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .nav-tabs {
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .nav-tabs .nav-link {
            color: var(--primary-color);
            border: none;
            border-bottom: 3px solid transparent;
            border-radius: 0;
            padding: 10px 15px;
            margin-right: 5px;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--secondary-color);
            border-bottom-color: var(--secondary-color);
            background: none;
        }
        
        .nav-tabs .nav-link:hover {
            border-bottom-color: #eee;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
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
            color: var(--primary-color);
            margin-bottom: 5px;
            display: block;
        }
        
        .info-value {
            color: #555;
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
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <img src="uploads/logo.JPG" alt="QuickCare Logo" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
        <h2>QuickCare</h2>
        <a href="receptionist.php">Dashboard</a>
        <a href="recep_profile.php" class="active">View My Profile</a>
        <a href="manage_appointments.php">Manage Appointments</a>
        <a href="manage_doctor_schedule.php">Manage Doctor Schedule</a>
        <a href="manage_medicine.php">Manage Medicine</a>
        <a href="set_reminder.php">Set Reminder</a>
        <a href="manage_user_profile.php">Manage User Profile</a>
        <a href="view_prescription.php">View Prescription</a>
        <button class="logout-btn">Logout</button>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <h1>My Profile</h1>
            <p>Welcome, <?php echo htmlspecialchars($receptionist['FIRST_NAME'] . ' ' . $receptionist['LAST_NAME']); ?></p>
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
        
        <!-- Profile Card -->
        <div class="card">
            <div class="card-header">
                <h3>Personal Information</h3>
            </div>
            <div class="card-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="true">Profile</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">Security</button>
                    </li>
                </ul>
                
                <!-- Tab Content -->
                <div class="tab-content" id="profileTabContent">
                    <!-- Profile Tab -->
                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <form method="POST" action="recep_profile.php">
                            <input type="hidden" name="update_profile" value="1">
                            
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
                                    <span class="info-label">Address</span>
                                    <span class="info-value"><?php echo htmlspecialchars($receptionist['ADDRESS']); ?></span>
                                </div>
                            </div>
                            
                            <div class="text-end mt-4">
                                <button type="button" class="btn btn-primary" id="editProfileBtn">
                                    <i class="bi bi-pencil"></i> Edit Profile
                                </button>
                            </div>
                        </form>
                        
                        <!-- Edit Profile Form (Hidden by default) -->
                        <form method="POST" action="recep_profile.php" id="editProfileForm" style="display: none;">
                            <input type="hidden" name="update_profile" value="1">
                            
                            <div class="info-grid">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($receptionist['FIRST_NAME']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($receptionist['LAST_NAME']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($receptionist['DOB']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" name="gender" required>
                                        <option value="MALE" <?php echo $receptionist['GENDER'] == 'MALE' ? 'selected' : ''; ?>>Male</option>
                                        <option value="FEMALE" <?php echo $receptionist['GENDER'] == 'FEMALE' ? 'selected' : ''; ?>>Female</option>
                                        <option value="OTHER" <?php echo $receptionist['GENDER'] == 'OTHER' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
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
                    
                    <!-- Security Tab -->
                    <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                        <form method="POST" action="recep_profile.php">
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
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check"></i> Change Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editProfileBtn = document.getElementById('editProfileBtn');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const profileView = document.querySelector('#profile form:first-of-type');
            const editProfileForm = document.getElementById('editProfileForm');
            
            editProfileBtn.addEventListener('click', function() {
                profileView.style.display = 'none';
                editProfileForm.style.display = 'block';
                editProfileBtn.style.display = 'none';
            });
            
            cancelEditBtn.addEventListener('click', function() {
                profileView.style.display = 'block';
                editProfileForm.style.display = 'none';
                editProfileBtn.style.display = 'block';
            });
        });
    </script>
</body>
</html>