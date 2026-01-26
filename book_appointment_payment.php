<?php
session_start();
include "config.php";
if (!isset($_SESSION['PATIENT_ID'])) {
    die("Patient not logged in");
}

// dummy amount
 $amount = 300;

// Get patient information
 $patient_id = $_SESSION['PATIENT_ID'];
 $patient_query = mysqli_query($conn, "SELECT * FROM patient_tbl WHERE PATIENT_ID = $patient_id");
 $patient = mysqli_fetch_assoc($patient_query);

// Get booking information from session
 $doctor_id = isset($_SESSION['booking_doctor_id']) ? $_SESSION['booking_doctor_id'] : 0;
 $selected_date = isset($_SESSION['booking_date']) ? $_SESSION['booking_date'] : '';
 $selected_time = isset($_SESSION['booking_time']) ? $_SESSION['booking_time'] : '';
 $reason = isset($_SESSION['booking_reason']) ? $_SESSION['booking_reason'] : '';

// Get doctor details if available
 $doctor = null;
if ($doctor_id > 0) {
    $doctor_query = mysqli_query($conn, "
        SELECT d.*, s.SPECIALISATION_NAME 
        FROM doctor_tbl d
        JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
        WHERE d.DOCTOR_ID = $doctor_id
    ");
    if (mysqli_num_rows($doctor_query) > 0) {
        $doctor = mysqli_fetch_assoc($doctor_query);
    }
}

// Handle Razorpay payment success
if (isset($_POST['razorpay_payment_id'])) {
    // Get the payment details
    $payment_id = $_POST['razorpay_payment_id'];
    $signature = isset($_POST['razorpay_signature']) ? $_POST['razorpay_signature'] : '';
    
    // Database insertion is commented out to skip saving to database
    /*
    // Insert appointment into database
    $insert_query = "INSERT INTO appointment_tbl (PATIENT_ID, DOCTOR_ID, APPOINTMENT_DATE, APPOINTMENT_TIME, REASON, STATUS) 
                   VALUES ($patient_id, $doctor_id, '$selected_date', '$selected_time', '$reason', 'COMPLETED')";
    
    $result = mysqli_query($conn, $insert_query);
    
    if ($result) {
        // Get the appointment ID that was just inserted
        $appointment_id = mysqli_insert_id($conn);
        
        // Insert payment record
        $payment_insert_query = "INSERT INTO payment_tbl (APPOINTMENT_ID, AMOUNT, PAYMENT_DATE, PAYMENT_MODE, STATUS, TRANSACTION_ID) 
                                VALUES ($appointment_id, $amount, NOW(), 'RAZORPAY', 'COMPLETED', '$payment_id')";
        
        $payment_result = mysqli_query($conn, $payment_insert_query);
    */
    
    // Since we're not inserting into database, we'll proceed directly to clearing session and redirecting
    // Clear booking session data
    unset($_SESSION['booking_doctor_id']);
    unset($_SESSION['booking_doctor_name']);
    unset($_SESSION['booking_specialization']);
    unset($_SESSION['booking_date']);
    unset($_SESSION['booking_time']);
    unset($_SESSION['booking_reason']);
    
    // Redirect to success page
    header("Location: payment_success.php");
    exit;
    /*
    } else {
        $error_message = "Failed to book appointment. Please try again. Error: " . mysqli_error($conn);
    }
    */
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Copy all the CSS styles from your original payment page here */
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
            padding-top:100px;
        }

       
        .container {
            max-width: 500px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .booking-section {
            padding: 4rem 0;
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

        .btn-success {
            background-color: var(--accent-color);
            color: white;
            font-size: 16px;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            width: 100%;
            margin-top: 20px;
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
            color: var(--accent-color);
            font-size: 1.1rem;
        }

        .patient-info {
            background-color: #f0f8ff;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: left;
        }

        .patient-info h4 {
            color: var(--primary-color);
            margin-bottom: 10px;
            text-align: center;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
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

        .box {
            width: 100%;
            padding: 25px;
            background: white;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }

        .box h2 {
            margin-bottom: 15px;
            color: var(--primary-color);
        }

        .box p {
            margin-bottom: 20px;
            font-size: 18px;
        }

        .razorpay-payment-button {
            background: #3399cc;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
            transition: all 0.3s ease;
        }

        .razorpay-payment-button:hover {
            background: #2277bb;
        }

        .error-message {
            color: var(--danger-color);
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
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
            
        @media (max-width: 768px) {
         
            
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
                <!-- Doctor Summary (if doctor info is available) -->
                <?php if ($doctor): ?>
                <div class="doctor-summary">
                    <div class="doctor-avatar">
                        <?php echo strtoupper(substr($doctor['FIRST_NAME'], 0, 1) . substr($doctor['LAST_NAME'], 0, 1)); ?>
                    </div>
                    <div class="doctor-info">
                        <h3>Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></h3>
                        <p><?php echo htmlspecialchars($doctor['SPECIALISATION_NAME']); ?></p>
                    </div>
                </div>
                <?php endif; ?>

                <div class="modal-content">
                    <h2>Secure Payment</h2>
                    
                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step completed" id="step1">1</div>
                        <div class="step completed" id="step2">2</div>
                        <div class="step completed" id="step3">3</div>
                        <div class="step completed" id="step4">4</div>
                        <div class="step active" id="step5">5</div>
                    </div>
                    
                    <!-- Patient Information -->
                    <div class="patient-info">
                        <h4>Patient Information</h4>
                        <div class="detail-row">
                            <span class="detail-label">Name:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Username:</span>
                            <span class="detail-value"><?php echo htmlspecialchars($patient['USERNAME']); ?></span>
                        </div>
                    </div>
                    
                    <?php if (isset($error_message)): ?>
                    <div class="error-message"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <div class="payment-processing-container">
                        <i class="fas fa-lock payment-icon"></i>
                        <h3 class="payment-title">Secure Payment</h3>
                        <p class="payment-message">Complete your payment to confirm the appointment</p>
                        
                        <div class="box">
                            <h2>Payment</h2>
                            <p>Total Amount: <b>₹<?php echo $amount; ?></b></p>

                            <!-- Razorpay Payment Button -->
                            <button class="razorpay-payment-button" id="rzp-button">Pay with Razorpay</button>
                        </div>
                        
                        <?php if ($doctor): ?>
                        <div class="appointment-summary">
                            <h4>Appointment Summary</h4>
                            <div class="summary-row">
                                <span>Doctor:</span>
                                <span>Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></span>
                            </div>
                            <?php if ($selected_date): ?>
                            <div class="summary-row">
                                <span>Date:</span>
                                <span><?php echo htmlspecialchars($selected_date); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($selected_time): ?>
                            <div class="summary-row">
                                <span>Time:</span>
                                <span><?php echo htmlspecialchars($selected_time); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if ($reason): ?>
                            <div class="summary-row">
                                <span>Reason:</span>
                                <span><?php echo htmlspecialchars($reason); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="summary-row">
                                <span>Consultation Fee:</span>
                                <span>₹<?php echo $amount; ?></span>
                            </div>
                            <div class="summary-row total">
                                <span>Total Amount:</span>
                                <span>₹<?php echo $amount; ?></span>
                            </div>
                        </div>
                        <?php endif; ?>
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

    <!-- Razorpay Script -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.getElementById('rzp-button').onclick = function(e) {
            e.preventDefault();
            
            var options = {
                "key": "rzp_test_S8YWQLeAKtofm8", // Enter your Test Key ID here
                "amount": "<?php echo $amount * 100; ?>", // Amount is in currency subunits. Default currency is INR. Hence, 50000 refers to 50000 paise
                "currency": "INR",
                "name": "QuickCare",
                "description": "Appointment Booking",
                "image": "https://example.com/your_logo.png", // Your company logo
                "handler": function (response) {
                    // Create a form to submit the payment details
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '';
                    
                    // Add the payment details to the form
                    var razorpay_payment_id = document.createElement('input');
                    razorpay_payment_id.type = 'hidden';
                    razorpay_payment_id.name = 'razorpay_payment_id';
                    razorpay_payment_id.value = response.razorpay_payment_id;
                    form.appendChild(razorpay_payment_id);
                    
                    if (response.razorpay_signature) {
                        var razorpay_signature = document.createElement('input');
                        razorpay_signature.type = 'hidden';
                        razorpay_signature.name = 'razorpay_signature';
                        razorpay_signature.value = response.razorpay_signature;
                        form.appendChild(razorpay_signature);
                    }
                    
                    // Submit the form
                    document.body.appendChild(form);
                    form.submit();
                },
                "prefill": {
                    "name": "<?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?>",
                    "email": "<?php echo isset($patient['EMAIL']) ? htmlspecialchars($patient['EMAIL']) : ''; ?>",
                    "contact": "<?php echo isset($patient['PHONE']) ? htmlspecialchars($patient['PHONE']) : ''; ?>"
                },
                "notes": {
                    "address": "QuickCare Appointment",
                    "doctor_id": "<?php echo $doctor_id; ?>",
                    "appointment_date": "<?php echo $selected_date; ?>",
                    "appointment_time": "<?php echo $selected_time; ?>"
                },
                "theme": {
                    "color": "#3399cc"
                }
            };
            
            var rzp1 = new Razorpay(options);
            rzp1.open();
        }
    </script>
</body>
</html>