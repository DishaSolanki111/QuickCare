<?php
session_start();

if (!isset($_SESSION['LOGGED_IN']) || $_SESSION['LOGGED_IN'] !== true || !isset($_SESSION['USER_TYPE']) || $_SESSION['USER_TYPE'] !== 'doctor') {
    header("Location: login.php");
    exit();
}

include 'config.php';
$doctor_id = $_SESSION['DOCTOR_ID'];

// Detect whether filter form was submitted
$filter_submitted = ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filter_name']));
$filter_name      = $filter_submitted ? trim($_POST['filter_name']) : '';

// Base query for this doctor's feedback
$feedback_query = "SELECT f.FEEDBACK_ID, f.RATING, f.COMMENTS, a.APPOINTMENT_DATE, p.PATIENT_ID, p.FIRST_NAME, p.LAST_NAME 
    FROM feedback_tbl f 
    JOIN appointment_tbl a ON f.APPOINTMENT_ID = a.APPOINTMENT_ID 
    JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID 
    WHERE a.DOCTOR_ID = ?";

if ($filter_name !== '') {
    $feedback_query .= " AND (p.FIRST_NAME LIKE ? OR p.LAST_NAME LIKE ? OR CONCAT(p.FIRST_NAME, ' ', p.LAST_NAME) LIKE ?)";
}

$feedback_query .= " ORDER BY a.APPOINTMENT_DATE DESC";

$feedback_stmt = $conn->prepare($feedback_query);

if ($filter_name !== '') {
    $like = '%' . $filter_name . '%';
    $feedback_stmt->bind_param("isss", $doctor_id, $like, $like, $like);
} else {
    $feedback_stmt->bind_param("i", $doctor_id);
}

$feedback_stmt->execute();
$feedback_result = $feedback_stmt->get_result();
$feedbacks       = [];
while ($row = $feedback_result->fetch_assoc()) {
    $feedbacks[] = $row;
}
$feedback_stmt->close();

// Group feedback by patient
$by_patient = [];
foreach ($feedbacks as $fb) {
    $pid = (int) $fb['PATIENT_ID'];
    if (!isset($by_patient[$pid])) {
        $by_patient[$pid] = [
            'FIRST_NAME' => $fb['FIRST_NAME'],
            'LAST_NAME'  => $fb['LAST_NAME'],
            'entries'    => [],
        ];
    }
    $by_patient[$pid]['entries'][] = $fb;
}
$conn->close();

