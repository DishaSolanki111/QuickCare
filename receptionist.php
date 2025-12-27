<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receptionist Dashboard - QuickCare</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
<style>
/* ----------------- COLOR PALETTE ----------------- */
:root {
    --dark-blue: #072D44;     /* Sidebar */
    --mid-blue: #064469;      /* Top Navbar */
    --soft-blue: #5790AB;     /* Hover / Active */
    --light-blue: #9CCDD8;    /* Cards */
    --gray-blue: #D0D7E1;     /* Text/Icons */
    --white: #ffffff;
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

/* ---------------- GLOBAL STYLES ---------------- */
body {
    margin: 0;
    font-family: Arial, sans-serif;
    font-weight: bold;
    background: #F5F8FA;
    display: flex;
}

/* ---------------- SIDEBAR ---------------- */
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
        

    /* Top bar */
    .topbar {
        background: white;
        padding: 15px 25px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .topbar h1 {
        margin: 0;
        color: #064469;
    }

/* ---------------- MAIN CONTENT ---------------- */
.main-content {
    margin-left: 240px;
    padding: 20px;
    width: calc(100% - 240px);
}

.page-title {
    font-size: 26px;
    font-weight: bold;
    color: var(--dark-blue);
}

/* ---------------- STATS CARDS ---------------- */
.stats-container {
    display: flex;
    gap: 20px;
    margin-top: 20px;
}

.stat-card {
    flex: 1;
    background: var(--white);
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0px 3px 10px rgba(0,0,0,0.1);
}

.stat-card h3 {
    margin: 0;
    font-size: 22px;
    color: var(--dark-blue);
}

.stat-card p {
    font-size: 14px;
    color: #666;
}
.logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
        }
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-medium);
            padding: 15px 20px;
            font-weight: 600;
            color: var(--primary-dark-blue);
        }
        
        .card-body {
            padding: 20px;
        }
        
        /* Statistics Styles */
        .stats-card {
            background-color: var(--white);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            height: 100%;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .stats-icon {
            font-size: 2rem;
            color: var(--accent-blue);
            margin-bottom: 15px;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-dark-blue);
            margin-bottom: 5px;
        }
        
        .stats-label {
            color: var(--gray-dark);
            font-size: 14px;
        }
</style>

</head>
<body>

<!-- ---------------- SIDEBAR ---------------- -->
<div class="sidebar">
    <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
        <h2>QuickCare</h2>
    

    <a href="#" class="active">Dashboard</a>
     <a href="recep_profile.php">View My Profile</a>
    <a href="appointment_recep.php">Manage Appointments</a>
    <a href="doctor_schedule_recep.php">Manage Doctor Schedule</a>
    <a href="manage_medicine.php">Manage Medicine</a>
    <a href="st_reminder.php">Set Reminder</a>
    <a href="manage_user_profile.php">Manage User Profile</a>
    <a href="#">View Prescription</a>
     <button class="logout-btn">logout</button>
   
</div>





<!-- ---------------- MAIN CONTENT ---------------- -->
<div class="main-content">
 <div class="topbar">
        <h1>Receptionist Dashboard</h1>
        <p>Welcome, Receptionist</p>
    </div>
  

    <!-- Stats Cards -->
    <div class="card">
        <div class="card-header">
         
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="stats-card">
                        <i class="bi bi-calendar-check stats-icon"></i>
                        <div class="stats-number">24</div>
                        <div class="stats-label">Total Appointments</div>
                    </div>
                </div>
               
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="stats-card">
                        <i class="bi bi-bell stats-icon"></i>
                        <div class="stats-number">9</div>
                        <div class="stats-label">Medicine Reminders</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="stats-card">
                        <i class="bi bi-capsule stats-icon"></i>
                        <div class="stats-number">15</div>
                        <div class="stats-label">Medicine Management</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>
