<?php
session_start(); // Start session at the very top
include 'header.php'; // Include the header
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>About Us | Quick Care Hospital</title>
<!-- Added FontAwesome because the new footer format uses icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        /* Variable from index.php needed for the new footer format */
        --medium-blue: #8ab4f8; 
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
        /* Kept margin-top for header alignment */
      

    }
    .hero h1 {
        font-size: 3.8rem;
        font-weight: 700;
    }
    .hero p {
        margin-top: 10px;
        font-size: 1.2rem;
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
        /* Reduced top padding from 60px to 20px to remove space */
        padding: 20px 20px 60px 20px;
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

    /* ================= FOOTER (NEW FORMAT FROM INDEX.PHP) ================= */
    footer {
        /* ORIGINAL COLOR KEPT AS REQUESTED */
        background: var(--gradient-1); 
        color: white;
        padding: 3rem 5%;
        margin-top: 50px;
        position: relative;
    }

    /* Wave effect from index.php format */
    footer::before {
        content: "";
        position: absolute;
        top: -100px;
        left: 0;
        width: 100%;
        height: 100px;
        background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='1' d='M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,112C1248,107,1344,117,1392,122.7L1440,128L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z'%3E%3C/path%3E%3C/svg%3E") no-repeat bottom;
        background-size: cover;
    }

    .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }

    .footer-column h3 {
        font-size: 1.3rem;
        margin-bottom: 1.5rem;
        position: relative;
        padding-bottom: 0.5rem;
    }

    /* Underline style from index.php */
    .footer-column h3::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 3px;
        background-color: var(--medium-blue);
    }

    .footer-column ul {
        list-style: none;
    }

    .footer-column ul li {
        margin-bottom: 0.8rem;
    }

    .footer-column ul li a {
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-block;
    }

    /* Hover effect from index.php */
    .footer-column ul li a:hover {
        color: white;
        transform: translateX(5px);
    }

    .social-links {
        display: flex;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .social-links a {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
        border-radius: 50%;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .social-links a:hover {
        background-color: var(--primary);
        transform: translateY(-3px);
    }

    .footer-bottom {
        text-align: center;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.7);
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }

    /* RESPONSIVE */
    @media(max-width: 992px) {
        .intro-section p, .info-section p {
            width: 95%;
        }
    }

    @media(max-width: 768px) {
        .hero h1 {
            font-size: 2.8rem;
        }
    }
</style>
</head>
<body>
    <!-- Animated Background -->
    <div class="bg-animation"></div>
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
            We maintain highest standards of hygiene and safety to ensure 
            well-being of our patients and staff.
        </p>
    </section>

    <!-- ================= FOOTER (NEW FORMAT STARTS HERE) ================= -->
    <footer id="footer-section">
        <div class="footer-content">
            <div class="footer-column">
                <h3>QuickCare</h3>
                <p>Your trusted partner in healthcare. Book appointments with verified specialists quickly and easily.</p>
             
            </div>
            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="doctors.php">Find Doctors</a></li>
                    <li><a href="appointment.php">Book Appointment</a></li>
                  
                </ul>
            </div>
        </div>
    </footer>

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