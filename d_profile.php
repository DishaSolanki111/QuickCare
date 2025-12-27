<?php
session_start();

/* ================= AUTH CHECK ================= */
if (
    !isset($_SESSION['LOGGED_IN']) ||
    $_SESSION['LOGGED_IN'] !== true ||
    $_SESSION['USER_TYPE'] !== 'doctor' ||
    !isset($_SESSION['DOCTOR_ID'])
) {
    header("Location: login_for_all.php");
    exit();
}

include 'config.php';

 $doctor_id = $_SESSION['DOCTOR_ID'];

/* ================= DOCTOR PROFILE ================= */
 $sql = "
SELECT 
    d.DOCTOR_ID,
    d.FIRST_NAME,
    d.LAST_NAME,
    d.EMAIL,
    d.PHONE,
    d.GENDER,
    d.DOB,
    d.DOJ,
    d.USERNAME,
    d.PROFILE_IMAGE,
    d.EDUCATION,
    s.SPECIALISATION_NAME
FROM doctor_tbl d
LEFT JOIN specialisation_tbl s 
    ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
WHERE d.DOCTOR_ID = ?
";

 $stmt = $conn->prepare($sql);
 $stmt->bind_param("i", $doctor_id);
 $stmt->execute();
 $result = $stmt->get_result();
 $doctor = $result->fetch_assoc();
 $stmt->close();

if (!$doctor) {
    die("Doctor record not found.");
}

/* ================= STATS ================= */
 $total_appointments = 0;
 $patients_treated = 0;
 $total_prescriptions = 0;
 $avg_rating = 0;

 $stat1 = $conn->prepare("
    SELECT COUNT(*) total 
    FROM appointment_tbl 
    WHERE DOCTOR_ID = ?
");
 $stat1->bind_param("i", $doctor_id);
 $stat1->execute();
 $stat1->bind_result($total_appointments);
 $stat1->fetch();
 $stat1->close();

 $stat2 = $conn->prepare("
    SELECT COUNT(DISTINCT PATIENT_ID) total 
    FROM appointment_tbl 
    WHERE DOCTOR_ID = ?
");
 $stat2->bind_param("i", $doctor_id);
 $stat2->execute();
 $stat2->bind_result($patients_treated);
 $stat2->fetch();
 $stat2->close();

// Get total prescriptions
 $stat3 = $conn->prepare("
    SELECT COUNT(DISTINCT p.PRESCRIPTION_ID) total
    FROM prescription_tbl p
    JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
    WHERE a.DOCTOR_ID = ?
");
 $stat3->bind_param("i", $doctor_id);
 $stat3->execute();
 $stat3->bind_result($total_prescriptions);
 $stat3->fetch();
 $stat3->close();

// Get average rating
 $stat4 = $conn->prepare("
    SELECT AVG(RATING) avg_rating
    FROM feedback_tbl f
    JOIN appointment_tbl a ON f.APPOINTMENT_ID = a.APPOINTMENT_ID
    WHERE a.DOCTOR_ID = ? AND f.RATING IS NOT NULL
");
 $stat4->bind_param("i", $doctor_id);
 $stat4->execute();
 $stat4->bind_result($avg_rating);
 $stat4->fetch();
 $stat4->close();

// Format rating to 1 decimal place
 $avg_rating = number_format($avg_rating, 1);

// Calculate years of experience
 $years_of_experience = date('Y') - date('Y', strtotime($doctor['DOJ']));
 $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile - QuickCare</title>
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
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

body {
    background-color: #f5f8fa;
    color: var(--text);
    line-height: 1.6;
    overflow-x: hidden;
}

/* Sidebar Styles */
.logo-img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin-bottom: 15px;
    object-fit: cover;
    border: 3px solid var(--primary-light);
}

.container {
    display: flex;
    min-height: 100vh;
}

/* Sidebar Styles - From doctor_dashboard.php */
.sidebar {
    width: 250px;
    background: #072D44;
    min-height: 100vh;
    color: white;
    padding-top: 30px;
    position: fixed;
}

.sidebar h2 {
    text-align: center;
    margin-bottom: 40px;
    color: #9CCDD8;
}

.sidebar a {
    display: block;
    padding: 15px 25px;
    color: #D0D7E1;
    text-decoration: none;
    font-size: 17px;
    border-left: 4px solid transparent;
}

.sidebar a:hover, .sidebar a.active {
    background: #064469;
    border-left: 4px solid #9CCDD8;
    color: white;
}

.logout-btn:hover{
    background-color: var(--light-blue);
}

.logout-btn {
    display: block;
    width: 80%;
    margin: 20px auto 0 auto;
    padding: 10px;
    background-color: var(--soft-blue);
    color: var(--white);    
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    text-align: center;
    transition: background-color 0.3s;
}

/* Main Content */
.main-content {
    margin-left: 260px;
    padding: 0;
    min-height: 100vh;
}

/* Header */
.topbar {
    background: var(--white);
    padding: 20px 30px;
    box-shadow: var(--shadow-md);
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 50;
}

.topbar h1 {
    color: var(--dark);
    font-size: 24px;
    font-weight: 700;
}

.topbar-right {
    display: flex;
    align-items: center;
}

.notification-icon {
    position: relative;
    margin-right: 20px;
    color: var(--text);
    font-size: 20px;
    cursor: pointer;
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: var(--warning);
    color: var(--white);
    font-size: 10px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-info {
    display: flex;
    align-items: center;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 12px;
    border: 2px solid var(--primary-light);
}

.user-details h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--dark);
    margin: 0;
}

.user-details p {
    font-size: 13px;
    color: var(--text-light);
    margin: 0;
}

/* Dashboard Content */
.dashboard-content {
    padding: 30px;
}

/* Profile Card Styles */
.profile-card {
    background: var(--white);
    border-radius: 12px;
    padding: 25px;
    box-shadow: var(--shadow-md);
    margin-bottom: 25px;
}

.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e2e8f0;
}

.profile-header h3 {
    font-size: 20px;
    color: var(--dark);
}

.doctor-id {
    background-color: #edf2f7;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 14px;
    color: #4a5568;
}

/* Stats Section */
.stats-section {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: var(--white);
    border-radius: 12px;
    padding: 20px;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-xl);
}

