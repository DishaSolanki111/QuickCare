<?php
session_start();

// Check if user is logged in and is a doctor
if (
    !isset($_SESSION['LOGGED_IN']) ||
    $_SESSION['LOGGED_IN'] !== true ||
    !isset($_SESSION['USER_TYPE']) ||
    $_SESSION['USER_TYPE'] !== 'doctor' ||
    !isset($_SESSION['DOCTOR_ID'])
) {
    header("Location: login.php");
    exit();
}

// Include database connection
include 'config.php';

 $doctor_id = $_SESSION['DOCTOR_ID'];
 $message = '';
 $edit_mode = false;

// Handle form submission for updating schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_schedule'])) {
    // Delete existing schedule entries for this doctor
    $delete_sql = "DELETE FROM doctor_schedule_tbl WHERE DOCTOR_ID = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $doctor_id);
    $delete_stmt->execute();
    $delete_stmt->close();
    
    // Insert new schedule entries
    $days = ['MON', 'TUE', 'WED', 'THUR', 'FRI', 'SAT', 'SUN'];
    $receptionist_id = 1; // Default receptionist ID, you might want to get this from session or another source
    
    $insert_sql = "INSERT INTO doctor_schedule_tbl (DOCTOR_ID, RECEPTIONIST_ID, START_TIME, END_TIME, AVAILABLE_DAY) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    
    foreach ($days as $day) {
        $available = isset($_POST[$day . '_available']) ? 1 : 0;
        
        if ($available) {
            $start_time = $_POST[$day . '_start'] . ':00';
            $end_time = $_POST[$day . '_end'] . ':00';
            
            $insert_stmt->bind_param("iisss", $doctor_id, $receptionist_id, $start_time, $end_time, $day);
            $insert_stmt->execute();
        }
    }
    
    $insert_stmt->close();
    $message = "Schedule updated successfully!";
}

// Fetch doctor's current schedule
 $schedule_sql = "SELECT * FROM doctor_schedule_tbl WHERE DOCTOR_ID = ?";
 $schedule_stmt = $conn->prepare($schedule_sql);
 $schedule_stmt->bind_param("i", $doctor_id);
 $schedule_stmt->execute();
 $schedule_result = $schedule_stmt->get_result();

// Create an associative array to store schedule by day
 $schedule = [
    'MON' => null,
    'TUE' => null,
    'WED' => null,
    'THUR' => null,
    'FRI' => null,
    'SAT' => null,
    'SUN' => null
];

while ($row = $schedule_result->fetch_assoc()) {
    $day = $row['AVAILABLE_DAY'];
    $schedule[$day] = $row;
}

 $schedule_stmt->close();
 $conn->close();

