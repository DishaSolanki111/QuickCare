<?php
    session_start();
    include 'config.php';
    include 'recept_sidebar.php';
    if (!isset($_SESSION['RECEPTIONIST_ID'])) {
        header("Location: login.php");
        exit;
    }
  
    $receptionist_id = $_SESSION['RECEPTIONIST_ID'];
    
    // Get receptionist information for the welcome message
    $receptionist_query = mysqli_query($conn, "SELECT * FROM receptionist_tbl WHERE RECEPTIONIST_ID = $receptionist_id");
    $receptionist = mysqli_fetch_assoc($receptionist_query);

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
    if (isset($_POST['download']) || isset($_GET['download'])) {
        $prescription_id = isset($_POST['download']) ? intval($_POST['download']) : intval($_GET['download']);

        $detail_q = mysqli_query($conn, "
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
            WHERE p.PRESCRIPTION_ID = $prescription_id
        ");

        if (mysqli_num_rows($detail_q) === 0) {
            header("Location: view_prescription.php?error=invalid_prescription");
            exit;
        }

        $prescription = mysqli_fetch_assoc($detail_q);

        // Get medicines for this prescription
        $med_q = mysqli_query($conn, "
            SELECT m.MED_NAME, pm.DOSAGE, pm.FREQUENCY, pm.DURATION
            FROM prescription_medicine_tbl pm
            JOIN medicine_tbl m ON pm.MEDICINE_ID = m.MEDICINE_ID
            WHERE pm.PRESCRIPTION_ID = $prescription_id
        ");

        // Fetch all medicines into an array
        $medicines = array();
        while ($m = mysqli_fetch_assoc($med_q)) {
            $medicines[] = $m;
        }

        // Include PDF generator
        require_once 'generate_prescription_pdf.php';
        
        // Generate and download PDF
        generatePrescriptionPDF($prescription, $medicines, $conn);
    }
    ?>

    <!DOCTYPE html>
    <html>
    <head>
        <title>View Prescriptions</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root {
                --primary-color: #064469;
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
            }

            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #f5f7fa;
                color: var(--dark-color);
                margin: 0;
                padding: 0;
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

            .prescriptions-container {
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
            }

            .prescription-card {
                background: white;
                border-radius: 10px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                overflow: hidden;
                transition: transform 0.2s, box-shadow 0.2s;
                flex: 1 1 calc(50% - 15px);
                min-width: 300px;
                max-width: calc(50% - 15px);
            }

            .prescription-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 6px 15px rgba(0,0,0,0.15);
            }

            .card-header-custom {
                background: linear-gradient(135deg, var(--mid-blue) 0%, var(--soft-blue) 100%);
                color: white;
                padding: 12px 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .doctor-name {
                font-weight: 600;
                font-size: 1.1rem;
                margin: 0;
            }

            .specialization {
                font-size: 0.9rem;
                opacity: 0.9;
            }

            .card-body-custom {
                padding: 20px;
            }

            .info-row {
                display: flex;
                margin-bottom: 10px;
            }

            .info-label {
                font-weight: 600;
                color: var(--primary-color);
                width: 100px;
                flex-shrink: 0;
            }

            .info-value {
                color: var(--dark-color);
            }

            .diagnosis-box {
                background-color: #f0f8ff;
                border-left: 4px solid var(--primary-color);
                padding: 10px 15px;
                margin: 15px 0;
                border-radius: 0 5px 5px 0;
            }

            .btn-download {
                background: var(--primary-color);
                color: white;
                border: none;
                padding: 8px 15px;
                border-radius: 5px;
                font-size: 0.9rem;
                transition: background 0.3s;
                display: inline-flex;
                align-items: center;
            }

            .btn-download:hover {
                background: var(--dark-blue);
                color: white;
            }

            .btn-close {
                background: transparent;
                border: none;
                color: white;
                font-size: 1.2rem;
                cursor: pointer;
                transition: transform 0.3s;
            }

            .btn-close:hover {
                transform: rotate(90deg);
            }

            .no-prescriptions {
                text-align: center;
                padding: 40px 20px;
                background: white;
                border-radius: 10px;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1);
                color: var(--primary-color);
            }

            .no-prescriptions i {
                font-size: 3rem;
                margin-bottom: 15px;
            }

            @media (max-width: 768px) {
                .main-content {
                    margin-left: 0;
                    width: 100%;
                }
                
                .prescription-card {
                    flex: 1 1 100%;
                    max-width: 100%;
                }
            }
        </style>
    </head>
    <body>
        <!-- Main Content -->
        <div class="main-content">
            <div class="topbar">
                <h1>View Prescriptions</h1>
                <p>Welcome, <?php echo htmlspecialchars($receptionist['FIRST_NAME'] . ' ' . $receptionist['LAST_NAME']); ?></p>
            </div>

            <div class="container-fluid p-0">
                <?php if (mysqli_num_rows($prescriptions_query) > 0): ?>
                    <div class="prescriptions-container">
                        <?php while ($p = mysqli_fetch_assoc($prescriptions_query)) { ?>
                            <div class="prescription-card">
                                <div class="card-header-custom">
                                    <div>
                                        <h5 class="doctor-name">Dr. <?= htmlspecialchars($p['DOC_FNAME'].' '.$p['DOC_LNAME']) ?></h5>
                                        <div class="specialization"><?= htmlspecialchars($p['SPECIALISATION_NAME']) ?></div>
                                    </div>
                                    <button class="btn-close" aria-label="Close">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="card-body-custom">
                                    <div class="info-row">
                                        <div class="info-label">Patient:</div>
                                        <div class="info-value"><?= htmlspecialchars($p['PAT_FNAME'].' '.$p['PAT_LNAME']) ?></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Date:</div>
                                        <div class="info-value"><?= date('d M Y', strtotime($p['ISSUE_DATE'])) ?></div>
                                    </div>
                                    <div class="info-row">
                                        <div class="info-label">Appointment:</div>
                                        <div class="info-value"><?= date('d M Y', strtotime($p['APPOINTMENT_DATE'])) ?> at <?= $p['APPOINTMENT_TIME'] ?></div>
                                    </div>
                                    
                                    <div class="diagnosis-box">
                                        <div class="info-label">Diagnosis:</div>
                                        <div class="info-value"><?= htmlspecialchars($p['DIAGNOSIS']) ?></div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <form method="POST" action="view_prescription.php" style="display:inline">
                                        <input type="hidden" name="download" value="<?= $p['PRESCRIPTION_ID'] ?>">
                                        <button type="submit" class="btn-download"><i class="fas fa-download me-1"></i> Download Prescription</button>
                                    </form>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php else: ?>
                    <div class="no-prescriptions">
                        <i class="fas fa-file-medical"></i>
                        <h4>No Prescriptions Found</h4>
                        <p>There are no prescriptions in the system at this time.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Add functionality to close buttons
                const closeButtons = document.querySelectorAll('.btn-close');
                closeButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const card = this.closest('.prescription-card');
                        card.style.transition = 'opacity 0.3s, transform 0.3s';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        
                        setTimeout(() => {
                            card.remove();
                            
                            // Check if there are any cards left
                            const remainingCards = document.querySelectorAll('.prescription-card');
                            if (remainingCards.length === 0) {
                                document.querySelector('.container-fluid').innerHTML = `
                                    <div class="no-prescriptions">
                                        <i class="fas fa-file-medical"></i>
                                        <h4>No Prescriptions Found</h4>
                                        <p>There are no prescriptions in the system at this time.</p>
                                    </div>
                                `;
                            }
                        }, 300);
                    });
                });
            });
        </script>
    </body>
    </html>