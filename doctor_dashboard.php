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

// Feedback average for this doctor
 $feedback_avg = 0;
 $feedback_sql = "SELECT AVG(f.RATING) AS avg_rating FROM feedback_tbl f 
    JOIN appointment_tbl a ON f.APPOINTMENT_ID = a.APPOINTMENT_ID 
    WHERE a.DOCTOR_ID = ?";
 $feedback_stmt = $conn->prepare($feedback_sql);
 $feedback_stmt->bind_param("i", $doctor_id);
 $feedback_stmt->execute();
 $feedback_result = $feedback_stmt->get_result();
 $row = $feedback_result->fetch_assoc();
if ($row && $row['avg_rating'] !== null) {
    $feedback_avg = round((float) $row['avg_rating'], 1);
}
 $feedback_stmt->close();

// Change percentages (compare with previous period)
 $appt_change = null;
 $week_sql2 = "SELECT COUNT(*) AS total FROM appointment_tbl WHERE DOCTOR_ID = ? AND DATE(APPOINTMENT_DATE) = DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
 $week_stmt2 = $conn->prepare($week_sql2);
 $week_stmt2->bind_param("i", $doctor_id);
 $week_stmt2->execute();
 $week_result2 = $week_stmt2->get_result();
if (($row2 = $week_result2->fetch_assoc()) && $row2['total'] > 0) {
    $last_week = (int) $row2['total'];
    $appt_change = $last_week > 0 ? round((($today_appointments - $last_week) / $last_week) * 100) : 0;
}
 $week_stmt2->close();

 $patients_change = null;
 $pat_sql = "SELECT COUNT(DISTINCT PATIENT_ID) AS total FROM appointment_tbl WHERE DOCTOR_ID = ? AND APPOINTMENT_DATE BETWEEN DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND CURDATE() - INTERVAL 1 DAY";
 $pat_stmt = $conn->prepare($pat_sql);
 $pat_stmt->bind_param("i", $doctor_id);
 $pat_stmt->execute();
 $pat_result = $pat_stmt->get_result();
 $row3 = $pat_result->fetch_assoc();
if ($row3 && $row3['total'] > 0) {
    $last_week_patients = (int) $row3['total'];
    $patients_change = $last_week_patients > 0 ? round((($patients_this_week - $last_week_patients) / $last_week_patients) * 100) : 0;
}
 $pat_stmt->close();

// Next available slot and available slots today
 $day_map = ['Monday' => 'MON', 'Tuesday' => 'TUE', 'Wednesday' => 'WED', 'Thursday' => 'THUR', 'Friday' => 'FRI', 'Saturday' => 'SAT', 'Sunday' => 'SUN'];
 $today_day = $day_map[date('l')];
 $next_slot = '-';
 $available_slots_today = 0;
 $slot_sql = "SELECT ds.START_TIME, ds.END_TIME FROM doctor_schedule_tbl ds WHERE ds.DOCTOR_ID = ? AND ds.AVAILABLE_DAY = ?";
 $slot_stmt = $conn->prepare($slot_sql);
 $slot_stmt->bind_param("is", $doctor_id, $today_day);
 $slot_stmt->execute();
 $slot_result = $slot_stmt->get_result();
if ($slot_result->num_rows > 0) {
    $slot_row = $slot_result->fetch_assoc();
    if ($slot_row && isset($slot_row['START_TIME'], $slot_row['END_TIME'])) {
        $start = strtotime($slot_row['START_TIME']);
        $end = strtotime($slot_row['END_TIME']);
    $today_date = date('Y-m-d');
    $booked_sql = "SELECT APPOINTMENT_TIME FROM appointment_tbl WHERE DOCTOR_ID = ? AND APPOINTMENT_DATE = ? AND STATUS = 'SCHEDULED'";
    $booked_stmt = $conn->prepare($booked_sql);
    $booked_stmt->bind_param("is", $doctor_id, $today_date);
    $booked_stmt->execute();
    $booked_result = $booked_stmt->get_result();
    $booked_times = [];
    while ($br = $booked_result->fetch_assoc()) {
        $booked_times[] = date('H:i', strtotime($br['APPOINTMENT_TIME']));
    }
    $booked_stmt->close();
    $slot_duration = 30 * 60; // 30 min in seconds
    $total_slots = 0;
    $found_first = false;
    for ($t = $start; $t < $end; $t += $slot_duration) {
        $ts = date('H:i', $t);
        if (!in_array($ts, $booked_times)) {
            $total_slots++;
            if (!$found_first) {
                $next_slot = date('h:i A', $t);
                $found_first = true;
            }
        }
    }
    $available_slots_today = $total_slots;
    }
}
 $slot_stmt->close();

