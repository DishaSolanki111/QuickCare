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

// Handle prescription reminder creation (per medicine, with date range and multiple times)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_prescription_reminder'])) {
    $prescription_id = mysqli_real_escape_string($conn, $_POST['prescription_id']);
    $medicine_id     = isset($_POST['medicine_id']) ? (int) $_POST['medicine_id'] : 0;
    $start_date      = isset($_POST['start_date']) ? mysqli_real_escape_string($conn, $_POST['start_date']) : '';
    $end_date        = isset($_POST['end_date']) ? mysqli_real_escape_string($conn, $_POST['end_date']) : '';
    $times_raw       = isset($_POST['reminder_time']) ? $_POST['reminder_time'] : [];
    $time_remarks    = isset($_POST['reminder_remarks']) ? $_POST['reminder_remarks'] : [];
    $global_remarks  = mysqli_real_escape_string($conn, $_POST['remarks'] ?? '');

    if (!is_array($times_raw)) {
        $times_raw = [$times_raw];
    }
    if (!is_array($time_remarks)) {
        $time_remarks = [$time_remarks];
    }

    if ($start_date && $end_date) {
        // Base remarks with IDs and date range (shared for all times)
        $base_remarks = "PRESCRIPTION_ID:{$prescription_id}. MEDICINE_ID:{$medicine_id}. ";
        $base_remarks .= "Range: {$start_date} to {$end_date}. ";
        if ($global_remarks) {
            $base_remarks .= $global_remarks . ' ';
        }

        $insert_ok = true;
        foreach ($times_raw as $idx => $time_val) {
            $time_val = trim($time_val);
            if ($time_val === '') continue;

            $time_esc = mysqli_real_escape_string($conn, $time_val);
            // Per-time remark (default if none provided)
            $per_time_raw = '';
            if (isset($time_remarks[$idx])) {
                $per_time_raw = trim($time_remarks[$idx]);
            }
            $per_time_final = $per_time_raw !== '' ? $per_time_raw : 'Time to take your medicine';
            $per_time_esc   = mysqli_real_escape_string($conn, $per_time_final);

            $full_remarks = $base_remarks . " Time: {$time_val}. Message: {$per_time_esc}.";

            // Insert one row per (medicine, time) with the same date range
            $create_query = "INSERT INTO medicine_reminder_tbl (MEDICINE_ID, CREATOR_ROLE, CREATOR_ID, PATIENT_ID, START_DATE, END_DATE, REMINDER_TIME, REMARKS) 
                             VALUES ($medicine_id, 'PATIENT', '$patient_id', '$patient_id', '$start_date', '$end_date', '$time_esc', '$full_remarks')";

            if (!mysqli_query($conn, $create_query)) {
                $insert_ok = false;
                $error_message = "Error creating medicine reminder: " . mysqli_error($conn);
                break;
            }
        }

        if ($insert_ok) {
            $success_message = "Medicine reminders created successfully!";
        }
    } else {
        $error_message = "Missing date range for reminder.";
    }
}

