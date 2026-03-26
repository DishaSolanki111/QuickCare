<?php
// ================== SESSION & ACCESS CONTROL ==================
session_start();

if (
    !isset($_SESSION['LOGGED_IN']) ||
    $_SESSION['LOGGED_IN'] !== true ||
    !isset($_SESSION['USER_TYPE']) ||
    $_SESSION['USER_TYPE'] !== 'doctor'
) {
    header("Location: login_for_all.php");
    exit();
}

// ================== DATABASE CONNECTION ==================
include 'config.php';

// ================== DOCTOR INFO ==================
 $doctor_id = isset($_SESSION['DOCTOR_ID']) ? (int) $_SESSION['DOCTOR_ID'] : 0;
if ($doctor_id <= 0) {
    header("Location: login_for_all.php");
    exit();
}
 $doctor_name = "Doctor";

 $doc_sql = "SELECT FIRST_NAME, LAST_NAME FROM doctor_tbl WHERE DOCTOR_ID = ?";
 $doc_stmt = $conn->prepare($doc_sql);
 $doc_stmt->bind_param("i", $doctor_id);
 $doc_stmt->execute();
 $doc_result = $doc_stmt->get_result();

if ($doc_result->num_rows === 1) {
    $doc = $doc_result->fetch_assoc();
    $doctor_name = htmlspecialchars($doc['FIRST_NAME'] . ' ' . $doc['LAST_NAME']);
}
 $doc_stmt->close();

// ================== HANDLE APPOINTMENT STATUS UPDATE ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $appointment_id = isset($_POST['appointment_id']) ? (int) $_POST['appointment_id'] : 0;
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';

    if ($appointment_id > 0 && in_array($status, ['COMPLETED', 'CANCELLED'], true)) {
        $update_sql = "UPDATE appointment_tbl SET STATUS = ? WHERE APPOINTMENT_ID = ? AND DOCTOR_ID = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sii", $status, $appointment_id, $doctor_id);

        if ($update_stmt->execute()) {
            $_SESSION['APPOINTMENT_STATUS_SUCCESS'] = $status === 'COMPLETED'
                ? "Appointment marked as completed. It now appears under Past Appointments."
                : "Appointment cancelled.";
            $update_stmt->close();

            if ($status === 'CANCELLED') {
                $row = $conn->query("SELECT PATIENT_ID, APPOINTMENT_DATE, APPOINTMENT_TIME FROM appointment_tbl WHERE APPOINTMENT_ID = " . (int)$appointment_id . " AND DOCTOR_ID = " . (int)$doctor_id)->fetch_assoc();
                if ($row) {
                    $doc_name_r = $conn->query("SELECT FIRST_NAME, LAST_NAME FROM doctor_tbl WHERE DOCTOR_ID = " . (int)$doctor_id);
                    $doctor_name_notif = $doc_name_r && ($dn = $doc_name_r->fetch_assoc()) ? 'Dr. ' . trim($dn['FIRST_NAME'] . ' ' . $dn['LAST_NAME']) : 'your doctor';
                    $date_time = date('M d, Y', strtotime($row['APPOINTMENT_DATE'])) . ' at ' . date('h:i A', strtotime($row['APPOINTMENT_TIME']));
                    $msg = "[CANCELLED] Your appointment with " . $doctor_name_notif . " on " . $date_time . " was cancelled by the doctor. Please book a new appointment if needed.";
                    $med_id = 1;
                    $med_r = $conn->query("SELECT MEDICINE_ID FROM medicine_tbl ORDER BY MEDICINE_ID ASC LIMIT 1");
                    if ($med_r && ($mr = $med_r->fetch_assoc())) $med_id = (int)$mr['MEDICINE_ID'];
                    $today = date('Y-m-d');
                    $now = date('H:i:s');
                    $ins = $conn->prepare("INSERT INTO medicine_reminder_tbl (MEDICINE_ID, CREATOR_ROLE, CREATOR_ID, PATIENT_ID, START_DATE, END_DATE, REMINDER_TIME, REMARKS) VALUES (?, 'RECEPTIONIST', 1, ?, ?, ?, ?, ?)");
                    $pat_id = (int) $row['PATIENT_ID'];
                    $ins->bind_param("iisssss", $med_id, $pat_id, $today, $today, $now, $msg);
                    $ins->execute();
                    $ins->close();

                    // Notify receptionist: insert into appointment_reminder_tbl
                    $rec_msg = "[CANCELLED_BY_DOCTOR] " . $doctor_name_notif . " cancelled the appointment for " . $date_time . ".";
                    $ins2 = $conn->prepare("INSERT INTO appointment_reminder_tbl (RECEPTIONIST_ID, APPOINTMENT_ID, REMINDER_TIME, REMARKS) VALUES (1, ?, ?, ?)");
                    $ins2->bind_param("iss", $appointment_id, date('H:i:s'), $rec_msg);
                    $ins2->execute();
                    $ins2->close();
                }
            }

            if ($status === 'COMPLETED') $_SESSION['ACTIVE_TAB'] = 'past';
            elseif ($status === 'CANCELLED') $_SESSION['ACTIVE_TAB'] = 'cancelled';
            header('Location: appointment_doctor.php');
            exit;
        }
        $error_message = "Error updating appointment status: " . $conn->error;
        $update_stmt->close();
    } else {
        $error_message = "Invalid request.";
    }
}

