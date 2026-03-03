<?php
session_start();

// Check if user is logged in and is a doctor
if (
    !isset($_SESSION['LOGGED_IN']) ||
    $_SESSION['LOGGED_IN'] !== true ||
    !isset($_SESSION['USER_TYPE']) ||
    $_SESSION['USER_TYPE'] !== 'doctor'
) {
    header("Location: login.php");
    exit();
}

// Include your existing config file
require_once 'config.php';

// Check if the connection variable $conn exists and is valid
if (!$conn) {
    die("Error: Database connection failed. Please check your config.php file.");
}

// Get doctor ID from session
$doctor_id = $_SESSION['DOCTOR_ID'];

// --- GET PATIENT ID FROM URL ---
$patient_id_val = isset($_POST['patient_id']) ? $_POST['patient_id'] : (isset($_GET['patient_id']) ? $_GET['patient_id'] : null);
if (!$patient_id_val || !is_numeric($patient_id_val)) {
    die("<div style='font-family: Arial, sans-serif; color: #d9534f; padding: 20px; border: 1px solid #d9534f; background-color: #f2dede; border-radius: 5px;'><strong>Error:</strong> Invalid or missing Patient ID. Please go back and select a patient.</div>");
}
 $patient_id = intval($patient_id_val);

// --- FETCH PATIENT DETAILS ---
 $stmt = $conn->prepare("SELECT pt.PATIENT_ID, pt.FIRST_NAME, pt.LAST_NAME, pt.DOB, pt.GENDER, 
                                pt.BLOOD_GROUP, pt.PHONE, pt.EMAIL, pt.ADDRESS,
                                MAX(a.APPOINTMENT_DATE) AS LAST_APPOINTMENT_DATE
                         FROM patient_tbl pt
                         INNER JOIN appointment_tbl a ON pt.PATIENT_ID = a.PATIENT_ID
                         WHERE pt.PATIENT_ID = ? AND a.DOCTOR_ID = ?
                         GROUP BY pt.PATIENT_ID, pt.FIRST_NAME, pt.LAST_NAME, pt.DOB, pt.GENDER, 
                                  pt.BLOOD_GROUP, pt.PHONE, pt.EMAIL, pt.ADDRESS
                         LIMIT 1");
if ($stmt === false) {
    die("Error preparing patient query: " . $conn->error);
}
 $stmt->bind_param("ii", $patient_id, $doctor_id);
 $stmt->execute();
 $result = $stmt->get_result();
 $patient = $result->fetch_assoc();
 $stmt->close();
if (!$patient) {
    die("<div style='font-family: Arial, sans-serif; color: #d9534f; padding: 20px; border: 1px solid #d9534f; background-color: #f2dede; border-radius: 5px;'><strong>Error:</strong> Patient not found or access denied.</div>");
}

// Age calculation
$patient_age = null;
if (!empty($patient['DOB']) && $patient['DOB'] !== '0000-00-00') {
    $dob = new DateTime($patient['DOB']);
    $today = new DateTime();
    $patient_age = $today->diff($dob)->y;
}

// Format last appointment date
$last_appointment_formatted = null;
if (!empty($patient['LAST_APPOINTMENT_DATE']) && $patient['LAST_APPOINTMENT_DATE'] !== '0000-00-00') {
    $last_appt = new DateTime($patient['LAST_APPOINTMENT_DATE']);
    $last_appointment_formatted = $last_appt->format('F d, Y');
}

 $message = '';

// --- HANDLE FORM SUBMISSION (MULTIPLE MEDICINES) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_prescription'])) {
    try {
        $appointment_id = intval($_POST['appointment_id']);
        $conn->begin_transaction();
        $height = !empty($_POST['height_cm']) ? $_POST['height_cm'] : null;
        $weight = !empty($_POST['weight_kg']) ? $_POST['weight_kg'] : null;
        $bp = !empty($_POST['blood_pressure']) ? $_POST['blood_pressure'] : null;

        $existing = $conn->query("SELECT PRESCRIPTION_ID FROM prescription_tbl WHERE APPOINTMENT_ID = $appointment_id LIMIT 1");
        $prescription_id = null;
        if ($existing && $existing->num_rows > 0) {
            $prescription_id = (int) $existing->fetch_assoc()['PRESCRIPTION_ID'];
            $upd = $conn->prepare("UPDATE prescription_tbl SET ISSUE_DATE=?, HEIGHT_CM=?, WEIGHT_KG=?, BLOOD_PRESSURE=?, DIABETES=?, SYMPTOMS=?, DIAGNOSIS=?, ADDITIONAL_NOTES=? WHERE PRESCRIPTION_ID=?");
            $upd->bind_param("ssssssssi", $_POST['issue_date'], $height, $weight, $bp, $_POST['diabetes'], $_POST['symptoms'], $_POST['diagnosis'], $_POST['additional_notes'], $prescription_id);
            $upd->execute();
            $upd->close();
            $conn->query("DELETE FROM prescription_medicine_tbl WHERE PRESCRIPTION_ID = $prescription_id");
        } else {
            $sql = "INSERT INTO prescription_tbl (APPOINTMENT_ID, ISSUE_DATE, HEIGHT_CM, WEIGHT_KG, BLOOD_PRESSURE, DIABETES, SYMPTOMS, DIAGNOSIS, ADDITIONAL_NOTES) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isissssss", $appointment_id, $_POST['issue_date'], $height, $weight, $bp, $_POST['diabetes'], $_POST['symptoms'], $_POST['diagnosis'], $_POST['additional_notes']);
            $stmt->execute();
            $prescription_id = $conn->insert_id;
            $stmt->close();
        }

        if (isset($_POST['medicine_id']) && is_array($_POST['medicine_id'])) {
            $med_sql = "INSERT INTO prescription_medicine_tbl (PRESCRIPTION_ID, MEDICINE_ID, DOSAGE, DURATION, FREQUENCY) VALUES (?, ?, ?, ?, ?)";
            $med_stmt = $conn->prepare($med_sql);
            foreach ($_POST['medicine_id'] as $key => $mid) {
                if (!empty($mid)) {
                    $dos = $_POST['dosage'][$key];
                    $dur = $_POST['duration'][$key];
                    $freq = $_POST['frequency'][$key];
                    $med_stmt->bind_param("iisss", $prescription_id, $mid, $dos, $dur, $freq);
                    $med_stmt->execute();
                }
            }
            $med_stmt->close();
        }

        $conn->commit();
        header("Location: manage_prescriptions.php?status=success");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $message = "<p class='message error'><strong>Error:</strong> " . $e->getMessage() . "</p>";
    }
}

