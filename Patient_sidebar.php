<?php
// Detect current page
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Sidebar - QuickCare</title>

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
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
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
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }
    .sidebar.active {
        transform: translateX(0);
    }
}
</style>
</head>

<body>

<!-- PATIENT SIDEBAR -->
<div class="sidebar" id="sidebar">
    <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img">
    <h2>QuickCare</h2>

    <a href="index.php" class="<?= ($currentPage == 'index.php') ? 'active' : '' ?>">Home</a>

    <a href="patient.php" class="<?= ($currentPage == 'patient.php') ? 'active' : '' ?>">Dashboard</a>

    <a href="patient_profile.php" class="<?= ($currentPage == 'patient_profile.php') ? 'active' : '' ?>">Profile</a>

    <a href="view_doctor_patient.php" class="<?= ($currentPage == 'view_doctor_patient.php') ? 'active' : '' ?>">Doctor Profile</a>

    <a href="manage_appointments.php" class="<?= ($currentPage == 'manage_appointments.php') ? 'active' : '' ?>">Appointments</a>

    <a href="doctor_schedule.php" class="<?= ($currentPage == 'doctor_schedule.php') ? 'active' : '' ?>">Doctor Schedule</a>

    <a href="patinet_prescriptions.php" class="<?= ($currentPage == 'patinet_prescriptions.php') ? 'active' : '' ?>">Prescriptions</a>

    <a href="medicine_reminder.php" class="<?= ($currentPage == 'medicine_reminder.php') ? 'active' : '' ?>">Reminder</a>

    <a href="patient_payments.php" class="<?= ($currentPage == 'patient_payments.php') ? 'active' : '' ?>">Payments</a>

    <a href="patient_feedback.php" class="<?= ($currentPage == 'patient_feedback.php') ? 'active' : '' ?>">Feedback</a>

    <a href="logout.php" class="logout-btn">Logout</a>
</div>

</body>
</html>
