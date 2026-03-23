<?php
// ================== SESSION & ACCESS CONTROL ==================
session_start();

if (
    !isset($_SESSION['LOGGED_IN']) ||
    $_SESSION['LOGGED_IN'] !== true ||
    $_SESSION['USER_TYPE'] !== 'receptionist'
) {
    header("Location: login_for_all.php");
    exit();
}

// ================== DATABASE CONNECTION ==================
include 'config.php';

// ================== RECEPTIONIST INFO ==================
$receptionist_id = $_SESSION['RECEPTIONIST_ID'];
$receptionist = [];

$sql = "SELECT * FROM receptionist_tbl WHERE RECEPTIONIST_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $receptionist_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $receptionist = $result->fetch_assoc();
}
$stmt->close();

// ================== UPDATE PROFILE ==================
// Only basic details (NOT phone/email) are editable by receptionist.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = $_POST['firstName'] ?? $receptionist['FIRST_NAME'];
    $last_name  = $_POST['lastName'] ?? $receptionist['LAST_NAME'];
    $dob        = $_POST['dob'] ?? $receptionist['DOB'];
    $doj        = $_POST['doj'] ?? $receptionist['DOJ'];
    $gender     = $_POST['gender'] ?? $receptionist['GENDER'];

    $update_sql = "UPDATE receptionist_tbl 
                   SET FIRST_NAME = ?, LAST_NAME = ?, DOB = ?, DOJ = ?, GENDER = ?
                   WHERE RECEPTIONIST_ID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssi", $first_name, $last_name, $dob, $doj, $gender, $receptionist_id);

    if ($update_stmt->execute()) {
        // Refresh receptionist data
        $sql = "SELECT * FROM receptionist_tbl WHERE RECEPTIONIST_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $receptionist_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $receptionist = $result->fetch_assoc();
        }
        $stmt->close();
        
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Profile update failed!";
    }
    $update_stmt->close();
}

// ================== SECURITY QUESTION SETUP (first-time completion) ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_security'])) {
    $sec_q = trim($_POST['security_question'] ?? '');
    $sec_a = trim($_POST['security_answer'] ?? '');

    if ($sec_q === '' || $sec_a === '') {
        $error_message = "Please select a security question and provide an answer.";
    } else {
        $q_esc = $conn->real_escape_string($sec_q);
        $a_esc = $conn->real_escape_string($sec_a);
        $sec_sql = "UPDATE receptionist_tbl SET SECURITY_QUESTION='$q_esc', SECURITY_ANSWER='$a_esc' WHERE RECEPTIONIST_ID='$receptionist_id'";
        if ($conn->query($sec_sql)) {
            // Refresh receptionist data
            $receptionist_query = mysqli_query($conn, "SELECT * FROM receptionist_tbl WHERE RECEPTIONIST_ID = '$receptionist_id'");
            $receptionist = mysqli_fetch_assoc($receptionist_query);
            $success_message = "Security question saved. Your profile is now complete.";
        } else {
            $error_message = "Could not save security question. Please try again.";
        }
    }
}

