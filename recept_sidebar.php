<?php
// Detect current page
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receptionist Dashboard - QuickCare</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

<style>
:root {
    --dark-blue: #072D44;
    --mid-blue: #064469;
    --soft-blue: #5790AB;
    --light-blue: #9CCDD8;
    --gray-blue: #D0D7E1;
    --white: #ffffff;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #F5F8FA;
    display: flex;
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
    overflow-y: auto; 
    z-index: 1000;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}

.sidebar h2 { 
    text-align: center;
    margin-bottom: 40px;
    color: var(--light-blue);
    font-size: 24px; 
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

/* ACTIVE PAGE */
.sidebar a:hover,
.sidebar a.active {
    background: var(--mid-blue);
    border-left: 4px solid var(--light-blue);
    color: white;
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
}

.logout-btn:hover {
    background-color: var(--light-blue);
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <img src="uploads/logo.JPG" alt="QuickCare Logo"
         style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  

    <h2>QuickCare</h2>

    <a href="index.php" class="<?= ($currentPage == 'index.php') ? 'active' : '' ?>">Home</a>

    <a href="receptionist.php" class="<?= ($currentPage == 'receptionist.php') ? 'active' : '' ?>">Dashboard</a>

    <a href="receptionist_profile.php" class="<?= ($currentPage == 'receptionist_profile.php') ? 'active' : '' ?>">My Profile</a>

    <a href="manage_user_profile.php" class="<?= ($currentPage == 'manage_user_profile.php') ? 'active' : '' ?>">User Profile</a>

    <a href="appointment_recep.php" class="<?= ($currentPage == 'appointment_recep.php') ? 'active' : '' ?>">Appointments</a>

    <a href="doctor_schedule_recep.php" class="<?= ($currentPage == 'doctor_schedule_recep.php') ? 'active' : '' ?>">Doctor Schedule</a>

    <a href="manage_medicine.php" class="<?= ($currentPage == 'manage_medicine.php') ? 'active' : '' ?>">Medicine</a>

    <a href="st_reminder.php" class="<?= ($currentPage == 'st_reminder.php') ? 'active' : '' ?>">Set Reminder</a>

    <a href="view_prescription.php" class="<?= ($currentPage == 'view_prescription.php') ? 'active' : '' ?>">View Prescription</a>

    <a href="logout.php" class="logout-btn">Logout</a>
</div>

</body>
</html>
