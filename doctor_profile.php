<?php
session_start(); // Start session to check login status
include 'config.php';
include 'header.php';
if (!isset($_GET['id'])) {
    die("Doctor ID missing.");
}

 $doctor_id = intval($_GET['id']);

/* =========================
   FETCH DOCTOR DETAILS
========================= */
 $doctor_sql = "
    SELECT 
        d.DOCTOR_ID,
        d.FIRST_NAME,
        d.LAST_NAME,
        d.PHONE,
        d.EMAIL,
        d.PROFILE_IMAGE,
        d.DOB,
        d.DOJ,
        d.GENDER,
        d.EDUCATION,
        s.SPECIALISATION_NAME
    FROM doctor_tbl d
    JOIN specialisation_tbl s 
        ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE d.DOCTOR_ID = $doctor_id
";

 $doctor_res = mysqli_query($conn, $doctor_sql);
 $doctor = mysqli_fetch_assoc($doctor_res);

if (!$doctor) {
    die("Doctor not found.");
}

/* =========================
   FETCH DOCTOR SCHEDULE
========================= */
 $schedule_sql = "
    SELECT AVAILABLE_DAY, START_TIME, END_TIME
    FROM doctor_schedule_tbl
    WHERE DOCTOR_ID = $doctor_id
    ORDER BY FIELD(AVAILABLE_DAY,'MON','TUE','WED','THU','FRI','SAT','SUN')
";

 $schedule_res = mysqli_query($conn, $schedule_sql);

