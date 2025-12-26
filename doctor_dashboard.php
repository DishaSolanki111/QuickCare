<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Doctor Dashboard - QuickCare</title>

<style>
/* ----------------- COLOR PALETTE ----------------- */
:root {

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
}

/* ---------------- GLOBAL STYLES ---------------- */
body {
    margin: 0;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #F5F8FA;
    display: flex;
}
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

.top-icons span {
    margin-left: 15px;
    cursor: pointer;
}

/* ---------------- MAIN CONTENT ---------------- */
.main-content {
    margin-left: 240px;
    padding: 20px;
    width: calc(100% - 240px);
}

/* ---------------- TITLES ---------------- */
.welcome-title {
    font-size: 28px;
    font-weight: bold;
    color: var(--dark-blue);
}

.date {
    font-size: 14px;
    color: #777;
    margin-top: -8px;
}

/* ---------------- DASHBOARD CARDS ---------------- */
.cards-container {
    display: flex;
    gap: 20px;
    margin-top: 25px;
}

.card {
    flex: 1;
    background: var(--white);
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.card h3 {
    margin: 0;
    font-size: 20px;
    color: var(--dark-blue);
}

.card-value {
    font-size: 30px;
    font-weight: bold;
    margin-top: 10px;
    color: var(--soft-blue);
}

/* ---------------- SCHEDULE SUMMARY ---------------- */
.schedule-card {
    width: 280px;
    background: var(--white);
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

.schedule-card h3 {
    margin: 0;
    font-size: 20px;
    color: var(--dark-blue);
}

.schedule-info {
    margin-top: 10px;
    font-size: 16px;
    color: #555;
}

.add-pres-btn {
    margin-top: 15px;
    background: var(--soft-blue);
    color: var(--white);
    border: none;
    padding: 12px 20px;
    width: 100%;
    border-radius: 8px;
    cursor: pointer;
    font-weight: bold;
}

/* Fix layout */
.row {
    display: flex;
    justify-content: space-between;
    margin-top: 25px;
}
.logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
        }
</style>

</head>
<body>

<!-- ---------------- SIDEBAR ---------------- -->
<div class="sidebar">
    <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
    <h2>QuickCare</h2>

    <a href="dashboard_doctor.php" class="active">Dashboard</a>
    <a href="d_dprofile.php">My Profile</a>
    <a href="mangae_schedule_doctor.php">Manage Schedule</a>
    <a href="appointment_doctor.php">Manage Appointments</a>
    <a href="manage_prescriptions.php">Manage Prescription</a>
    <a href="#">View Medicine</a>
    <a href="#">View Feedback</a>
    <button class="logout-btn">Logout</button>
</div>



<!-- ---------------- MAIN CONTENT ---------------- -->
<div class="main-content">
<!-- ---------------- TOP BAR ---------------- -->
<div class="topbar">
    <h1>Doctor Dashboard</h1>
    <div class="top-icons">
        <span>🔔</span>
        <span style="margin-left:20px; background:#fff; color:#000; padding:6px 12px; border-radius:6px; font-size:14px; cursor:pointer;">
            Logout
        </span>
    </div>
</div>
<br>
    <!-- Title -->
    <div class="welcome-title">Welcome, Dr. John Doe</div>
    <br>
    <div class="date">April 27, 2024</div>

    <!-- Dashboard row -->
    <div class="row">
        <div style="flex: 1;">
            <div class="cards-container">
                <!-- Today's Appointments -->
                <div class="card">
                    <h3>Today's Appointments</h3>
                    <div class="card-value">5</div>
                </div>

                <!-- Feedback Summary -->
                <div class="card">
                    <h3>Feedback Summary</h3>
                    <div class="card-value">⭐ 4.8</div>
                </div>
            </div>

        </div>


    </div>
<br>
        <!-- Schedule Summary -->
        <div class="schedule-card">
            <h3>Schedule Summary</h3>
            <div class="schedule-info">
                Next Available Slot: <br><b>10:00 AM</b><br><br>
                Total Patients This Week: <b>15</b>
            </div>
            
        </div>
</div>

</body>
</html>
