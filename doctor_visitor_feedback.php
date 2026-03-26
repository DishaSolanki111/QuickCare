<?php
session_start();
include 'config.php';
include 'header.php';

// Get doctor_id from POST (to hide from URL) or GET (fallback)
$doctor_id = isset($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : (isset($_GET['doctor_id']) ? (int)$_GET['doctor_id'] : 0);

// Get star filter from POST
$star_filter = isset($_POST['star_filter']) ? (int)$_POST['star_filter'] : 0; // 0 = all stars, 1-5 = specific stars

if ($doctor_id <= 0) {
    header("Location: doctors.php");
    exit();
}

// Store doctor_id in session for filtering
$_SESSION['feedback_doctor_id'] = $doctor_id;

// Fetch doctor name (optional, for context if needed later)
$doc_name = '';
$doc_stmt = $conn->prepare("
    SELECT FIRST_NAME, LAST_NAME 
    FROM doctor_tbl 
    WHERE DOCTOR_ID = ?
");
$doc_stmt->bind_param("i", $doctor_id);
$doc_stmt->execute();
$doc_res = $doc_stmt->get_result();
if ($doc_res && $doc_res->num_rows > 0) {
    $d = $doc_res->fetch_assoc();
    $doc_name = 'Dr. ' . $d['FIRST_NAME'] . ' ' . $d['LAST_NAME'];
}
$doc_stmt->close();

// Fetch feedback records for this doctor's appointments with star filtering
$fb_query = "
    SELECT 
        f.FEEDBACK_ID,
        f.RATING,
        f.COMMENTS,
        CONCAT(p.FIRST_NAME, ' ', p.LAST_NAME) AS PATIENT_NAME,
        p.DOB,
        p.PHONE,
        a.APPOINTMENT_DATE
    FROM feedback_tbl f
    JOIN appointment_tbl a ON f.APPOINTMENT_ID = a.APPOINTMENT_ID
    JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
    WHERE a.DOCTOR_ID = ?";

// Add star filter if specified
if ($star_filter > 0 && $star_filter <= 5) {
    $fb_query .= " AND f.RATING = ?";
}

$fb_query .= " ORDER BY a.APPOINTMENT_DATE DESC, f.FEEDBACK_ID DESC";

$fb_stmt = $conn->prepare($fb_query);
if ($star_filter > 0 && $star_filter <= 5) {
    $fb_stmt->bind_param("ii", $doctor_id, $star_filter);
} else {
    $fb_stmt->bind_param("i", $doctor_id);
}
$fb_stmt->execute();
$feedback_result = $fb_stmt->get_result();
$fb_stmt->close();

// Calculate feedback statistics for star-wise filtering
$stats_query = "
    SELECT 
        RATING,
        COUNT(*) as count
    FROM feedback_tbl f
    JOIN appointment_tbl a ON f.APPOINTMENT_ID = a.APPOINTMENT_ID
    WHERE a.DOCTOR_ID = ?
    GROUP BY RATING
    ORDER BY RATING DESC";
$stats_stmt = $conn->prepare($stats_query);
$stats_stmt->bind_param("i", $doctor_id);
$stats_stmt->execute();
$stats_result = $stats_stmt->get_result();
$star_counts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
$total_feedback = 0;
while ($stat = $stats_result->fetch_assoc()) {
    $star_counts[$stat['RATING']] = $stat['count'];
    $total_feedback += $stat['count'];
}
$stats_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Feedback Records - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --navy: #001F3F;
            --emerald: #2ECC71;
            --bg: #F5F7FA;
            --card-bg: #FFFFFF;
            --border-soft: #E2E8F0;
            --text-main: #111827;
            --text-muted: #6B7280;
            --gold: #FBBF24;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        body {
            min-height: 100vh;
            background: var(--bg);
            display: flex;
            justify-content: center;
        }

        .page {
            width: 100%;
            max-width: 1100px;
            padding: 24px 24px 32px;
        }

        .top-row {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: 18px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 999px;
            border: 1px solid var(--navy);
            background: #ffffff;
            color: var(--navy);
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
        }

        .back-btn i {
            font-size: 0.9rem;
        }

        .back-btn:hover {
            background: var(--navy);
            color: #ffffff;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--navy);
            margin-top: 4px;
        }

        .subtitle {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 18px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 16px;
        }

        .fb-card {
            background: var(--card-bg);
            border-radius: 14px;
            border: 1px solid var(--border-soft);
            padding: 16px 16px 14px;
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
            display: flex;
            flex-direction: column;
        }

        .fb-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .avatar-circle {
            width: 46px;
            height: 46px;
            border-radius: 999px;
            background: var(--navy);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
        }

        .stars {
            color: var(--gold);
            font-size: 0.9rem;
        }

        .fb-body {
            margin-top: 4px;
            color: var(--navy);
            font-size: 0.9rem;
        }

        .fb-body p {
            margin-bottom: 4px;
        }

        .fb-quote {
            margin-top: 8px;
            padding: 10px 12px;
            border-radius: 10px;
            background: #F9FAFB;
            border: 1px solid #E5E7EB;
            color: #374151;
            font-size: 0.9rem;
        }

        .fb-quote i {
            margin-right: 6px;
            color: #9CA3AF;
        }

        .fb-footer {
            margin-top: 12px;
        }

        .btn-view {
            width: 100%;
            border-radius: 999px;
            border: none;
            padding: 9px 14px;
            background: var(--emerald);
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-view:hover {
            background: #27ae60;
        }

        .empty-state {
            margin-top: 24px;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        @media (max-width: 640px) {
            .page {
                padding: 18px 14px 24px;
            }
            .top-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="top-row">
            <a href="doctors.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Doctors List
            </a>
            <div class="page-title">Patient Feedback Records</div>
        </div>

        <!-- Star Filter Section -->
        <div style="background: white; padding: 16px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #e5e7eb;">
            <form method="POST" action="" style="display: flex; flex-wrap: wrap; align-items: center; gap: 12px;">
                <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
                
                <div style="display: flex; align-items: center; gap: 8px;">
                    <label for="star_filter" style="font-weight: 600; color: #374151;">Filter by Rating:</label>
                    <select name="star_filter" id="star_filter" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; background: white; color: #374151; cursor: pointer; font-size: 14px; min-width: 150px;">
                        <option value="0" <?php echo $star_filter == 0 ? 'selected' : ''; ?>>All Stars</option>
                        <?php for ($stars = 5; $stars >= 1; $stars--): ?>
                            <option value="<?php echo $stars; ?>" <?php echo $star_filter == $stars ? 'selected' : ''; ?>>
                                <?php echo $stars; ?> Star<?php echo $stars > 1 ? 's' : ''; ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <button type="submit" style="padding: 8px 16px; border: none; border-radius: 6px; background: #072d44; color: white; cursor: pointer; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                    <i class="fas fa-search"></i> Search
                </button>
                
                <?php if ($star_filter > 0): ?>
                    <button type="submit" name="star_filter" value="0" style="padding: 8px 16px; border: none; border-radius: 6px; background: #dc3545; color: white; cursor: pointer; font-size: 14px; font-weight: 600;">
                        <i class="fas fa-times"></i> Clear
                    </button>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($feedback_result && $feedback_result->num_rows > 0): ?>
            <div class="cards-grid">
                <?php while ($fb = $feedback_result->fetch_assoc()):
                    $patientName = $fb['PATIENT_NAME'];
                    $initials   = strtoupper(substr($patientName, 0, 1));
                    $rating     = (int)$fb['RATING'];
                ?>
                    <div class="fb-card">
                        <div class="fb-header">
                            <div class="avatar-circle"><?php echo htmlspecialchars($initials); ?></div>
                            <div class="stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="<?php echo $i <= $rating ? 'fas fa-star' : 'far fa-star'; ?>"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="fb-body">
                            <p><strong>Patient:</strong> <?php echo htmlspecialchars($patientName); ?></p>
                        </div>
                        <div class="fb-quote">
                            <i class="fas fa-quote-left"></i>
                            <?php echo htmlspecialchars($fb['COMMENTS']); ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                No feedback records found for this doctor yet.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

