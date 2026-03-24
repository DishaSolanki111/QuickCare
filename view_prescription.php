<?php
    session_start();
    include 'config.php';
    
    // Check authentication
    if (!isset($_SESSION['RECEPTIONIST_ID'])) {
        header("Location: login.php");
        exit;
    }
  
    // ================= DOWNLOAD LOGIC (POST) =================
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['download'])) {
        while (ob_get_level()) { ob_end_clean(); }
        $prescription_id = intval($_POST['download']);

        // Fetch prescription details
        $detail_q = mysqli_query($conn, "
            SELECT p.*, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME,
                d.FIRST_NAME AS DOC_FNAME, d.LAST_NAME AS DOC_LNAME, d.EDUCATION, s.SPECIALISATION_NAME,
                pat.FIRST_NAME AS PAT_FNAME, pat.LAST_NAME AS PAT_LNAME, pat.DOB, pat.GENDER, pat.BLOOD_GROUP
            FROM prescription_tbl p
            JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
            JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
            JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
            JOIN patient_tbl pat ON a.PATIENT_ID = pat.PATIENT_ID
            WHERE p.PRESCRIPTION_ID = $prescription_id
        ");

        $prescription = mysqli_fetch_assoc($detail_q);
        
        // Fetch medicines
        $med_q = mysqli_query($conn, "
            SELECT m.MED_NAME, pm.DOSAGE, pm.FREQUENCY, pm.DURATION
            FROM prescription_medicine_tbl pm
            JOIN medicine_tbl m ON pm.MEDICINE_ID = m.MEDICINE_ID
            WHERE pm.PRESCRIPTION_ID = $prescription_id
        ");

        $medicines = [];
        while ($m = mysqli_fetch_assoc($med_q)) { $medicines[] = $m; }

        require_once 'generate_prescription_pdf.php';
        generatePrescriptionPDF($prescription, $medicines, $conn);
        exit;
    }

    // ================= SAVE VITALS: Receptionist adds BP, Height, Weight → stored in prescription_tbl → shown in prescription_form.php when doctor selects this appointment =================
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_vitals'])) {
        $aid = (int) $_POST['appointment_id'];
        $bp = isset($_POST['blood_pressure']) ? mysqli_real_escape_string($conn, trim($_POST['blood_pressure'])) : '';
        $weight = isset($_POST['weight_kg']) && $_POST['weight_kg'] !== '' ? floatval($_POST['weight_kg']) : null;
        $height = isset($_POST['height_cm']) && $_POST['height_cm'] !== '' ? floatval($_POST['height_cm']) : null;
        $vitals_saved = false;
        if ($aid > 0) {
            // Receptionist can add vitals only at or after appointment time (same rule as reminder/button: use MySQL NOW())
            $apt_check = $conn->query("SELECT 1 FROM appointment_tbl WHERE APPOINTMENT_ID = $aid AND TIMESTAMP(APPOINTMENT_DATE, APPOINTMENT_TIME) <= NOW() LIMIT 1");
            if ($apt_check && $apt_check->num_rows === 0) {
                $tq = $conn->query("SELECT APPOINTMENT_TIME FROM appointment_tbl WHERE APPOINTMENT_ID = $aid LIMIT 1");
                $tm = $tq && ($tr = $tq->fetch_assoc()) ? date('h:i A', strtotime($tr['APPOINTMENT_TIME'])) : '';
                $_SESSION['vitals_saved_msg'] = 'Vitals can only be added at or after the appointment time (' . $tm . ').';
                header('Location: view_prescription.php');
                exit;
            }
            $exist = $conn->query("SELECT PRESCRIPTION_ID FROM prescription_tbl WHERE APPOINTMENT_ID = $aid LIMIT 1");
            if ($exist && $exist->num_rows > 0) {
                $stmt = $conn->prepare("UPDATE prescription_tbl SET HEIGHT_CM=?, WEIGHT_KG=?, BLOOD_PRESSURE=? WHERE APPOINTMENT_ID=?");
                if ($stmt) {
                    $stmt->bind_param("ddsi", $height, $weight, $bp, $aid);
                    $stmt->execute();
                    $vitals_saved = true;
                    $stmt->close();
                }
            } else {
                $apt_row = $conn->query("SELECT APPOINTMENT_DATE FROM appointment_tbl WHERE APPOINTMENT_ID = $aid LIMIT 1");
                $issue_date = date('Y-m-d');
                if ($apt_row && $a = $apt_row->fetch_assoc()) $issue_date = $a['APPOINTMENT_DATE'];
                $stmt = $conn->prepare("INSERT INTO prescription_tbl (APPOINTMENT_ID, ISSUE_DATE, HEIGHT_CM, WEIGHT_KG, BLOOD_PRESSURE, DIABETES, SYMPTOMS, DIAGNOSIS, ADDITIONAL_NOTES) VALUES (?, ?, ?, ?, ?, 'NO', '', '', '')");
                if ($stmt) {
                    $stmt->bind_param("isdds", $aid, $issue_date, $height, $weight, $bp);
                    $stmt->execute();
                    $vitals_saved = true;
                    $stmt->close();
                }
            }
        }
        $_SESSION['vitals_saved_msg'] = $vitals_saved ? 'Vitals (BP, Height, Weight) saved. They will appear in the prescription form when the doctor selects this appointment.' : 'Could not save vitals. Please try again.';
        header('Location: view_prescription.php');
        exit;
    }
    
    include 'recept_sidebar.php';
    $receptionist_id = $_SESSION['RECEPTIONIST_ID'];

    // ================= SCHEDULED APPOINTMENTS (for Add vitals) =================
    $scheduled_appointments = [];
    $sched_q = mysqli_query($conn, "
        SELECT a.APPOINTMENT_ID, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME,
               (TIMESTAMP(a.APPOINTMENT_DATE, a.APPOINTMENT_TIME) <= NOW()) AS time_reached,
               pat.PATIENT_ID, pat.FIRST_NAME AS PAT_FNAME, pat.LAST_NAME AS PAT_LNAME,
               d.DOCTOR_ID, d.FIRST_NAME AS DOC_FNAME, d.LAST_NAME AS DOC_LNAME, s.SPECIALISATION_NAME
        FROM appointment_tbl a
        JOIN patient_tbl pat ON a.PATIENT_ID = pat.PATIENT_ID
        JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
        JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
        WHERE a.STATUS = 'SCHEDULED'
          AND a.APPOINTMENT_DATE >= CURDATE()
          AND a.APPOINTMENT_DATE <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
        ORDER BY a.APPOINTMENT_DATE, a.APPOINTMENT_TIME
    ");
    if ($sched_q) {
        while ($r = mysqli_fetch_assoc($sched_q)) $scheduled_appointments[] = $r;
    }
    $vitals_by_apt = [];
    if (!empty($scheduled_appointments)) {
        $ids = array_map(function($a) { return $a['APPOINTMENT_ID']; }, $scheduled_appointments);
        $vq = @mysqli_query($conn, "SELECT APPOINTMENT_ID, BLOOD_PRESSURE, WEIGHT_KG, HEIGHT_CM FROM prescription_tbl WHERE APPOINTMENT_ID IN (" . implode(',', array_map('intval', $ids)) . ")");
        if ($vq) while ($v = mysqli_fetch_assoc($vq)) $vitals_by_apt[$v['APPOINTMENT_ID']] = $v;
    }

    // ================= SEARCH & FILTER LOGIC (POST) =================
    $doc_search = isset($_POST['doc_search']) ? mysqli_real_escape_string($conn, $_POST['doc_search']) : '';
    $pat_search = isset($_POST['pat_search']) ? mysqli_real_escape_string($conn, $_POST['pat_search']) : '';
    $spec_filter = isset($_POST['spec']) ? mysqli_real_escape_string($conn, $_POST['spec']) : '';

    $query = "
        SELECT p.*, a.APPOINTMENT_DATE, 
            d.DOCTOR_ID, d.FIRST_NAME AS DOC_FNAME, d.LAST_NAME AS DOC_LNAME, s.SPECIALISATION_NAME,
            pat.PATIENT_ID, pat.FIRST_NAME AS PAT_FNAME, pat.LAST_NAME AS PAT_LNAME,
            GROUP_CONCAT(CONCAT(m.MED_NAME, ' (', pm.DOSAGE, ')') SEPARATOR ', ') as MEDICINES_LIST
        FROM prescription_tbl p
        JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
        JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
        JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
        JOIN patient_tbl pat ON a.PATIENT_ID = pat.PATIENT_ID
        LEFT JOIN prescription_medicine_tbl pm ON p.PRESCRIPTION_ID = pm.PRESCRIPTION_ID
        LEFT JOIN medicine_tbl m ON pm.MEDICINE_ID = m.MEDICINE_ID
        WHERE 1=1";

    // Case-insensitive searches
    if ($doc_search != '') {
        $query .= " AND (d.FIRST_NAME LIKE '%$doc_search%' OR d.LAST_NAME LIKE '%$doc_search%')";
    }
    if ($pat_search != '') {
        $query .= " AND (pat.FIRST_NAME LIKE '%$pat_search%' OR pat.LAST_NAME LIKE '%$pat_search%')";
    }
    if ($spec_filter != '') {
        $query .= " AND s.SPECIALISATION_NAME = '$spec_filter'";
    }

    $query .= " GROUP BY p.PRESCRIPTION_ID ORDER BY d.LAST_NAME ASC, pat.LAST_NAME ASC, p.ISSUE_DATE DESC";
    $result = mysqli_query($conn, $query);

    // Grouping for Doctor Cards
    $doctors = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $doc_id = $row['DOCTOR_ID'];
        $pat_id = $row['PATIENT_ID'];
        if (!isset($doctors[$doc_id])) {
            $doctors[$doc_id] = ['name' => 'Dr. '.$row['DOC_FNAME'].' '.$row['DOC_LNAME'], 'spec' => $row['SPECIALISATION_NAME'], 'patients' => []];
        }
        if (!isset($doctors[$doc_id]['patients'][$pat_id])) {
            $doctors[$doc_id]['patients'][$pat_id] = ['name' => $row['PAT_FNAME'].' '.$row['PAT_LNAME'], 'prescriptions' => []];
        }
        $doctors[$doc_id]['patients'][$pat_id]['prescriptions'][] = $row;
    }

    $specs_res = mysqli_query($conn, "SELECT SPECIALISATION_NAME FROM specialisation_tbl");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prescriptions Directory - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --dark: #072D44; --mid: #064469; --soft: #5790AB; --light: #9CCDD8; --bg: #f4f7f6; }
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: var(--bg); display: flex; }
        .main { margin-left: 250px; padding: 30px; width: calc(100% - 250px); }
        
        .filter-bar { background: #fff; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .filter-bar form { display: flex; flex-wrap: nowrap; gap: 15px; align-items: flex-end; width: 100%; }
        .filter-group { display: flex; flex-direction: column; gap: 5px; flex: 1; }
        .filter-group label { font-size: 0.8rem; font-weight: 600; color: var(--mid); white-space: nowrap; }
        .filter-group input, .filter-group select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 6px; outline: none; width: 100%; }
        
        .btn-search { background: var(--mid); color: #fff; border: none; padding: 0 20px; border-radius: 6px; cursor: pointer; height: 38px; transition: 0.3s; white-space: nowrap; }
        .btn-search:hover { background: var(--soft); }
        .btn-reset { padding: 8px 10px; color: var(--soft); text-decoration: none; font-size: 0.85rem; height: 38px; display: flex; align-items: center; }

        .doctor-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(500px, 1fr)); gap: 25px; }
        .doctor-card { background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 8px 20px rgba(0,0,0,0.05); border: 1px solid #eee; margin-bottom: 10px; }
        .doctor-header { background: var(--dark); color: #fff; padding: 15px 20px; display: flex; align-items: center; gap: 12px; }
        .patient-group { padding: 15px 20px; border-bottom: 1px solid #f0f0f0; background: #fafafa; }
        .patient-name { font-weight: bold; color: var(--mid); margin-bottom: 10px; display: block; }
        
        .prescription-detail-card { background: #fff; border: 1px solid #eef2f7; border-radius: 10px; padding: 15px; margin-bottom: 10px; border-left: 4px solid var(--soft); }
        .detail-row { font-size: 0.9rem; margin-bottom: 6px; color: #333; line-height: 1.4; }
        .detail-row strong { color: var(--mid); min-width: 100px; display: inline-block; }
        .card-actions { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; margin-bottom: 8px; }
        .btn-pdf { background: var(--soft); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.8rem; cursor: pointer; }
        .btn-vitals-inline { background: var(--mid); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.8rem; cursor: pointer; white-space: nowrap; }
        .btn-vitals-inline:hover { background: var(--dark); }
        .vitals-section { background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 25px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-left: 4px solid var(--soft); }
        .vitals-section h3 { color: var(--dark); margin: 0 0 15px 0; font-size: 1.1rem; }
        .vitals-table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        .vitals-table th, .vitals-table td { padding: 10px 12px; text-align: left; border-bottom: 1px solid #eee; }
        .vitals-table th { background: var(--mid); color: #fff; }
        .btn-vitals { background: var(--soft); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.8rem; cursor: pointer; }
        .btn-vitals:hover { background: var(--mid); }
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 9999; align-items: center; justify-content: center; }
        .modal-overlay.show { display: flex; }
        .modal-box { background: #fff; padding: 25px; border-radius: 12px; min-width: 320px; max-width: 420px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        .modal-box h4 { margin: 0 0 15px 0; color: var(--dark); }
        .modal-box .form-group { margin-bottom: 12px; }
        .modal-box label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 4px; color: #555; }
        .modal-box input { width: 100%; padding: 8px 10px; border: 1px solid #ddd; border-radius: 6px; }
        .modal-box .btn-row { display: flex; gap: 10px; margin-top: 18px; }
        .modal-box .btn-row button { padding: 8px 16px; border-radius: 6px; border: none; cursor: pointer; font-weight: 600; }
        .modal-box .btn-save { background: var(--soft); color: #fff; }
        .modal-box .btn-cancel { background: #e0e0e0; color: #333; }
        .vitals-badge { font-size: 0.8rem; color: var(--mid); margin-top: 4px; }
    </style>
</head>
<body>
<div class="main">
    <?php include 'receptionist_header.php'; ?>

    <?php if (isset($_SESSION['vitals_saved_msg'])) { echo '<div class="alert alert-success" style="background:#d4edda;color:#155724;padding:12px 20px;border-radius:8px;margin-bottom:20px;">' . htmlspecialchars($_SESSION['vitals_saved_msg']) . '</div>'; unset($_SESSION['vitals_saved_msg']); } ?>
    <?php if (!empty($scheduled_appointments)): ?>
    <div class="vitals-section">
        <h3><i class="fa-solid fa-heart-pulse"></i> Add vitals for appointment (Receptionist)</h3>
        <table class="vitals-table">
            <thead><tr><th>Date & Time</th><th>Patient</th><th>Doctor</th><th>Vitals</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($scheduled_appointments as $sapt): 
                $vid = $sapt['APPOINTMENT_ID'];
                $vitals = isset($vitals_by_apt[$vid]) ? $vitals_by_apt[$vid] : null;
                $time_reached = !empty($sapt['time_reached']); // Same as reminder: TIMESTAMP(...) <= NOW() in MySQL
                $apt_ts = strtotime($sapt['APPOINTMENT_DATE'] . ' ' . $sapt['APPOINTMENT_TIME']);
            ?>
                <tr>
                    <td><?= date('M d, Y', strtotime($sapt['APPOINTMENT_DATE'])) ?> <?= date('h:i A', strtotime($sapt['APPOINTMENT_TIME'])) ?></td>
                    <td><?= htmlspecialchars($sapt['PAT_FNAME'] . ' ' . $sapt['PAT_LNAME']) ?></td>
                    <td>Dr. <?= htmlspecialchars($sapt['DOC_FNAME'] . ' ' . $sapt['DOC_LNAME']) ?> (<?= htmlspecialchars($sapt['SPECIALISATION_NAME']) ?>)</td>
                    <td class="vitals-badge">
                        <?php if ($vitals): ?>
                            BP: <?= htmlspecialchars($vitals['BLOOD_PRESSURE'] ?: '–') ?> | W: <?= $vitals['WEIGHT_KG'] !== null ? $vitals['WEIGHT_KG'] : '–' ?> kg | H: <?= $vitals['HEIGHT_CM'] !== null ? $vitals['HEIGHT_CM'] : '–' ?> cm
                        <?php else: ?>
                            <span style="color:#999;">Not recorded</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                        $has_vitals = $vitals && (trim($vitals['BLOOD_PRESSURE'] ?? '') !== '' || ($vitals['WEIGHT_KG'] ?? null) !== null || ($vitals['HEIGHT_CM'] ?? null) !== null);
                        if ($has_vitals): ?>
                        <span style="color:#0a6b2d; font-weight:600;">Recorded</span>
                        <?php elseif (!$time_reached): ?>
                        <span style="color:#666;">Available at <?= date('h:i A', $apt_ts) ?></span>
                        <?php else: ?>
                        <button type="button" class="btn-vitals" onclick="openVitalsModal(<?= $vid ?>, '<?= date('M d, Y', strtotime($sapt['APPOINTMENT_DATE'])) ?> <?= date('h:i A', strtotime($sapt['APPOINTMENT_TIME'])) ?>', '<?= htmlspecialchars(addslashes($sapt['PAT_FNAME'] . ' ' . $sapt['PAT_LNAME'])) ?>', <?= $vitals ? json_encode($vitals) : 'null' ?>)">Add vitals</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
    
    <div class="filter-bar">
        <form method="POST" action="view_prescription.php">
            <div class="filter-group">
                <label>Doctor Name</label>
                <input type="text" name="doc_search" placeholder="Search Doctor..." value="<?= htmlspecialchars($doc_search) ?>">
            </div>
            <div class="filter-group">
                <label>Patient Name</label>
                <input type="text" name="pat_search" placeholder="Search Patient..." value="<?= htmlspecialchars($pat_search) ?>">
            </div>
            <div class="filter-group">
                <label>Specialization</label>
                <select name="spec">
                    <option value="">All</option>
                    <?php while($s = mysqli_fetch_assoc($specs_res)): ?>
                        <option value="<?= $s['SPECIALISATION_NAME'] ?>" <?= ($spec_filter == $s['SPECIALISATION_NAME']) ? 'selected' : '' ?>>
                            <?= $s['SPECIALISATION_NAME'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <button type="submit" class="btn-search"><i class="fa-solid fa-filter"></i> Filter</button>
            <a href="view_prescription.php" class="btn-reset">Reset</a>
        </form>
    </div>

    <div class="doctor-grid">
        <?php if (!empty($doctors)): ?>
            <?php foreach ($doctors as $doc): ?>
                <div class="doctor-card">
                    <div class="doctor-header"><i class="fa-solid fa-user-md"></i> <h3><?= $doc['name'] ?> (<?= $doc['spec'] ?>)</h3></div>
                    <?php foreach ($doc['patients'] as $pat): ?>
                        <div class="patient-group">
                            <span class="patient-name"><i class="fa-solid fa-user"></i> <?= $pat['name'] ?></span>
                            <?php foreach ($pat['prescriptions'] as $pres): ?>
                                <div class="prescription-detail-card">
                                    <?php $has_vitals = !empty($pres['BLOOD_PRESSURE']) || ($pres['WEIGHT_KG'] !== null && $pres['WEIGHT_KG'] !== '') || ($pres['HEIGHT_CM'] !== null && $pres['HEIGHT_CM'] !== ''); ?>
                                    <div class="card-actions">
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="download" value="<?= $pres['PRESCRIPTION_ID'] ?>">
                                            <button type="submit" class="btn-pdf"><i class="fa-solid fa-file-pdf"></i> PDF</button>
                                        </form>
                                        <?php if (!$has_vitals): ?>
                                        <button type="button" class="btn-vitals-inline" onclick="openVitalsModal(<?= (int)$pres['APPOINTMENT_ID'] ?>, '<?= date('M d, Y', strtotime($pres['APPOINTMENT_DATE'])) ?>', '<?= htmlspecialchars(addslashes($pat['name'])) ?>', <?= htmlspecialchars(json_encode(['BLOOD_PRESSURE' => $pres['BLOOD_PRESSURE'] ?? '', 'WEIGHT_KG' => $pres['WEIGHT_KG'] ?? null, 'HEIGHT_CM' => $pres['HEIGHT_CM'] ?? null])) ?>)">Add vitals</button>
                                        <?php endif; ?>
                                    </div>
                                    <div class="detail-row"><strong>Appt. Date:</strong> <?= date('M d, Y', strtotime($pres['APPOINTMENT_DATE'])) ?></div>
                                    <?php
                                    $is_past_appointment = strtotime($pres['APPOINTMENT_DATE']) < strtotime(date('Y-m-d'));
                                    if ($has_vitals && $is_past_appointment):
                                    ?>
                                    <div class="detail-row"><strong>Vitals:</strong> BP: <?= htmlspecialchars($pres['BLOOD_PRESSURE'] ?? '–') ?> | Weight: <?= $pres['WEIGHT_KG'] !== null && $pres['WEIGHT_KG'] !== '' ? $pres['WEIGHT_KG'] : '–' ?> kg | Height: <?= $pres['HEIGHT_CM'] !== null && $pres['HEIGHT_CM'] !== '' ? $pres['HEIGHT_CM'] : '–' ?> cm</div>
                                    <?php endif; ?>
                                    <div class="detail-row"><strong>Symptoms:</strong> <?= htmlspecialchars($pres['SYMPTOMS']) ?></div>
                                    <div class="detail-row"><strong>Diagnosis:</strong> <?= htmlspecialchars($pres['DIAGNOSIS']) ?></div>
                                    <div class="detail-row"><strong>Medicines:</strong> <?= htmlspecialchars($pres['MEDICINES_LIST'] ?: 'None') ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 50px; color: #888;">
                <i class="fa-solid fa-magnifying-glass" style="font-size: 3rem; margin-bottom: 10px;"></i>
                <h3>No records found</h3>
            </div>
        <?php endif; ?>
    </div>

    <div class="modal-overlay" id="vitalsModal">
        <div class="modal-box">
            <h4 id="vitalsModalTitle">Add vitals (BP, Height, Weight)</h4>
            <p style="font-size:0.8rem; color:#666; margin-bottom:12px;">Stored for this appointment and pre-filled in the prescription form when the doctor selects it.</p>
            <form method="POST" action="view_prescription.php">
                <input type="hidden" name="save_vitals" value="1">
                <input type="hidden" name="appointment_id" id="vitalsAppointmentId" value="">
                <div class="form-group">
                    <label>Blood Pressure (BP)</label>
                    <input type="text" name="blood_pressure" id="vitalsBP" placeholder="e.g. 120/80">
                </div>
                <div class="form-group">
                    <label>Height (cm)</label>
                    <input type="number" name="height_cm" id="vitalsHeight" step="0.1" placeholder="e.g. 170" min="0">
                </div>
                <div class="form-group">
                    <label>Weight (kg)</label>
                    <input type="number" name="weight_kg" id="vitalsWeight" step="0.1" placeholder="e.g. 65" min="0">
                </div>
                <div class="btn-row">
                    <button type="submit" class="btn-save">Save</button>
                    <button type="button" class="btn-cancel" onclick="closeVitalsModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function openVitalsModal(aptId, dateTime, patientName, existing) {
    document.getElementById('vitalsAppointmentId').value = aptId;
    document.getElementById('vitalsModalTitle').textContent = 'Add vitals – ' + dateTime + ' – ' + patientName;
    document.getElementById('vitalsBP').value = existing && existing.BLOOD_PRESSURE ? existing.BLOOD_PRESSURE : '';
    document.getElementById('vitalsWeight').value = existing && existing.WEIGHT_KG != null ? existing.WEIGHT_KG : '';
    document.getElementById('vitalsHeight').value = existing && existing.HEIGHT_CM != null ? existing.HEIGHT_CM : '';
    document.getElementById('vitalsModal').classList.add('show');
}
function closeVitalsModal() {
    document.getElementById('vitalsModal').classList.remove('show');
}
</script>
</body>
</html>