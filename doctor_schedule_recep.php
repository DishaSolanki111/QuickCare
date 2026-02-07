<?php
session_start();

// Check if user is logged in as a receptionist
if (!isset($_SESSION['RECEPTIONIST_ID'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';
 $receptionist_id = $_SESSION['RECEPTIONIST_ID'];

// Fetch receptionist data from database
 $receptionist_query = mysqli_query($conn, "SELECT * FROM receptionist_tbl WHERE RECEPTIONIST_ID = '$receptionist_id'");
 $receptionist = mysqli_fetch_assoc($receptionist_query);

// Handle schedule creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_schedule'])) {
    $doctor_id = mysqli_real_escape_string($conn, $_POST['doctor_id']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);
    $available_day = mysqli_real_escape_string($conn, $_POST['available_day']);
    
    $create_query = "INSERT INTO doctor_schedule_tbl (DOCTOR_ID, RECEPTIONIST_ID, START_TIME, END_TIME, AVAILABLE_DAY) 
                     VALUES ('$doctor_id', '$receptionist_id', '$start_time', '$end_time', '$available_day')";
    
    if (mysqli_query($conn, $create_query)) {
        $success_message = "Schedule created successfully!";
    } else {
        $error_message = "Error creating schedule: " . mysqli_error($conn);
    }
}

// Handle schedule update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_schedule'])) {
    $schedule_id = mysqli_real_escape_string($conn, $_POST['schedule_id']);
    $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);
    $available_day = mysqli_real_escape_string($conn, $_POST['available_day']);
    
    $update_query = "UPDATE doctor_schedule_tbl SET 
                    START_TIME = '$start_time',
                    END_TIME = '$end_time',
                    AVAILABLE_DAY = '$available_day'
                    WHERE SCHEDULE_ID = '$schedule_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $success_message = "Schedule updated successfully!";
    } else {
        $error_message = "Error updating schedule: " . mysqli_error($conn);
    }
}

// Handle schedule deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_schedule'])) {
    $schedule_id = mysqli_real_escape_string($conn, $_POST['schedule_id']);
    
    $delete_query = "DELETE FROM doctor_schedule_tbl WHERE SCHEDULE_ID = '$schedule_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        $success_message = "Schedule deleted successfully!";
    } else {
        $error_message = "Error deleting schedule: " . mysqli_error($conn);
    }
}

