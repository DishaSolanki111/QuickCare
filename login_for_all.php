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
            margin-bottom: 20px;
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

        /* --- MODIFIED SLIDER & FORM STYLES --- */
        .slider {
            position: relative;
            width: 450px;
            min-height: 350px;
            overflow: hidden; /* This is crucial for the sliding effect */
        }

        .form {
            width: 450px;
            min-height: 350px;
            background: white;
            padding: 30px;
            border-radius: 15px;
            position: absolute;
            top: 0;
            /* Default state: hidden to the right and transparent */
            left: 100%; 
            opacity: 0;
            /* Smooth transition for sliding and fading */
            transition: left 0.4s ease-in-out, opacity 0.3s ease-in-out;
            box-shadow: var(--shadow-lg);
        }

        /* Active state: visible in the slider */
        .form.active {
            left: 0;
            opacity: 1;
        }
        /* --- END OF MODIFIED STYLES --- */

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
            background: var(--gradient-1);
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
            background: var(--gradient-2);
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
                left: 100%; /* Keep this for responsive */
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
    </style>
</head>
<body>

<?php
include 'header.php';
?>

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
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="pswd" placeholder="Password" required>
                <button type="submit">Login</button>
                <a href="patientform.php" class="small">Not registered yet? Create an account</a>
            </form>

            <!-- Doctor Login Form -->
            <form action="loginhome.php" method="POST" class="form" id="doctor">
                <input type="hidden" name="user_type" value="doctor">
                <h3>Doctor Login</h3>
                <div class="error-message" id="doctor-error"></div>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="pswd" placeholder="Password" required>
                <button type="submit">Login</button>
                <div class="small">Ask Receptionist to register you.</div>
            </form>

            <!-- Receptionist Login Form -->
            <form action="loginhome.php" method="POST" class="form" id="receptionist">
                <input type="hidden" name="user_type" value="receptionist">
                <h3>Receptionist Login</h3>
                <div class="error-message" id="receptionist-error"></div>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="pswd" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</div>

<!-- Hidden form for POST error handling -->
<form id="errorForm" action="login_for_all.php" method="POST" style="display: none;">
    <input type="hidden" id="errorUserType" name="user_type" value="">
    <input type="hidden" id="errorMessage" name="error" value="">
</form>

<script>
    // --- MODIFIED JAVASCRIPT FOR TAB SWITCHING ---
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll(".tab");
        const forms = document.querySelectorAll(".form");

        tabs.forEach(tab => {
            tab.addEventListener("click", () => {
                // 1. Get the target form ID from the clicked tab's data attribute
                const targetFormId = tab.dataset.target;
                
                // 2. Remove 'active' class from all tabs and all forms
                tabs.forEach(t => t.classList.remove("active"));
                forms.forEach(f => f.classList.remove("active"));
                
                // 3. Add 'active' class to the clicked tab
                tab.classList.add("active");
                
                // 4. Add 'active' class to the corresponding form
                const targetForm = document.getElementById(targetFormId);
                if (targetForm) {
                    targetForm.classList.add("active");
                }
            });
        });
        
        // --- Rest of your JavaScript remains the same ---
        
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
        
        // Show error messages from POST data
        <?php if (isset($_POST['error']) && isset($_POST['user_type'])): ?>
            const errorElement = document.getElementById('<?php echo $_POST['user_type']; ?>-error');
            if (errorElement) {
                errorElement.textContent = '<?php echo $_POST['error']; ?>';
                errorElement.style.display = 'block';
                
                // Find and click the correct tab to show the form with the error
                const tabWithError = document.querySelector(`.tab[data-target="<?php echo $_POST['user_type']; ?>"]`);
                if (tabWithError) {
                    tabWithError.click(); // Programmatically click the tab to trigger the switch
                }
            }
        <?php endif; ?>
    });
</script>

</body>
</html>