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

// ================== HANDLE APPOINTMENT STATUS UPDATE ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $status = $_POST['status'];
    
    $update_sql = "UPDATE appointment_tbl SET STATUS = ? WHERE APPOINTMENT_ID = ? AND DOCTOR_ID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sii", $status, $appointment_id, $doctor_id);
    
    if ($update_stmt->execute()) {
        $success_message = "Appointment status updated successfully!";
    } else {
        $error_message = "Error updating appointment status: " . $conn->error;
    }
    $update_stmt->close();
}

// ================== FETCH APPOINTMENTS ==================
 $today = date('Y-m-d');

// Today's appointments
 $today_appointments = [];
 $today_sql = "
    SELECT a.*, p.FIRST_NAME as PAT_FNAME, p.LAST_NAME as PAT_LNAME, p.PHONE as PAT_PHONE, p.EMAIL as PAT_EMAIL
    FROM appointment_tbl a
    JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
    WHERE a.DOCTOR_ID = ? AND DATE(a.APPOINTMENT_DATE) = ?
    ORDER BY a.APPOINTMENT_TIME
";
 $today_stmt = $conn->prepare($today_sql);
 $today_stmt->bind_param("is", $doctor_id, $today);
 $today_stmt->execute();
 $today_result = $today_stmt->get_result();

if ($today_result->num_rows > 0) {
    while ($row = $today_result->fetch_assoc()) {
        $today_appointments[] = $row;
    }
}
 $today_stmt->close();

// Upcoming appointments
 $upcoming_appointments = [];
 $upcoming_sql = "
    SELECT a.*, p.FIRST_NAME as PAT_FNAME, p.LAST_NAME as PAT_LNAME, p.PHONE as PAT_PHONE, p.EMAIL as PAT_EMAIL
    FROM appointment_tbl a
    JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
    WHERE a.DOCTOR_ID = ? AND a.APPOINTMENT_DATE < ?
    ORDER BY a.APPOINTMENT_DATE, a.APPOINTMENT_TIME
";
 $upcoming_stmt = $conn->prepare($upcoming_sql);
 $upcoming_stmt->bind_param("is", $doctor_id, $today);
 $upcoming_stmt->execute();
 $upcoming_result = $upcoming_stmt->get_result();

if ($upcoming_result->num_rows > 0) {
    while ($row = $upcoming_result->fetch_assoc()) {
        $upcoming_appointments[] = $row;
    }
}
 $upcoming_stmt->close();

// Past appointments
 $past_appointments = [];
 $past_sql = "
    SELECT a.*, p.FIRST_NAME as PAT_FNAME, p.LAST_NAME as PAT_LNAME, p.PHONE as PAT_PHONE, p.EMAIL as PAT_EMAIL
    FROM appointment_tbl a
    JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
    WHERE a.DOCTOR_ID = ? AND a.APPOINTMENT_DATE < ?
    ORDER BY a.APPOINTMENT_DATE DESC, a.APPOINTMENT_TIME DESC
    LIMIT 20
";
 $past_stmt = $conn->prepare($past_sql);
 $past_stmt->bind_param("is", $doctor_id, $today);
 $past_stmt->execute();
 $past_result = $past_stmt->get_result();

