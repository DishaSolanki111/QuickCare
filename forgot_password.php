<?php
// Start session only if not already active (prevents PHP 8.2 notice)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use the consistent database connection file
require_once "config.php";

$stage              = 'request'; // request | security | verify | set | done
$error_message      = '';
$info_message       = '';
$new_password       = '';
$security_question  = '';

// If we already started a reset, keep user context in session
if (!isset($_SESSION['reset_user_type'])) {
    $_SESSION['reset_user_type'] = null;
    $_SESSION['reset_user_id']   = null;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $step = $_POST['step'] ?? 'request';

    if ($step === 'request') {
        $user_type = $_POST['user_type'] ?? '';
        $username  = trim($_POST['username'] ?? '');

        if ($user_type === '' || $username === '') {
            $error_message = 'Please select user type and enter username.';
        } else {
            // For patients, use security question instead of OTP
            if ($user_type === 'patient') {
                $stmt = $conn->prepare("SELECT PATIENT_ID AS id, SECURITY_QUESTION FROM patient_tbl WHERE USERNAME = ?");
                if (!$stmt) {
                    $error_message = 'Unable to process your request. Please try again.';
                } else {
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $res = $stmt->get_result();

                    if ($res && $row = $res->fetch_assoc()) {
                        $user_id           = (int)$row['id'];
                        $security_question = $row['SECURITY_QUESTION'] ?? '';

                        $_SESSION['reset_user_type']          = 'patient';
                        $_SESSION['reset_user_id']            = $user_id;
                        $_SESSION['reset_security_question']  = $security_question;

                        if ($security_question === '' || $security_question === null) {
                            $error_message = 'No security question is set for this account. Please contact support.';
                            $stage = 'request';
                        } else {
                            $stage = 'security';
                        }
                    } else {
                        // Generic message to avoid username enumeration
                        $error_message = 'If the username exists, you will be able to reset your password.';
                    }
                    $stmt->close();
                }
            } else {
                // For doctor & receptionist, keep existing OTP-based flow
                if ($user_type === 'doctor') {
                    $stmt = $conn->prepare("SELECT DOCTOR_ID AS id, PHONE FROM doctor_tbl WHERE USERNAME = ?");
                } elseif ($user_type === 'receptionist') {
                    $stmt = $conn->prepare("SELECT RECEPTIONIST_ID AS id, PHONE FROM receptionist_tbl WHERE USERNAME = ?");
                } else {
                    $stmt = null;
                }

                if (!$stmt) {
                    $error_message = 'Invalid user type.';
                } else {
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $res = $stmt->get_result();

                    if ($res && $row = $res->fetch_assoc()) {
                        $user_id = (int) $row['id'];
                        $phone   = $row['PHONE'];

                        // Generate 6-digit OTP
                        $otp      = random_int(100000, 999999);
                        $otp_hash = password_hash((string)$otp, PASSWORD_DEFAULT);
                        $expires  = date('Y-m-d H:i:s', time() + 600); // 10 minutes

                        // Store in password_resets
                        $insert = $conn->prepare("
                            INSERT INTO password_resets (user_type, user_id, phone, otp_hash, expires_at, attempts)
                            VALUES (?, ?, ?, ?, ?, 0)
                        ");
                        if ($insert) {
                            $insert->bind_param("sisss", $user_type, $user_id, $phone, $otp_hash, $expires);
                            $insert->execute();
                            $insert->close();

                            // Save context for verification step
                            $_SESSION['reset_user_type'] = $user_type;
                            $_SESSION['reset_user_id']   = $user_id;

                            // NOTE: In production, send $otp via SMS/Email.
                            // For development/demo, we show it on screen.
                            $info_message = "An OTP has been sent to your registered phone. (For demo: OTP is {$otp})";
                            $stage = 'verify';
                        } else {
                            $error_message = 'Unable to create reset request. Please try again.';
                        }
                    } else {
                        // Use a generic error message to prevent user enumeration
                        $error_message = 'If the username exists, an OTP will be sent.';
                    }

                    $stmt->close();
                }
            }
        }
    } elseif ($step === 'security') {
        // Patient answers security question
        $user_type = $_SESSION['reset_user_type'] ?? null;
        $user_id   = $_SESSION['reset_user_id'] ?? null;
        $answer    = trim($_POST['security_answer'] ?? '');

        if ($user_type !== 'patient' || !$user_id) {
            $error_message = 'Reset session expired. Please start again.';
            $stage = 'request';
        } elseif ($answer === '') {
            $error_message = 'Please enter your answer.';
            $stage = 'security';
        } else {
            $stmt = $conn->prepare("SELECT SECURITY_ANSWER FROM patient_tbl WHERE PATIENT_ID = ?");
            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($res && $row = $res->fetch_assoc()) {
                    $db_answer = $row['SECURITY_ANSWER'] ?? '';
                    if ($db_answer !== '' && strcasecmp(trim($db_answer), $answer) === 0) {
                        // Correct answer – allow setting new password (no OTP)
                        $_SESSION['reset_request_id'] = null;
                        $stage = 'set';
                    } else {
                        $error_message = 'Incorrect answer. Please try again.';
                        $stage = 'security';
                    }
                } else {
                    $error_message = 'Unable to verify answer. Please start again.';
                    $stage = 'request';
                }
                $stmt->close();
            } else {
                $error_message = 'Unable to verify answer. Please try again later.';
                $stage = 'request';
            }
        }
    } elseif ($step === 'verify') {
        $entered_otp = trim($_POST['otp'] ?? '');

        $user_type = $_SESSION['reset_user_type'] ?? null;
        $user_id   = $_SESSION['reset_user_id'] ?? null;

        if (!$user_type || !$user_id) {
            $error_message = 'Reset session expired. Please start again.';
            $stage = 'request';
        } elseif ($entered_otp === '') {
            $error_message = 'Please enter the OTP.';
            $stage = 'verify';
        } else {
            // Get latest reset request for this user
            $stmt = $conn->prepare("
                SELECT id, otp_hash, expires_at, attempts
                FROM password_resets
                WHERE user_type = ? AND user_id = ?
                ORDER BY id DESC
                LIMIT 1
            ");
            $stmt->bind_param("si", $user_type, $user_id);
            $stmt->execute();
            $res = $stmt->get_result();

            if (!$res || !$row = $res->fetch_assoc()) {
                $error_message = 'No valid reset request found. Please start again.';
                $stage = 'request';
            } else {
                $reset_id  = (int) $row['id'];
                $otp_hash  = $row['otp_hash'];
                $expires   = strtotime($row['expires_at']);
                $attempts  = (int) $row['attempts'];

                if (time() > $expires) {
                    $error_message = 'OTP has expired. Please request a new one.';
                    $stage = 'request';
                } elseif ($attempts >= 5) {
                    $error_message = 'Too many incorrect attempts. Please request a new OTP.';
                    $stage = 'request';
                } elseif (!password_verify($entered_otp, $otp_hash)) {
                    // Increment attempts
                    $upd = $conn->prepare("UPDATE password_resets SET attempts = attempts + 1 WHERE id = ?");
                    $upd->bind_param("i", $reset_id);
                    $upd->execute();
                    $upd->close();

                    $error_message = 'Invalid OTP. Please try again.';
                    $stage = 'verify';
                } else {
                    // OTP correct: move to the "set new password" step
                    $_SESSION['reset_user_type']  = $user_type;
                    $_SESSION['reset_user_id']    = $user_id;
                    $_SESSION['reset_request_id'] = $reset_id;
                    $stage = 'set';
                }
            }

            $stmt->close();
        }
    } elseif ($step === 'set') {
        // User submits a new password after OTP verification / security question
        $user_type = $_SESSION['reset_user_type']  ?? null;
        $user_id   = $_SESSION['reset_user_id']    ?? null;
        $reset_id  = $_SESSION['reset_request_id'] ?? null;

        $password         = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        // For patients using security question, there is no reset_id required
        if (!$user_type || !$user_id || ($user_type !== 'patient' && !$reset_id)) {
            $error_message = 'Reset session expired. Please start again.';
            $stage = 'request';
        } elseif ($password === '' || $confirm_password === '') {
            $error_message = 'Please enter and confirm your new password.';
            $stage = 'set';
        } elseif ($password !== $confirm_password) {
            $error_message = 'Passwords do not match.';
            $stage = 'set';
        } elseif (strlen($password) < 6) {
            $error_message = 'Password should be at least 6 characters long.';
            $stage = 'set';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            if ($user_type === 'patient') {
                $upd = $conn->prepare("UPDATE patient_tbl SET PSWD = ? WHERE PATIENT_ID = ?");
            } elseif ($user_type === 'doctor') {
                $upd = $conn->prepare("UPDATE doctor_tbl SET PSWD = ? WHERE DOCTOR_ID = ?");
            } elseif ($user_type === 'receptionist') {
                $upd = $conn->prepare("UPDATE receptionist_tbl SET PSWD = ? WHERE RECEPTIONIST_ID = ?");
            } else {
                $upd = null;
            }

            if ($upd) {
                $upd->bind_param("si", $hash, $user_id);
                $upd->execute();
                $upd->close();
                // For OTP-based reset, delete the used reset row
                if (!empty($reset_id)) {
                    $del = $conn->prepare("DELETE FROM password_resets WHERE id = ?");
                    if ($del) {
                        $del->bind_param("i", $reset_id);
                        $del->execute();
                        $del->close();
                    }
                }

                // Clear reset context
                $_SESSION['reset_user_type']  = null;
                $_SESSION['reset_user_id']    = null;
                $_SESSION['reset_request_id'] = null;

                $stage = 'done';
            } else {
                $error_message = 'Could not update password. Please contact support.';
                $stage = 'set';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Hospital Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-gradient-start: #BEE3F8;
            --bg-gradient-end:   #EBF8FF;
            --dark-blue: #1A365D;
            --primary-blue: #0077b6;
            --primary-blue-2: #00b4d8;
            --text-main: #0f172a;
            --text-muted: #4A5568;
            --border-light: #cbd5f5;
            --error-bg: #fee2e2;
            --error-text: #b91c1c;
            --info-bg: #dbeafe;
            --info-text: #1d4ed8;
            --card-bg: rgba(255, 255, 255, 0.7);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, 0.12), transparent 55%),
                radial-gradient(circle at bottom right, rgba(56, 189, 248, 0.12), transparent 55%),
                linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
        }

        /* subtle abstract medical waves overlay */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg width='1600' height='900' viewBox='0 0 1600 900' xmlns='http://www.w3.org/2000/svg'%3E%3Cdefs%3E%3ClinearGradient id='g' x1='0%25' y1='0%25' x2='100%25' y2='100%25'%3E%3Cstop stop-color='%23ffffff' stop-opacity='0.15' offset='0%25'/%3E%3Cstop stop-color='%23ffffff' stop-opacity='0.05' offset='100%25'/%3E%3C/linearGradient%3E%3C/defs%3E%3Cpath d='M0 600 Q400 520 800 580 T1600 560 L1600 900 L0 900 Z' fill='url(%23g)'/%3E%3Cpath d='M0 260 Q500 340 900 300 T1600 320 L1600 900 L0 900 Z' fill='url(%23g)'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-size: cover;
            opacity: 0.35;
            pointer-events: none;
            z-index: -1;
        }

        .reset-card {
            width: 100%;
            max-width: 460px;
            padding: 32px 30px 26px;
            border-radius: 24px;
            background: var(--card-bg);
            box-shadow:
                0 28px 65px rgba(15, 23, 42, 0.35),
                0 0 0 1px rgba(255, 255, 255, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        h2 {
            margin: 0 0 8px 0;
            color: var(--dark-blue);
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        p.sub {
            margin: 0 0 20px 0;
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.6;
        }

        label {
            display: block;
            font-size: 14px;
            margin-bottom: 6px;
            color: #1f2933;
            font-weight: 500;
        }

        .field-with-icon {
            position: relative;
            margin-bottom: 14px;
        }

        .field-with-icon i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            font-size: 0.9rem;
            pointer-events: none;
        }

        .input-icon {
            padding-left: 36px !important;
        }

        input,
        select {
            width: 100%;
            padding: 11px 12px;
            border-radius: 12px;
            border: 1px solid var(--border-light);
            font-size: 14px;
            margin-bottom: 14px;
            background: #ffffff;
            color: var(--text-main);
            transition:
                border-color 0.18s ease,
                box-shadow 0.18s ease,
                transform 0.18s ease,
                background-color 0.18s ease;
        }

        input::placeholder {
            color: #9ca3af;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 2px rgba(0, 119, 182, 0.25);
            transform: translateY(-1px);
        }

        button {
            width: 100%;
            padding: 12px 16px;
            border-radius: 999px;
            border: none;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-2) 100%);
            color: #ffffff;
            font-weight: 600;
            cursor: pointer;
            font-size: 15px;
            letter-spacing: 0.5px;
            transition:
                background-color 0.18s ease,
                transform 0.2s ease,
                box-shadow 0.18s ease;
        }

        button:hover {
            box-shadow: 0 12px 24px rgba(0, 119, 182, 0.35);
            transform: translateY(-1px);
        }

        button:active {
            transform: scale(0.98);
            box-shadow: 0 6px 14px rgba(0, 119, 182, 0.25);
        }

        .error {
            background: var(--error-bg);
            color: var(--error-text);
            padding: 10px 12px;
            border-radius: 12px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .info {
            background: var(--info-bg);
            color: var(--info-text);
            padding: 10px 12px;
            border-radius: 12px;
            margin-bottom: 12px;
            font-size: 13px;
        }

        .new-password-box {
            background: #ecfdf3;
            border: 1px dashed #16a34a;
            padding: 12px;
            border-radius: 10px;
            margin-top: 16px;
            font-size: 14px;
            color: #166534;
        }

        .back-link {
            margin-top: 18px;
            text-align: center;
            font-size: 13px;
        }

        .back-link a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid rgba(15, 23, 42, 0.15);
            color: #1f2933;
            text-decoration: none;
            font-weight: 500;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.18s ease;
        }

        .back-link a i {
            font-size: 0.9rem;
        }

        .back-link a:hover {
            background: rgba(15, 23, 42, 0.85);
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.25);
            transform: translateY(-1px);
        }

        .security-question-box {
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-bottom: 12px;
            border: 1px solid rgba(209, 213, 219, 0.8);
        }
    </style>
