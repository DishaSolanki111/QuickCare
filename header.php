<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>QuickCare Hospital</title>
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

    /* GLOBAL STYLES */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }
    body {
        background: var(--light);
        color: var(--text);
        line-height: 1.6;
        overflow-x: hidden;
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

    /* ================= HEADER ================= */
    header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(0, 102, 204, 0.1);
    }

    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 5%;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Logo Area - Left Side */
    .logo-area {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .logo-area:hover {
        transform: scale(1.05);
    }

    .logo-img {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        object-fit: cover;
        box-shadow: var(--shadow-md);
    }

    .site-name {
        font-size: 1.8rem;
        font-weight: 700;
        background: var(--gradient-1);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    /* Right Side Container */
    .nav-right-container {
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    /* Navigation Links - Right Aligned */
    .nav-links {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .nav-links a {
        text-decoration: none;
        color: var(--text);
        font-weight: 500;
        font-size: 1rem;
        position: relative;
        transition: all 0.3s ease;
        padding: 0.5rem 0;
    }

    .nav-links a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--primary);
        transition: width 0.3s ease;
    }

    .nav-links a:hover::after {
        width: 100%;
    }

    .nav-links a:hover {
        color: var(--primary);
    }

    .nav-links a.active {
        color: var(--primary);
        font-weight: 600;
    }

    /* Auth Buttons */
    .auth-buttons {
        display: flex;
        gap: 0.8rem;
    }

    .btn-login, .btn-register {
        padding: 0.6rem 1.2rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-login {
        background: transparent;
        color: var(--primary);
        border: 2px solid var(--primary);
    }

    .btn-login:hover {
        background: var(--primary);
        color: white;
    }

    .btn-register {
        background: var(--primary);
        color: white;
    }

    .btn-register:hover {
        background: var(--primary-dark);
    }

    /* Mobile Menu Button */
    .mobile-menu-btn {
        display: none;
        background: var(--primary);
        border: none;
        border-radius: 8px;
        width: 40px;
        height: 40px;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .mobile-menu-btn:hover {
        background: var(--primary-dark);
    }

    /* Mobile Navigation */
    .mobile-nav {
        position: fixed;
        top: 0;
        right: -350px;
        width: 350px;
        height: 100vh;
        background: white;
        box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
        z-index: 1001;
        transition: right 0.3s ease;
        overflow-y: auto;
    }

    .mobile-nav.active {
        right: 0;
    }

    .mobile-nav-header {
        background: var(--primary);
        padding: 1.5rem;
        color: white;
        position: relative;
    }

    .close-menu {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        cursor: pointer;
    }

    .close-menu:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .mobile-nav-links {
        padding: 1.5rem;
    }

    .mobile-nav-links a {
        display: block;
        text-decoration: none;
        color: var(--text);
        font-weight: 500;
        padding: 0.8rem;
        margin-bottom: 0.5rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .mobile-nav-links a:hover {
        background: var(--primary-light);
        color: var(--primary);
    }

    .mobile-nav-links a.active {
        background: var(--primary);
        color: white;
    }

    /* RESPONSIVE */
    @media(max-width: 992px) {
        .nav-right-container {
            display: none;
        }

        .mobile-menu-btn {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }

    @media(max-width: 768px) {
        .mobile-nav {
            width: 100%;
            right: -100%;
        }
    }
</style>
</head>
<body>
    <!-- ================= HEADER ================= -->
    <header>
        <nav class="navbar">
            <div class="logo-area">
                <img src="./uploads/logo.JPG" alt="QuickCare Logo" class="logo-img">
                <span class="site-name">QuickCare</span>
            </div>
            
            <div class="nav-right-container">
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="schedule.php">Schedule</a>
                    <a href="appointment.php">Doctors</a>
                    <a href="aboutus.php" >About</a>
                    <a href="contactus.php">Contact</a>
                </div>
                
                <div class="auth-buttons">
                    <a href="login.php" class="btn-login">
                        Login
                    </a>
                    <a href="patientform.php" class="btn-register">
                        Register
                    </a>
                </div>
            </div>
            
            <button class="mobile-menu-btn">
                ☰
            </button>
        </nav>
    </header>

    <!-- Mobile Navigation -->
    <div class="mobile-nav">
        <div class="mobile-nav-header">
            <h2>QuickCare Menu</h2>
            <button class="close-menu">✕</button>
        </div>
        <div class="mobile-nav-links">
            <a href="index.php">Home</a>
            <a href="services.php">Services</a>
            <a href="doctors.php">Doctors</a>
            <a href="aboutus.php" class="active">About Us</a>
            <a href="contactus.php">Contact</a>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </div>
    </div>

    <script>
        // Mobile menu toggle
        const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
        const closeMenu = document.querySelector('.close-menu');
        const mobileNav = document.querySelector('.mobile-nav');

        mobileMenuBtn.addEventListener('click', () => {
            mobileNav.classList.add('active');
        });

        closeMenu.addEventListener('click', () => {
            mobileNav.classList.remove('active');
        });

        // Navbar scroll effect
        let lastScroll = 0;
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > 100) {
                header.style.padding = '0.5rem 0';
                header.style.boxShadow = '0 5px 15px rgba(0, 0, 0, 0.1)';
            } else {
                header.style.padding = '1rem 0';
                header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.05)';
            }

            lastScroll = currentScroll;
        });
    </script>
</body>
</html>