// --- HANDLE DELETION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_prescription_id'])) {
    $delete_id = intval($_POST['delete_prescription_id']);
    $stmt = $conn->prepare("DELETE FROM prescription_tbl WHERE PRESCRIPTION_ID = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: manage_prescriptions.php?status=deleted_success");
    exit();
}

// --- FETCH EXISTING PRESCRIPTIONS ---
$sql = "SELECT p.PRESCRIPTION_ID, p.ISSUE_DATE, p.DIAGNOSIS, p.SYMPTOMS, a.APPOINTMENT_DATE, d.FIRST_NAME AS DOC_FNAME, d.LAST_NAME AS DOC_LNAME 
        FROM prescription_tbl p 
        JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID 
        JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID 
        WHERE a.PATIENT_ID = ? AND a.DOCTOR_ID = ? 
        ORDER BY a.APPOINTMENT_DATE DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $patient_id, $doctor_id);
$stmt->execute();
$prescriptions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// --- FETCH MEDICINES & APPOINTMENTS ---
 $medicines = $conn->query("SELECT MEDICINE_ID, MED_NAME FROM medicine_tbl ORDER BY MED_NAME")->fetch_all(MYSQLI_ASSOC);
 $appointment_sql = "SELECT a.APPOINTMENT_ID, a.APPOINTMENT_DATE, d.FIRST_NAME, d.LAST_NAME, s.SPECIALISATION_NAME 
                     FROM appointment_tbl a 
                     JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID 
                     JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID 
                     WHERE a.PATIENT_ID = ? AND a.DOCTOR_ID = ? AND a.STATUS = 'COMPLETED' 
                     ORDER BY a.APPOINTMENT_DATE DESC";
 $appointment_stmt = $conn->prepare($appointment_sql);
 $appointment_stmt->bind_param("ii", $patient_id, $doctor_id);
 $appointment_stmt->execute();
 $completed_appointments = $appointment_stmt->get_result()->fetch_all(MYSQLI_ASSOC);

 // Fetch vitals from prescription_tbl (receptionist may have added them in view_prescription) for pre-fill
 $appointment_vitals = [];
 if (!empty($completed_appointments)) {
     $apt_ids = array_column($completed_appointments, 'APPOINTMENT_ID');
     $vq = @$conn->query("SELECT APPOINTMENT_ID, BLOOD_PRESSURE, WEIGHT_KG, HEIGHT_CM FROM prescription_tbl WHERE APPOINTMENT_ID IN (" . implode(',', array_map('intval', $apt_ids)) . ")");
     if ($vq) while ($v = $vq->fetch_assoc()) $appointment_vitals[$v['APPOINTMENT_ID']] = $v;
 }
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
            --dark-blue: #072D44; --mid-blue: #064469; --soft-blue: #072D44; --light-blue: #9CCDD8;
            --gray-blue: #D0D7E1; --white: #ffffff; --card-bg: #F6F9FB; --primary-color: #1a3a5f;
            --secondary-color: #3498db; --accent-color: #2ecc71; --danger-color: #e74c3c;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
        body { background-color: #f5f7fa; color: #333; line-height: 1.6; }
        .container { display: flex; min-height: 100vh; }
        .sidebar { width: 250px; background: #072D44; min-height: 100vh; color: white; padding-top: 30px; position: fixed; }
        .sidebar h2 { text-align: center; margin-bottom: 40px; color: #9CCDD8; }
        .sidebar a { display: block; padding: 15px 25px; color: #D0D7E1; text-decoration: none; border-left: 4px solid transparent; }
        .sidebar a:hover { background: #064469; border-left: 4px solid #9CCDD8; color: white; }
        .main-content { flex: 1; margin-left: 250px; padding: 20px; }
        .content-card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .patient-details-card { background: white; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08); border: 1px solid #e0e0e0; }
        .patient-details-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 25px; }
        .patient-detail-item { display: flex; align-items: flex-start; gap: 12px; }
        .patient-detail-icon { width: 40px; height: 40px; background: var(--primary-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; }
        .form-container { margin-top: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .medicine-block { background: #f9f9f9; padding: 15px; border: 1px solid #eee; border-radius: 5px; margin-bottom: 10px; position: relative; }
        .btn { padding: 10px 15px; border-radius: 5px; color: #fff; border: none; cursor: pointer; }
        .btn-submit { background-color: #072d44; width: 100%; font-size: 16px; margin-top: 10px; }
        .btn-add { background-color: var(--accent-color); margin-bottom: 15px; }
        .btn-delete { background-color: #dc3545; float: right; }
        .prescription-item { border: 1px solid #eee; padding: 15px; margin-bottom: 15px; background-color: #fafafa; }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'doctor_sidebar.php'; ?>
        <div class="main-content">
            <?php include 'doctor_header.php'; ?>
            <div class="content-card">
                <a href="manage_prescriptions.php" style="display:inline-block; margin-bottom:20px; background-color: var(--primary-color); color:#fff; text-decoration:none; font-weight:bold; padding:8px 16px; border:1px solid var(--primary-color); border-radius:5px;">&larr; Back to Patient List</a>
                
                <?php echo $message; ?>

                <div class="patient-details-card">
                    <h2 style="color:var(--soft-blue); border-bottom:2px solid #f0f0f0; padding-bottom:10px; margin-bottom:20px;"><i class="fas fa-user-md"></i> Patient Information</h2>
                    <div class="patient-details-grid">
                        <div class="patient-detail-item">
                            <div class="patient-detail-icon"><i class="fas fa-user"></i></div>
                            <div><div style="font-size:0.85rem; color:#666;">Patient Name</div><strong><?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></strong></div>
                        </div>
                        <div class="patient-detail-item">
                            <div class="patient-detail-icon"><i class="fas fa-birthday-cake"></i></div>
                            <div><div style="font-size:0.85rem; color:#666;">Age</div><strong><?php echo $patient_age ?? 'N/A'; ?> years</strong></div>
                        </div>
                        <div class="patient-detail-item">
                            <div class="patient-detail-icon"><i class="fas fa-tint"></i></div>
                            <div><div style="font-size:0.85rem; color:#666;">Blood Group</div><strong><?php echo $patient['BLOOD_GROUP'] ?? 'N/A'; ?></strong></div>
                        </div>
                        <div class="patient-detail-item">
                            <div class="patient-detail-icon"><i class="fas fa-calendar-check"></i></div>
                            <div><div style="font-size:0.85rem; color:#666;">Last Visit</div><strong><?php echo $last_appointment_formatted ?? 'N/A'; ?></strong></div>
                        </div>
                    </div>
                </div>

                <div class="form-container">
                    <h2 style="color:var(--soft-blue); border-bottom:2px solid #f0f0f0; padding-bottom:10px; margin-bottom:20px;">Add New Prescription</h2>
                    <form action="prescription_form.php" method="POST" id="prescriptionForm">
                        <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                        <div class="form-group">
                            <label>Link to Completed Appointment:</label>
                            <select name="appointment_id" required>
                                <option value="">--Select--</option>
                                <?php foreach ($completed_appointments as $apt): ?>
                                    <option value="<?php echo $apt['APPOINTMENT_ID']; ?>"><?php echo $apt['APPOINTMENT_DATE'] . " (Dr. " . $apt['LAST_NAME'] . ")"; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-grid">
                            <div class="form-group"><label>Issue Date:</label><input type="date" name="issue_date" required value="<?php echo date('Y-m-d'); ?>"></div>
                            <div class="form-group"><label>Blood Pressure (BP):</label><input type="text" name="blood_pressure" id="form_blood_pressure" placeholder="120/80"></div>
                            <div class="form-group"><label>Height (cm):</label><input type="number" name="height_cm" id="form_height_cm" step="0.1"></div>
                            <div class="form-group"><label>Weight (kg):</label><input type="number" step="0.1" name="weight_kg" id="form_weight_kg"></div>
                            <div class="form-group">
                                <label>Diabetes:</label>
                                <select name="diabetes">
                                    <option value="NO">No</option>
                                    <option value="TYPE-1">Type-1</option>
                                    <option value="TYPE-2">Type-2</option>
                                    <option value="PRE-DIABTIC">Pre-Diabetic</option>
                                </select>
                            </div>
                        </div>
                        <p style="font-size:0.85rem; color:#666; margin-bottom:12px;"><i class="fas fa-info-circle"></i> BP, Height and Weight are added by the receptionist in <strong>View Prescription → Add vitals</strong>. When you select an appointment above, those vitals are loaded here automatically.</p>
                        <div class="form-group"><label>Symptoms:</label><textarea name="symptoms" rows="3"></textarea></div>
                        <div class="form-group"><label>Diagnosis:</label><textarea name="diagnosis" rows="3" required></textarea></div>

                        <h3 style="color:var(--soft-blue); border-bottom:2px solid #f0f0f0; padding-bottom:10px; margin-bottom:20px;">Medicine Details</h3>
                        <div id="medicine-area">
                            <div class="medicine-block">
                                <div class="form-grid">
                                    <div class="form-group">
                                        <label>Medicine:</label>
                                        <select name="medicine_id[]" required>
                                            <option value="">--Select--</option>
                                            <?php foreach ($medicines as $m): ?><option value="<?php echo $m['MEDICINE_ID']; ?>"><?php echo htmlspecialchars($m['MED_NAME']); ?></option><?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group"><label>Dosage:</label><input type="text" name="dosage[]" required placeholder="500mg"></div>
                                    <div class="form-group"><label>Duration:</label><input type="text" name="duration[]" required placeholder="7 days"></div>
                                    <div class="form-group"><label>Frequency:</label><input type="text" name="frequency[]" required placeholder="Twice a day"></div>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-add" onclick="addMedRow()"><i class="fas fa-plus"></i> Add Another Medicine</button>

                        <div class="form-group"><label>Additional Notes:</label><textarea name="additional_notes" rows="3"></textarea></div>
                        <button type="submit" name="add_prescription" class="btn btn-submit">Add Prescription</button>
                    </form>
                </div>

                <div class="prescription-list" style="margin-top:40px;">
                    <h2 style="color:var(--soft-blue); border-bottom:2px solid #f0f0f0; padding-bottom:10px; margin-bottom:20px;">Existing Prescriptions</h2>
                    <?php foreach ($prescriptions as $p): ?>
                        <div class="prescription-item">
                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                <span><strong>Issue Date:</strong> <?php echo $p['ISSUE_DATE']; ?> | <strong>Doctor:</strong> <?php echo $p['DOC_FNAME'] . ' ' . $p['DOC_LNAME']; ?></span>
                                <form action="prescription_form.php" method="POST" onsubmit="return confirm('Are you sure?');">
                                    <input type="hidden" name="patient_id" value="<?php echo $patient_id; ?>">
                                    <input type="hidden" name="delete_prescription_id" value="<?php echo $p['PRESCRIPTION_ID']; ?>">
                                    <button type="submit" class="btn btn-delete">Delete</button>
                                </form>
                            </div>
                            <p style="margin-top:10px;"><strong>Diagnosis:</strong> <?php echo nl2br(htmlspecialchars($p['DIAGNOSIS'])); ?></p>
                            <p><strong>Symptoms:</strong> <?php echo nl2br(htmlspecialchars($p['SYMPTOMS'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        var appointmentVitals = <?= json_encode($appointment_vitals); ?>;
        var aptSelect = document.querySelector('select[name="appointment_id"]');
        if (aptSelect) {
            aptSelect.addEventListener('change', function() {
                var aptId = this.value;
                var v = appointmentVitals[aptId];
                document.getElementById('form_blood_pressure').value = v && v.BLOOD_PRESSURE ? v.BLOOD_PRESSURE : '';
                document.getElementById('form_height_cm').value = v && v.HEIGHT_CM != null && v.HEIGHT_CM !== '' ? v.HEIGHT_CM : '';
                document.getElementById('form_weight_kg').value = v && v.WEIGHT_KG != null && v.WEIGHT_KG !== '' ? v.WEIGHT_KG : '';
            });
        }
        function addMedRow() {
            const area = document.getElementById('medicine-area');
            const row = document.querySelector('.medicine-block').cloneNode(true);
            row.querySelectorAll('input').forEach(i => i.value = '');
            row.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
            const delBtn = document.createElement('button');
            delBtn.type = 'button';
            delBtn.style = 'background:red; color:white; border:none; padding:5px 10px; border-radius:3px; cursor:pointer; margin-top:5px;';
            delBtn.innerHTML = 'Remove Row';
            delBtn.onclick = function() { this.parentElement.remove(); };
            row.appendChild(delBtn);
            area.appendChild(row);
        }
    </script>
</body>
</html>