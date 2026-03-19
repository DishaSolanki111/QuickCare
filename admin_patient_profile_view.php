<?php
session_start();
include 'config.php';

// Access control: only admin can view patient details
if (!isset($_SESSION['LOGGED_IN']) || $_SESSION['LOGGED_IN'] !== true || ($_SESSION['USER_TYPE'] ?? '') !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

$patient_id = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : 0;
if ($patient_id <= 0) {
    die("Invalid patient.");
}

$stmt = $conn->prepare("
    SELECT
        PATIENT_ID,
        FIRST_NAME,
        LAST_NAME,
        USERNAME,
        DOB,
        GENDER,
        BLOOD_GROUP,
        PHONE,
        EMAIL,
        ADDRESS
    FROM patient_tbl
    WHERE PATIENT_ID = ?
");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();
$stmt->close();

if (!$patient) {
    die("Patient not found.");
}

$fullName = $patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Profile View - <?php echo htmlspecialchars($fullName); ?></title>
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
            padding: 20px 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .sheet {
            width: 794px;
            max-width: 100%;
            background: #ffffff;
            box-shadow: 0 10px 25px rgba(15, 23, 42, 0.25);
            border-radius: 10px;
            padding: 24px 28px 28px;
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

        .profile-main-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #0f172a;
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
    </style>
</head>
<body>
    <div class="sheet">
        <div class="sheet-header"></div>

        <div class="profile-header">
            <div class="avatar-lg">
                <?php echo strtoupper(substr($patient['FIRST_NAME'], 0, 1) . substr($patient['LAST_NAME'], 0, 1)); ?>
            </div>
            <div>
                <div class="profile-main-name"><?php echo htmlspecialchars($fullName); ?></div>
                <div class="profile-id" style="margin-top:4px;color:#64748b;font-size:0.85rem;">
                    Username: <?php echo htmlspecialchars($patient['USERNAME']); ?>
                </div>
            </div>
        </div>

        <div class="section-title">Personal Information</div>
        <div class="grid-2">
            <div>
                <div class="field-label">First Name</div>
                <div class="field-value"><?php echo htmlspecialchars($patient['FIRST_NAME']); ?></div>
            </div>
            <div>
                <div class="field-label">Last Name</div>
                <div class="field-value"><?php echo htmlspecialchars($patient['LAST_NAME']); ?></div>
            </div>
            <div>
                <div class="field-label">Date of Birth</div>
                <div class="field-value"><?php echo htmlspecialchars($patient['DOB']); ?></div>
            </div>
            <div>
                <div class="field-label">Gender</div>
                <div class="field-value"><?php echo htmlspecialchars($patient['GENDER']); ?></div>
            </div>
            <div>
                <div class="field-label">Blood Group</div>
                <div class="field-value"><?php echo htmlspecialchars($patient['BLOOD_GROUP']); ?></div>
            </div>
            <div>
                <div class="field-label">Phone</div>
                <div class="field-value"><?php echo htmlspecialchars($patient['PHONE']); ?></div>
            </div>
        </div>

        <div class="section-title" style="margin-top:16px;">Contact Information</div>
        <div class="grid-2">
            <div>
                <div class="field-label">Email</div>
                <div class="field-value"><?php echo htmlspecialchars($patient['EMAIL']); ?></div>
            </div>
            <div>
                <div class="field-label">Address</div>
                <div class="field-value"><?php echo nl2br(htmlspecialchars($patient['ADDRESS'])); ?></div>
            </div>
        </div>

        <div class="footer-actions">
            <a class="btn-back" href="Admin_patient.php">
                <i class="fas fa-arrow-left"></i> Back to Patients
            </a>
        </div>
    </div>
</body>
</html>

