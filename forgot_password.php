<?php
// Start session only if not already active (prevents PHP 8.2 notice)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use the consistent database connection file
require_once "config.php";

$stage         = 'request'; // request | verify | set | done
 $error_message = '';
 $info_message  = '';
 $new_password  = '';

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
            // Look up user by username in the appropriate table
            if ($user_type === 'patient') {
                $stmt = $conn->prepare("SELECT PATIENT_ID AS id, PHONE FROM patient_tbl WHERE USERNAME = ?");
            } elseif ($user_type === 'doctor') {
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
        // User submits a new password after OTP verification
        $user_type = $_SESSION['reset_user_type']  ?? null;
        $user_id   = $_SESSION['reset_user_id']    ?? null;
        $reset_id  = $_SESSION['reset_request_id'] ?? null;

        $password        = trim($_POST['password'] ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        if (!$user_type || !$user_id || !$reset_id) {
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

                // Delete the used reset row
                $del = $conn->prepare("DELETE FROM password_resets WHERE id = ?");
                if ($del) {
                    $del->bind_param("i", $reset_id);
                    $del->execute();
                    $del->close();
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
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .reset-card {
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.12);
            width: 100%;
            max-width: 420px;
        }
        h2 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #1a3a5f;
        }
        p.sub {
            margin-top: 0;
            margin-bottom: 20px;
            color: #4b5563;
            font-size: 14px;
        }
        label {
            display: block;
            font-size: 14px;
            margin-bottom: 6px;
            color: #374151;
            font-weight: 500;
        }
        input, select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 14px;
            margin-bottom: 14px;
            box-sizing: border-box;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.2);
        }
        button {
            width: 100%;
            padding: 11px 14px;
            border-radius: 8px;
            border: none;
            background: #2563eb;
            color: #ffffff;
            font-weight: 600;
            cursor: pointer;
            font-size: 15px;
        }
        button:hover {
            background: #1d4ed8;
        }
        .error {
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 12px;
            font-size: 13px;
        }
        .info {
            background: #dbeafe;
            color: #1d4ed8;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 12px;
            font-size: 13px;
        }
        .new-password-box {
            background: #ecfdf3;
            border: 1px dashed #16a34a;
            padding: 12px;
            border-radius: 8px;
            margin-top: 16px;
            font-size: 14px;
            color: #166534;
        }
        .back-link {
            margin-top: 16px;
            text-align: center;
            font-size: 13px;
        }
        .back-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 500;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="reset-card">
        <?php if ($stage === 'request'): ?>
            <h2>Forgot Password</h2>
            <p class="sub">Enter your username and select your role. We will send a one-time password (OTP) to your registered phone.</p>

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
                <input type="text" name="username" id="username" required>

                <button type="submit">Send OTP</button>
            </form>

            <div class="back-link">
                <a href="login_for_all.php">Back to login</a>
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

                <button type="submit">Verify OTP & Reset Password</button>
            </form>

            <div class="back-link">
                <a href="forgot_password.php">Start over</a>
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
                <a href="forgot_password.php">Start over</a>
            </div>

        <?php elseif ($stage === 'done'): ?>
            <h2>Password Reset Successful</h2>
            <p class="sub">Your password has been reset. You can now log in with your new password.</p>

            <div class="back-link">
                <a href="login_for_all.php">Go to login</a>
            </div>

        <?php endif; ?>
    </div>
</body>
</html>