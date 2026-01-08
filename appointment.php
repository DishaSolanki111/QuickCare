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
    <style>
        :root {
            --primary: #0066cc;
            --primary-dark: #004a99;
            --primary-light: #e6f0ff;
            --secondary: #0099ff;
            --accent: #0052cc;
            --light-blue: #f0f7ff;
            --medium-blue: #d4e6ff;
            --dark-blue: #003366;
            --text-dark: #1a3a5f;
            --text-light: #ffffff;
            --shadow: 0 4px 15px rgba(0, 102, 204, 0.1);
            --shadow-hover: 0 10px 25px rgba(0, 102, 204, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f8ff 0%, #e6f0ff 100%);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .page-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 4rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-top: 50px;
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
        }

        .grid-container {
            display: flex;
            justify-content: center;
            align-items: stretch;
            flex-wrap: wrap;
            gap: 30px;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            flex: 1 1 calc(25% - 30px);
            min-width: 250px;
            max-width: 280px;
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
            background: linear-gradient(90deg, var(--primary), var(--secondary));
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
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .card-icon i {
            font-size: 2rem;
            color: var(--primary);
        }

        .card:hover .card-icon {
            background: var(--primary);
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
            color: var(--primary);
        }

        .card button {
            margin-top: 15px;
            padding: 12px 24px;
            background: var(--primary);
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
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 102, 204, 0.3);
        }

        footer {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--primary) 100%);
            color: white;
            padding: 3rem 0;
            text-align: center;
            position: relative;
        }

        footer::before {
            content: "";
            position: absolute;
            top: -50px;
            left: 0;
            width: 100%;
            height: 50px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23f5f8ff' fill-opacity='1' d='M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,112C1248,107,1344,117,1392,122.7L1440,128L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z'%3E%3C/path%3E%3C/svg%3E") no-repeat top;
            background-size: cover;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }

        .footer-content p {
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-link {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
            font-size: 1.2rem;
        }

        .social-link:hover {
            background: var(--secondary);
            transform: translateY(-5px);
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .card {
                flex: 1 1 calc(33.333% - 30px);
            }
        }

        @media (max-width: 900px) {
            .card {
                flex: 1 1 calc(50% - 30px);
            }
        }

        @media (max-width: 600px) {
            .page-header h1 {
                font-size: 2.2rem;
            }
            
            .page-header p {
                font-size: 1rem;
            }
            
            .card {
                flex: 1 1 100%;
                max-width: 100%;
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
            <p>Select from our wide range of medical specializations to find the right doctor for your needs</p>
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

    <footer>
        <div class="footer-content">
            <p>&copy; <span id="year"></span> QuickCare — Revolutionizing Healthcare Access</p>
            <div class="social-links">
                <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>