// ================== CHANGE PASSWORD ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new === $confirm) {
        if (password_verify($current, $receptionist['PSWD'])) {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $pwd_sql = "UPDATE receptionist_tbl SET PSWD=? WHERE RECEPTIONIST_ID=?";
            $pwd_stmt = $conn->prepare($pwd_sql);
            $pwd_stmt->bind_param("si", $hash, $receptionist_id);
            $pwd_stmt->execute();
            $password_success = "Password changed successfully!";
            $pwd_stmt->close();
        } else {
            $password_error = "Current password incorrect!";
        }
    } else {
        $password_error = "Passwords do not match!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receptionist Profile</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #072D44;
            --primary-dark: #064469;
            --secondary: #5790AB;
            --accent: #9CCDD8;
            --light: #D0D7E1;
            --white: #ffffff;
            --gray: #6c757d;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 8px rgba(0,0,0,0.15);
            --shadow-lg: 0 10px 20px rgba(0,0,0,0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light);
            color: #333;
            line-height: 1.6;
        }

        /* Container for the entire layout */
        .container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

      
        /* Main Content */
        .main-content {
            margin-left: 150px;
            padding: 20px 0;
            width: calc(100% - 250px);
            min-height: 100vh;
        }

        /* Top Bar - match doctor_dashboard/doctor_header */
        .topbar {
            background: #ffffff;
            padding: 18px 30px;
            
            justify-content: space-between;
            align-items: left;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            margin-bottom: 20px;
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

        /* Profile Content */
        .profile-content {
            background: var(--white);
            border-radius: 12px;
            padding: 25px 30px 15px 30px; /* Reduced bottom padding */
            box-shadow: var(--shadow-md);
          
            height: fit-content; /* Fit snugly after buttons */
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background-color: var(--secondary);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 42px;
            font-weight: bold;
            margin-right: 30px;
            box-shadow: var(--shadow-md);
        }

        .profile-info h2 {
            font-size: 32px;
            color: var(--primary);
            margin-bottom: 8px;
        }

        .profile-info p {
            color: var(--gray);
            font-size: 16px;
            margin-bottom: 5px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary);
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
            border-color: var(--secondary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(87, 144, 171, 0.2);
        }

        .form-control:disabled {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            gap: 8px;
        }

        .btn-primary {
            background-color: var(--secondary);
            color: var(--white);
        }

        .btn-primary:hover {
            background-color: #46829b;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-danger {
            background-color: var(--danger);
            color: var(--white);
        }

        .btn-danger:hover {
            background-color: #c82333;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-success {
            background-color: var(--success);
            color: var(--white);
        }

        .btn-success:hover {
            background-color: #218838;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .alert {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 10px;
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
            gap: 25px;
        }

        .info-item {
            margin-bottom: 20px;
        }

        .info-label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 5px;
            display: block;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            color: #555;
            font-size: 16px;
        }

        /* Edit Form Styles */
        .edit-profile-form {
            display: none;
        }

        /* Password modal popup */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: #ffffff;
            border-radius: 10px;
            padding: 25px 30px;
            width: 100%;
            max-width: 450px;
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 20px;
            color: var(--primary);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: var(--gray);
        }

        .error-message {
            color: var(--danger);
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }

        /* Password toggle in modal */
        .password-wrapper {
            position: relative;
        }

        .password-wrapper .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--secondary);
            font-size: 14px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        /* Tabs */
        .profile-tabs {
            display: flex;
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
        }

        .tab {
            padding: 12px 20px;
            cursor: pointer;
            font-weight: 600;
            color: var(--gray);
            border-bottom: 3px solid transparent;
            transition: all 0.3s ease;
        }

        .tab:hover {
            color: var(--primary);
        }

        .tab.active {
            color: var(--primary);
            border-bottom-color: var(--secondary);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
           
            
            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
            
            .info-grid,
            .form-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
          
            
            .main-content {
                margin-left: 0;
                width: 100%;
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
            
            .btn-group {
                flex-direction: column;
            }
        }

        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--primary);
            cursor: pointer;
            margin-right: 15px;
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
        <?php include 'recept_sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <?php include 'receptionist_header.php'; ?>
          
            <!-- Profile Content -->
            <div class="profile-content">
                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="profile-avatar">
                        <?php echo strtoupper(substr($receptionist['FIRST_NAME'], 0, 1) . substr($receptionist['LAST_NAME'], 0, 1)); ?>
                    </div>
                    
                    <div class="profile-info">
                        <h2><?php echo htmlspecialchars($receptionist['FIRST_NAME'] . ' ' . $receptionist['LAST_NAME']); ?></h2>
                        <p>Receptionist</p>
                    </div>
                </div>

                <!-- Combined Information Section (Personal + Security) -->
                <div id="personal-info">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Profile View -->
        <div id="profileView">
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
                    <span class="info-label">Date of Joining</span>
                    <span class="info-value"><?php echo date('F d, Y', strtotime($receptionist['DOJ'])); ?></span>
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
            </div>

            <?php
            $needs_security = trim((string)($receptionist['SECURITY_QUESTION'] ?? '')) === '';
            if (isset($_SESSION['COMPLETE_SECURITY']) && !$needs_security) {
                unset($_SESSION['COMPLETE_SECURITY']);
            }
            if ($needs_security): ?>
            <div class="alert alert-danger" style="margin-top:20px;">
                <i class="fas fa-exclamation-triangle"></i>
                For security, please set a security question and answer. This will be used for password recovery.
            </div>
            <form method="POST" action="receptionist_profile.php" style="margin-top:10px;">
                <input type="hidden" name="update_security" value="1">
                <div class="form-group">
                    <label for="security_question">Security Question</label>
                    <select id="security_question" name="security_question" class="form-control" required>
                        <option value="">Select a security question</option>
                        <option value="What was the name of your first school?">What was the name of your first school?</option>
                        <option value="What is your favorite food from childhood?">What is your favorite food from childhood?</option>
                        <option value="Where did you go for your first school trip?">Where did you go for your first school trip?</option>
                        <option value="What was the nickname your family calls you?">What was the nickname your family calls you?</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="security_answer">Security Answer</label>
                    <input type="text" id="security_answer" name="security_answer" class="form-control" placeholder="Your answer" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-shield-alt"></i> Save Security Question
                </button>
            </form>
            <?php endif; ?>
            
            <div class="btn-group">
                <button class="btn btn-primary" id="editProfileBtn">
                     Edit Profile
                </button>
                <button class="btn btn-danger" id="changePasswordBtn">
                    Change Password
                </button>
            </div>
        </div>
        
        <!-- Edit Profile Form -->
        <div id="editProfileForm" class="edit-profile-form">
            <form method="POST" action="receptionist_profile.php" id="editProfileFormElement">
                <input type="hidden" name="update_profile" value="1">
                
                <div class="info-grid">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($receptionist['FIRST_NAME']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($receptionist['LAST_NAME']); ?>" required>
                    </div>
                </div>
                
                <div class="info-grid">
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($receptionist['DOB']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="doj">Date of Joining</label>
                        <input type="date" class="form-control" id="doj" name="doj" value="<?php echo htmlspecialchars($receptionist['DOJ']); ?>" required>
                    </div>
                    
                </div>
                
                <div class="info-grid">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="MALE" <?php echo $receptionist['GENDER'] == 'MALE' ? 'selected' : ''; ?>>Male</option>
                            <option value="FEMALE" <?php echo $receptionist['GENDER'] == 'FEMALE' ? 'selected' : ''; ?>>Female</option>
                            <option value="OTHER" <?php echo $receptionist['GENDER'] == 'OTHER' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($receptionist['PHONE']); ?>" disabled style="background:#f5f5f5; cursor:not-allowed;">
                    </div>
                </div>
                <div class="info-grid">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($receptionist['EMAIL']); ?>" disabled style="background:#f5f5f5; cursor:not-allowed;">
                    </div>

                </div>
                
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                     Save Changes
                    </button>
                    <button type="button" class="btn btn-danger" id="cancelEditBtn">
                     Cancel
                    </button>
                </div>
            </form>
        </div>

        <!-- Security: Change Password (popup modal) -->
        <?php if (isset($password_success)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $password_success; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($password_error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $password_error; ?>
            </div>
        <?php endif; ?>
        
        <div id="passwordModal" class="modal-overlay">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Change Password</h3>
                    <button type="button" class="modal-close" id="closePasswordModal">&times;</button>
                </div>
                <form method="POST" action="receptionist_profile.php" id="receptionistPasswordForm">
                    <input type="hidden" name="change_password" value="1">
                    
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                            <i class="fas fa-eye toggle-password" data-target="current_password"></i>
                        </div>
                        <div class="error-message" id="current_password_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" id="new_password" name="new_password" required>
                            <i class="fas fa-eye toggle-password" data-target="new_password"></i>
                        </div>
                        <div class="error-message" id="new_password_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <div class="password-wrapper">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            <i class="fas fa-eye toggle-password" data-target="confirm_password"></i>
                        </div>
                        <div class="error-message" id="confirm_password_error"></div>
                    </div>
                    
                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key"></i> Change Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit profile functionality
    const editProfileBtn = document.getElementById('editProfileBtn');
    const cancelEditBtn = document.getElementById('cancelEditBtn');
    const profileView = document.getElementById('profileView');
    const editProfileForm = document.getElementById('editProfileForm');

    // Change password modal functionality
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    const passwordModal = document.getElementById('passwordModal');
    const closePasswordModal = document.getElementById('closePasswordModal');
    const receptionistPasswordForm = document.getElementById('receptionistPasswordForm');
    
    if (editProfileBtn) {
        editProfileBtn.addEventListener('click', () => {
            profileView.style.display = 'none';
            editProfileForm.style.display = 'block';
            // Clear any previous validation errors when opening edit form
            hideError('phone');
            hideError('email');
        });
    }
    
    if (cancelEditBtn) {
        cancelEditBtn.addEventListener('click', () => {
            profileView.style.display = 'block';
            editProfileForm.style.display = 'none';
            // Clear validation errors when canceling
            hideError('phone');
            hideError('email');
        });
    }

    if (changePasswordBtn && passwordModal) {
        changePasswordBtn.addEventListener('click', () => {
            passwordModal.classList.add('active');
        });
    }

    if (closePasswordModal && passwordModal) {
        closePasswordModal.addEventListener('click', () => {
            passwordModal.classList.remove('active');
        });
    }

    // Close modal when clicking outside the content
    if (passwordModal) {
        passwordModal.addEventListener('click', (e) => {
            if (e.target === passwordModal) {
                passwordModal.classList.remove('active');
            }
        });
    }

    // Show/hide password toggles in modal
    const toggleIcons = document.querySelectorAll('.toggle-password');
    toggleIcons.forEach(icon => {
        icon.addEventListener('click', () => {
            const targetId = icon.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;
            const isPassword = input.getAttribute('type') === 'password';
            input.setAttribute('type', isPassword ? 'text' : 'password');
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        });
    });

    // Validation helpers
    function hideError(id) {
        const el = document.getElementById(id + '_error');
        const input = document.getElementById(id);
        if (el) {
            el.style.display = 'none';
            el.textContent = '';
        }
        if (input) {
            input.classList.remove('error');
            input.classList.add('valid');
        }
    }

    function showError(id, message) {
        const el = document.getElementById(id + '_error');
        const input = document.getElementById(id);
        if (el) {
            el.textContent = message;
            el.style.display = 'block';
        }
        if (input) {
            input.classList.remove('valid');
            input.classList.add('error');
        }
    }

    // Edit Profile Form Validation
    const editProfileFormElement = document.getElementById('editProfileFormElement');
    const phoneInput = null; // phone/email are not editable by receptionist
    const emailInput = null;

    // Phone validation (10 digits) - real-time
    // phone/email are read-only now; no runtime validation needed

    // Email validation - real-time
    // email read-only: skip validation

    // Form submit validation
    if (editProfileFormElement) {
        editProfileFormElement.addEventListener('submit', function (e) {
            let valid = true;

            // Clear previous errors
            hideError('phone');
            hideError('email');

            // Phone and email are read-only; no validation required here

            if (!valid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = document.querySelector('.error-message[style*="block"]');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            }
        });
    }

    // Password validation on submit
    if (receptionistPasswordForm) {
        receptionistPasswordForm.addEventListener('submit', function (e) {
            let valid = true;

            const currentPassword = document.getElementById('current_password');
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');

            // Clear old errors
            hideError('current_password');
            hideError('new_password');
            hideError('confirm_password');

            if (!currentPassword.value.trim()) {
                showError('current_password', 'Current password is required.');
                valid = false;
            }

            const pwd = newPassword.value;
            if (pwd.length < 8) {
                showError('new_password', 'Password must be at least 8 characters long.');
                valid = false;
            } else if (!/[A-Z]/.test(pwd)) {
                showError('new_password', 'Password must contain at least one uppercase letter.');
                valid = false;
            } else if (!/[0-9]/.test(pwd)) {
                showError('new_password', 'Password must contain at least one digit.');
                valid = false;
            } else if (!/[\W_]/.test(pwd)) {
                showError('new_password', 'Password must contain at least one special character.');
                valid = false;
            }

            if (confirmPassword.value !== pwd) {
                showError('confirm_password', 'Confirm password must match new password.');
                valid = false;
            }

            if (!valid) {
                e.preventDefault();
            }
        });
    }
});
</script>

        </div>
    </div>
</body>
</html>
