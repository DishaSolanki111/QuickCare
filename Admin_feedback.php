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
    --card-bg: #F6F9FB;
    --primary-color: #1a3a5f;
    --secondary-color: #3498db;
    --accent-color: #2ecc71;
    --danger-color: #e74c3c;
    --warning-color: #f39c12;
    --info-color: #17a2b8;
        }

    /* Main content */
    .main {
        margin-left: 250px;
        padding: 20px;
        width: calc(100% - 250px);
    }

    /* Top bar */
    .topbar {
        background: white;
        padding: 15px 25px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .topbar h1 {
        margin: 0;
        color: #064469;
    }

    /* Table Section */
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

    tr:hover {
        background: #F2F9FB;
    }
    .logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
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
    .filter-container input, .filter-container select {
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
    .filter-container button:hover {
        background: #064469;
    }
    .action-btn {
        padding: 5px 10px;
        margin: 0 2px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }
    .view-btn {
        background-color: #3498db;
        color: white;
    }
    
    .rating {
        color: #f39c12;
    }
    .feedback-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border-left: 8px solid #5790AB;
    }
    .feedback-card h3 {
        margin: 0;
        color: #072D44;
    }
    .feedback-card p {
        margin-top: 10px;
        font-size: 22px;
        color: #064469;
        font-weight: bold;
    }
</style>
</head>
<body>

<!-- Main Content -->
<div class="main">
    <!-- Topbar -->
    <div class="topbar">
        <h1>View Feedback</h1>
        <p>Welcome, Admin</p>
    </div>

    <!-- Feedback Statistics -->
    <div class="feedback-card">
        <h3>Average Rating</h3>
        <p><span class="rating"><?php 
        // PHP code to calculate average rating
        include 'config.php';
        $rating_query = "SELECT AVG(RATING) as avg_rating FROM feedback_tbl";
        $rating_result = mysqli_query($conn, $rating_query);
        $rating_row = mysqli_fetch_assoc($rating_result);
        $avg_rating = round($rating_row['avg_rating'], 1);
        
        // Display star rating
        for($i = 1; $i <= 5; $i++) {
            if($i <= $avg_rating) {
                echo "★";
            } else {
                echo "☆";
            }
        }
        echo " " . $avg_rating;
        mysqli_close($conn);
        ?></span></p>
    </div>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="view_feedback.php">
            <select name="rating_filter">
                <option value="">All Ratings</option>
                <option value="5">5 Stars</option>
                <option value="4">4 Stars</option>
                <option value="3">3 Stars</option>
                <option value="2">2 Stars</option>
                <option value="1">1 Star</option>
            </select>
            <button type="submit">Filter</button>
        </form>
    </div>

    <!-- Feedback Table -->
    <table>
        <tr>
            <th>Feedback ID</th>
            <th>Appointment ID</th>
            <th>Patient Name</th>
            <th>Doctor Name</th>
            <th>Rating</th>
            <th>Comments</th>
            <th>Actions</th>
        </tr>
        <?php
        // PHP code to fetch feedback from database
        include 'config.php';
        
        // Build query based on filters
        $query = "SELECT f.FEEDBACK_ID, f.APPOINTMENT_ID, f.RATING, f.COMMENTS,
                  p.FIRST_NAME as p_first, p.LAST_NAME as p_last,
                  d.FIRST_NAME as d_first, d.LAST_NAME as d_last
                  FROM feedback_tbl f
                  JOIN appointment_tbl a ON f.APPOINTMENT_ID = a.APPOINTMENT_ID
                  JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
                  JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
                  WHERE 1=1";
        
        // Apply filters if set
        if(isset($_GET['rating_filter']) && !empty($_GET['rating_filter'])) {
            $rating = mysqli_real_escape_string($conn, $_GET['rating_filter']);
            $query .= " AND f.RATING = $rating";
        }
        
        $query .= " ORDER BY f.FEEDBACK_ID DESC";
        
        $result = mysqli_query($conn, $query);
        
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $stars = "";
                for($i = 1; $i <= 5; $i++) {
                    if($i <= $row['RATING']) {
                        $stars .= "★";
                    } else {
                        $stars .= "☆";
                    }
                }
                
                echo "<tr>
                    <td>".$row['FEEDBACK_ID']."</td>
                    <td>".$row['APPOINTMENT_ID']."</td>
                    <td>".$row['p_first']." ".$row['p_last']."</td>
                    <td>".$row['d_first']." ".$row['d_last']."</td>
                    <td><span class='rating'>".$stars."</span></td>
                    <td>".$row['COMMENTS']."</td>
                    <td>
                        <button class='action-btn view-btn' onclick='viewFeedback(".$row['FEEDBACK_ID'].")'>View</button>
                       
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No feedback found</td></tr>";
        }
        
        mysqli_close($conn);
        ?>
    </table>
</div>

<script>
function viewFeedback(id) {
    window.location.href = "view_feedback_details.php?id=" + id;
}

}
</script>

</body>
</html>