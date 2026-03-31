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

// Preserve form values on validation error
$form_data = [
    'medicine_id'   => '',
    'start_date'    => '',
    'end_date'      => '',
    'reminder_time' => '',
    'remarks'       => '',
];

// Determine if this page is linked to a specific prescription
$prescription_id = '';
if (isset($_POST['prescription_id']) && $_POST['prescription_id'] !== '') {
    $prescription_id = mysqli_real_escape_string($conn, $_POST['prescription_id']);
} elseif (isset($_POST['prescription']) && $_POST['prescription'] !== '') {
    // Coming directly from prescriptions page
    $prescription_id = mysqli_real_escape_string($conn, $_POST['prescription']);
}

// Preload prescription medicine meta (duration, issue date) for auto dates
$prescription_medicine_meta = [];
if (!empty($prescription_id)) {
    $prescription_meta_q = mysqli_query($conn, "
        SELECT pm.MEDICINE_ID, pm.DURATION, p.ISSUE_DATE
        FROM prescription_medicine_tbl pm
        JOIN prescription_tbl p ON pm.PRESCRIPTION_ID = p.PRESCRIPTION_ID
        WHERE pm.PRESCRIPTION_ID = '$prescription_id'
    ");
    if ($prescription_meta_q && mysqli_num_rows($prescription_meta_q) > 0) {
        while ($row = mysqli_fetch_assoc($prescription_meta_q)) {
            $prescription_medicine_meta[$row['MEDICINE_ID']] = [
                'duration'   => $row['DURATION'],
                'issue_date' => $row['ISSUE_DATE'],
            ];
        }
    }
}

// Handle form submission for adding new reminder
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_reminder'])) {
    $medicine_id_raw   = isset($_POST['medicine_id'])   ? trim($_POST['medicine_id'])   : '';
    $start_date_raw    = isset($_POST['start_date'])    ? trim($_POST['start_date'])    : '';
    $end_date_raw      = isset($_POST['end_date'])      ? trim($_POST['end_date'])      : '';
    $reminder_time_raw = isset($_POST['reminder_time']) ? trim($_POST['reminder_time']) : '';
    $remarks_raw       = isset($_POST['remarks'])       ? trim($_POST['remarks'])       : '';

    // Update form_data so that user input is preserved on error
    $form_data = [
        'medicine_id'   => $medicine_id_raw,
        'start_date'    => $start_date_raw,
        'end_date'      => $end_date_raw,
        'reminder_time' => $reminder_time_raw,
        'remarks'       => $remarks_raw,
    ];

    // Sanitized copies for DB use
    $medicine_id   = $medicine_id_raw   !== '' ? mysqli_real_escape_string($conn, $medicine_id_raw)   : '';
    $start_date    = $start_date_raw    !== '' ? mysqli_real_escape_string($conn, $start_date_raw)    : '';
    $end_date      = $end_date_raw      !== '' ? mysqli_real_escape_string($conn, $end_date_raw)      : '';
    $reminder_time = $reminder_time_raw !== '' ? mysqli_real_escape_string($conn, $reminder_time_raw) : '';
    $remarks       = $remarks_raw       !== '' ? mysqli_real_escape_string($conn, $remarks_raw)       : '';
    
    // Validate required fields
    if (empty($medicine_id) || empty($start_date) || empty($end_date) || empty($reminder_time)) {
        $error_message = "Please fill in Medicine, Start Date, End Date, and Reminder Time.";
    } else {
        // If linked to a specific prescription, enforce date range:
        // start date cannot be before prescription issue date
        // end date cannot be after (issue date + duration - 1 day)
        if (!empty($prescription_id) && !empty($medicine_id) && isset($prescription_medicine_meta[$medicine_id])) {
            $issue_date_raw   = $prescription_medicine_meta[$medicine_id]['issue_date'];
            $duration_str     = (string) $prescription_medicine_meta[$medicine_id]['duration'];
            if (!empty($issue_date_raw) && preg_match('/(\d+)\s*day/i', $duration_str, $m)) {
                $days = (int) $m[1];
                if ($days > 0) {
                    $issue_date = date('Y-m-d', strtotime($issue_date_raw));
                    // Example: issue 7 March + 30 days → last allowed day = 6 April
                    $max_date  = date('Y-m-d', strtotime($issue_date . ' + ' . ($days - 1) . ' days'));

                    if ($start_date < $issue_date || $start_date > $max_date || $end_date < $issue_date || $end_date > $max_date || $end_date < $start_date) {
                        $error_message = "For this medicine, start date must be on or after the prescription date and end date cannot go beyond the prescribed duration.";
                    }
                }
            }
        }

        if (empty($error_message)) {
        // Verify that this medicine is prescribed to the patient
        $verify_query = mysqli_query($conn, "
            SELECT COUNT(*) as count
            FROM prescription_medicine_tbl pm
            JOIN prescription_tbl p 
                ON pm.PRESCRIPTION_ID = p.PRESCRIPTION_ID
            JOIN appointment_tbl a 
                ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
            WHERE pm.MEDICINE_ID = '$medicine_id'
              AND a.PATIENT_ID = '$patient_id'
        ");

        $verify_result = $verify_query ? mysqli_fetch_assoc($verify_query) : null;
        
        if ($verify_result && (int)$verify_result['count'] > 0) {
            // Get the next ID for MEDICINE_REMINDER_ID
            $id_query = mysqli_query($conn, "SELECT MAX(MEDICINE_REMINDER_ID) as max_id FROM medicine_reminder_tbl");
            $id_result = mysqli_fetch_assoc($id_query);
            $next_id = ($id_result['max_id'] ?? 0) + 1;
            
            $add_query = "INSERT INTO medicine_reminder_tbl (MEDICINE_REMINDER_ID, MEDICINE_ID, CREATOR_ROLE, CREATOR_ID, PATIENT_ID, START_DATE, END_DATE, REMINDER_TIME, REMARKS) 
                          VALUES ('$next_id', '$medicine_id', 'PATIENT', '$patient_id', '$patient_id', '$start_date', '$end_date', '$reminder_time', '$remarks')";
            
            if (mysqli_query($conn, $add_query)) {
                // On success, clear form fields and show message;
                // reminders list below will include the newly added record.
                $success_message = "Medicine reminder added successfully!";
                $form_data = [
                    'medicine_id'   => '',
                    'start_date'    => '',
                    'end_date'      => '',
                    'reminder_time' => '',
                    'remarks'       => '',
                ];
            } else {
                $error_message = "Error adding reminder: " . mysqli_error($conn);
            }
        } else {
            $error_message = "You can only set reminders for medicines prescribed to you.";
        }
        }
    }
}

// Handle form submission for updating reminder
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_reminder'])) {
    $reminder_id   = isset($_POST['reminder_id'])   ? mysqli_real_escape_string($conn, $_POST['reminder_id'])   : '';
    $start_date    = isset($_POST['start_date'])    ? mysqli_real_escape_string($conn, $_POST['start_date'])    : '';
    $end_date      = isset($_POST['end_date'])      ? mysqli_real_escape_string($conn, $_POST['end_date'])      : '';
    $reminder_time = isset($_POST['reminder_time']) ? mysqli_real_escape_string($conn, $_POST['reminder_time']) : '';
    $remarks       = isset($_POST['remarks'])       ? mysqli_real_escape_string($conn, $_POST['remarks'])       : '';
    
    $update_query = "UPDATE medicine_reminder_tbl SET 
                    START_DATE = '$start_date',
                    END_DATE = '$end_date',
                    REMINDER_TIME = '$reminder_time',
                    REMARKS = '$remarks'
                    WHERE MEDICINE_REMINDER_ID = '$reminder_id' AND PATIENT_ID = '$patient_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $success_message = "Medicine reminder updated successfully!";
    } else {
        $error_message = "Error updating reminder: " . mysqli_error($conn);
    }
}