// Day names for display
 $day_names = [
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
<html>
<head>
    <title>Doctor Schedule Management</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
    :root {
        --dark-blue: #072D44;
        --mid-blue: #064469;
        --soft-blue: #5790AB;
        --light-blue: #9CCDD8;
        --gray-blue: #D0D7E1;
        --white: #ffffff;
        --card-bg: #F6F9FB;
        --primary-color: #3498db;
        --dark-color: #2c3e50;
        --accent-color: #2ecc71;
        --danger-color: #e74c3c;
    }
    
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #F5F8FA;
        line-height: 1.6;
    }
    
    .main-content {
        padding: 20px;
        background-color: #f9f9f9;
        min-height: 100vh;
        margin-left: 240px;
    }
    
    .container {
        max-width: 900px;
        margin: 0 auto;
    }
    
    .sidebar {
        width: 250px;
        background: #072D44;
        min-height: 100vh;
        color: var(--white);
        padding-top: 30px;
        position: fixed;
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 40px;
        color: #9CCDD8;
    }
    
    .sidebar a {
        display: block;
        padding: 15px 25px;
        text-decoration: none;
        color: var(--gray-blue);
        font-size: 15px;
        margin: 4px 0;
        transition: .2s;
    }
    
    .sidebar a:hover, .sidebar a.active {
        background: #064469;
        border-left: 4px solid #9CCDD8;
        color: white;
    }
    
    .logout-btn:hover {
        background-color: var(--light-blue);
    }
    
    .logout-btn {
        display: block;
        width: 80%;
        margin: 20px auto 0 auto;
        padding: 10px;
        background-color: var(--soft-blue);
        color: var(--white);
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        text-align: center;
        transition: background-color 0.3s;
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
    
    .schedule-day {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .schedule-day:last-child {
        border-bottom: none;
    }
    
    .day-name {
        font-weight: 600;
        color: var(--dark-color);
        width: 100px;
    }
    
    .day-time {
        color: #666;
    }
    
    .day-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-available {
        background-color: rgba(46, 204, 113, 0.2);
        color: var(--accent-color);
    }
    
    .status-unavailable {
        background-color: rgba(231, 76, 60, 0.2);
        color: var(--danger-color);
    }
    
    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s;
    }
    
    .btn-primary {
        background-color: var(--primary-color);
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
    
    .btn-secondary {
        background-color: #95a5a6;
        color: white;
    }
    
    .btn-secondary:hover {
        background-color: #7f8c8d;
    }
    
    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 4px;
    }
    
    .alert-success {
        background-color: rgba(46, 204, 113, 0.2);
        color: var(--accent-color);
        border: 1px solid rgba(46, 204, 113, 0.3);
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
    }
    
    .form-control {
        width: 100%;
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    
    .checkbox-group {
        display: flex;
        align-items: center;
    }
    
    .checkbox-group input {
        margin-right: 10px;
    }
    
    .time-inputs {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .time-inputs input {
        width: 80px;
    }
    
    .edit-form {
        display: none;
    }
    
    .edit-form.active {
        display: block;
    }
    
    .view-mode {
        display: block;
    }
    
    .view-mode.hidden {
        display: none;
    }
    
    .schedule-day-form {
        padding: 15px;
        background-color: #f9f9f9;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    
    .schedule-day-form-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .time-range {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    </style>
</head>
<body>
    <div class="sidebar">
        <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
        <h2>QuickCare</h2>

        <a href="doctor_dashboard.php" >Dashboard</a>
        <a href="d_profile.php">My Profile</a>
        <a href="manage_schedule_doctor.php" class="active">Manage Schedule</a>
        <a href="appointment_doctor.php">Manage Appointments</a>
        <a href="manage_prescriptions.php">Manage Prescription</a>
        <a href="#">View Medicine</a>
        <a href="doctor_feedback.php">View Feedback</a>
        <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
    </div>

    <div class="main-content">
        <div class="container">
            <h3 style="margin-bottom: 20px;">Weekly Schedule</h3>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-success">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <div class="schedule-card">
                <div class="schedule-header">
                    <h3>Regular Schedule</h3>
                    <button class="btn btn-primary" id="editBtn" onclick="toggleEditMode()">
                        <i class="fas fa-edit"></i> Edit Schedule
                    </button>
                </div>
                
                <!-- View Mode -->
                <div id="viewMode" class="view-mode">
                    <?php foreach (['MON', 'TUE', 'WED', 'THUR', 'FRI', 'SAT', 'SUN'] as $day): ?>
                        <div class="schedule-day">
                            <div class="day-name"><?php echo $day_names[$day]; ?></div>
                            <div class="day-time">
                                <?php 
                                if ($schedule[$day]) {
                                    echo date('h:i A', strtotime($schedule[$day]['START_TIME'])) . ' - ' . 
                                         date('h:i A', strtotime($schedule[$day]['END_TIME']));
                                } else {
                                    echo 'Not Available';
                                }
                                ?>
                            </div>
                            <div class="day-status <?php echo $schedule[$day] ? 'status-available' : 'status-unavailable'; ?>">
                                <?php echo $schedule[$day] ? 'Available' : 'Unavailable'; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Edit Mode -->
                <form method="post" id="editMode" class="edit-form">
                    <input type="hidden" name="update_schedule" value="1">
                    
                    <?php foreach (['MON', 'TUE', 'WED', 'THUR', 'FRI', 'SAT', 'SUN'] as $day): ?>
                        <div class="schedule-day-form">
                            <div class="schedule-day-form-header">
                                <h4><?php echo $day_names[$day]; ?></h4>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="<?php echo $day; ?>_available" name="<?php echo $day; ?>_available" 
                                           <?php echo $schedule[$day] ? 'checked' : ''; ?> 
                                           onchange="toggleTimeInputs('<?php echo $day; ?>')">
                                    <label for="<?php echo $day; ?>_available">Available</label>
                                </div>
                            </div>
                            
                            <div class="time-range" id="<?php echo $day; ?>_time_inputs" 
                                 style="<?php echo $schedule[$day] ? '' : 'display: none;'; ?>">
                                <input type="time" name="<?php echo $day; ?>_start" 
                                       value="<?php echo $schedule[$day] ? date('H:i', strtotime($schedule[$day]['START_TIME'])) : ''; ?>"
                                       class="form-control">
                                <span>to</span>
                                <input type="time" name="<?php echo $day; ?>_end" 
                                       value="<?php echo $schedule[$day] ? date('H:i', strtotime($schedule[$day]['END_TIME'])) : ''; ?>"
                                       class="form-control">
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <div style="margin-top: 20px; display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="toggleEditMode()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleEditMode() {
            const viewMode = document.getElementById('viewMode');
            const editMode = document.getElementById('editMode');
            const editBtn = document.getElementById('editBtn');
            
            if (viewMode.classList.contains('hidden')) {
                // Switch to view mode
                viewMode.classList.remove('hidden');
                editMode.classList.remove('active');
                editBtn.innerHTML = '<i class="fas fa-edit"></i> Edit Schedule';
            } else {
                // Switch to edit mode
                viewMode.classList.add('hidden');
                editMode.classList.add('active');
                editBtn.innerHTML = '<i class="fas fa-eye"></i> View Schedule';
            }
        }
        
        function toggleTimeInputs(day) {
            const checkbox = document.getElementById(day + '_available');
            const timeInputs = document.getElementById(day + '_time_inputs');
            
            if (checkbox.checked) {
                timeInputs.style.display = 'flex';
            } else {
                timeInputs.style.display = 'none';
            }
        }
    </script>
</body>
</html>