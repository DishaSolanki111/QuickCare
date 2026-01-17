<?php
include "config.php";
include "header.php";

 $spec_id = intval($_GET['spec_id']);

 $q = "SELECT DOCTOR_ID, FIRST_NAME, LAST_NAME, PROFILE_IMAGE, SPECIALISATION_ID 
     FROM doctor_tbl 
     WHERE SPECIALISATION_ID = $spec_id";

 $res = mysqli_query($conn, $q);

// Fetch specializations for the modal
 $specializations_query = mysqli_query($conn, "
    SELECT * FROM specialisation_tbl
    ORDER BY SPECIALISATION_NAME
");

// Fetch specialization name
 $spec_query = mysqli_query($conn, "SELECT SPECIALISATION_NAME FROM specialisation_tbl WHERE SPECIALISATION_ID = $spec_id");
 $spec_data = mysqli_fetch_assoc($spec_query);
 $specialization_name = $spec_data['SPECIALISATION_NAME'] ?? 'Specialist';

// Fetch doctors for booking modal
 $doctors_query = mysqli_query($conn, "
    SELECT d.*, s.SPECIALISATION_NAME 
    FROM doctor_tbl d
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME
");
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
            --white: #ffffff;
            --card-bg: #F6F9FB;
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
            --gradient-1: linear-gradient(135deg, #0066cc 0%, #00a8cc 100%);
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
            padding: 7rem 0;
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

        .doctors-section {
            padding: 4rem 0;
            flex-grow: 1;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            width: 100%;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow);
            text-align: center;
            padding: 2rem;
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
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-hover);
        }

        .doctor-image-container {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .doctor-image {
            width: 140px;
            height: 140px;
            border-radius: 8px;
            object-fit: cover;
            border: 5px solid var(--light-blue);
            transition: all 0.3s ease;
        }

        .card:hover .doctor-image {
            border-color: var(--primary);
            transform: scale(1.05);
        }

        .card h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--text-dark);
        }

        .card-title {
            color: var(--primary);
            font-size: 1rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .card-rating {
            display: flex;
            justify-content: center;
            margin-bottom: 1.5rem;
            color: #ffc107;
        }

        .card-actions {
            display: flex;
            gap: 10px;
            width: 100%;
            margin-top: auto;
        }

        .card-actions button, .card-actions a {
            flex: 1;
            padding: 12px 0;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            font-size: 0.9rem;
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

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 3% auto;
            padding: 20px;
            border: none;
            width: 90%;
            max-width: 800px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            max-height: 90vh;
            overflow-y: auto;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-color);
        }

        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-success {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-success:hover {
            background-color: #27ae60;
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #ddd;
            margin: 0 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .step.active {
            background-color: var(--secondary-color);
        }

        .step.completed {
            background-color: var(--accent-color);
        }

        .step-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .step-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .doctor-info {
            display: flex;
            align-items: center;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .doctor-info:hover {
            background-color: #f8f9fa;
            border-color: var(--secondary-color);
        }

        .doctor-info.selected {
            background-color: rgba(52, 152, 219, 0.1);
            border-color: var(--secondary-color);
        }

        .doctor-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
            font-size: 18px;
        }

        .doctor-details {
            flex: 1;
        }

        .doctor-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--primary-color);
        }

        .doctor-specialization {
            font-size: 14px;
            color: #666;
        }

        /* Calendar Styles */
        .calendar-container {
            margin-top: 10px;
            position: relative;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .calendar-nav {
            background: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .calendar-nav:hover {
            background: var(--primary-color);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
        }

        .calendar-day-header {
            text-align: center;
            font-weight: bold;
            padding: 5px;
            color: var(--dark-color);
        }

        .calendar-day {
            text-align: center;
            padding: 10px 5px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .calendar-day.available {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--accent-color);
            font-weight: 500;
        }

        .calendar-day.available:hover {
            background-color: var(--accent-color);
            color: white;
        }

        .calendar-day.selected {
            background-color: var(--secondary-color);
            color: white;
            font-weight: bold;
        }

        .calendar-day.disabled {
            color: #ccc;
            cursor: not-allowed;
        }

        .calendar-day.other-month {
            color: #eee;
        }

        /* Time Slots Styles */
        .time-slots-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .time-slot {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .time-slot.available {
            background-color: rgba(46, 204, 113, 0.1);
            border-color: var(--accent-color);
            color: var(--accent-color);
        }

        .time-slot.available:hover {
            background-color: var(--accent-color);
            color: white;
        }

        .time-slot.selected {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
            font-weight: bold;
        }

        .time-slot.disabled {
            background-color: #f8f9fa;
            color: #ccc;
            cursor: not-allowed;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        .loading i {
            font-size: 24px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Login Form Styles */
        .login-form {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }

        .login-form h3 {
            text-align: center;
            margin-bottom: 20px;
            color: var(--primary-color);
        }

        .login-form .form-group {
            margin-bottom: 15px;
        }

        .login-form .form-control {
            padding: 12px;
        }

        .login-form .btn {
            width: 100%;
            padding: 12px;
        }

        .login-form .alert {
            margin-bottom: 15px;
        }

        .login-form p {
            text-align: center;
            margin-top: 15px;
        }

        .login-form a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .login-form a:hover {
            text-decoration: underline;
        }

        /* Appointment Confirmation Styles */
        .confirmation-container {
            text-align: center;
            padding: 20px;
        }

        .confirmation-icon {
            font-size: 4rem;
            color: var(--accent-color);
            margin-bottom: 20px;
        }

        .confirmation-title {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .confirmation-message {
            color: var(--text-dark);
            margin-bottom: 20px;
        }

        .appointment-details {
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: left;
        }

        .appointment-details h4 {
            color: var(--primary-color);
            margin-bottom: 15px;
            text-align: center;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .detail-label {
            font-weight: 600;
            color: var(--dark-color);
        }

        .detail-value {
            color: var(--primary-color);
        }

        /* Payment Processing Styles */
.payment-processing-container {
    max-width: 500px;
    margin: 0 auto;
    padding: 20px;
    text-align: center;
}

.payment-icon {
    font-size: 3rem;
    color: var(--primary-color);
    margin-bottom: 20px;
}

.payment-title {
    font-size: 1.5rem;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.payment-message {
    color: var(--text-dark);
    margin-bottom: 25px;
}

.payment-form {
    background-color: #f9f9f9;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    text-align: left;
}

.appointment-summary {
    background-color: var(--light-blue);
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 20px;
}

.appointment-summary h4 {
    color: var(--dark-blue);
    margin-bottom: 10px;
    text-align: center;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px solid #eee;
}

.summary-row:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.summary-row.total {
    font-weight: bold;
    color: var(--success-color);
    font-size: 1.1rem;
}

        /* Footer with Wave Effect */
        footer {
            background: var(--gradient-1);
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

        .footer-bottom {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .time-slots-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

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
            
            .time-slots-container {
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
            <h1>Our Doctors</h1>
            <p>Meet our team of experienced medical professionals dedicated to your health</p>
        </div>
    </div>

    <section class="doctors-section">
        <div class="container">
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
                        <div class="card-title">Specialist</div>
                        <div class="card-rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <div class="card-actions">
                            <a href="doctor_profile.php?id=<?php echo $row['DOCTOR_ID']; ?>" class="btn-secondary">
                                <i class="fas fa-user"></i> View Profile
                            </a>
                            <button class="btn-primary" onclick="openBookingModal(<?php echo $row['DOCTOR_ID']; ?>, '<?php echo $row['FIRST_NAME'] . ' ' . $row['LAST_NAME']; ?>')">
                                <i class="fas fa-calendar-check"></i> Book Now
                            </button>
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
                <ul>
                    <li><a href="#"><i class="fas fa-map-marker-alt"></i> 123 Healthcare Ave, Medical City</a></li>
                    <li><a href="#"><i class="fas fa-phone"></i> 91+ 9632587418</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2023 QuickCare. All Rights Reserved. | <a href="#" style="color: rgba(255, 255, 255, 0.7);">Privacy Policy</a> | <a href="#" style="color: rgba(255, 255, 255, 0.7);">Terms of Service</a></p>
        </div>
    </footer>

    <!-- Booking Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeBookingModal()">&times;</span>
            <h2>Book New Appointment</h2>
            
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="step1">1</div>
                <div class="step" id="step2">2</div>
                <div class="step" id="step3">3</div>
                <div class="step" id="step4">4</div>
                <div class="step" id="step5">5</div>
                <div class="step" id="step6">6</div>
            </div>
            
            <form method="POST" action="payment.php" id="appointmentForm">
                <!-- Step 1: Select Doctor -->
                <div class="step-content active" id="step1Content">
                    <div class="form-group">
                        <label>Filter by Specialization</label>
                        <select class="form-control" id="specialization_filter" onchange="filterDoctors()">
                            <option value="">All Specializations</option>
                            <?php
                            if (mysqli_num_rows($specializations_query) > 0) {
                                while ($specialization = mysqli_fetch_assoc($specializations_query)) {
                                    echo '<option value="' . $specialization['SPECIALISATION_ID'] . '">' . 
                                         htmlspecialchars($specialization['SPECIALISATION_NAME']) . '</option>';
                                }
                                mysqli_data_seek($specializations_query, 0);
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Select Doctor</label>
                        <div id="doctors_list" style="max-height: 300px; overflow-y: auto;">
                            <?php
                            if (mysqli_num_rows($doctors_query) > 0) {
                                while ($doctor = mysqli_fetch_assoc($doctors_query)) {
                                    echo '<div class="doctor-info" data-specialization="' . $doctor['SPECIALISATION_ID'] . '" onclick="selectDoctor(this, ' . $doctor['DOCTOR_ID'] . ', \'' . htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']) . '\', \'' . htmlspecialchars($doctor['SPECIALISATION_NAME']) . '\')">
                                                <div class="doctor-avatar">' . strtoupper(substr($doctor['FIRST_NAME'], 0, 1) . substr($doctor['LAST_NAME'], 0, 1)) . '</div>
                                                <div class="doctor-details">
                                                    <div class="doctor-name">Dr. ' . htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']) . '</div>
                                                    <div class="doctor-specialization">' . htmlspecialchars($doctor['SPECIALISATION_NAME']) . '</div>
                                                </div>
                                            </div>';
                                }
                            }
                            ?>
                        </div>
                        <input type="hidden" id="selected_doctor_id" name="doctor_id" required>
                        <input type="hidden" id="selected_doctor_name" name="doctor_name">
                        <input type="hidden" id="selected_specialization" name="specialization">
                    </div>
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" onclick="nextStep(2)" id="nextToStep2" disabled>
                            Next: Select Date <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 2: Select Date -->
                <div class="step-content" id="step2Content">
                    <div class="form-group">
                        <label>Select Appointment Date</label>
                        <div class="calendar-container">
                            <div class="calendar-header">
                                <button type="button" class="calendar-nav" id="prevMonth">&lt;</button>
                                <span id="currentMonth">Month Year</span>
                                <button type="button" class="calendar-nav" id="nextMonth">&gt;</button>
                            </div>
                            <div class="calendar-grid" id="calendarGrid">
                                <!-- Calendar will be generated here -->
                            </div>
                        </div>
                        <input type="hidden" id="selected_date" name="appointment_date" required>
                    </div>
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger" onclick="prevStep(1)">
                            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back
                        </button>
                        <button type="button" class="btn btn-primary" onclick="nextStep(3)" id="nextToStep3" disabled>
                            Next: Select Time <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 3: Select Time -->
                <div class="step-content" id="step3Content">
                    <div class="form-group">
                        <label>Select Time Slot</label>
                        <div id="timeSlotsContainer" class="time-slots-container">
                            <div class="loading">
                                <i class="fas fa-spinner"></i>
                                <p>Loading available time slots...</p>
                            </div>
                        </div>
                        <input type="hidden" id="selected_time" name="appointment_time" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reason">Reason for Visit</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Please describe your symptoms or reason for the appointment"></textarea>
                    </div>
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger" onclick="prevStep(2)">
                            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back
                        </button>
                        <button type="button" class="btn btn-primary" onclick="nextStep(4)" id="nextToStep4" disabled>
                            Next: Login <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 4: Login -->
                <div class="step-content" id="step4Content">
                    <div class="login-form">
                        <h3>Login to Continue</h3>
                        <div class="alert alert-danger" id="loginError" style="display: none;"></div>
                        
                        <div class="form-group">
                            <label for="login_username">Username</label>
                            <input type="text" class="form-control" id="login_username" placeholder="Enter your Username" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="login_password">Password</label>
                            <input type="password" class="form-control" id="login_password" placeholder="Enter your password" required>
                        </div>
                        
                        <div class="form-group">
                            <button type="button" class="btn btn-primary" onclick="loginUser()">
                                Login <i class="fas fa-sign-in-alt" style="margin-left: 5px;"></i>
                            </button>
                        </div>
                        
                        <p>Don't have an account? <a href="register.php">Register here</a></p>
                        <p><a href="forgot_password.php">Forgot password?</a></p>
                    </div>
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger" onclick="prevStep(3)">
                            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back
                        </button>
                        <button type="button" class="btn btn-success" onclick="nextStep(5)" id="nextToStep5" disabled>
                            Next: Confirm <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 5: Appointment Confirmation -->
                <div class="step-content" id="step5Content">
                    <div class="confirmation-container">
                        <i class="fas fa-check-circle confirmation-icon"></i>
                        <h3 class="confirmation-title">Appointment Confirmed!</h3>
                        <p class="confirmation-message">Your appointment has been successfully booked. Please make the payment to confirm your booking.</p>
                        
                        <div class="appointment-details">
                            <h4>Appointment Details</h4>
                            <div class="detail-row">
                                <span class="detail-label">Doctor:</span>
                                <span class="detail-value" id="confirmDoctor"></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Specialization:</span>
                                <span class="detail-value" id="confirmSpecialization"></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Date:</span>
                                <span class="detail-value" id="confirmDate"></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Time:</span>
                                <span class="detail-value" id="confirmTime"></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Reason:</span>
                                <span class="detail-value" id="confirmReason"></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Consultation Fee:</span>
                                <span class="detail-value">₹300</span>
                            </div>
                        </div>
                        
                        <div class="btn-group" style="justify-content: center;">
                            <button type="button" class="btn btn-danger" onclick="prevStep(4)">
                                <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back
                            </button>
                            <button type="button" class="btn btn-success" id="submitBtn" onclick="submitAppointment()">
                            <i class="fas fa-credit-card"></i> Proceed to Payment
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Step 6: Payment Processing -->
<div class="step-content" id="step6Content">
    <div class="payment-processing-container">
        <i class="fas fa-lock payment-icon"></i>
        <h3 class="payment-title">Secure Payment</h3>
        <p class="payment-message">Enter your payment details to complete the booking</p>
        
        <div class="payment-form">
            <div class="form-group">
                <label for="cardNumber">Card Number</label>
                <input type="text" class="form-control" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19" required>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="expiryMonth">Expiry Month</label>
                    <select class="form-control" id="expiryMonth" required>
                        <option value="">MM</option>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="expiryYear">Expiry Year</label>
                    <select class="form-control" id="expiryYear" required>
                        <option value="">YYYY</option>
                        <?php 
                        $currentYear = date('Y');
                        for ($i = $currentYear; $i <= $currentYear + 10; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label for="cvv">CVV</label>
                <input type="text" class="form-control" id="cvv" placeholder="123" maxlength="4" required>
            </div>
            
            <div class="form-group">
                <label for="cardName">Cardholder Name</label>
                <input type="text" class="form-control" id="cardName" placeholder="John Doe" required>
            </div>
        </div>
        
        <div class="appointment-summary">
            <h4>Order Summary</h4>
            <div class="summary-row">
                <span>Doctor:</span>
                <span id="summaryDoctor"></span>
            </div>
            <div class="summary-row">
                <span>Date & Time:</span>
                <span id="summaryDateTime"></span>
            </div>
            <div class="summary-row">
                <span>Consultation Fee:</span>
                <span>₹300</span>
            </div>
            <div class="summary-row total">
                <span>Total Amount:</span>
                <span>₹300</span>
            </div>
        </div>
        
        <div class="btn-group" style="justify-content: center;">
            <button type="button" class="btn btn-danger" onclick="prevStep(5)">
                <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back
            </button>
            <button type="button" class="btn btn-success" id="processPaymentBtn" onclick="processPayment()">
                <i class="fas fa-lock"></i> Process Payment
            </button>
        </div>
    </div>
</div>
            </form>
        </div>
    </div>

    <script>
    // Modal functions
    function openBookingModal(doctorId, doctorName) {
        if (doctorId) {
            // Pre-select the doctor if doctorId is provided
            selectedDoctorId = doctorId;
            document.getElementById('selected_doctor_id').value = doctorId;
            
            // Auto-advance to step 2
            loadDoctorSchedule(doctorId);
            nextStep(2);
        } else {
            // Start from step 1
            resetBookingModal();
        }
        
        document.getElementById('bookingModal').style.display = 'block';
    }
    
    function closeBookingModal() {
        document.getElementById('bookingModal').style.display = 'none';
        resetBookingModal();
    }
    
    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const bookingModal = document.getElementById('bookingModal');
        if (event.target == bookingModal) {
            bookingModal.style.display = 'none';
        }
    }
    
    // Step navigation functions
    function nextStep(stepNumber) {
        document.querySelectorAll('.step-content').forEach(content => {
            content.classList.remove('active');
        });
        
        document.getElementById('step' + stepNumber + 'Content').classList.add('active');
        
        for (let i = 1; i < stepNumber; i++) {
            document.getElementById('step' + i).classList.add('completed');
            document.getElementById('step' + i).classList.remove('active');
        }
        document.getElementById('step' + stepNumber).classList.add('active');
        
        // If moving to step 5, populate the confirmation details
        if (stepNumber === 5) {
            populateConfirmationDetails();
        }
    }
    
    function prevStep(stepNumber) {
        document.querySelectorAll('.step-content').forEach(content => {
            content.classList.remove('active');
        });
        
        document.getElementById('step' + stepNumber + 'Content').classList.add('active');
        
        for (let i = stepNumber + 1; i <= 5; i++) {
            document.getElementById('step' + i).classList.remove('active');
            document.getElementById('step' + i).classList.remove('completed');
        }
        document.getElementById('step' + stepNumber).classList.add('active');
    }
    
    function resetBookingModal() {
        document.querySelectorAll('.step-content').forEach(content => {
            content.classList.remove('active');
        });
        document.getElementById('step1Content').classList.add('active');
        
        for (let i = 2; i <= 5; i++) {
            document.getElementById('step' + i).classList.remove('active');
            document.getElementById('step' + i).classList.remove('completed');
        }
        
        // Reset form fields
        document.getElementById('appointmentForm').reset();
        document.getElementById('selected_doctor_id').value = '';
        document.getElementById('selected_doctor_name').value = '';
        document.getElementById('selected_specialization').value = '';
        document.getElementById('selected_date').value = '';
        document.getElementById('selected_time').value = '';
        
        // Reset button states
        document.getElementById('nextToStep2').disabled = true;
        document.getElementById('nextToStep3').disabled = true;
        document.getElementById('nextToStep4').disabled = true;
        document.getElementById('nextToStep5').disabled = true;
        
        // Reset selections
        document.querySelectorAll('.doctor-info').forEach(doc => {
            doc.classList.remove('selected');
        });
        document.querySelectorAll('.calendar-day').forEach(day => {
            day.classList.remove('selected');
        });
        document.querySelectorAll('.time-slot').forEach(slot => {
            slot.classList.remove('selected');
        });
        
        // Reset login form
        document.getElementById('loginError').style.display = 'none';
        document.getElementById('login_username').value = '';
        document.getElementById('login_password').value = '';
        document.getElementById('login_username').disabled = false;
        document.getElementById('login_password').disabled = false;
        document.querySelector('.login-form .btn-primary').style.display = 'block';
        
        // Remove success message if exists
        const successMessage = document.querySelector('.login-form .alert-success');
        if (successMessage) {
            successMessage.remove();
        }
    }
    
    // Doctor selection and filtering
    function selectDoctor(element, doctorId, doctorName, specialization) {
        document.querySelectorAll('.doctor-info').forEach(doc => {
            doc.classList.remove('selected');
        });
        
        element.classList.add('selected');
        document.getElementById('selected_doctor_id').value = doctorId;
        document.getElementById('selected_doctor_name').value = doctorName;
        document.getElementById('selected_specialization').value = specialization;
        document.getElementById('nextToStep2').disabled = false;
        
        loadDoctorSchedule(doctorId);
    }
    
    function filterDoctors() {
        const specializationId = document.getElementById('specialization_filter').value;
        const doctors = document.querySelectorAll('.doctor-info');
        
        doctors.forEach(doctor => {
            if (specializationId === '' || doctor.getAttribute('data-specialization') === specializationId) {
                doctor.style.display = 'flex';
            } else {
                doctor.style.display = 'none';
            }
        });
    }
    
    // Calendar functionality
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let selectedDoctorId = null;
    let doctorSchedule = [];
    
    document.addEventListener('DOMContentLoaded', function() {
        initCalendar();
    });
    
    function initCalendar() {
        renderCalendar(currentMonth, currentYear);
        
        document.getElementById('prevMonth').addEventListener('click', () => {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            renderCalendar(currentMonth, currentYear);
        });
        
        document.getElementById('nextMonth').addEventListener('click', () => {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            renderCalendar(currentMonth, currentYear);
        });
    }
    
    function renderCalendar(month, year) {
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                           'July', 'August', 'September', 'October', 'November', 'December'];
        const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        
        document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;
        
        const calendarGrid = document.getElementById('calendarGrid');
        calendarGrid.innerHTML = '';
        
        dayNames.forEach(day => {
            const dayHeader = document.createElement('div');
            dayHeader.className = 'calendar-day-header';
            dayHeader.textContent = day;
            calendarGrid.appendChild(dayHeader);
        });
        
        for (let i = 0; i < firstDay; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'calendar-day other-month';
            calendarGrid.appendChild(emptyDay);
        }
        
        const today = new Date();
        for (let day = 1; day <= daysInMonth; day++) {
            const dayElement = document.createElement('div');
            dayElement.className = 'calendar-day';
            dayElement.textContent = day;
            
            const currentDate = new Date(year, month, day);
            if (currentDate < today.setHours(0, 0, 0, 0)) {
                dayElement.classList.add('disabled');
            }
            
            if (selectedDoctorId && !dayElement.classList.contains('disabled')) {
                const dayOfWeek = currentDate.getDay();
                const dayMap = ['SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'];
                
                if (doctorSchedule.includes(dayMap[dayOfWeek])) {
                    dayElement.classList.add('available');
                    dayElement.addEventListener('click', () => selectDate(year, month, day));
                } else {
                    dayElement.classList.add('disabled');
                }
            }
            
            calendarGrid.appendChild(dayElement);
        }
    }
    
    function loadDoctorSchedule(doctorId) {
        selectedDoctorId = doctorId;
        
        document.getElementById('timeSlotsContainer').innerHTML = `
            <div class="loading">
                <i class="fas fa-spinner"></i>
                <p>Loading doctor's schedule...</p>
            </div>
        `;
        
        fetch(`get_doctor_schedule.php?doctor_id=${doctorId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    doctorSchedule = data.available_days;
                    renderCalendar(currentMonth, currentYear);
                    
                    document.getElementById('timeSlotsContainer').innerHTML = `
                        <div class="loading">
                            <i class="fas fa-spinner"></i>
                            <p>Please select a date first</p>
                        </div>
                    `;
                } else {
                    document.getElementById('timeSlotsContainer').innerHTML = `
                        <div class="alert alert-danger">
                            Error loading doctor's schedule: ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading doctor schedule:', error);
                document.getElementById('timeSlotsContainer').innerHTML = `
                    <div class="alert alert-danger">
                        Error loading doctor's schedule. Please try again later.
                    </div>
                `;
            });
    }
    
    function selectDate(year, month, day) {
        const date = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        document.getElementById('selected_date').value = date;
        
        document.querySelectorAll('.calendar-day').forEach(el => {
            el.classList.remove('selected');
        });
        event.target.classList.add('selected');
        
        document.getElementById('nextToStep3').disabled = false;
        loadTimeSlots(selectedDoctorId, date);
    }
    
    function loadTimeSlots(doctorId, date) {
        document.getElementById('timeSlotsContainer').innerHTML = `
            <div class="loading">
                <i class="fas fa-spinner"></i>
                <p>Loading available time slots...</p>
            </div>
        `;
        
        fetch(`get_time_slots.php?doctor_id=${doctorId}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const timeSlotsContainer = document.getElementById('timeSlotsContainer');
                    timeSlotsContainer.innerHTML = '';
                    
                    if (data.time_slots.length > 0) {
                        data.time_slots.forEach(timeSlot => {
                            const timeSlotElement = document.createElement('div');
                            timeSlotElement.className = 'time-slot available';
                            timeSlotElement.textContent = timeSlot;
                            timeSlotElement.addEventListener('click', () => selectTimeSlot(timeSlot));
                            timeSlotsContainer.appendChild(timeSlotElement);
                        });
                    } else {
                        timeSlotsContainer.innerHTML = `
                            <div class="alert alert-warning">
                                No available time slots for this date.
                            </div>
                        `;
                    }
                } else {
                    document.getElementById('timeSlotsContainer').innerHTML = `
                        <div class="alert alert-danger">
                            Error loading time slots: ${data.message}
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading time slots:', error);
                document.getElementById('timeSlotsContainer').innerHTML = `
                    <div class="alert alert-danger">
                        Error loading time slots. Please try again later.
                    </div>
                `;
            });
    }
    
    function selectTimeSlot(time) {
        document.getElementById('selected_time').value = time;
        
        document.querySelectorAll('.time-slot').forEach(slot => {
            slot.classList.remove('selected');
        });
        event.target.classList.add('selected');
        
        document.getElementById('nextToStep4').disabled = false;
    }
    
    // Login function
    function loginUser() {
        const username = document.getElementById('login_username').value;
        const password = document.getElementById('login_password').value;
        
        if (!username || !password) {
            document.getElementById('loginError').textContent = 'Please enter both username and password';
            document.getElementById('loginError').style.display = 'block';
            return;
        }
        
        // Show loading state
        const loginButton = event.target;
        const originalText = loginButton.innerHTML;
        loginButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
        loginButton.disabled = true;
        
        // Create FormData object to properly send the login data
        const formData = new FormData();
        formData.append('username', username);
        formData.append('password', password);
        
        fetch('login_process.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Login successful, enable next button
                document.getElementById('nextToStep5').disabled = false;
                document.getElementById('loginError').style.display = 'none';
                
                // Show success message
                const loginForm = document.querySelector('.login-form');
                const successMessage = document.createElement('div');
                successMessage.className = 'alert alert-success';
                successMessage.textContent = 'Login successful! You can now proceed with booking.';
                loginForm.insertBefore(successMessage, loginForm.firstChild);
                
                // Disable login form
                document.getElementById('login_username').disabled = true;
                document.getElementById('login_password').disabled = true;
                loginButton.style.display = 'none';
            } else {
                // Login failed
                document.getElementById('loginError').textContent = data.message || 'Login failed. Please try again.';
                document.getElementById('loginError').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error during login:', error);
            document.getElementById('loginError').textContent = 'An error occurred. Please try again.';
            document.getElementById('loginError').style.display = 'block';
        })
        .finally(() => {
            // Restore button state
            loginButton.innerHTML = originalText;
            loginButton.disabled = false;
        });
    }
    
    // Update the submitAppointment function to go to step 6 instead of redirecting
function submitAppointment() {
    // Prevent default form submission
    event.preventDefault();
    
    // Get form data
    const doctorId = document.getElementById('selected_doctor_id').value;
    const doctorName = document.getElementById('selected_doctor_name').value;
    const specialization = document.getElementById('selected_specialization').value;
    const date = document.getElementById('selected_date').value;
    const time = document.getElementById('selected_time').value;
    const reason = document.getElementById('reason').value;
    
    // Show loading state
    const submitButton = document.getElementById('submitBtn');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    submitButton.disabled = true;
    
    // Store appointment data in session via AJAX
    fetch('store_appointment_session.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `doctor_id=${doctorId}&doctor_name=${encodeURIComponent(doctorName)}&specialization=${encodeURIComponent(specialization)}&appointment_date=${date}&appointment_time=${time}&reason=${encodeURIComponent(reason)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Move to step 6 instead of redirecting
            nextStep(6);
        } else {
            alert('Error preparing payment: ' + data.message);
            // Restore button state
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error storing appointment data:', error);
        alert('An error occurred. Please try again.');
        // Restore button state
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
}

// NEW FUNCTION: Process payment
function processPayment() {
    // Get form data
    const cardNumber = document.getElementById('cardNumber').value;
    const expiryMonth = document.getElementById('expiryMonth').value;
    const expiryYear = document.getElementById('expiryYear').value;
    const cvv = document.getElementById('cvv').value;
    const cardName = document.getElementById('cardName').value;
    
    // Validate form
    if (!cardNumber || !expiryMonth || !expiryYear || !cvv || !cardName) {
        alert('Please fill all payment details');
        return;
    }
    
    // Show loading state
    const processButton = document.getElementById('processPaymentBtn');
    const originalText = processButton.innerHTML;
    processButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    processButton.disabled = true;
    
    // Simulate payment processing
    setTimeout(() => {
        // Show success message
        const paymentContainer = document.querySelector('.payment-processing-container');
        paymentContainer.innerHTML = `
            <div class="payment-success-container">
                <i class="fas fa-check-circle payment-icon" style="color: var(--success-color);"></i>
                <h3 class="payment-title" style="color: var(--success-color);">Payment Successful!</h3>
                <p class="payment-message">Your appointment has been confirmed and payment has been processed successfully.</p>
                <div class="btn-group" style="justify-content: center; margin-top: 20px;">
                    <button type="button" class="btn btn-primary" onclick="window.location.href='patient.php'">
                        <i class="fas fa-home"></i> Go to Dashboard
                    </button>
                </div>
            </div>
        `;
        
        // Mark step 6 as completed
        document.getElementById('step6').classList.add('completed');
    }, 2000);
}

// Update the resetBookingModal function to handle 6 steps
function resetBookingModal() {
    document.querySelectorAll('.step-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById('step1Content').classList.add('active');
    
    for (let i = 2; i <= 6; i++) {
        document.getElementById('step' + i).classList.remove('active');
        document.getElementById('step' + i).classList.remove('completed');
    }
    
    // ... rest of the existing reset code ...
}

// Update the prevStep function to handle 6 steps
function prevStep(stepNumber) {
    document.querySelectorAll('.step-content').forEach(content => {
        content.classList.remove('active');
    });
    
    document.getElementById('step' + stepNumber + 'Content').classList.add('active');
    
    for (let i = stepNumber + 1; i <= 6; i++) {
        document.getElementById('step' + i).classList.remove('active');
        document.getElementById('step' + i).classList.remove('completed');
    }
    document.getElementById('step' + stepNumber).classList.add('active');
}
</script>
    
</body>
</html>