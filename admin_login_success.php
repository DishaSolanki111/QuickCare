<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if admin is logged in
if (!isset($_SESSION['LOGGED_IN']) || $_SESSION['LOGGED_IN'] !== true || $_SESSION['USER_TYPE'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

$admin_name = $_SESSION['USER_NAME'] ?? 'Administrator';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Successful - QuickCare Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0066cc;
            --primary-dark: #0052a3;
            --primary-light: #e6f2ff;
            --secondary: #00a8cc;
            --accent: #00a86b;
            --success: #28a745;
            --success-light: #d4edda;
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
            --admin-color: #2c5282;
            --admin-gradient: linear-gradient(135deg, #0066cc  0%, #6bacfc 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
            background: linear-gradient(135deg, #f0f7ff 0%, #ffffff 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .success-container {
            text-align: center;
            background: var(--white);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: var(--shadow-2xl);
            max-width: 500px;
            width: 100%;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(0, 102, 204, 0.1);
        }

        .success-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 2rem;
            background: var(--success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: scaleIn 0.5s ease-out;
        }

        .success-icon i {
            font-size: 3rem;
            color: var(--white);
        }

        .success-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--success);
            margin-bottom: 1rem;
            animation: fadeInUp 0.6s ease-out;
        }

        .success-message {
            font-size: 1.1rem;
            color: var(--text);
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.7s ease-out;
        }

        .admin-name {
            font-weight: 600;
            color: var(--admin-color);
        }

        .redirect-info {
            background: var(--success-light);
            border: 1px solid #c3e6cb;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 2rem;
            animation: fadeInUp 0.8s ease-out;
        }

        .redirect-info p {
            color: #155724;
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }

        .countdown {
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--success);
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.9s ease-out;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--success), var(--accent));
            border-radius: 10px;
            width: 0%;
            animation: progressAnimation 3s linear forwards;
        }

        .manual-redirect {
            animation: fadeInUp 1s ease-out;
        }

        .btn-redirect {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--admin-gradient);
            color: var(--white);
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-redirect:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes progressAnimation {
            from {
                width: 0%;
            }
            to {
                width: 100%;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .success-container {
                padding: 2rem;
                margin: 1rem;
            }

            .success-title {
                font-size: 1.5rem;
            }

            .success-icon {
                width: 80px;
                height: 80px;
            }

            .success-icon i {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1 class="success-title">Login Successful!</h1>
        
        <p class="success-message">
            Welcome back, <span class="admin-name"><?php echo htmlspecialchars($admin_name); ?></span>!
        </p>
        
        <div class="redirect-info">
            <p>You are being redirected to the admin dashboard...</p>
            <p class="countdown">Redirecting in <span id="countdown">3</span> seconds</p>
        </div>
        
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
        
        <div class="manual-redirect">
            <a href="admin.php" class="btn-redirect">
                <i class="fas fa-tachometer-alt"></i>
                Go to Dashboard Now
            </a>
        </div>
    </div>

    <script>
        // Countdown timer
        let seconds = 3;
        const countdownElement = document.getElementById('countdown');
        
        const countdown = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(countdown);
                window.location.href = 'admin.php';
            }
        }, 1000);
        
        // Auto-redirect after 3 seconds
        setTimeout(() => {
            window.location.href = 'admin.php';
        }, 3000);
        
        // Allow manual redirect
        document.querySelector('.btn-redirect').addEventListener('click', function(e) {
            e.preventDefault();
            clearInterval(countdown);
            window.location.href = 'admin.php';
        });
    </script>
</body>
</html>
