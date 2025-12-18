<?php
// =============================================================================
// START OF PHP BACKEND LOGIC
// =============================================================================

// Start the session to access session variables
session_start();

// Check if the doctor is logged in. If not, redirect to the login page.
if (!isset($_SESSION['doctor_id'])) {
    // Replace 'login.php' with your actual login page
    header('Location: login.php'); 
    exit();
}

 $doctor_id = $_SESSION['doctor_id'];

// --- Database Connection ---
// Replace with your actual database credentials
 $host = 'localhost';
 $db_user = 'your_db_user';
 $db_pass = 'your_db_password';
 $db_name = 'your_db_name';

 $conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Fetch Doctor's Core Profile Data ---
 $doctor_sql = "SELECT d.FIRST_NAME, d.LAST_NAME, d.DOB, d.DOJ, d.USERNAME, d.PHONE, d.EMAIL, d.GENDER, d.PROFILE_IMAGE, s.SPECIALISATION_NAME
               FROM doctor_tbl d
               JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
               WHERE d.DOCTOR_ID = ?";
 $stmt = $conn->prepare($doctor_sql);
 $stmt->bind_param("i", $doctor_id);
 $stmt->execute();
 $doctor_result = $stmt->get_result();
 $doctor = $doctor_result->fetch_assoc();

// --- Fetch Dashboard Summary Data ---

// 1. Today's Appointments Count
 $appointments_sql = "SELECT COUNT(*) as count FROM appointment_tbl 
                    WHERE DOCTOR_ID = ? AND APPOINTMENT_DATE = CURDATE() AND STATUS = 'SCHEDULED'";
 $stmt = $conn->prepare($appointments_sql);
 $stmt->bind_param("i", $doctor_id);
 $stmt->execute();
 $appointments_result = $stmt->get_result();
 $today_appointments = $appointments_result->fetch_assoc()['count'];

// 2. Feedback Summary (Average Rating)
 $feedback_sql = "SELECT AVG(RATING) as avg_rating, COUNT(FEEDBACK_ID) as total_feedbacks
                 FROM feedback_tbl f
                 JOIN appointment_tbl a ON f.APPOINTMENT_ID = a.APPOINTMENT_ID
                 WHERE a.DOCTOR_ID = ?";
 $stmt = $conn->prepare($feedback_sql);
 $stmt->bind_param("i", $doctor_id);
 $stmt->execute();
 $feedback_result = $stmt->get_result();
 $feedback_data = $feedback_result->fetch_assoc();
 $avg_rating = $feedback_data['avg_rating'] ? round($feedback_data['avg_rating'], 1) : 0; // Handle no feedback case

// 3. Schedule Summary (Total patients this week)
 $patients_sql = "SELECT COUNT(DISTINCT PATIENT_ID) as total_patients
                 FROM appointment_tbl
                 WHERE DOCTOR_ID = ? AND YEARWEEK(APPOINTMENT_DATE, 1) = YEARWEEK(CURDATE(), 1)";
 $stmt = $conn->prepare($patients_sql);
 $stmt->bind_param("i", $doctor_id);
 $stmt->execute();
 $patients_result = $stmt->get_result();
 $total_patients_week = $patients_result->fetch_assoc()['total_patients'];

