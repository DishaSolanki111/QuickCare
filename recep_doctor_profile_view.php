<?php
session_start();
include 'config.php';

// Receptionist access control
if (!isset($_SESSION['RECEPTIONIST_ID'])) {
    header("Location: login.php");
    exit;
}

$doctor_id = isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : 0;
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

$schedule_stmt = $conn->prepare("
    SELECT AVAILABLE_DAY, START_TIME, END_TIME
    FROM doctor_schedule_tbl
    WHERE DOCTOR_ID = ?
    ORDER BY FIELD(AVAILABLE_DAY,'MON','TUE','WED','THUR','FRI','SAT','SUN')
");
$schedule_stmt->bind_param("i", $doctor_id);
$schedule_stmt->execute();
$schedule_result = $schedule_stmt->get_result();
$schedule_rows = [];
while ($r = $schedule_result->fetch_assoc()) {
    $schedule_rows[] = $r;
}
$schedule_stmt->close();

function recep_day_name($code) {
    $map = [
        'MON' => 'Monday',
        'TUE' => 'Tuesday',
        'WED' => 'Wednesday',
        'THUR' => 'Thursday',
        'FRI' => 'Friday',
        'SAT' => 'Saturday',
        'SUN' => 'Sunday',
    ];
    return $map[$code] ?? $code;
}
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
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0;
        }

        .brand-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: #072D44;
        }

        .brand-sub {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 2px;
        }

        .meta-block {
            text-align: right;
            font-size: 0.8rem;
            color: #64748b;
        }

        .meta-block div + div {
            margin-top: 3px;
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

        .badge-chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            border-radius: 999px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: 0.75rem;
            font-weight: 600;
            margin-top: 6px;
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

        .schedule-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-size: 0.86rem;
        }

        .schedule-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            border: 1px solid #eef2f7;
            padding: 10px 12px;
            border-radius: 10px;
        }

        .schedule-day {
            font-weight: 700;
            color: #0f172a;
        }

        .schedule-time {
            color: #1d4ed8;
            font-weight: 600;
        }

        .footer-actions {
            margin-top: 18px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .btn-print {
            padding: 7px 16px;
            border-radius: 999px;
            border: none;
            background: #072D44;
            color: #ffffff;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-print:hover {
            background: #0b3a60;
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

        .btn-back:hover {
            background: #e2e8f0;
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
            }
            .footer-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="sheet">
        <div class="sheet-header">
            <div>
            </div>
        </div>

        <div class="profile-header">
            <div class="avatar-lg">
                <img src="<?php echo htmlspecialchars($img); ?>" alt="Doctor">
            </div>
            <div>
                <div class="profile-main-name">Dr. <?php echo htmlspecialchars($doctor_name); ?></div>
                <div class="profile-id">Specialization: <?php echo htmlspecialchars($doctor['SPECIALISATION_NAME']); ?></div>
                <div class="badge-chip">
                    <i class="fas fa-user-md"></i>
                    Verified Doctor
                </div>
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

        <!-- Schedule section removed (receptionist view). -->

        <div class="footer-actions">
            <a class="btn-back" href="recep_doctor.php">
                <i class="fas fa-arrow-left"></i> Back to Doctors
            </a>
        </div>
    </div>
</body>
</html>

