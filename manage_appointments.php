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
        
        /* Sidebar Styles */
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
            margin: 10% auto;
            padding: 20px;
            border: none;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
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
        
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            
            .logo h1 span, .nav-item span {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
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
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <img src="./uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">
            <h2>QuickCare</h2>
            <div class="nav">
                <a href="patient.php">Dashboard</a>
                <a href="patient_profile.php">My Profile</a>
                <a class="active">Manage Appointments</a>
                <a href="doctor_schedule.php">View Doctor Schedule</a>
                <a href="prescriptions.php">My Prescriptions</a>
                <a href="medicine_reminder.php">Medicine Reminder</a>
                <a href="payments.php">Payments</a>
                <a href="feedback.php">Feedback</a>
                <a href="doctor_profiles.php">View Doctor Profile</a>
                <button class="logout-btn">logout</button>
            </div>
        </div>
        
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
            <form method="POST" action="book_appointment.php">
                <div class="form-group">
                    <label for="doctor">Select Doctor</label>
                    <select class="form-control" id="doctor" name="doctor_id" required>
                        <option value="">-- Select Doctor --</option>
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
                        <label for="appointment_date">Appointment Date</label>
                        <input type="date" class="form-control" id="appointment_date" name="appointment_date" required>
                    </div>
                    <div class="form-group">
                        <label for="appointment_time">Preferred Time</label>
                        <input type="time" class="form-control" id="appointment_time" name="appointment_time" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="reason">Reason for Visit</label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Please describe your symptoms or reason for the appointment"></textarea>
                </div>
                
                <div class="btn-group">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check"></i> Book Appointment
                    </button>
                    <button type="button" class="btn btn-danger" onclick="closeBookingModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
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
            
            // Set minimum date for appointment booking to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('appointment_date').setAttribute('min', today);
            document.getElementById('new_date').setAttribute('min', today);
        });
        
        // Modal functions
        function openBookingModal() {
            document.getElementById('bookingModal').style.display = 'block';
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
    </script>
</body>
</html>