// Next appointment (earliest upcoming SCHEDULED) with patient details
 $next_appointment = null;
 $next_sql = "
    SELECT a.APPOINTMENT_ID, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME,
           p.FIRST_NAME AS PAT_FNAME, p.LAST_NAME AS PAT_LNAME, p.PHONE AS PAT_PHONE, p.EMAIL AS PAT_EMAIL
    FROM appointment_tbl a
    JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
    WHERE a.DOCTOR_ID = ? AND a.STATUS = 'SCHEDULED'
      AND ( (a.APPOINTMENT_DATE > CURDATE()) OR (a.APPOINTMENT_DATE = CURDATE() AND a.APPOINTMENT_TIME >= CURTIME()) )
    ORDER BY a.APPOINTMENT_DATE, a.APPOINTMENT_TIME
    LIMIT 1
";
 $next_stmt = $conn->prepare($next_sql);
 $next_stmt->bind_param("i", $doctor_id);
 $next_stmt->execute();
 $next_result = $next_stmt->get_result();
if ($next_result->num_rows > 0) {
    $next_appointment = $next_result->fetch_assoc();
}
 $next_stmt->close();

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
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
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
    padding: 20px 20px 0 20px;
    margin-top: -15px;
}

/* Dashboard Content */
.dashboard-content {
    padding: 10px;
    padding-bottom: 0;
}

.welcome-section {
    margin-bottom: 16px;
    text-align: left;
    padding: 0;
}

.welcome-section h2 {
    font-size: 32px;
    font-weight: 700;
    color: var(--dark);
    margin: 0 0 10px 0;
    position: relative;
    display: inline-block;
}

.welcome-section h2:after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 0;
    width: 60px;
    height: 4px;
    background: var(--gradient-1);
    border-radius: 2px;
}

.welcome-section p {
    color: var(--text-light);
    font-size: 16px;
    max-width: 600px;
}

/* Cards Container */
.cards-container {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

/* Card Design */
.card {
    background: var(--white);
    border-radius: 0;
    padding: 16px;
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
    border-radius: 0;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-xl);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.card h3 {
    font-size: 15px;
    color: var(--text);
    margin: 0;
    font-weight: 600;
}

.card-icon {
    width: 44px;
    height: 44px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    box-shadow: var(--shadow-sm);
}

.appointments-icon {
    background: linear-gradient(135deg, rgba(0, 102, 204, 0.1) 0%, rgba(0, 168, 204, 0.1) 100%);
    color: var(--primary);
}

.feedback-icon {
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 152, 0, 0.1) 100%);
    color: #ffc107;
}

.patients-icon {
    background: linear-gradient(135deg, rgba(0, 168, 107, 0.1) 0%, rgba(46, 204, 113, 0.1) 100%);
    color: var(--accent);
}

.card-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 0;
    position: relative;
    z-index: 1;
}

.card-change {
    font-size: 14px;
    color: var(--text-light);
    margin-top: auto;
    display: flex;
    align-items: center;
    gap: 5px;
}

.card-change i {
    font-size: 12px;
}

.card-change.positive {
    color: var(--accent);
}

.card-change.negative {
    color: var(--warning);
}

