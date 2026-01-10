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

// Initialize doctor schedules array
 $doctor_schedules = [];

// Fetch all doctors' schedules
 $doctors_schedule_query = "SELECT DOCTOR_ID, AVAILABLE_DAY, START_TIME, END_TIME FROM doctor_schedule_tbl";
 $schedule_result = mysqli_query($conn, $doctors_schedule_query);

if ($schedule_result && mysqli_num_rows($schedule_result) > 0) {
    while ($schedule_row = mysqli_fetch_assoc($schedule_result)) {
        $doctor_id = $schedule_row['DOCTOR_ID'];
        if (!isset($doctor_schedules[$doctor_id])) {
            $doctor_schedules[$doctor_id] = [
                'available_days' => [],
                'time_slots' => []
            ];
        }
        
        // Add available day
        if (!in_array($schedule_row['AVAILABLE_DAY'], $doctor_schedules[$doctor_id]['available_days'])) {
            $doctor_schedules[$doctor_id]['available_days'][] = $schedule_row['AVAILABLE_DAY'];
        }
        
        // Add time slot for this day
        $day = $schedule_row['AVAILABLE_DAY'];
        if (!isset($doctor_schedules[$doctor_id]['time_slots'][$day])) {
            $doctor_schedules[$doctor_id]['time_slots'][$day] = [];
        }
        
        // Generate time slots between start and end time
        $start_time = new DateTime($schedule_row['START_TIME']);
        $end_time = new DateTime($schedule_row['END_TIME']);
        $interval = new DateInterval('PT30M'); // 30-minute intervals
        
        while ($start_time < $end_time) {
            $time_slot = $start_time->format('h:i A');
            if (!in_array($time_slot, $doctor_schedules[$doctor_id]['time_slots'][$day])) {
                $doctor_schedules[$doctor_id]['time_slots'][$day][] = $time_slot;
            }
            $start_time->add($interval);
        }
    }
}

// Fetch doctor details for all doctors
 $doctor_details = [];
 $doctors_query = "SELECT d.DOCTOR_ID, d.FIRST_NAME, d.LAST_NAME, s.SPECIALISATION_NAME as specialization 
                  FROM doctor_tbl d 
                  JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID";
 $details_result = mysqli_query($conn, $doctors_query);