</head>
<body>
    <div class="reset-card">
        <?php if ($stage === 'request'): ?>
            <h2>Forgot Password</h2>
            <p class="sub">
                Patients will verify using their security question. Doctors and receptionists will receive an OTP on their registered phone.
            </p>

            <?php if ($error_message): ?>
                <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form method="POST" action="forgot_password.php">
                <input type="hidden" name="step" value="request">

                <label for="user_type">I am a</label>
                <select name="user_type" id="user_type" required>
                    <option value="">-- Select Role --</option>
                    <option value="patient">Patient</option>
                    <option value="doctor">Doctor</option>
                    <option value="receptionist">Receptionist</option>
                </select>

                <label for="username">Username</label>
                <div class="field-with-icon">
                    <i class="fas fa-user-md"></i>
                    <input type="text" name="username" id="username" class="input-icon" required>
                </div>

                <button type="submit">Continue</button>
            </form>

            <div class="back-link">
                <a href="login_for_all.php">
                    <i class="fas fa-arrow-left"></i>
                    Back to login
                </a>
            </div>

        <?php elseif ($stage === 'security'): ?>
            <h2>Answer Security Question</h2>
            <p class="sub">Please answer the security question you set during registration.</p>

            <?php if ($error_message): ?>
                <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <?php
            // Get question from session for display
            if (isset($_SESSION['reset_security_question'])) {
                $security_question = $_SESSION['reset_security_question'];
            }
            ?>

            <form method="POST" action="forgot_password.php">
                <input type="hidden" name="step" value="security">

                <label>Security Question</label>
                <div class="security-question-box">
                    <?php echo htmlspecialchars($security_question ?: 'Security question not available.'); ?>
                </div>

                <label for="security_answer">Your Answer</label>
                <input type="text" name="security_answer" id="security_answer" required>

                <button type="submit">Verify &amp; Reset Password</button>
            </form>

            <div class="back-link">
                <a href="forgot_password.php">
                    <i class="fas fa-rotate-left"></i>
                    Start over
                </a>
            </div>

        <?php elseif ($stage === 'verify'): ?>
            <h2>Verify OTP</h2>
            <p class="sub">Enter the 6-digit OTP sent to your registered phone number.</p>

            <?php if ($error_message): ?>
                <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <?php if ($info_message): ?>
                <div class="info"><?php echo htmlspecialchars($info_message); ?></div>
            <?php endif; ?>

            <form method="POST" action="forgot_password.php">
                <input type="hidden" name="step" value="verify">

                <label for="otp">OTP</label>
                <input type="text" name="otp" id="otp" maxlength="6" required>

                <button type="submit">Verify OTP &amp; Reset Password</button>
            </form>

            <div class="back-link">
                <a href="forgot_password.php">
                    <i class="fas fa-rotate-left"></i>
                    Start over
                </a>
            </div>

        <?php elseif ($stage === 'set'): ?>
            <h2>Set New Password</h2>
            <p class="sub">Enter your new password below. After saving, you can log in with it.</p>

            <?php if ($error_message): ?>
                <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <form method="POST" action="forgot_password.php">
                <input type="hidden" name="step" value="set">

                <label for="password">New Password</label>
                <input type="password" name="password" id="password" required>

                <label for="confirm_password">Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" required>

                <button type="submit">Save New Password</button>
            </form>

            <div class="back-link">
                <a href="forgot_password.php">
                    <i class="fas fa-rotate-left"></i>
                    Start over
                </a>
            </div>

        <?php elseif ($stage === 'done'): ?>
            <h2>Password Reset Successful</h2>
            <p class="sub">Your password has been reset. You can now log in with your new password.</p>

            <div class="back-link">
                <a href="login_for_all.php">
                    <i class="fas fa-sign-in-alt"></i>
                    Go to login
                </a>
            </div>

        <?php endif; ?>
    </div>
</body>
</html>