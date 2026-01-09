<?php
include "config.php";
include "header.php";

 $q = "SELECT SPECIALISATION_ID, SPECIALISATION_NAME FROM specialisation_tbl";
 $res = mysqli_query($conn,$q);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Specialization</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Using same color variables as index.php */
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
            --primary: #0B5ED7;
            --accent: #00C2CB;
            --dark: #0f172a;
            --text: #334155;
            --bg: #f8fafc;
            --card: #ffffff;
            
            /* Header specific variables from index.php */
            --header-primary: #0066cc;
            --header-primary-dark: #0052a3;
            --header-primary-light: #e6f2ff;
            --header-secondary: #00a8cc;
            --header-accent: #00a86b;
            --header-warning: #ff6b6b;
            --header-dark: #1a3a5f;
            --header-light: #f8fafc;
            --header-white: #ffffff;
            --header-text: #2c5282;
            --header-text-light: #4a6fa5;
            --header-gradient-1: linear-gradient(135deg, #0066cc 0%, #00a8cc 100%);
            --header-gradient-2: linear-gradient(135deg, #00a8cc 0%, #00a86b 100%);
            --header-gradient-3: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
            --header-shadow-sm: 0 2px 4px rgba(0,0,0,0.06);
            --header-shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --header-shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --header-shadow-xl: 0 20px 25px rgba(0,0,0,0.1);
            --header-shadow-2xl: 0 25px 50px rgba(0,0,0,0.25);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f8ff 0%, #e6f0ff 100%);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding-top: 80px; /* Account for fixed header */
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .page-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23ffffff' fill-opacity='0.1' d='M0,224L48,213.3C96,203,192,181,288,181.3C384,181,480,203,576,224C672,245,768,267,864,261.3C960,256,1056,224,1152,213.3C1248,203,1344,213,1392,218.7L1440,224L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z'%3E%3C/path%3E%3C/svg%3E") no-repeat bottom;
            background-size: cover;
        }

        .page-header h1 {
            font-size: 2.8rem;
            margin-bottom: 1rem;
            position: relative;
            z-index: 2;
        }

        .page-header p {
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .specializations {
            padding: 4rem 0;
            flex-grow: 1;
            background-color: white;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            text-align: center;
            padding: 2.5rem 1.5rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 102, 204, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-blue), var(--secondary-blue));
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }

        .card-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .card-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: var(--light-blue);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .card-icon i {
            font-size: 2rem;
            color: var(--primary-blue);
        }

        .card:hover .card-icon {
            background: var(--primary-blue);
        }

        .card:hover .card-icon i {
            color: white;
        }

        .card h2 {
            font-size: 1.5rem;
            margin-bottom: 1.5rem;
            color: var(--text-dark);
            transition: color 0.3s ease;
        }

        .card:hover h2 {
            color: var(--primary-blue);
        }

        .card button {
            margin-top: 15px;
            padding: 12px 24px;
            background: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1rem;
            box-shadow: 0 4px 10px rgba(0, 102, 204, 0.2);
            width: 100%;
        }

        .card button:hover {
            background: var(--accent-blue);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 102, 204, 0.3);
        }

        /* Footer with Wave Effect - Updated to match aboutus.php color */
        footer {
            background: var(--header-gradient-1); /* Changed from var(--dark-blue) */
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
            text-decoration: none;
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

        /* Responsive Design */
        @media (max-width: 1200px) {
            .grid-container {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 992px) {
            .page-header h1 {
                font-size: 2.4rem;
            }
            
            .page-header p {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 768px) {
            .grid-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .page-header {
                padding: 3rem 0;
            }
            
            .page-header h1 {
                font-size: 2.2rem;
            }
            
            .specializations {
                padding: 3rem 0;
            }
        }

        @media (max-width: 576px) {
            .grid-container {
                grid-template-columns: 1fr;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .page-header p {
                font-size: 1rem;
            }
            
            .card {
                padding: 2rem 1rem;
            }
            
            .footer-content {
                grid-template-columns: 1fr;
            }
        }

        /* Animation for cards */
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

        .card {
            animation: fadeInUp 0.6s ease forwards;
        }

        .card:nth-child(1) { animation-delay: 0.1s; }
        .card:nth-child(2) { animation-delay: 0.2s; }
        .card:nth-child(3) { animation-delay: 0.3s; }
        .card:nth-child(4) { animation-delay: 0.4s; }
        .card:nth-child(5) { animation-delay: 0.5s; }
        .card:nth-child(6) { animation-delay: 0.6s; }
    </style>
</head>
<body>
    <div class="page-header">
        <div class="container">
            <h1>Choose Specialization</h1>
            <p>Choose from our trusted specialists to find the right doctor for your needs</p>
        </div>
    </div>

    <section class="specializations">
        <div class="container">
            <div class="grid-container">
                <?php 
                $icons = array(
                    'fa-heartbeat', 'fa-brain', 'fa-user-md', 'fa-bone', 
                    'fa-eye', 'fa-tooth', 'fa-baby', 'fa-lungs'
                );
                $iconIndex = 0;
                while($row = mysqli_fetch_assoc($res)){ 
                    $icon = isset($icons[$iconIndex]) ? $icons[$iconIndex] : 'fa-user-md';
                    $iconIndex++;
                ?>
                    <div class="card">
                        <div class="card-content">
                            <div class="card-icon">
                                <i class="fas <?php echo $icon; ?>"></i>
                            </div>
                            <h2><?php echo $row['SPECIALISATION_NAME']; ?></h2>
                            <a href="doctors.php?spec_id=<?php echo $row['SPECIALISATION_ID']; ?>">
                                <button>View Doctors</button>
                            </a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- Footer with Wave Effect - Updated to match aboutus.php -->
    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>QuickCare</h3>
                <p>Your trusted partner in healthcare. Book appointments with verified specialists quickly and easily.</p>
                <!-- Added Social Links -->
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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="appointment.php">Find Doctors</a></li>
                    <li><a href="appointment.php">Book Appointment</a></li>
                </ul>
            </div>
            <div class="footer-column">
                <h3>Contact Us</h3>
                <ul>
                    <li><a href="#"><i class="fas fa-map-marker-alt"></i> 123 Healthcare Ave, Medical City</a></li>
                    <li><a href="#"><i class="fas fa-phone"></i> 91+ 9632587418</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <span id="year"></span> QuickCare. All Rights Reserved. | <a href="#" style="color: rgba(255, 255, 255, 0.7);">Privacy Policy</a> | <a href="#" style="color: rgba(255, 255, 255, 0.7);">Terms of Service</a></p>
        </div>
    </footer>

    <script>
        // Set current year
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>