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

// Handle prescription reminder creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_prescription_reminder'])) {
    $prescription_id = mysqli_real_escape_string($conn, $_POST['prescription_id']);
    $reminder_time = mysqli_real_escape_string($conn, $_POST['reminder_time']);
    $reminder_date = mysqli_real_escape_string($conn, $_POST['reminder_date']);
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
    
    // Get patient ID from prescription
    $prescription_query = mysqli_query($conn, "SELECT p.PATIENT_ID FROM prescription_tbl pr JOIN appointment_tbl a ON pr.APPOINTMENT_ID = a.APPOINTMENT_ID JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID WHERE pr.PRESCRIPTION_ID = '$prescription_id'");
    $prescription_data = mysqli_fetch_assoc($prescription_query);
    $patient_id = $prescription_data['PATIENT_ID'];
    
    // Insert into medicine_reminder_tbl
    $create_query = "INSERT INTO medicine_reminder_tbl (MEDICINE_ID, CREATOR_ROLE, CREATOR_ID, PATIENT_ID, REMINDER_TIME, REMARKS) 
                     VALUES (0, 'RECEPTIONIST', '$receptionist_id', '$patient_id', '$reminder_time', '$remarks')";
    
    if (mysqli_query($conn, $create_query)) {
        $success_message = "Prescription reminder created successfully!";
    } else {
        $error_message = "Error creating prescription reminder: " . mysqli_error($conn);
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

// Fetch completed appointments with prescription details
 $completed_appointments_query = mysqli_query($conn, "
    SELECT a.APPOINTMENT_ID, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, 
           p.PATIENT_ID, p.FIRST_NAME as PAT_FNAME, p.LAST_NAME as PAT_LNAME, p.PHONE, p.EMAIL,
           d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME,
           pr.PRESCRIPTION_ID, pr.ISSUE_DATE, pr.HEIGHT_CM, pr.WEIGHT_KG, pr.BLOOD_PRESSURE, 
           pr.DIABETES, pr.SYMPTOMS, pr.DIAGNOSIS, pr.ADDITIONAL_NOTES
    FROM appointment_tbl a
    JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    LEFT JOIN prescription_tbl pr ON a.APPOINTMENT_ID = pr.APPOINTMENT_ID
    WHERE a.STATUS = 'COMPLETED'
    ORDER BY a.APPOINTMENT_DATE DESC
");

// Fetch medicine reminders
 $medicine_reminders_query = mysqli_query($conn, "
    SELECT mr.*, p.FIRST_NAME as PAT_FNAME, p.LAST_NAME as PAT_LNAME
    FROM medicine_reminder_tbl mr
    JOIN patient_tbl p ON mr.PATIENT_ID = p.PATIENT_ID
    ORDER BY mr.REMINDER_TIME
");
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
        }
        
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #F5F8FA;
            display: flex;
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
            color: var(--primary-color);
            font-weight: 600;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-blue);
            padding: 15px 20px;
            font-weight: 600;
            color: var(--primary-color);
            border-radius: 10px 10px 0 0 !important;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .btn-success {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-success:hover {
            background-color: #27ae60;
            border-color: #27ae60;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .btn-warning:hover {
            background-color: #e67e22;
            border-color: #e67e22;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: none;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            color: #27ae60;
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            color: #c0392b;
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
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .patient-card {
            border-left: 4px solid var(--secondary-color);
            margin-bottom: 20px;
            padding: 20px;
            background-color: var(--white);
            border-radius: 0 10px 10px 0;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .patient-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }
        
        .patient-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .patient-name {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .patient-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .patient-detail {
            display: flex;
            align-items: center;
            color: #666;
        }
        
        .patient-detail i {
            margin-right: 8px;
            color: var(--secondary-color);
        }
        
        .prescription-section {
            background-color: var(--card-bg);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .prescription-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .prescription-title {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .prescription-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .prescription-detail {
            display: flex;
            align-items: center;
            color: #666;
        }
        
        .prescription-detail i {
            margin-right: 8px;
            color: var(--secondary-color);
        }
        
        .medicines-list {
            margin-top: 15px;
        }
        
        .medicine-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            background-color: white;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        
        .medicine-info {
            flex-grow: 1;
        }
        
        .medicine-name {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .medicine-dosage {
            color: #666;
            font-size: 14px;
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
        
        .nav-tabs {
            border-bottom: 2px solid var(--gray-blue);
            margin-bottom: 20px;
        }
        
        .nav-tabs .nav-link {
            color: var(--primary-color);
            border: none;
            border-bottom: 3px solid transparent;
            border-radius: 0;
            padding: 12px 20px;
            margin-right: 5px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--secondary-color);
            border-bottom-color: var(--secondary-color);
            background: none;
        }
        
        .nav-tabs .nav-link:hover {
            border-bottom-color: #eee;
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
            
            .patient-details {
                grid-template-columns: 1fr;
            }
            
            .prescription-details {
                grid-template-columns: 1fr;
            }
            
            .patient-header {
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
            <h1>Set Reminder</h1>
            <p>Welcome, <?php echo htmlspecialchars($receptionist['FIRST_NAME'] . ' ' . $receptionist['LAST_NAME']); ?></p>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill me-2"></i><?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Reminder Card -->
        <div class="card">
            <div class="card-header">
                <h3>Reminders</h3>
            </div>
            <div class="card-body">
                <!-- Tabs -->
                <ul class="nav nav-tabs" id="reminderTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="prescriptions-tab" data-bs-toggle="tab" data-bs-target="#prescriptions" type="button" role="tab" aria-controls="prescriptions" aria-selected="true">Completed Prescriptions</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="reminders-tab" data-bs-toggle="tab" data-bs-target="#reminders" type="button" role="tab" aria-controls="reminders" aria-selected="false">Active Reminders</button>
                    </li>
                </ul>
                
                <!-- Tab Content -->
                <div class="tab-content" id="reminderTabContent">
                    <!-- Completed Prescriptions Tab -->
                    <div class="tab-pane fade show active" id="prescriptions" role="tabpanel" aria-labelledby="prescriptions-tab">
                        <?php
                        if (mysqli_num_rows($completed_appointments_query) > 0) {
                            while ($appointment = mysqli_fetch_assoc($completed_appointments_query)) {
                                // Fetch medicines for this prescription
                                $medicines_query = mysqli_query($conn, "
                                    SELECT m.MED_NAME, pm.DOSAGE, pm.DURATION, pm.FREQUENCY
                                    FROM prescription_medicine_tbl pm
                                    JOIN medicine_tbl m ON pm.MEDICINE_ID = m.MEDICINE_ID
                                    WHERE pm.PRESCRIPTION_ID = '" . $appointment['PRESCRIPTION_ID'] . "'
                                ");
                                ?>
                                <div class="patient-card">
                                    <div class="patient-header">
                                        <div class="patient-name">
                                            <?php echo htmlspecialchars($appointment['PAT_FNAME'] . ' ' . $appointment['PAT_LNAME']); ?>
                                        </div>
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#createPrescriptionReminderModal" 
                                                data-patient-id="<?php echo $appointment['PATIENT_ID']; ?>"
                                                data-patient-name="<?php echo htmlspecialchars($appointment['PAT_FNAME'] . ' ' . $appointment['PAT_LNAME']); ?>"
                                                data-prescription-id="<?php echo $appointment['PRESCRIPTION_ID']; ?>">
                                            <i class="bi bi-bell"></i> Set Reminder
                                        </button>
                                    </div>
                                    
                                    <div class="patient-details">
                                        <div class="patient-detail">
                                            <i class="bi bi-person"></i>
                                            <span>Patient ID: <?php echo $appointment['PATIENT_ID']; ?></span>
                                        </div>
                                        <div class="patient-detail">
                                            <i class="bi bi-telephone"></i>
                                            <span><?php echo htmlspecialchars($appointment['PHONE']); ?></span>
                                        </div>
                                        <div class="patient-detail">
                                            <i class="bi bi-envelope"></i>
                                            <span><?php echo htmlspecialchars($appointment['EMAIL']); ?></span>
                                        </div>
                                        <div class="patient-detail">
                                            <i class="bi bi-calendar"></i>
                                            <span>Appointment: <?php echo date('F d, Y', strtotime($appointment['APPOINTMENT_DATE'])); ?> at <?php echo date('h:i A', strtotime($appointment['APPOINTMENT_TIME'])); ?></span>
                                        </div>
                                        <div class="patient-detail">
                                            <i class="bi bi-person-badge"></i>
                                            <span>Dr. <?php echo htmlspecialchars($appointment['DOC_FNAME'] . ' ' . $appointment['DOC_LNAME']); ?> (<?php echo htmlspecialchars($appointment['SPECIALISATION_NAME']); ?>)</span>
                                        </div>
                                    </div>
                                    
                                    <?php if ($appointment['PRESCRIPTION_ID']): ?>
                                    <div class="prescription-section">
                                        <div class="prescription-header">
                                            <div class="prescription-title">
                                                Prescription Details (ID: <?php echo $appointment['PRESCRIPTION_ID']; ?>)
                                            </div>
                                        </div>
                                        
                                        <div class="prescription-details">
                                            <div class="prescription-detail">
                                                <i class="bi bi-calendar-check"></i>
                                                <span>Issue Date: <?php echo date('F d, Y', strtotime($appointment['ISSUE_DATE'])); ?></span>
                                            </div>
                                            <?php if ($appointment['HEIGHT_CM']): ?>
                                            <div class="prescription-detail">
                                                <i class="bi bi-rulers"></i>
                                                <span>Height: <?php echo $appointment['HEIGHT_CM']; ?> cm</span>
                                            </div>
                                            <?php endif; ?>
                                            <?php if ($appointment['WEIGHT_KG']): ?>
                                            <div class="prescription-detail">
                                                <i class="bi bi-speedometer2"></i>
                                                <span>Weight: <?php echo $appointment['WEIGHT_KG']; ?> kg</span>
                                            </div>
                                            <?php endif; ?>
                                            <?php if ($appointment['BLOOD_PRESSURE']): ?>
                                            <div class="prescription-detail">
                                                <i class="bi bi-heart-pulse"></i>
                                                <span>Blood Pressure: <?php echo $appointment['BLOOD_PRESSURE']; ?></span>
                                            </div>
                                            <?php endif; ?>
                                            <?php if ($appointment['DIABETES']): ?>
                                            <div class="prescription-detail">
                                                <i class="bi bi-droplet"></i>
                                                <span>Diabetes: <?php echo $appointment['DIABETES']; ?></span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <?php if ($appointment['SYMPTOMS']): ?>
                                        <div class="mb-2">
                                            <strong>Symptoms:</strong> <?php echo htmlspecialchars($appointment['SYMPTOMS']); ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($appointment['DIAGNOSIS']): ?>
                                        <div class="mb-2">
                                            <strong>Diagnosis:</strong> <?php echo htmlspecialchars($appointment['DIAGNOSIS']); ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <?php if ($appointment['ADDITIONAL_NOTES']): ?>
                                        <div class="mb-2">
                                            <strong>Additional Notes:</strong> <?php echo htmlspecialchars($appointment['ADDITIONAL_NOTES']); ?>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="medicines-list">
                                            <h5>Prescribed Medicines:</h5>
                                            <?php
                                            if (mysqli_num_rows($medicines_query) > 0) {
                                                while ($medicine = mysqli_fetch_assoc($medicines_query)) {
                                                    ?>
                                                    <div class="medicine-item">
                                                        <div class="medicine-info">
                                                            <div class="medicine-name"><?php echo htmlspecialchars($medicine['MED_NAME']); ?></div>
                                                            <div class="medicine-dosage">
                                                                Dosage: <?php echo htmlspecialchars($medicine['DOSAGE']); ?> | 
                                                                Duration: <?php echo htmlspecialchars($medicine['DURATION']); ?> | 
                                                                Frequency: <?php echo htmlspecialchars($medicine['FREQUENCY']); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                                // Reset the result pointer
                                                mysqli_data_seek($medicines_query, 0);
                                            } else {
                                                echo '<p>No medicines prescribed.</p>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle-fill me-2"></i>No prescription details available for this appointment.
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="empty-state">
                                <i class="bi bi-clipboard-x"></i>
                                <h4>No completed appointments found</h4>
                                <p>No patients with completed appointments and prescriptions found.</p>
                            </div>';
                        }
                        ?>
                    </div>
                    
                    <!-- Active Reminders Tab -->
                    <div class="tab-pane fade" id="reminders" role="tabpanel" aria-labelledby="reminders-tab">
                        <?php
                        if (mysqli_num_rows($medicine_reminders_query) > 0) {
                            while ($reminder = mysqli_fetch_assoc($medicine_reminders_query)) {
                                ?>
                                <div class="reminder-card">
                                    <div class="reminder-header">
                                        <div class="reminder-title">
                                            Reminder for <?php echo htmlspecialchars($reminder['PAT_FNAME'] . ' ' . $reminder['PAT_LNAME']); ?>
                                        </div>
                                        <span class="reminder-type">Medicine</span>
                                    </div>
                                    
                                    <div class="reminder-details">
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
                                <i class="bi bi-bell-slash"></i>
                                <h4>No reminders found</h4>
                                <p>No medicine reminders have been set yet.</p>
                            </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Create Prescription Reminder Modal -->
    <div class="modal fade" id="createPrescriptionReminderModal" tabindex="-1" aria-labelledby="createPrescriptionReminderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createPrescriptionReminderModalLabel">Create Prescription Reminder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="set_reminder.php">
                        <input type="hidden" name="create_prescription_reminder" value="1">
                        <input type="hidden" id="prescription_id" name="prescription_id">
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <span id="patient_info"></span>
                        </div>
                        
                        <div class="form-group">
                            <label for="reminder_date">Reminder Date</label>
                            <input type="date" class="form-control" id="reminder_date" name="reminder_date" required>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle prescription reminder modal
            const prescriptionReminderModal = document.getElementById('createPrescriptionReminderModal');
            
            if (prescriptionReminderModal) {
                prescriptionReminderModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const patientId = button.getAttribute('data-patient-id');
                    const patientName = button.getAttribute('data-patient-name');
                    const prescriptionId = button.getAttribute('data-prescription-id');
                    
                    // Update modal content
                    document.getElementById('patient_info').textContent = `Setting reminder for ${patientName} (ID: ${patientId})`;
                    document.getElementById('prescription_id').value = prescriptionId;
                    
                    // Set default date to today
                    const today = new Date().toISOString().split('T')[0];
                    document.getElementById('reminder_date').value = today;
                });
            }
        });
    </script>
</body>
</html>