if ($past_result->num_rows > 0) {
    while ($row = $past_result->fetch_assoc()) {
        $past_appointments[] = $row;
    }
}
 $past_stmt->close();

 $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Appointments - QuickCare</title>
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
            --info-color: #17a2b8;
        }

        * {
        ;
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
            margin-left: 280px;
            padding: 0;
            min-height: 100vh;
        }

        
        /* Appointment Content */
        .appointment-content {
            padding: 30px;
        }

        .tabs {
            display: flex;
            margin-bottom: 25px;
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .tab {
            flex: 1;
            padding: 15px 20px;
            text-align: center;
            cursor: pointer;
            background: var(--white);
            color: var(--text-light);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .tab.active {
            background: var(--primary);
            color: var(--white);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .appointment-card {
            background: var(--white);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
        }

        .appointment-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .appointment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .patient-info h3 {
            font-size: 20px;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .patient-info p {
            color: var(--text-light);
            margin-bottom: 5px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-scheduled {
            background-color: rgba(0, 102, 204, 0.1);
            color: var(--primary);
        }

        .status-completed {
            background-color: rgba(0, 168, 107, 0.1);
            color: var(--accent);
        }

        .status-cancelled {
            background-color: rgba(255, 107, 107, 0.1);
            color: var(--warning);
        }

        .appointment-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .appointment-detail {
            display: flex;
            align-items: center;
            color: #666;
        }

        .appointment-detail i {
            margin-right: 10px;
            color: var(--primary);
        }

        .appointment-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 15px;
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
            
            .appointment-details {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
    
            
            .main-content {
                margin-left: 0;
            }
            
          
            .appointment-content {
                padding: 20px;
            }
            
            .appointment-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .appointment-actions {
                flex-wrap: wrap;
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
 
 <?php include 'doctor_sidebar.php'; ?>
 <?php include 'doctor_header.php'; ?>
    <!-- Main Content -->
    <div class="main-content">
        
        
        <!-- Appointment Content -->
        <div class="appointment-content">
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
            
            <!-- Tabs -->
            <div class="tabs">
                <div class="tab active" data-tab="today">Today's Appointments</div>
                <div class="tab" data-tab="upcoming">Upcoming</div>
                <div class="tab" data-tab="past">Past Appointments</div>
            </div>
            
            <!-- Tab Content -->
            <div class="tab-content active" id="today">
                <?php if (count($today_appointments) > 0): ?>
                    <?php foreach ($today_appointments as $appointment): ?>
                        <div class="appointment-card">
                            <div class="appointment-header">
                                <div class="patient-info">
                                    <h3><?php echo htmlspecialchars($appointment['PAT_FNAME'] . ' ' . $appointment['PAT_LNAME']); ?></h3>
                                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($appointment['PAT_PHONE']); ?></p>
                                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($appointment['PAT_EMAIL']); ?></p>
                                </div>
                                <span class="status-badge status-<?php echo strtolower($appointment['STATUS']); ?>">
                                    <?php echo $appointment['STATUS']; ?>
                                </span>
                            </div>
                            
                            <div class="appointment-details">
                                <div class="appointment-detail">
                                    <i class="far fa-calendar"></i>
                                    <span><?php echo date('F d, Y', strtotime($appointment['APPOINTMENT_DATE'])); ?></span>
                                </div>
                                <div class="appointment-detail">
                                    <i class="far fa-clock"></i>
                                    <span><?php echo date('h:i A', strtotime($appointment['APPOINTMENT_TIME'])); ?></span>
                                </div>
                                
                            </div>
                            
                            <div class="appointment-actions">
                                <?php if ($appointment['STATUS'] === 'SCHEDULED'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['APPOINTMENT_ID']; ?>">
                                        <input type="hidden" name="status" value="COMPLETED">
                                        <button type="submit" name="update_status" class="btn btn-success">
                                            <i class="fas fa-check"></i> Mark as Completed
                                        </button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['APPOINTMENT_ID']; ?>">
                                        <input type="hidden" name="status" value="CANCELLED">
                                        <button type="submit" name="update_status" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    </form>
                                <?php endif; ?>
                                
                                <button class="btn btn-primary" onclick="viewPrescription(<?php echo $appointment['APPOINTMENT_ID']; ?>)">
                                    <i class="fas fa-file-medical"></i> View Prescription
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="far fa-calendar-times"></i>
                        <h3>No Appointments Today</h3>
                        <p>You don't have any appointments scheduled for today.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="tab-content" id="upcoming">
                <?php if (count($upcoming_appointments) > 0): ?>
                    <?php foreach ($upcoming_appointments as $appointment): ?>
                        <div class="appointment-card">
                            <div class="appointment-header">
                                <div class="patient-info">
                                    <h3><?php echo htmlspecialchars($appointment['PAT_FNAME'] . ' ' . $appointment['PAT_LNAME']); ?></h3>
                                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($appointment['PAT_PHONE']); ?></p>
                                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($appointment['PAT_EMAIL']); ?></p>
                                </div>
                                <span class="status-badge status-<?php echo strtolower($appointment['STATUS']); ?>">
                                    <?php echo $appointment['STATUS']; ?>
                                </span>
                                
                            </div>
                            
                            <div class="appointment-details">
                                <div class="appointment-detail">
                                    <i class="far fa-calendar"></i>
                                    <span><?php echo date('F d, Y', strtotime($appointment['APPOINTMENT_DATE'])); ?></span>
                                </div>
                                <div class="appointment-detail">
                                    <i class="far fa-clock"></i>
                                    <span><?php echo date('h:i A', strtotime($appointment['APPOINTMENT_TIME'])); ?></span>
                                </div>
                               
                            </div>
                            
                            <div class="appointment-actions">
                                <?php if ($appointment['STATUS'] === 'SCHEDULED'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['APPOINTMENT_ID']; ?>">
                                        <input type="hidden" name="status" value="CANCELLED">
                                        <button type="submit" name="update_status" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                            <i class="fas fa-times"></i> Cancel
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="far fa-calendar-times"></i>
                        <h3>No Upcoming Appointments</h3>
                        <p>You don't have any upcoming appointments scheduled.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="tab-content" id="past">
                <?php if (count($past_appointments) > 0): ?>
                    <?php foreach ($past_appointments as $appointment): ?>
                        <div class="appointment-card">
                            <div class="appointment-header">
                                <div class="patient-info">
                                    <h3><?php echo htmlspecialchars($appointment['PAT_FNAME'] . ' ' . $appointment['PAT_LNAME']); ?></h3>
                                    <p><i class="fas fa-phone"></i> <?php echo htmlspecialchars($appointment['PAT_PHONE']); ?></p>
                                    <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($appointment['PAT_EMAIL']); ?></p>
                                </div>
                                <span class="status-badge status-<?php echo strtolower($appointment['STATUS']); ?>">
                                    <?php echo $appointment['STATUS']; ?>
                                </span>
                            </div>
                            
                            <div class="appointment-details">
                                <div class="appointment-detail">
                                    <i class="far fa-calendar"></i>
                                    <span><?php echo date('F d, Y', strtotime($appointment['APPOINTMENT_DATE'])); ?></span>
                                </div>
                                <div class="appointment-detail">
                                    <i class="far fa-clock"></i>
                                    <span><?php echo date('h:i A', strtotime($appointment['APPOINTMENT_TIME'])); ?></span>
                                </div>
                                
                            </div>
                            
                            <div class="appointment-actions">
                                <button class="btn btn-primary" onclick="viewPrescription(<?php echo $appointment['APPOINTMENT_ID']; ?>)">
                                    <i class="fas fa-file-medical"></i> View Prescription
                                </button>
                                
                                <?php if ($appointment['STATUS'] === 'COMPLETED'): ?>
                                    <button class="btn btn-success" onclick="viewFeedback(<?php echo $appointment['APPOINTMENT_ID']; ?>)">
                                        <i class="fas fa-star"></i> View Feedback
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="far fa-calendar-times"></i>
                        <h3>No Past Appointments</h3>
                        <p>You don't have any past appointments.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        document.addEventListener('DOMContentLoaded', function() {
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
                });
            });
            
            // Mobile menu toggle
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
        });
        
        // View prescription function
        function viewPrescription(appointmentId) {
            // Redirect to prescription page
            window.location.href = 'manage_prescriptions.php?appointment=' + appointmentId;
        }
        
        // View feedback function
        function viewFeedback(appointmentId) {
            // Redirect to feedback page
            window.location.href = 'doctor_feedback.php?appointment=' + appointmentId;
        }
    </script>
</body>
</html>