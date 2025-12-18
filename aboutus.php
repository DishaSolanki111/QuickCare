<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>About Us | Quick Care Hospital</title>
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

    /* Animated Background */
    .bg-animation {
        position: fixed;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        z-index: -1;
        background: linear-gradient(45deg, #f0f7ff, #ffffff);
        overflow: hidden;
    }

    .bg-animation::before {
        content: '';
        position: absolute;
        width: 200%;
        height: 200%;
        top: -50%;
        left: -50%;
        background: radial-gradient(circle, rgba(0, 102, 204, 0.05) 0%, transparent 70%);
        animation: rotate 30s linear infinite;
    }

    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
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

    /* ================= HERO SECTION ================= */
    .hero {
        height: 200px;
        background: var(--gradient-1);
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: white;
        padding: 20px;
        margin-top: 80px;
    }
    .hero h1 {
        font-size: 3.8rem;
        font-weight: 700;
        animation: fadeInDown 1s ease;
    }
    .hero p {
        margin-top: 10px;
        font-size: 1.2rem;
        animation: fadeInUp 1s ease;
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-40px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ================= GLASS CARDS SECTION ================= */
    .intro-section {
        padding: 60px 20px;
        text-align: center;
        max-width: 1200px;
        margin: 0 auto;
    }

    .intro-section h2 {
        font-size: 2.5rem;
        color: var(--primary);
        margin-bottom: 15px;
    }

    .intro-section p {
        width: 70%;
        margin: auto;
        font-size: 1.1rem;
        color: var(--text-light);
        line-height: 1.7;
    }

    .card-container {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 25px;
        margin-top: 60px;
    }

    .glass-card {
        width: 300px;
        padding: 25px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(15px);
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: var(--shadow-lg);
        text-align: center;
        transition: 0.3s;
        animation: fadeInUp 0.8s ease;
    }

    .glass-card:hover {
        transform: scale(1.05);
        box-shadow: var(--shadow-2xl);
    }

    .glass-card h3 {
        margin-top: 10px;
        font-size: 1.5rem;
        color: var(--primary);
    }

    .glass-card p {
        margin-top: 10px;
        font-size: 1rem;
        color: var(--text-light);
        line-height: 1.5;
    }

    /* ICONS */
    .icon {
        font-size: 3rem;
        color: var(--primary);
    }

    /* ================= HOSPITAL INFO SECTIONS ================= */
    .info-section {
        background: white;
        padding: 60px 20px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .info-section h2 {
        font-size: 2rem;
        color: var(--primary);
        text-align: center;
        margin-bottom: 20px;
    }
    .info-section p {
        width: 75%;
        margin: auto;
        font-size: 1.1rem;
        line-height: 1.7;
        color: var(--text-light);
        text-align: center;
    }

    /* ================= FOOTER ================= */
    footer {
        background: var(--gradient-1);
        color: white;
        padding: 50px 20px;
        margin-top: 50px;
    }

    .footer-grid {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
    }

    .footer-grid div {
        width: 22%;
        min-width: 200px;
    }

    footer h3, footer h4 {
        margin-bottom: 10px;
        font-size: 1.3rem;
        font-weight: 600;
    }

    footer ul {
        list-style: none;
    }

    footer ul li {
        margin-bottom: 8px;
        font-size: 0.95rem;
        opacity: 0.9;
    }

    .copyright {
        text-align: center;
        margin-top: 20px;
        opacity: 0.9;
        border-top: 1px solid rgba(255,255,255,0.3);
        padding-top: 15px;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
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

        .intro-section p, .info-section p {
            width: 95%;
        }
        .footer-grid div {
            width: 100%;
        }
    }

    @media(max-width: 768px) {
        .hero h1 {
            font-size: 2.8rem;
        }

        .mobile-nav {
            width: 100%;
            right: -100%;
        }
    }
</style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation"></div>

    <!-- ================= HEADER ================= -->
    <header>
        <nav class="navbar">
            <div class="logo-area">
                <img src="imgs/logo.JPG" alt="QuickCare Logo" class="logo-img">
                <span class="site-name">QuickCare</span>
            </div>
            
            <div class="nav-right-container">
                <div class="nav-links">
                    <a href="index.php">Home</a>
                    <a href="services.php">Services</a>
                    <a href="doctors.php">Doctors</a>
                    <a href="aboutus.php" class="active">About</a>
                    <a href="contactus.php">Contact</a>
                </div>
                
                <div class="auth-buttons">
                    <a href="login.php" class="btn-login">
                        Login
                    </a>
                    <a href="register.php" class="btn-register">
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

    <!-- ================= HERO SECTION ================= -->
    <section class="hero">
        <div>
            <h1>About Quick Care</h1>
            <p>Your Trusted Healthcare Partner</p>
        </div>
    </section>

    <!-- ================= INTRO ================= -->
    <section class="intro-section">
        <h2>Who We Are</h2>
        <p>
            Quick Care is a modern multi-speciality hospital offering advanced treatment,
            smart appointment booking, and compassionate patient support.  
            We believe in **quality, trust, and care** with world-class medical facilities.
        </p>

        <!-- GLASS EFFECT CARDS -->
        <div class="card-container">

            <div class="glass-card">
                <div class="icon">🏥</div>
                <h3>Our Vision</h3>
                <p>
                    To provide accessible, high-quality and affordable healthcare 
                    with modern technology and human touch.
                </p>
            </div>

            <div class="glass-card">
                <div class="icon">🤝</div>
                <h3>Responsibility</h3>
                <p>
                    We serve society with honesty, empathy, and commitment 
                    by supporting community health & well-being.
                </p>
            </div>

            <div class="glass-card">
                <div class="icon">👨‍⚕️</div>
                <h3>Expert Doctors</h3>
                <p>
                    A team of highly qualified doctors and specialists 
                    available 24×7 ensuring top-tier medical care.
                </p>
            </div>

        </div>
    </section>

    <!-- ================= HOSPITAL DETAILS ================= -->
    <section class="info-section">
        <h2>Infrastructure</h2>
        <p>
            Our hospital features modern ICUs, advanced diagnostic equipment, 
            specialized operation theaters, and comfortable patient rooms. 
            We maintain the highest standards of hygiene and safety to ensure 
            well-being of our patients and staff.
        </p>
    </section>

    <!-- ================= FOOTER ================= -->
    <footer>
        <div class="footer-grid">
            <div>
                <h3>QuickCare Hospital</h3>
                <p>Providing quality healthcare with compassion and excellence.</p>
            </div>
            <div>
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.html" style="color: white; text-decoration: none;">Home</a></li>
                    <li><a href="#" style="color: white; text-decoration: none;">Our Services</a></li>
                    <li><a href="#" style="color: white; text-decoration: none;">Find a Doctor</a></li>
                    <li><a href="#" style="color: white; text-decoration: none;">Patient Portal</a></li>
                </ul>
            </div>
            <div>
                <h4>Contact Info</h4>
                <ul>
                    <li>123 Healthcare Avenue</li>
                    <li>City, State 12345</li>
                    <li>Phone: (123) 456-7890</li>
                    <li>Email: info@quickcare.com</li>
                </ul>
            </div>
            <div>
                <h4>Follow Us</h4>
                <ul>
                    <li><a href="#" style="color: white; text-decoration: none;">Facebook</a></li>
                    <li><a href="#" style="color: white; text-decoration: none;">Twitter</a></li>
                    <li><a href="#" style="color: white; text-decoration: none;">Instagram</a></li>
                    <li><a href="#" style="color: white; text-decoration: none;">LinkedIn</a></li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            <p>&copy; <span id="year"></span> QuickCare Hospital. All Rights Reserved.</p>
        </div>
    </footer>

    <script>
        // Set current year
        document.getElementById('year').textContent = new Date().getFullYear();

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