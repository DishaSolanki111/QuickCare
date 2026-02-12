<?php
session_start();

// Check if this is a standalone login (not from appointment booking)
 $standalone = $_POST['standalone'] ?? false;

// Only require these parameters if not standalone
if (!$standalone) {
    // Check both POST and GET for parameters
    $doctor_id   = $_POST['doctor_id']   ?? null;
    $date        = $_POST['date']        ?? null;
    $time        = $_POST['time']        ?? null;
    $schedule_id = $_POST['schedule_id'] ?? null;

    // Only check for appointment data if at least one parameter is provided
    // This allows the login page to work both with and without appointment data
    if ($doctor_id || $date || $time || $schedule_id) {
        if (!$doctor_id || !$date || !$time || !$schedule_id) {
            die("Invalid access - incomplete appointment data");
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* 🔴 EXACT SAME CSS — NOT TOUCHED */
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

        .form-header { 
            margin-bottom: 30px; 
            text-align: center;
            position: relative;
        }

        .back-button {
            position: absolute;
            left: 0;
            top: 0;
            background: none;
            border: none;
            color: var(--primary);
            font-size: 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            padding: 5px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            color: var(--primary-dark);
            transform: translateX(-3px);
        }

        .back-button i {
            margin-right: 5px;
        }

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
        
        .signup-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .signup-link a {
            color: var(--primary);
            text-decoration: none;
        }
        
        .signup-link a:hover {
            text-decoration: underline;
        }
        
        .appointment-info {
            background-color: #e8f0fe;
            padding: 10px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #174ea6;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-form">
        <div class="form-header">
            <?php if (!$standalone && $doctor_id && $date && $time && $schedule_id): ?>
            <button class="back-button" onclick="goBack()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <?php endif; ?>
            <h2>Patient Login</h2>
            <p>Access your account to book appointments</p>
        </div>

        <?php if (!$standalone && $doctor_id && $date && $time && $schedule_id): ?>
        <div class="appointment-info">
            <strong>Appointment Details:</strong><br>
            Date: <?= date("l, F j, Y", strtotime($date)) ?><br>
            Time: <?= $time ?>
        </div>
        <?php endif; ?>

        <form action="login_process.php" method="post">

            <!-- 🔴 INVISIBLE BUT CRITICAL -->
            <?php if (!$standalone && $doctor_id && $date && $time && $schedule_id): ?>
            <input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">
            <input type="hidden" name="date" value="<?= $date ?>">
            <input type="hidden" name="time" value="<?= $time ?>">
            <input type="hidden" name="schedule_id" value="<?= $schedule_id ?>">
            <?php endif; ?>

            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="signup-link">
            Don't have an account? <a href="register.php">Register Yourself</a>
        </div>
    </div>
</div>

<script>
function goBack() {
    // Go back to the previous page
    window.history.back();
}
</script>

</body>
</html>