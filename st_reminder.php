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

// Handle appointment reminder creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_appointment_reminder'])) {
    $appointment_id = mysqli_real_escape_string($conn, $_POST['appointment_id']);
    $reminder_time = mysqli_real_escape_string($conn, $_POST['reminder_time']);
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
    
    $create_query = "INSERT INTO appointment_reminder_tbl (RECEPTIONIST_ID, APPOINTMENT_ID, REMINDER_TIME, REMARKS) 
                     VALUES ('$receptionist_id', '$appointment_id', '$reminder_time', '$remarks')";
    
    if (mysqli_query($conn, $create_query)) {
        $success_message = "Appointment reminder created successfully!";
    } else {
        $error_message = "Error creating appointment reminder: " . mysqli_error($conn);
    }
}

// Handle medicine reminder creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_medicine_reminder'])) {
    $medicine_id = mysqli_real_escape_string($conn, $_POST['medicine_id']);
    $patient_id = mysqli_real_escape_string($conn, $_POST['patient_id']);
    $reminder_time = mysqli_real_escape_string($conn, $_POST['reminder_time']);
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
    
    $create_query = "INSERT INTO medicine_reminder_tbl (MEDICINE_ID, CREATOR_ROLE, CREATOR_ID, PATIENT_ID, REMINDER_TIME, REMARKS) 
                     VALUES ('$medicine_id', 'RECEPTIONIST', '$receptionist_id', '$patient_id', '$reminder_time', '$remarks')";
    
    if (mysqli_query($conn, $create_query)) {
        $success_message = "Medicine reminder created successfully!";
    } else {
        $error_message = "Error creating medicine reminder: " . mysqli_error($conn);
    }
}

// Handle appointment reminder deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_appointment_reminder'])) {
    $reminder_id = mysqli_real_escape_string($conn, $_POST['reminder_id']);
    
    $delete_query = "DELETE FROM appointment_reminder_tbl WHERE APPOINTMENT_REMINDER_ID = '$reminder_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        $success_message = "Appointment reminder deleted successfully!";
    } else {
        $error_message = "Error deleting appointment reminder: " . mysqli_error($conn);
    }
}

// Handle medicine reminder deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_medicine_reminder'])) {
    $reminder_id = mysqli_real_escape_string($conn, $_POST['reminder_id']);
    
    $delete_query = "DELETE FROM medicine_reminder_tbl WHERE MEDICINE_REMINDER_ID = '$reminder_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        $success_message = "Medicine reminder deleted successfully!";
    } else {
        $error_message = "Error deleting medicine reminder: " . mysqli_error($conn);
    }
}

