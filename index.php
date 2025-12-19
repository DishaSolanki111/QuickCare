<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickCare - Book Doctor Appointments</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1a73e8;
            --secondary-blue: #4285f4;
            --light-blue: #e8f0fe;
            --medium-blue: #8ab4f8;
            --dark-blue: #174ea6;
            --accent-blue: #0b57d0;
            --text-dark: #202124;
            --text-light: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            color: var(--text-dark);
            line-height: 1.6;
            background-color: white;
        }

        /* Header Styles */
        header {
            background-color: white;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
            transition: all 0.3s ease;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 5%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--primary-blue);
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: transform 0.3s ease;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
        }

        .nav-links {
            display: flex;
            list-style: none;
            align-items: center;
        }

        .nav-links li {
            margin-left: 2rem;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            transition: color 0.3s ease;
            position: relative;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: var(--primary-blue);
            transition: width 0.3s ease;
        }

        .nav-links a:hover {
            color: var(--primary-blue);
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .btn {
            padding: 0.5rem 1.2rem;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            border: none;
        }

        .btn-login {
            border: 1px solid var(--primary-blue);
            color: var(--primary-blue);
            background-color: white;
        }

        .btn-login:hover {
            background-color: var(--light-blue);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-register {
            background-color: var(--primary-blue);
            color: white;
            margin-left: 1rem;
        }

        .btn-register:hover {
            background-color: var(--dark-blue);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        /* Blue Section - Hero and Stats Only */
        .blue-section {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            position: relative;
            overflow: hidden;
        }

        /* Hero Section - Side by Side Layout with Professional Blue Background */
        .hero {
            display: flex;
            align-items: center;
            padding: 4rem 5%;
            min-height: 80vh;
            position: relative;
            z-index: 2;
        }

        .hero-content {
            flex: 1;
            max-width: 600px;
            padding-right: 3rem;
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 2.8rem;
            margin-bottom: 1.5rem;
            color: var(--text-light);
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2.5rem;
            color: rgba(255, 255, 255, 0.9);
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .btn-primary {
            background-color: white;
            color: var(--primary-blue);
            padding: 0.8rem 2rem;
            font-size: 1.1rem;
            border-radius: 50px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background-color: var(--light-blue);
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary {
            background-color: transparent;
            color: white;
            border: 2px solid white;
            padding: 0.8rem 2rem;
            font-size: 1.1rem;
            border-radius: 50px;
            font-weight: 600;
        }

        .btn-secondary:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }

        .stats {
            display: flex;
            gap: 2rem;
            margin-top: 2rem;
        }

        .stat-item {
            text-align: center;
            background-color: rgba(255, 255, 255, 0.15);
            padding: 1rem;
            border-radius: 10px;
            min-width: 120px;
            backdrop-filter: blur(5px);
        }

        .stat-number {
            font-size: 2.2rem;
            font-weight: bold;
            color: white;
            display: block;
            line-height: 1.2;
        }

        .stat-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
        }

        .hero-image {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding-left: 3rem;
            position: relative;
            z-index: 2;
        }

        .hero-image img {
            max-width: 100%;
            height: auto;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.5s ease;
        }

        .hero-image img:hover {
            transform: scale(1.03);
        }

        /* Wave at the bottom of blue section */
        .wave-container {
            position: relative;
            width: 100%;
            height: 100px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='1' d='M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,261.3C960,256,1056,224,1152,213.3C1248,203,1344,213,1392,218.7L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E") no-repeat bottom;
            background-size: cover;
        }

        /* Specialists Section - Now with White Background */
        .specialists {
            padding: 5rem 5%;
            background-color: white;
            position: relative;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        .section-subtitle {
            text-align: center;
            color: #5f6368;
            margin-bottom: 3rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }

        .specialist-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .specialist-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            text-align: center;
            padding: 2rem 1.5rem;
            border: 1px solid #e0e0e0;
        }

        .specialist-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
            border-color: var(--medium-blue);
        }

        .specialist-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background-color: var(--light-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .specialist-card:hover .specialist-icon {
            background-color: var(--primary-blue);
            color: white;
        }

        .specialist-icon i {
            font-size: 2rem;
            color: var(--primary-blue);
        }

        .specialist-card:hover .specialist-icon i {
            color: white;
        }

        .specialist-title {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .specialist-subtitle {
            color: #5f6368;
            margin-bottom: 1.5rem;
        }

        .specialist-link {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: all 0.3s ease;
        }

        .specialist-link:hover {
            color: var(--accent-blue);
        }

        .specialist-link i {
            margin-left: 0.5rem;
            transition: transform 0.3s ease;
        }

        .specialist-link:hover i {
            transform: translateX(5px);
        }

        /* Features Section - Now with White Background */
        .features {
            padding: 5rem 5%;
            background-color: white;
            position: relative;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            position: relative;
            z-index: 2;
        }

        .feature-card {
            background-color: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .feature-icon {
            font-size: 2.5rem;
            color: var(--primary-blue);
            margin-bottom: 1rem;
        }

        .feature-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }

        .feature-description {
            color: #5f6368;
        }

        /* Footer with Wave Effect */
        footer {
            background-color: var(--dark-blue);
            color: white;
            padding: 3rem 5%;
            position: relative;
        }

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
        }

        .social-links a:hover {
            background-color: var(--primary-blue);
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
        }

        /* Responsive Design - Adjusted to keep sections side by side */
        @media (max-width: 1200px) {
            .hero h1 {
                font-size: 2.4rem;
            }
        }

        @media (max-width: 992px) {
            .hero {
                padding: 3rem 5%;
            }
            
            .hero-content {
                padding-right: 1.5rem;
            }
            
            .hero-image {
                padding-left: 1.5rem;
            }
            
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1.1rem;
            }
            
            .stats {
                gap: 1rem;
            }
            
            .stat-item {
                min-width: 100px;
                padding: 0.8rem;
            }
            
            .stat-number {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            
            /* Mobile menu toggle would go here */
            
            .hero {
                flex-direction: column;
                padding: 2rem 5%;
                align-items: flex-start;
            }
            
            .hero-content {
                padding-right: 0;
                margin-bottom: 2rem;
                max-width: 100%;
            }
            
            .hero-image {
                padding-left: 0;
                width: 100%;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero-buttons {
                flex-direction: column;
                align-items: center;
                width: 100%;
            }
            
            .stats {
                justify-content: space-between;
                width: 100%;
            }
            
            .stat-item {
                min-width: auto;
                flex: 1;
            }
            
            .specialist-cards {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
            
            .logo-img {
                height: 30px;
            }
        }

        /* Animation on scroll */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav>
            <a href="#" class="logo">
                <img src="https://z-cdn-media.chatglm.cn/files/2a7da7c7-2186-4874-8640-1ee2b6be2bd2.JPG?auth_key=1866143499-d2d60416c37c474c83c4ba00c8faa76c-0-1c4ea7f393c80b02243d32ae07ffe413" alt="QuickCare Logo" class="logo-img">
                QuickCare
            </a>
            <ul class="nav-links">
                <li><a href="#">Home</a></li>
                <li><a href="#">Schedules</a></li>
                <li><a href="#">Doctors</a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">Contact</a></li>
                <li><a href="#" class="btn btn-login">Login</a></li>
                <li><a href="#" class="btn btn-register">Register</a></li>
            </ul>
        </nav>
    </header>

    <!-- Blue Section - Hero and Stats Only -->
    <div class="blue-section">
        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content fade-in">
                <h1>Book Doctor Appointments Without Waiting</h1>
                <p>Choose verified specialists, pick your slot, and get treated faster with QuickCare.</p>
                <div class="hero-buttons">
                    <a href="#" class="btn btn-primary">Book Appointment</a>
                    <a href="#" class="btn btn-secondary">Watch Demo</a>
                </div>
                <div class="stats">
                    <div class="stat-item">
                        <span class="stat-number">550+</span>
                        <span class="stat-label">Doctors</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">750+</span>
                        <span class="stat-label">Patients</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">25+</span>
                        <span class="stat-label">Years Experience</span>
                    </div>
                </div>
            </div>
            <div class="hero-image fade-in">
                 <img src="https://z-cdn-media.chatglm.cn/files/00dd8042-b057-44ce-8651-b48c5a1b0983.png?auth_key=1866141090-a165a7d6b546484fa03a28536d384ce7-0-6066138938f7c1173744e069e3e46f6f" alt="Doctor consultation"> -->
            </div>
        </section>

        <!-- Wave at the bottom of blue section -->
        <div class="wave-container"></div>
    </div>

    <!-- Popular Specialists Section with White Background -->
    <section class="specialists">
        <div class="container">
            <h2 class="section-title fade-in">Popular Specialists</h2>
            <p class="section-subtitle fade-in">Find right specialist for your health needs</p>
            <div class="specialist-cards">
                <div class="specialist-card fade-in">
                    <div class="specialist-icon">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                    <h3 class="specialist-title">Cardiologist</h3>
                    <p class="specialist-subtitle">Heart Specialist</p>
                    <a href="#" class="specialist-link">Book Now <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="specialist-card fade-in">
                    <div class="specialist-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3 class="specialist-title">Neurologist</h3>
                    <p class="specialist-subtitle">Brain & Nerves</p>
                    <a href="#" class="specialist-link">Book Now <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="specialist-card fade-in">
                    <div class="specialist-icon">
                        <i class="fas fa-hand-holding-medical"></i>
                    </div>
                    <h3 class="specialist-title">Dermatologist</h3>
                    <p class="specialist-subtitle">Skin Care</p>
                    <a href="#" class="specialist-link">Book Now <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="specialist-card fade-in">
                    <div class="specialist-icon">
                        <i class="fas fa-bone"></i>
                    </div>
                    <h3 class="specialist-title">Orthopedic</h3>
                    <p class="specialist-subtitle">Bones & Joints</p>
                    <a href="#" class="specialist-link">Book Now <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section with White Background -->
    <section class="features">
        <div class="container">
            <h2 class="section-title fade-in">Why Choose QuickCare?</h2>
            <p class="section-subtitle fade-in">Experience healthcare made simple with our innovative features</p>
            <div class="features-grid">
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="feature-title">Easy Booking</h3>
                    <p class="feature-description">Book appointments with just a few clicks. No waiting, no hassle.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-user-md"></i>
                    </div>
                    <h3 class="feature-title">Verified Doctors</h3>
                    <p class="feature-description">All our doctors are verified professionals with proven expertise.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="feature-title">Flexible Scheduling</h3>
                    <p class="feature-description">Choose from multiple time slots that fit your schedule.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer with Wave Effect -->
    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>QuickCare</h3>
                <p>Your trusted partner in healthcare. Book appointments with verified specialists quickly and easily.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="footer-column">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Find Doctors</a></li>
                    <li><a href="#">Book Appointment</a></li>
                    <li><a href="#">Health Blog</a></li>
                    <li><a href="#">FAQs</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Services</h3>
                <ul>
                    <li><a href="#">Online Consultation</a></li>
                    <li><a href="#">Emergency Care</a></li>
                    <li><a href="#">Health Checkup</a></li>
                    <li><a href="#">Lab Tests</a></li>
                    <li><a href="#">Medicine Delivery</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Contact Us</h3>
                <ul>
                    <li><a href="#"><i class="fas fa-map-marker-alt"></i> 123 Healthcare Ave, Medical City</a></li>
                    <li><a href="#"><i class="fas fa-phone"></i> +1 (555) 123-4567</a></li>
                    <li><a href="#"><i class="fas fa-envelope"></i> info@quickcare.com</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2023 QuickCare. All Rights Reserved. | <a href="#" style="color: rgba(255, 255, 255, 0.7);">Privacy Policy</a> | <a href="#" style="color: rgba(255, 255, 255, 0.7);">Terms of Service</a></p>
        </div>
    </footer>

    <script>
        // Fade in animation on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const fadeElements = document.querySelectorAll('.fade-in');
            
            function checkFade() {
                fadeElements.forEach(element => {
                    const elementTop = element.getBoundingClientRect().top;
                    const elementVisible = 150;
                    
                    if (elementTop < window.innerHeight - elementVisible) {
                        element.classList.add('visible');
                    }
                });
            }
            
            // Initial check
            checkFade();
            
            // Check on scroll
            window.addEventListener('scroll', checkFade);
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>