/* =========================
   PROFILE IMAGE PATH
========================= */
 $image_path = !empty($doctor['PROFILE_IMAGE']) 
    ? $doctor['PROFILE_IMAGE'] 
    : 'imgs/default.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Blue color scheme */
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
            padding-top: 80px; /* Account for fixed header */
        }

        .main-container {
            display: flex;
            min-height: 100vh;
        }

        .content-wrapper {
            flex: 1;
            margin-left: 250px; /* Width of the sidebar */
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .profile-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            display: flex;
            flex-wrap: wrap;
        }

        .left {
            width: 35%;
            background: linear-gradient(135deg, var(--light-blue) 0%, #d4e6ff 100%);
            text-align: center;
            padding: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .left img {
            width: 220px;
            height: 220px;
            object-fit: cover;
            border-radius: 12px;
            border: 5px solid white;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .left img:hover {
            transform: scale(1.05);
        }

        .left h2 {
            margin-top: 15px;
            color: var(--dark-blue);
            font-size: 1.8rem;
            font-weight: 700;
        }

        .badge {
            display: inline-block;
            background: var(--primary-blue);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            margin-top: 15px;
            font-weight: 500;
            font-size: 1rem;
        }

        /* Book Now Button */
        .book-now-btn {
            margin-top: 25px;
            padding: 14px 40px;
            background: var(--primary-blue);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 10px rgba(26, 115, 232, 0.3);
        }

        .book-now-btn:hover {
            background: var(--dark-blue);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(26, 115, 232, 0.4);
        }

        .book-now-btn i {
            font-size: 1.2rem;
        }

        .right {
            width: 65%;
            padding: 30px;
        }

        .section-title {
            margin-top: 20px;
            font-weight: 700;
            color: var(--dark-blue);
            font-size: 1.3rem;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--light-blue);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary-blue);
        }

        .contact-info {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 25px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--light-blue);
            padding: 12px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .contact-item:hover {
            background: #d4e6ff;
            transform: translateY(-3px);
        }

        .contact-item i {
            color: var(--primary-blue);
            font-size: 1.2rem;
        }

        .schedule-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .schedule-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--light-blue);
            padding: 12px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .schedule-item:hover {
            background: #d4e6ff;
            transform: translateX(5px);
        }

        .schedule-day {
            font-weight: 600;
            color: var(--dark-blue);
        }

        .schedule-time {
            color: var(--primary-blue);
            font-weight: 500;
        }

        /* CALENDAR POPUP STYLES */
        #calendarModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            z-index: 999;
        }

        #calendarBox {
            background: white;
            width: 90%;
            max-width: 500px;
            height: 80vh;
            max-height: 600px;
            border-radius: 12px;
            position: relative;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .calendar-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calendar-header h3 {
            margin: 0;
            font-size: 1.3rem;
        }

        .calendar-header span {
            font-size: 1.5rem;
            cursor: pointer;
            background: rgba(255, 255, 255, 0.2);
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .calendar-header span:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .calendar-content {
            flex-grow: 1;
            overflow: hidden;
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .content-wrapper {
                margin-left: 70px; /* Adjusted for collapsed sidebar */
            }
            
            .profile-card {
                flex-direction: column;
            }
            
            .left, .right {
                width: 100%;
            }
            
            .left {
                padding: 20px;
            }
            
            .left img {
                width: 180px;
                height: 180px;
            }
        }

        @media (max-width: 768px) {
            .content-wrapper {
                margin-left: 0; /* No sidebar on mobile */
            }
            
            .container {
                padding: 20px 15px;
            }
            
            .left, .right {
                padding: 20px;
            }
            
            .left img {
                width: 150px;
                height: 150px;
            }
            
            .left h2 {
                font-size: 1.5rem;
            }
            
            .contact-info {
                flex-direction: column;
                gap: 10px;
            }

            .book-now-btn {
                padding: 12px 30px;
                font-size: 1rem;
            }
        }

        @media (max-width: 576px) {
            .left img {
                width: 120px;
                height: 120px;
            }
            
            .section-title {
                font-size: 1.1rem;
            }
        }

        /* Animation for elements */
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

        .profile-card {
            animation: fadeInUp 0.6s ease forwards;
        }
    </style>
</head>
<body>
    <div class="main-container">
        <?php include 'doctor_sidebar.php'; ?>
        
        <div class="content-wrapper">
            <div class="container">
                <div class="profile-card">
                    <!-- LEFT -->
                    <div class="left">
                        <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Doctor">
                        <h2>Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'].' '.$doctor['LAST_NAME']); ?></h2>
                        <span class="badge"><?php echo htmlspecialchars($doctor['SPECIALISATION_NAME']); ?></span>
                        
                        <!-- Book Now Button -->
                        <button class="book-now-btn" onclick="handleBooking(<?php echo $doctor_id; ?>)">
                            <i class="fas fa-calendar-check"></i> Book Appointment
                        </button>
                    </div>

                    <!-- RIGHT -->
                    <div class="right">
                        <div class="section-title">
                            <i class="fas fa-user"></i> CONTACT
                        </div>
                        <div class="contact-info">
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <span><?php echo htmlspecialchars($doctor['PHONE']); ?></span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo htmlspecialchars($doctor['EMAIL']); ?></span>
                            </div>
                        </div>

                        <div class="section-title">
                            <i class="fas fa-info-circle"></i> PERSONAL INFORMATION
                        </div>
                        <div class="contact-info">
                            <div class="contact-item">
                                <i class="fas fa-venus-mars"></i>
                                <span><?php echo htmlspecialchars($doctor['GENDER']); ?></span>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-birthday-cake"></i>
                                <span><?php echo date('d M Y', strtotime($doctor['DOB'])); ?></span>
                            </div>
                        </div>

                        <div class="section-title">
                            <i class="fas fa-graduation-cap"></i> EDUCATION
                        </div>
                        <div class="contact-info">
                            <div class="contact-item">
                                <i class="fas fa-user-graduate"></i>
                                <span><?php echo htmlspecialchars($doctor['EDUCATION']); ?></span>
                            </div>
                        </div>

                        <div class="section-title">
                            <i class="fas fa-calendar-alt"></i> AVAILABLE SCHEDULE
                        </div>
                        <div class="schedule-container">
                            <?php
                            if (mysqli_num_rows($schedule_res) > 0) {
                                while ($row = mysqli_fetch_assoc($schedule_res)) {
                                    echo "<div class='schedule-item'>
                                            <span class='schedule-day'>{$row['AVAILABLE_DAY']}</span>
                                            <span class='schedule-time'>" . substr($row['START_TIME'],0,5) . " - " . substr($row['END_TIME'],0,5) . "</span>
                                          </div>";
                                }
                            } else {
                                echo "<p>No schedule available</p>";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CALENDAR POPUP -->
    <div id="calendarModal">
        <div id="calendarBox">
            <div class="calendar-header">
                <h3>Select Appointment Date</h3>
                <span onclick="closeCalendar()">&times;</span>
            </div>
            <div class="calendar-content">
                <iframe id="calendarFrame"></iframe>
            </div>
        </div>
    </div>

    <script>
        // Check if user is logged in (passed from PHP)
        const isLoggedIn = <?php echo isset($_SESSION['LOGGED_IN']) && $_SESSION['LOGGED_IN'] ? 'true' : 'false'; ?>;
        
        function handleBooking(doctorId) {
            if (isLoggedIn) {
                // User is logged in, open calendar directly
                openCalendar(doctorId);
            } else {
                // User is not logged in, redirect to login with standalone parameter
                window.location.href = 'login.php?standalone=true';
            }
        }
        
        function openCalendar(id){
            document.getElementById("calendarFrame").src = "calendar.php?doctor_id=" + id;
            document.getElementById("calendarModal").style.display = "flex";
            document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
        }
        
        function closeCalendar(){
            document.getElementById("calendarModal").style.display = "none";
            document.getElementById("calendarFrame").src = "";
            document.body.style.overflow = 'auto'; // Enable scrolling back
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById("calendarModal");
            if (event.target == modal) {
                closeCalendar();
            }
        }
    </script>
</body>
</html>