<?php
session_start();
include "header.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* (Your existing CSS styles) */
        :root { --primary: #1a73e8; --primary-dark: #0b57d0; --primary-light: #e8f0fe; --secondary: #4285f4; --accent: #174ea6; --light-blue: #f0f7ff; --medium-blue: #d2e3fc; --dark-blue: #002855; --text-dark: #202124; --text-light: #ffffff; --shadow: 0 4px 15px rgba(0, 0, 0, 0.1); --shadow-hover: 0 8px 25px rgba(0, 0, 0, 0.15); --success: #28a745; --error: #dc3545; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: linear-gradient(135deg, #f5f8ff 0%, #e6f0ff 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
        .login-container { width: 100%; max-width: 450px; background: white; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow); display: flex; flex-direction: column; }
        .login-form { padding: 40px; }
        .form-header { margin-bottom: 30px; text-align: center; }
        .form-group { margin-bottom: 20px; position: relative; }
        .form-group input { width: 100%; padding: 12px 15px 12px 45px; border: 1px solid #dadce0; border-radius: 8px; }
        .form-group i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #5f6368; }
        .login-btn { width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; transition: background 0.3s; }
        .login-btn:hover { background: var(--primary-dark); }
        .back-link { text-align: center; margin-top: 20px; color: #666; }
        .back-link a { color: var(--primary); text-decoration: none; }
        .back-link a:hover { text-decoration: underline; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center; font-size: 14px; }
        .alert-error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-form">
        <div class="form-header">
            <h2>Reset Password</h2>
            <p>Enter the OTP sent to your phone and your new password.</p>
        </div>

        <?php
        // Display error message if it exists
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']); // Remove error after showing it
        }
        ?>

        <form action="reset_password.php" method="POST">
            <div class="form-group">
                <i class="fas fa-key"></i>
                <input type="text" name="otp" placeholder="Enter 6-digit OTP" required maxlength="6">
            </div>
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="new_password" placeholder="Enter new password" required>
            </div>
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" placeholder="Confirm new password" required>
            </div>
            <button type="submit" class="login-btn">Reset Password</button>
        </form>

        <div class="back-link">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</div>

</body>
</html>