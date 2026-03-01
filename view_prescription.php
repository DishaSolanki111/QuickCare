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
    
    include 'recept_sidebar.php';
    $receptionist_id = $_SESSION['RECEPTIONIST_ID'];

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
        .btn-pdf { background: var(--soft); color: #fff; border: none; padding: 6px 12px; border-radius: 4px; font-size: 0.8rem; cursor: pointer; float: right; }
    </style>
</head>
<body>
<div class="main">
    <?php include 'receptionist_header.php'; ?>
    
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
                                    <form method="POST">
                                        <input type="hidden" name="download" value="<?= $pres['PRESCRIPTION_ID'] ?>">
                                        <button type="submit" class="btn-pdf"><i class="fa-solid fa-file-pdf"></i> PDF</button>
                                    </form>
                                    <div class="detail-row"><strong>Appt. Date:</strong> <?= date('M d, Y', strtotime($pres['APPOINTMENT_DATE'])) ?></div>
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
</div>
</body>
</html>