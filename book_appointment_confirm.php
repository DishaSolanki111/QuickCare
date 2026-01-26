<?php
session_start();
include "config.php";
include "header.php";
if (!isset($_SESSION['PATIENT_ID'])) {
    header("Location: book_appointment_login.php");
    exit;
}

// Get doctor details
 $doctor_id = $_SESSION['booking_doctor_id'];
 $doctor_query = mysqli_query($conn, "
    SELECT d.*, s.SPECIALISATION_NAME 
    FROM doctor_tbl d
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE d.DOCTOR_ID = $doctor_id
");

if (mysqli_num_rows($doctor_query) == 0) {
    header("Location: doctors.php");
    exit();
}

 $doctor = mysqli_fetch_assoc($doctor_query);

// Get selected date, time, and reason from session
 $selected_date = isset($_SESSION['booking_date']) ? $_SESSION['booking_date'] : '';
 $selected_time = isset($_SESSION['booking_time']) ? $_SESSION['booking_time'] : '';
 $reason = isset($_SESSION['booking_reason']) ? $_SESSION['booking_reason'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmation - Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Copy all the CSS styles from book_appointment.php here */
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

       

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .booking-section {
            padding: 2.5rem 0;
            flex-grow: 1;
        }

        .booking-container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .doctor-summary {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--mid-blue) 100%);
            color: white;
            padding: 2rem;
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .doctor-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: white;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 24px;
            flex-shrink: 0;
        }

        .doctor-info h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        .doctor-info p {
            opacity: 0.9;
        }

        .modal-content {
            background-color: #fefefe;
            padding: 30px;
            width: 100%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            text-decoration: none;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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
            margin-bottom: 30px;
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

        /* Footer with Wave Effect */
        footer {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--mid-blue) 100%);
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
            
            .doctor-summary {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    

    <section class="booking-section">
        <div class="container">
            <div class="booking-container">
                <!-- Doctor Summary -->
                <div class="doctor-summary">
                    <div class="doctor-avatar">
                        <?php echo strtoupper(substr($doctor['FIRST_NAME'], 0, 1) . substr($doctor['LAST_NAME'], 0, 1)); ?>
                    </div>
                    <div class="doctor-info">
                        <h3>Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></h3>
                        <p><?php echo htmlspecialchars($doctor['SPECIALISATION_NAME']); ?></p>
                    </div>
                </div>

                <div class="modal-content">
                    <a href="book_appointment_login.php" class="close">&times;</a>
                    <h2>Appointment Details</h2>
                    
                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step completed" id="step1">1</div>
                        <div class="step completed" id="step2">2</div>
                        <div class="step completed" id="step3">3</div>
                        <div class="step active" id="step4">4</div>
                        <div class="step" id="step5">5</div>
                    </div>
                    
                    <div class="confirmation-container">
                        <i class="fas fa-check-circle confirmation-icon"></i>
                        <h3 class="confirmation-title">Appointment Details</h3>
                        <p class="confirmation-message">Please review your appointment details and proceed to payment</p>
                        
                        <div class="appointment-details">
                            <h4>Appointment Summary</h4>
                            <div class="detail-row">
                                <span class="detail-label">Doctor:</span>
                                <span class="detail-value">Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Specialization:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($doctor['SPECIALISATION_NAME']); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Date:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($selected_date); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Time:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($selected_time); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Reason:</span>
                                <span class="detail-value"><?php echo htmlspecialchars($reason ?: 'Not specified'); ?></span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label">Consultation Fee:</span>
                                <span class="detail-value">₹300</span>
                            </div>
                        </div>
                        
                        <div class="btn-group" style="justify-content: center;">
                            <button type="button" class="btn btn-danger" onclick="goBack()">
                                <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back
                            </button>
                            <button type="button" class="btn btn-success" onclick="proceedToPayment()">
                            <i class="fas fa-credit-card"></i> Proceed to Payment
                            </button>
                        </div>
                    </div>
                </div>
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

    <script>
    function goBack() {
        window.location.href = 'book_appointment_login.php';
    }
    
    function proceedToPayment() {
        // Redirect to payment page
        window.location.href = 'book_appointment_payment.php';
    }
    </script>
</body>
</html>