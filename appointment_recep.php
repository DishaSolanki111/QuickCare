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

// Handle appointment creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_appointment'])) {
    $patient_id = mysqli_real_escape_string($conn, $_POST['patient_id']);
    $doctor_id = mysqli_real_escape_string($conn, $_POST['doctor_id']);
    $schedule_id = mysqli_real_escape_string($conn, $_POST['schedule_id']);
    $appointment_date = mysqli_real_escape_string($conn, $_POST['appointment_date']);
    $appointment_time = mysqli_real_escape_string($conn, $_POST['appointment_time']);
    
    $create_query = "INSERT INTO appointment_tbl (PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS) 
                     VALUES ('$patient_id', '$doctor_id', '$schedule_id', '$appointment_date', '$appointment_time', 'SCHEDULED')";
    
    if (mysqli_query($conn, $create_query)) {
        $success_message = "Appointment created successfully!";
    } else {
        $error_message = "Error creating appointment: " . mysqli_error($conn);
    }
}

// Handle appointment status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $appointment_id = mysqli_real_escape_string($conn, $_POST['appointment_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $update_query = "UPDATE appointment_tbl SET STATUS = '$status' WHERE APPOINTMENT_ID = '$appointment_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $success_message = "Appointment status updated successfully!";
    } else {
        $error_message = "Error updating appointment status: " . mysqli_error($conn);
    }
}

