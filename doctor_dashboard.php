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
// ================== DOCTOR BASIC INFO ==================
 $doctor_id = $_SESSION['DOCTOR_ID'];
 $doctor_name = "Doctor";

 $doc_sql = "SELECT FIRST_NAME, LAST_NAME, PROFILE_IMAGE FROM doctor_tbl WHERE DOCTOR_ID = ?";
 $doc_stmt = $conn->prepare($doc_sql);
 $doc_stmt->bind_param("i", $doctor_id);
 $doc_stmt->execute();
 $doc_result = $doc_stmt->get_result();

if ($doc_result->num_rows === 1) {
    $doc = $doc_result->fetch_assoc();
    $doctor_name = htmlspecialchars($doc['FIRST_NAME'] . ' ' . $doc['LAST_NAME']);
}
 $doc_stmt->close();

// ================== DASHBOARD METRICS ==================

// Today's Appointments
 $today_appointments = 0;
 $appt_sql = "
    SELECT COUNT(*) AS total 
    FROM appointment_tbl 
    WHERE DOCTOR_ID = ? AND DATE(APPOINTMENT_DATE) = CURDATE()
";
 $appt_stmt = $conn->prepare($appt_sql);
 $appt_stmt->bind_param("i", $doctor_id);
 $appt_stmt->execute();
 $appt_result = $appt_stmt->get_result();
if ($row = $appt_result->fetch_assoc()) {
    $today_appointments = $row['total'];
}
 $appt_stmt->close();

// Patients This Week
 $patients_this_week = 0;
 $week_sql = "
    SELECT COUNT(DISTINCT PATIENT_ID) AS total 
    FROM appointment_tbl 
    WHERE DOCTOR_ID = ?
      AND APPOINTMENT_DATE BETWEEN CURDATE() AND CURDATE() + INTERVAL 7 DAY
";
 $week_stmt = $conn->prepare($week_sql);
 $week_stmt->bind_param("i", $doctor_id);
 $week_stmt->execute();
 $week_result = $week_stmt->get_result();
if ($row = $week_result->fetch_assoc()) {
    $patients_this_week = $row['total'];
}
 $week_stmt->close();

 $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctor Dashboard - QuickCare</title>
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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
       
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            margin-top:-15px;
        }
        
        
        .welcome-msg {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .user-actions {
            display: flex;
            align-items: center;
        }
        
        .notification-btn {
            position: relative;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--dark-color);
            margin-right: 20px;
            cursor: pointer;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }
        
        .user-dropdown {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
.sidebar a {
    display: block;
    padding: 15px 25px;
    color: var(--gray-blue);
    text-decoration: none;
    font-size: 17px;
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
}
.sidebar a.active {
    background: var(--mid-blue);
    border-left: 4px solid var(--light-blue);
    color: var(--white);
}



        
/* Dashboard Content */
.dashboard-content {
    padding: 10px;
}

.welcome-section {
    margin-bottom: 30px;
    text-align: left;
}

.welcome-section h2 {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 5px;
}

.welcome-section p {
    color: var(--text-light);
    font-size: 16px;
}

/* Cards */
.cards-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
    margin-bottom: 40px;
}

.card {
    background: var(--white);
    border-radius: 12px;
    padding: 25px;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    height: 100%;
}

.card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: var(--gradient-1);
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-xl);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.card h3 {
    font-size: 18px;
    color: var(--text);
    margin: 0;
}

.card-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.appointments-icon {
    background: rgba(0, 102, 204, 0.1);
    color: var(--primary);
}

.feedback-icon {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
}

.patients-icon {
    background: rgba(0, 168, 107, 0.1);
    color: var(--accent);
}

.card-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 5px;
}

.card-change {
    font-size: 14px;
    color: var(--text-light);
    margin-top: auto;
}

/* Schedule Card */
.schedule-card {
    background: var(--white);
    border-radius: 12px;
    padding: 25px;
    box-shadow: var(--shadow-md);
    margin-bottom: 30px;
}

.schedule-card h3 {
    font-size: 20px;
    color: var(--dark);
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.schedule-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}

.schedule-info-item {
    flex: 1;
    text-align: center;
    padding: 0 15px;
}

.schedule-info-item h4 {
    font-size: 14px;
    color: var(--text-light);
    margin-bottom: 10px;
    font-weight: 500;
}

.schedule-info-item p {
    font-size: 20px;
    font-weight: 600;
    color: var(--dark);
    margin: 0;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .cards-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 992px) {
    .main-content {
        margin-left: 70px;
        width: calc(100% - 70px);
    }
    
    .cards-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
        width: 100%;
    }
    
    .topbar {
        padding: 15px 20px;
        flex-direction: column;
        align-items: flex-start;
    }
    
    .topbar-right {
        margin-top: 15px;
        align-self: flex-end;
    }
    
    .dashboard-content {
        padding: 20px;
    }
    
    .cards-container {
        grid-template-columns: 1fr;
    }
    
    .schedule-info {
        flex-direction: column;
    }
    
    .schedule-info-item {
        margin-bottom: 15px;
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    
    .schedule-info-item:last-child {
        border-bottom: none;
    }
}
</style>
</head>
<body>

<div class="container">

    <?php include 'doctor_sidebar.php'; ?>

    <div class="main-content">
        <?php include 'doctor_header.php'; ?>
        <!-- Dashboard Content -->
        <div class="dashboard-content">
            <div class="welcome-section">
                <h2>Dashboard Overview</h2>
                <p>Here's what's happening with your practice today.</p>
            </div>
            
            <div class="cards-container">
                <div class="card">
                    <div class="card-header">
                        <h3>Today's Appointments</h3>
                        <div class="card-icon appointments-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <div class="card-value"><?php echo $today_appointments; ?></div>
                    <div class="card-change">
                        <i class="fas fa-arrow-up"></i> 12% from last week
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Feedback Summary</h3>
                        <div class="card-icon feedback-icon">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <div class="card-value">⭐ 4.8</div>
                    <div class="card-change">
                        <i class="fas fa-arrow-up"></i> 0.3 from last month
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Patients This Week</h3>
                        <div class="card-icon patients-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="card-value"><?php echo $patients_this_week; ?></div>
                    <div class="card-change">
                        <i class="fas fa-arrow-up"></i> 5% from last week
                    </div>
                </div>
            </div>
            
            <div class="schedule-card">
                <h3>Schedule Summary</h3>
                <div class="schedule-info">
                    <div class="schedule-info-item">
                        <h4>Next Available Slot</h4>
                        <p>10:00 AM</p>
                    </div>
                    <div class="schedule-info-item">
                        <h4>Total Patients This Week</h4>
                        <p><?php echo $patients_this_week; ?></p>
                    </div>
                    <div class="schedule-info-item">
                        <h4>Available Slots Today</h4>
                        <p>5</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>