// Fetch prescriptions data
$prescriptions_query = mysqli_query($conn, "
    SELECT p.*, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, a.APPOINTMENT_DATE
    FROM prescription_tbl p
    JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    WHERE a.PATIENT_ID = '$patient_id'
    ORDER BY p.ISSUE_DATE DESC
");

// Handle prescription download
if (isset($_POST['download']) && !empty($_POST['download'])) {
    $prescription_id = mysqli_real_escape_string($conn, $_POST['download']);
    
    // Get prescription details
    $prescription_detail = mysqli_query($conn, "
        SELECT p.*, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, d.EDUCATION, d.PHONE as DOC_PHONE, d.EMAIL as DOC_EMAIL,
               s.SPECIALISATION_NAME, a.APPOINTMENT_DATE, pa.FIRST_NAME as PAT_FNAME, pa.LAST_NAME as PAT_LNAME,
               pa.DOB, pa.GENDER, pa.ADDRESS, pa.PHONE as PAT_PHONE, pa.EMAIL as PAT_EMAIL
        FROM prescription_tbl p
        JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
        JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
        JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
        JOIN patient_tbl pa ON a.PATIENT_ID = pa.PATIENT_ID
        WHERE p.PRESCRIPTION_ID = '$prescription_id' AND a.PATIENT_ID = '$patient_id'
    ");
    
    if (mysqli_num_rows($prescription_detail) > 0) {
        $prescription = mysqli_fetch_assoc($prescription_detail);
        
        // Get medicines for this prescription
        $medicines_query = mysqli_query($conn, "
            SELECT pm.MEDICINE_ID, pm.DOSAGE, pm.DURATION, pm.FREQUENCY, m.MED_NAME, m.DESCRIPTION
            FROM prescription_medicine_tbl pm
            JOIN medicine_tbl m ON pm.MEDICINE_ID = m.MEDICINE_ID
            WHERE pm.PRESCRIPTION_ID = " . $prescription['PRESCRIPTION_ID']
        );
        
        // Fetch all medicines into an array
        $medicines = array();
        while ($medicine = mysqli_fetch_assoc($medicines_query)) {
            $medicines[] = $medicine;
        }
        
        // Include PDF generator
        require_once 'generate_prescription_pdf.php';
        
        // Generate and download PDF
        generatePrescriptionPDF($prescription, $medicines, $conn);
    } else {
        header("Location: patinet_prescriptions.php?error=invalid_prescription");
        exit;
    }
}

// Build map of existing reminders: [prescription_id][medicine_id] => reminder row
$reminder_by_prescription_medicine = [];
$reminder_check_query = mysqli_query($conn, "
    SELECT mr.*, mr.MEDICINE_REMINDER_ID
    FROM medicine_reminder_tbl mr
    WHERE mr.PATIENT_ID = '$patient_id' AND mr.CREATOR_ROLE = 'PATIENT'
");
while ($mr = mysqli_fetch_assoc($reminder_check_query)) {
    if (!empty($mr['REMARKS']) && preg_match('/PRESCRIPTION_ID:\s*(\d+)\. MEDICINE_ID:\s*(\d+)/', $mr['REMARKS'], $m)) {
        $pres_id = (int)$m[1];
        $med_id = (int)$m[2];
        if (!isset($reminder_by_prescription_medicine[$pres_id])) {
            $reminder_by_prescription_medicine[$pres_id] = [];
        }
        $reminder_by_prescription_medicine[$pres_id][$med_id] = $mr;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Prescriptions - QuickCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
            --card-bg: rgba(255, 255, 255, 0.8);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
        }
        
        html, body {
            height: 100%;
        }

        body {
            color: #1f2933;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f7fafc;
        }

        .container {
            display: flex;
           max-width: 100% !important;
           min-height:100vh;
           margin:auto;
        }
.main-content {
    flex: 1;
    margin-left: 240px;
    padding: 30px;
    width: calc(100% - 250px);
    background: #f7fafc;
}
       
        
        .prescription-card {
            background: var(--card-bg);
            padding: 22px 22px 18px;
            margin-bottom: 24px;
        }
        
        .prescription-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(148, 163, 184, 0.25);
        }

        .doctor-block {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .doctor-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, #072D44 0%, #0b3a60 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.2rem;
        }

        .doctor-meta h3 {
            margin: 0;
            font-size: 1.15rem;
            font-weight: 700;
            color: #1f2937;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .doctor-meta h3 .doc-badge {
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 999px;
            background: rgba(37,99,235,0.12);
            color: #1d4ed8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .doctor-meta .subline {
            margin-top: 2px;
            font-size: 0.8rem;
            color: #6b7280;
        }
        
        .prescription-date {
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(148,163,184,0.15);
            color: #4b5563;
            font-size: 0.8rem;
            font-weight: 500;
            white-space: nowrap;
        }
        
        .prescription-body {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .vitals-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 10px;
        }

        .vital-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255,255,255,0.9);
            border: 1px solid rgba(226,232,240,0.9);
        }

        .vital-icon {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(37, 99, 235, 0.08);
            color: #1d4ed8;
            font-size: 0.9rem;
        }

        .vital-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #4b5563;
        }

        .vital-value {
            font-size: 0.9rem;
            color: #111827;
        }

        .detail-block {
            margin-top: 2px;
        }

        .detail-row {
            margin-bottom: 6px;
            font-size: 1rem;
        }

        .detail-row strong {
            color: #1f2937;
            font-size: 1.02rem;
        }

        .notes-text {
            margin-top: 4px;
            font-size: 1rem;
            color: #111827;
            line-height: 1.8;
        }
        
        .section-title {
            margin: 18px 0 10px;
            font-size: 0.98rem;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title i {
            color: #1d4ed8;
        }
        
        .medicine-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .medicine-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255,255,255,0.95);
            border: 1px solid rgba(226,232,240,0.9);
        }
        
        .medicine-name {
            font-weight: 700;
            color: #111827;
            font-size: 0.95rem;
        }
        
        .medicine-details {
            color: #4b5563;
            font-size: 0.85rem;
        }

        .medicine-details em {
            font-style: italic;
        }

        .duration-pill {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(16,185,129,0.12);
            color: #047857;
            white-space: nowrap;
        }

        .reminder-action-row {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
        }

        .reminder-set-badge {
            font-size: 0.85rem;
            color: var(--accent-color);
            font-weight: 500;
        }

        .btn-set-reminder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 6px 12px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, #0d7a9c 0%, #064469 100%);
            border: none;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(6, 68, 105, 0.3);
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .btn-set-reminder i {
            font-size: 0.9rem;
        }
        .btn-set-reminder:hover {
            background: linear-gradient(135deg, #0e8fb5 0%, #072D44 100%);
            box-shadow: 0 5px 15px rgba(6, 68, 105, 0.4);
            transform: translateY(-1px);
        }
        .btn-set-reminder:active {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(6, 68, 105, 0.25);
        }
        
        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            letter-spacing: 0.4px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #072D44 0%, #0b3a60 100%);
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(7,45,68,0.45);
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(37,99,235,0.4);
        }

        .btn-primary:active {
            transform: scale(0.98);
            box-shadow: 0 6px 15px rgba(37,99,235,0.35);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #059669 0%, #22C55E 100%);
            color: #ffffff;
            box-shadow: 0 8px 18px rgba(16,185,129,0.35);
        }
        
        .btn-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(16,185,129,0.4);
        }

        .btn-success:active {
            transform: scale(0.98);
            box-shadow: 0 6px 15px rgba(16,185,129,0.35);
        }
        
        .btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 16px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #cbd5f5;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: none;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.1);
            color: #27ae60;
            border-left: 4px solid #27ae60;
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            color: #c0392b;
            border-left: 4px solid #c0392b;
        }

        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }

        .modal-dialog.modal-reminder-lg {
            max-width: 720px;
            width: 90%;
        }
        
        .modal-header {
            border-radius: 15px 15px 0 0;
            border-bottom: none;
        }
        
        .modal-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 14px;
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
        
        .form-control[readonly] {
            background-color: #f8f9fa;
            cursor: not-allowed;
            opacity: 0.8;
        }
        
        @media (max-width: 1100px) {
            .vitals-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        
      
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 200px;
            }

            .prescription-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .medicine-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .vitals-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
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
                    <i class="bi bi-check-circle-fill me-2"></i><?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle-fill me-2"></i><?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Prescriptions List -->
            <?php
            if (mysqli_num_rows($prescriptions_query) > 0) {
                while ($prescription = mysqli_fetch_assoc($prescriptions_query)) {
                    $prescription_id = (int)$prescription['PRESCRIPTION_ID'];
                    // Get medicines for this prescription
                    $medicines_query = mysqli_query($conn, "
                        SELECT pm.MEDICINE_ID, pm.DOSAGE, pm.DURATION, pm.FREQUENCY, m.MED_NAME
                        FROM prescription_medicine_tbl pm
                        JOIN medicine_tbl m ON pm.MEDICINE_ID = m.MEDICINE_ID
                        WHERE pm.PRESCRIPTION_ID = " . $prescription_id
                    );
                    ?>
                    <div class="prescription-card">
                        <div class="prescription-header">
                            <div class="doctor-block">
                                <div class="doctor-avatar">
                                    <i class="fas fa-user-md"></i>
                                </div>
                                <div class="doctor-meta">
                                    <h3>
                                        Dr. <?php echo htmlspecialchars($prescription['DOC_FNAME'] . ' ' . $prescription['DOC_LNAME']); ?>
                                        
                                    </h3>
                                    <div class="subline">
                                        Patient: <?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?>
                                    </div>
                                </div>
                            </div>
                            <span class="prescription-date">
                                <?php echo date('F d, Y', strtotime($prescription['ISSUE_DATE'])); ?>
                            </span>
                        </div>
                        
                        <div class="prescription-body">
                            <div class="vitals-grid">
                                <?php if (!empty($prescription['HEIGHT_CM'])): ?>
                                <div class="vital-item">
                                    <div class="vital-icon">
                                        <i class="fas fa-ruler-vertical"></i>
                                    </div>
                                    <div>
                                        <div class="vital-label">Height</div>
                                        <div class="vital-value"><?php echo $prescription['HEIGHT_CM']; ?> cm</div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($prescription['WEIGHT_KG'])): ?>
                                <div class="vital-item">
                                    <div class="vital-icon">
                                        <i class="fas fa-weight"></i>
                                    </div>
                                    <div>
                                        <div class="vital-label">Weight</div>
                                        <div class="vital-value"><?php echo $prescription['WEIGHT_KG']; ?> kg</div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($prescription['BLOOD_PRESSURE'])): ?>
                                <div class="vital-item">
                                    <div class="vital-icon">
                                        <i class="fas fa-heartbeat"></i>
                                    </div>
                                    <div>
                                        <div class="vital-label">Blood Pressure</div>
                                        <div class="vital-value"><?php echo $prescription['BLOOD_PRESSURE']; ?> mmHg</div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($prescription['DIABETES'])): ?>
                                <div class="vital-item">
                                    <div class="vital-icon">
                                        <i class="fas fa-droplet"></i>
                                    </div>
                                    <div>
                                        <div class="vital-label">Diabetes</div>
                                        <div class="vital-value"><?php echo htmlspecialchars($prescription['DIABETES']); ?></div>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="detail-block">
                                <div class="detail-row">
                                    <strong>Diagnosis:</strong>
                                    <?php echo htmlspecialchars($prescription['DIAGNOSIS']); ?>
                                </div>
                                <div class="detail-row">
                                    <strong>Symptoms:</strong>
                                    <?php echo htmlspecialchars($prescription['SYMPTOMS']); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="section-title">
                            <i class="fas fa-pills"></i>
                            <span>Medications</span>
                        </div>
                        <div class="medicine-list">
                            <?php
                            if (mysqli_num_rows($medicines_query) > 0) {
                                while ($medicine = mysqli_fetch_assoc($medicines_query)) {
                                    $med_id = (int)$medicine['MEDICINE_ID'];
                                    $med_duration_days = 0;
                                    if (!empty($medicine['DURATION']) && preg_match('/(\d+)/', $medicine['DURATION'], $matches)) {
                                        $med_duration_days = (int)$matches[1];
                                    }
                                    $reminder_row = isset($reminder_by_prescription_medicine[$prescription_id][$med_id])
                                        ? $reminder_by_prescription_medicine[$prescription_id][$med_id]
                                        : null;
                                    ?>
                                    <div class="medicine-item">
                                        <div>
                                            <div class="medicine-name"><?php echo htmlspecialchars($medicine['MED_NAME']); ?></div>
                                            <div class="medicine-details">
                                                <em><?php echo htmlspecialchars($medicine['DOSAGE']); ?> - <?php echo htmlspecialchars($medicine['FREQUENCY']); ?></em>
                                            </div>
                                        </div>
                                        <div class="reminder-action-row">
                                            <span class="duration-pill"><?php echo htmlspecialchars($medicine['DURATION']); ?></span>
                                            <?php if ($reminder_row): ?>
                                                <span class="reminder-set-badge"><i class="bi bi-bell-fill me-1"></i>Reminder set</span>
                                            <?php else: ?>
                                                <button class="btn-set-reminder" data-bs-toggle="modal" data-bs-target="#createPrescriptionReminderModal" 
                                                        data-patient-name="<?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?>"
                                                        data-prescription-id="<?php echo $prescription_id; ?>"
                                                        data-medicine-id="<?php echo $med_id; ?>"
                                                        data-medicine-name="<?php echo htmlspecialchars($medicine['MED_NAME']); ?>"
                                                        data-appointment-date="<?php echo htmlspecialchars($prescription['APPOINTMENT_DATE']); ?>"
                                                        data-medicine-duration="<?php echo $med_duration_days; ?>">
                                                    <i class="bi bi-bell-fill"></i><span>Set Reminder</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        
                        <div class="btn-group">
                            <form method="POST" action="patinet_prescriptions.php" style="display:inline">
                                <input type="hidden" name="download" value="<?php echo $prescription['PRESCRIPTION_ID']; ?>">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-download"></i> Download PDF</button>
                            </form>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="empty-state">
                    <i class="fas fa-file-medical"></i>
                    <p>No prescriptions found</p>
                </div>';
            }
            ?>
        </div>
    </div>
    
    <!-- Create Prescription Reminder Modal -->
    <div class="modal fade" id="createPrescriptionReminderModal" tabindex="-1" aria-labelledby="createPrescriptionReminderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-reminder-lg">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, var(--secondary-color), var(--soft-blue)); color: white;">
                    <h5 class="modal-title" id="createPrescriptionReminderModalLabel">
                        <i class="bi bi-bell-fill me-2"></i>Create Medicine Reminder
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" id="createReminderForm">
                        <input type="hidden" name="create_prescription_reminder" value="1">
                        <input type="hidden" id="prescription_id" name="prescription_id">
                        <input type="hidden" id="medicine_id" name="medicine_id">
                        
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <div>
                                <strong>Patient:</strong> <span id="patient_info"></span><br>
                                <small id="appointment_info" class="text-muted"></small><br>
                                <small id="medicine_info" class="text-muted"></small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="start_date">
                                <i class="bi bi-calendar-plus me-2"></i>Start Date
                            </label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>

                        <div class="form-group">
                            <label for="end_date">
                                <i class="bi bi-calendar-minus me-2"></i>End Date
                            </label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>

                        <div class="form-group">
                            <label for="duration">
                                <i class="bi bi-calendar-range me-2"></i>Duration After Appointment (Days)
                            </label>
                            <input type="number" class="form-control" id="duration" name="duration" min="0" required readonly>
                            <small class="form-text text-muted">Duration fetched from selected medicine</small>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <i class="bi bi-clock me-2"></i>Reminder Times
                            </label>
                            <div id="reminder-times-container">
                                <div class="d-flex align-items-center mb-2 reminder-time-row">
                                    <input type="time" class="form-control" name="reminder_time[]" required>
                                    <input type="text" class="form-control ms-2" name="reminder_remarks[]" placeholder="Time to take your medicine">
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="add-reminder-time-btn">
                                <i class="bi bi-plus-circle me-1"></i>Add another time
                            </button>
                            <small class="form-text text-muted d-block mt-1">You can set multiple reminder times per day.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="remarks">
                                <i class="bi bi-chat-left-text me-2"></i>Remarks
                            </label>
                            <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Add any additional notes for the reminder"></textarea>
                        </div>
                        
                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle me-2"></i> Create Reminder
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-2"></i> Cancel
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
                    const patientName = button.getAttribute('data-patient-name');
                    const prescriptionId = button.getAttribute('data-prescription-id');
                    const medicineId = button.getAttribute('data-medicine-id');
                    const medicineName = button.getAttribute('data-medicine-name');
                    
                    // Update modal content
                    document.getElementById('patient_info').textContent = patientName;
                    
                    // Get appointment date from button data attribute
                    const appointmentDateStr = button.getAttribute('data-appointment-date');
                    if (appointmentDateStr) {
                        const appointmentDate = new Date(appointmentDateStr);
                        const formattedDate = appointmentDate.toLocaleDateString('en-US', { 
                            weekday: 'long', 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        });
                        document.getElementById('appointment_info').textContent = `Appointment Date: ${formattedDate}`;

                        // Default start/end dates based on appointment + duration
                        const medicineDuration = parseInt(button.getAttribute('data-medicine-duration') || '0', 10);
                        const startDateInput = document.getElementById('start_date');
                        const endDateInput = document.getElementById('end_date');
                        if (startDateInput && endDateInput) {
                            const startDate = new Date(appointmentDate);
                            const endDate = new Date(appointmentDate);
                            if (!isNaN(medicineDuration) && medicineDuration > 0) {
                                endDate.setDate(endDate.getDate() + medicineDuration);
                            }
                            const toYMD = d => d.toISOString().slice(0, 10);
                            startDateInput.value = toYMD(startDate);
                            endDateInput.value = toYMD(endDate);
                        }

                        // Keep duration field in sync (read-only info)
                        const durationInput = document.getElementById('duration');
                        if (durationInput) {
                            durationInput.value = isNaN(medicineDuration) ? '0' : medicineDuration.toString();
                        }
                    }
                    
                    document.getElementById('prescription_id').value = prescriptionId;
                    document.getElementById('medicine_id').value = medicineId;
                    
                    // Show medicine info
                    const medicineInfoEl = document.getElementById('medicine_info');
                    if (medicineInfoEl && medicineName) {
                        medicineInfoEl.textContent = `Medicine: ${medicineName}`;
                    }
                    
                    // Reset reminder times (one default at 09:00 with default remark)
                    const timesContainer = document.getElementById('reminder-times-container');
                    if (timesContainer) {
                        timesContainer.innerHTML = '';
                        const row = document.createElement('div');
                        row.className = 'd-flex align-items-center mb-2 reminder-time-row';
                        row.innerHTML = '<input type="time" class="form-control" name="reminder_time[]" value="09:00" required>' +
                            '<input type="text" class="form-control ms-2" name="reminder_remarks[]" placeholder="Time to take your medicine">';
                        timesContainer.appendChild(row);
                    }
                    
                    // Reset remarks
                    document.getElementById('remarks').value = '';
                });
            }
            
            // Add more reminder time inputs
            const addTimeBtn = document.getElementById('add-reminder-time-btn');
            if (addTimeBtn) {
                addTimeBtn.addEventListener('click', function() {
                    const container = document.getElementById('reminder-times-container');
                    if (!container) return;
                    const row = document.createElement('div');
                    row.className = 'd-flex align-items-center mb-2 reminder-time-row';
                    row.innerHTML = '<input type="time" class="form-control" name="reminder_time[]" required>' +
                        '<input type="text" class="form-control ms-2" name="reminder_remarks[]" placeholder="Time to take your medicine">' +
                        '<button type="button" class="btn btn-link text-danger ms-2 remove-reminder-time-btn"><i class="bi bi-x-circle"></i></button>';
                    container.appendChild(row);
                });

                document.addEventListener('click', function(e) {
                    if (e.target.closest('.remove-reminder-time-btn')) {
                        const row = e.target.closest('.reminder-time-row');
                        const container = document.getElementById('reminder-times-container');
                        if (row && container && container.children.length > 1) {
                            row.remove();
                        }
                    }
                });
            }
        });
    </script>
</body>
</html>