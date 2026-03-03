<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['LOGGED_IN']) || $_SESSION['LOGGED_IN'] !== true || $_SESSION['USER_TYPE'] !== 'admin') {
    // If not logged in or not an admin, redirect to admin login page
    header("Location: admin_login.php");
    exit();
}

// Get admin name for display
$adminName = $_SESSION['USER_NAME'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Feedback - QuickCare</title>
<?php include 'admin_sidebar.php'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #D0D7E1;
        display: flex;
    }
    :root {
        --dark-blue: #072D44;
        --mid-blue: #064469;
        --soft-blue: #5790AB;
        --light-blue: #9CCDD8;
        --gray-blue: #D0D7E1;
        --white: #ffffff;
    }

    .main {
        margin-left: 250px;
        padding: 20px;
        width: calc(100% - 250px);
    }

    
    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    th, td {
        padding: 14px;
        border-bottom: 1px solid #D0D7E1;
    }

    th {
        background: #072D44;
        color: white;
        text-align: left;
    }

    .filter-container {
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .filter-container form {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }

    .filter-container select {
        padding: 10px;
        border: 1px solid #D0D7E1;
        border-radius: 5px;
    }

    .filter-container button {
        padding: 10px 15px;
        background: var(--dark-blue);
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .rating {
        color: #f39c12;
    }

    .feedback-card {
        padding: 10px 0;
        margin-bottom: 0;
    }

    .feedback-groups {
        margin-top: 10px;
    }

    .doctor-group {
        margin-bottom: 28px;
        background: #f8fafc;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .doctor-header-bar {
        background: var(--dark-blue);
        color: #ffffff;
        padding: 14px 18px;
        border-radius: 10px 10px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .doctor-header-title {
        font-weight: 600;
        font-size: 1rem;
    }

    .doctor-header-subtitle {
        font-size: 0.9rem;
        opacity: 0.95;
    }

    .feedback-list {
        margin-top: 0;
        padding: 10px 16px 12px;
    }

    .feedback-meta {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 8px;
        font-size: 0.9rem;
        color: #555;
        margin-bottom: 6px;
    }

    .feedback-meta .feedback-patient {
        font-weight: 600;
    }

    .feedback-meta .feedback-date {
        font-style: italic;
    }

    .feedback-meta .feedback-stars {
        color: #f39c12;
    }

    .feedback-comment {
        font-size: 0.95rem;
        color: #333;
    }
</style>
</head>

<body>

<div class="main">

    <?php include 'admin_header.php'; ?>

    <!-- AVERAGE RATING -->
    <div class="feedback-card">
        <h3>Average Rating</h3>
        <p class="rating">
            <?php
            include 'config.php';
            $avgQ = mysqli_query($conn, "SELECT AVG(RATING) AS avg_rating FROM feedback_tbl");
            $avgR = mysqli_fetch_assoc($avgQ);
            $avg = round($avgR['avg_rating'], 1);

            for ($i = 1; $i <= 5; $i++) {
                echo ($i <= $avg) ? "★" : "☆";
            }
            echo " $avg";
            mysqli_close($conn);
            ?>
        </p>
    </div>

    <!-- FILTER (FIXED) -->
    <div class="filter-container">
        <form method="POST" action="">
            <select name="rating_filter">
                <option value="">All Ratings</option>
                <?php
                $selectedRating = $_POST['rating_filter'] ?? '';
                for ($i = 5; $i >= 1; $i--) {
                    $sel = ($selectedRating == $i) ? "selected" : "";
                    echo "<option value='$i' $sel>$i Star</option>";
                }
                ?>
            </select>
            <button type="submit">
                <i class="bi bi-funnel"></i>
                Filter
            </button>
        </form>
    </div>

    <!-- FEEDBACK GROUPED BY DOCTOR -->
    <div class="feedback-groups">
        <?php
        include 'config.php';

        $query = "SELECT 
                    f.FEEDBACK_ID,
                    f.APPOINTMENT_ID,
                    f.RATING,
                    f.COMMENTS,
                    a.APPOINTMENT_DATE,
                    p.FIRST_NAME AS p_first,
                    p.LAST_NAME AS p_last,
                    d.FIRST_NAME AS d_first,
                    d.LAST_NAME AS d_last,
                    s.SPECIALISATION_NAME
                  FROM feedback_tbl f
                  JOIN appointment_tbl a ON f.APPOINTMENT_ID = a.APPOINTMENT_ID
                  JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
                  JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
                  JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
                  WHERE 1=1";

        if (!empty($_POST['rating_filter'])) {
            $rating = (int) $_POST['rating_filter'];
            $query .= " AND f.RATING = $rating";
        }

        $query .= " ORDER BY d.LAST_NAME, d.FIRST_NAME, a.APPOINTMENT_DATE DESC, f.FEEDBACK_ID DESC";

        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $currentDoctorKey = '';

            while ($row = mysqli_fetch_assoc($result)) {
                $doctorName = 'Dr. ' . $row['d_first'] . ' ' . $row['d_last'];
                $specialisation = $row['SPECIALISATION_NAME'];
                $doctorKey = $doctorName . '|' . $specialisation;

                if ($doctorKey !== $currentDoctorKey) {
                    if ($currentDoctorKey !== '') {
                        // Close previous group
                        echo '</div></div>';
                    }

                    $currentDoctorKey = $doctorKey;

                    echo '<div class="doctor-group">';
                    echo '  <div class="doctor-header-bar">';
                    echo '      <div class="doctor-header-title">' . htmlspecialchars($doctorName) . '</div>';
                    echo '      <div class="doctor-header-subtitle">' . htmlspecialchars($specialisation) . '</div>';
                    echo '  </div>';
                    echo '  <div class="feedback-list">';
                }

                $stars = '';
                for ($i = 1; $i <= 5; $i++) {
                    $stars .= ($i <= (int)$row['RATING']) ? '★' : '☆';
                }

                $patientName = $row['p_first'] . ' ' . $row['p_last'];
                $dateText = !empty($row['APPOINTMENT_DATE'])
                    ? date('F d, Y', strtotime($row['APPOINTMENT_DATE']))
                    : '';
                $comment = htmlspecialchars($row['COMMENTS']);

                echo '      <div class="feedback-card">';
                echo '          <div class="feedback-meta">';
                echo '              <span class="feedback-patient">' . htmlspecialchars($patientName) . '</span>';
                if ($dateText !== '') {
                    echo '          <span class="feedback-date">' . htmlspecialchars($dateText) . '</span>';
                }
                echo '              <span class="feedback-stars">' . $stars . '</span>';
                echo '          </div>';
                echo '          <div class="feedback-comment">' . $comment . '</div>';
                echo '      </div>';
            }

            if ($currentDoctorKey !== '') {
                // Close last group
                echo '  </div></div>';
            }
        } else {
            echo "<p>No feedback found</p>";
        }

        mysqli_close($conn);
        ?>
    </div>

</div>

</body>
</html>
