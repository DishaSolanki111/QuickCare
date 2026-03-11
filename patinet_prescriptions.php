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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Prescriptions - QuickCare</title>
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
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 24px;
        }
        
        .prescription-card {
            background: var(--card-bg);
            border-radius: 24px;
            box-shadow:
                0 24px 60px rgba(15, 23, 42, 0.25),
                0 0 0 1px rgba(255,255,255,0.4);
            border: 1px solid rgba(255,255,255,0.35);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
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
        
        @media (max-width: 1100px) {
            .vitals-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 200px;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 16px;
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
            
            <!-- Prescriptions List -->
            <?php
            if (mysqli_num_rows($prescriptions_query) > 0) {
                while ($prescription = mysqli_fetch_assoc($prescriptions_query)) {
                    // Get medicines for this prescription
                    $medicines_query = mysqli_query($conn, "
                        SELECT pm.MEDICINE_ID, pm.DOSAGE, pm.DURATION, pm.FREQUENCY, m.MED_NAME
                        FROM prescription_medicine_tbl pm
                        JOIN medicine_tbl m ON pm.MEDICINE_ID = m.MEDICINE_ID
                        WHERE pm.PRESCRIPTION_ID = " . $prescription['PRESCRIPTION_ID']
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
                                        <span class="doc-badge"><i class="fas fa-stethoscope"></i> Doctor</span>
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
                                    ?>
                                    <div class="medicine-item">
                                        <div>
                                            <div class="medicine-name"><?php echo htmlspecialchars($medicine['MED_NAME']); ?></div>
                                            <div class="medicine-details">
                                                <em><?php echo htmlspecialchars($medicine['DOSAGE']); ?> - <?php echo htmlspecialchars($medicine['FREQUENCY']); ?></em>
                                            </div>
                                        </div>
                                        <div>
                                            <span class="duration-pill"><?php echo htmlspecialchars($medicine['DURATION']); ?></span>
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
                            <button class="btn btn-success" onclick="setReminder(<?php echo $prescription['PRESCRIPTION_ID']; ?>)">
                                <i class="fas fa-bell"></i> Set Medicine Reminder
                            </button>
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
    
    <script>
        function setReminder(prescriptionId) {
            // Submit POST form to medicine reminder page with prescription ID
            var form = document.createElement('form');
            form.method = 'POST';
            form.action = 'medicine_reminder.php';
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'prescription';
            input.value = prescriptionId;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>