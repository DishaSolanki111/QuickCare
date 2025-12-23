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
}

/* ---------------- GLOBAL STYLES ---------------- */
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #F5F8FA;
    display: flex;
}

/* ---------------- SIDEBAR ---------------- */
.sidebar {
    width: 240px;
    background: var(--dark-blue);
    height: 100vh;
    color: var(--white);
    padding: 20px 0;
    position: fixed;
}


.sidebar h2 {
        text-align: center;
        margin-bottom: 40px;
        color: #9CCDD8;
    }
.sidebar a {
    display: block;
    padding: 12px 20px;
    text-decoration: none;
    color: var(--gray-blue);
    font-size: 15px;
    margin: 4px 0;
    transition: .2s;
}

.sidebar a:hover,
.sidebar a.active {
    background: var(--soft-blue);
    color: var(--white);
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

    <a href="#" class="active">Dashboard</a>
    <a href="d_dprofile.php">My Profile</a>
    <a href="#">Manage Schedule</a>
    <a href="#">Manage Appointments</a>
    <a href="manage_prescriptions.php">Manage Prescription</a>
    <a href="#">View Medicine</a>
    <a href="#">View Feedback</a>
</div>



<!-- ---------------- MAIN CONTENT ---------------- -->
<div class="main-content">
<!-- ---------------- TOP BAR ---------------- -->
<div class="topbar">
    <h1>Doctor Dashboard</h1>
    <div class="top-icons">
        <span>🔔</span>
        <span>💬</span>
        <span>⚙️</span>
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
