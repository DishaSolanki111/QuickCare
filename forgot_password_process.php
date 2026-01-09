<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];

    // Basic validation
    if (empty($username)) {
        die("Error: Username is required.");
    }

    // Sanitize username
    $username = mysqli_real_escape_string($conn, $username);

    // Check if the username exists in the patient_tbl
    $query = "SELECT PATIENT_ID FROM patient_tbl WHERE USERNAME='$username'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // User exists, generate a new password
        // Generating an 8-character alphanumeric password
        $new_password = substr(bin2hex(random_bytes(4)), 0, 8); 
        
        // Hash the new password for secure storage
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // **THE SINGLE UPDATE QUERY**
        $updateQuery = "UPDATE patient_tbl SET PSWD='$hashed_password' WHERE USERNAME='$username'";
        
        if (mysqli_query($conn, $updateQuery)) {
            // Password updated successfully, now show the new password to the user
            ?>
            <!DOCTYPE html>
            <html lang="en">
            <head>
                <meta charset="UTF-8">
                <title>Password Reset Successful</title>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                <style>
                    /* Reusing styles for consistency */
                    :root { --primary: #1a73e8; --success: #28a745; --shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
                    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
                    body { background: linear-gradient(135deg, #f5f8ff 0%, #e6f0ff 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
                    .login-container { width: 100%; max-width: 450px; background: white; border-radius: 12px; box-shadow: var(--shadow); }
                    .login-form { padding: 40px; text-align: center; }
                    .form-header { margin-bottom: 30px; }
                    .new-password-display {
                        background-color: #e8f0fe;
                        border: 1px solid #c3e6cb;
                        padding: 20px;
                        border-radius: 8px;
                        margin: 20px 0;
                    }
                    .new-password-display h3 {
                        color: var(--success);
                        margin-bottom: 10px;
                    }
                    .password-text {
                        font-size: 24px;
                        font-weight: bold;
                        color: var(--primary);
                        letter-spacing: 2px;
                        background: #f0f7ff;
                        padding: 10px;
                        border-radius: 5px;
                        display: inline-block;
                    }
                    .login-btn { width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; display: inline-block; margin-top: 20px;}
                </style>
            </head>
            <body>
                <div class="login-container">
                    <div class="login-form">
                        <div class="form-header">
                            <i class="fas fa-check-circle" style="font-size: 48px; color: var(--success); margin-bottom: 15px;"></i>
                            <h2>Password Reset Successful</h2>
                        </div>
                        <p>Your password has been reset. Please use the password below to log in.</p>
                        
                        <div class="new-password-display">
                            <h3>Your New Password</h3>
                            <div class="password-text"><?= htmlspecialchars($new_password) ?></div>
                        </div>
                        
                        <p style="color: #666; font-size: 14px;">Make sure to save this password securely.</p>
                        
                        <a href="login.php" class="login-btn">Go to Login</a>
                    </div>
                </div>
            </body>
            </html>
            <?php
        } else {
            die("Error: Could not reset password. Please try again later.");
        }
    } else {
        // Username does not exist
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>User Not Found</title>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                /* Reusing styles for consistency */
                :root { --primary: #1a73e8; --error: #dc3545; --shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
                * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
                body { background: linear-gradient(135deg, #f5f8ff 0%, #e6f0ff 100%); min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px; }
                .login-container { width: 100%; max-width: 450px; background: white; border-radius: 12px; box-shadow: var(--shadow); }
                .login-form { padding: 40px; text-align: center; }
                .form-header { margin-bottom: 30px; }
                .form-header i { font-size: 48px; color: var(--error); margin-bottom: 15px; }
                .login-btn { width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; display: inline-block; margin-top: 20px;}
            </style>
        </head>
        <body>
            <div class="login-container">
                <div class="login-form">
                    <div class="form-header">
                        <i class="fas fa-user-slash"></i>
                        <h2>User Not Found</h2>
                    </div>
                    <p>We could not find an account associated with that username.</p>
                    <p>Please check the username and try again.</p>
                    
                    <a href="forgot_password.php" class="login-btn">Try Again</a>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}
?>