// Fetch appointments data
 $appointments_query = mysqli_query($conn, "
    SELECT a.*, p.FIRST_NAME as PAT_FNAME, p.LAST_NAME as PAT_LNAME, p.PHONE as PAT_PHONE,
           d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME
    FROM appointment_tbl a
    JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    ORDER BY a.APPOINTMENT_DATE DESC, a.APPOINTMENT_TIME DESC
");

// Fetch patients for dropdown
 $patients_query = mysqli_query($conn, "SELECT * FROM patient_tbl ORDER BY FIRST_NAME, LAST_NAME");

// Fetch doctors for dropdown
 $doctors_query = mysqli_query($conn, "
    SELECT d.*, s.SPECIALISATION_NAME 
    FROM doctor_tbl d
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME
");

// Fetch doctor schedules for dropdown
 $schedules_query = mysqli_query($conn, "
    SELECT ds.*, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME
    FROM doctor_schedule_tbl ds
    JOIN doctor_tbl d ON ds.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME, ds.AVAILABLE_DAY
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments - QuickCare</title>
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
        
        .appointment-card {
            border-left: 4px solid var(--secondary-color);
            margin-bottom: 15px;
            padding: 15px;
            background-color: var(--white);
            border-radius: 0 8px 8px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .appointment-title {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .appointment-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .appointment-detail {
            display: flex;
            align-items: center;
            color: #666;
        }
        
        .appointment-detail i {
            margin-right: 8px;
            color: var(--secondary-color);
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-scheduled {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--secondary-color);
        }
        
        .status-completed {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--accent-color);
        }
        
        .status-cancelled {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
        }
        
        .appointment-actions {
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
            max-width: 800px;
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
            
            .appointment-details {
                grid-template-columns: 1fr;
            }
            
            .appointment-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <img src="uploads/logo.JPG" alt="QuickCare Logo" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
        <h2>QuickCare</h2>
        <a href="receptionist.php">Dashboard</a>
        <a href="recep_profile.php">View My Profile</a>
        <a href="manage_appointments.php" class="active">Manage Appointments</a>
        <a href="manage_doctor_schedule.php">Manage Doctor Schedule</a>
        <a href="manage_medicine.php">Manage Medicine</a>
        <a href="set_reminder.php">Set Reminder</a>
        <a href="manage_user_profile.php">Manage User Profile</a>
        <a href="view_prescription.php">View Prescription</a>
        <button class="logout-btn">Logout</button>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <h1>Manage Appointments</h1>
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
        
        <!-- Appointments Card -->
        <div class="card">
            <div class="card-header">
                <h3>Appointments</h3>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
                    <i class="bi bi-plus-circle"></i> Create Appointment
                </button>
            </div>
            <div class="card-body">
                <?php
                if (mysqli_num_rows($appointments_query) > 0) {
                    while ($appointment = mysqli_fetch_assoc($appointments_query)) {
                        $status_class = '';
                        if ($appointment['STATUS'] == 'SCHEDULED') {
                            $status_class = 'status-scheduled';
                        } elseif ($appointment['STATUS'] == 'COMPLETED') {
                            $status_class = 'status-completed';
                        } elseif ($appointment['STATUS'] == 'CANCELLED') {
                            $status_class = 'status-cancelled';
                        }
                        ?>
                        <div class="appointment-card">
                            <div class="appointment-header">
                                <div class="appointment-title">
                                    <?php echo htmlspecialchars($appointment['PAT_FNAME'] . ' ' . $appointment['PAT_LNAME']); ?> with Dr. <?php echo htmlspecialchars($appointment['DOC_FNAME'] . ' ' . $appointment['DOC_LNAME']); ?>
                                </div>
                                <span class="status-badge <?php echo $status_class; ?>"><?php echo $appointment['STATUS']; ?></span>
                            </div>
                            
                            <div class="appointment-details">
                                <div class="appointment-detail">
                                    <i class="bi bi-calendar"></i>
                                    <span><?php echo date('F d, Y', strtotime($appointment['APPOINTMENT_DATE'])); ?></span>
                                </div>
                                <div class="appointment-detail">
                                    <i class="bi bi-clock"></i>
                                    <span><?php echo date('h:i A', strtotime($appointment['APPOINTMENT_TIME'])); ?></span>
                                </div>
                                <div class="appointment-detail">
                                    <i class="bi bi-person"></i>
                                    <span><?php echo htmlspecialchars($appointment['SPECIALISATION_NAME']); ?></span>
                                </div>
                                <div class="appointment-detail">
                                    <i class="bi bi-telephone"></i>
                                    <span><?php echo htmlspecialchars($appointment['PAT_PHONE']); ?></span>
                                </div>
                            </div>
                            
                            <div class="appointment-actions">
                                <?php if ($appointment['STATUS'] == 'SCHEDULED'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['APPOINTMENT_ID']; ?>">
                                        <input type="hidden" name="status" value="COMPLETED">
                                        <button type="submit" name="update_status" class="btn btn-success btn-sm">
                                            <i class="bi bi-check"></i> Complete
                                        </button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['APPOINTMENT_ID']; ?>">
                                        <input type="hidden" name="status" value="CANCELLED">
                                        <button type="submit" name="update_status" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                            <i class="bi bi-x"></i> Cancel
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="empty-state">
                        <i class="bi bi-calendar-x"></i>
                        <h4>No appointments found</h4>
                        <p>There are no appointments scheduled yet.</p>
                    </div>';
                }
                ?>
            </div>
        </div>
    </div>
    
    <!-- Create Appointment Modal -->
    <div class="modal fade" id="createAppointmentModal" tabindex="-1" aria-labelledby="createAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createAppointmentModalLabel">Create New Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="manage_appointments.php">
                        <input type="hidden" name="create_appointment" value="1">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="patient_id">Patient</label>
                                <select class="form-control" id="patient_id" name="patient_id" required>
                                    <option value="">Select Patient</option>
                                    <?php
                                    if (mysqli_num_rows($patients_query) > 0) {
                                        while ($patient = mysqli_fetch_assoc($patients_query)) {
                                            echo '<option value="' . $patient['PATIENT_ID'] . '">' . 
                                                 htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']) . '</option>';
                                        }
                                        // Reset the result pointer
                                        mysqli_data_seek($patients_query, 0);
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="doctor_id">Doctor</label>
                                <select class="form-control" id="doctor_id" name="doctor_id" required onchange="updateScheduleOptions()">
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
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="schedule_id">Schedule</label>
                                <select class="form-control" id="schedule_id" name="schedule_id" required>
                                    <option value="">Select Schedule</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="appointment_date">Appointment Date</label>
                                <input type="date" class="form-control" id="appointment_date" name="appointment_date" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="appointment_time">Appointment Time</label>
                            <input type="time" class="form-control" id="appointment_time" name="appointment_time" required>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check"></i> Create Appointment
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
        // Function to update schedule options based on selected doctor
        function updateScheduleOptions() {
            const doctorId = document.getElementById('doctor_id').value;
            const scheduleSelect = document.getElementById('schedule_id');
            
            // Clear existing options
            scheduleSelect.innerHTML = '<option value="">Select Schedule</option>';
            
            if (doctorId) {
                // Make an AJAX request to get schedules for the selected doctor
                fetch(`get_doctor_schedules.php?doctor_id=${doctorId}`)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(schedule => {
                            const option = document.createElement('option');
                            option.value = schedule.SCHEDULE_ID;
                            
                            const dayName = getDayName(schedule.AVAILABLE_DAY);
                            const startTime = formatTime(schedule.START_TIME);
                            const endTime = formatTime(schedule.END_TIME);
                            
                            option.textContent = `${dayName}: ${startTime} - ${endTime}`;
                            scheduleSelect.appendChild(option);
                        });
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
        
        // Helper function to convert day abbreviation to full name
        function getDayName(day) {
            const days = {
                'MON': 'Monday',
                'TUE': 'Tuesday',
                'WED': 'Wednesday',
                'THUR': 'Thursday',
                'FRI': 'Friday',
                'SAT': 'Saturday',
                'SUN': 'Sunday'
            };
            return days[day] || day;
        }
        
        // Helper function to format time
        function formatTime(time) {
            const [hours, minutes] = time.split(':');
            const h = parseInt(hours);
            const ampm = h >= 12 ? 'PM' : 'AM';
            const displayHours = h % 12 || 12;
            return `${displayHours}:${minutes} ${ampm}`;
        }
        
        // Set minimum date for appointment to today
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('appointment_date').setAttribute('min', today);
        });
    </script>
</body>
</html>