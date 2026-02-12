<?php
// Detect current page
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Doctor Sidebar - QuickCare</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
:root {
    --dark-blue: #072D44;
    --mid-blue: #064469;
    --soft-blue: #5790AB;
    --light-blue: #9CCDD8;
    --gray-blue: #D0D7E1;
    --white: #ffffff;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
}

body {
    background-color: #f5f8fa;
    color: #333;
}

/* Sidebar */
.sidebar { 
    width: 250px; 
    background: var(--dark-blue);
    height: 100vh; 
    color: white; 
    padding-top: 30px;
    position: fixed; 
    left: 0; 
    top: 0; 
    overflow-y: auto;   /* ✅ SCROLLBAR ENABLED */
    z-index: 1000;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

/* ✅ CUSTOM SCROLLBAR */
.sidebar::-webkit-scrollbar {
    width: 8px;
}
.sidebar::-webkit-scrollbar-track {
    background: var(--mid-blue);
}
.sidebar::-webkit-scrollbar-thumb {
    background: var(--light-blue);
    border-radius: 4px;
}

/* Logo */
.logo-img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin: 0 auto 15px auto;
    display: block;
    border: 3px solid var(--light-blue);
}

.sidebar h2 {
    text-align: center;
    margin-bottom: 40px;
    color: var(--light-blue);
}

/* Links */
.sidebar a {
    display: block;
    padding: 15px 25px;
    color: var(--gray-blue);
    text-decoration: none;
    font-size: 17px;
    overflow-y: auto;  /* ✅ SCROLLBAR ENABLED */
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
}
/* ✅ CUSTOM SCROLLBAR (NOT REMOVED) */
.sidebar::-webkit-scrollbar {
    width: 8px;
}
.sidebar::-webkit-scrollbar-track {
    background: var(--mid-blue);
}
.sidebar::-webkit-scrollbar-thumb {
    background: var(--light-blue);
    border-radius: 4px;
}
/* Active / Hover */
.sidebar a:hover,
.sidebar a.active {
    background: var(--mid-blue);
    border-left: 4px solid var(--light-blue);
    color: var(--white);
}

/* Logout */
.logout-btn {
    display: block;
    width: 80%;
    margin: 25px auto;
    padding: 12px;
    background-color: var(--soft-blue);
    color: var(--white);
    border-radius: 6px;
    text-align: center;
    font-size: 16px;
    text-decoration: none;
}

.logout-btn:hover {
    background-color: var(--light-blue);
}

/* Responsive */
@media (max-width: 992px) {
    .sidebar { width: 70px; }
    .sidebar h2 { display: none; }
}

@media (max-width: 768px) {
    .sidebar { transform: translateX(-100%); }
    .sidebar.active { transform: translateX(0); }
}
</style>
</head>

<body>

<!-- DOCTOR SIDEBAR -->
<div class="sidebar" id="sidebar">
    <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img">
    <h2>QuickCare</h2>

    <a href="index.php" class="<?= ($currentPage == 'index.php') ? 'active' : '' ?>">Home</a>

    <a href="doctor_dashboard.php" class="<?= ($currentPage == 'doctor_dashboard.php') ? 'active' : '' ?>">Dashboard</a>

    <a href="doctor_profile.php" class="<?= ($currentPage == 'doctor_profile.php') ? 'active' : '' ?>">Profile</a>

    <a href="mangae_schedule_doctor.php" class="<?= ($currentPage == 'mangae_schedule_doctor.php') ? 'active' : '' ?>">Schedule</a>

    <a href="appointment_doctor.php" class="<?= ($currentPage == 'appointment_doctor.php') ? 'active' : '' ?>">Appointments</a>

    <a href="manage_prescriptions.php" class="<?= ($currentPage == 'manage_prescriptions.php') ? 'active' : '' ?>">Prescription</a>

    <a href="doctor_feedback.php" class="<?= ($currentPage == 'doctor_feedback.php') ? 'active' : '' ?>">Feedback</a>

    <a href="logout.php" class="logout-btn">Logout</a>
</div>

</body>
</html>