if ($details_result && mysqli_num_rows($details_result) > 0) {
    while ($details_row = mysqli_fetch_assoc($details_result)) {
        $doctor_details[$details_row['DOCTOR_ID']] = $details_row;
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

        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
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

        /* Payment Styles */
        .payment-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .payment-method {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }

        .payment-method:hover {
            border-color: var(--secondary-color);
            background-color: rgba(52, 152, 219, 0.05);
        }

        .payment-method.selected {
            border-color: var(--secondary-color);
            background-color: rgba(52, 152, 219, 0.1);
        }

        .payment-method i {
            font-size: 24px;
            margin-right: 10px;
            color: var(--secondary-color);
        }

        .appointment-summary {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .summary-row:last-child {
            margin-bottom: 0;
            font-weight: bold;
            font-size: 18px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
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

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
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

        /* Success Animation */
        .success-animation {
            text-align: center;
            padding: 40px 0;
        }

        .success-animation i {
            font-size: 60px;
            color: var(--accent-color);
            margin-bottom: 20px;
            animation: scaleIn 0.5s ease;
        }

        @keyframes scaleIn {
            from { transform: scale(0); }
            to { transform: scale(1); }
        }

        /* Footer with Wave Effect - Color changed to match aboutus.php */
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
            background-color: var(--primary);
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
            <h2>Book Appointment</h2>
            
            <!-- Doctor Info Display -->
            <div class="form-group" id="doctorInfo">
                <div class="doctor-info selected" style="padding: 10px; border: 1px solid #eee; border-radius: 8px; margin-bottom: 20px;">
                    <div class="doctor-avatar" style="width: 50px; height: 50px; border-radius: 50%; background-color: var(--secondary-color); color: white; display: flex; align-items: center; justify-content: center; margin-right: 15px; font-weight: bold; font-size: 18px; float: left;">
                        <span id="doctorInitials"></span>
                    </div>
                    <div style="overflow: hidden;">
                        <div class="doctor-name" id="doctorName" style="font-weight: 600; margin-bottom: 5px; color: var(--primary-color);"></div>
                        <div class="doctor-specialization" id="doctorSpecialization" style="font-size: 14px; color: #666;"></div>
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </div>
            
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step completed" id="step1">1</div>
                <div class="step active" id="step2">2</div>
                <div class="step" id="step3">3</div>
                <div class="step" id="step4">4</div>
                <div class="step" id="step5">5</div>
            </div>
            
            <form method="POST" action="payment.php" id="bookingForm">
                <input type="hidden" id="selected_doctor_id" name="doctor_id" required>
                <input type="hidden" id="selected_date" name="appointment_date" required>
                <input type="hidden" id="selected_time" name="appointment_time" required>
                
                <!-- Step 1: Select Doctor (Hidden) -->
                <div class="step-content" id="step1Content">
                    <!-- This step is skipped but kept for compatibility -->
                </div>
                
                <!-- Step 2: Select Date -->
                <div class="step-content active" id="step2Content">
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
                    </div>
                    
                    <div class="btn-group">
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
                            Next: Payment <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 4: Payment -->
                <div class="step-content" id="step4Content">
                    <div class="appointment-summary">
                        <h3>Appointment Summary</h3>
                        <div class="summary-row">
                            <span>Doctor:</span>
                            <span id="summaryDoctor"></span>
                        </div>
                        <div class="summary-row">
                            <span>Date:</span>
                            <span id="summaryDate"></span>
                        </div>
                        <div class="summary-row">
                            <span>Time:</span>
                            <span id="summaryTime"></span>
                        </div>
                        <div class="summary-row">
                            <span>Consultation Fee:</span>
                            <span id="summaryFee">$50</span>
                        </div>
                        <div class="summary-row">
                            <span>Total:</span>
                            <span id="summaryTotal">$50</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Select Payment Method</label>
                        <div class="payment-methods">
                            <div class="payment-method" onclick="selectPaymentMethod(this, 'credit-card')">
                                <i class="fas fa-credit-card"></i>
                                <span>Credit Card</span>
                            </div>
                            <div class="payment-method" onclick="selectPaymentMethod(this, 'debit-card')">
                                <i class="fas fa-credit-card"></i>
                                <span>Debit Card</span>
                            </div>
                            <div class="payment-method" onclick="selectPaymentMethod(this, 'paypal')">
                                <i class="fab fa-paypal"></i>
                                <span>PayPal</span>
                            </div>
                            <div class="payment-method" onclick="selectPaymentMethod(this, 'cash')">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Cash on Visit</span>
                            </div>
                        </div>
                        <input type="hidden" id="payment_method" name="payment_method" required>
                    </div>
                    
                    <div class="form-group" id="cardDetails" style="display: none;">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="card_number">Card Number</label>
                                <input type="text" class="form-control" id="card_number" placeholder="1234 5678 9012 3456">
                            </div>
                            <div class="form-group">
                                <label for="card_name">Cardholder Name</label>
                                <input type="text" class="form-control" id="card_name" placeholder="John Doe">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="expiry_date">Expiry Date</label>
                                <input type="text" class="form-control" id="expiry_date" placeholder="MM/YY">
                            </div>
                            <div class="form-group">
                                <label for="cvv">CVV</label>
                                <input type="text" class="form-control" id="cvv" placeholder="123">
                            </div>
                        </div>
                    </div>
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger" onclick="prevStep(3)">
                            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back
                        </button>
                        <button type="button" class="btn btn-primary" onclick="processPayment()" id="processPaymentBtn" disabled>
                            Process Payment <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 5: Payment Success -->
                <div class="step-content" id="step5Content">
                    <div class="success-animation">
                        <i class="fas fa-check-circle"></i>
                        <h2>Payment Successful!</h2>
                        <p>Your appointment has been confirmed.</p>
                        <p>An email with the appointment details has been sent to your registered email address.</p>
                        
                        <div class="appointment-summary" style="margin-top: 30px;">
                            <h3>Appointment Details</h3>
                            <div class="summary-row">
                                <span>Appointment ID:</span>
                                <span id="appointmentId">APT-2023-<?php echo rand(1000, 9999); ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Doctor:</span>
                                <span id="finalDoctor"></span>
                            </div>
                            <div class="summary-row">
                                <span>Date:</span>
                                <span id="finalDate"></span>
                            </div>
                            <div class="summary-row">
                                <span>Time:</span>
                                <span id="finalTime"></span>
                            </div>
                            <div class="summary-row">
                                <span>Payment Method:</span>
                                <span id="finalPaymentMethod"></span>
                            </div>
                        </div>
                        
                        <div class="btn-group" style="justify-content: center; margin-top: 30px;">
                            <button type="button" class="btn btn-primary" onclick="closeBookingModal()">
                                Done
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Pass PHP data to JavaScript
        const doctorSchedules = <?php echo json_encode($doctor_schedules); ?>;
        const doctorDetails = <?php echo json_encode($doctor_details); ?>;
        
        // Calendar functionality
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        let selectedDoctorId = null;
        let doctorSchedule = [];
        let selectedDoctorData = null;
        let selectedPaymentMethod = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize calendar
            initCalendar();
        });

        // Modal functions
        function openBookingModal(doctorId, doctorName) {
            document.getElementById('bookingModal').style.display = 'block';
            
            // Set the selected doctor
            selectedDoctorId = doctorId;
            document.getElementById('selected_doctor_id').value = doctorId;
            
            // Update doctor info display
            document.getElementById('doctorName').textContent = 'Dr. ' + doctorName;
            document.getElementById('doctorInitials').textContent = doctorName.split(' ').map(n => n[0]).join('').toUpperCase();
            
            // Set doctor details from the preloaded data
            if (doctorDetails[doctorId]) {
                selectedDoctorData = doctorDetails[doctorId];
                document.getElementById('doctorSpecialization').textContent = selectedDoctorData.specialization;
            }
            
            // Load doctor's schedule from preloaded data
            loadDoctorSchedule(doctorId);
            
            // Reset modal to step 2
            resetBookingModal();
        }
        
        function closeBookingModal() {
            document.getElementById('bookingModal').style.display = 'none';
            // Reload page to refresh any data
            window.location.reload();
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const bookingModal = document.getElementById('bookingModal');
            if (event.target == bookingModal) {
                closeBookingModal();
            }
        }
        
        // Step navigation functions
        function nextStep(stepNumber) {
            // Hide current step
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Show next step
            document.getElementById('step' + stepNumber + 'Content').classList.add('active');
            
            // Update step indicators
            for (let i = 1; i < stepNumber; i++) {
                document.getElementById('step' + i).classList.add('completed');
                document.getElementById('step' + i).classList.remove('active');
            }
            document.getElementById('step' + stepNumber).classList.add('active');
            
            // If moving to payment step, update summary
            if (stepNumber === 4) {
                updatePaymentSummary();
            }
        }
        
        function prevStep(stepNumber) {
            // Hide current step
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Show previous step
            document.getElementById('step' + stepNumber + 'Content').classList.add('active');
            
            // Update step indicators
            for (let i = stepNumber + 1; i <= 5; i++) {
                document.getElementById('step' + i).classList.remove('active');
                document.getElementById('step' + i).classList.remove('completed');
            }
            document.getElementById('step' + stepNumber).classList.add('active');
        }
        
        function resetBookingModal() {
            // Reset to step 2
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById('step2Content').classList.add('active');
            
            // Reset step indicators
            document.getElementById('step1').classList.add('completed');
            document.getElementById('step1').classList.remove('active');
            document.getElementById('step2').classList.add('active');
            document.getElementById('step3').classList.remove('active');
            document.getElementById('step3').classList.remove('completed');
            document.getElementById('step4').classList.remove('active');
            document.getElementById('step4').classList.remove('completed');
            document.getElementById('step5').classList.remove('active');
            document.getElementById('step5').classList.remove('completed');
            
            // Reset form values
            document.getElementById('selected_date').value = '';
            document.getElementById('selected_time').value = '';
            document.getElementById('reason').value = '';
            document.getElementById('payment_method').value = '';
            document.getElementById('nextToStep3').disabled = true;
            document.getElementById('nextToStep4').disabled = true;
            document.getElementById('processPaymentBtn').disabled = true;
            
            // Reset payment method selection
            document.querySelectorAll('.payment-method').forEach(method => {
                method.classList.remove('selected');
            });
            document.getElementById('cardDetails').style.display = 'none';
            
            // Clear calendar selection
            document.querySelectorAll('.calendar-day').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Clear time slots
            document.getElementById('timeSlotsContainer').innerHTML = `
                <div class="loading">
                    <i class="fas fa-spinner"></i>
                    <p>Please select a date first</p>
                </div>
            `;
        }
        
        // Calendar functionality
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
            
            // Update month and year display
            document.getElementById('currentMonth').textContent = `${monthNames[month]} ${year}`;
            
            // Clear calendar grid
            const calendarGrid = document.getElementById('calendarGrid');
            calendarGrid.innerHTML = '';
            
            // Add day headers
            dayNames.forEach(day => {
                const dayHeader = document.createElement('div');
                dayHeader.className = 'calendar-day-header';
                dayHeader.textContent = day;
                calendarGrid.appendChild(dayHeader);
            });
            
            // Add empty cells for days before month starts
            for (let i = 0; i < firstDay; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day other-month';
                calendarGrid.appendChild(emptyDay);
            }
            
            // Add days of the month
            const today = new Date();
            for (let day = 1; day <= daysInMonth; day++) {
                const dayElement = document.createElement('div');
                dayElement.className = 'calendar-day';
                dayElement.textContent = day;
                
                // Check if this day is in the past
                const currentDate = new Date(year, month, day);
                if (currentDate < today.setHours(0, 0, 0, 0)) {
                    dayElement.classList.add('disabled');
                }
                
                // Check if this day is available for the selected doctor
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
            
            // Get doctor schedule from preloaded data
            if (doctorSchedules[doctorId]) {
                doctorSchedule = doctorSchedules[doctorId].available_days;
                console.log('Available days:', doctorSchedule); // Debug log
                renderCalendar(currentMonth, currentYear);
                
                // Clear time slots
                document.getElementById('timeSlotsContainer').innerHTML = `
                    <div class="loading">
                        <i class="fas fa-spinner"></i>
                        <p>Please select a date first</p>
                    </div>
                `;
            } else {
                console.error('No schedule found for doctor:', doctorId);
                document.getElementById('timeSlotsContainer').innerHTML = `
                    <div class="alert alert-warning">
                        No schedule available for this doctor.
                    </div>
                `;
            }
        }
        
        function selectDate(year, month, day) {
            // Format date as YYYY-MM-DD
            const date = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            document.getElementById('selected_date').value = date;
            
            // Update calendar to show selected date
            document.querySelectorAll('.calendar-day').forEach(el => {
                el.classList.remove('selected');
            });
            event.target.classList.add('selected');
            
            // Load time slots for the selected date
            loadTimeSlots(selectedDoctorId, date);
        }
        
        function loadTimeSlots(doctorId, date) {
            // Show loading indicator
            document.getElementById('timeSlotsContainer').innerHTML = `
                <div class="loading">
                    <i class="fas fa-spinner"></i>
                    <p>Loading available time slots...</p>
                </div>
            `;
            
            // Get day of week from date
            const dayOfWeek = new Date(date).toLocaleDateString('en-US', { weekday: 'short' }).toUpperCase();
            
            // Get time slots from preloaded data
            if (doctorSchedules[doctorId] && doctorSchedules[doctorId].time_slots[dayOfWeek]) {
                const timeSlots = doctorSchedules[doctorId].time_slots[dayOfWeek];
                const timeSlotsContainer = document.getElementById('timeSlotsContainer');
                timeSlotsContainer.innerHTML = '';
                
                if (timeSlots.length > 0) {
                    timeSlots.forEach(timeSlot => {
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
                    <div class="alert alert-warning">
                        No available time slots for this date.
                    </div>
                `;
            }
        }
        
        function selectTimeSlot(time) {
            // Update hidden input
            document.getElementById('selected_time').value = time;
            
            // Update UI to show selected time slot
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('selected');
            });
            event.target.classList.add('selected');
            
            // Enable next button
            document.getElementById('nextToStep4').disabled = false;
        }
        
        // Payment functions
        function selectPaymentMethod(element, method) {
            // Remove selected class from all payment methods
            document.querySelectorAll('.payment-method').forEach(el => {
                el.classList.remove('selected');
            });
            
            // Add selected class to clicked payment method
            element.classList.add('selected');
            
            // Set the hidden input value
            document.getElementById('payment_method').value = method;
            selectedPaymentMethod = method;
            
            // Show/hide card details based on payment method
            if (method === 'credit-card' || method === 'debit-card') {
                document.getElementById('cardDetails').style.display = 'block';
            } else {
                document.getElementById('cardDetails').style.display = 'none';
            }
            
            // Enable process payment button
            document.getElementById('processPaymentBtn').disabled = false;
        }
        
        function updatePaymentSummary() {
            // Update summary with appointment details
            document.getElementById('summaryDoctor').textContent = document.getElementById('doctorName').textContent;
            
            const dateValue = document.getElementById('selected_date').value;
            if (dateValue) {
                const date = new Date(dateValue);
                const formattedDate = date.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
                document.getElementById('summaryDate').textContent = formattedDate;
            }
            
            document.getElementById('summaryTime').textContent = document.getElementById('selected_time').value;
        }
        
        function processPayment() {
            // Show loading state
            document.getElementById('processPaymentBtn').disabled = true;
            document.getElementById('processPaymentBtn').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            
            // Simulate payment processing
            setTimeout(() => {
                // Update final appointment details
                document.getElementById('finalDoctor').textContent = document.getElementById('doctorName').textContent;
                
                const dateValue = document.getElementById('selected_date').value;
                if (dateValue) {
                    const date = new Date(dateValue);
                    const formattedDate = date.toLocaleDateString('en-US', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric' 
                    });
                    document.getElementById('finalDate').textContent = formattedDate;
                }
                
                document.getElementById('finalTime').textContent = document.getElementById('selected_time').value;
                
                // Format payment method for display
                let paymentMethodText = '';
                switch (selectedPaymentMethod) {
                    case 'credit-card':
                        paymentMethodText = 'Credit Card';
                        break;
                    case 'debit-card':
                        paymentMethodText = 'Debit Card';
                        break;
                    case 'paypal':
                        paymentMethodText = 'PayPal';
                        break;
                    case 'cash':
                        paymentMethodText = 'Cash on Visit';
                        break;
                    default:
                        paymentMethodText = 'Unknown';
                }
                document.getElementById('finalPaymentMethod').textContent = paymentMethodText;
                
                // Move to success step
                nextStep(5);
                
                // Submit the form to save the appointment in the database
                document.getElementById('bookingForm').submit();
            }, 2000); // Simulate 2 seconds processing time
        }
    </script>
</body>
</html>