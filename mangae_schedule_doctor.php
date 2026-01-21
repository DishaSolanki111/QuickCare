<?php
// ================== SESSION & ACCESS CONTROL ==================
session_start();
if (
    !isset($_SESSION['LOGGED_IN']) ||
    $_SESSION['LOGGED_IN'] !== true ||
    !isset($_SESSION['USER_TYPE']) ||
    $_SESSION['USER_TYPE'] !== 'doctor'
) {
    header("Location: login.php");
    exit();
}

// ================== DATABASE CONNECTION ==================
include 'config.php';


// ================== DOCTOR INFO ==================
 $doctor_id = $_SESSION['DOCTOR_ID'];
 $doctor_name = "Doctor";

 $doc_sql = "SELECT FIRST_NAME, LAST_NAME FROM doctor_tbl WHERE DOCTOR_ID = ?";
 $doc_stmt = $conn->prepare($doc_sql);
 $doc_stmt->bind_param("i", $doctor_id);
 $doc_stmt->execute();
 $doc_result = $doc_stmt->get_result();

if ($doc_result->num_rows === 1) {
    $doc = $doc_result->fetch_assoc();
    $doctor_name = htmlspecialchars($doc['FIRST_NAME'] . ' ' . $doc['LAST_NAME']);
}
 $doc_stmt->close();

// ================== HANDLE SCHEDULE ADDITION ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_schedule'])) {
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $available_day = $_POST['available_day'];
    
    // Get a receptionist ID (using ID 1 as default)
    $receptionist_id = 1;
    
    $add_sql = "INSERT INTO doctor_schedule_tbl (DOCTOR_ID, RECEPTIONIST_ID, START_TIME, END_TIME, AVAILABLE_DAY) 
                VALUES (?, ?, ?, ?, ?)";
    $add_stmt = $conn->prepare($add_sql);
    $add_stmt->bind_param("iisss", $doctor_id, $receptionist_id, $start_time, $end_time, $available_day);
    
    if ($add_stmt->execute()) {
        $success_message = "Schedule added successfully!";
    } else {
        $error_message = "Error adding schedule: " . $conn->error;
    }
    $add_stmt->close();
}

// ================== HANDLE SCHEDULE UPDATE ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_schedule'])) {
    $schedule_id = $_POST['schedule_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $available_day = $_POST['available_day'];
    
    $update_sql = "UPDATE doctor_schedule_tbl SET 
                   START_TIME = ?, 
                   END_TIME = ?, 
                   AVAILABLE_DAY = ? 
                   WHERE SCHEDULE_ID = ? AND DOCTOR_ID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssii", $start_time, $end_time, $available_day, $schedule_id, $doctor_id);
    
    if ($update_stmt->execute()) {
        $success_message = "Schedule updated successfully!";
    } else {
        $error_message = "Error updating schedule: " . $conn->error;
    }
    $update_stmt->close();
}

// ================== HANDLE SCHEDULE DELETION ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_schedule'])) {
    $schedule_id = $_POST['schedule_id'];
    
    $delete_sql = "DELETE FROM doctor_schedule_tbl WHERE SCHEDULE_ID = ? AND DOCTOR_ID = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $schedule_id, $doctor_id);
    
    if ($delete_stmt->execute()) {
        $success_message = "Schedule deleted successfully!";
    } else {
        $error_message = "Error deleting schedule: " . $conn->error;
    }
    $delete_stmt->close();
}

