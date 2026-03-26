<?php
session_start();

// Check if user is logged in as a doctor
if (!isset($_SESSION['DOCTOR_ID'])) {
    header("Location: login_for_all.php");
    exit;
}

include 'config.php';
$doctor_id = $_SESSION['DOCTOR_ID'];

// Fetch doctor data from database
$doctor_query = mysqli_query($conn, "SELECT d.*, s.SPECIALISATION_NAME FROM doctor_tbl d 
                                     LEFT JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID 
                                     WHERE d.DOCTOR_ID = '$doctor_id'");
$doctor = mysqli_fetch_assoc($doctor_query);

// Handle form submission for profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $first_name = mysqli_real_escape_string($conn, $_POST['firstName']);
    $last_name  = mysqli_real_escape_string($conn, $_POST['lastName']);
    $dob        = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender     = mysqli_real_escape_string($conn, $_POST['gender']);

    $update_query = "UPDATE doctor_tbl SET 
                        FIRST_NAME = '$first_name',
                        LAST_NAME  = '$last_name',
                        DOB        = '$dob',
                        GENDER     = '$gender'
                    WHERE DOCTOR_ID = '$doctor_id'";

    if (mysqli_query($conn, $update_query)) {
        $doctor_query = mysqli_query($conn, "SELECT d.*, s.SPECIALISATION_NAME FROM doctor_tbl d 
                                            LEFT JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID 
                                            WHERE d.DOCTOR_ID = '$doctor_id'");
        $doctor = mysqli_fetch_assoc($doctor_query);
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Error updating profile: " . mysqli_error($conn);
    }
}

// Handle security question setup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_security'])) {
    $sec_q = trim($_POST['security_question'] ?? '');
    $sec_a = trim($_POST['security_answer'] ?? '');

    if ($sec_q === '' || $sec_a === '') {
        $error_message = "Please select a security question and provide an answer.";
    } else {
        $q_esc = mysqli_real_escape_string($conn, $sec_q);
        $a_esc = mysqli_real_escape_string($conn, $sec_a);
        $sec_query = "UPDATE doctor_tbl SET SECURITY_QUESTION = '$q_esc', SECURITY_ANSWER = '$a_esc' WHERE DOCTOR_ID = '$doctor_id'";
        if (mysqli_query($conn, $sec_query)) {
            $doctor_query = mysqli_query($conn, "SELECT d.*, s.SPECIALISATION_NAME FROM doctor_tbl d
                                                LEFT JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
                                                WHERE d.DOCTOR_ID = '$doctor_id'");
            $doctor = mysqli_fetch_assoc($doctor_query);
            $success_message = "Security question saved. Your profile is now complete.";
        } else {
            $error_message = "Could not save security question. Please try again.";
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (password_verify($current_password, $doctor['PSWD'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $hashed_esc      = mysqli_real_escape_string($conn, $hashed_password);
            $password_query  = "UPDATE doctor_tbl SET PSWD = '$hashed_esc' WHERE DOCTOR_ID = '$doctor_id'";

            if (mysqli_query($conn, $password_query)) {
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

// Calculate years of experience
$years_exp = 0;
if (!empty($doctor['DOJ'])) {
    $doj       = new DateTime($doctor['DOJ']);
    $today     = new DateTime();
    $years_exp = $doj->diff($today)->y;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile - QuickCare</title>
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

        .container {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            min-height: 100vh;
        }

        .topbar {
            background: #ffffff;
            padding: 18px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.06);
            margin-bottom: 10px;
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

        .doctor-name {
            font-weight: 600;
            color: #1a3a5f;
            font-size: 16px;
        }

        .profile-content {
            background: var(--white);
            border-radius: 12px;
            padding: 30px;
            box-shadow: var(--shadow-md);
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

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
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

        .edit-profile-form {
            display: none;
        }

        /* Password modal */
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
        <?php include 'doctor_sidebar.php'; ?>

        <div class="main-content">
            <?php include 'doctor_header.php'; ?>

            <div class="profile-content">
                <!-- Profile Header -->
                <div class="profile-header">
                    <?php if (!empty($doctor['PROFILE_IMAGE'])): ?>
                        <img src="<?php echo htmlspecialchars($doctor['PROFILE_IMAGE']); ?>" alt="Profile" class="profile-avatar" style="object-fit:cover;">
                    <?php else: ?>
                        <div class="profile-avatar">
                            <?php echo strtoupper(substr($doctor['FIRST_NAME'], 0, 1) . substr($doctor['LAST_NAME'], 0, 1)); ?>
                        </div>
                    <?php endif; ?>

                    <div class="profile-info">
                        <h2>Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></h2>
                        <p><?php echo htmlspecialchars($doctor['SPECIALISATION_NAME']); ?></p>
                        <p><?php echo htmlspecialchars($doctor['EDUCATION']); ?></p>
                    </div>
                </div>

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

                    <!-- Profile View -->
                    <div id="profileView">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">First Name</span>
                                <span class="info-value"><?php echo htmlspecialchars($doctor['FIRST_NAME']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Last Name</span>
                                <span class="info-value"><?php echo htmlspecialchars($doctor['LAST_NAME']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Date of Birth</span>
                                <span class="info-value"><?php echo date('F d, Y', strtotime($doctor['DOB'])); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Gender</span>
                                <span class="info-value"><?php echo htmlspecialchars($doctor['GENDER']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Phone Number</span>
                                <span class="info-value"><?php echo htmlspecialchars($doctor['PHONE']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email Address</span>
                                <span class="info-value"><?php echo htmlspecialchars($doctor['EMAIL']); ?></span>
                            </div>
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
                            <div class="info-item">
                                <span class="info-label">Years of Experience</span>
                                <span class="info-value"><?php echo $years_exp; ?> years</span>
                            </div>
                        </div>

                        <?php
                        $needs_security = trim((string)($doctor['SECURITY_QUESTION'] ?? '')) === '';
                        if ($needs_security): ?>
                            <div class="alert alert-warning" style="margin-top:20px;">
                                <i class="fas fa-exclamation-triangle"></i>
                                For security, please set a security question and answer. This will be used for password recovery.
                            </div>
                            <form method="POST" action="doctor_profile.php" style="margin-top:10px;">
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
                                <button type="submit" class="btn btn-success">
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
                        <form method="POST" action="doctor_profile.php">
                            <input type="hidden" name="update_profile" value="1">

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="firstName"
                                           value="<?php echo htmlspecialchars($doctor['FIRST_NAME']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="lastName"
                                           value="<?php echo htmlspecialchars($doctor['LAST_NAME']); ?>" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="date" class="form-control" id="dob" name="dob"
                                           value="<?php echo htmlspecialchars($doctor['DOB']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" name="gender" required>
                                        <option value="MALE"   <?php echo $doctor['GENDER'] == 'MALE'   ? 'selected' : ''; ?>>Male</option>
                                        <option value="FEMALE" <?php echo $doctor['GENDER'] == 'FEMALE' ? 'selected' : ''; ?>>Female</option>
                                        <option value="OTHER"  <?php echo $doctor['GENDER'] == 'OTHER'  ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                           value="<?php echo htmlspecialchars($doctor['PHONE']); ?>" disabled>
                                    <div class="error-message" id="phone_error"></div>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?php echo htmlspecialchars($doctor['EMAIL']); ?>" disabled>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="specialization">Specialization</label>
                                    <input type="text" class="form-control" id="specialization"
                                           value="<?php echo htmlspecialchars($doctor['SPECIALISATION_NAME']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="education">Education</label>
                                    <input type="text" class="form-control" id="education"
                                           value="<?php echo htmlspecialchars($doctor['EDUCATION']); ?>" disabled>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="doj">Date of Joining</label>
                                    <input type="date" class="form-control" id="doj"
                                           value="<?php echo htmlspecialchars($doctor['DOJ']); ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="experience">Years of Experience</label>
                                    <input type="text" class="form-control" id="experience"
                                           value="<?php echo $years_exp; ?> years" disabled>
                                </div>
                            </div>

                            <div class="btn-group">
                                <button type="submit" class="btn btn-success">
                                    Save Changes
                                </button>
                                <button type="button" class="btn btn-danger" id="cancelEditBtn">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password Modal -->
    <div id="passwordModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Change Password</h3>
                <button type="button" class="modal-close" id="closePasswordModal">&times;</button>
            </div>
            <form method="POST" action="doctor_profile.php" id="doctorPasswordForm">
                <input type="hidden" name="change_password" value="1">

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <div class="password-wrapper">
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                        <i class="fas fa-eye-slash toggle-password" data-target="current_password"></i>
                    </div>
                    <div class="error-message" id="current_password_error"></div>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="password-wrapper">
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <i class="fas fa-eye-slash toggle-password" data-target="new_password"></i>
                    </div>
                    <div class="error-message" id="new_password_error"></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="password-wrapper">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <i class="fas fa-eye-slash toggle-password" data-target="confirm_password"></i>
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
        document.addEventListener('DOMContentLoaded', function () {

            // ── Edit Profile ──────────────────────────────────────────
            const editProfileBtn  = document.getElementById('editProfileBtn');
            const cancelEditBtn   = document.getElementById('cancelEditBtn');
            const profileView     = document.getElementById('profileView');
            const editProfileForm = document.getElementById('editProfileForm');

            if (editProfileBtn) {
                editProfileBtn.addEventListener('click', function () {
                    profileView.style.display     = 'none';
                    editProfileForm.style.display = 'block';
                });
            }

            if (cancelEditBtn) {
                cancelEditBtn.addEventListener('click', function () {
                    profileView.style.display     = 'block';
                    editProfileForm.style.display = 'none';
                });
            }

            // ── Change Password Modal ─────────────────────────────────
            const changePasswordBtn  = document.getElementById('changePasswordBtn');
            const passwordModal      = document.getElementById('passwordModal');
            const closePasswordModal = document.getElementById('closePasswordModal');
            const doctorPasswordForm = document.getElementById('doctorPasswordForm');

            if (changePasswordBtn && passwordModal) {
                changePasswordBtn.addEventListener('click', function () {
                    passwordModal.classList.add('active');
                });
            }

            if (closePasswordModal && passwordModal) {
                closePasswordModal.addEventListener('click', function () {
                    passwordModal.classList.remove('active');
                });
            }

            if (passwordModal) {
                passwordModal.addEventListener('click', function (e) {
                    if (e.target === passwordModal) {
                        passwordModal.classList.remove('active');
                    }
                });
            }

            // ── Password toggle icons ─────────────────────────────────
            document.querySelectorAll('.toggle-password').forEach(function (icon) {
                icon.addEventListener('click', function () {
                    const input   = document.getElementById(icon.getAttribute('data-target'));
                    if (!input) return;
                    const isHidden = input.type === 'password';
                    input.type = isHidden ? 'text' : 'password';
                    icon.classList.toggle('fa-eye-slash', !isHidden);
                    icon.classList.toggle('fa-eye', isHidden);
                });
            });

            // ── Validation helpers ────────────────────────────────────
            function showError(id, msg) {
                const el = document.getElementById(id + '_error');
                if (el) { el.textContent = msg; el.style.display = 'block'; }
            }

            function hideError(id) {
                const el = document.getElementById(id + '_error');
                if (el) { el.textContent = ''; el.style.display = 'none'; }
            }

            // ── Password form validation ──────────────────────────────
            if (doctorPasswordForm) {
                doctorPasswordForm.addEventListener('submit', function (e) {
                    let valid = true;

                    const currentPwd = document.getElementById('current_password');
                    const newPwd     = document.getElementById('new_password');
                    const confirmPwd = document.getElementById('confirm_password');

                    hideError('current_password');
                    hideError('new_password');
                    hideError('confirm_password');

                    if (!currentPwd.value.trim()) {
                        showError('current_password', 'Current password is required.');
                        valid = false;
                    }

                    const pwd = newPwd.value;
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

                    if (confirmPwd.value !== pwd) {
                        showError('confirm_password', 'Confirm password must match new password.');
                        valid = false;
                    }

                    if (!valid) e.preventDefault();
                });
            }

        }); // ← correctly closes DOMContentLoaded
    </script>
</body>
</html>