.stat-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.stat-card h3 {
    font-size: 16px;
    color: var(--text);
    margin: 0;
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
}

.appointments-icon {
    background: rgba(0, 102, 204, 0.1);
    color: var(--primary);
}

.patients-icon {
    background: rgba(0, 168, 107, 0.1);
    color: var(--accent);
}

.prescriptions-icon {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
}

.rating-icon {
    background: rgba(255, 87, 34, 0.1);
    color: #ff5722;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--dark);
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    color: var(--text-light);
}

/* Information Sections */
.info-section {
    background: var(--white);
    border-radius: 12px;
    padding: 25px;
    box-shadow: var(--shadow-md);
    margin-bottom: 25px;
}

.info-section h3 {
    font-size: 18px;
    color: var(--dark);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #e2e8f0;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-item label {
    font-size: 14px;
    color: var(--text-light);
    margin-bottom: 5px;
}

.info-item .value {
    font-size: 16px;
    color: var(--dark);
    font-weight: 500;
}

/* Responsive Design */
@media (max-width: 992px) {
    .sidebar {
        width: 70px;
    }
    
    .sidebar h2 {
        display: none;
    }
    
    .sidebar a span {
        display: none;
    }
    
    .sidebar a {
        justify-content: center;
    }
    
    .main-content {
        margin-left: 70px;
    }
    
    .stats-section {
        grid-template-columns: repeat(2, 1fr);
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
    
    .dashboard-content {
        padding: 20px;
    }
    
    .stats-section {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
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
<div class="sidebar">
    <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
    <h2>QuickCare</h2>

    <a href="doctor_dashboard.php" >Dashboard</a>
    <a href="d_profile.php" class="active">My Profile</a>
    <a href="mangae_schedule_doctor.php">Manage Schedule</a>
    <a href="appointment_doctor.php">Manage Appointments</a>
    <a href="manage_prescriptions.php">Manage Prescription</a>
    <a href="#">View Medicine</a>
    <a href="doctor_feedback.php">View Feedback</a>
     <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
</div>

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <header class="topbar">
        <button class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <h1>My Profile</h1>
        
        <div class="topbar-right">
            <div class="notification-icon">
                <i class="far fa-bell"></i>
                <span class="notification-badge">3</span>
            </div>
            
            <div class="user-info">
                <img src="<?php echo htmlspecialchars($doctor['PROFILE_IMAGE'] ?: 'https://picsum.photos/seed/doctor/40/40.jpg'); ?>" alt="Doctor" class="user-avatar">
                <div class="user-details">
                    <h3>Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></h3>
                    <p><?php echo date("F j, Y"); ?></p>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Dashboard Content -->
    <div class="dashboard-content">
        <div class="profile-card">
            <div class="profile-header">
                <h3>Doctor Profile</h3>
                <div class="doctor-id">Doctor ID: DOC00<?php echo htmlspecialchars($doctor['DOCTOR_ID']); ?></div>
            </div>
            
            <!-- Stats Section -->
            <div class="stats-section">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Appointments</h3>
                        <div class="stat-icon appointments-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?php echo $total_appointments; ?></div>
                    <div class="stat-label">Total Appointments</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Patients</h3>
                        <div class="stat-icon patients-icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?php echo $patients_treated; ?></div>
                    <div class="stat-label">Patients Treated</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Prescriptions</h3>
                        <div class="stat-icon prescriptions-icon">
                            <i class="fas fa-prescription"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?php echo $total_prescriptions; ?></div>
                    <div class="stat-label">Total Prescriptions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header">
                        <h3>Rating</h3>
                        <div class="stat-icon rating-icon">
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    <div class="stat-value"><?php echo $avg_rating; ?></div>
                    <div class="stat-label">Average Rating</div>
                </div>
            </div>
        </div>
        
        <!-- Personal Information -->
        <div class="info-section">
            <h3>Personal Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Name</label>
                    <div class="value">Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']); ?></div>
                </div>
                <div class="info-item">
                    <label>Birthday</label>
                    <div class="value"><?php echo date('F d, Y', strtotime($doctor['DOB'])); ?></div>
                </div>
                <div class="info-item">
                    <label>Gender</label>
                    <div class="value"><?php echo htmlspecialchars($doctor['GENDER']); ?></div>
                </div>
                <div class="info-item">
                    <label>Date Joined</label>
                    <div class="value"><?php echo date('F d, Y', strtotime($doctor['DOJ'])); ?></div>
                </div>
                <div class="info-item">
                    <label>Phone</label>
                    <div class="value"><?php echo htmlspecialchars($doctor['PHONE']); ?></div>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <div class="value"><?php echo htmlspecialchars($doctor['EMAIL']); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Professional Information -->
        <div class="info-section">
            <h3>Professional Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Specialization</label>
                    <div class="value"><?php echo htmlspecialchars($doctor['SPECIALISATION_NAME']); ?></div>
                </div>
                <div class="info-item">
                    <label>Experience</label>
                    <div class="value"><?php echo $years_of_experience; ?> Years</div>
                </div>
                <div class="info-item">
                    <label>Education</label>
                    <div class="value"><?php echo htmlspecialchars($doctor['EDUCATION']); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Mobile menu toggle
const menuToggle = document.getElementById('menuToggle');
const sidebar = document.querySelector('.sidebar');

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
</script>
</body>
</html>