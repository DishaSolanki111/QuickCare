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

// Handle appointment cancellation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_appointment'])) {
    $appointment_id = mysqli_real_escape_string($conn, $_POST['appointment_id']);
    
    $cancel_query = "UPDATE appointment_tbl SET STATUS = 'CANCELLED' WHERE APPOINTMENT_ID = '$appointment_id' AND PATIENT_ID = '$patient_id'";
    
    if (mysqli_query($conn, $cancel_query)) {
        $success_message = "Appointment cancelled successfully!";
    } else {
        $error_message = "Error cancelling appointment: " . mysqli_error($conn);
    }
}

// Fetch appointments data
 $appointments_query = mysqli_query($conn, "
    SELECT a.*, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME as SPECIALIZATION 
    FROM appointment_tbl a
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE a.PATIENT_ID = '$patient_id'
    ORDER BY a.APPOINTMENT_DATE DESC
");

// Fetch specializations for filter
 $specializations_query = mysqli_query($conn, "
    SELECT * FROM specialisation_tbl
    ORDER BY SPECIALISATION_NAME
");

// Fetch doctors for booking new appointment
 $doctors_query = mysqli_query($conn, "
    SELECT d.*, s.SPECIALISATION_NAME 
    FROM doctor_tbl d
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments - QuickCare</title>
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
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
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
        
        .appointment-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .appointment-info h3 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .appointment-details {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        
        .appointment-detail {
            display: flex;
            align-items: center;
            color: #666;
        }
        
        .appointment-detail i {
            margin-right: 5px;
            color: var(--secondary-color);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-scheduled {
            background-color: rgba(52, 152, 219, 0.2);
            color: var(--secondary-color);
        }
        
        .status-completed {
            background-color: rgba(46, 204, 113, 0.2);
            color: var(--accent-color);
        }
        
        .status-cancelled {
            background-color: rgba(231, 76, 60, 0.2);
            color: var(--danger-color);
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
        
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 12px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            color: #777;
            transition: all 0.3s ease;
        }
        
        .tab.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--secondary-color);
        }
        
        .tab:hover {
            color: var(--primary-color);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
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
        
        .doctor-filter {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
        }
        
        .doctor-info {
            display: flex;
            align-items: center;
            padding: 10px;
            border: 1px solid #eee;
            border-radius: 8px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .doctor-info:hover {
            background-color: #f8f9fa;
            border-color: var(--secondary-color);
        }
        
        .doctor-info.selected {
            background-color: rgba(52, 152, 219, 0.1);
            border-color: var(--secondary-color);
        }
        
        .doctor-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
            font-size: 18px;
        }
        
        .doctor-details {
            flex: 1;
        }
        
        .doctor-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--primary-color);
        }
        
        .doctor-specialization {
            font-size: 14px;
            color: #666;
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
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 200px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .doctor-filter {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .time-slots-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .appointment-card {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .appointment-details {
                flex-direction: column;
                gap: 10px;
            }
            
            .doctor-info {
                flex-direction: column;
                text-align: center;
            }
            
            .doctor-avatar {
                margin-right: 0;
                margin-bottom: 10px;
            }
            
            .time-slots-container {
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
                <div class="welcome-msg">Manage Appointments</div>
                <div class="user-actions">
                    <div class="user-dropdown">
                        <div class="user-avatar"><?php echo strtoupper(substr($patient['FIRST_NAME'], 0, 1) . substr($patient['LAST_NAME'], 0, 1)); ?></div>
                        <span><?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></span>
                        <i class="fas fa-chevron-down" style="margin-left: 8px;"></i>
                    </div>
                </div>
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
            
            <!-- Book New Appointment Button -->
            <div style="margin-bottom: 20px;">
                <button class="btn btn-success" onclick="openBookingModal()">
                    <i class="fas fa-plus"></i> Book New Appointment
                </button>
            </div>
            
            <!-- Tabs Section -->
            <div class="tabs">
                <div class="tab active" data-tab="upcoming">Upcoming Appointments</div>
                <div class="tab" data-tab="past">Past Appointments</div>
            </div>
            
            <!-- Tab Content -->
            <div class="tab-content active" id="upcoming">
                <?php
                $upcoming_appointments = [];
                
                if (mysqli_num_rows($appointments_query) > 0) {
                    // Reset the result pointer to beginning
                    mysqli_data_seek($appointments_query, 0);
                    while ($appointment = mysqli_fetch_assoc($appointments_query)) {
                        if ($appointment['APPOINTMENT_DATE'] >= date('Y-m-d')) {
                            $upcoming_appointments[] = $appointment;
                        }
                    }
                }
                
                // Display upcoming appointments
                if (count($upcoming_appointments) > 0) {
                    foreach ($upcoming_appointments as $appointment) {
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
                            <div class="appointment-info">
                                <h3>Dr. <?php echo htmlspecialchars($appointment['DOC_FNAME'] . ' ' . $appointment['DOC_LNAME']); ?></h3>
                                <p><?php echo htmlspecialchars($appointment['SPECIALIZATION']); ?></p>
                                <div class="appointment-details">
                                    <div class="appointment-detail">
                                        <i class="far fa-calendar"></i>
                                        <span><?php echo date('F d, Y', strtotime($appointment['APPOINTMENT_DATE'])); ?></span>
                                    </div>
                                    <div class="appointment-detail">
                                        <i class="far fa-clock"></i>
                                        <span><?php echo date('h:i A', strtotime($appointment['APPOINTMENT_TIME'])); ?></span>
                                    </div>
                                    <div class="appointment-detail">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span>Main Hospital, Room 204</span>
                                    </div>
                                </div>
                            </div>
                            <div class="appointment-actions">
                                <span class="status-badge <?php echo $status_class; ?>"><?php echo ucfirst(strtolower($appointment['STATUS'])); ?></span>
                                <div class="btn-group" style="margin-top: 15px;">
                                    <button class="btn btn-primary" onclick="openRescheduleModal(<?php echo $appointment['APPOINTMENT_ID']; ?>)">
                                        <i class="fas fa-edit"></i> Reschedule
                                    </button>
                                    <?php if ($appointment['STATUS'] == 'SCHEDULED'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['APPOINTMENT_ID']; ?>">
                                        <button type="submit" name="cancel_appointment" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="empty-state">
                        <i class="far fa-calendar-times"></i>
                        <p>No upcoming appointments</p>
                    </div>';
                }
                ?>
            </div>
            
            <div class="tab-content" id="past">
                <?php
                $past_appointments = [];
                
                if (mysqli_num_rows($appointments_query) > 0) {
                    // Reset the result pointer to beginning
                    mysqli_data_seek($appointments_query, 0);
                    while ($appointment = mysqli_fetch_assoc($appointments_query)) {
                        if ($appointment['APPOINTMENT_DATE'] < date('Y-m-d')) {
                            $past_appointments[] = $appointment;
                        }
                    }
                }
                
                // Display past appointments
                if (count($past_appointments) > 0) {
                    foreach ($past_appointments as $appointment) {
                        ?>
                        <div class="appointment-card">
                            <div class="appointment-info">
                                <h3>Dr. <?php echo htmlspecialchars($appointment['DOC_FNAME'] . ' ' . $appointment['DOC_LNAME']); ?></h3>
                                <p><?php echo htmlspecialchars($appointment['SPECIALIZATION']); ?></p>
                                <div class="appointment-details">
                                    <div class="appointment-detail">
                                        <i class="far fa-calendar"></i>
                                        <span><?php echo date('F d, Y', strtotime($appointment['APPOINTMENT_DATE'])); ?></span>
                                    </div>
                                    <div class="appointment-detail">
                                        <i class="far fa-clock"></i>
                                        <span><?php echo date('h:i A', strtotime($appointment['APPOINTMENT_TIME'])); ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="appointment-actions">
                                <span class="status-badge status-completed">Completed</span>
                                <div class="btn-group" style="margin-top: 15px;">
                                    <button class="btn btn-primary">
                                        <i class="fas fa-file-medical"></i> View Prescription
                                    </button>
                                    <button class="btn btn-success">
                                        <i class="fas fa-star"></i> Leave Feedback
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="empty-state">
                        <i class="far fa-calendar-check"></i>
                        <p>No past appointments</p>
                    </div>';
                }
                ?>
            </div>
        </div>
    </div>
    
    <!-- Booking Modal -->
    <div id="bookingModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeBookingModal()">&times;</span>
            <h2>Book New Appointment</h2>
            
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="step1">1</div>
                <div class="step" id="step2">2</div>
                <div class="step" id="step3">3</div>
            </div>
            
            <form method="POST" action="payment.php">
                <!-- Step 1: Select Doctor -->
                <div class="step-content active" id="step1Content">
                    <div class="form-group">
                        <label>Filter by Specialization</label>
                        <select class="form-control" id="specialization_filter" onchange="filterDoctors()">
                            <option value="">All Specializations</option>
                            <?php
                            if (mysqli_num_rows($specializations_query) > 0) {
                                while ($specialization = mysqli_fetch_assoc($specializations_query)) {
                                    echo '<option value="' . $specialization['SPECIALISATION_ID'] . '">' . 
                                         htmlspecialchars($specialization['SPECIALISATION_NAME']) . '</option>';
                                }
                                // Reset the result pointer
                                mysqli_data_seek($specializations_query, 0);
                            }
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Select Doctor</label>
                        <div id="doctors_list">
                            <?php
                            if (mysqli_num_rows($doctors_query) > 0) {
                                while ($doctor = mysqli_fetch_assoc($doctors_query)) {
                                    echo '<div class="doctor-info" data-specialization="' . $doctor['SPECIALISATION_ID'] . '" onclick="selectDoctor(this, ' . $doctor['DOCTOR_ID'] . ')">
                                                <div class="doctor-avatar">' . strtoupper(substr($doctor['FIRST_NAME'], 0, 1) . substr($doctor['LAST_NAME'], 0, 1)) . '</div>
                                                <div class="doctor-details">
                                                    <div class="doctor-name">Dr. ' . htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']) . '</div>
                                                    <div class="doctor-specialization">' . htmlspecialchars($doctor['SPECIALISATION_NAME']) . '</div>
                                                </div>
                                            </div>';
                                }
                                // Reset the result pointer
                                mysqli_data_seek($doctors_query, 0);
                            }
                            ?>
                        </div>
                        <input type="hidden" id="selected_doctor_id" name="doctor_id" required>
                    </div>
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-primary" onclick="nextStep(2)" id="nextToStep2" disabled>
                            Next: Select Date <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Step 2: Select Date -->
                <div class="step-content" id="step2Content">
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
                        <input type="hidden" id="selected_date" name="appointment_date" required>
                    </div>
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger" onclick="prevStep(1)">
                            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back
                        </button>
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
                        <input type="hidden" id="selected_time" name="appointment_time" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reason">Reason for Visit</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Please describe your symptoms or reason for the appointment"></textarea>
                    </div>
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger" onclick="prevStep(2)">
                            <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back
                        </button>
                        <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                            <i class="fas fa-check"></i> Book Appointment
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Reschedule Modal -->
    <div id="rescheduleModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeRescheduleModal()">&times;</span>
            <h2>Reschedule Appointment</h2>
            <form method="POST" action="reschedule_appointment.php">
                <input type="hidden" id="reschedule_appointment_id" name="appointment_id">
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="new_date">New Date</label>
                        <input type="date" class="form-control" id="new_date" name="new_date" required>
                    </div>
                    <div class="form-group">
                        <label for="new_time">New Time</label>
                        <input type="time" class="form-control" id="new_time" name="new_time" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="reschedule_reason">Reason for Rescheduling</label>
                    <textarea class="form-control" id="reschedule_reason" name="reschedule_reason" rows="3" placeholder="Please provide a reason for rescheduling"></textarea>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Update Appointment
                    </button>
                    <button type="button" class="btn btn-danger" onclick="closeRescheduleModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabId = tab.getAttribute('data-tab');
                    
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    tab.classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                });
            });
            
            // Initialize calendar
            initCalendar();
        });
        
        // Modal functions
        function openBookingModal() {
            document.getElementById('bookingModal').style.display = 'block';
            resetBookingModal();
        }
        
        function closeBookingModal() {
            document.getElementById('bookingModal').style.display = 'none';
        }
        
        function openRescheduleModal(appointmentId) {
            document.getElementById('reschedule_appointment_id').value = appointmentId;
            document.getElementById('rescheduleModal').style.display = 'block';
        }
        
        function closeRescheduleModal() {
            document.getElementById('rescheduleModal').style.display = 'none';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const bookingModal = document.getElementById('bookingModal');
            const rescheduleModal = document.getElementById('rescheduleModal');
            
            if (event.target == bookingModal) {
                bookingModal.style.display = 'none';
            }
            if (event.target == rescheduleModal) {
                rescheduleModal.style.display = 'none';
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
        }
        
        function prevStep(stepNumber) {
            // Hide current step
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Show previous step
            document.getElementById('step' + stepNumber + 'Content').classList.add('active');
            
            // Update step indicators
            for (let i = stepNumber + 1; i <= 3; i++) {
                document.getElementById('step' + i).classList.remove('active');
                document.getElementById('step' + i).classList.remove('completed');
            }
            document.getElementById('step' + stepNumber).classList.add('active');
        }
        
        function resetBookingModal() {
            // Reset all steps
            document.querySelectorAll('.step-content').forEach(content => {
                content.classList.remove('active');
            });
            document.getElementById('step1Content').classList.add('active');
            
            // Reset step indicators
            for (let i = 2; i <= 3; i++) {
                document.getElementById('step' + i).classList.remove('active');
                document.getElementById('step' + i).classList.remove('completed');
            }
            
            // Reset form values
            document.getElementById('selected_doctor_id').value = '';
            document.getElementById('selected_date').value = '';
            document.getElementById('selected_time').value = '';
            document.getElementById('nextToStep2').disabled = true;
            document.getElementById('nextToStep3').disabled = true;
            document.getElementById('submitBtn').disabled = true;
            
            // Clear selections
            document.querySelectorAll('.doctor-info').forEach(doc => {
                doc.classList.remove('selected');
            });
        }
        
        // Doctor selection and filtering
        function selectDoctor(element, doctorId) {
            // Remove selected class from all doctors
            document.querySelectorAll('.doctor-info').forEach(doc => {
                doc.classList.remove('selected');
            });
            
            // Add selected class to clicked doctor
            element.classList.add('selected');
            
            // Set the hidden input value
            document.getElementById('selected_doctor_id').value = doctorId;
            
            // Enable next button
            document.getElementById('nextToStep2').disabled = false;
            
            // Load doctor's schedule
            loadDoctorSchedule(doctorId);
        }
        
        function filterDoctors() {
            const specializationId = document.getElementById('specialization_filter').value;
            const doctors = document.querySelectorAll('.doctor-info');
            
            doctors.forEach(doctor => {
                if (specializationId === '' || doctor.getAttribute('data-specialization') === specializationId) {
                    doctor.style.display = 'flex';
                } else {
                    doctor.style.display = 'none';
                }
            });
        }
        
        // Calendar functionality
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        let selectedDoctorId = null;
        let doctorSchedule = [];
        
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
            
            // Show loading indicator
            document.getElementById('timeSlotsContainer').innerHTML = `
                <div class="loading">
                    <i class="fas fa-spinner"></i>
                    <p>Loading doctor's schedule...</p>
                </div>
            `;
            
            // Fetch doctor's schedule from database
            fetch(`get_doctor_schedule.php?doctor_id=${doctorId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        doctorSchedule = data.available_days;
                        renderCalendar(currentMonth, currentYear);
                        
                        // Clear time slots
                        document.getElementById('timeSlotsContainer').innerHTML = `
                            <div class="loading">
                                <i class="fas fa-spinner"></i>
                                <p>Please select a date first</p>
                            </div>
                        `;
                    } else {
                        document.getElementById('timeSlotsContainer').innerHTML = `
                            <div class="alert alert-danger">
                                Error loading doctor's schedule: ${data.message}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error loading doctor schedule:', error);
                    document.getElementById('timeSlotsContainer').innerHTML = `
                        <div class="alert alert-danger">
                            Error loading doctor's schedule. Please try again later.
                        </div>
                    `;
                });
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
            
            // Fetch time slots from database
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
                            
                            // Enable next button
                            document.getElementById('nextToStep3').disabled = false;
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
            // Update hidden input
            document.getElementById('selected_time').value = time;
            
            // Update UI to show selected time slot
            document.querySelectorAll('.time-slot').forEach(slot => {
                slot.classList.remove('selected');
            });
            event.target.classList.add('selected');
            
            // Enable submit button
            document.getElementById('submitBtn').disabled = false;
        }
    </script>
</body>
</html>