// 4. Next Available Slot (Simplified for this example: finds the next day the doctor works)
 $next_day_sql = "SELECT AVAILABLE_DAY, START_TIME FROM doctor_schedule_tbl 
                 WHERE DOCTOR_ID = ? ORDER BY FIELD(AVAILABLE_DAY, 'MON', 'TUE', 'WED', 'THUR', 'FRI', 'SAT', 'SUN') LIMIT 1";
 $stmt = $conn->prepare($next_day_sql);
 $stmt->bind_param("i", $doctor_id);
 $stmt->execute();
 $schedule_result = $stmt->get_result();
 $next_schedule = $schedule_result->fetch_assoc();
 $next_available_day = $next_schedule ? $next_schedule['AVAILABLE_DAY'] : 'Not Set';
 $next_available_time = $next_schedule ? date("h:i A", strtotime($next_schedule['START_TIME'])) : '';


 $stmt->close();
 $conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile - QuickCare</title>
    <!-- Link to an icon library like Font Awesome for the icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- =============================================================================
         START OF EMBEDDED CSS
         ============================================================================= -->
    <style>
        /* --- General Styles & Variables --- */
        :root {
            --sidebar-bg: #2c3e50;
            --sidebar-hover: #34495e;
            --main-bg: #f4f7f6;
            --card-bg: #ffffff;
            --primary-text: #333;
            --secondary-text: #777;
            --border-color: #e0e0e0;
            --active-link-bg: #3498db;
            --font-family: 'Poppins', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--main-bg);
            color: var(--primary-text);
        }

        /* --- Dashboard Layout --- */
        .dashboard-container {
            display: flex;
            min-height: 100vh;
        }

        /* --- Sidebar --- */
        .sidebar {
            width: 250px;
            background-color: var(--sidebar-bg);
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100%;
            overflow-y: auto;
            transition: all 0.3s;
        }

        .sidebar-header h2 {
            text-align: center;
            padding: 0 20px 20px;
            border-bottom: 1px solid var(--sidebar-hover);
            color: #ecf0f1;
        }

        .sidebar-menu {
            list-style: none;
            padding-top: 20px;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 15px 25px;
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar-menu li a i {
            margin-right: 15px;
            font-size: 1.1em;
        }

        .sidebar-menu li a:hover {
            background-color: var(--sidebar-hover);
            color: white;
        }

        .sidebar-menu li.active a {
            background-color: var(--active-link-bg);
            color: white;
            border-left: 5px solid #2980b9;
        }

        /* --- Main Content --- */
        .main-content {
            flex-grow: 1;
            margin-left: 250px;
            padding: 30px;
        }

        .profile-header {
            margin-bottom: 30px;
        }

        .profile-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .profile-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--active-link-bg);
        }

        .profile-header h1 {
            font-size: 2em;
            color: var(--primary-text);
        }

        .profile-header p {
            color: var(--secondary-text);
            margin-top: 5px;
        }

        /* --- Summary Cards --- */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .summary-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5em;
            color: white;
        }

        .appointments-icon { background-color: #3498db; }
        .feedback-icon { background-color: #f1c40f; }
        .schedule-icon { background-color: #2ecc71; }

        .card-content h3 {
            font-size: 1em;
            color: var(--secondary-text);
            font-weight: 500;
            margin-bottom: 5px;
        }

        .card-number {
            font-size: 2.2em;
            font-weight: 700;
            color: var(--primary-text);
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stars {
            color: #f1c40f;
        }

        .card-text {
            font-size: 0.9em;
            color: var(--secondary-text);
        }

        .card-text strong {
            color: var(--primary-text);
        }

        /* --- Profile Details Section --- */
        .profile-details {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .profile-details h2 {
            margin-bottom: 25px;
            color: var(--primary-text);
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 10px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--main-bg);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: var(--secondary-text);
        }

        .detail-value {
            color: var(--primary-text);
            font-weight: 500;
        }

        /* --- Responsive Design --- */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                z-index: 1000;
            }

            .main-content {
                margin-left: 0;
                padding: 20px;
            }

            .dashboard-container.active .sidebar {
                transform: translateX(0);
            }
            
            .summary-cards {
                grid-template-columns: 1fr;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }
            
            .profile-info {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
    <!-- =============================================================================
         END OF EMBEDDED CSS
         ============================================================================= -->
</head>
<body>

    <div class="dashboard-container">
        <!-- Sidebar Navigation -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <h2>QuickCare</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li class="active"><a href="profile.php"><i class="fas fa-user-md"></i> My Profile</a></li>
                <li><a href="appointments.php"><i class="fas fa-calendar-check"></i> Appointments</a></li>
                <li><a href="patients.php"><i class="fas fa-users"></i> Patients</a></li>
                <li><a href="prescriptions.php"><i class="fas fa-file-prescription"></i> Prescriptions</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content Area -->
        <main class="main-content">
            <!-- Profile Header -->
            <header class="profile-header">
                <div class="profile-info">
                    <img src="images/<?php echo htmlspecialchars($doctor['PROFILE_IMAGE']); ?>" alt="Doctor Profile" class="profile-image">
                    <div>
                        <h1>Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></h1>
                        <p><?php echo date('F j, Y'); ?></p>
                    </div>
                </div>
            </header>

            <!-- Summary Cards -->
            <section class="summary-cards">
                <div class="summary-card">
                    <div class="card-icon appointments-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="card-content">
                        <h3>Today's Appointments</h3>
                        <p class="card-number"><?php echo $today_appointments; ?></p>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="card-icon feedback-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="card-content">
                        <h3>Feedback Summary</h3>
                        <div class="rating">
                            <p class="card-number"><?php echo $avg_rating; ?></p>
                            <div class="stars">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <i class="fa<?php echo $i <= $avg_rating ? 's' : 'r'; ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="summary-card">
                    <div class="card-icon schedule-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-content">
                        <h3>Schedule Summary</h3>
                        <p class="card-text">Next Available: <strong><?php echo $next_available_day . ' ' . $next_available_time; ?></strong></p>
                        <p class="card-text">Total Patients This Week: <strong><?php echo $total_patients_week; ?></strong></p>
                    </div>
                </div>
            </section>

            <!-- Detailed Profile Information -->
            <section class="profile-details">
                <h2>Profile Information</h2>
                <div class="details-grid">
                    <div class="detail-item">
                        <span class="detail-label">Full Name:</span>
                        <span class="detail-value">Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Specialisation:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($doctor['SPECIALISATION_NAME']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date of Birth:</span>
                        <span class="detail-value"><?php echo date('F j, Y', strtotime($doctor['DOB'])); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date of Joining:</span>
                        <span class="detail-value"><?php echo date('F j, Y', strtotime($doctor['DOJ'])); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($doctor['EMAIL']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Phone:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($doctor['PHONE']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Gender:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($doctor['GENDER']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Username:</span>
                        <span class="detail-value"><?php echo htmlspecialchars($doctor['USERNAME']); ?></span>
                    </div>
                </div>
            </section>
        </main>
    </div>

</body>
</html>