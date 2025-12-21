<?php include 'header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Appointment – Contact</title>
    
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
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
            background: var(--light);
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

        
        /* PAGE HEADER */
        .page-header {
            background: var(--gradient-1);
            padding: 80px 0 60px;
            text-align: center;
            color: white;
            margin-top: 80px;
        }

        .page-header h1 {
            margin: 0;
            font-size: 45px;
            font-weight: 700;
        }

        .page-header p {
            margin-top: 10px;
            font-size: 16px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        /* MAIN SECTION */
        .contact-section {
            width: 90%;
            max-width: 1300px;
            margin: 40px auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        /* LEFT CONTACT CARDS */
        .contact-cards {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--shadow-md);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .card-icon {
            font-size: 40px;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .card h3 {
            margin-bottom: 8px;
            font-size: 18px;
            color: var(--dark);
        }

        .card p {
            margin: 0;
            color: var(--text-light);
        }

        /* RIGHT FORM SIDE */
        .contact-form {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: var(--shadow-md);
        }

        .contact-form h2 {
            margin: 0;
            font-size: 28px;
            color: var(--dark);
        }

        .contact-form p {
            margin: 5px 0 20px;
            color: var(--text-light);
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 12px;
            margin-top: 12px;
            border-radius: 8px;
            border: 1px solid #c7d2fe;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        
        .contact-form input:focus,
        .contact-form textarea:focus {
            outline: none;
            border-color: var(--primary);
        }

        .contact-form textarea {
            resize: none;
        }

        .contact-form button {
            width: 100%;
            padding: 14px;
            margin-top: 20px;
            border: none;
            background: var(--primary);
            color: white;
            font-size: 18px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .contact-form button:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* FOOTER */
        footer {
            background: var(--dark);
            color: white;
            padding: 3rem 5%;
            text-align: center;
            margin-top: 60px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-link:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 992px) {
            .nav-right-container {
                display: none;
            }

            .mobile-menu-btn {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .contact-section {
                grid-template-columns: 1fr;
            }

            .contact-cards {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 36px;
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

    <!-- NAVBAR - Right-Aligned Navigation -->
    
    
    <!-- Page Header -->
    <div class="page-header">
        <h1>Contact Us</h1>
        <p>Reach out for appointments, inquiries, and medical support.</p>
    </div>

    <!-- Main Section -->
    <div class="contact-section">
        <!-- LEFT CONTACT INFO -->
        <div class="contact-cards">
            <div class="card">
                <div class="card-icon">📞</div>
                <h3>Phone</h3>
                <p>+1 234 567 890</p>
            </div>

            <div class="card">
                <div class="card-icon">💬</div>
                <h3>Whatsapp</h3>
                <p>+1 987 654 321</p>
            </div>

            <div class="card">
                <div class="card-icon">📧</div>
                <h3>Email</h3>
                <p>support@Quickcare.com</p>
            </div>

            <div class="card">
                <div class="card-icon">💠</div>
                <h3>Customer Care</h3>
                <p>24/7 Support Available</p>
            </div>
        </div>

        <!-- RIGHT FORM -->
        <div class="contact-form">
            <h2>Get In Touch</h2>
            <p>Fill out the form below to book your appointment.</p>

            <form>
                <input type="text" placeholder="Your Name" required>
                <input type="email" placeholder="Your Email" required>
                <input type="text" placeholder="Phone Number" required>
                <input type="text" placeholder="Subject">
                <textarea rows="5" placeholder="Message"></textarea>

                <button type="submit">Send Now</button>
            </form>
        </div>
    </div>

    <!-- FOOTER -->
    <footer>
        <div class="footer-content">
            <p>&copy; <span id="year"></span> QuickCare — Revolutionizing Healthcare Access</p>
            <div class="social-links">
                <a href="#" class="social-link">
                    <span>f</span>
                </a>
                <a href="#" class="social-link">
                    <span>𝕏</span>
                </a>
                <a href="#" class="social-link">
                    <span>in</span>
                </a>
                <a href="#" class="social-link">
                    <span>📷</span>
                </a>
            </div>
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

        // Form submission
        const form = document.querySelector('.contact-form form');
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Thank you for your message! We will get back to you soon.');
            form.reset();
        });
    </script>
</body>
</html>