// ================== FETCH DOCTOR SCHEDULE ==================
 $schedule_query = "SELECT * FROM doctor_schedule_tbl WHERE DOCTOR_ID = ? ORDER BY FIELD(AVAILABLE_DAY, 'MON', 'TUE', 'WED', 'THUR', 'FRI', 'SAT', 'SUN')";
 $schedule_stmt = $conn->prepare($schedule_query);
 $schedule_stmt->bind_param("i", $doctor_id);
 $schedule_stmt->execute();
 $schedule_result = $schedule_stmt->get_result();

 $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedule - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0066cc;
            --primary-dark: #0052a3;
            --primary-light: #e6f2ff;
            --secondary: #00a8cc;
            --accent: #00a86b;
            --warning: #ff6b6b;
            --dark: #1a3a5f;
            --light: #f8fafc;
            --white: #ffffff;
            --text: #2c5282;
            --text-light: #4a6fa5;
            --gradient-1: linear-gradient(135deg, #0066cc 0%, #00a8cc 100%);
            --gradient-2: linear-gradient(135deg, #00a8cc 0%, #00a86b 100%);
            --gradient-3: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --shadow-xl: 0 20px 25px rgba(0,0,0,0.1);
            --shadow-2xl: 0 25px 50px rgba(0,0,0,0.25);
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background-color: #f5f8fa;
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

       

        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 0;
            min-height: 100vh;
        }

        

        /* Schedule Content */
        .schedule-content {
            padding: 30px;
        }

        .schedule-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .schedule-header h2 {
            font-size: 28px;
            color: var(--dark);
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-success {
            background-color: var(--accent);
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

        .btn-warning {
            background-color: var(--warning-color);
            color: white;
        }

        .btn-warning:hover {
            background-color: #e67e22;
        }

        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        .schedule-card {
            background: var(--white);
            border-radius: 12px;
            padding: 25px;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
        }

        .schedule-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .schedule-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .schedule-card-header h3 {
            font-size: 20px;
            color: var(--dark);
            margin: 0;
        }

        .day-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            background-color: rgba(0, 102, 204, 0.1);
            color: var(--primary);
        }

        .schedule-time {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            color: #666;
        }

        .schedule-time i {
            margin-right: 10px;
            color: var(--primary);
        }

        .schedule-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #777;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow-md);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
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

        /* Modal Styles */
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
            border-radius: 12px;
            box-shadow: var(--shadow-xl);
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 102, 204, 0.2);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar-header h2 {
                display: none;
            }
            
            .sidebar-nav a span {
                display: none;
            }
            
            .sidebar-nav a {
                justify-content: center;
            }
            
            .sidebar-nav a i {
                margin: 0;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .schedule-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .topbar {
                padding: 15px 20px;
            }
            
            .schedule-content {
                padding: 20px;
            }
            
            .schedule-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .schedule-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--dark);
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'doctor_sidebar.php'; ?>
    <?php include 'doctor_header.php'; ?>
    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Schedule Content -->
        <div class="schedule-content">
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
            
            <!-- Schedule Header -->
            <div class="schedule-header">
                <h2>My Weekly Schedule</h2>
                <button class="btn btn-primary" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add Schedule
                </button>
            </div>
            
            <!-- Schedule Grid -->
            <?php if ($schedule_result->num_rows > 0): ?>
                <div class="schedule-grid">
                    <?php while ($schedule = $schedule_result->fetch_assoc()): ?>
                        <div class="schedule-card">
                            <div class="schedule-card-header">
                                <h3>
                                    <?php 
                                    $day_name = '';
                                    switch($schedule['AVAILABLE_DAY']) {
                                        case 'MON': $day_name = 'Monday'; break;
                                        case 'TUE': $day_name = 'Tuesday'; break;
                                        case 'WED': $day_name = 'Wednesday'; break;
                                        case 'THUR': $day_name = 'Thursday'; break;
                                        case 'FRI': $day_name = 'Friday'; break;
                                        case 'SAT': $day_name = 'Saturday'; break;
                                        case 'SUN': $day_name = 'Sunday'; break;
                                    }
                                    echo $day_name;
                                    ?>
                                </h3>
                                <span class="day-badge"><?php echo $schedule['AVAILABLE_DAY']; ?></span>
                            </div>
                            
                            <div class="schedule-time">
                                <i class="far fa-clock"></i>
                                <span><?php echo date('h:i A', strtotime($schedule['START_TIME'])); ?> - <?php echo date('h:i A', strtotime($schedule['END_TIME'])); ?></span>
                            </div>
                            
                            <div class="schedule-actions">
                                <button class="btn btn-warning" onclick="openEditModal(<?php echo $schedule['SCHEDULE_ID']; ?>, '<?php echo $schedule['START_TIME']; ?>', '<?php echo $schedule['END_TIME']; ?>', '<?php echo $schedule['AVAILABLE_DAY']; ?>')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="schedule_id" value="<?php echo $schedule['SCHEDULE_ID']; ?>">
                                    <button type="submit" name="delete_schedule" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this schedule?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="far fa-calendar-times"></i>
                    <h3>No Schedule Found</h3>
                    <p>You haven't added any schedule yet. Click the "Add Schedule" button to get started.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Add Schedule Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddModal()">&times;</span>
            <h2>Add New Schedule</h2>
            <form method="POST" action="mangae_schedule_doctor.php">
                <input type="hidden" name="add_schedule" value="1">
                
                <div class="form-group">
                    <label for="available_day">Day</label>
                    <select class="form-control" id="available_day" name="available_day" required>
                        <option value="">Select Day</option>
                        <option value="MON">Monday</option>
                        <option value="TUE">Tuesday</option>
                        <option value="WED">Wednesday</option>
                        <option value="THUR">Thursday</option>
                        <option value="FRI">Friday</option>
                        <option value="SAT">Saturday</option>
                        <option value="SUN">Sunday</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="start_time">Start Time</label>
                    <input type="time" class="form-control" id="start_time" name="start_time" required>
                </div>
                
                <div class="form-group">
                    <label for="end_time">End Time</label>
                    <input type="time" class="form-control" id="end_time" name="end_time" required>
                </div>
                
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Add Schedule
                </button>
            </form>
        </div>
    </div>
    
    <!-- Edit Schedule Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Schedule</h2>
            <form method="POST" action="mangae_schedule_doctor.php">
                <input type="hidden" id="edit_schedule_id" name="schedule_id">
                <input type="hidden" name="update_schedule" value="1">
                
                <div class="form-group">
                    <label for="edit_available_day">Day</label>
                    <select class="form-control" id="edit_available_day" name="available_day" required>
                        <option value="">Select Day</option>
                        <option value="MON">Monday</option>
                        <option value="TUE">Tuesday</option>
                        <option value="WED">Wednesday</option>
                        <option value="THUR">Thursday</option>
                        <option value="FRI">Friday</option>
                        <option value="SAT">Saturday</option>
                        <option value="SUN">Sunday</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_start_time">Start Time</label>
                    <input type="time" class="form-control" id="edit_start_time" name="start_time" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_end_time">End Time</label>
                    <input type="time" class="form-control" id="edit_end_time" name="end_time" required>
                </div>
                
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Update Schedule
                </button>
            </form>
        </div>
    </div>

    <script>
        // Modal functions
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }
        
        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }
        
        function openEditModal(scheduleId, startTime, endTime, availableDay) {
            document.getElementById('edit_schedule_id').value = scheduleId;
            document.getElementById('edit_start_time').value = startTime;
            document.getElementById('edit_end_time').value = endTime;
            document.getElementById('edit_available_day').value = availableDay;
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                        sidebar.classList.remove('active');
                    }
                }
            });
            
            // Close modals when clicking outside
            window.onclick = function(event) {
                const addModal = document.getElementById('addModal');
                const editModal = document.getElementById('editModal');
                
                if (event.target == addModal) {
                    addModal.style.display = 'none';
                }
                if (event.target == editModal) {
                    editModal.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>