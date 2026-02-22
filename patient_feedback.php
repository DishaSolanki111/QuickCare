<?php
session_start();

// Check if user is logged in as a patient
if (!isset($_SESSION['PATIENT_ID'])) {
    header("Location: login_for_all.php");
    exit;
}

include 'config.php';
 $patient_id = $_SESSION['PATIENT_ID'];

// Fetch patient data from database
 $patient_query = mysqli_query($conn, "SELECT * FROM patient_tbl WHERE PATIENT_ID = '$patient_id'");
 $patient = mysqli_fetch_assoc($patient_query);

// Handle form submission for feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    $appointment_id = mysqli_real_escape_string($conn, $_POST['appointment_id']);
    $rating = mysqli_real_escape_string($conn, $_POST['rating']);
    $comments = mysqli_real_escape_string($conn, $_POST['comments']);
    
    // Check if feedback already exists for this appointment
    $check_query = "SELECT * FROM feedback_tbl WHERE APPOINTMENT_ID = '$appointment_id'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update existing feedback
        $update_query = "UPDATE feedback_tbl SET 
                        RATING = '$rating',
                        COMMENTS = '$comments'
                        WHERE APPOINTMENT_ID = '$appointment_id'";
        
        if (mysqli_query($conn, $update_query)) {
            $success_message = "Feedback updated successfully!";
        } else {
            $error_message = "Error updating feedback: " . mysqli_error($conn);
        }
    } else {
        // Insert new feedback
        $insert_query = "INSERT INTO feedback_tbl (APPOINTMENT_ID, RATING, COMMENTS) 
                         VALUES ('$appointment_id', '$rating', '$comments')";
        
        if (mysqli_query($conn, $insert_query)) {
            $success_message = "Feedback submitted successfully!";
        } else {
            $error_message = "Error submitting feedback: " . mysqli_error($conn);
        }
    }
}

// Filter by star rating: 1, 2, 3, 4, or 5 stars (empty = All)
$filter_rating = isset($_GET['filter_rating']) ? (int) $_GET['filter_rating'] : 0;
// Preserve active tab after filter (your-feedback or all-feedback)
$active_tab = isset($_GET['tab']) && $_GET['tab'] === 'all-feedback' ? 'all-feedback' : 'your-feedback';
$rating_where = "";
if ($filter_rating >= 1 && $filter_rating <= 5) {
    $rating_where = " AND COALESCE(f.RATING, 0) = " . $filter_rating;
}

