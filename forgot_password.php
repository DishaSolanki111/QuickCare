<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Reusing the same styles as login.php for consistency */
        :root {
            --primary: #1a73e8;
            --primary-dark: #0b57d0;
            --primary-light: #e8f0fe;
            --secondary: #4285f4;
            --accent: #174ea6;
            --light-blue: #f0f7ff;
            --medium-blue: #d2e3fc;
            --dark-blue: #002855;
            --text-dark: #202124;
            --text-light: #ffffff;
            --shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 8px 25px rgba(0, 0, 0, 0.15);
            --success: #28a745;
            --error: #dc3545;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

        body {
            background: linear-gradient(135deg, #f5f8ff 0%, #e6f0ff 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
        }

        .login-form { padding: 40px; }

        .form-header { margin-bottom: 30px; text-align: center; }

        .form-group { margin-bottom: 20px; position: relative; }

        .form-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 1px solid #dadce0;
            border-radius: 8px;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
        
        .back-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .back-link a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-form">
        <div class="form-header">
            <h2>Forgot Password</h2>
            <p>Enter your username to reset your password</p>
        </div>

        <form action="forgot_password_process.php" method="post">
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Enter your username" required>
            </div>
            <button type="submit" class="login-btn">Reset My Password</button>
        </form>

        <div class="back-link">
            <a href="login.php">Back to Login</a>
        </div>
    </div>
</div>

</body>
</html>