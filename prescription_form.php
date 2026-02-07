<?php
session_start();
// Include your existing config file
require_once 'config.php';

// Check if the connection variable $conn exists and is valid
if (!$conn) {
    die("Error: Database connection failed. Please check your config.php file.");
}

// --- GET PATIENT ID FROM URL ---
$patient_id_val = isset($_POST['patient_id']) ? $_POST['patient_id'] : (isset($_GET['patient_id']) ? $_GET['patient_id'] : null);
if (!$patient_id_val || !is_numeric($patient_id_val)) {
    die("<div style='font-family: Arial, sans-serif; color: #d9534f; padding: 20px; border: 1px solid #d9534f; background-color: #f2dede; border-radius: 5px;'><strong>Error:</strong> Invalid or missing Patient ID. Please go back and select a patient.</div>");
}
 $patient_id = intval($patient_id_val);

// --- FETCH PATIENT DETAILS ---
 $stmt = $conn->prepare("SELECT FIRST_NAME, LAST_NAME FROM patient_tbl WHERE PATIENT_ID = ?");
if ($stmt === false) {
    die("Error preparing patient query: " . $conn->error);
}
 $stmt->bind_param("i", $patient_id);
 $stmt->execute();
 $result = $stmt->get_result();
 $patient = $result->fetch_assoc();
 $stmt->close();
if (!$patient) {
    die("<div style='font-family: Arial, sans-serif; color: #d9534f; padding: 20px; border: 1px solid #d9534f; background-color: #f2dede; border-radius: 5px;'><strong>Error:</strong> Patient not found.</div>");
}

 $message = '';

// --- HANDLE FORM SUBMISSION (ADD NEW PRESCRIPTION) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_prescription'])) {
    try {
        $conn->begin_transaction();
        
        // 1. Insert into prescription_tbl
        $sql = "INSERT INTO prescription_tbl (APPOINTMENT_ID, ISSUE_DATE, HEIGHT_CM, WEIGHT_KG, BLOOD_PRESSURE, DIABETES, SYMPTOMS, DIAGNOSIS, ADDITIONAL_NOTES) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        // --- NEW: Check if prepare failed ---
        if ($stmt === false) {
            throw new Exception("Error preparing prescription insert: " . $conn->error);
        }

        $height = !empty($_POST['height_cm']) ? $_POST['height_cm'] : null;
        $weight = !empty($_POST['weight_kg']) ? $_POST['weight_kg'] : null;
        $bp = !empty($_POST['blood_pressure']) ? $_POST['blood_pressure'] : null;
        
        // --- CHANGED: 'd' to 's' for weight for better compatibility ---
        $stmt->bind_param("isissssss", $_POST['appointment_id'], $_POST['issue_date'], $height, $weight, $bp, $_POST['diabetes'], $_POST['symptoms'], $_POST['diagnosis'], $_POST['additional_notes']);
        
        if (!$stmt->execute()) {
            throw new Exception("Error executing prescription insert: " . $stmt->error);
        }
        
        $prescription_id = $conn->insert_id;
        $stmt->close();

        // 2. Insert into prescription_medicine_tbl
        $med_sql = "INSERT INTO prescription_medicine_tbl (PRESCRIPTION_ID, MEDICINE_ID, DOSAGE, DURATION, FREQUENCY) VALUES (?, ?, ?, ?, ?)";
        $med_stmt = $conn->prepare($med_sql);

        // --- NEW: Check if prepare failed ---
        if ($med_stmt === false) {
            throw new Exception("Error preparing medicine insert: " . $conn->error);
        }

        $med_stmt->bind_param("iisss", $prescription_id, $_POST['medicine_id'], $_POST['dosage'], $_POST['duration'], $_POST['frequency']);

        if (!$med_stmt->execute()) {
            throw new Exception("Error executing medicine insert: " . $med_stmt->error);
        }
        
        $med_stmt->close();

        $conn->commit();
        
        // Redirect with a success message
        header("Location: manage_prescriptions.php?status=success");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        // --- NEW: Show the specific error message on the screen ---
        $message = "<p class='message error'><strong>Error:</strong> " . $e->getMessage() . "</p>";
    }
}

// --- HANDLE DELETION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_prescription_id'])) {
    $delete_id = intval($_POST['delete_prescription_id']);
    try {
        $stmt = $conn->prepare("DELETE FROM prescription_medicine_tbl WHERE PRESCRIPTION_ID = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("DELETE FROM prescription_tbl WHERE PRESCRIPTION_ID = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
        
        header("Location: manage_prescriptions.php?status=deleted_success");
        exit();

    } catch (Exception $e) {
        $message = "<p class='message error'><strong>Error deleting prescription:</strong> " . $e->getMessage() . "</p>";
    }
}

