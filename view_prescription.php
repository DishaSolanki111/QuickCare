<?php
session_start();

// Check if user is logged in as a receptionist
if (!isset($_SESSION['RECEPTIONIST_ID'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';
 $receptionist_id = $_SESSION['REceptionist_ID'];

// Fetch receptionist data from database
 $receptionist_query = mysqli_query($conn, "SELECT * FROM receptionist_tbl WHERE RECEPTIONIST_ID = '$receptionist_id'");
 $receptionist = mysqli_fetch_assoc($receptionist_query);

// Fetch prescriptions data
 $prescriptions_query = mysqli_query($conn, "
    SELECT p.*, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, 
           d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME,
           pat.FIRST_NAME as PAT_FNAME, pat.LAST_NAME as PAT_LNAME
    FROM prescription_tbl p
    JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    JOIN patient_tbl pat ON a.PATIENT_ID = pat.PATIENT_ID
    ORDER BY p.ISSUE_DATE DESC, a.APPOINTMENT_TIME DESC
");

// Fetch medicines for each prescription
 $prescriptions_with_medicines = [];
if (mysqli_num_rows($prescriptions_query) > 0) {
    while ($prescription = mysqli_fetch_assoc($prescriptions_query)) {
        // Get medicines for this prescription
        $medicines_query = mysqli_query($conn, "
            SELECT pm.MEDICINE_ID, pm.DOSAGE, pm.DURATION, pm.FREQUENCY, m.MED_NAME
            FROM prescription_medicine_tbl pm
            JOIN medicine_tbl m ON pm.MEDICINE_ID = m.MEDICINE_ID
            WHERE pm.PRESCRIPTION_ID = " . $prescription['PRESCRIPTION_ID']."
        ");
        
        $medicines = [];
        if (mysqli_num_rows($medicines_query) > 0) {
            while ($medicine = mysqli_fetch_assoc($medicines_query)) {
                $medicines[] = $medicine;
            }
        }
        
        $prescription['medicines'] = $medicines;
        $prescriptions_with_medicines[] = $prescription;
    }
}

// Fetch appointments for dropdown
 $appointments_query = mysqli_query($conn, "
    SELECT a.*, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME
    FROM appointment_tbl a
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE a.STATUS = 'COMPLETED'
    ORDER BY a.APPOINTMENT_DATE DESC
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

// Handle prescription download
if (isset($_GET['download']) && !empty($_GET['download'])) {
    $prescription_id = mysqli_real_escape_string($conn, $_GET['download']);
    
    // Get prescription details
    $prescription_detail = mysqli_query($conn, "
        SELECT p.*, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME, 
               pat.FIRST_NAME as PAT_FNAME, pat.LAST_NAME as PAT_LNAME, pat.DOB, pat.GENDER, pat.BLOOD_GROUP, pat.ADDRESS, pat.PHONE, pat.EMAIL,
               d.EDUCATION, d.PHONE as DOC_PHONE, d.EMAIL as DOC_EMAIL
        FROM prescription_tbl p
        JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
        JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
        JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
        JOIN patient_tbl pat ON a.PATIENT_ID = pat.PATIENT_ID
        WHERE p.PRESCRIPTION_ID = '$prescription_id'
    ");
    
    if (mysqli_num_rows($prescription_detail) > 0) {
        $prescription = mysqli_fetch_assoc($prescription_detail);
        
        // Get medicines for this prescription
        $medicines_query = mysqli_query($conn, "
            SELECT pm.MEDICINE_ID, pm.DOSAGE, pm.DURATION, pm.FREQUENCY, m.MED_NAME, m.DESCRIPTION
            FROM prescription_medicine_tbl pm
            JOIN medicine_tbl m ON pm.MEDICINE_ID = m.MEDICINE_ID
            WHERE pm.PRESCRIPTION_ID = " . $prescription['PRESCRIPTION_ID']."
        ");
        
        // Generate PDF content
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="prescription_' . $prescription_id . '.pdf');
        
        // In a real application, you would use a library like FPDF or TCPDF to generate a proper PDF
        // For this example, we'll just output a simple HTML that can be saved as PDF
        echo '<html>
        <html>
        <head>
            <title>Prescription - ' . $prescription['PRESCRIPTION_ID'] . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    padding: 20px;
                    line-height: 1.6;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 1px solid #eee;
                }
                .logo {
                    width: 100px;
                    height: 100px;
                    margin-bottom: 20px;
                }
                .title {
                    font-size: 24px;
                    color: #072D44;
                    margin-bottom: 5px;
                }
                .subtitle {
                    font-size: 16px;
                    color: #064469;
                    margin-bottom: 20px;
                }
                .section {
                    margin-bottom: 20px;
                }
                .section h3 {
                    color: #072D44;
                    margin-bottom: 10px;
                }
                .info-row {
                    display: flex;
                    margin-bottom: 10px;
                }
                .info-label {
                    width: 200px;
                    font-weight: bold;
                }
                .info-value {
                    flex: 1;
                }
                .medicine-list {
                    margin-top: 20px;
                }
                .medicine-item {
                    display: flex;
                    justify-content: space-between;
                    padding: 10px 0;
                    border-bottom: 1px solid #eee;
                }
                .medicine-name {
                    font-weight: bold;
                }
                .medicine-details {
                    color: #666;
                    font-size: 14px;
                }
                .footer {
                    margin-top: 30px;
                    text-align: center;
                    font-style: italic;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo">
                <div class="title">QuickCare Medical Center</div>
                <div class="subtitle">Medical Prescription</div>
            </div>
            
            <div class="section">
                <h3>Doctor Information</h3>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value">Dr. ' . $prescription['DOC_FNAME'] . ' ' . $prescription['DOC_LNAME'] . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Specialization:</span>
                    <span class="info-value">' . $prescription['SPECIALISATION_NAME'] . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Education:</span>
                    <span class="info-value">' . $prescription['EDUCATION'] . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Contact:</span>
                    <span class="info-value">' . $prescription['DOC_PHONE'] . ' | ' . $prescription['DOC_EMAIL'] . '</span>
                </div>
            </div>
            
            <div class="section">
                <h3>Patient Information</h3>
                <div class="info-row">
                    <span class="info-label">Name:</span>
                    <span class="info-value">' . $prescription['PAT_FNAME'] . ' ' . $prescription['PAT_LNAME'] . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date of Birth:</span>
                    <span class="info-value">' . date('F d, Y', strtotime($prescription['PAT_DOB'])) . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Gender:</span>
                    <span class="info-value">' . $prescription['GENDER'] . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Blood Group:</span>
                    <span class="info-value">' . $prescription['BLOOD_GROUP'] . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Contact:</span>
                    <span class="info-value">' . $prescription['PAT_PHONE'] . ' | ' . $prescription['PAT_EMAIL'] . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Address:</span>
                   <span class="info-value">' . nl2br>' . $prescription['ADDRESS'] . '</span>
            </div>
            
            <div class="section">
                <h3>Prescription Details</h3>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span class="info-value">' . date('F d, Y', strtotime($prescription['ISSUE_DATE']) . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Appointment Date:</span>
                    <span class="info-value">' . date('F d, Y', strtotime($prescription['APPOINTMENT_DATE']) . ' at ' . date('h:i A', strtotime($prescription['APPOINTMENT_TIME']) . '</span>
                </div>
                
                <div class="info-row">
                    <span class="info-label">Height:</span>
                    <span class="info-value">' . $prescription['HEIGHT_CM'] . ' cm</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Weight:</span>
                    <span class="info-value">' . $prescription['WEIGHT_KG'] . ' kg</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Blood Pressure:</span>
                    <span class="info-value">' . $prescription['BLOOD_PRESSURE'] . ' mmHg</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Diabetes:</span>
                    <span class="info-value">' . $prescription['DIABETES'] . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Symptoms:</span>
                    <span class="info-value">' . nl2br>' . htmlspecialchars($prescription['SYMPTOMS']) . '</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Diagnosis:</span>
                    <span class="info-value">' . nl2br>' . htmlspecialchars($prescription['DIAGNOSIS']) . '</span>
                </div>
                
                <?php if (!empty($prescription['ADDITIONAL_NOTES'])): ?>
                <div class="info-row">
                    <span class="info-label">Additional Notes:</span>
                    <span class="info-value">' . nl2br>' . htmlspecialchars($prescription['ADDITIONAL_NOTES']) . '</span>
                </div>
                <?php endif; ?>
            </div>
            
            <div class="section">
                <h3>Medications</h3>
                <div class="medicine-list">
                    <?php
                    if (isset($medicines)) {
                        foreach ($medicines as $medicine) {
                            ?>
                            <div class="medicine-item">
                                <div>
                                    <div class="medicine-name">' . htmlspecialchars($medicine['MED_NAME']) . '</div>
                                    <div class="medicine-details">' . htmlspecialchars($medicine['DOSAGE']) . ' - ' . htmlspecialchars($medicine['FREQUENCY']) . '</div>
                                </div>
                                <div class="medicine-details">' . htmlspecialchars($medicine['DURATION']) . '</div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
            
            <div class="footer">
                <p>This is a digitally generated prescription. For any queries, please contact the medical center.</p>
                <p>Generated on: ' . date('F d, Y') . '</p>
            </div>
        </body>
        </html>';
        
        exit;
    }
}

// Fetch medicines for dropdown
 $medicines_query = mysqli_query($conn, "SELECT * FROM medicine_tbl ORDER BY MED_NAME");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Prescription - QuickCare</title>
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
            padding: 15px 22px;
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
        
        .prescription-card {
            border-left: 4px solid var(--secondary-color);
            margin-bottom: 20px;
            padding: 20px;
            background-color: var(--white);
            border-radius: 0 8px 8px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .prescription-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .prescription-title {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .prescription-date {
            color: #777;
            font-size: 14px;
        }
        
        .prescription-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr);
            gap: 10px;
            margin-bottom: 15px;
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
        
        .prescription-content {
            margin-bottom: 15px;
        }
        
        .prescription-content p {
            margin-bottom: 5px;
            color: #666;
        }
        
        .medicine-list {
            margin-top: 15px;
        }
        
        .medicine-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .medicine-name {
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .medicine-details {
            color: #666;
            font-size: 14px;
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
        
        .search-bar {
            display: flex;
            margin-bottom: 20px;
        }
        
        .search-bar input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px 0 0 0 0 5px;
            font-size: 16px;
        }
        
        .search-bar button {
            padding: 10px 20px;
            background: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 0 5px 5px 0 0;
            cursor: pointer;
        }
        
        .filter-section {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .filter-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight:         color: var(--primary-color);
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
            box-shadow: 0 0 0 0 2px rgba(52, 152, 219, 0.2);
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
            
            .prescription-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            
            .prescription-details {
                grid-template-columns: 1fr;
            }
            
            .medicine-item {
                flex-direction: column;
                gap: 5px;
            }
            
            .prescription-actions {
                flex-direction: column;
                align-items: flex-start;
            }
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
        <a href="set_reminder.php">Set Reminder</a>
        <a href="manage_user_profile.php">Manage User Profile</a>
        <a href="view_prescription.php" class="active">View Prescription</a>
        <button class="filter-section">
            <div class="filter-row">
                <div class="form-group">
                    <label for="search">Search</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Search prescriptions..." value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="form-group">
                    <label for="filter_doctor">Filter by Doctor</label>
                    <select class="form-control" id="filter_doctor" name="filter_doctor">
                        <option value="">All Doctors</option>
                        <?php
                        if (mysqli_num_rows($doctors_query) > 0) {
                            while ($doctor = mysqli_fetch_assoc($doctors_query)) {
                                echo '<option value="' . $doctor['DOCTOR_ID'] . '">' . 
                                     'Dr. ' . htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME'] . 
                                     ' (' . htmlspecialchars($doctor['SPECIALISATION_NAME']) . ')</option>';
                            }
                            // Reset the result pointer
                            mysqli_data_seek($doctors_query, 0);
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="filter_date">Filter by Date</label>
                    <input type="date" class="form-control" id="filter_date" name="filter_date">
                    </div>
                </div>
                <button type="button" class="btn btn-primary" onclick="applyFilters()">
                    <i class="bi bi-funnel"></i> Apply Filters
                </button>
            </div>
        </div>
        
        <!-- Prescription Cards -->
        <div class="card">
            <div class="card-header">
                <h3>Prescriptions</h3>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#downloadModal">
                    <i class="bi bi-download"></i> Download All Prescriptions
                </button>
            </div>
            </div>
            <div class="card-body">
                <?php
                if (mysqli_num_rows($prescriptions_with_medicines) > 0) {
                    foreach ($prescriptions_with_medicines as $prescription) {
                        ?>
                        <div class="prescription-card">
                            <div class="prescription-header">
                                <div class="prescription-title">
                                    Dr. <?php echo htmlspecialchars($prescription['DOC_FNAME'] . ' ' . $prescription['DOC_LNAME']); ?>
                                </div>
                                <span class="prescription-date"><?php echo date('F d, Y', strtotime($prescription['ISSUE_DATE']); ?></span>
                            </div>
                            
                            <div class="prescription-content">
                                <p><strong>Diagnosis:</strong> <?php echo htmlspecialchars($prescription['DIAGNOSIS']; ?></p>
                                <p><strong>Symptoms:</strong> <?php echo htmlspecialchars($prescription['SYMPTOMS']); ?></p>
                            </div>
                            
                            <div class="medicine-list">
                                <h4>Medications:</h4>
                                <?php
                                foreach ($prescription['medicines'] as $medicine) {
                                    ?>
                                    <div class="medicine-item">
                                        <div>
                                            <div class="medicine-name"><?php echo htmlspecialchars($medicine['MED_NAME']); ?></div>
                                            <div class="medicine-details"><?php echo htmlspecialchars($medicine['DOSAGE']; ?> - <?php echo htmlspecialchars($medicine['FREQUENCY']); ?></div>
                                        </div>
                                        <div class="medicine-details"><?php echo htmlspecialchars($medicine['DURATION']; ?></div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                            </div>
                            
                            <div class="prescription-actions">
                                <button class="btn btn-primary" onclick="downloadPrescription(<?php echo $prescription['PRESCRIPTION_ID']; ?>)">
                                    <i class="bi bi-download"></i> Download
                                </button>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="empty-state">
                        <i class="bi bi-file-medical"></i>
                        <h4>No prescriptions found</h4>
                        <p>No prescriptions found.</p>
                    </div>';
                ?>
            </div>
        </div>
    </div>
    
    <!-- Download Modal -->
    <div class="modal fade" id="downloadModal" tabindex="-1" aria-labelledby="downloadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="downloadModalLabel">Download Prescriptions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="select>
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="download_date_from">From Date</label>
                        <input type="date" class="form-control" id="download_date_from">
                    </div>
                        <div class="form-group">
                            <label for="download_date_to">To Date</label>
                            <input type="date" class="form-control" id="download_date_to">
                        </div>
                        <div class="form-group">
                            <label for="download_doctor">Filter by Doctor</label>
                            <select class="form-control" id="download_doctor">
                                <option value="">All Doctors</option>
                                <?php
                                if (mysqli_num_rows($doctors_query) > 0) {
                                    while ($doctor = mysqli_fetch_assoc($doctors_query)) {
                                        echo '<option value="' . $doctor['DOCTOR_ID'] . '">' . 
                                             'Dr. ' . htmlspecialchars($doctor['FIRST_NAME'] . ' . $doctor['LAST_NAME']) . 
                                             ' (' . htmlspecialchars($doctor['SPECIALISATION_NAME']) . ')</option>';
                                    }
                                    // Reset the result pointer
                                    mysqli_data_seek($doctors_query, 0);
                                }
                                ?>
                            </select>
                        </div>
                        <div class="text-end">
                            <button type="button" class="btn btn-success" onclick="downloadAllPrescriptions()">
                                <i class="bi bi-download"></i> Download All
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
    
    <script src="https://applyFilters();
        function applyFilters() {
            const search = document.getElementById('search').value;
            const doctor_id = document.getElementById('filter_doctor').value;
            const date_from = document.getElementById('download_date_from').value;
            const date_to = document.getElementById('download_date_to').value;
            
            // Redirect with filters
            const url = new URL('view_prescription.php');
            url.searchParams.set('search', search);
            if (doctor_id) url.searchParams.set('doctor_id', doctor_id);
            if (date_from) url.searchParams.set('date_from', date_from);
            if (date_to) url.searchParams.set('date_to', date_to);
            
            window.location.href = url.toString();
        }
        
        function downloadAllPrescriptions() {
            const doctor_id = document.getElementById('download_doctor').value;
            const date_from = document.getElementById('download_date_from').value;
            const date_to = document.getElementById('download_date_to').value;
            
            // Create a form and submit it to download all prescriptions
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'download_prescriptions.php';
            form.innerHTML = `
                <input type="hidden" name="download_all" value="1">
                <input type="hidden" name="doctor_id" value="${doctor_id}">
                <input type="hidden" name="date_from" value="${date_from}">
                <input type="hidden" name="date_to" value="${date_to}">
            `;
            
            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>