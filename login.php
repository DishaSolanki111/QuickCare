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

        /* NAVBAR - Right-Aligned Navigation */
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

        /* Search Bar - Simplified */
        .search-container {
            position: relative;
            margin: 0 1rem;
        }

        .search-input {
            padding: 0.6rem 2.5rem 0.6rem 1rem;
            border: 2px solid #e1e5e9;
            border-radius: 20px;
            background: white;
            color: var(--text);
            font-size: 0.9rem;
            width: 180px;
            transition: all 0.3s ease;
            outline: none;
        }

        .search-input:focus {
            width: 220px;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
        }

        .search-btn {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--primary);
            border: none;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.8rem;
        }

        .search-btn:hover {
            background: var(--primary-dark);
        }

        /* Auth Buttons - More Simple */
        .auth-buttons {
            display: flex;
            gap: 0.8rem;
        }

        .btn-register {
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
            background: var(--primary);
            color: white;
        }

        .btn-register:hover {
            background: var(--primary-dark);
        }

        /* Mobile Menu Button - Simplified */
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

        /* Mobile Navigation - Simplified */
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
            color: var(--text);
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
            height: 350px;
            overflow: hidden;
        }

        .form {
            width: 450px;
            height: 350px;
            background: white;
            padding: 30px;
            border-radius: 15px;
            position: absolute;
            left: 500px;
            transition: 0.4s;
            box-shadow: var(--shadow-lg);
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
                height: 350px;
            }

            .form {
                width: 100%;
                left: 500px;
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
    </style>
</head>
<body>

<header>
    <nav class="navbar">
        <div class="logo-area">
            <img src="./imgs/logo.JPG" alt="QuickCare Logo" class="logo-img">
            <span class="site-name">QuickCare</span>
        </div>
        
        <div class="nav-right-container">
            <div class="nav-links">
                <a href="index.html">Home</a>
                <a href="services.html">Services</a>
                <a href="doctors.html">Doctors</a>
                <a href="aboutus.html">About</a>
                <a href="contactus.html">Contact</a>
            </div>
            
            <div class="auth-buttons">
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
        <a href="index.html">Home</a>
        <a href="services.html">Services</a>
        <a href="doctors.html">Doctors</a>
        <a href="aboutus.html">About Us</a>
        <a href="contactus.html">Contact</a>
        <a href="register.php">Register</a>
    </div>
</div>

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
            <form class="form" id="patient" style="left:0;">
                <h3>Patient Login</h3>
                <input type="email" placeholder="Email Address">
                <input type="password" placeholder="Password">
                <button>Login</button>
                <a href="register.php" class="small">Not registered yet? Create an account</a>
            </form>

            <form class="form" id="doctor">
                <h3>Doctor Login</h3>
                <input type="email" placeholder="Email Address">
                <input type="password" placeholder="Password">
                <button>Login</button>
                <div class="small">Ask Receptionist to register you.</div>
            </form>

            <form class="form" id="receptionist">
                <h3>Receptionist Login</h3>
                <input type="email" placeholder="Email Address">
                <input type="password" placeholder="Password">
                <button>Login</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Tab switching functionality
    let tabs = document.querySelectorAll(".tab");
    let forms = document.querySelectorAll(".form");

    tabs.forEach(tab => {
        tab.addEventListener("click", () => {
            document.querySelector(".tab.active").classList.remove("active");
            tab.classList.add("active");

            let target = tab.dataset.target;

            forms.forEach(form => {
                form.style.left = (form.id === target) ? "0" : "500px";
            });
        });
    });

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

    // Active link switching
    document.querySelectorAll('.nav-links a, .mobile-nav-links a').forEach(link => {
        link.addEventListener('click', function(e) {
            // Remove active class from all links
            document.querySelectorAll('.nav-links a, .mobile-nav-links a').forEach(l => {
                l.classList.remove('active');
            });
            // Add active class to clicked link
            this.classList.add('active');
        });
    });
</script>

</body>
</html>