// Fetch appointment reminders
 $appointment_reminders_query = mysqli_query($conn, "
    SELECT ar.*, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, 
           p.FIRST_NAME as PAT_FNAME, p.LAST_NAME as PAT_LNAME,
           d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME
    FROM appointment_reminder_tbl ar
    JOIN appointment_tbl a ON ar.APPOINTMENT_ID = a.APPOINTMENT_ID
    JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    ORDER BY ar.REMINDER_TIME
");

// Fetch medicine reminders
 $medicine_reminders_query = mysqli_query($conn, "
    SELECT mr.*, m.MED_NAME, p.FIRST_NAME as PAT_FNAME, p.LAST_NAME as PAT_LNAME
    FROM medicine_reminder_tbl mr
    JOIN medicine_tbl m ON mr.MEDICINE_ID = m.MEDICINE_ID
    JOIN patient_tbl p ON mr.PATIENT_ID = p.PATIENT_ID
    ORDER BY mr.REMINDER_TIME
");

// Fetch appointments for dropdown
 $appointments_query = mysqli_query($conn, "
    SELECT a.*, p.FIRST_NAME as PAT_FNAME, p.LAST_NAME as PAT_LNAME,
           d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME
    FROM appointment_tbl a
    JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE a.STATUS = 'SCHEDULED'
    ORDER BY a.APPOINTMENT_DATE, a.APPOINTMENT_TIME
");

// Fetch medicines for dropdown
 $medicines_query = mysqli_query($conn, "SELECT * FROM medicine_tbl ORDER BY MED_NAME");

// Fetch patients for dropdown
 $patients_query = mysqli_query($conn, "SELECT * FROM patient_tbl ORDER BY FIRST_NAME, LAST_NAME");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Reminder - QuickCare</title>
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
        
        .reminder-card {
            border-left: 4px solid var(--warning-color);
            margin-bottom: 15px;
            padding: 15px;
            background-color: var(--white);
            border-radius: 0 8px 8px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .reminder-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .reminder-title {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .reminder-type {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background-color: rgba(243, 156, 18, 0.1);
            color: var(--warning-color);
        }
        
        .reminder-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .reminder-detail {
            display: flex;
            align-items: center;
            color: #666;
        }
        
        .reminder-detail i {
            margin-right: 8px;
            color: var(--secondary-color);
        }
        
        .reminder-actions {
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
        
        .nav-tabs {
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .nav-tabs .nav-link {
            color: var(--primary-color);
            border: none;
            border-bottom: 3px solid transparent;
            border-radius: 0;
            padding: 10px 15px;
            margin-right: 5px;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--secondary-color);
            border-bottom-color: var(--secondary-color);
            background: none;
        }
        
        .nav-tabs .nav-link:hover {
            border-bottom-color: #eee;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
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
            
            .reminder-details {
                grid-template-columns: 1fr;
            }
            
            .reminder-header {
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
        <a href="manage_appointments.php">Manage Appointments</a>
        <a href="manage_doctor_schedule.php">Manage Doctor Schedule</a>
        <a href="manage_medicine.php">Manage Medicine</a>
        <a href="set_reminder.php" class="active">Set Reminder</a>
        <a href="manage_user_profile.php">Manage User Profile</a>
        <a href="view_prescription.php">View Prescription</a>
        <button class="logout-btn">Logout</button>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <h1>Set Reminder</h1>
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
        
        <!-- Reminder Card -->
        <div class="card">
            <div class="card-header">
                <h3>Reminders</h3>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createAppointmentReminderModal">
                    <i class="bi bi-bell"></i> Appointment Reminder
                </button>
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#createMedicineReminderModal">
                    <i class="bi bi-capsule"></i> Medicine Reminder
                </button>
            </div>
            </div>
            <div class="card-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="reminderTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="appointment-tab" data-bs-toggle="tab" data-bs-target="#appointment" type="button" role="tab" aria-controls="appointment" aria-selected="true">Appointment Reminders</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="medicine-tab" data-bs-toggle="tab" data-bs-target="#medicine" type="button" role="tab" aria-controls="medicine" aria-selected="false">Medicine Reminders</button>
                    </li>
                </ul>
                
                <!-- Tab Content -->
                <div class="tab-content" id="reminderTabContent">
                    <!-- Appointment Reminders Tab -->
                    <div class="tab-pane fade show active" id="appointment" role="tabpanel" aria-labelledby="appointment-tab">
                        <?php
                        if (mysqli_num_rows($appointment_reminders_query) > 0) {
                            while ($reminder = mysqli_fetch_assoc($appointment_reminders_query)) {
                                ?>
                                <div class="reminder-card">
                                    <div class="reminder-header">
                                        <div class="reminder-title">
                                            Appointment for <?php echo htmlspecialchars($reminder['PAT_FNAME'] . ' ' . $reminder['PAT_LNAME']); ?>
                                        </div>
                                        <span class="reminder-type">Appointment</span>
                                    </div>
                                    
                                    <div class="reminder-details">
                                        <div class="reminder-detail">
                                            <i class="bi bi-person"></i>
                                            <span>Dr. <?php echo htmlspecialchars($reminder['DOC_FNAME'] . ' ' . $reminder['DOC_LNAME']); ?></span>
                                        </div>
                                        <div class="reminder-detail">
                                            <i class="bi bi-calendar"></i>
                                            <span><?php echo date('F d, Y', strtotime($reminder['APPOINTMENT_DATE'])); ?> at <?php echo date('h:i A', strtotime($reminder['APPOINTMENT_TIME'])); ?></span>
                                        </div>
                                        <div class="reminder-detail">
                                            <i class="bi bi-clock"></i>
                                            <span>Reminder at <?php echo date('h:i A', strtotime($reminder['REMINDER_TIME'])); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="reminder-actions">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="reminder_id" value="<?php echo $reminder['APPOINTMENT_REMINDER_ID']; ?>">
                                            <button type="submit" name="delete_appointment_reminder" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this reminder?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="empty-state">
                                <i class="bi bi-bell-slash"></i>
                                <h4>No appointment reminders found</h4>
                                <p>No appointment reminders have been set yet.</p>
                            </div>';
                        }
                        ?>
                    </div>
                    
                    <!-- Medicine Reminders Tab -->
                    <div class="tab-pane fade" id="medicine" role="tabpanel" aria-labelledby="medicine-tab">
                        <?php
                        if (mysqli_num_rows($medicine_reminders_query) > 0) {
                            while ($reminder = mysqli_fetch_assoc($medicine_reminders_query)) {
                                ?>
                                <div class="reminder-card">
                                    <div class="reminder-header">
                                        <div class="reminder-title">
                                            <?php echo htmlspecialchars($reminder['MED_NAME']); ?> for <?php echo htmlspecialchars($reminder['PAT_FNAME'] . ' ' . $reminder['PAT_LNAME']); ?>
                                        </div>
                                        <span class="reminder-type">Medicine</span>
                                    </div>
                                    
                                    <div class="reminder-details">
                                        <div class="reminder-detail">
                                            <i class="bi bi-person"></i>
                                            <span>Patient: <?php echo htmlspecialchars($reminder['PAT_FNAME'] . ' ' . $reminder['PAT_LNAME']); ?></span>
                                        </div>
                                        <div class="reminder-detail">
                                            <i class="bi bi-clock"></i>
                                            <span>Reminder at <?php echo date('h:i A', strtotime($reminder['REMINDER_TIME'])); ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="reminder-remarks">
                                        <strong>Remarks:</strong> <?php echo htmlspecialchars($reminder['REMARKS']); ?>
                                    </div>
                                    
                                    <div class="reminder-actions">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="reminder_id" value="<?php echo $reminder['MEDICINE_REMINDER_ID']; ?>">
                                            <button type="submit" name="delete_medicine_reminder" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this reminder?')">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="empty-state">
                                <i class="bi bi-capsule"></i>
                                <h4>No medicine reminders found</h4>
                                <p>No medicine reminders have been set yet.</p>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Create Appointment Reminder Modal -->
    <div class="modal fade" id="createAppointmentReminderModal" tabindex="-1" aria-labelledby="createAppointmentReminderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createAppointmentReminderModalLabel">Create Appointment Reminder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="set_reminder.php">
                        <input type="hidden" name="create_appointment_reminder" value="1">
                        
                        <div class="form-group">
                            <label for="appointment_id">Appointment</label>
                            <select class="form-control" id="appointment_id" name="appointment_id" required>
                                <option value="">Select Appointment</option>
                                <?php
                                if (mysqli_num_rows($appointments_query) > 0) {
                                    while ($appointment = mysqli_fetch_assoc($appointments_query)) {
                                        echo '<option value="' . $appointment['APPOINTMENT_ID'] . '">' . 
                                             htmlspecialchars($appointment['PAT_FNAME'] . ' ' . $appointment['PAT_LNAME']) . 
                                             ' with Dr. ' . htmlspecialchars($appointment['DOC_FNAME'] . ' ' . $appointment['DOC_LNAME']) . 
                                             ' (' . htmlspecialchars($appointment['SPECIALISATION_NAME']) . ') on ' . 
                                             date('F d, Y', strtotime($appointment['APPOINTMENT_DATE'])) . ' at ' . 
                                             date('h:i A', strtotime($appointment['APPOINTMENT_TIME'])) . '</option>';
                                    }
                                    // Reset the result pointer
                                    mysqli_data_seek($appointments_query, 0);
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="reminder_time">Reminder Time</label>
                            <input type="time" class="form-control" id="reminder_time" name="reminder_time" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Add any additional notes for the reminder"></textarea>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check"></i> Create Reminder
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
    
    <!-- Create Medicine Reminder Modal -->
    <div class="modal fade" id="createMedicineReminderModal" tabindex="-1" aria-labelledby="createMedicineReminderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createMedicineReminderModalLabel">Create Medicine Reminder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="set_reminder.php">
                        <input type="hidden" name="create_medicine_reminder" value="1">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="medicine_id">Medicine</label>
                                <select class="form-control" id="medicine_id" name="medicine_id" required>
                                    <option value="">Select Medicine</option>
                                    <?php
                                    if (mysqli_num_rows($medicines_query) > 0) {
                                        while ($medicine = mysqli_fetch_assoc($medicines_query)) {
                                            echo '<option value="' . $medicine['MEDICINE_ID'] . '">' . 
                                                 htmlspecialchars($medicine['MED_NAME']) . '</option>';
                                        }
                                        // Reset the result pointer
                                        mysqli_data_seek($medicines_query, 0);
                                    }
                                    ?>
                                </select>
                            </div>
                            
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
                        </div>
                        
                        <div class="form-group">
                            <label for="reminder_time">Reminder Time</label>
                            <input type="time" class="form-control" id="reminder_time" name="reminder_time" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Add any additional notes for the reminder"></textarea>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check"></i> Create Reminder
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
</body>
</html>