// Determine correct "no results" message
$no_results_message = '';
if (empty($by_patient)) {
    if ($filter_submitted && $filter_name !== '') {
        $no_results_message = "No feedback found for patient matching \"" . htmlspecialchars($filter_name) . "\".";
    } else {
        $no_results_message = "No feedback received yet.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --dark-blue: #072D44;
            --mid-blue: #064469;
            --soft-blue: #5790AB;
            --light-blue: #9CCDD8;
            --gray-blue: #D0D7E1;
            --white: #ffffff;
            --card-bg: #F6F9FB;
            --primary-color: #1a3a5f;
            --secondary-color: #3498db;
            --accent-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #17a2b8;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f5f7fa; color: #333; line-height: 1.6; }
        .container { display: flex; min-height: 100vh; }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            margin-top: -15px;
        }

        .content-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        /* Feedback filter form */
        .feedback-filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .feedback-filter-form .filter-group { display: flex; flex-direction: column; gap: 5px; }
        .feedback-filter-form label { font-weight: 600; color: var(--primary-color); font-size: 14px; }
        .feedback-filter-form input[type="text"] { padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; min-width: 200px; }

        .feedback-filter-actions { display: flex; gap: 10px; align-items: flex-end; }

        .btn { display: inline-block; padding: 8px 16px; border-radius: 5px; border: none; cursor: pointer; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s ease; }
        .btn-primary { background-color: var(--primary-color); color: #fff; }
        .btn-primary:hover { background-color: #2176bd; }
        .btn-secondary { background-color: #6c757d; color: #fff; }
        .btn-secondary:hover { background-color: #5a6268; }

        /* Active filter badge */
        .filter-badge {
            display: inline-block;
            background: #e8f4f8;
            color: var(--primary-color);
            border: 1px solid #b8d8e8;
            border-radius: 20px;
            padding: 4px 12px;
            font-size: 13px;
            margin-bottom: 12px;
        }

        /* Feedback Card Styles */
        .feedback-card {
            background: #fafafa;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .feedback-header { display: flex; justify-content: space-between; align-items: center; }

        .patient-info { display: flex; align-items: center; }

        .patient-avatar {
            width: 50px; height: 50px; border-radius: 50%;
            background-color: #072d44; color: #fff;
            display: flex; justify-content: center; align-items: center;
            font-size: 20px; margin-right: 15px;
        }

        .patient-name { font-weight: bold; }

        .rating i { color: #FFD700; }

        .feedback-entry {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #e9ecef;
        }

        .feedback-entry:first-of-type { margin-top: 15px; padding-top: 0; border-top: none; }

        .feedback-entry-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 6px;
        }

        .feedback-date { color: #777; font-size: 16px; font-weight: bold; }
        .feedback-comment { margin: 0; color: #333; }

        /* No results */
        .no-results {
            text-align: center;
            color: #777;
            padding: 30px 0;
            font-size: 15px;
        }
        .no-results i { font-size: 36px; display: block; margin-bottom: 10px; color: #ccc; }

        @media (max-width: 992px) { .main-content { margin-left: 70px; } }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'doctor_sidebar.php'; ?>

        <div class="main-content">
            <?php include 'doctor_header.php'; ?>

            <div class="content-card">
                <div class="feedback-section">
                    <h3 style="margin-bottom: 20px;">Patient Feedback</h3>

                    <!-- Filter Form -->
                    <form method="POST" action="doctor_feedback.php" class="feedback-filter-form">
                        <div class="filter-group">
                            <label for="filter_name"><i class="fas fa-user"></i> Patient Name</label>
                            <input
                                type="text"
                                id="filter_name"
                                name="filter_name"
                                placeholder="Search by patient name..."
                                value="<?php echo htmlspecialchars($filter_name); ?>"
                            >
                        </div>
                        <div class="feedback-filter-actions">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                            <button type="button" class="btn btn-secondary"
                                    onclick="window.location.href='doctor_feedback.php'">
                                <i class="fas fa-times"></i> Clear
                            </button>
                        </div>
                    </form>

                    <!-- Active filter indicator -->
                    <?php if ($filter_submitted && $filter_name !== ''): ?>
                        <div>
                            <span class="filter-badge">
                                <i class="fas fa-filter"></i> Showing results for: <strong><?php echo htmlspecialchars($filter_name); ?></strong>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($by_patient)): ?>
                        <?php foreach ($by_patient as $patient):
                            $initials    = strtoupper(substr($patient['FIRST_NAME'], 0, 1) . substr($patient['LAST_NAME'], 0, 1));
                            $patient_name = htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']);
                        ?>
                        <div class="feedback-card">
                            <div class="feedback-header">
                                <div class="patient-info">
                                    <div class="patient-avatar"><?php echo $initials; ?></div>
                                    <div class="patient-name"><?php echo $patient_name; ?></div>
                                </div>
                            </div>
                            <?php foreach ($patient['entries'] as $fb):
                                $rating = (int) ($fb['RATING'] ?? 0);
                            ?>
                            <div class="feedback-entry">
                                <div class="feedback-entry-header">
                                    <span class="feedback-date"><?php echo date('F d, Y', strtotime($fb['APPOINTMENT_DATE'])); ?></span>
                                    <div class="rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="<?php echo $i <= $rating ? 'fas fa-star' : 'far fa-star'; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <p class="feedback-comment"><?php echo htmlspecialchars($fb['COMMENTS'] ?? 'No comments'); ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endforeach; ?>

                    <?php else: ?>
                        <div class="feedback-card">
                            <div class="no-results">
                                <i class="fas fa-comment-slash"></i>
                                <?php echo $no_results_message; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</body>
</html>