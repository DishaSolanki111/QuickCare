<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['PATIENT_ID'])) {
    header("Location: book_appointment_confirm.php");
    exit();
}
include "config.php";
include "header.php";

// Check if doctor is selected
if (!isset($_SESSION['booking_doctor_id'])) {
    header("Location: doctors.php");
    exit();
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

// Get selected date and time from session
 $selected_date = isset($_SESSION['booking_date']) ? $_SESSION['booking_date'] : '';
 $selected_time = isset($_SESSION['booking_time']) ? $_SESSION['booking_time'] : '';
 $reason = isset($_SESSION['booking_reason']) ? $_SESSION['booking_reason'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></title>
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
            padding-top:100px;
        }


        .container {
            max-width: 1200px;
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

        .btn-secondary {
            background: var(--light-blue);
            color: var(--primary);
        }

        .btn-secondary:hover {
            background: var(--medium-blue);
            transform: translateY(-3px);
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

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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
                <!-- Doctor Summary -->
                <div class="doctor-summary">
                    <div class="doctor-avatar">
                        <?php echo strtoupper(substr($doctor['FIRST_NAME'], 0, 1) . substr($doctor['LAST_NAME'], 0, 1)); ?>
                    </div>
                    <div class="doctor-info">
                        <h3>Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></h3>
                        <p><?php echo htmlspecialchars($doctor['SPECIALISATION_NAME']); ?></p>
                        <p>Selected Date: <?php echo htmlspecialchars($selected_date); ?> at <?php echo htmlspecialchars($selected_time); ?></p>
                    </div>
                </div>

                <div class="modal-content">
                    <a href="book_appointment_time.php" class="close">&times;</a>
                    <h2>Login to Continue</h2>
                    
                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step completed" id="step1">1</div>
                        <div class="step completed" id="step2">2</div>
                        <div class="step active" id="step3">3</div>
                        <div class="step" id="step4">4</div>
                        <div class="step" id="step5">5</div>
                    </div>
                    
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
                        
                        <p>Don't have an account? <a href="patientform.php">Register here</a></p>
                        <p><a href="forgot_password.php">Forgot password?</a></p>
                    </div>
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger" onclick="goBack()">
                            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back
                        </button>
                        <button type="button" class="btn btn-primary" onclick="proceedToConfirm()" id="nextToConfirm" disabled>
                            Next: Confirm <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                        </button>
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
        </div>
    </footer>

    <script>
    function goBack() {
        window.location.href = 'book_appointment_time.php';
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
                document.getElementById('nextToConfirm').disabled = false;
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
    
    function proceedToConfirm() {
        // Redirect to confirmation page
        window.location.href = 'book_appointment_confirm.php';
    }
    </script>
</body>
</html>