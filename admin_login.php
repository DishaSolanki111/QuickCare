<?php
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickCare Admin Login</title>

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

        .login-footer {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            box-sizing: border-box;
            padding: 0 4px;
        }

        .login-footer a {
            font-size: 14px;
            color: #1a73e8;
            text-decoration: none;
            font-weight: 500;
            white-space: nowrap;
        }

        .login-footer a:hover {
            text-decoration: underline;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        /* LOGIN FORM STYLES - ENLARGED */
        .stage {
            width: 100%;
            max-width: 1200px;
            display: grid;
            grid-template-columns: 1fr 450px;
            gap: 40px;
            align-items: center;
            margin-top: 120px;
            background: rgba(255, 255, 255, 0.95);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: var(--shadow-2xl);
            backdrop-filter: blur(10px);
        }

        .hero {
            color: var(--text);
            padding: 2rem;
        }

        .brand {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 20px;
            background: var(--admin-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            line-height: 1.2;
        }

        .subtitle {
            color: var(--text-light);
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 1.2rem;
        }

        .container {
            position: relative;
            width: 450px;
        }

        .admin-badge {
            display: inline-flex;
            align-items: center;
            background: var(--admin-gradient);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .admin-badge svg {
            margin-right: 8px;
        }

        .tabs {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            background: var(--primary-light);
            border-radius: 15px;
            padding: 5px;
        }

        .tab {
            flex: 1;
            background: transparent;
            border: none;
            color: var(--text-light);
            padding: 15px;
            opacity: 0.7;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .tab.active {
            opacity: 1;
            background: white;
            color: var(--admin-color);
            box-shadow: var(--shadow-md);
        }

        /* --- MODIFIED SLIDER & FORM STYLES --- */
        .slider {
            position: relative;
            width: 450px;
            min-height: 350px;
            overflow: hidden;
        }

        .form {
            width: 450px;
            min-height: 350px;
            background: white;
            padding: 30px;
            border-radius: 15px;
            position: absolute;
            top: 0;
            left: 100%;
            opacity: 0;
            transition: left 0.4s ease-in-out, opacity 0.3s ease-in-out;
            box-shadow: var(--shadow-lg);
        }

        .form.active {
            left: 0;
            opacity: 1;
        }

        .form h3 {
            font-size: 1.8rem;
            color: var(--dark);
            margin-bottom: 25px;
            font-weight: 700;
        }

        input {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
            border: 2px solid #e1e5e9;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--admin-color);
            box-shadow: 0 0 0 3px rgba(142, 36, 170, 0.1);
        }

        button {
            width: 100%;
            padding: 15px;
            background: var(--admin-gradient);
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 10px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .small {
            font-size: 15px;
            color: var(--text-light);
            text-align: center;
            margin-top: 15px;
        }

        .small a {
            color: var(--admin-color);
            text-decoration: none;
            font-weight: 500;
        }

        .small a:hover {
            text-decoration: underline;
        }

        /* Add decorative elements */
        .hero::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 150px;
            height: 150px;
            background: var(--admin-gradient);
            border-radius: 50%;
            opacity: 0.1;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30px;
            right: -30px;
            width: 100px;
            height: 100px;
            background: var(--admin-gradient);
            border-radius: 50%;
            opacity: 0.1;
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 1200px) {
            .search-container {
                display: none;
            }
        }

        @media (max-width: 992px) {
            .nav-right-container {
                display: none;
            }

            .mobile-menu-btn {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .stage {
                grid-template-columns: 1fr;
                text-align: center;
                padding: 2rem;
                max-width: 500px;
            }

            .container {
                margin: 0 auto;
                width: 100%;
            }

            .slider {
                width: 100%;
            }

            .form {
                width: 100%;
                left: 100%;
            }
        }

        @media (max-width: 768px) {
            .mobile-nav {
                width: 100%;
                right: -100%;
            }

            .brand {
                font-size: 36px;
            }

            .subtitle {
                font-size: 1rem;
            }

            .stage {
                padding: 1.5rem;
                margin-top: 100px;
            }
        }

        .logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
        }

        .error-message {
            color: var(--warning);
            background-color: rgba(255, 107, 107, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: none;
        }

        /* Password field container and eye icon styles */
        .password-container {
            position: relative;
            margin-bottom: 5px;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-light);
        }

        .password-toggle:hover {
            color: var(--admin-color);
        }

        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }

        .back-to-login a {
            color: var(--text-light);
            text-decoration: none;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
        }

        .back-to-login a:hover {
            color: var(--admin-color);
        }

        .back-to-login svg {
            margin-right: 5px;
        }
    </style>
</head>
<body>

<?php
include 'header.php';
?>

<div class="stage">
    <div class="hero">
        <h1 class="brand">QuickCare Admin</h1>
        <p class="subtitle">Administrator access for system management and configuration. Secure login required for authorized personnel only.</p>
    </div>

    <div class="container">
       

        <div class="slider">
            <!-- Admin Login Form -->
            <form action="admin_login_process.php" method="POST" class="form active" id="admin">
                <input type="hidden" name="user_type" value="admin">
                <h3>Admin Login</h3>
                <div class="error-message" id="admin-error">
                    <?php 
                    if (isset($_GET['error'])) {
                        echo htmlspecialchars($_GET['error']);
                    }
                    ?>
                </div>
                <input type="text" name="username" placeholder="Admin Username" required>
                <div class="password-container">
                    <input type="password" name="pswd" placeholder="Admin Password" required id="admin-password">
                    <span class="password-toggle" onclick="togglePassword('admin-password', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </span>
                </div>
                <button type="submit">Login</button>
                <div class="login-footer">
                    <a href="forgot_password.php">Forgot password?</a>
                </div>
            </form>
        </div>

        <div class="back-to-login">
            <a href="loginhome.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Back to User Login
            </a>
        </div>
    </div>
</div>

<script>
    // Password visibility toggle function
    function togglePassword(inputId, toggleElement) {
        const passwordInput = document.getElementById(inputId);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            // Change to eye-off icon
            toggleElement.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                    <line x1="1" y1="1" x2="23" y2="23"></line>
                </svg>
            `;
        } else {
            passwordInput.type = 'password';
            // Change back to eye icon
            toggleElement.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            `;
        }
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        // Mobile menu toggle
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const closeMenu = document.querySelector('.close-menu');
        const mobileNav = document.querySelector('.mobile-nav');

        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileNav.classList.add('active');
            });
        }

        if (closeMenu) {
            closeMenu.addEventListener('click', () => {
                mobileNav.classList.remove('active');
            });
        }

        // Navbar scroll effect
        let lastScroll = 0;
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (header) {
                const currentScroll = window.pageYOffset;
                
                if (currentScroll > 100) {
                    header.style.padding = '0.5rem 0';
                    header.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
                } else {
                    header.style.padding = '1rem 0';
                    header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.05)';
                }
                lastScroll = currentScroll;
            }
        });

        // Active link switching
        document.querySelectorAll('.nav-links a, .mobile-nav-links a').forEach(link => {
            link.addEventListener('click', function(e) {
                document.querySelectorAll('.nav-links a, .mobile-nav-links a').forEach(l => {
                    l.classList.remove('active');
                });
                this.classList.add('active');
            });
        });
        
        // Show error messages from GET data
        <?php if (isset($_GET['error'])): ?>
            const errorElement = document.getElementById('admin-error');
            if (errorElement) {
                errorElement.style.display = 'block';
            }
        <?php endif; ?>
    });
</script>

</body>
</html>