// --- FETCH EXISTING PRESCRIPTIONS ---
 $sql = "SELECT p.PRESCRIPTION_ID, p.ISSUE_DATE, p.DIAGNOSIS, p.SYMPTOMS, a.APPOINTMENT_DATE, d.FIRST_NAME AS DOC_FNAME, d.LAST_NAME AS DOC_LNAME FROM prescription_tbl p JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID WHERE a.PATIENT_ID = ? ORDER BY a.APPOINTMENT_DATE DESC";
 $stmt = $conn->prepare($sql);
 $stmt->bind_param("i", $patient_id);
 $stmt->execute();
 $prescriptions_result = $stmt->get_result();
 $prescriptions = $prescriptions_result->fetch_all(MYSQLI_ASSOC);
 $stmt->close();

// --- FETCH ALL MEDICINES ---
 $medicines_result = $conn->query("SELECT MEDICINE_ID, MED_NAME FROM medicine_tbl ORDER BY MED_NAME");
 $medicines = $medicines_result->fetch_all(MYSQLI_ASSOC);

// --- FETCH PATIENT'S COMPLETED APPOINTMENTS ---
 $appointment_sql = "SELECT a.APPOINTMENT_ID, a.APPOINTMENT_DATE, d.FIRST_NAME, d.LAST_NAME, s.SPECIALISATION_NAME FROM appointment_tbl a JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID WHERE a.PATIENT_ID = ? AND a.STATUS = 'COMPLETED' ORDER BY a.APPOINTMENT_DATE DESC";
 $appointment_stmt = $conn->prepare($appointment_sql);
 $appointment_stmt->bind_param("i", $patient_id);
 $appointment_stmt->execute();
 $appointment_result = $appointment_stmt->get_result();
 $completed_appointments = $appointment_result->fetch_all(MYSQLI_ASSOC);
 $appointment_stmt->close();

 $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prescriptions for <?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            border-bottom: none !important; /* Added to remove white line */
            padding-bottom: 0 !important;
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
        
        /* Content Card Styles */
        .content-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        h1, h2 {
            color: #0056b3;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #0056b3;
            text-decoration: none;
            font-weight: bold;
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid;
            border-radius: 4px;
        }
        
        .message.success {
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }
        
        .message.error {
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
        
        .form-container {
            margin-top: 30px;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn-submit {
            background-color: #007bff;
            width: 100%;
            font-size: 16px;
            margin-top: 10px;
        }
        
        .btn-submit:hover {
            background-color: #0056b3;
        }
        
        .btn-delete {
            background-color: #dc3545;
            float: right;
        }
        
        .btn-delete:hover {
            background-color: #c82333;
        }
        
        .prescription-list {
            margin-top: 40px;
        }
        
        .prescription-item {
            border: 1px solid #eee;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            background-color: #fafafa;
            overflow: hidden;
        }
        
        .prescription-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* Footer Styles */
        footer {
            background: var(--dark);
            color: white;
            padding: 3rem 5%;
            text-align: center;
        }

        .footer-content {
            color:black;
            max-width: 1200px;
            margin: 0 auto;
        }

        .social-links {
            color:black;
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-link {
            color:black;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-link:hover {
            color:black;
            background: var(--primary);
            transform: translateY(-3px);
        }
        
        .logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar h2 span, .sidebar a span {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <?php include 'doctor_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
             <?php include 'doctor_header.php'; ?>
            <!-- Content Card -->
            <div class="content-card">
                <a href="manage_prescriptions.php" class="back-link">&larr; Back to Patient List</a>
                
                <?php echo $message; ?>

                <div class="form-container">
                    <h2>Add New Prescription</h2>
                    <?php if (empty($completed_appointments)): ?>
                        <p style="color: #8a6d3b; background-color: #fcf8e3; border: 1px solid #faebcc; padding: 15px; border-radius: 4px;"><strong>Note:</strong> This patient has no completed appointments.</p>
                    <?php else: ?>
                        <form action="prescription_form.php" method="POST">
                            <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                            <div class="form-group">
                                <label for="appointment_id">Link to Completed Appointment:</label>
                                <select id="appointment_id" name="appointment_id" required>
                                    <option value="">--Select an Appointment--</option>
                                    <?php foreach ($completed_appointments as $apt): ?>
                                        <option value="<?php echo $apt['APPOINTMENT_ID']; ?>"><?php echo htmlspecialchars($apt['APPOINTMENT_DATE'] . " with Dr. " . $apt['FIRST_NAME'] . " " . $apt['LAST_NAME'] . " (" . $apt['SPECIALISATION_NAME'] . ")"); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="issue_date">Issue Date:</label>
                                    <input type="date" id="issue_date" name="issue_date" required>
                                </div>
                                <div class="form-group">
                                    <label for="blood_pressure">Blood Pressure:</label>
                                    <input type="text" id="blood_pressure" name="blood_pressure" placeholder="e.g., 120/80">
                                </div>
                                <div class="form-group">
                                    <label for="height_cm">Height (cm):</label>
                                    <input type="number" id="height_cm" name="height_cm">
                                </div>
                                <div class="form-group">
                                    <label for="weight_kg">Weight (kg):</label>
                                    <input type="number" step="0.1" id="weight_kg" name="weight_kg">
                                </div>
                                <div class="form-group">
                                    <label for="diabetes">Diabetes:</label>
                                    <select id="diabetes" name="diabetes">
                                        <option value="NO">No</option>
                                        <option value="TYPE-1">Type-1</option>
                                        <option value="TYPE-2">Type-2</option>
                                        <option value="PRE-DIABTIC">Pre-Diabetic</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="symptoms">Symptoms:</label>
                                <textarea id="symptoms" name="symptoms" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="diagnosis">Diagnosis:</label>
                                <textarea id="diagnosis" name="diagnosis" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="additional_notes">Additional Notes:</label>
                                <textarea id="additional_notes" name="additional_notes" rows="3"></textarea>
                            </div>
                            <h3 style="color: #0056b3; border-bottom: 1px solid #eee; padding-bottom: 10px;">Medicine Details</h3>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="medicine_id">Medicine:</label>
                                    <select id="medicine_id" name="medicine_id" required>
                                        <option value="">--Select Medicine--</option>
                                        <?php foreach ($medicines as $medicine): ?>
                                            <option value="<?php echo $medicine['MEDICINE_ID']; ?>"><?php echo htmlspecialchars($medicine['MED_NAME']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="dosage">Dosage:</label>
                                    <input type="text" id="dosage" name="dosage" placeholder="e.g., 500mg" required>
                                </div>
                                <div class="form-group">
                                    <label for="duration">Duration:</label>
                                    <input type="text" id="duration" name="duration" placeholder="e.g., 7 days" required>
                                </div>
                                <div class="form-group">
                                    <label for="frequency">Frequency:</label>
                                    <input type="text" id="frequency" name="frequency" placeholder="e.g., Twice a day" required>
                                </div>
                            </div>
                            <button type="submit" name="add_prescription" class="btn btn-submit">Add Prescription</button>
                        </form>
                    <?php endif; ?>
                </div>
                
                <div class="prescription-list">
                    <h2>Existing Prescriptions</h2>
                    <?php if (count($prescriptions) > 0): ?>
                        <?php foreach ($prescriptions as $prescription): ?>
                            <div class="prescription-item">
                                <div class="prescription-header">
                                    <div>
                                        <strong>Issue Date:</strong> <?php echo htmlspecialchars($prescription['ISSUE_DATE']); ?> | 
                                        <strong>Doctor:</strong> <?php echo htmlspecialchars($prescription['DOC_FNAME'] . ' ' . $prescription['DOC_LNAME']); ?>
                                    </div>
                                    <form action="prescription_form.php" method="POST" onsubmit="return confirm('Are you sure?');">
                                        <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                                        <input type="hidden" name="delete_prescription_id" value="<?php echo $prescription['PRESCRIPTION_ID']; ?>">
                                        <button type="submit" class="btn btn-delete">Delete</button>
                                    </form>
                                </div>
                                <p style="margin-top: 10px;"><strong>Diagnosis:</strong> <?php echo nl2br(htmlspecialchars($prescription['DIAGNOSIS'])); ?></p>
                                <p><strong>Symptoms:</strong> <?php echo nl2br(htmlspecialchars($prescription['SYMPTOMS'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No prescriptions found for this patient.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Footer -->
            <footer>
                <div class="footer-content">
                    <p>&copy; <span id="year"></span> QuickCare — Revolutionizing Healthcare Access</p>
                    <div class="social-links">
                    <a href="#" class="social-link"><span>f</span></a>
                    <a href="#" class="social-link"><span>𝕏</span></a>
                    <a href="#" class="social-link"><span>in</span></a>
                    <a href="#" class="social-link"><span>📷</span></a>
                </div>
            </footer>
        </div>
    </div>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>