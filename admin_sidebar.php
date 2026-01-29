<?php
// Get current page name
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sidebar Navigation</title>

<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #D0D7E1;
        display: flex;
    }

    :root {
        --dark-blue: #072D44;
        --mid-blue: #064469;
        --soft-blue: #5790AB;
        --light-blue: #9CCDD8;
        --gray-blue: #D0D7E1;
        --white: #ffffff;
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
        transition: transform 0.3s ease;
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 40px;
        color: var(--light-blue);
    }

    .sidebar a {
        display: block;
        padding: 15px 25px;
        color: var(--gray-blue);
        text-decoration: none;
        font-size: 17px;
        border-left: 4px solid transparent;
    }

    /* ACTIVE + HOVER */
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
        color: white;    
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        text-align: center;
    }

    .logout-btn:hover {
        background-color: var(--light-blue);
    }

    .logo-img {
        display:block; 
        margin: 0 auto 10px auto; 
        width:80px; 
        height:80px; 
        border-radius:50%;
    }

    /* Mobile menu toggle */
    .menu-toggle {
        display: none;
        position: fixed;
        top: 20px;
        left: 20px;
        z-index: 1001;
        background: var(--dark-blue);
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px;
        cursor: pointer;
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .menu-toggle {
            display: block;
        }
    }
</style>
</head>

<body>

<!-- Mobile Menu Button -->
<button class="menu-toggle">☰ Menu</button>

<!-- Sidebar -->
<div class="sidebar">
    <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img">  
    <h2>QuickCare</h2>

    <a href="index.php" class="<?= ($currentPage == 'index.php') ? 'active' : '' ?>">Home</a>
    <a href="admin.php" class="<?= ($currentPage == 'admin.php') ? 'active' : '' ?>">Dashboard</a>
    <a href="Admin_appoitment.php" class="<?= ($currentPage == 'Admin_appoitment.php') ? 'active' : '' ?>">Appointments</a>
    <a href="Admin_doctor.php" class="<?= ($currentPage == 'Admin_doctor.php') ? 'active' : '' ?>">Doctors</a>
    <a href="Admin_recept.php" class="<?= ($currentPage == 'Admin_recept.php') ? 'active' : '' ?>">Receptionist</a>
    <a href="Admin_patient.php" class="<?= ($currentPage == 'Admin_patient.php') ? 'active' : '' ?>">Patients</a>
    <a href="Admin_medicine.php" class="<?= ($currentPage == 'Admin_medicine.php') ? 'active' : '' ?>">Medicine</a>
    <a href="Admin_payment.php" class="<?= ($currentPage == 'Admin_payment.php') ? 'active' : '' ?>">Payments</a>
    <a href="Admin_feedback.php" class="<?= ($currentPage == 'Admin_feedback.php') ? 'active' : '' ?>">Feedback</a>

    <button class="logout-btn">Logout</button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');

        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });

        document.addEventListener('click', function (event) {
            if (window.innerWidth <= 768 &&
                !sidebar.contains(event.target) &&
                !menuToggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });
    });
</script>

</body>
</html>
