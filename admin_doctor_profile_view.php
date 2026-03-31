<?php
session_start();
include 'config.php';

// Access control: only admin can view doctor details PDF
if (!isset($_SESSION['LOGGED_IN']) || $_SESSION['LOGGED_IN'] !== true || ($_SESSION['USER_TYPE'] ?? '') !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

include 'admin_sidebar.php';
$adminName = isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : 'Admin';

$doctor_id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doctor_id'])) {
    $doctor_id = (int)$_POST['doctor_id'];
} elseif (isset($_GET['doctor_id'])) {
    // fallback if GET used accidentally
    $doctor_id = (int)$_GET['doctor_id'];
}
if ($doctor_id <= 0) {
    die("Invalid doctor.");
}

$doctor_stmt = $conn->prepare("
    SELECT d.DOCTOR_ID, d.FIRST_NAME, d.LAST_NAME, d.DOB, d.DOJ, d.GENDER, d.EDUCATION, d.PHONE, d.EMAIL, d.PROFILE_IMAGE,
           s.SPECIALISATION_NAME, s.SPECIALISATION_ID
    FROM doctor_tbl d
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE d.DOCTOR_ID = ?
");
$doctor_stmt->bind_param("i", $doctor_id);
$doctor_stmt->execute();
$result = $doctor_stmt->get_result();
$doctor = $result->fetch_assoc();
$doctor_stmt->close();

if (!$doctor) {
    die("Doctor not found.");
}

$doctor_name = $doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME'];
$img = !empty($doctor['PROFILE_IMAGE']) ? $doctor['PROFILE_IMAGE'] : 'uploads/default_doctor.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Doctor Profile PDF View - Dr. <?php echo htmlspecialchars($doctor_name); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        body {
    background: #e5edf5;
    min-height: 100vh;
    margin: 0;
    padding: 0;
}

.main {
    margin-left: 240px; /* same as sidebar width */
    width: calc(100% - 240px);
    min-height: 100vh;
    padding: 20px;
    box-sizing: border-box;
}
        .sheet {
    width: 100%;
    background: #ffffff;
    border: 1px solid #d9dee5;
    border-radius: 12px;
    padding: 24px 28px 28px;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
    margin-top: 18px;
}

        .sheet-header {
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 18px;
        }

        .avatar-lg {
            width: 70px;
            height: 70px;
            border-radius: 999px;
            background: #e0f2fe;
            border: 2px solid #bfdbfe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            font-weight: 700;
            color: #1d4ed8;
            overflow: hidden;
            flex-shrink: 0;
        }

        .avatar-lg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-main-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0f172a;
        }

        .profile-id {
            font-size: 0.85rem;
            color: #64748b;
            margin-top: 4px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 32px;
            font-size: 0.86rem;
        }

        .field-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #000000;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 2px;
        }

        .field-value {
            color: #111827;
            line-height: 1.35;
        }

        .section-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: #0f172a;
            margin-top: 14px;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e2e8f0;
        }

        .footer-actions {
            margin-top: 18px;
            display: flex;
            justify-content: flex-start;
        }

        .btn-back {
            padding: 7px 16px;
            border-radius: 999px;
            border: 1px solid #cbd5f5;
            background: #f1f5f9;
            color: #1e293b;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .sheet {
                box-shadow: none;
                border-radius: 0;
                width: auto;
                margin-top: 0;
            }
            .footer-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    
    <div class="main">
        <?php include 'admin_header.php'; ?>
        <div class="sheet">
            <div class="sheet-header"></div>

            <div class="profile-header">
                <div class="avatar-lg">
                    <img src="<?php echo htmlspecialchars($img); ?>" alt="Doctor">
                </div>
                <div>
                    <div class="profile-main-name">Dr. <?php echo htmlspecialchars($doctor_name); ?></div>
                    <div class="profile-id">Specialization: <?php echo htmlspecialchars($doctor['SPECIALISATION_NAME']); ?></div>
                </div>
            </div>

            <div class="section-title">Personal Information</div>
            <div class="grid-2">
                <div>
                    <div class="field-label">Date of Birth</div>
                    <div class="field-value"><?php echo htmlspecialchars($doctor['DOB']); ?></div>
                </div>
                <div>
                    <div class="field-label">Gender</div>
                    <div class="field-value"><?php echo htmlspecialchars($doctor['GENDER']); ?></div>
                </div>
                <div>
                    <div class="field-label">Date of Joining</div>
                    <div class="field-value"><?php echo htmlspecialchars($doctor['DOJ']); ?></div>
                </div>
                <div>
                    <div class="field-label">Education</div>
                    <div class="field-value"><?php echo htmlspecialchars($doctor['EDUCATION']); ?></div>
                </div>
            </div>

            <div class="section-title" style="margin-top:16px;">Contact Information</div>
            <div class="grid-2">
                <div>
                    <div class="field-label">Phone</div>
                    <div class="field-value"><?php echo htmlspecialchars($doctor['PHONE']); ?></div>
                </div>
                <div>
                    <div class="field-label">Email</div>
                    <div class="field-value"><?php echo htmlspecialchars($doctor['EMAIL']); ?></div>
                </div>
            </div>

            <div class="footer-actions">
                <a class="btn-back" href="Admin_doctor.php">
                    <i class="fas fa-arrow-left"></i> Back to Doctors
                </a>
            </div>

        </div>
    </div>
</body>
</html>
