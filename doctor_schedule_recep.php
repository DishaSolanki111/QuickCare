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

// Fetch doctors with their schedules grouped
 $doctors_with_schedules = [];
 $doctors_query = mysqli_query($conn, "
    SELECT d.DOCTOR_ID, d.FIRST_NAME, d.LAST_NAME, d.PROFILE_IMAGE, s.SPECIALISATION_NAME
    FROM doctor_tbl d
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME, d.LAST_NAME
");

if (mysqli_num_rows($doctors_query) > 0) {
    while ($doctor = mysqli_fetch_assoc($doctors_query)) {
        $doctor_id = $doctor['DOCTOR_ID'];
        
        // Get schedules for this doctor
        $schedules_query = mysqli_query($conn, "
            SELECT * FROM doctor_schedule_tbl 
            WHERE DOCTOR_ID = '$doctor_id' 
            ORDER BY FIELD(AVAILABLE_DAY, 'MON', 'TUE', 'WED', 'THUR', 'FRI', 'SAT', 'SUN')
        ");
        
        $schedules = [];
        while ($schedule = mysqli_fetch_assoc($schedules_query)) {
            $schedules[] = $schedule;
        }
        
        $doctors_with_schedules[] = [
            'doctor' => $doctor,
            'schedules' => $schedules
        ];
    }
}

// Handle AJAX request for doctor schedules
if (isset($_POST['doctor_id']) && isset($_POST['ajax'])) {
    $doctor_id = mysqli_real_escape_string($conn, $_POST['doctor_id']);
    
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        
        .sidebar {
            width: 250px;
            background: var(--dark-blue);
            min-height: 100vh;
            color: white;
            padding-top: 30px;
            position: fixed;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            color: var(--light-blue);
            font-size: 24px;
        }

        .sidebar a {
            display: block;
            padding: 15px 25px;
            color: var(--gray-blue);
            text-decoration: none;
            font-size: 16px;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }

        .sidebar a:hover, .sidebar a.active {
            background: var(--mid-blue);
            border-left: 4px solid var(--light-blue);
            color: var(--white);
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
            margin-left: 250px;
            padding: 30px;
            width: calc(100% - 250px);
        }
        
        .page-header {
            background: var(--white);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .page-title {
            font-size: 28px;
            font-weight: 600;
            color: var(--dark-blue);
            margin: 0;
        }
        
        .doctor-schedule-card {
            background: var(--white);
            border-radius: 15px;
            padding: 0;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .doctor-schedule-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }
        
        .doctor-header {
            background: var(--white);
            color: var(--primary-color);
            padding: 20px 25px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .doctor-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid var(--light-blue);
            object-fit: cover;
        }
        
        .doctor-info h3 {
            margin: 0;
            font-size: 22px;
            font-weight: 600;
        }
        
        .doctor-specialization {
            display: inline-block;
            background: rgba(72, 41, 112, 0.2);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 14px;
            margin-top: 8px;
        }
        
        .schedule-content {
            padding: 25px;
        }
        
        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
      
        }
        
        .day-schedule {
            background: var(--card-bg);
            border-radius: 10px;
            padding: 15px;
            border-left: 4px solid var(--secondary-color);
            transition: all 0.3s ease;
        }
        
        .day-schedule:hover {
            background: #e8f4f8;
            transform: scale(1.02);
        }
        
        .day-name {
            font-weight: 600;
            color: var(--dark-blue);
            margin-bottom: 10px;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .day-name i {
            color: var(--secondary-color);
        }
        
        .time-range {
            color: #555;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .time-range i {
            color: var(--accent-color);
            font-size: 20px;
        }
        
        .schedule-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .btn-edit, .btn-delete {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        
        .btn-edit {
            background: var(--warning-color);
            color: var(--white);
        }
        
        .btn-edit:hover {
            background: #e67e22;
        }
        
        .btn-delete {
            background: var(--danger-color);
            color: var(--white);
        }
        
        .btn-delete:hover {
            background: #c0392b;
        }
        
        .no-schedule {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-style: italic;
        }
        
        .no-schedule i {
            font-size: 24px;
            margin-bottom: 10px;
            display: block;
        }
        
        .add-schedule-btn {
            background: var(--accent-color);
            color: var(--white);
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .add-schedule-btn:hover {
            background: #27ae60;
            transform: translateY(-2px);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--mid-blue) 100%);
            color: var(--white);
            border-radius: 15px 15px 0 0;
            border: none;
        }
        
        .modal-title {
            font-weight: 600;
        }
        
        .btn-close {
            filter: brightness(0) invert(1);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-blue);
            margin-bottom: 8px;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-primary {
            background: var(--secondary-color);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: var(--accent-color);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            background: #27ae60;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: var(--danger-color);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-danger:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: var(--white);
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .empty-state i {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 20px;
        }
        
        .empty-state h4 {
            color: var(--dark-blue);
            margin-bottom: 10px;
        }
        
        .empty-state p {
            color: #6c757d;
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
                padding: 20px;
            }
            
            .page-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
            
            
            
            .doctor-header {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <?php include 'recept_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <?php include 'receptionist_header.php'; ?>
        
        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i>
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-title">
                <i class="bi bi-calendar-week"></i> Doctor Schedules
            </h1>
            <button class="add-schedule-btn" data-bs-toggle="modal" data-bs-target="#createScheduleModal">
                <i class="bi bi-plus-circle"></i> Create New Schedule
            </button>
        </div>
        
        <!-- Doctor Schedules -->
        <?php if (!empty($doctors_with_schedules)): ?>
            <?php foreach ($doctors_with_schedules as $doctor_data): ?>
                <div class="doctor-schedule-card">
                    <div class="doctor-header">
                        <img src="<?php echo !empty($doctor_data['doctor']['PROFILE_IMAGE']) ? $doctor_data['doctor']['PROFILE_IMAGE'] : 'https://picsum.photos/seed/doctor' . $doctor_data['doctor']['DOCTOR_ID'] . '/70/70.jpg'; ?>" 
                             alt="Doctor" class="doctor-avatar">
                        <div class="doctor-info">
                            <h3>Dr. <?php echo htmlspecialchars($doctor_data['doctor']['FIRST_NAME'] . ' ' . $doctor_data['doctor']['LAST_NAME']); ?></h3>
                            <span class="doctor-specialization">
                                <i class="bi bi-award"></i> 
                                <?php echo htmlspecialchars($doctor_data['doctor']['SPECIALISATION_NAME']); ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="schedule-content">
                        <?php if (!empty($doctor_data['schedules'])): ?>
                            <div class="schedule-grid">
                                <?php foreach ($doctor_data['schedules'] as $schedule): ?>
                                    <div class="day-schedule">
                                        <div class="day-name">
                                            <?php 
                                            $day_icons = [
                                                'MON' => 'bi-calendar-week',
                                                'TUE' => 'bi-calendar2-week',
                                                'WED' => 'bi-calendar3',
                                                'THUR' => 'bi-calendar4',
                                                'FRI' => 'bi-calendar5',
                                                'SAT' => 'bi-calendar6',
                                                'SUN' => 'bi-calendar'
                                            ];
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
                                            <i class="bi <?php echo $day_icons[$schedule['AVAILABLE_DAY']]; ?>"></i>
                                            <?php echo $day_names[$schedule['AVAILABLE_DAY']]; ?>
                                        </div>
                                        <div class="time-range">
                                            <i class="bi bi-clock-fill"></i>
                                            <?php echo date('h:i A', strtotime($schedule['START_TIME'])); ?> - 
                                            <?php echo date('h:i A', strtotime($schedule['END_TIME'])); ?>
                                        </div>
                                        <div class="schedule-actions">
                                            <button class="btn-edit" onclick="editSchedule(<?php echo $schedule['SCHEDULE_ID']; ?>, '<?php echo $schedule['DOCTOR_ID']; ?>', '<?php echo $schedule['START_TIME']; ?>', '<?php echo $schedule['END_TIME']; ?>', '<?php echo $schedule['AVAILABLE_DAY']; ?>')">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="schedule_id" value="<?php echo $schedule['SCHEDULE_ID']; ?>">
                                                <button type="submit" name="delete_schedule" class="btn-delete" onclick="return confirm('Are you sure you want to delete this schedule?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-schedule">
                                <i class="bi bi-calendar-x"></i>
                                No schedules assigned to this doctor yet
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <h4>No Doctors Found</h4>
                <p>There are no doctors in the system yet.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Create Schedule Modal -->
    <div class="modal fade" id="createScheduleModal" tabindex="-1" aria-labelledby="createScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createScheduleModalLabel">
                        <i class="bi bi-plus-circle"></i> Create New Schedule
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="doctor_schedule_recep.php">
                        <input type="hidden" name="create_schedule" value="1">
                        
                        <div class="mb-3">
                            <label for="doctor_id" class="form-label">Select Doctor</label>
                            <select class="form-select" id="doctor_id" name="doctor_id" required>
                                <option value="">Choose a doctor...</option>
                                <?php
                                $doctors_dropdown_query = mysqli_query($conn, "
                                    SELECT d.*, s.SPECIALISATION_NAME 
                                    FROM doctor_tbl d
                                    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
                                    ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME
                                ");
                                if (mysqli_num_rows($doctors_dropdown_query) > 0) {
                                    while ($doctor = mysqli_fetch_assoc($doctors_dropdown_query)) {
                                        echo '<option value="' . $doctor['DOCTOR_ID'] . '">' . 
                                             'Dr. ' . htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']) . 
                                             ' (' . htmlspecialchars($doctor['SPECIALISATION_NAME']) . ')</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="start_time" name="start_time" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="end_time" name="end_time" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="available_day" class="form-label">Available Day</label>
                            <select class="form-select" id="available_day" name="available_day" required>
                                <option value="">Select day...</option>
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
                            <button type="submit" class="btn btn-success me-2">
                                <i class="bi bi-check-circle"></i> Create Schedule
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> Cancel
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
                    <h5 class="modal-title" id="editScheduleModalLabel">
                        <i class="bi bi-pencil-square"></i> Edit Schedule
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="doctor_schedule_recep.php">
                        <input type="hidden" id="edit_schedule_id" name="schedule_id">
                        <input type="hidden" name="update_schedule" value="1">
                        
                        <div class="mb-3">
                            <label for="edit_doctor_id" class="form-label">Select Doctor</label>
                            <select class="form-select" id="edit_doctor_id" name="doctor_id" required>
                                <option value="">Choose a doctor...</option>
                                <?php
                                $doctors_dropdown_query = mysqli_query($conn, "
                                    SELECT d.*, s.SPECIALISATION_NAME 
                                    FROM doctor_tbl d
                                    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
                                    ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME
                                ");
                                if (mysqli_num_rows($doctors_dropdown_query) > 0) {
                                    while ($doctor = mysqli_fetch_assoc($doctors_dropdown_query)) {
                                        echo '<option value="' . $doctor['DOCTOR_ID'] . '">' . 
                                             'Dr. ' . htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']) . 
                                             ' (' . htmlspecialchars($doctor['SPECIALISATION_NAME']) . ')</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit_start_time" class="form-label">Start Time</label>
                                <input type="time" class="form-control" id="edit_start_time" name="start_time" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="edit_end_time" class="form-label">End Time</label>
                                <input type="time" class="form-control" id="edit_end_time" name="end_time" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_available_day" class="form-label">Available Day</label>
                            <select class="form-select" id="edit_available_day" name="available_day" required>
                                <option value="">Select day...</option>
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
                            <button type="submit" class="btn btn-success me-2">
                                <i class="bi bi-check-circle"></i> Update Schedule
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> Cancel
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
        
        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>