// Fetch schedules data
 $schedules_query = mysqli_query($conn, "
    SELECT ds.*, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME
    FROM doctor_schedule_tbl ds
    JOIN doctor_tbl d ON ds.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME, ds.AVAILABLE_DAY
");

// Fetch doctors for dropdown
 $doctors_query = mysqli_query($conn, "
    SELECT d.*, s.SPECIALISATION_NAME 
    FROM doctor_tbl d
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME
");

// Handle AJAX request for doctor schedules
if ((isset($_POST['doctor_id']) || isset($_GET['doctor_id'])) && (isset($_POST['ajax']) || isset($_GET['ajax']))) {
    $doctor_id = mysqli_real_escape_string($conn, isset($_POST['doctor_id']) ? $_POST['doctor_id'] : $_GET['doctor_id']);
    
    $doctor_schedules_query = mysqli_query($conn, "
        SELECT * FROM doctor_schedule_tbl WHERE DOCTOR_ID = '$doctor_id' ORDER BY FIELD(AVAILABLE_DAY, 'MON', 'TUE', 'WED', 'THUR', 'FRI', 'SAT', 'SUN')
    ");
    
    $schedules = array();
    while ($schedule = mysqli_fetch_assoc($doctor_schedules_query)) {
        $schedules[] = $schedule;
    }
    
    header('Content-Type: application/json');
    echo json_encode($schedules);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Doctor Schedule - QuickCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --dark-blue: #072D44;
            --mid-blue: #064469;
            --soft-blue: #5790AB;
            --light-blue: #9CCDD8;
            --gray-blue: #D0D7E1;
            --white: #ffffff;
            --card-bg: #F6F9FB;
            --primary-color: #1a3a5f;
            --secondary-color: #3498db;
            --accent-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #17a2b8;
        }
        
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            font-weight: bold;
            background: #F5F8FA;
            display: flex;
        }
        
        .sidebar {
            width: 250px;
            background: #072D44;
            min-height: 100vh;
            color: white;
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
            color: #D0D7E1;
            text-decoration: none;
            font-size: 17px;
            border-left: 4px solid transparent;
        }

        .sidebar a:hover, .sidebar a.active {
            background: #064469;
            border-left: 4px solid #9CCDD8;
            color: white;
        }

        .logout-btn:hover{
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
        
        .main-content {
            margin-left: 240px;
            padding: 20px;
            width: calc(100% - 240px);
        }
        
        .topbar {
            background: white;
            padding: 15px 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .topbar h1 {
            margin: 0;
            color: #064469;
        }
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-blue);
            padding: 15px 20px;
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .btn-success {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-success:hover {
            background-color: #27ae60;
            border-color: #27ae60;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }
        
        .btn-warning:hover {
            background-color: #e67e22;
            border-color: #e67e22;
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
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .schedule-card {
            border-left: 4px solid var(--secondary-color);
            margin-bottom: 15px;
            padding: 15px;
            background-color: var(--white);
            border-radius: 0 8px 8px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .schedule-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .schedule-title {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .schedule-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .schedule-detail {
            display: flex;
            align-items: center;
            color: #666;
        }
        
        .schedule-detail i {
            margin-right: 8px;
            color: var(--secondary-color);
        }
        
        .day-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--secondary-color);
        }
        
        .schedule-actions {
            display: flex;
            gap: 10px;
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
            margin: 5% auto;
            padding: 20px;
            border: none;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
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
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar h2, .sidebar a span {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .schedule-details {
                grid-template-columns: 1fr;
            }
            
            .schedule-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include 'recept_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <h1>Manage Doctor Schedule</h1>
            <p>Welcome, <?php echo htmlspecialchars($receptionist['FIRST_NAME'] . ' ' . $receptionist['LAST_NAME']); ?></p>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Schedules Card -->
        <div class="card">
            <div class="card-header">
                <h3>Doctor Schedules</h3>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
                    <i class="bi bi-plus-circle"></i> Create Schedule
                </button>
            </div>
            <div class="card-body">
                <?php
                if (mysqli_num_rows($schedules_query) > 0) {
                    while ($schedule = mysqli_fetch_assoc($schedules_query)) {
                        ?>
                        <div class="schedule-card">
                            <div class="schedule-header">
                                <div class="schedule-title">
                                    Dr. <?php echo htmlspecialchars($schedule['DOC_FNAME'] . ' ' . $schedule['DOC_LNAME']); ?>
                                </div>
                                <span class="day-badge">
                                    <?php 
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
                                    echo $day_name;
                                    ?>
                                </span>
                            </div>
                            
                            <div class="schedule-details">
                                <div class="schedule-detail">
                                    <i class="bi bi-person"></i>
                                    <span><?php echo htmlspecialchars($schedule['SPECIALISATION_NAME']); ?></span>
                                </div>
                                <div class="schedule-detail">
                                    <i class="bi bi-clock"></i>
                                    <span><?php echo date('h:i A', strtotime($schedule['START_TIME'])); ?> - <?php echo date('h:i A', strtotime($schedule['END_TIME'])); ?></span>
                                </div>
                            </div>
                            
                            <div class="schedule-actions">
                                <button class="btn btn-warning btn-sm" onclick="editSchedule(<?php echo $schedule['SCHEDULE_ID']; ?>, '<?php echo $schedule['DOCTOR_ID']; ?>', '<?php echo $schedule['START_TIME']; ?>', '<?php echo $schedule['END_TIME']; ?>', '<?php echo $schedule['AVAILABLE_DAY']; ?>')">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="schedule_id" value="<?php echo $schedule['SCHEDULE_ID']; ?>">
                                    <button type="submit" name="delete_schedule" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this schedule?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="empty-state">
                        <i class="bi bi-calendar-x"></i>
                        <h4>No schedules found</h4>
                        <p>There are no doctor schedules created yet.</p>
                    </div>';
                }
                ?>
            </div>
        </div>
    </div>
    
    <!-- Create Schedule Modal -->
    <div class="modal fade" id="createScheduleModal" tabindex="-1" aria-labelledby="createScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createScheduleModalLabel">Create New Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="manage_doctor_schedule.php">
                        <input type="hidden" name="create_schedule" value="1">
                        
                        <div class="form-group">
                            <label for="doctor_id">Doctor</label>
                            <select class="form-control" id="doctor_id" name="doctor_id" required>
                                <option value="">Select Doctor</option>
                                <?php
                                if (mysqli_num_rows($doctors_query) > 0) {
                                    while ($doctor = mysqli_fetch_assoc($doctors_query)) {
                                        echo '<option value="' . $doctor['DOCTOR_ID'] . '">' . 
                                             'Dr. ' . htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']) . 
                                             ' (' . htmlspecialchars($doctor['SPECIALISATION_NAME']) . ')</option>';
                                    }
                                    // Reset the result pointer
                                    mysqli_data_seek($doctors_query, 0);
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="start_time">Start Time</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="end_time">End Time</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="available_day">Available Day</label>
                            <select class="form-control" id="available_day" name="available_day" required>
                                <option value="">Select Day</option>
                                <option value="MON">Monday</option>
                                <option value="TUE">Tuesday</option>
                                <option value="WED">Wednesday</option>
                                <option value="THUR">Thursday</option>
                                <option value="FRI">Friday</option>
                                <option value="SAT">Saturday</option>
                                <option value="SUN">Sunday</option>
                            </select>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check"></i> Create Schedule
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                <i class="bi bi-x"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Schedule Modal -->
    <div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="editScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editScheduleModalLabel">Edit Schedule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="manage_doctor_schedule.php">
                        <input type="hidden" id="edit_schedule_id" name="schedule_id">
                        <input type="hidden" name="update_schedule" value="1">
                        
                        <div class="form-group">
                            <label for="edit_doctor_id">Doctor</label>
                            <select class="form-control" id="edit_doctor_id" name="doctor_id" required>
                                <option value="">Select Doctor</option>
                                <?php
                                if (mysqli_num_rows($doctors_query) > 0) {
                                    while ($doctor = mysqli_fetch_assoc($doctors_query)) {
                                        echo '<option value="' . $doctor['DOCTOR_ID'] . '">' . 
                                             'Dr. ' . htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']) . 
                                             ' (' . htmlspecialchars($doctor['SPECIALISATION_NAME']) . ')</option>';
                                    }
                                    // Reset the result pointer
                                    mysqli_data_seek($doctors_query, 0);
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="edit_start_time">Start Time</label>
                                <input type="time" class="form-control" id="edit_start_time" name="start_time" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="edit_end_time">End Time</label>
                                <input type="time" class="form-control" id="edit_end_time" name="end_time" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_available_day">Available Day</label>
                            <select class="form-control" id="edit_available_day" name="available_day" required>
                                <option value="">Select Day</option>
                                <option value="MON">Monday</option>
                                <option value="TUE">Tuesday</option>
                                <option value="WED">Wednesday</option>
                                <option value="THUR">Thursday</option>
                                <option value="FRI">Friday</option>
                                <option value="SAT">Saturday</option>
                                <option value="SUN">Sunday</option>
                            </select>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check"></i> Update Schedule
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                <i class="bi bi-x"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to edit schedule
        function editSchedule(scheduleId, doctorId, startTime, endTime, availableDay) {
            document.getElementById('edit_schedule_id').value = scheduleId;
            document.getElementById('edit_doctor_id').value = doctorId;
            document.getElementById('edit_start_time').value = startTime;
            document.getElementById('edit_end_time').value = endTime;
            document.getElementById('edit_available_day').value = availableDay;
            
            // Show the modal
            const editModal = new bootstrap.Modal(document.getElementById('editScheduleModal'));
            editModal.show();
        }
    </script>
</body>
</html>