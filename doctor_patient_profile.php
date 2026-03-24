<?php
session_start();
include 'config.php';

// Logged-in doctor
$doctor_id = isset($_SESSION['DOCTOR_ID']) ? (int)$_SESSION['DOCTOR_ID'] : 0;

// Optional patient name filter
$search = isset($_POST['q']) ? trim($_POST['q']) : '';

// Only show patients who have appointments with this doctor
$sql = "
    SELECT 
        p.PATIENT_ID,
        p.FIRST_NAME,
        p.LAST_NAME,
        p.DOB,
        p.PHONE,
        MAX(a.APPOINTMENT_DATE) AS LAST_VISIT
    FROM appointment_tbl a
    JOIN patient_tbl p ON p.PATIENT_ID = a.PATIENT_ID
    WHERE a.DOCTOR_ID = {$doctor_id}
";
if ($search !== '') {
    $esc = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (p.FIRST_NAME LIKE '%$esc%' 
              OR p.LAST_NAME LIKE '%$esc%' 
              OR CONCAT(p.FIRST_NAME,' ',p.LAST_NAME) LIKE '%$esc%')";
}
$sql .= " GROUP BY p.PATIENT_ID, p.FIRST_NAME, p.LAST_NAME, p.DOB, p.PHONE
          ORDER BY p.FIRST_NAME, p.LAST_NAME";
$patients_result = $doctor_id > 0 ? mysqli_query($conn, $sql) : false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profiles - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-body: #F5F7FA;
            --bg-card: #ffffff;
            --border-soft: #e2e8f0;
            --navy: #001F3F;
            --text-main: #001F3F;
            --text-muted: #6b7280;
            --teal-soft: #3A86FF;
            --emerald: #2ECC71;
            --red-strong: #C0392B;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        body {
            min-height: 100vh;
            background-color: var(--bg-body);
            color: var(--text-main);
            display: flex;
        }

        .page-shell {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
        }

        @media (max-width: 992px) {
            .main-content {
                margin-left: 70px;
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
            }
        }

        /* Main content area */
        .content-wrap {
            padding: 20px 32px 28px;
        }

        /* Filter/Search Panel */
        .filter-panel {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 14px 18px;
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
        }

        .filter-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #0f766e;
            margin-bottom: 8px;
        }

        .filter-row {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .filter-input {
            flex: 1;
            background: #ffffff;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 8px 10px;
            font-size: 0.9rem;
            color: var(--text-main);
        }

        .filter-input::placeholder {
            color: #9ca3af;
        }

        .btn {
            border-radius: 999px;
            padding: 8px 18px;
            border: 1px solid transparent;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.18s ease;
        }

        .btn-search {
            background: var(--navy);
            color: #ffffff;
        }

        .btn-search:hover {
            background: #012552;
        }

        .btn-reset {
            background: #ffffff;
            color: var(--red-strong);
            border-color: var(--red-strong);
        }

        .btn-reset:hover {
            background: #fee2e2;
        }

        /* Cards grid */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 18px;
        }

        .patient-card {
            background-color: var(--bg-card);
            border-radius: 14px;
            border: 1px solid var(--border-soft);
            padding: 18px 18px 16px;
            display: flex;
            flex-direction: column;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
        }

        .card-top {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 10px;
        }

        .avatar-circle {
            width: 58px;
            height: 58px;
            border-radius: 999px;
            background: var(--navy);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 26px;
            margin-bottom: 8px;
        }

        .patient-name {
            font-size: 1rem;
            font-weight: 700;
            color: var(--navy);
            text-align: center;
        }

        .primary-contact {
            margin-top: 4px;
            font-size: 0.85rem;
            color: var(--navy);
            font-weight: 500;
            text-align: center;
        }

        .card-details {
            margin-top: 10px;
            font-size: 0.85rem;
        }

        .detail-row {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #4b5563;
            margin-bottom: 4px;
        }

        .detail-row i {
            color: var(--navy);
            font-size: 0.85rem;
        }

        .detail-label {
            font-weight: 600;
            margin-right: 2px;
        }

        .card-actions {
            margin-top: 12px;
        }

        .btn-view-profile {
            width: 100%;
            justify-content: center;
            background: var(--emerald);
            border-color: var(--emerald);
            color: #ffffff;
            padding: 9px 16px;
        }

        .btn-view-profile:hover {
            background: #27ae60;
        }

        @media (max-width: 768px) {
            .content-wrap {
                padding: 16px 16px 24px;
            }

            .filter-row {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <?php include 'doctor_sidebar.php'; ?>
    <div class="main-content">
        <?php $page_title = 'Patient Profiles'; include 'doctor_header.php'; ?>
        <div class="page-shell">
        <div class="content-wrap">
            <!-- Filter/Search Panel -->
            <div class="filter-panel">
                <div class="filter-label">Filter by Patient:</div>
                <form id="patientSearchForm" method="post">
                    <div class="filter-row">
                        <input
                            type="text"
                            class="filter-input"
                            name="q"
                            value="<?php echo htmlspecialchars($search ?? ''); ?>"
                            placeholder="Search All Recent Patients">
                        <button class="btn btn-search" type="submit">
                            <i class="fas fa-search"></i> Search
                        </button>
                        <button class="btn btn-reset" type="button" onclick="window.location.href='doctor_patient_profile.php'">
                            Reset
                        </button>
                    </div>
                </form>
            </div>

            <!-- Patient Cards Grid -->
            <div class="cards-grid">
            <?php if ($patients_result && mysqli_num_rows($patients_result) > 0): ?>
                <?php while ($p = mysqli_fetch_assoc($patients_result)): ?>
                    <?php
                        $fullName = $p['FIRST_NAME'] . ' ' . $p['LAST_NAME'];
                        $initials = strtoupper(substr($p['FIRST_NAME'], 0, 1) . substr($p['LAST_NAME'], 0, 1));
                    ?>
                    <div class="patient-card">
                        <div class="card-top">
                            <div class="avatar-circle">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="patient-name"><?php echo htmlspecialchars($fullName); ?></div>
                        </div>
                        <div class="card-details">
                            <div class="detail-row">
                                <i class="fas fa-calendar-alt"></i>
                                <span class="detail-label">DOB:</span>
                                <span><?php echo htmlspecialchars($p['DOB']); ?></span>
                            </div>
                            <div class="detail-row">
                                <i class="fas fa-phone-alt"></i>
                                <span class="detail-label">Last Visit:</span>
                                <span>
                                    <?php echo $p['LAST_VISIT'] ? htmlspecialchars($p['LAST_VISIT']) : 'No visits yet'; ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-actions">
                            <form method="POST" action="doctor_patient_profile_view.php" style="display:inline;">
                                <input type="hidden" name="patient_id" value="<?php echo (int)$p['PATIENT_ID']; ?>">
                                <button type="submit" class="btn btn-view-profile">
                                    <i class="fas fa-user-circle"></i> View Patient Profile
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div style="grid-column:1/-1;text-align:center;color:var(--text-muted);padding:24px 8px;">
                    No patients found.
                </div>
            <?php endif; ?>
            </div>
        </div>
        </div>
    </div>
</body>
</html>

