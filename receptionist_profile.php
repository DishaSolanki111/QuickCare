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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Profile - QuickCare</title>
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
            min-height: 100vh;
        }

        .container {
            margin-left: 260px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            width: 1500px;
        }

        .main {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            flex: 1;
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

        .error-message {
            color: var(--danger-color);
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }

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
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
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
            color: var(--primary-color);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 22px;
            cursor: pointer;
            color: var(--dark-color);
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--secondary-color);
            font-size: 14px;
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

        .form-control.error {
            border-color: var(--danger-color);
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
            .main {
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

            .main {
                margin-left: 0;
                width: 100%;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .profile-avatar {
                margin-right: 0;
                margin-bottom: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'recept_sidebar.php'; ?>

        <div class="main">
            <?php include 'receptionist_header.php'; ?>

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

            <div class="profile-section">
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar"><?php echo strtoupper(substr($receptionist['FIRST_NAME'], 0, 1) . substr($receptionist['LAST_NAME'], 0, 1)); ?></div>
                        <div class="profile-title">
                            <h2><?php echo htmlspecialchars($receptionist['FIRST_NAME'] . ' ' . $receptionist['LAST_NAME']); ?></h2>
                            <p>Receptionist</p>
                        </div>
                    </div>

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

                        <div class="btn-group">
                            <button class="btn btn-primary" id="editProfileBtn">
                                <i class="fas fa-edit"></i> Edit Profile
                            </button>
                            <button class="btn btn-danger" id="changePasswordBtn">
                                <i class="fas fa-key"></i> Change Password
                            </button>
                        </div>
                    </div>

                    <div id="editProfileForm" class="edit-profile-form">
                        <form method="POST" action="receptionist_profile.php" id="editProfileFormElement">
                            <input type="hidden" name="update_profile" value="1">

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($receptionist['FIRST_NAME']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($receptionist['LAST_NAME']); ?>" disabled>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($receptionist['DOB']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="doj">Date of Joining</label>
                                    <input type="date" class="form-control" id="doj" name="doj" value="<?php echo htmlspecialchars($receptionist['DOJ']); ?>" disabled>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <input type="text" class="form-control" id="gender" name="gender" value="<?php echo htmlspecialchars($receptionist['GENDER']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($receptionist['PHONE']); ?>" required maxlength="10">
                                    <div class="error-message" id="phone_error"></div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($receptionist['EMAIL']); ?>" required>
                                <div class="error-message" id="email_error"></div>
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

    <?php if (isset($password_success)): ?>
        <div class="alert alert-success" style="margin: 0 20px 10px 20px;">
            <i class="fas fa-check-circle"></i>
            <?php echo $password_success; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($password_error)): ?>
        <div class="alert alert-danger" style="margin: 0 20px 10px 20px;">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editProfileBtn = document.getElementById('editProfileBtn');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const profileView = document.getElementById('profileView');
            const editProfileForm = document.getElementById('editProfileForm');

            const changePasswordBtn = document.getElementById('changePasswordBtn');
            const passwordModal = document.getElementById('passwordModal');
            const closePasswordModal = document.getElementById('closePasswordModal');
            const receptionistPasswordForm = document.getElementById('receptionistPasswordForm');

            if (editProfileBtn && profileView && editProfileForm) {
                editProfileBtn.addEventListener('click', () => {
                    profileView.style.display = 'none';
                    editProfileForm.style.display = 'block';
                });
            }

            if (cancelEditBtn && profileView && editProfileForm) {
                cancelEditBtn.addEventListener('click', () => {
                    profileView.style.display = 'block';
                    editProfileForm.style.display = 'none';
                });
            }

            function hideError(id) {
                const el = document.getElementById(id + '_error');
                if (el) {
                    el.style.display = 'none';
                    el.textContent = '';
                }
            }

            function showError(id, msg) {
                const el = document.getElementById(id + '_error');
                if (el) {
                    el.textContent = msg;
                    el.style.display = 'block';
                }
            }

            const phoneInput = document.getElementById('phone');
            const emailInput = document.getElementById('email');
            const editForm = document.getElementById('editProfileFormElement');

            if (phoneInput) {
                phoneInput.addEventListener('input', function () {
                    this.value = this.value.replace(/[^0-9]/g, '');
                    if (/^\d{10}$/.test(this.value)) {
                        hideError('phone');
                    }
                });
            }

            if (emailInput) {
                emailInput.addEventListener('input', function () {
                    if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value.trim())) {
                        hideError('email');
                    }
                });
            }

            if (editForm) {
                editForm.addEventListener('submit', function (e) {
                    let valid = true;

                    if (phoneInput && !/^\d{10}$/.test(phoneInput.value.trim())) {
                        showError('phone', 'Phone number must be exactly 10 digits.');
                        valid = false;
                    }

                    if (emailInput && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value.trim())) {
                        showError('email', 'e.g. abc@gmail.com');
                        valid = false;
                    }

                    if (!valid) {
                        e.preventDefault();
                    }
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

            if (passwordModal) {
                passwordModal.addEventListener('click', (e) => {
                    if (e.target === passwordModal) {
                        passwordModal.classList.remove('active');
                    }
                });
            }

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

            if (receptionistPasswordForm) {
                receptionistPasswordForm.addEventListener('submit', function (e) {
                    let validPwd = true;

                    const currentPassword = document.getElementById('current_password');
                    const newPassword = document.getElementById('new_password');
                    const confirmPassword = document.getElementById('confirm_password');

                    hideError('current_password');
                    hideError('new_password');
                    hideError('confirm_password');

                    if (!currentPassword.value.trim()) {
                        showError('current_password', 'Current password is required.');
                        validPwd = false;
                    }

                    const pwd = newPassword.value;
                    if (pwd.length < 8) {
                        showError('new_password', 'Password must be at least 8 characters long.');
                        validPwd = false;
                    } else if (!/[A-Z]/.test(pwd)) {
                        showError('new_password', 'Password must contain at least one uppercase letter.');
                        validPwd = false;
                    } else if (!/[0-9]/.test(pwd)) {
                        showError('new_password', 'Password must contain at least one digit.');
                        validPwd = false;
                    } else if (!/[\W_]/.test(pwd)) {
                        showError('new_password', 'Password must contain at least one special character.');
                        validPwd = false;
                    }

                    if (confirmPassword.value !== pwd) {
                        showError('confirm_password', 'Confirm password must match new password.');
                        validPwd = false;
                    }

                    if (!validPwd) {
                        e.preventDefault();
                    }
                });
            }
        });
    </script>
</body>
</html>