// Handle reminder deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_reminder'])) {
    $reminder_id = mysqli_real_escape_string($conn, $_POST['reminder_id']);
    
    $delete_query = "DELETE FROM medicine_reminder_tbl WHERE MEDICINE_REMINDER_ID = '$reminder_id' AND PATIENT_ID = '$patient_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        $success_message = "Medicine reminder deleted successfully!";
    } else {
        $error_message = "Error deleting reminder: " . mysqli_error($conn);
    }
}

// Clean up expired reminders (past their end date) for this patient.
// Run this only on non-add-reminder requests so freshly added records
// (even with past dates) are still visible to the user.
if (!($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_reminder']))) {
    mysqli_query(
        $conn,
        "DELETE FROM medicine_reminder_tbl 
         WHERE PATIENT_ID = '" . mysqli_real_escape_string($conn, $patient_id) . "'
           AND END_DATE < CURDATE()"
    );
}

// Fetch ONLY medicines prescribed to this patient (all prescriptions)
 $prescribed_medicines_query = mysqli_query($conn, "
    SELECT DISTINCT m.MEDICINE_ID, m.MED_NAME
    FROM medicine_tbl m
    JOIN prescription_medicine_tbl pm 
        ON m.MEDICINE_ID = pm.MEDICINE_ID
    JOIN prescription_tbl p 
        ON pm.PRESCRIPTION_ID = p.PRESCRIPTION_ID
    JOIN appointment_tbl a 
        ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
    WHERE a.PATIENT_ID = '$patient_id'
    ORDER BY m.MED_NAME
");

// Fetch medicine reminders (use LEFT JOIN so reminders still appear even if medicine record was removed)
$reminders_query = mysqli_query($conn, "
    SELECT mr.*, m.MED_NAME
    FROM medicine_reminder_tbl mr
    LEFT JOIN medicine_tbl m ON mr.MEDICINE_ID = m.MEDICINE_ID
    WHERE mr.PATIENT_ID = '$patient_id'
    ORDER BY mr.START_DATE, mr.REMINDER_TIME
");

// Appointment reminders for notification bell
$reminder_query = mysqli_query($conn, "
    SELECT ar.REMARKS, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, d.FIRST_NAME, d.LAST_NAME
    FROM appointment_reminder_tbl ar
    JOIN appointment_tbl a ON ar.APPOINTMENT_ID = a.APPOINTMENT_ID
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    WHERE a.PATIENT_ID = '$patient_id'
    AND a.APPOINTMENT_DATE >= CURDATE()
    AND ar.REMINDER_TIME <= CURTIME()
    AND ar.REMINDER_TIME > DATE_SUB(CURTIME(), INTERVAL 1 HOUR)
    ORDER BY ar.REMINDER_TIME DESC
    LIMIT 5
");

// Medicine reminders for notification bell (today, fired in last hour)
$medicine_reminder_query = mysqli_query($conn, "
    SELECT mr.REMARKS, mr.REMINDER_TIME, m.MED_NAME, CURDATE() as REMINDER_DATE
    FROM medicine_reminder_tbl mr
    JOIN medicine_tbl m ON mr.MEDICINE_ID = m.MEDICINE_ID
    WHERE mr.PATIENT_ID = '$patient_id'
    AND CURDATE() BETWEEN mr.START_DATE AND mr.END_DATE
    AND mr.REMINDER_TIME <= CURTIME()
    AND mr.REMINDER_TIME > DATE_SUB(CURTIME(), INTERVAL 1 HOUR)
    ORDER BY mr.REMINDER_TIME DESC
    LIMIT 5
");

// If prescription ID is provided, get medicines from that prescription; otherwise fall back to all prescribed medicines
$prescription_medicines = [];
if (!empty($prescription_id)) {
    $prescription_medicines_query = mysqli_query($conn, "
        SELECT pm.MEDICINE_ID, m.MED_NAME
        FROM prescription_medicine_tbl pm
        JOIN medicine_tbl m ON pm.MEDICINE_ID = m.MEDICINE_ID
        WHERE pm.PRESCRIPTION_ID = '$prescription_id'
    ");
    
    while ($medicine = mysqli_fetch_assoc($prescription_medicines_query)) {
        $prescription_medicines[] = $medicine;
    }
} else {
    if ($prescribed_medicines_query && mysqli_num_rows($prescribed_medicines_query) > 0) {
        while ($medicine = mysqli_fetch_assoc($prescribed_medicines_query)) {
            $prescription_medicines[] = [
                'MEDICINE_ID' => $medicine['MEDICINE_ID'],
                'MED_NAME'    => $medicine['MED_NAME'],
            ];
        }
        // reset pointer for later select options usage
        mysqli_data_seek($prescribed_medicines_query, 0);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Reminder - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0077b6;
            --secondary-color: #0077b6;
            --accent-color: #0077b6;
            --danger-color: #e74c3c;
            --dark-blue: #1A365D;
            --mid-blue: #064469;
            --soft-blue: #5790AB;
            --light-blue: #caf0f8;
            --gray-blue: #D0D7E1;
            --white: #ffffff;
            --card-bg: rgba(255, 255, 255, 0.95);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
        }
        
        body {
            background-color: #f7fafc;
            display: flex;
            height: 100vh;
            overflow: hidden;
            color: #1A365D;
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
            padding: 12px;
            width: calc(100% - 250px);
            max-width: none;
            height: 100vh;
            overflow-y: auto;
        }

        .back-bar {
            margin-bottom: 15px;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 999px;
            border: 1px solid var(--dark-blue);
            color: var(--dark-blue);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.2s ease;
        }

        .back-link i {
            font-size: 0.95rem;
        }

        .back-link:hover {
            background: var(--dark-blue);
            color: #fff;
            box-shadow: 0 4px 10px rgba(7,45,68,0.35);
            transform: translateY(-1px);
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
        
        .reminder-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .reminder-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 20px;
        }
        
        .reminder-content {
            flex: 1;
        }
        
        .reminder-content h4 {
            margin-bottom: 5px;
            color: var(--primary-color);
        }
        
        .reminder-time {
            color: #666;
            font-size: 14px;
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
            background: #072D44;
            color: white;
            border-radius: 999px;
            letter-spacing: 0.5px;
        }
        
        .btn-success:hover {
            transform: scale(1.03) translateY(-1px);
            box-shadow: 0 10px 22px rgba(7,45,68,0.35);
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
        }
        
        .add-reminder-section {
            background-color: var(--card-bg);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 119, 182, 0.1);
            padding: 24px 24px 22px;
            margin-bottom: 28px;
        }
        
        .add-reminder-section h3 {
            color: var(--dark-blue);
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 16px;
        }
        
        .form-group {
            margin-bottom: 22px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-blue);
        }
        
        .form-control {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d0e4f8;
            border-radius: 10px;
            font-size: 15px;
            background: #ffffff;
            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease,
                background-color 0.2s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 119, 182, 0.25);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
            margin-bottom: 10px;
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
        
        .prescription-medicines {
            margin-bottom: 20px;
        }
        
        .medicine-chip {
            display: inline-block;
            padding: 6px 12px;
            margin: 5px;
            background-color: #caf0f8;
            color: #0077b6;
            border-radius: 999px;
            font-size: 14px;
            cursor: pointer;
            border: 1px solid rgba(0, 119, 182, 0.2);
            box-shadow: 0 2px 4px rgba(0, 119, 182, 0.12);
            transition: transform 0.15s ease, box-shadow 0.15s ease, background-color 0.15s ease;
        }
        
        .medicine-chip::before {
            content: "💊 ";
        }
        
        .medicine-chip:hover {
            background-color: #b3e5fc;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 119, 182, 0.22);
        }
        
        .info-note {
            background-color: #caf0f8;
            border: 1px solid rgba(0, 119, 182, 0.25);
            padding: 12px 15px;
            margin-bottom: 22px;
            border-radius: 12px;
            color: #0077b6;
            display: flex;
            align-items: flex-start;
            gap: 8px;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 200px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .reminder-card {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .reminder-icon {
                margin-bottom: 15px;
                margin-right: 0;
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
            <?php include 'patient_header.php'; ?>
            
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
            
            <!-- Add Reminder Section -->
            <div class="add-reminder-section">
                <h3 style="margin-bottom: 20px;">Add New Medicine Reminder</h3>
                
                <div class="info-note">
                    <i class="fas fa-info-circle"></i> You can only set reminders for medicines that have been prescribed to you.
                </div>
                
                <?php if (!empty($prescription_medicines)): ?>
                    <div class="prescription-medicines">
                        <p><strong>Medicines from your prescription:</strong></p>
                        <div>
                            <?php foreach ($prescription_medicines as $medicine): ?>
                                <span class="medicine-chip" onclick="selectMedicine(<?php echo $medicine['MEDICINE_ID']; ?>, '<?php echo htmlspecialchars($medicine['MED_NAME']); ?>')">
                                    <?php echo htmlspecialchars($medicine['MED_NAME']); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="medicine_reminder.php">
                    <?php if (!empty($prescription_id)): ?>
                        <input type="hidden" name="prescription_id" value="<?php echo htmlspecialchars($prescription_id); ?>">
                    <?php endif; ?>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="medicine_id">Medicine</label>
                            <select class="form-control" id="medicine_id" name="medicine_id" required>
                                <option value="">-- Select Medicine --</option>
                                <?php
                                // If coming from a specific prescription, only show medicines from that prescription
                                if (!empty($prescription_medicines)) {
                                    foreach ($prescription_medicines as $medicine) {
                                        $selected = ($form_data['medicine_id'] == $medicine['MEDICINE_ID']) ? 'selected' : '';
                                        echo '<option value="' . $medicine['MEDICINE_ID'] . '" ' . $selected . '>' . 
                                             htmlspecialchars($medicine['MED_NAME']) . '</option>';
                                    }
                                } else {
                                    // Otherwise, show all medicines ever prescribed to this patient
                                    if ($prescribed_medicines_query && mysqli_num_rows($prescribed_medicines_query) > 0) {
                                        while ($medicine = mysqli_fetch_assoc($prescribed_medicines_query)) {
                                            $selected = ($form_data['medicine_id'] == $medicine['MEDICINE_ID']) ? 'selected' : '';
                                            echo '<option value="' . $medicine['MEDICINE_ID'] . '" ' . $selected . '>' . 
                                                 htmlspecialchars($medicine['MED_NAME']) . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required
                                   value="<?php echo htmlspecialchars($form_data['start_date']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required
                                   value="<?php echo htmlspecialchars($form_data['end_date']); ?>">
                        </div>
                        <div class="form-group">
                            <label for="reminder_time">Reminder Time</label>
                            <input type="time" class="form-control" id="reminder_time" name="reminder_time" required
                                   value="<?php echo htmlspecialchars($form_data['reminder_time']); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="remarks">Additional Notes</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Add any additional notes or dosage instructions"><?php echo htmlspecialchars($form_data['remarks']); ?></textarea>
                    </div>
                    
                    <button type="submit" name="add_reminder" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Reminder
                    </button>
                </form>
            </div>
            
            <!-- Existing Reminders -->
            <h3 id="reminders" style="margin-bottom: 20px;">Your Medicine Reminders</h3>
            
            <?php
            if (mysqli_num_rows($reminders_query) > 0) {
                while ($reminder = mysqli_fetch_assoc($reminders_query)) {
                    ?>
                    <div class="reminder-card">
                        <div class="reminder-icon">
                            <i class="fas fa-pills"></i>
                        </div>
                        <div class="reminder-content">
                            <h4><?php echo htmlspecialchars($reminder['MED_NAME'] ?? 'Medicine'); ?></h4>
                            <?php if (!empty($reminder['REMARKS'])): ?>
                                <p><strong>Notes:</strong> <?php echo htmlspecialchars($reminder['REMARKS']); ?></p>
                            <?php endif; ?>
                            <div class="reminder-time">
                                <i class="far fa-calendar-alt"></i>
                                <?php echo $reminder['START_DATE'] ? date('d M Y', strtotime($reminder['START_DATE'])) : '—'; ?>
                                &nbsp; to &nbsp;
                                <?php echo $reminder['END_DATE'] ? date('d M Y', strtotime($reminder['END_DATE'])) : '—'; ?>
                                &nbsp; | &nbsp;
                                <i class="far fa-clock"></i>
                                <?php echo date('h:i A', strtotime($reminder['REMINDER_TIME'])); ?>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-primary" onclick="openEditModal(<?php echo $reminder['MEDICINE_REMINDER_ID']; ?>, '<?php echo addslashes($reminder['MED_NAME']); ?>', '<?php echo addslashes($reminder['START_DATE']); ?>', '<?php echo addslashes($reminder['END_DATE']); ?>', '<?php echo addslashes($reminder['REMINDER_TIME']); ?>', '<?php echo addslashes($reminder['REMARKS']); ?>')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="reminder_id" value="<?php echo $reminder['MEDICINE_REMINDER_ID']; ?>">
                                <button type="submit" name="delete_reminder" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this reminder?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <p>No medicine reminders set</p>
                </div>';
            }
            ?>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Medicine Reminder</h2>
            <form method="POST" action="medicine_reminder.php">
                <?php if (!empty($prescription_id)): ?>
                    <input type="hidden" name="prescription_id" value="<?php echo htmlspecialchars($prescription_id); ?>">
                <?php endif; ?>
                <input type="hidden" id="edit_reminder_id" name="reminder_id">
                
                <div class="form-group">
                    <label for="edit_medicine_name">Medicine</label>
                    <input type="text" class="form-control" id="edit_medicine_name" readonly>
                </div>
                
                <div class="form-group">
                    <label for="edit_start_date">Start Date</label>
                    <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                </div>
                <div class="form-group">
                    <label for="edit_end_date">End Date</label>
                    <input type="date" class="form-control" id="edit_end_date" name="end_date" required>
                </div>
                <div class="form-group">
                    <label for="edit_reminder_time">Reminder Time</label>
                    <input type="time" class="form-control" id="edit_reminder_time" name="reminder_time" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_remarks">Additional Notes</label>
                    <textarea class="form-control" id="edit_remarks" name="remarks" rows="3"></textarea>
                </div>
                
                <div class="btn-group">
                    <button type="submit" name="update_reminder" class="btn btn-success">
                        <i class="fas fa-save"></i> Update Reminder
                    </button>
                    <button type="button" class="btn btn-danger" onclick="closeEditModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function selectMedicine(medicineId, medicineName) {
            document.getElementById('medicine_id').value = medicineId;
            document.getElementById('start_date').focus();
        }
        
        function openEditModal(reminderId, medicineName, startDate, endDate, reminderTime, remarks) {
            document.getElementById('edit_reminder_id').value = reminderId;
            document.getElementById('edit_medicine_name').value = medicineName;
            document.getElementById('edit_start_date').value = startDate;
            document.getElementById('edit_end_date').value = endDate;
            document.getElementById('edit_reminder_time').value = reminderTime;
            document.getElementById('edit_remarks').value = remarks || '';
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
        
    </script>
</body>
</html>