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

    /* Sidebar */
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
        transition: all 0.3s ease;
    }

    .sidebar a:hover {
        background: #064469;
        border-left: 4px solid #9CCDD8;
        color: white;
    }

    .sidebar a.active {
        background: #064469;
        border-left: 4px solid #9CCDD8;
        color: white;
    }

    .logo-img {
        height: 40px;
        margin-right: 12px;
        border-radius: 5px;
    }

    /* Mobile menu toggle */
    .menu-toggle {
        display: none;
        position: fixed;
        top: 20px;
        left: 20px;
        z-index: 1000;
        background: #072D44;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px;
        cursor: pointer;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .menu-toggle {
            display: block;
        }

        .main {
            margin-left: 0;
            width: 100%;
        }
    }
</style>
</head>
<body>

<!-- Mobile Menu Toggle Button -->
<button class="menu-toggle">☰ Menu</button>

<!-- Sidebar -->
<div class="sidebar">
  <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
  <h2>QuickCare</h2>

  <a href="admin.html" >Dashboard</a>
  <a href="appointments.php">Appointments</a>
  <a href="doctors.php">Doctors</a>
  <a href="patients.php">Patients</a>
  <a href="medicine.php">Medicine</a>
  <a href="payments.php">Payments</a>
  <a href="feedback.php">Feedback</a>
  <a href="reports.php">Reports</a>
  <a href="settings.php">Settings</a>
</div>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        
        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
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