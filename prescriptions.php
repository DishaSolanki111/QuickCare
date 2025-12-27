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
if (isset($_GET['download']) && !empty($_GET['download'])) {
    $prescription_id = mysqli_real_escape_string($conn, $_GET['download']);
    
    // Get prescription details
    $prescription_detail = mysqli_query($conn, "
        SELECT p.*, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, d.EDUCATION, d.PHONE, d.EMAIL,
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
        
        // Generate PDF content (simplified for this example)
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="prescription_' . $prescription_id . '.pdf"');
        
        // In a real application, you would use a library like FPDF or TCPDF to generate a proper PDF
        // For this example, we'll just output a simple HTML that can be saved as PDF
        echo '<html>
        <head>
            <title>Prescription - ' . $prescription['PRESCRIPTION_ID'] . '</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .doctor-info, .patient-info { margin-bottom: 20px; }
                .prescription-details { margin-bottom: 20px; }
                .medicine-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .medicine-table th, .medicine-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .medicine-table th { background-color: #f2f2f2; }
                .footer { margin-top: 30px; text-align: center; font-style: italic; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>QuickCare Medical Center</h1>
                <h2>Medical Prescription</h2>
            </div>
            
            <div class="doctor-info">
                <h3>Doctor Information</h3>
                <p><strong>Name:</strong> Dr. ' . htmlspecialchars($prescription['DOC_FNAME'] . ' ' . $prescription['DOC_LNAME']) . '</p>
                <p><strong>Specialization:</strong> ' . htmlspecialchars($prescription['SPECIALISATION_NAME']) . '</p>
                <p><strong>Education:</strong> ' . htmlspecialchars($prescription['EDUCATION']) . '</p>
                <p><strong>Contact:</strong> ' . htmlspecialchars($prescription['PHONE']) . ' | ' . htmlspecialchars($prescription['EMAIL']) . '</p>
            </div>
            
            <div class="patient-info">
                <h3>Patient Information</h3>
                <p><strong>Name:</strong> ' . htmlspecialchars($prescription['PAT_FNAME'] . ' ' . $prescription['PAT_LNAME']) . '</p>
                <p><strong>Date of Birth:</strong> ' . date('F d, Y', strtotime($prescription['DOB'])) . '</p>
                <p><strong>Gender:</strong> ' . htmlspecialchars($prescription['GENDER']) . '</p>
                <p><strong>Contact:</strong> ' . htmlspecialchars($prescription['PAT_PHONE']) . ' | ' . htmlspecialchars($prescription['PAT_EMAIL']) . '</p>
                <p><strong>Address:</strong> ' . htmlspecialchars($prescription['ADDRESS']) . '</p>
            </div>
            
            <div class="prescription-details">
                <h3>Prescription Details</h3>
                <p><strong>Date:</strong> ' . date('F d, Y', strtotime($prescription['ISSUE_DATE'])) . '</p>
                <p><strong>Appointment Date:</strong> ' . date('F d, Y', strtotime($prescription['APPOINTMENT_DATE'])) . '</p>';
                
                if (!empty($prescription['HEIGHT_CM'])) {
                    echo '<p><strong>Height:</strong> ' . $prescription['HEIGHT_CM'] . ' cm</p>';
                }
                
                if (!empty($prescription['WEIGHT_KG'])) {
                    echo '<p><strong>Weight:</strong> ' . $prescription['WEIGHT_KG'] . ' kg</p>';
                }
                
                if (!empty($prescription['BLOOD_PRESSURE'])) {
                    echo '<p><strong>Blood Pressure:</strong> ' . $prescription['BLOOD_PRESSURE'] . ' mmHg</p>';
                }
                
                if (!empty($prescription['DIABETES'])) {
                    echo '<p><strong>Diabetes:</strong> ' . $prescription['DIABETES'] . '</p>';
                }
                
                echo '<p><strong>Symptoms:</strong> ' . htmlspecialchars($prescription['SYMPTOMS']) . '</p>
                <p><strong>Diagnosis:</strong> ' . htmlspecialchars($prescription['DIAGNOSIS']) . '</p>';
                
                if (!empty($prescription['ADDITIONAL_NOTES'])) {
                    echo '<p><strong>Additional Notes:</strong> ' . htmlspecialchars($prescription['ADDITIONAL_NOTES']) . '</p>';
                }
                
                echo '</div>
            
            <div class="medicines">
                <h3>Prescribed Medicines</h3>
                <table class="medicine-table">
                    <tr>
                        <th>Medicine Name</th>
                        <th>Dosage</th>
                        <th>Frequency</th>
                        <th>Duration</th>
                    </tr>';
                    
                    while ($medicine = mysqli_fetch_assoc($medicines_query)) {
                        echo '<tr>
                            <td>' . htmlspecialchars($medicine['MED_NAME']) . '</td>
                            <td>' . htmlspecialchars($medicine['DOSAGE']) . '</td>
                            <td>' . htmlspecialchars($medicine['FREQUENCY']) . '</td>
                            <td>' . htmlspecialchars($medicine['DURATION']) . '</td>
                        </tr>';
                    }
                    
                    echo '</table>
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
        
        .prescription-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .prescription-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .prescription-header h3 {
            color: var(--primary-color);
        }
        
        .prescription-date {
            color: #777;
            font-size: 14px;
        }
        
        .prescription-details {
            margin-bottom: 15px;
        }
        
        .prescription-details p {
            margin-bottom: 5px;
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
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 15px;
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
        }
        
        @media (max-width: 768px) {
            .prescription-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .medicine-item {
                flex-direction: column;
                gap: 5px;
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
                <a href="manage_appointments.php">Manage Appointments</a>
                <a href="doctor_schedule.php">View Doctor Schedule</a>
                <a class="active">My Prescriptions</a>
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
                <div class="welcome-msg">My Prescriptions</div>
                <div class="user-actions">
                    <div class="user-dropdown">
                        <div class="user-avatar"><?php echo strtoupper(substr($patient['FIRST_NAME'], 0, 1) . substr($patient['LAST_NAME'], 0, 1)); ?></div>
                        <span><?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></span>
                        <i class="fas fa-chevron-down" style="margin-left: 8px;"></i>
                    </div>
                </div>
            </div>
            
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
                            <h3>Dr. <?php echo htmlspecialchars($prescription['DOC_FNAME'] . ' ' . $prescription['DOC_LNAME']); ?></h3>
                            <span class="prescription-date"><?php echo date('F d, Y', strtotime($prescription['ISSUE_DATE'])); ?></span>
                        </div>
                        
                        <div class="prescription-details">
                            <p><strong>Diagnosis:</strong> <?php echo htmlspecialchars($prescription['DIAGNOSIS']); ?></p>
                            <p><strong>Symptoms:</strong> <?php echo htmlspecialchars($prescription['SYMPTOMS']); ?></p>
                            
                            <?php if (!empty($prescription['HEIGHT_CM'])): ?>
                                <p><strong>Height:</strong> <?php echo $prescription['HEIGHT_CM']; ?> cm</p>
                            <?php endif; ?>
                            
                            <?php if (!empty($prescription['WEIGHT_KG'])): ?>
                                <p><strong>Weight:</strong> <?php echo $prescription['WEIGHT_KG']; ?> kg</p>
                            <?php endif; ?>
                            
                            <?php if (!empty($prescription['BLOOD_PRESSURE'])): ?>
                                <p><strong>Blood Pressure:</strong> <?php echo $prescription['BLOOD_PRESSURE']; ?> mmHg</p>
                            <?php endif; ?>
                            
                            <?php if (!empty($prescription['DIABETES'])): ?>
                                <p><strong>Diabetes:</strong> <?php echo $prescription['DIABETES']; ?></p>
                            <?php endif; ?>
                            
                            <?php if (!empty($prescription['ADDITIONAL_NOTES'])): ?>
                                <p><strong>Additional Notes:</strong> <?php echo htmlspecialchars($prescription['ADDITIONAL_NOTES']); ?></p>
                            <?php endif; ?>
                        </div>
                        
                        <h4 style="margin: 15px 0 10px;">Medications</h4>
                        <div class="medicine-list">
                            <?php
                            if (mysqli_num_rows($medicines_query) > 0) {
                                while ($medicine = mysqli_fetch_assoc($medicines_query)) {
                                    ?>
                                    <div class="medicine-item">
                                        <div>
                                            <div class="medicine-name"><?php echo htmlspecialchars($medicine['MED_NAME']); ?></div>
                                            <div class="medicine-details"><?php echo htmlspecialchars($medicine['DOSAGE']); ?> - <?php echo htmlspecialchars($medicine['FREQUENCY']); ?></div>
                                        </div>
                                        <div class="medicine-details"><?php echo htmlspecialchars($medicine['DURATION']); ?></div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        
                        <div class="btn-group">
                            <a href="prescriptions.php?download=<?php echo $prescription['PRESCRIPTION_ID']; ?>" class="btn btn-primary">
                                <i class="fas fa-download"></i> Download PDF
                            </a>
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
            // Redirect to medicine reminder page with prescription ID
            window.location.href = 'medicine_reminder.php?prescription=' + prescriptionId;
        }
    </script>
</body>
</html>