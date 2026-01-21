    <?php
    session_start();

    if (!isset($_SESSION['RECEPTIONIST_ID'])) {
        header("Location: login.php");
        exit;
    }

    include 'config.php';
    include 'recept_sidebar.php';
    $receptionist_id = $_SESSION['RECEPTIONIST_ID'];

    // ================= FETCH PRESCRIPTIONS =================
    $prescriptions_query = mysqli_query($conn, "
        SELECT p.*, 
            a.APPOINTMENT_DATE, a.APPOINTMENT_TIME,
            d.FIRST_NAME AS DOC_FNAME, d.LAST_NAME AS DOC_LNAME, d.EDUCATION, d.PHONE AS DOC_PHONE, d.EMAIL AS DOC_EMAIL,
            s.SPECIALISATION_NAME,
            pat.FIRST_NAME AS PAT_FNAME, pat.LAST_NAME AS PAT_LNAME,
            pat.DOB, pat.GENDER, pat.BLOOD_GROUP, pat.ADDRESS, pat.PHONE AS PAT_PHONE, pat.EMAIL AS PAT_EMAIL
        FROM prescription_tbl p
        JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
        JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
        JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
        JOIN patient_tbl pat ON a.PATIENT_ID = pat.PATIENT_ID
        ORDER BY p.ISSUE_DATE DESC
    ");

    // ================= DOWNLOAD SINGLE PRESCRIPTION =================
    if (isset($_GET['download'])) {

        $prescription_id = intval($_GET['download']);

        $detail_q = mysqli_query($conn, "
            SELECT * FROM prescription_tbl WHERE PRESCRIPTION_ID = $prescription_id
        ");

        if (mysqli_num_rows($detail_q) === 0) {
            exit("Invalid prescription");
        }

        $prescription = mysqli_fetch_assoc($detail_q);

        $med_q = mysqli_query($conn, "
            SELECT m.MED_NAME, pm.DOSAGE, pm.FREQUENCY, pm.DURATION
            FROM prescription_medicine_tbl pm
            JOIN medicine_tbl m ON pm.MEDICINE_ID = m.MEDICINE_ID
            WHERE pm.PRESCRIPTION_ID = $prescription_id
        ");

        $med_html = '';
        while ($m = mysqli_fetch_assoc($med_q)) {
            $med_html .= "
                <div class='medicine-item'>
                    <strong>{$m['MED_NAME']}</strong><br>
                    {$m['DOSAGE']} | {$m['FREQUENCY']} | {$m['DURATION']}
                </div>
            ";
        }

        header("Content-Type: application/pdf");
        header("Content-Disposition: attachment; filename=prescription_$prescription_id.html");

        ob_start();
        ?>
        <html>
        <head>
            <style>
                body { font-family: Arial; padding:20px; }
                h2 { color:#064469; }
                .section { margin-bottom:20px; }
                .label { font-weight:bold; }
                .medicine-item { border-bottom:1px solid #ddd; padding:8px 0; }
            </style>
        </head>
        <body>

            <h2>QuickCare Medical Prescription</h2>

            <div class="section">
                <div class="label">Patient:</div>
                <?= htmlspecialchars($prescription['PAT_FNAME'].' '.$prescription['PAT_LNAME']) ?><br>
                <?= nl2br(htmlspecialchars($prescription['ADDRESS'])) ?>
            </div>

            <div class="section">
                <div class="label">Diagnosis:</div>
                <?= nl2br(htmlspecialchars($prescription['DIAGNOSIS'])) ?>
            </div>

            <div class="section">
                <div class="label">Symptoms:</div>
                <?= nl2br(htmlspecialchars($prescription['SYMPTOMS'])) ?>
            </div>

            <div class="section">
                <div class="label">Medicines:</div>
                <?= $med_html ?>
            </div>

            <p><em>Generated on <?= date('d M Y') ?></em></p>

        </body>
        </html>
        <?php
        echo ob_get_clean();
        exit;
    }
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>View Prescriptions</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">

    <div class="container mt-4">
        <h3 class="mb-4">Prescriptions</h3>

        <?php while ($p = mysqli_fetch_assoc($prescriptions_query)) { ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5>
                        Dr. <?= htmlspecialchars($p['DOC_FNAME'].' '.$p['DOC_LNAME']) ?>
                    </h5>

                    <p><strong>Patient:</strong>
                        <?= htmlspecialchars($p['PAT_FNAME'].' '.$p['PAT_LNAME']) ?>
                    </p>

                    <p><strong>Date:</strong>
                        <?= date('d M Y', strtotime($p['ISSUE_DATE'])) ?>
                    </p>

                    <p><strong>Diagnosis:</strong>
                        <?= htmlspecialchars($p['DIAGNOSIS']) ?>
                    </p>

                    <a href="view_prescription.php?download=<?= $p['PRESCRIPTION_ID'] ?>"
                    class="btn btn-primary btn-sm">
                        Download
                    </a>
                </div>
            </div>
        <?php } ?>

    </div>

    </body>
    </html>
