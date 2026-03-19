<?php
session_start();
include "config.php";
include "header.php";
// Accept specialization from POST (schedule.php) or GET; if none, show all doctors
$spec_id = isset($_POST['spec_id']) ? intval($_POST['spec_id']) : (isset($_GET['spec_id']) ? intval($_GET['spec_id']) : 0);

if ($spec_id > 0) {
    $q = "SELECT DOCTOR_ID, FIRST_NAME, LAST_NAME, PROFILE_IMAGE, SPECIALISATION_ID 
        FROM doctor_tbl 
        WHERE SPECIALISATION_ID = $spec_id AND STATUS = 'approved'";
} else {
    $q = "SELECT DOCTOR_ID, FIRST_NAME, LAST_NAME, PROFILE_IMAGE, SPECIALISATION_ID 
        FROM doctor_tbl 
        WHERE STATUS = 'approved'";
}

 $res = mysqli_query($conn, $q);

// Fetch specialization name
$specialization_name = 'All Specializations';
if ($spec_id > 0) {
    $spec_query = mysqli_query($conn, "SELECT SPECIALISATION_NAME FROM specialisation_tbl WHERE SPECIALISATION_ID = $spec_id");
    $spec_data = mysqli_fetch_assoc($spec_query);
    if (!empty($spec_data['SPECIALISATION_NAME'])) {
        $specialization_name = $spec_data['SPECIALISATION_NAME'];
    } else {
        $specialization_name = 'Specialist';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a3a5f;
            --secondary-color: #3498db;
            --accent-color: #2ecc71;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #17a2b8;
            --dark-blue: #072D44;
            --mid-blue: #064469;
            --soft-blue: #5790AB;
            --light-blue: #9CCDD8;
            --gray-blue: #D0D7E1;
            --primary-dark: #0f2640;
            --medium-blue: #4f8fb5;
            --white: #ffffff;
            --header-gradient-1: linear-gradient(135deg, #0066cc 0%, #00a8cc 100%);
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
            height: 200px; /* Match aboutus.php hero height */
            padding: 20px; /* Match vertical spacing from aboutus.php */
            text-align: center;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
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

        .doctors-section {
            padding: 1.25rem 0 4rem 0;
            flex-grow: 1;
        }
        
        .back-bar {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid var(--primary);
            color: var(--primary);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.2s ease;
        }

        .back-link i {
            font-size: 0.95rem;
        }

        .back-link:hover {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 4px 10px rgba(0, 102, 204, 0.35);
            transform: translateY(-1px);
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
            width: 100%;
            margin: 0 auto;
        }
        
        .card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            text-align: center;
            padding: 1.25rem;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 102, 204, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .doctor-image-container {
            position: relative;
            margin-bottom: 1.1rem;
        }
        
        .doctor-image {
            width: 120px;
            height: 120px;
            border-radius: 8px;
            object-fit: cover;
            object-position: top center;
            border: 3px solid var(--light-blue);
            transition: all 0.3s ease;
        }

        .card:hover .doctor-image {
            border-color: var(--primary);
            transform: scale(1.05);
        }

        .card h3 {
            font-size: 1.3rem;
            margin-bottom: 0.4rem;
            color: var(--text-dark);
        }
        
        .card-title {
            color: var(--primary);
            font-size: 0.95rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .card-rating {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
            color: #ffc107;
        }

        .card-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            margin-top: auto;
        }

        .card-actions form {
            flex: 1;
            display: flex;
            min-width: 0;
        }

        .card-actions button,
        .card-actions a {
            flex: 1;
            width: 100%;
            padding: 8px 0;
            border: none;
            border-radius: 7px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0, 102, 204, 0.3);
        }

        .btn-secondary {
            background: var(--light-blue);
            color: var(--primary);
        }

        .btn-secondary:hover {
            background: var(--medium-blue);
            transform: translateY(-3px);
        }

        /* Footer with Wave Effect */
        footer {
            background: var(--header-gradient-1);
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
            background-color: var(--soft-blue);
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

        .footer-bottom {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2.2rem;
            }
            
            .page-header p {
                font-size: 1rem;
            }
            
            .grid-container {
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 20px;
            }
            
            .card {
                padding: 1.5rem;
            }
            
            .doctor-image {
                width: 120px;
                height: 120px;
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
            <h1>Our Doctors</h1>
            <p>Meet our team of experienced medical professionals dedicated to your health</p>
        </div>
    </div>

    <section class="doctors-section">
        <div class="container">
            <div class="back-bar">
                <a href="appointment.php" class="back-link">
                    <i class="fas fa-arrow-left"></i>
                    Back
                </a>
            </div>
            <div class="grid-container">
                <?php while($row = mysqli_fetch_assoc($res)){ 
                    $img = !empty($row['PROFILE_IMAGE']) 
                        ? $row['PROFILE_IMAGE'] 
                        : 'imgs/default.jpg';
                ?>
                    <div class="card">
                        <div class="doctor-image-container">
                            <img src="<?php echo htmlspecialchars($img); ?>" alt="Doctor" class="doctor-image">
                        </div>
                        <h3><?php echo "Dr. ".$row['FIRST_NAME']." ".$row['LAST_NAME']; ?></h3>
                        <div class="card-title"><?php echo htmlspecialchars($specialization_name); ?></div>
                        <div class="card-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <div class="card-actions">
                            <form method="POST" action="d_profile.php" style="display:inline">
                                <input type="hidden" name="id" value="<?php echo $row['DOCTOR_ID']; ?>">
                                <button type="submit" class="btn-secondary"><i class="fas fa-user"></i> View Profile</button>
                            </form>
                            <form method="POST" action="book_appointment_date.php" style="display:inline">
                                <input type="hidden" name="doctor_id" value="<?php echo $row['DOCTOR_ID']; ?>">
                                <button type="submit" class="btn-primary"><i class="fas fa-calendar-check"></i> Book Now</button>
                            </form>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- Footer with Wave Effect -->
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
            <div class="footer-column">
                <h3>Contact Us</h3>
                <p><a href="mailto:quickcare012@gmail.com" style="color: rgba(255,255,255,0.9); text-decoration: none;">quickcare012@gmail.com</a></p>
            </div>
        </div>
    </footer>
</body>
</html>