<?php
session_start();

// Check if user is logged in as a patient
if (!isset($_SESSION['PATIENT_ID'])) {
    header("Location: login_for_all.php");
    exit;
}

include 'config.php';
 $patient_id = $_SESSION['PATIENT_ID'];

// Fetch patient data from database
 $patient_query = mysqli_query($conn, "SELECT * FROM patient_tbl WHERE PATIENT_ID = '$patient_id'");
 $patient = mysqli_fetch_assoc($patient_query);

// Get selected doctor and day from query parameters
 $selected_doctor = isset($_GET['doctor']) ? mysqli_real_escape_string($conn, $_GET['doctor']) : '';
 $selected_day = isset($_GET['day']) ? mysqli_real_escape_string($conn, $_GET['day']) : '';

// Fetch doctors for filter
 $doctors_query = mysqli_query($conn, "
    SELECT d.*, s.SPECIALISATION_NAME 
    FROM doctor_tbl d
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME
");

// Build schedule query
 $schedule_query = "
    SELECT ds.*, d.FIRST_NAME, d.LAST_NAME, s.SPECIALISATION_NAME 
    FROM doctor_schedule_tbl ds
    JOIN doctor_tbl d ON ds.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE 1=1
";

if (!empty($selected_doctor)) {
    $schedule_query .= " AND ds.DOCTOR_ID = '$selected_doctor'";
}

if (!empty($selected_day)) {
    $schedule_query .= " AND ds.AVAILABLE_DAY = '$selected_day'";
}

 $schedule_query .= " ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME, ds.AVAILABLE_DAY";

 $schedule_result = mysqli_query($conn, $schedule_query);

// Days of week for filter
 $days = [
    'MON' => 'Monday',
    'TUE' => 'Tuesday',
    'WED' => 'Wednesday',
    'THUR' => 'Thursday',
    'FRI' => 'Friday',
    'SAT' => 'Saturday',
    'SUN' => 'Sunday'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Schedule - QuickCare</title>
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
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #D0D7E1;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* Container for the entire layout */
        .container {
            display: flex;
            width: 100%;
            height: 100%;
        }
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
            height: 100vh;
            overflow-y: auto;
        }
        /* Custom scrollbar for main content */
        .main-content::-webkit-scrollbar {
            width: 8px;
        }

        .main-content::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .main-content::-webkit-scrollbar-thumb {
            background: #5790AB;
            border-radius: 4px;
        }

        .main-content::-webkit-scrollbar-thumb:hover {
            background: #064469;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }
        
        .welcome-msg {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .user-actions {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .filter-section {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .filter-form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .form-group {
            flex: 1;
            min-width: 200px;
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
        
        .schedule-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .schedule-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .schedule-header h3 {
            color: var(--primary-color);
        }
        
        .schedule-details {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .schedule-item {
            display: flex;
            align-items: center;
            color: #666;
        }
        
        .schedule-item i {
            margin-right: 10px;
            color: var(--secondary-color);
            font-size: 18px;
        }
        
        .day-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--secondary-color);
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #777;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 200px;
            }
            
            .filter-form {
                flex-direction: column;
            }
            
            .form-group {
                width: 100%;
            }
        }
        
        @media (max-width: 768px) {
            .schedule-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Import Sidebar -->
        <?php include 'patient_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="welcome-msg">Doctor Schedule</div>
                <div class="user-actions">
                    <div class="user-dropdown">
                        <div class="user-avatar"><?php echo strtoupper(substr($patient['FIRST_NAME'], 0, 1) . substr($patient['LAST_NAME'], 0, 1)); ?></div>
                        <span><?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></span>
                        <i class="fas fa-chevron-down" style="margin-left: 8px;"></i>
                    </div>
                </div>
            </div>
            
            <!-- Filter Section -->
            <div class="filter-section">
                <form method="GET" action="doctor_schedule.php" class="filter-form">
                    <div class="form-group">
                        <label for="doctor">Filter by Doctor</label>
                        <select class="form-control" id="doctor" name="doctor">
                            <option value="">All Doctors</option>
                            <?php
                            if (mysqli_num_rows($doctors_query) > 0) {
                                while ($doctor = mysqli_fetch_assoc($doctors_query)) {
                                    $selected = ($selected_doctor == $doctor['DOCTOR_ID']) ? 'selected' : '';
                                    echo '<option value="' . $doctor['DOCTOR_ID'] . '" ' . $selected . '>' . 
                                         'Dr. ' . htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']) . 
                                         ' (' . htmlspecialchars($doctor['SPECIALISATION_NAME']) . ')</option>';
                                }
                                // Reset the result pointer
                                mysqli_data_seek($doctors_query, 0);
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="day">Filter by Day</label>
                        <select class="form-control" id="day" name="day">
                            <option value="">All Days</option>
                            <?php
                            foreach ($days as $key => $value) {
                                $selected = ($selected_day == $key) ? 'selected' : '';
                                echo '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group" style="display: flex; align-items: flex-end;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Apply Filters
                        </button>
                        <a href="doctor_schedule.php" class="btn" style="margin-left: 10px; background-color: #6c757d; color: white;">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Schedule Cards -->
            <?php
            if (mysqli_num_rows($schedule_result) > 0) {
                $current_doctor_id = 0;
                $doctor_schedule = [];
                
                // Group schedules by doctor
                while ($schedule = mysqli_fetch_assoc($schedule_result)) {
                    if ($current_doctor_id != $schedule['DOCTOR_ID']) {
                        // If we have a previous doctor, display their schedule
                        if ($current_doctor_id != 0) {
                            displayDoctorSchedule($doctor_schedule);
                        }
                        
                        // Start a new doctor
                        $current_doctor_id = $schedule['DOCTOR_ID'];
                        $doctor_schedule = [
                            'doctor_id' => $schedule['DOCTOR_ID'],
                            'first_name' => $schedule['FIRST_NAME'],
                            'last_name' => $schedule['LAST_NAME'],
                            'specialization' => $schedule['SPECIALISATION_NAME'],
                            'schedules' => []
                        ];
                    }
                    
                    // Add this schedule to the current doctor
                    $doctor_schedule['schedules'][] = $schedule;
                }
                
                // Display the last doctor's schedule
                if ($current_doctor_id != 0) {
                    displayDoctorSchedule($doctor_schedule);
                }
            } else {
                echo '<div class="empty-state">
                    <i class="far fa-calendar-times"></i>
                    <p>No schedules found matching your criteria</p>
                </div>';
            }
            ?>
        </div>
    </div>
    
    <?php
    function displayDoctorSchedule($doctor_schedule) {
        echo '<div class="schedule-card">
            <div class="schedule-header">
                <h3>Dr. ' . htmlspecialchars($doctor_schedule['first_name'] . ' ' . $doctor_schedule['last_name']) . '</h3>
                <span>' . htmlspecialchars($doctor_schedule['specialization']) . '</span>
            </div>
            
            <div class="schedule-details">';
            
        foreach ($doctor_schedule['schedules'] as $schedule) {
            $day_name = '';
            switch($schedule['AVAILABLE_DAY']) {
                case 'MON': $day_name = 'Monday'; break;
                case 'TUE': $day_name = 'Tuesday'; break;
                case 'WED': $day_name = 'Wednesday'; break;
                case 'THUR': $day_name = 'Thursday'; break;
                case 'FRI': $day_name = 'Friday'; break;
                case 'SAT': $day_name = 'Saturday'; break;
                case 'SUN': $day_name = 'Sunday'; break;
            }
            
            echo '<div><div class="schedule-item">
                <i class="far fa-calendar"></i>
                <span class="day-badge">' . $day_name . '</span>
            </div>
            
            <div class="schedule-item">
                <i class="far fa-clock"></i>
                <span>' . date('h:i A', strtotime($schedule['START_TIME'])) . ' - ' . date('h:i A', strtotime($schedule['END_TIME'])) . '</span>
            </div>
            
            <div class="schedule-item" style="margin-top: 15px;">
                <button class="btn btn-success" onclick="bookAppointment(' . $schedule['DOCTOR_ID'] . ', \'' . $schedule['AVAILABLE_DAY'] . '\')">
                    <i class="fas fa-calendar-plus"></i> Book Appointment
                </button>
            </div></div>';
        }
        
        echo '</div>
        </div>';
    }
    ?>
    
    <script>
        function bookAppointment(doctorId, day) {
            // Redirect to appointment booking page with pre-selected doctor and day
            window.location.href = 'payment.php?doctor=' + doctorId + '&day=' + day;
        }
    </script>
</body>
</html>