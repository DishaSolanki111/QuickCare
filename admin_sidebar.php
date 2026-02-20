<?php
// Detect current page
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Sidebar - QuickCare</title>

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
    font-family: Arial, sans-serif;
}

body {
    background: #D0D7E1;
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
    overflow-y: auto;   /* ✅ scrollbar kept */
    z-index: 1000;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}

/* Scrollbar */
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
    display: block;
    margin: 0 auto 15px auto;
    width: 80px;
    height: 80px;
    border-radius: 50%;
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
}

/* Active + Hover */
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
</style>
</head>

<body>

<!-- ADMIN SIDEBAR -->
<div class="sidebar">
    <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img">
    <h2>QuickCare</h2>

    <a href="index.php" class="<?= ($currentPage == 'index.php') ? 'active' : '' ?>">Home</a>

    <a href="admin.php" class="<?= ($currentPage == 'admin.php') ? 'active' : '' ?>">Dashboard</a>

    <a href="Admin_appoitment.php" class="<?= ($currentPage == 'Admin_appoitment.php') ? 'active' : '' ?>">Appointments</a>

    <a href="Admin_doctor.php" class="<?= ($currentPage == 'Admin_doctor.php') ? 'active' : '' ?>">Doctors</a>

    <a href="Admin_recept.php" class="<?= ($currentPage == 'Admin_recept.php') ? 'active' : '' ?>">Receptionist</a>

    <a href="Admin_patient.php" class="<?= ($currentPage == 'Admin_patient.php') ? 'active' : '' ?>">Patients</a>

    <a href="Admin_Doctor_schedule.php" class="<?= ($currentPage == 'Admin_Doctor_schedule.php') ? 'active' : '' ?>">Doctor Schedule</a>

    <a href="Admin_medicine.php" class="<?= ($currentPage == 'Admin_medicine.php') ? 'active' : '' ?>">Medicine</a>

    <a href="Admin_prescription.php" class="<?= ($currentPage == 'Admin_prescription.php') ? 'active' : '' ?>">Prescriptions</a>
    
    <a href="Admin_payment.php" class="<?= ($currentPage == 'Admin_payment.php') ? 'active' : '' ?>">Payments</a>

    <a href="Admin_feedback.php" class="<?= ($currentPage == 'Admin_feedback.php') ? 'active' : '' ?>">Feedback</a>

    <a href="logout.php" class="logout-btn">Logout</a>
</div>

</body>
</html>
