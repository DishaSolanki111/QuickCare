<?php
session_start();
include 'config.php';
include 'recept_sidebar.php'; 

// Receptionist access control
if (!isset($_SESSION['RECEPTIONIST_ID'])) {
    header("Location: login.php");
    exit;
}

// Fetch receptionist info for the header name display
$receptionist_id = $_SESSION['RECEPTIONIST_ID'];
$rec_q = mysqli_query($conn, "SELECT * FROM receptionist_tbl WHERE RECEPTIONIST_ID = '" . (int)$receptionist_id . "'");
$receptionist = mysqli_fetch_assoc($rec_q);

// Accept patient_id via POST (hidden from URL) or GET as fallback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['patient_id'])) {
    $patient_id = (int)$_POST['patient_id'];
} elseif (isset($_GET['patient_id'])) {
    // Redirect GET requests to a POST-based form so the ID stays hidden
    $pid = (int)$_GET['patient_id'];
    if ($pid <= 0) {
        die("Invalid patient.");
    }
    // Auto-submit via a self-posting redirect page
    echo '<!DOCTYPE html><html><body>';
    echo '<form id="rf" method="POST" action="recep_patient_profile_view.php">';
    echo '<input type="hidden" name="patient_id" value="' . $pid . '">';
    echo '</form><script>document.getElementById("rf").submit();</script>';
    echo '</body></html>';
    exit;
} else {
    die("Invalid patient.");
}

if ($patient_id <= 0) {
    die("Invalid patient.");
}

$stmt = $conn->prepare("
    SELECT PATIENT_ID, FIRST_NAME, LAST_NAME, USERNAME, DOB, GENDER, BLOOD_GROUP, PHONE, EMAIL, ADDRESS
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
    <title>Patient Profile PDF View - <?php echo htmlspecialchars($fullName); ?></title>
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

        .address-block {
            margin-top: 4px;
            font-size: 0.86rem;
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
        <?php include 'receptionist_header.php'; ?>
        <div class="sheet">
            <div class="sheet-header"></div>

            <div class="profile-header">
                <div class="avatar-lg">
                    <?php echo strtoupper(substr($patient['FIRST_NAME'], 0, 1) . substr($patient['LAST_NAME'], 0, 1)); ?>
                </div>
                <div>
                    <div class="profile-main-name"><?php echo htmlspecialchars($fullName); ?></div>
                    <div class="profile-id">Username: <?php echo htmlspecialchars($patient['USERNAME']); ?></div>
                </div>
            </div>

            <div class="section-title">Personal Information</div>
            <div class="grid-2">
                <div>
                    <div class="field-label">Full Name</div>
                    <div class="field-value"><?php echo htmlspecialchars($fullName); ?></div>
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
            </div>

            <div class="section-title" style="margin-top:16px;">Contact Information</div>
            <div class="grid-2">
                <div>
                    <div class="field-label">Phone</div>
                    <div class="field-value"><?php echo htmlspecialchars($patient['PHONE']); ?></div>
                </div>
                <div>
                    <div class="field-label">Email</div>
                    <div class="field-value"><?php echo htmlspecialchars($patient['EMAIL']); ?></div>
                </div>
            </div>

            <div class="section-title" style="margin-top:16px;">Address</div>
            <div class="address-block">
                <div class="field-label">Full Address</div>
                <div class="field-value"><?php echo nl2br(htmlspecialchars($patient['ADDRESS'])); ?></div>
            </div>

            <div class="footer-actions">
                <form method="POST" action="recep_patient.php" style="display:inline;">
                    <button type="submit" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Back to Patients
                    </button>
                </form>
            </div>
        </div>
    </div>

<script>
// No JS navigation needed
</script>
</body>
</html>