// Reschedule flash messages
if (isset($_SESSION['RESCHEDULE_SUCCESS'])) {
    $success_message = $_SESSION['RESCHEDULE_SUCCESS'];
    unset($_SESSION['RESCHEDULE_SUCCESS']);
}
if (isset($_SESSION['RESCHEDULE_ERROR'])) {
    $error_message = $_SESSION['RESCHEDULE_ERROR'];
    unset($_SESSION['RESCHEDULE_ERROR']);
}
// Status update flash (Mark as Completed / Cancel)
if (isset($_SESSION['APPOINTMENT_STATUS_SUCCESS'])) {
    $success_message = $_SESSION['APPOINTMENT_STATUS_SUCCESS'];
    unset($_SESSION['APPOINTMENT_STATUS_SUCCESS']);
}
if (isset($_SESSION['PRESCRIPTION_ALREADY_ADDED'])) {
    $error_message = $_SESSION['PRESCRIPTION_ALREADY_ADDED'];
    unset($_SESSION['PRESCRIPTION_ALREADY_ADDED']);
}

// ================== SYNC: appointments with completed prescription should be COMPLETED ==================
$conn->query("
    UPDATE appointment_tbl a
    INNER JOIN prescription_tbl p ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
    SET a.STATUS = 'COMPLETED'
    WHERE a.DOCTOR_ID = " . (int)$doctor_id . "
      AND a.STATUS = 'SCHEDULED'
      AND TRIM(COALESCE(p.DIAGNOSIS,'')) != ''
");

// ================== FETCH APPOINTMENTS ==================
// Fetch appointments for the logged-in doctor from appointment_tbl; filter by STATUS.
$today_php = date('Y-m-d');

$all_appointments = [];
$all_sql = "
    SELECT a.*, p.FIRST_NAME as PAT_FNAME, p.LAST_NAME as PAT_LNAME, p.PHONE as PAT_PHONE, p.EMAIL as PAT_EMAIL
    FROM appointment_tbl a
    JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
    WHERE a.DOCTOR_ID = ?
    ORDER BY a.APPOINTMENT_DATE ASC, a.APPOINTMENT_TIME ASC
";
$all_stmt = $conn->prepare($all_sql);
$all_stmt->bind_param("i", $doctor_id);
$all_stmt->execute();
$all_result = $all_stmt->get_result();
if ($all_result) {
    while ($row = $all_result->fetch_assoc()) {
        if ((int)($row['DOCTOR_ID'] ?? 0) === $doctor_id) {
            $all_appointments[] = $row;
        }
    }
}
$all_stmt->close();

$today_appointments = [];
$upcoming_appointments = [];
$past_appointments = [];
$cancelled_appointments = [];

foreach ($all_appointments as $appt) {
    $appt_date = $appt['APPOINTMENT_DATE'];
    $status = strtoupper(trim($appt['STATUS'] ?? ''));

    // STATUS = CANCELLED → Cancelled tab
    if ($status === 'CANCELLED') {
        $cancelled_appointments[] = $appt;
        continue;
    }

    // STATUS = COMPLETED → Past tab
    if ($status === 'COMPLETED') {
        $past_appointments[] = $appt;
        continue;
    }

    // STATUS = SCHEDULED → Today (date = today) or Upcoming for all scheduled entries
    if ($status === 'SCHEDULED') {
        if ($appt_date === $today_php) {
            $today_appointments[] = $appt;
        } else {
            $upcoming_appointments[] = $appt;
        }
    }
}

// Read tab from POST first, then session, then GET (fallback only), default 'today'
if (isset($_POST['tab']) && in_array($_POST['tab'], ['today', 'upcoming', 'past', 'cancelled'], true)) {
    $active_tab = $_POST['tab'];
    $_SESSION['ACTIVE_TAB'] = $active_tab;
} elseif (isset($_SESSION['ACTIVE_TAB']) && in_array($_SESSION['ACTIVE_TAB'], ['today', 'upcoming', 'past', 'cancelled'], true)) {
    $active_tab = $_SESSION['ACTIVE_TAB'];
    unset($_SESSION['ACTIVE_TAB']);
} elseif (isset($_GET['tab']) && in_array($_GET['tab'], ['today', 'upcoming', 'past', 'cancelled'], true)) {
    $active_tab = $_GET['tab'];
} else {
    $active_tab = 'today';
}

// Appointment IDs that have any prescription row + map to prescription_id (for direct link to edit form)
$appointment_ids_with_prescription = [];
$prescription_id_by_apt = [];
$pr_q = $conn->query("SELECT p.APPOINTMENT_ID, p.PRESCRIPTION_ID FROM prescription_tbl p JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID WHERE a.DOCTOR_ID = " . (int)$doctor_id);
if ($pr_q) { while ($pr = $pr_q->fetch_assoc()) { $appointment_ids_with_prescription[] = (int)$pr['APPOINTMENT_ID']; $prescription_id_by_apt[(int)$pr['APPOINTMENT_ID']] = (int)$pr['PRESCRIPTION_ID']; } }
// Appointment IDs that have a completed prescription (doctor already submitted) — hide "Add Prescription" for these
$appointment_ids_with_complete_prescription = [];
$pr_complete_q = $conn->query("SELECT p.APPOINTMENT_ID FROM prescription_tbl p JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID WHERE a.DOCTOR_ID = " . (int)$doctor_id . " AND TRIM(COALESCE(p.DIAGNOSIS,'')) != ''");
if ($pr_complete_q) { while ($pr = $pr_complete_q->fetch_assoc()) $appointment_ids_with_complete_prescription[] = (int)$pr['APPOINTMENT_ID']; }

 $conn->close();
error_log("doctor_id=$doctor_id today=$today_php upcoming:".count($upcoming_appointments));
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
            --primary: #0066cc;
            --primary-dark: #0052a3;
            --primary-light: #e6f2ff;
            --secondary: #00a8cc;
            --accent: #00a86b;
            --warning: #ff6b6b;
            --dark: #1a3a5f;
            --light: #f8fafc;
            --white: #ffffff;
            --text: #2c5282;
            --text-light: #4a6fa5;
            --gradient-1: linear-gradient(135deg, #0066cc 0%, #00a8cc 100%);
            --gradient-2: linear-gradient(135deg, #00a8cc 0%, #00a86b 100%);
            --gradient-3: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --shadow-xl: 0 20px 25px rgba(0,0,0,0.1);
            --shadow-2xl: 0 25px 50px rgba(0,0,0,0.25);
            --dark-blue: #072D44;
            --mid-blue: #064469;
            --soft-blue: #5790AB;
            --light-blue: #9CCDD8;
            --gray-blue: #D0D7E1;
            --white: #ffffff;
            --card-bg: #F6F9FB;
            --primary-color: #1a3a5f;
            --info-color: #17a2b8;
            --danger-color: #ff6b6b;        }

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
            margin-top:-15px;
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
        
        .notification-btn {
            position: relative;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--dark-color);
            margin-right: 20px;
            cursor: pointer;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }
        
        .user-dropdown {
            display: flex;
            align-items: center;
            cursor: pointer;
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
            .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding: 10px;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow-md);
        }
        /* Appointment Content */
        .appointment-content {
            padding: 5px;
        }

        .tabs {
            display: flex;
            margin-bottom: 25px;
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .tab {
            flex: 1;
            padding: 15px 20px;
            text-align: center;
            cursor: pointer;
            background: var(--white);
            color: var(--text-light);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .tab.active {
            background: var(--dark-blue);
            color: var(--white);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .appointment-card {
            background: var(--white);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
        }

        .appointment-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1px solid #eee;
        }

        .patient-info h3 {
            font-size: 20px;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .patient-info p {
            color: var(--text-light);
            margin-bottom: 4px;
        }

        .patient-info p:last-child {
            margin-bottom: 0;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-scheduled {
            background-color: rgba(0, 102, 204, 0.1);
            color: var(--primary);
        }

        .status-completed {
            background-color: rgba(0, 168, 107, 0.1);
            color: var(--accent);
        }

        .status-cancelled {
            background-color: rgba(255, 107, 107, 0.1);
            color: var(--warning);
        }

        .appointment-details {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-start;
            gap: 20px;
            margin-bottom: 16px;
        }

        .appointment-detail {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #666;
        }

        .appointment-detail i {
            color: var(--primary);
        }

        .appointment-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .btn i {
            flex-shrink: 0;
        }

        .btn-primary {
            background-color: var(--dark-blue);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-success {
            background-color: var(--accent);
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

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #777;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow-md);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
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

        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.active {
            display: flex;
        }
        .modal-box {
            background: white;
            padding: 25px 30px;
            border-radius: 10px;
            max-width: 450px;
            width: 90%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .modal-box h3 {
            margin-bottom: 20px;
            color: var(--primary);
        }
        .modal-box .form-group {
            margin-bottom: 15px;
        }
        .modal-box label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
        }
        .modal-box input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        .modal-box .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .modal-box .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar-header h2 {
                display: none;
            }
            
            .sidebar-nav a span {
                display: none;
            }
            
            .sidebar-nav a {
                justify-content: center;
            }
            
            .sidebar-nav a i {
                margin: 0;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .appointment-details {
                gap: 15px;
            }
        }

        @media (max-width: 768px) {
    
            
            .main-content {
                margin-left: 0;
            }
            
          
            .appointment-content {
                padding: 20px;
            }
            
            .appointment-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .appointment-actions {
                flex-wrap: wrap;
            }
        }

        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--dark);
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
 
 <?php include 'doctor_sidebar.php'; ?>
 
    <!-- Main Content -->
    <div class="main-content">
        
        <?php include 'doctor_header.php'; ?>
        <!-- Appointment Content -->
        <div class="appointment-content">
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
            
            <!-- Tabs -->
            <div class="tabs">
                <div class="tab <?= $active_tab === 'today' ? 'active' : '' ?>" data-tab="today">Today's Appointments</div>
                <div class="tab <?= $active_tab === 'upcoming' ? 'active' : '' ?>" data-tab="upcoming">Upcoming Appointments</div>
                <div class="tab <?= $active_tab === 'past' ? 'active' : '' ?>" data-tab="past">Past Appointments</div>
                <div class="tab <?= $active_tab === 'cancelled' ? 'active' : '' ?>" data-tab="cancelled">Cancelled Appointments</div>
            </div>
            
            <!-- Tab Content -->
            <div class="tab-content <?= $active_tab === 'today' ? 'active' : '' ?>" id="today">
                <?php if (count($today_appointments) > 0): ?>
                    <?php foreach ($today_appointments as $appointment): ?>
                        <div class="appointment-card">
                            <div class="appointment-header">
                                <div class="patient-info">
                                    <h3><?php echo htmlspecialchars($appointment['PAT_FNAME'] . ' ' . $appointment['PAT_LNAME']); ?></h3>
                                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($appointment['PAT_PHONE']); ?></p>
                                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($appointment['PAT_EMAIL']); ?></p>
                                </div>
                                <span class="status-badge status-<?php echo strtolower($appointment['STATUS']); ?>">
                                    <?php echo $appointment['STATUS']; ?>
                                </span>
                            </div>
                            
                            <div class="appointment-details">
                                <div class="appointment-detail">
                                    <i class="far fa-calendar"></i>
                                    <span><?php echo date('F d, Y', strtotime($appointment['APPOINTMENT_DATE'])); ?></span>
                                </div>
                                <div class="appointment-detail">
                                    <i class="far fa-clock"></i>
                                    <span><?php echo date('h:i A', strtotime($appointment['APPOINTMENT_TIME'])); ?></span>
                                </div>
                                <?php if (isset($appointment['REASON'])): ?>
                                <div class="appointment-detail">
                                    <i class="fas fa-stethoscope"></i>
                                    <span><?php echo htmlspecialchars($appointment['REASON']); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (isset($appointment['NOTES'])): ?>
                                <div class="appointment-detail">
                                    <i class="fas fa-notes-medical"></i>
                                    <span><?php echo htmlspecialchars($appointment['NOTES']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="appointment-actions">
                                <?php if ($appointment['STATUS'] === 'SCHEDULED' && !in_array((int)$appointment['APPOINTMENT_ID'], $appointment_ids_with_complete_prescription)): ?>
                                    <form method="POST" action="prescription_form.php" style="display: inline;">
                                        <input type="hidden" name="patient_id" value="<?php echo (int)$appointment['PATIENT_ID']; ?>">
                                        <input type="hidden" name="appointment_id" value="<?php echo (int)$appointment['APPOINTMENT_ID']; ?>">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-file-prescription"></i> Add Prescription
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                <?php if ($appointment['STATUS'] === 'SCHEDULED'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['APPOINTMENT_ID']; ?>">
                                        <input type="hidden" name="status" value="CANCELLED">
                                        <button type="submit" name="update_status" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <?php if (in_array((int)$appointment['APPOINTMENT_ID'], $appointment_ids_with_prescription)): 
                                    $pid = $prescription_id_by_apt[(int)$appointment['APPOINTMENT_ID']] ?? 0;
                                    $patid = (int)$appointment['PATIENT_ID'];
                                    if ($pid && $patid): ?>
                                <a href="prescription_form.php?patient_id=<?php echo $patid; ?>&prescription_id=<?php echo $pid; ?>" class="btn btn-primary">
                                    <i class="fas fa-file-medical"></i> View Prescription
                                </a>
                                <?php endif; endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="far fa-calendar-times"></i>
                        <h3>No Appointments Today</h3>
                        <p>You don't have any appointments scheduled for today.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="tab-content <?= $active_tab === 'upcoming' ? 'active' : '' ?>" id="upcoming">
                <?php if (count($upcoming_appointments) > 0): ?>
                    <?php foreach ($upcoming_appointments as $appointment): ?>
                        <div class="appointment-card">
                            <div class="appointment-header">
                                <div class="patient-info">
                                    <h3><?php echo htmlspecialchars($appointment['PAT_FNAME'] . ' ' . $appointment['PAT_LNAME']); ?></h3>
                                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($appointment['PAT_PHONE']); ?></p>
                                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($appointment['PAT_EMAIL']); ?></p>
                                </div>
                                <span class="status-badge status-<?php echo strtolower($appointment['STATUS']); ?>">
                                    <?php echo $appointment['STATUS']; ?>
                                </span>
                                
                            </div>
                            
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
                            
                            <div class="appointment-actions">
                                <form method="POST" action="book_appointment_date.php" style="display:inline">
                                    <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
                                    <input type="hidden" name="reschedule_appointment_id" value="<?php echo $appointment['APPOINTMENT_ID']; ?>">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Reschedule
                                    </button>
                                </form>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="appointment_id" value="<?php echo $appointment['APPOINTMENT_ID']; ?>">
                                    <input type="hidden" name="status" value="CANCELLED">
                                    <button type="submit" name="update_status" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                        <i class="fas fa-times"></i> Cancel
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="far fa-calendar-times"></i>
                        <h3>No Upcoming Appointments</h3>
                        <p>You don't have any upcoming appointments scheduled.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="tab-content <?= $active_tab === 'past' ? 'active' : '' ?>" id="past">
                <?php if (count($past_appointments) > 0): ?>
                    <?php foreach ($past_appointments as $appointment): ?>
                        <div class="appointment-card">
                            <div class="appointment-header">
                                <div class="patient-info">
                                    <h3><?php echo htmlspecialchars($appointment['PAT_FNAME'] . ' ' . $appointment['PAT_LNAME']); ?></h3>
                                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($appointment['PAT_PHONE']); ?></p>
                                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($appointment['PAT_EMAIL']); ?></p>
                                </div>
                                <span class="status-badge status-<?php echo strtolower($appointment['STATUS']); ?>">
                                    <?php echo $appointment['STATUS']; ?>
                                </span>
                            </div>
                            
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
                            
                            <?php if ($appointment['STATUS'] !== 'CANCELLED'): ?>
                            <div class="appointment-actions">
                                <?php if (in_array((int)$appointment['APPOINTMENT_ID'], $appointment_ids_with_prescription)): 
                                    $pid = $prescription_id_by_apt[(int)$appointment['APPOINTMENT_ID']] ?? 0;
                                    $patid = (int)$appointment['PATIENT_ID'];
                                    if ($pid && $patid): ?>
                                <a href="prescription_form.php?patient_id=<?php echo $patid; ?>&prescription_id=<?php echo $pid; ?>" class="btn btn-primary">
                                    <i class="fas fa-file-medical"></i> View Prescription
                                </a>
                                <?php endif; endif; ?>
                                <button class="btn btn-success" onclick="viewFeedback(<?php echo $appointment['APPOINTMENT_ID']; ?>)">
                                    <i class="fas fa-star"></i> View Feedback
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="far fa-calendar-times"></i>
                        <h3>No Past Appointments</h3>
                        <p>You don't have any past appointments.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tab-content <?= $active_tab === 'cancelled' ? 'active' : '' ?>" id="cancelled">
                <?php if (count($cancelled_appointments) > 0): ?>
                    <?php foreach ($cancelled_appointments as $appointment): ?>
                        <div class="appointment-card">
                            <div class="appointment-header">
                                <div class="patient-info">
                                    <h3><?php echo htmlspecialchars($appointment['PAT_FNAME'] . ' ' . $appointment['PAT_LNAME']); ?></h3>
                                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($appointment['PAT_PHONE']); ?></p>
                                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($appointment['PAT_EMAIL']); ?></p>
                                </div>
                                <span class="status-badge status-cancelled"><?php echo $appointment['STATUS']; ?></span>
                            </div>
                            <div class="appointment-details">
                                <div class="appointment-detail">
                                    <i class="far fa-calendar"></i>
                                    <span><?php echo date('F d, Y', strtotime($appointment['APPOINTMENT_DATE'])); ?></span>
                                </div>
                                <div class="appointment-detail">
                                    <i class="far fa-clock"></i>
                                    <span><?php echo date('h:i A', strtotime($appointment['APPOINTMENT_TIME'])); ?></span>
                                </div>
                                <?php if (!empty($appointment['REASON'])): ?>
                                <div class="appointment-detail">
                                    <i class="fas fa-stethoscope"></i>
                                    <span><?php echo htmlspecialchars($appointment['REASON']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="far fa-calendar-times"></i>
                        <h3>No Cancelled Appointments</h3>
                        <p>You don't have any cancelled appointments.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
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
            
            // Mobile menu toggle
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            if (menuToggle && sidebar) {
                menuToggle.addEventListener('click', () => {
                    sidebar.classList.toggle('active');
                });
                document.addEventListener('click', (e) => {
                    if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                        sidebar.classList.remove('active');
                    }
                });
            }
        });
        
        // View prescription function
        function viewPrescription(appointmentId) {
            // Submit POST form to prescription page
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'manage_prescriptions.php';
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'appointment';
            input.value = appointmentId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
        
        // View feedback function
        function viewFeedback(appointmentId) {
            // Submit POST form to feedback page
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'doctor_feedback.php';
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'appointment';
            input.value = appointmentId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>