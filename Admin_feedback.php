<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Feedback - QuickCare</title>
<?php include 'admin_sidebar.php'; ?>
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

    .topbar {
        background: white;
        padding: 15px 25px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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
        background: #5790AB;
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
        background: #5790AB;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .rating {
        color: #f39c12;
    }

    .feedback-card {
        background: white;
        padding: 8px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border-left: 8px solid #5790AB;
    }
</style>
</head>

<body>

<div class="main">

    <div class="topbar">
        <h1>View Feedback</h1>
        <p>Welcome, Admin</p>
    </div>

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
        <form method="GET" action="">
            <select name="rating_filter">
                <option value="">All Ratings</option>
                <?php
                $selectedRating = $_GET['rating_filter'] ?? '';
                for ($i = 5; $i >= 1; $i--) {
                    $sel = ($selectedRating == $i) ? "selected" : "";
                    echo "<option value='$i' $sel>$i Star</option>";
                }
                ?>
            </select>
            <button type="submit">Filter</button>
        </form>
    </div>

    <!-- TABLE -->
    <table>
        <tr>
            <th>Feedback ID</th>
            <th>Appointment ID</th>
            <th>Patient Name</th>
            <th>Doctor Name</th>
            <th>Rating</th>
            <th>Comments</th>
        </tr>

        <?php
        include 'config.php';

        $query = "SELECT f.FEEDBACK_ID, f.APPOINTMENT_ID, f.RATING, f.COMMENTS,
                  p.FIRST_NAME p_first, p.LAST_NAME p_last,
                  d.FIRST_NAME d_first, d.LAST_NAME d_last
                  FROM feedback_tbl f
                  JOIN appointment_tbl a ON f.APPOINTMENT_ID=a.APPOINTMENT_ID
                  JOIN patient_tbl p ON a.PATIENT_ID=p.PATIENT_ID
                  JOIN doctor_tbl d ON a.DOCTOR_ID=d.DOCTOR_ID
                  WHERE 1=1";

        if (!empty($_GET['rating_filter'])) {
            $rating = (int) $_GET['rating_filter'];
            $query .= " AND f.RATING = $rating";
        }

        $query .= " ORDER BY f.FEEDBACK_ID DESC";

        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {

                $stars = "";
                for ($i = 1; $i <= 5; $i++) {
                    $stars .= ($i <= $row['RATING']) ? "★" : "☆";
                }

                echo "<tr>
                    <td>{$row['FEEDBACK_ID']}</td>
                    <td>{$row['APPOINTMENT_ID']}</td>
                    <td>{$row['p_first']} {$row['p_last']}</td>
                    <td>{$row['d_first']} {$row['d_last']}</td>
                    <td class='rating'>$stars</td>
                    <td>{$row['COMMENTS']}</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>No feedback found</td></tr>";
        }

        mysqli_close($conn);
        ?>
    </table>

</div>

</body>
</html>
