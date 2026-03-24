<?php
session_start();
include 'config.php';
// Access control: only admin can view receptionist details
if (!isset($_SESSION['LOGGED_IN']) || $_SESSION['LOGGED_IN'] !== true || ($_SESSION['USER_TYPE'] ?? '') !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

$receptionist_id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receptionist_id'])) {
    $receptionist_id = (int)$_POST['receptionist_id'];
} elseif (isset($_GET['receptionist_id'])) {
    // fallback if GET used accidentally
    $receptionist_id = (int)$_GET['receptionist_id'];
}
if ($receptionist_id <= 0) {
    die("Invalid receptionist.");
}

$stmt = $conn->prepare("
    SELECT RECEPTIONIST_ID, FIRST_NAME, LAST_NAME, DOB, DOJ, GENDER, PHONE, EMAIL, ADDRESS
        
    FROM receptionist_tbl
    WHERE RECEPTIONIST_ID = ?
");
$stmt->bind_param("i", $receptionist_id);
$stmt->execute();
$result = $stmt->get_result();
$receptionist = $result->fetch_assoc();
$stmt->close();

if (!$receptionist) {
    die("Receptionist not found.");
}

$fullName = $receptionist['FIRST_NAME'] . ' ' . $receptionist['LAST_NAME'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receptionist Profile View - <?php echo htmlspecialchars($fullName); ?></title>
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
                <?php echo strtoupper(substr($receptionist['FIRST_NAME'], 0, 1) . substr($receptionist['LAST_NAME'], 0, 1)); ?>
            </div>
            <div>
                <div class="profile-main-name"><?php echo htmlspecialchars($fullName); ?></div>

            </div>
        </div>

        <div class="section-title">Personal Information</div>
        <div class="grid-2">
            <div>
                <div class="field-label">First Name</div>
                <div class="field-value"><?php echo htmlspecialchars($receptionist['FIRST_NAME']); ?></div>
            </div>
            <div>
                <div class="field-label">Last Name</div>
                <div class="field-value"><?php echo htmlspecialchars($receptionist['LAST_NAME']); ?></div>
            </div>
            <div>
                <div class="field-label">Date of Birth</div>
                <div class="field-value"><?php echo htmlspecialchars($receptionist['DOB']); ?></div>
            </div>
            <div>
                <div class="field-label">Date of Joining</div>
                <div class="field-value"><?php echo htmlspecialchars($receptionist['DOJ']); ?></div>
            </div>
            <div>
                <div class="field-label">Gender</div>
                <div class="field-value"><?php echo htmlspecialchars($receptionist['GENDER']); ?></div>
            </div>
            <div>
                <div class="field-label">Phone</div>
                <div class="field-value"><?php echo htmlspecialchars($receptionist['PHONE']); ?></div>
            </div>
        </div>

        <div class="section-title" style="margin-top:16px;">Contact Information</div>
        <div class="grid-2">
            <div>
                <div class="field-label">Email</div>
                <div class="field-value"><?php echo htmlspecialchars($receptionist['EMAIL']); ?></div>
            </div>
            <div>
                <div class="field-label">Address</div>
                <div class="field-value"><?php echo nl2br(htmlspecialchars($receptionist['ADDRESS'])); ?></div>
            </div>
        </div>

       

        <div class="footer-actions">
            <a class="btn-back" href="Admin_recept.php">
                <i class="fas fa-arrow-left"></i> Back to Receptionists
            </a>
        </div>
    </div>
</body>
</html>

