<?php
// Start session to get booking data
session_start();
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

// Get selected date from session
 $selected_date = isset($_SESSION['booking_date']) ? $_SESSION['booking_date'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Time Slot - Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></title>
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
            padding: 4rem 0;
            flex-grow: 1;
        }

        .booking-container {
            max-width: 800px;
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
        @media (max-width: 992px) {
            .time-slots-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
           
            
            .doctor-summary {
                flex-direction: column;
                text-align: center;
            }
            
            .time-slots-container {
                grid-template-columns: 1fr;
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
                        <p>Selected Date: <?php echo htmlspecialchars($selected_date); ?></p>
                    </div>
                </div>

                <div class="modal-content">
                    <a href="book_appointment_date.php?doctor_id=<?php echo $doctor['DOCTOR_ID']; ?>" class="close">&times;</a>
                    <h2>Select Time Slot</h2>
                    
                    <!-- Step Indicator -->
                    <div class="step-indicator">
                        <div class="step completed" id="step1">1</div>
                        <div class="step active" id="step2">2</div>
                        <div class="step" id="step3">3</div>
                        <div class="step" id="step4">4</div>
                        <div class="step" id="step5">5</div>
                    </div>
                    
                    <form method="POST" action="book_appointment_login.php" id="appointmentForm">
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
                            <button type="button" class="btn btn-danger" onclick="goBack()">
                                <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back
                            </button>
                            <button type="button" class="btn btn-primary" onclick="proceedToLogin()" id="nextToLogin" disabled>
                                Next: Login <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                            </button>
                        </div>
                    </form>
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
    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        // Load time slots
        loadTimeSlots(<?php echo $doctor['DOCTOR_ID']; ?>, '<?php echo $selected_date; ?>');
    });
    
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
        
        document.getElementById('nextToLogin').disabled = false;
    }
    
    function goBack() {
        window.location.href = 'book_appointment_date.php?doctor_id=<?php echo $doctor['DOCTOR_ID']; ?>';
    }
    
    function proceedToLogin() {
        const selectedTime = document.getElementById('selected_time').value;
        const reason = document.getElementById('reason').value;
        
        if (!selectedTime) {
            alert('Please select a time slot');
            return;
        }
        
        // Store the selected time and reason in session
        fetch('store_booking_session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `step=time&appointment_time=${selectedTime}&reason=${encodeURIComponent(reason)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Redirect to login page
                window.location.href = 'book_appointment_login.php';
            } else {
                alert('Error saving time: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error saving time:', error);
            alert('An error occurred. Please try again.');
        });
    }
    </script>
</body>
</html>