/* Schedule Card */
.schedule-card {
    background: var(--white);
    border-radius: 0;
    padding: 30px;
    box-shadow: var(--shadow-md);
    margin-bottom: 0;
    position: relative;
    overflow: hidden;
}

.schedule-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: var(--gradient-2);
    border-radius: 0;
}

.schedule-card h3 {
    font-size: 22px;
    color: var(--dark);
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
    position: relative;
}

.schedule-card h3::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    width: 50px;
    height: 3px;
    background: var(--gradient-2);
    border-radius: 2px;
}

.schedule-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
}

.schedule-info-item {
    flex: 1;
    text-align: center;
    padding: 20px 15px;
    border-radius: 12px;
    background: var(--card-bg);
    margin: 0 10px;
    transition: all 0.3s ease;
}

.schedule-info-item:first-child {
    margin-left: 0;
}

.schedule-info-item:last-child {
    margin-right: 0;
}

.schedule-info-item:hover {
    transform: translateY(-3px);
    box-shadow: var(--shadow-md);
}

.schedule-info-item h4 {
    font-size: 14px;
    color: var(--text-light);
    margin-bottom: 15px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.schedule-info-item p {
    font-size: 24px;
    font-weight: 700;
    color: var(--dark);
    margin: 0;
}

/* Next Appointment patient details */
.next-appointment-card .next-appointment-details {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.next-appt-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 16px;
    background: var(--light);
    border-radius: 10px;
    border-left: 4px solid var(--accent);
}

.next-appt-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-light);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    min-width: 100px;
}

.next-appt-value {
    font-size: 15px;
    font-weight: 600;
    color: var(--dark);
    text-align: right;
    word-break: break-word;
}

.next-appointment-empty {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-light);
}

.next-appointment-empty i {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
}

.next-appointment-empty p {
    font-size: 16px;
    margin: 0;
}

/* Animation for numbers */
@keyframes countUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card-value, .schedule-info-item p {
    animation: countUp 0.5s ease-out;
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
        margin-left: 0;
        margin-right: 0;
    }
    
    .schedule-info-item:last-child {
        margin-bottom: 0;
    }
    
    .next-appt-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }
    
    .next-appt-value {
        text-align: left;
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
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Feedback Summary</h3>
                        <div class="card-icon feedback-icon">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <div class="card-value">⭐ <?php echo $feedback_avg > 0 ? $feedback_avg : '—'; ?></div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h3>Patients This Week</h3>
                        <div class="card-icon patients-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="card-value"><?php echo $patients_this_week; ?></div>
                </div>
            </div>
            
            <div class="schedule-card next-appointment-card">
                <h3>Next Appointment</h3>
                <?php if ($next_appointment): ?>
                <div class="next-appointment-details">
                    <div class="next-appt-row">
                        <span class="next-appt-label">Patient</span>
                        <span class="next-appt-value"><?php echo htmlspecialchars(trim($next_appointment['PAT_FNAME'] . ' ' . $next_appointment['PAT_LNAME'])); ?></span>
                    </div>
                    <div class="next-appt-row">
                        <span class="next-appt-label">Date & Time</span>
                        <span class="next-appt-value"><?php echo date('M d, Y', strtotime($next_appointment['APPOINTMENT_DATE'])); ?> · <?php echo date('h:i A', strtotime($next_appointment['APPOINTMENT_TIME'])); ?></span>
                    </div>
                    <?php if (!empty($next_appointment['PAT_PHONE'])): ?>
                    <div class="next-appt-row">
                        <span class="next-appt-label">Phone</span>
                        <span class="next-appt-value"><?php echo htmlspecialchars($next_appointment['PAT_PHONE']); ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($next_appointment['PAT_EMAIL'])): ?>
                    <div class="next-appt-row">
                        <span class="next-appt-label">Email</span>
                        <span class="next-appt-value"><?php echo htmlspecialchars($next_appointment['PAT_EMAIL']); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="next-appointment-empty">
                    <i class="fas fa-calendar-check"></i>
                    <p>No upcoming appointments</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>