// Fetch completed appointments for feedback (with rating filter)
$appointments_query = mysqli_query($conn, "
    SELECT a.*, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME,
           f.FEEDBACK_ID, f.RATING, f.COMMENTS as FEEDBACK_COMMENTS
    FROM appointment_tbl a
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    LEFT JOIN feedback_tbl f ON a.APPOINTMENT_ID = f.APPOINTMENT_ID
    WHERE a.PATIENT_ID = '$patient_id'
    AND a.STATUS = 'COMPLETED'
    $rating_where
    ORDER BY a.APPOINTMENT_DATE DESC
");

// Get appointment ID from URL if coming from appointments page
 $appointment_id = isset($_POST['appointment']) ? mysqli_real_escape_string($conn, $_POST['appointment']) : '';

// If appointment ID is provided, get appointment details
 $appointment_details = null;
if (!empty($appointment_id)) {
    $appointment_details_query = mysqli_query($conn, "
        SELECT a.*, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME,
               f.FEEDBACK_ID, f.RATING, f.COMMENTS as FEEDBACK_COMMENTS
        FROM appointment_tbl a
        JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
        JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
        LEFT JOIN feedback_tbl f ON a.APPOINTMENT_ID = f.APPOINTMENT_ID
        WHERE a.PATIENT_ID = '$patient_id'
        AND a.APPOINTMENT_ID = '$appointment_id'
        AND a.STATUS = 'COMPLETED'
    ");
    
    if (mysqli_num_rows($appointment_details_query) > 0) {
        $appointment_details = mysqli_fetch_assoc($appointment_details_query);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a3a5f;
            --secondary-color: #3498db;
            --accent-color: #2ecc71;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #17a2b8;
            --dark-blue: #072D44;
            --mid-blue: #064469;
            --soft-blue: #5790AB;
            --light-blue: #9CCDD8;
            --gray-blue: #D0D7E1;
            --white: #ffffff;
            --card-bg: #F6F9FB;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
          body {
    background-color: #f5f7fa;
    color: #333;
    line-height: 1.6;
    margin: 0;
    padding: 0;
    height: 100vh;
    overflow-y: scroll;
}

html {
    height: 100%;
   
}

.container {
    display: flex;
    min-height: 100vh;
    height: 100%;
}

.main-content {
    flex: 1;
    margin-left: 250px;
    padding: 20px;
    height: 100%;
    overflow-y: auto;
}
        .user-actions {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .feedback-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .feedback-header h3 {
            color: var(--primary-color);
        }
        
        .feedback-details {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .feedback-item {
            display: flex;
            align-items: center;
            color: #666;
        }
        
        .feedback-item i {
            margin-right: 10px;
            color: var(--secondary-color);
            font-size: 18px;
        }
        
        .rating {
            display: flex;
            align-items: center;
            margin: 15px 0;
        }
        
        .rating-stars {
            display: flex;
            margin-right: 10px;
        }
        
        .star {
            color: #ddd;
            font-size: 20px;
            cursor: pointer;
            transition: color 0.2s;
        }
        
        .star.active {
            color: #ffc107;
        }
        
        .star:hover,
        .star.hover {
            color: #ffc107;
        }
        
        /* Display-only stars (cards) */
        .star-display {
            color: #ddd;
            font-size: 20px;
        }
        
        .star-display.active {
            color: #ffc107;
        }
        
        .rating-value {
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .feedback-text {
            margin-top: 15px;
            padding: 15px;
            background-color: var(--light-color);
            border-radius: 8px;
            color: var(--dark-color);
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-success {
            background-color: var(--accent-color);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #27ae60;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #777;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }

        /* Tabs styling (same as manage_appointments.php) */
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 12px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            color: #777;
            transition: all 0.3s ease;
        }
        
        .tab.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--secondary-color);
        }
        
        .tab:hover {
            color: var(--primary-color);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: none;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 200px;
            }
        }
        
        @media (max-width: 768px) {
            .feedback-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Import Sidebar -->
        <?php include 'patient_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">

            <!-- Header -->
            <?php include 'patient_header.php'; ?>
            
            <!-- Tabs Section (same style as manage_appointments.php) -->
            <div class="tabs">
                <div class="tab <?php echo $active_tab === 'your-feedback' ? 'active' : ''; ?>" data-tab="your-feedback">Your Feedback</div>
                <div class="tab <?php echo $active_tab === 'all-feedback' ? 'active' : ''; ?>" data-tab="all-feedback">All Feedback</div>
            </div>

            <!-- Filter by Star Rating: 1 to 5 stars -->
            <form method="GET" action="patient_feedback.php" id="feedbackFilterForm" style="margin-bottom:20px;">
                <input type="hidden" name="tab" id="filterTabInput" value="<?php echo htmlspecialchars($active_tab); ?>">
                <label for="filter_rating" style="margin-right:10px;">Filter by rating:</label>
                <select name="filter_rating" id="filter_rating" class="form-control" style="display:inline-block; width:auto; margin-right:10px;">
                    <option value="" <?php echo $filter_rating == 0 ? 'selected' : ''; ?>>All</option>
                    <option value="1" <?php echo $filter_rating == 1 ? 'selected' : ''; ?>>1 Star</option>
                    <option value="2" <?php echo $filter_rating == 2 ? 'selected' : ''; ?>>2 Stars</option>
                    <option value="3" <?php echo $filter_rating == 3 ? 'selected' : ''; ?>>3 Stars</option>
                    <option value="4" <?php echo $filter_rating == 4 ? 'selected' : ''; ?>>4 Stars</option>
                    <option value="5" <?php echo $filter_rating == 5 ? 'selected' : ''; ?>>5 Stars</option>
                </select>
                <button type="submit" class="btn btn-primary" style="vertical-align:middle;">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
            
            <!-- Success/Error Messages -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            

            <!-- Tab Content: Your Feedback -->
            <div class="tab-content <?php echo $active_tab === 'your-feedback' ? 'active' : ''; ?>" id="your-feedback">
                <?php if ($appointment_details): ?>
                <div class="feedback-card">
                    <div class="feedback-header">
                        <h3>Leave Feedback for Dr. <?php echo htmlspecialchars($appointment_details['DOC_FNAME'] . ' ' . $appointment_details['DOC_LNAME']); ?></h3>
                        <span><?php echo htmlspecialchars($appointment_details['SPECIALISATION_NAME']); ?></span>
                    </div>
                    <div class="feedback-details">
                        <div class="feedback-item">
                            <i class="far fa-calendar"></i>
                            <span><?php echo date('F d, Y', strtotime($appointment_details['APPOINTMENT_DATE'])); ?></span>
                        </div>
                        <div class="feedback-item">
                            <i class="far fa-clock"></i>
                            <span><?php echo date('h:i A', strtotime($appointment_details['APPOINTMENT_TIME'])); ?></span>
                        </div>
                    </div>
                    <?php if ($appointment_details['FEEDBACK_ID']): ?>
                        <div class="rating">
                            <div class="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <i class="fas fa-star star-display <?php echo $i <= $appointment_details['RATING'] ? 'active' : ''; ?>"></i>
                                <?php endfor; ?>
                            </div>
                            <span class="rating-value"><?php echo $appointment_details['RATING']; ?>/5</span>
                        </div>
                        <div class="feedback-text">
                            <?php echo htmlspecialchars($appointment_details['FEEDBACK_COMMENTS']); ?>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-primary" onclick="openFeedbackModal(<?php echo $appointment_details['APPOINTMENT_ID']; ?>, <?php echo $appointment_details['RATING']; ?>, '<?php echo htmlspecialchars($appointment_details['FEEDBACK_COMMENTS']); ?>')">
                                <i class="fas fa-edit"></i> Edit Feedback
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="btn-group">
                            <button class="btn btn-success" onclick="openFeedbackModal(<?php echo $appointment_details['APPOINTMENT_ID']; ?>)">
                                <i class="fas fa-star"></i> Leave Feedback
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <h3 style="margin-bottom: 20px;">Your Completed Appointments</h3>
                <?php
                if (mysqli_num_rows($appointments_query) > 0) {
                    while ($appointment = mysqli_fetch_assoc($appointments_query)) {
                        ?>
                        <div class="feedback-card">
                            <div class="feedback-header">
                                <h3>Dr. <?php echo htmlspecialchars($appointment['DOC_FNAME'] . ' ' . $appointment['DOC_LNAME']); ?></h3>
                                <span><?php echo htmlspecialchars($appointment['SPECIALISATION_NAME']); ?></span>
                            </div>
                            <div class="feedback-details">
                                <div class="feedback-item">
                                    <i class="far fa-calendar"></i>
                                    <span><?php echo date('F d, Y', strtotime($appointment['APPOINTMENT_DATE'])); ?></span>
                                </div>
                                <div class="feedback-item">
                                    <i class="far fa-clock"></i>
                                    <span><?php echo date('h:i A', strtotime($appointment['APPOINTMENT_TIME'])); ?></span>
                                </div>
                            </div>
                            <?php if ($appointment['FEEDBACK_ID']): ?>
                                <div class="rating">
                                    <div class="rating-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star star-display <?php echo $i <= $appointment['RATING'] ? 'active' : ''; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <span class="rating-value"><?php echo $appointment['RATING']; ?>/5</span>
                                </div>
                                <div class="feedback-text">
                                    <?php echo htmlspecialchars($appointment['FEEDBACK_COMMENTS']); ?>
                                </div>
                                <div class="btn-group">
                                    <button class="btn btn-primary" onclick="openFeedbackModal(<?php echo $appointment['APPOINTMENT_ID']; ?>, <?php echo $appointment['RATING']; ?>, '<?php echo htmlspecialchars($appointment['FEEDBACK_COMMENTS']); ?>')">
                                        <i class="fas fa-edit"></i> Edit Feedback
                                    </button>
                                </div>
                            <?php else: ?>
                                <div class="btn-group">
                                    <button class="btn btn-success" onclick="openFeedbackModal(<?php echo $appointment['APPOINTMENT_ID']; ?>)">
                                        <i class="fas fa-star"></i> Leave Feedback
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="empty-state">
                        <i class="fas fa-comment-slash"></i>
                        <p>No completed appointments found</p>
                    </div>';
                }
                ?>
            </div>

            <!-- Tab Content: All Feedback -->
            <div class="tab-content <?php echo $active_tab === 'all-feedback' ? 'active' : ''; ?>" id="all-feedback">
                <h3 style="margin-bottom: 20px;">All Patient Feedback</h3>
                <div style="overflow-x:auto;">
                <table style="width:100%; border-collapse:collapse; background:white; border-radius:12px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.05);">
                    <tr style="background:#5790AB; color:white;">
                        <th>Patient Name</th>
                        <th>Doctor Name</th>
                        <th>Specialization</th>
                        <th>Rating</th>
                        <th>Comments</th>
                    </tr>
                    <?php
                    $all_rating_where = ($filter_rating >= 1 && $filter_rating <= 5) ? " AND f.RATING = " . $filter_rating : "";
                    $all_feedback_query = mysqli_query(
                        $conn,
                        "SELECT 
                            f.RATING, 
                            f.COMMENTS, 
                            p.FIRST_NAME AS P_FNAME, 
                            p.LAST_NAME AS P_LNAME, 
                            d.FIRST_NAME AS D_FNAME, 
                            d.LAST_NAME AS D_LNAME,
                            s.SPECIALISATION_NAME
                         FROM feedback_tbl f 
                         JOIN appointment_tbl a ON f.APPOINTMENT_ID = a.APPOINTMENT_ID 
                         JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID 
                         JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
                         JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
                         WHERE 1=1 $all_rating_where
                         ORDER BY f.RATING ASC, f.FEEDBACK_ID DESC"
                    );
                    if ($all_feedback_query && mysqli_num_rows($all_feedback_query) > 0) {
                        while ($row = mysqli_fetch_assoc($all_feedback_query)) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['P_FNAME'] . ' ' . $row['P_LNAME']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['D_FNAME'] . ' ' . $row['D_LNAME']) . '</td>';
                            echo '<td>' . htmlspecialchars($row['SPECIALISATION_NAME']) . '</td>';
                            echo '<td class="rating">';
                            for ($i = 1; $i <= 5; $i++) {
                                echo ($i <= $row['RATING']) ? '★' : '☆';
                            }
                            echo ' ' . $row['RATING'] . '/5</td>';
                            echo '<td>' . htmlspecialchars($row['COMMENTS']) . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="5">No feedback found</td></tr>';
                    }
                    ?>
                </table>
                </div>
            </div>

            <!-- Feedback Modal -->
            <div id="feedbackModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeFeedbackModal()">&times;</span>
                    <h2>Rate Your Experience</h2>
                    <form method="POST">
                        <input type="hidden" name="appointment_id" id="feedback_appointment_id">
                        <div class="form-group">
                            <label>Rating</label>
                            <div class="rating">
                                <div class="rating-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star star" data-rating="<?php echo $i; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <span class="rating-value" id="ratingValue">0/5</span>
                                <input type="hidden" name="rating" id="rating" value="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="comments">Comments</label>
                            <textarea class="form-control" id="comments" name="comments" rows="4" placeholder="Share your experience..."></textarea>
                        </div>
                        <div class="btn-group">
                            <button type="submit" name="submit_feedback" class="btn btn-success">
                                <i class="fas fa-check"></i> Save Feedback
                            </button>
                            <button type="button" class="btn btn-danger" onclick="closeFeedbackModal()">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tabs behavior (same as manage_appointments.php)
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabId = tab.getAttribute('data-tab');
                    
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    tab.classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                    
                    // Update hidden input so filter form preserves active tab on submit
                    const filterTabInput = document.getElementById('filterTabInput');
                    if (filterTabInput) filterTabInput.value = tabId;
                });
            });

            // Rating stars functionality
            const stars = document.querySelectorAll('.star');
            const ratingValue = document.getElementById('ratingValue');
            const ratingInput = document.getElementById('rating');
            
            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    ratingInput.value = rating;
                    ratingValue.textContent = rating + '/5';
                    
                    // Update star display
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.classList.add('active');
                        } else {
                            s.classList.remove('active');
                        }
                    });
                });
                
                star.addEventListener('mouseenter', function() {
                    const rating = parseInt(this.getAttribute('data-rating'));
                    
                    // Update star display on hover
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.classList.add('hover');
                        } else {
                            s.classList.remove('hover');
                        }
                    });
                });
            });
            
            // Remove hover effect when leaving the rating area
            document.querySelector('.rating').addEventListener('mouseleave', function() {
                stars.forEach(s => {
                    s.classList.remove('hover');
                });
            });
        });
        
        function openFeedbackModal(appointmentId, rating = 0, comments = '') {
            document.getElementById('feedback_appointment_id').value = appointmentId;
            document.getElementById('rating').value = rating;
            document.getElementById('comments').value = comments;
            document.getElementById('ratingValue').textContent = rating + '/5';
            
            // Update star display
            const stars = document.querySelectorAll('.star');
            stars.forEach((s, index) => {
                if (index < rating) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
            
            document.getElementById('feedbackModal').style.display = 'block';
        }
        
        function closeFeedbackModal() {
            document.getElementById('feedbackModal').style.display = 'none';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('feedbackModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>