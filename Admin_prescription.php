<?php
    session_start();
    include 'config.php';
    
    // Check if user is logged in and is an admin
    if (!isset($_SESSION['LOGGED_IN']) || $_SESSION['LOGGED_IN'] !== true || $_SESSION['USER_TYPE'] !== 'admin') {
        header("Location: admin_login.php");
        exit();
    }
    
    // Check authentication
   
  
    // ================= DOWNLOAD SINGLE PRESCRIPTION =================
    // Handle download BEFORE any HTML output (including sidebar)
    if (isset($_POST['download'])) {
        // Clear any output buffers to ensure clean PDF output
        while (ob_get_level()) {
            ob_end_clean();
        }
        
        $prescription_id = intval($_POST['download']);

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
            header("Location: Admin_prescription.php?error=invalid_prescription");
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
        
        // Generate and download PDF - this will exit and output only PDF content
        generatePrescriptionPDF($prescription, $medicines, $conn);
        exit; // Ensure no further output
    }
    
    // Only include sidebar and show page if NOT downloading
    include 'admin_sidebar.php';
    
   

    // ================= SEARCH FUNCTIONALITY =================
    $search_term = '';
    $search_results_info = '';
    
    // Handle search submission
    if (isset($_POST['submit_search']) && !empty($_POST['search'])) {
        $search_term = trim($_POST['search']);
        $search_results_info = "Showing results for: <strong>" . htmlspecialchars($search_term) . "</strong>";
    }
    
    // Handle clear search
    if (isset($_POST['clear_search'])) {
        $search_term = '';
        $search_results_info = '';
    }

    // ================= FETCH AND GROUP PRESCRIPTIONS =================
    $base_query = "
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
    ";
    
    // Add search conditions if search term exists
    if (!empty($search_term)) {
        $search_condition = " WHERE (
            d.FIRST_NAME LIKE '%$search_term%' OR 
            d.LAST_NAME LIKE '%$search_term%' OR 
            CONCAT(d.FIRST_NAME, ' ', d.LAST_NAME) LIKE '%$search_term%' OR
            s.SPECIALISATION_NAME LIKE '%$search_term%' OR
            pat.FIRST_NAME LIKE '%$search_term%' OR 
            pat.LAST_NAME LIKE '%$search_term%' OR 
            CONCAT(pat.FIRST_NAME, ' ', pat.LAST_NAME) LIKE '%$search_term%' OR
            p.DIAGNOSIS LIKE '%$search_term%' OR
            p.PRESCRIPTION_ID LIKE '%$search_term%' OR
            a.APPOINTMENT_DATE LIKE '%$search_term%' OR
            pat.PHONE LIKE '%$search_term%' OR
            pat.EMAIL LIKE '%$search_term%'
        )";
        $prescriptions_query = mysqli_query($conn, $base_query . $search_condition . " ORDER BY d.LAST_NAME, d.FIRST_NAME, pat.LAST_NAME, pat.FIRST_NAME, p.ISSUE_DATE DESC");
    } else {
        $prescriptions_query = mysqli_query($conn, $base_query . " ORDER BY d.LAST_NAME, d.FIRST_NAME, pat.LAST_NAME, pat.FIRST_NAME, p.ISSUE_DATE DESC");
    }

    // Group prescriptions by doctor and then by patient
    $grouped_prescriptions = [];
    while ($p = mysqli_fetch_assoc($prescriptions_query)) {
        $doctor_key = $p['DOC_FNAME'] . ' ' . $p['DOC_LNAME'] . ' (' . $p['SPECIALISATION_NAME'] . ')';
        $patient_key = $p['PAT_FNAME'] . ' ' . $p['PAT_LNAME'];
        
        if (!isset($grouped_prescriptions[$doctor_key])) {
            $grouped_prescriptions[$doctor_key] = [
                'doctor_info' => [
                    'name' => $p['DOC_FNAME'] . ' ' . $p['DOC_LNAME'],
                    'specialization' => $p['SPECIALISATION_NAME'],
                    'education' => $p['EDUCATION'],
                    'phone' => $p['DOC_PHONE'],
                    'email' => $p['DOC_EMAIL']
                ],
                'patients' => []
            ];
        }
        
        if (!isset($grouped_prescriptions[$doctor_key]['patients'][$patient_key])) {
            $grouped_prescriptions[$doctor_key]['patients'][$patient_key] = [
                'patient_info' => [
                    'name' => $p['PAT_FNAME'] . ' ' . $p['PAT_LNAME'],
                    'dob' => $p['DOB'],
                    'gender' => $p['GENDER'],
                    'blood_group' => $p['BLOOD_GROUP'],
                    'phone' => $p['PAT_PHONE'],
                    'email' => $p['PAT_EMAIL']
                ],
                'prescriptions' => []
            ];
        }
        
        $grouped_prescriptions[$doctor_key]['patients'][$patient_key]['prescriptions'][] = $p;
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
                --primary: #1e3a8a;
                --primary-light: #3b82f6;
                --secondary: #06b6d4;
                --success: #10b981;
                --danger: #ef4444;
                --warning: #f59e0b;
                --info: #8b5cf6;
                --dark: #1f2937;
                --light: #f8fafc;
                --gray: #6b7280;
                --border: #e5e7eb;
                --shadow: rgba(0, 0, 0, 0.1);
                --gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                --doctor-bg: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
                --patient-bg: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
                min-height: 100vh;
                color: var(--dark);
            }

            .main-content {
                margin-left: 240px;
                padding: 30px;
                width: calc(100% - 240px);
            }

            /* Header Section */
            .page-header {
                background: white;
                border-radius: 20px;
                padding: 30px;
                margin-bottom: 30px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                position: relative;
                overflow: hidden;
            }

            .page-header::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 5px;
                background: var(--gradient);
            }

            .page-title {
                font-size: 2rem;
                font-weight: 700;
                color: var(--primary);
                margin-bottom: 10px;
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .page-title i {
                font-size: 1.8rem;
                color: var(--primary-light);
            }

            .page-subtitle {
                color: var(--gray);
                font-size: 1.1rem;
            }

            /* Search Section */
            .search-section {
                background: white;
                border-radius: 20px;
                padding: 30px;
                margin-bottom: 30px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            .search-header {
                display: flex;
                align-items: center;
                gap: 15px;
                margin-bottom: 25px;
            }

            .search-header i {
                font-size: 1.5rem;
                color: var(--primary-light);
            }

            .search-header h2 {
                font-size: 1.5rem;
                font-weight: 600;
                color: var(--dark);
            }

            .search-form {
                display: flex;
                gap: 15px;
                align-items: stretch;
            }

            .search-input-group {
                flex: 1;
                position: relative;
            }

            .search-input {
                width: 100%;
                padding: 15px 20px 15px 50px;
                border: 2px solid var(--border);
                border-radius: 12px;
                font-size: 1rem;
                transition: all 0.3s ease;
                background: #f8fafc;
            }

            .search-input:focus {
                outline: none;
                border-color: var(--primary-light);
                background: white;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            .search-input-icon {
                position: absolute;
                left: 20px;
                top: 50%;
                transform: translateY(-50%);
                color: var(--gray);
                font-size: 1.1rem;
            }

            .search-btn {
                padding: 15px 30px;
                background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
                color: white;
                border: none;
                border-radius: 12px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
                box-shadow: 0 5px 15px rgba(30, 58, 138, 0.3);
            }

            .search-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 25px rgba(30, 58, 138, 0.4);
            }

            .clear-btn {
                padding: 15px 25px;
                background: #f3f4f6;
                color: var(--gray);
                border: 2px solid var(--border);
                border-radius: 12px;
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .clear-btn:hover {
                background: #e5e7eb;
                border-color: var(--gray);
            }

            .search-filters {
                display: flex;
                gap: 10px;
                margin-top: 15px;
                flex-wrap: wrap;
            }

            .filter-chip {
                padding: 8px 16px;
                background: #f0f9ff;
                color: var(--primary);
                border: 1px solid var(--primary-light);
                border-radius: 20px;
                font-size: 0.85rem;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .filter-chip:hover {
                background: var(--primary-light);
                color: white;
            }

            .filter-chip.active {
                background: var(--primary);
                color: white;
                border-color: var(--primary);
            }

            .search-results-info {
                margin-top: 20px;
                padding: 15px;
                background: #f0f9ff;
                border-radius: 10px;
                border-left: 4px solid var(--primary-light);
                display: none;
            }

            .search-results-info.show {
                display: block;
            }

            .search-results-text {
                color: var(--dark);
                font-weight: 500;
            }

            /* Doctor Cards Container */
            .doctors-container {
                display: flex;
                flex-direction: column;
                gap: 30px;
            }

            /* Doctor Card */
            .doctor-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                transition: all 0.3s ease;
            }

            .doctor-card:hover {
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            }

            .doctor-header {
                background: var(--doctor-bg);
                padding: 25px 30px;
                position: relative;
            }

            .doctor-info {
                display: flex;
                align-items: center;
                gap: 20px;
            }

            .doctor-avatar {
                width: 80px;
                height: 80px;
                border-radius: 50%;
                background: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2rem;
                color: var(--primary);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            }

            .doctor-details h2 {
                color: white;
                font-size: 1.8rem;
                font-weight: 600;
                margin-bottom: 8px;
            }

            .doctor-details .specialization {
                color: rgba(255, 255, 255, 0.9);
                font-size: 1.1rem;
                margin-bottom: 5px;
            }

            .doctor-details .education {
                color: rgba(255, 255, 255, 0.8);
                font-size: 0.95rem;
            }

            .doctor-stats {
                display: flex;
                gap: 30px;
                margin-top: 15px;
                padding-top: 15px;
                border-top: 1px solid rgba(255, 255, 255, 0.2);
            }

            .doctor-stat {
                display: flex;
                align-items: center;
                gap: 8px;
                color: rgba(255, 255, 255, 0.9);
            }

            .doctor-stat i {
                font-size: 1.1rem;
            }

            .doctor-stat span {
                font-weight: 600;
            }

            /* Patients Section */
            .patients-section {
                padding: 30px;
                background: #fafbfc;
            }

            .section-title {
                font-size: 1.3rem;
                font-weight: 600;
                color: var(--dark);
                margin-bottom: 20px;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .section-title i {
                color: var(--primary-light);
            }

            .patients-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
                gap: 20px;
            }

            /* Patient Card */
            .patient-card {
                background: white;
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
                transition: all 0.3s ease;
                border: 1px solid var(--border);
            }

            .patient-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
            }

            .patient-header {
                background: var(--patient-bg);
                padding: 20px;
                border-bottom: 1px solid var(--border);
            }

            .patient-info {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            .patient-avatar {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: var(--primary);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
                color: white;
            }

            .patient-details h3 {
                color: var(--dark);
                font-size: 1.2rem;
                font-weight: 600;
                margin-bottom: 5px;
            }

            .patient-meta {
                display: flex;
                gap: 15px;
                font-size: 0.85rem;
                color: var(--gray);
            }

            .patient-meta span {
                display: flex;
                align-items: center;
                gap: 5px;
            }

            /* Prescriptions List */
            .prescriptions-list {
                padding: 20px;
            }

            .prescription-item {
                background: #f8fafc;
                border-radius: 10px;
                padding: 15px;
                margin-bottom: 15px;
                border-left: 4px solid var(--primary-light);
                transition: all 0.3s ease;
            }

            .prescription-item:hover {
                background: #f1f5f9;
                transform: translateX(5px);
            }

            .prescription-item:last-child {
                margin-bottom: 0;
            }

            .prescription-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
            }

            .prescription-date {
                display: flex;
                align-items: center;
                gap: 8px;
                color: var(--gray);
                font-size: 0.9rem;
            }

            .prescription-id {
                background: var(--primary);
                color: white;
                padding: 4px 10px;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 600;
            }

            .diagnosis-text {
                color: var(--dark);
                font-size: 0.95rem;
                line-height: 1.5;
                margin-bottom: 10px;
            }

            .appointment-info {
                display: flex;
                align-items: center;
                gap: 8px;
                color: var(--gray);
                font-size: 0.85rem;
            }

            .prescription-actions {
                display: flex;
                justify-content: flex-end;
                margin-top: 10px;
            }

            .btn-download {
                background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 8px;
                font-weight: 600;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 0.85rem;
                box-shadow: 0 3px 10px rgba(16, 185, 129, 0.3);
            }

            .btn-download:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
                background: linear-gradient(135deg, #059669 0%, #047857 100%);
            }

            /* Empty State */
            .empty-state {
                background: white;
                border-radius: 20px;
                padding: 60px 30px;
                text-align: center;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            .empty-state i {
                font-size: 4rem;
                color: var(--gray);
                margin-bottom: 20px;
                opacity: 0.5;
            }

            .empty-state h3 {
                color: var(--dark);
                font-size: 1.5rem;
                margin-bottom: 10px;
            }

            .empty-state p {
                color: var(--gray);
                font-size: 1rem;
            }

            /* Responsive Design */
            @media (max-width: 1200px) {
                .patients-grid {
                    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
                }
            }

            @media (max-width: 768px) {
                .main-content {
                    margin-left: 0;
                    width: 100%;
                    padding: 20px;
                }

                .page-title {
                    font-size: 1.5rem;
                }

                .patients-grid {
                    grid-template-columns: 1fr;
                }

                .search-form {
                    flex-direction: column;
                }

                .search-filters {
                    flex-wrap: wrap;
                }

                .doctor-info {
                    flex-direction: column;
                    text-align: center;
                }

                .doctor-stats {
                    justify-content: center;
                }
            }

            /* Animations */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .doctor-card {
                animation: fadeInUp 0.5s ease forwards;
            }

            .doctor-card:nth-child(1) { animation-delay: 0.1s; }
            .doctor-card:nth-child(2) { animation-delay: 0.2s; }
            .doctor-card:nth-child(3) { animation-delay: 0.3s; }
        </style>
    </head>
    <body>
        <!-- Main Content -->
        <div class="main-content">
            <?php include 'Admin_header.php'; ?>

            <!-- Page Header -->
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-file-medical"></i>
                    Prescription Management
                </h1>
                <p class="page-subtitle">View and download patient prescriptions organized by doctor</p>
            </div>

            <!-- Search Section -->
            <!-- <div class="search-section">
                <div class="search-header">
                    <i class="fas fa-search"></i>
                    <h2>Search Prescriptions</h2>
                </div>
                <form method="POST" action="view_prescription.php" id="searchForm" class="search-form">
                    <div class="search-input-group">
                        <i class="fas fa-search search-input-icon"></i>
                        <input type="text" 
                               name="search" 
                               id="searchInput" 
                               class="search-input" 
                               placeholder="Search by doctor name, patient name, diagnosis, prescription ID..."
                               value="<?= htmlspecialchars($search_term) ?>">
                    </div>
                    <button type="submit" name="submit_search" class="search-btn">
                        <i class="fas fa-search"></i>
                        Search
                    </button>
                    <button type="submit" name="clear_search" class="clear-btn">
                        <i class="fas fa-times"></i>
                        Clear
                    </button>
                </form>
                <div class="search-filters">
                    <span class="filter-chip" onclick="quickSearch('Dr.')">Doctors</span>
                    <span class="filter-chip" onclick="quickSearch('Diabetes')">Diabetes</span>
                    <span class="filter-chip" onclick="quickSearch('Hypertension')">Hypertension</span>
                    <span class="filter-chip" onclick="quickSearch('Cardiology')">Cardiology</span>
                    <span class="filter-chip" onclick="quickSearch('Pediatrics')">Pediatrics</span>
                </div>
                <?php if (!empty($search_results_info)): ?>
                    <div class="search-results-info show">
                        <div class="search-results-text">
                            <?= $search_results_info ?> - Found <?= count($grouped_prescriptions) ?> doctor(s)
                        </div>
                    </div>
                <?php endif; ?>
            </div> -->

            <!-- Doctors Container -->
            <?php if (!empty($grouped_prescriptions)): ?>
                <div class="doctors-container">
                    <?php foreach ($grouped_prescriptions as $doctor_key => $doctor_data): ?>
                        <div class="doctor-card">
                            <!-- Doctor Header -->
                            <div class="doctor-header">
                                <div class="doctor-info">
                                    <div class="doctor-avatar">
                                        <i class="fas fa-user-md"></i>
                                    </div>
                                    <div class="doctor-details">
                                        <h2><?= htmlspecialchars($doctor_data['doctor_info']['name']) ?></h2>
                                        <div class="specialization"><?= htmlspecialchars($doctor_data['doctor_info']['specialization']) ?></div>
                                        <div class="education"><?= htmlspecialchars($doctor_data['doctor_info']['education']) ?></div>
                                    </div>
                                </div>
                                <div class="doctor-stats">
                                    <div class="doctor-stat">
                                        <i class="fas fa-users"></i>
                                        <span><?= count($doctor_data['patients']) ?> Patients</span>
                                    </div>
                                    <div class="doctor-stat">
                                        <i class="fas fa-file-prescription"></i>
                                        <span>
                                            <?php 
                                                $doc_prescriptions = 0;
                                                foreach ($doctor_data['patients'] as $patient) {
                                                    $doc_prescriptions += count($patient['prescriptions']);
                                                }
                                                echo $doc_prescriptions;
                                            ?> Prescriptions
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Patients Section -->
                            <div class="patients-section">
                                <h3 class="section-title">
                                    <i class="fas fa-users"></i>
                                    Patients under Dr. <?= htmlspecialchars($doctor_data['doctor_info']['name']) ?>
                                </h3>
                                <div class="patients-grid">
                                    <?php foreach ($doctor_data['patients'] as $patient_key => $patient_data): ?>
                                        <div class="patient-card">
                                            <!-- Patient Header -->
                                            <div class="patient-header">
                                                <div class="patient-info">
                                                    <div class="patient-avatar">
                                                        <i class="fas fa-user"></i>
                                                    </div>
                                                    <div class="patient-details">
                                                        <h3><?= htmlspecialchars($patient_data['patient_info']['name']) ?></h3>
                                                        <div class="patient-meta">
                                                            <span><i class="fas fa-birthday-cake"></i> <?= date('M d, Y', strtotime($patient_data['patient_info']['dob'])) ?></span>
                                                            <span><i class="fas fa-venus-mars"></i> <?= htmlspecialchars($patient_data['patient_info']['gender']) ?></span>
                                                            <span><i class="fas fa-tint"></i> <?= htmlspecialchars($patient_data['patient_info']['blood_group']) ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Prescriptions List -->
                                            <div class="prescriptions-list">
                                                <?php foreach ($patient_data['prescriptions'] as $prescription): ?>
                                                    <div class="prescription-item">
                                                        <div class="prescription-header">
                                                            <div class="prescription-date">
                                                                <i class="fas fa-calendar"></i>
                                                                <?= date('M d, Y', strtotime($prescription['ISSUE_DATE'])) ?>
                                                            </div>
                                                            <div class="prescription-id">
                                                                #<?= str_pad($prescription['PRESCRIPTION_ID'], 5, '0', STR_PAD_LEFT) ?>
                                                            </div>
                                                        </div>
                                                        <div class="diagnosis-text">
                                                            <strong>Diagnosis:</strong> <?= htmlspecialchars($prescription['DIAGNOSIS']) ?>
                                                        </div>
                                                        <div class="appointment-info">
                                                            <i class="fas fa-clock"></i>
                                                            Appointment: <?= date('M d, Y', strtotime($prescription['APPOINTMENT_DATE'])) ?> at <?= $prescription['APPOINTMENT_TIME'] ?>
                                                        </div>
                                                        <div class="prescription-actions">
                                                            <form method="POST" action="Admin_prescription.php" style="display:inline">
                                                                <input type="hidden" name="download" value="<?= $prescription['PRESCRIPTION_ID'] ?>">
                                                                <button type="submit" class="btn-download">
                                                                    <i class="fas fa-download"></i>
                                                                    Download
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3><?= !empty($search_term) ? 'No Results Found' : 'No Prescriptions Found' ?></h3>
                    <p><?= !empty($search_term) ? 'No prescriptions match your search criteria. Try different keywords.' : 'There are no prescriptions in the system at this time.' ?></p>
                </div>
            <?php endif; ?>
        </div>

        <script>
            function quickSearch(term) {
                document.getElementById('searchInput').value = term;
                document.getElementById('searchForm').submit();
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Real-time search feedback
                let searchTimeout;
                document.getElementById('searchInput').addEventListener('input', function(e) {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        console.log('Searching for:', e.target.value);
                    }, 500);
                });

                // Filter chips interaction
                document.querySelectorAll('.filter-chip').forEach(chip => {
                    chip.addEventListener('click', function() {
                        // Visual feedback
                        this.style.transform = 'scale(0.95)';
                        setTimeout(() => {
                            this.style.transform = 'scale(1)';
                        }, 100);
                    });
                });
            });
        </script>
    </body>
    </html>Ad