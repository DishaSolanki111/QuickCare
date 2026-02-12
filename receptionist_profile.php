<?php
// ================== SESSION & ACCESS CONTROL ==================
session_start();

if (
    !isset($_SESSION['LOGGED_IN']) ||
    $_SESSION['LOGGED_IN'] !== true ||
    $_SESSION['USER_TYPE'] !== 'receptionist'
) {
    header("Location: login.php");
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $update_sql = "UPDATE receptionist_tbl 
                   SET PHONE = ?, EMAIL = ? 
                   WHERE RECEPTIONIST_ID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $phone, $email, $receptionist_id);

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

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receptionist Profile</title>
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
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background-color: #f5f8fa;
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }
      
        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            width: 1500px;
            min-height: 100vh;
        }
/* Top bar */
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

        .form-control.error {
            border-color: var(--warning);
            box-shadow: 0 0 0 2px rgba(255, 107, 107, 0.2);
        }

        .form-control.valid {
            border-color: var(--accent);
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
            gap: 20px;
        }

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
            background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%);
            border-radius: 10px;
            padding: 25px 30px;
            width: 100%;
            max-width: 450px;
            box-shadow: var(--shadow-xl);
            border: 2px solid var(--primary-light);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--primary-light);
        }

        .modal-header h3 {
            margin: 0;
            font-size: 22px;
            color: var(--primary);
            font-weight: 600;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: var(--text-light);
        }

        .error-message {
            color: var(--warning);
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
            color: var(--primary);
            font-size: 14px;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .info-item {
            margin-bottom: 15px;
        }

        .info-label {
            font-weight: 600;
            color: var(--dark);
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

<?php include 'recept_sidebar.php'; ?>


<div class="main-content">
    <div class="topbar">
        <h1>My Profile</h1>
        <p>Welcome,<?= $receptionist['FIRST_NAME'].' '.$receptionist['LAST_NAME'] ?></p>
    </div>
  
<div class="profile-content">
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
            
            <div class="btn-group">
                <button class="btn btn-primary" id="editProfileBtn">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
                <button class="btn btn-danger" id="changePasswordBtn">
                    <i class="fas fa-key"></i> Change Password
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
                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($receptionist['FIRST_NAME']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($receptionist['LAST_NAME']); ?>" disabled>
                    </div>
                </div>
                
                <div class="info-grid">
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($receptionist['DOB']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <input type="text" class="form-control" id="gender" name="gender" value="<?php echo htmlspecialchars($receptionist['GENDER']); ?>" disabled>
                    </div>
                </div>
                
                <div class="info-grid">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($receptionist['PHONE']); ?>" required maxlength="10">
                        <div class="error-message" id="phone_error"></div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($receptionist['EMAIL']); ?>" required>
                        <div class="error-message" id="email_error"></div>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <button type="button" class="btn btn-danger" id="cancelEditBtn">
                        <i class="fas fa-times"></i> Cancel
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
    const phoneInput = document.getElementById('phone');
    const emailInput = document.getElementById('email');

    // Phone validation (10 digits) - real-time
    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9]/g, '');
            const phoneValue = this.value.trim();
            
            if (phoneValue === '') {
                showError('phone', 'Phone number is required.');
            } else if (!/^\d{10}$/.test(phoneValue)) {
                showError('phone', 'Phone number must be exactly 10 digits.');
            } else {
                hideError('phone');
            }
        });

        phoneInput.addEventListener('blur', function () {
            const phoneValue = this.value.trim();
            if (phoneValue === '') {
                showError('phone', 'Phone number is required.');
            } else if (!/^\d{10}$/.test(phoneValue)) {
                showError('phone', 'Phone number must be exactly 10 digits.');
            } else {
                hideError('phone');
            }
        });
    }

    // Email validation - real-time
    if (emailInput) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        emailInput.addEventListener('input', function () {
            const emailValue = this.value.trim();
            
            if (emailValue === '') {
                showError('email', 'Email address is required.');
            } else if (!emailRegex.test(emailValue)) {
                showError('email', 'Please enter a valid email address.');
            } else {
                hideError('email');
            }
        });

        emailInput.addEventListener('blur', function () {
            const emailValue = this.value.trim();
            if (emailValue === '') {
                showError('email', 'Email address is required.');
            } else if (!emailRegex.test(emailValue)) {
                showError('email', 'Please enter a valid email address.');
            } else {
                hideError('email');
            }
        });
    }

    // Form submit validation
    if (editProfileFormElement) {
        editProfileFormElement.addEventListener('submit', function (e) {
            let valid = true;

            // Clear previous errors
            hideError('phone');
            hideError('email');

            // Validate phone
            const phoneValue = phoneInput ? phoneInput.value.trim() : '';
            if (phoneValue === '') {
                showError('phone', 'Phone number is required.');
                valid = false;
            } else if (!/^\d{10}$/.test(phoneValue)) {
                showError('phone', 'Phone number must be exactly 10 digits.');
                valid = false;
            }

            // Validate email
            const emailValue = emailInput ? emailInput.value.trim() : '';
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (emailValue === '') {
                showError('email', 'Email address is required.');
                valid = false;
            } else if (!emailRegex.test(emailValue)) {
                showError('email', 'Please enter a valid email address.');
                valid = false;
            }

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

</body>
</html>
