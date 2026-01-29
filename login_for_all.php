<?php
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickCare Login</title>

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

        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 5px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary-dark); }
        
        /* LOGIN FORM STYLES */
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
            position: relative;
        }

        .brand {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 20px;
            background: var(--gradient-1);
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
            color: var(--primary);
            box-shadow: var(--shadow-md);
        }

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
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

        button {
            width: 100%;
            padding: 15px;
            background: var(--primary);
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
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }
        
        button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .small {
            font-size: 15px;
            color: var(--text-light);
            text-align: center;
            margin-top: 15px;
        }

        .small a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50px; left: -50px;
            width: 150px; height: 150px;
            background: var(--gradient-1);
            border-radius: 50%;
            opacity: 0.1;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30px; right: -30px;
            width: 100px; height: 100px;
            background: var(--gradient-2);
            border-radius: 50%;
            opacity: 0.1;
        }

        @media (max-width: 992px) {
            .nav-right-container { display: none; }
            .mobile-menu-btn { display: flex; align-items: center; justify-content: center; }
            .stage {
                grid-template-columns: 1fr;
                text-align: center;
                padding: 2rem;
                max-width: 500px;
            }
            .container { margin: 0 auto; width: 100%; }
            .slider { width: 100%; }
            .form { width: 100%; left: 100%; }
        }

        @media (max-width: 768px) {
            .mobile-nav { width: 100%; right: -100%; }
            .brand { font-size: 36px; }
            .stage { padding: 1.5rem; margin-top: 100px; }
        }
        
        .logo-img { height: 40px; margin-right: 12px; border-radius: 5px; }
        
        .error-message {
            color: var(--warning);
            background-color: rgba(255, 107, 107, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: none;
            font-size: 14px;
        }

        /* ✅ NEW SUCCESS MESSAGE STYLE */
        .success-message {
            color: #155724;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: none;
            font-size: 15px;
            font-weight: 600;
            text-align: center;
            animation: fadeIn 0.5s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
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
        
        .password-toggle:hover { color: var(--primary); }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="stage">
    <div class="hero">
        <h1 class="brand">QuickCare Login</h1>
        <p class="subtitle">Login for Patients, Doctors & Receptionists. Access your personalized healthcare dashboard.</p>
    </div>

    <div class="container">
        <div class="tabs">
            <button class="tab active" data-target="patient">Patient</button>
            <button class="tab" data-target="doctor">Doctor</button>
            <button class="tab" data-target="receptionist">Receptionist</button>
        </div>

       <div class="slider">
            <!-- Patient Login Form -->
            <form action="loginhome.php" method="POST" class="form active" id="patient">
                <input type="hidden" name="user_type" value="patient">
                <h3>Patient Login</h3>
                <div class="error-message" id="patient-error"></div>
                <!-- ✅ SUCCESS DIV ADDED -->
                <div class="success-message" id="patient-success"></div>
                
                <input type="text" name="username" placeholder="Username" required>
                <div class="password-container">
                    <input type="password" name="pswd" placeholder="Password" required id="patient-password">
                    <span class="password-toggle" onclick="togglePassword('patient-password', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </span>
                </div>
                <button type="submit">Login</button>
                <div class="login-footer">
                    <a href="register.php" class="left-link">Create account</a>
                    <a href="forgot_password.php" class="right-link">Forgot password?</a>
                </div>
            </form>

            <!-- Doctor Login Form -->
            <form action="loginhome.php" method="POST" class="form" id="doctor">
                <input type="hidden" name="user_type" value="doctor">
                <h3>Doctor Login</h3>
                <div class="error-message" id="doctor-error"></div>
                <!-- ✅ SUCCESS DIV ADDED -->
                <div class="success-message" id="doctor-success"></div>

                <input type="text" name="username" placeholder="Username" required>
                <div class="password-container">
                    <input type="password" name="pswd" placeholder="Password" required id="doctor-password">
                    <span class="password-toggle" onclick="togglePassword('doctor-password', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </span>
                </div>
                <button type="submit">Login</button>
                <div class="login-footer">
                    <a href="forgot_password.php" class="right-link">Forgot password?</a>
                </div>
            </form>

            <!-- Receptionist Login Form -->
            <form action="loginhome.php" method="POST" class="form" id="receptionist">
                <input type="hidden" name="user_type" value="receptionist">
                <h3>Receptionist Login</h3>
                <div class="error-message" id="receptionist-error"></div>
                <!-- ✅ SUCCESS DIV ADDED -->
                <div class="success-message" id="receptionist-success"></div>

                <input type="text" name="username" placeholder="Username" required>
                <div class="password-container">
                    <input type="password" name="pswd" placeholder="Password" required id="receptionist-password">
                    <span class="password-toggle" onclick="togglePassword('receptionist-password', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </span>
                </div>
                <button type="submit">Login</button>
                <div class="login-footer">
                    <a href="forgot_password.php" class="right-link">Forgot password?</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Password visibility toggle function
    function togglePassword(inputId, toggleElement) {
        const passwordInput = document.getElementById(inputId);
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleElement.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>`;
        } else {
            passwordInput.type = 'password';
            toggleElement.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>`;
        }
    }
    
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll(".tab");
        const forms = document.querySelectorAll(".form");

        // Tab Switching Logic
        tabs.forEach(tab => {
            tab.addEventListener("click", () => {
                const targetFormId = tab.dataset.target;
                tabs.forEach(t => t.classList.remove("active"));
                forms.forEach(f => f.classList.remove("active"));
                tab.classList.add("active");
                const targetForm = document.getElementById(targetFormId);
                if (targetForm) targetForm.classList.add("active");
            });
        });
        
        // ✅ AJAX FORM SUBMISSION LOGIC
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Stop normal page reload

                const formData = new FormData(this);
                const userType = formData.get('user_type');
                const errorDiv = document.getElementById(`${userType}-error`);
                const successDiv = document.getElementById(`${userType}-success`);
                const submitBtn = this.querySelector('button[type="submit"]');

                // Reset messages
                errorDiv.style.display = 'none';
                successDiv.style.display = 'none';
                
                // Disable button to prevent double submit
                submitBtn.disabled = true;
                submitBtn.innerText = 'Logging in...';

                fetch('loginhome.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Show Success Message
                        successDiv.innerText = data.message;
                        successDiv.style.display = 'block';
                        
                        // Hide inputs (optional, keeps UI clean)
                        const inputs = this.querySelectorAll('input');
                        inputs.forEach(input => input.style.display = 'none');
                        submitBtn.style.display = 'none';

                        // Wait 3 seconds then redirect
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 3000);

                    } else {
                        // Show Error Message
                        errorDiv.innerText = data.message;
                        errorDiv.style.display = 'block';
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Login';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    errorDiv.innerText = "An error occurred. Please try again.";
                    errorDiv.style.display = 'block';
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Login';
                });
            });
        });

        // Mobile Menu & Scroll Logic (Keep existing)
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const closeMenu = document.querySelector('.close-menu');
        const mobileNav = document.querySelector('.mobile-nav');

        if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', () => mobileNav.classList.add('active'));
        if (closeMenu) closeMenu.addEventListener('click', () => mobileNav.classList.remove('active'));

        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (header) {
                const currentScroll = window.pageYOffset;
                header.style.padding = currentScroll > 100 ? '0.5rem 0' : '1rem 0';
                header.style.boxShadow = currentScroll > 100 ? '0 5px 15px rgba(0, 0, 0, 0.1)' : '0 2px 10px rgba(0, 0, 0, 0.05)';
            }
        });
    });
</script>

</body>
</html>