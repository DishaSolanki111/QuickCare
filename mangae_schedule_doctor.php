
<html>
<head>
    <title>Doctor Schedule Management</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
    :root {
    --dark-blue: #072D44;
    --mid-blue: #064469;
    --soft-blue: #5790AB;
    --light-blue: #9CCDD8;
    --gray-blue: #D0D7E1;
    --white: #ffffff;
    --card-bg: #F6F9FB;
    }
    body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #F5F8FA;
     line-height: 1.6;
    }
    .main-content {
            padding: 20px;
            background-color: #f9f9f9;
            min-height: 100vh;
            margin-left: 240px;
        }
    .container {
            max-width: 900px;
            margin: 0 auto;
        }
    .sidebar {
    width: 250px;
    background: #072D44;
    min-height: 100vh;
    color: var(--white);
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
    text-decoration: none;
    color: var(--gray-blue);
    font-size: 15px;
    margin: 4px 0;
    transition: .2s;
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
        .schedule-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .schedule-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .schedule-header h3 {
            color: var(--primary-color);
        }
        
        .schedule-day {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .schedule-day:last-child {
            border-bottom: none;
        }
        
        .day-name {
            font-weight: 600;
            color: var(--dark-color);
            width: 100px;
        }
        
        .day-time {
            color: #666;
        }
        
        .day-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-available {
            background-color: rgba(46, 204, 113, 0.2);
            color: var(--accent-color);
        }
        
        .status-unavailable {
            background-color: rgba(231, 76, 60, 0.2);
            color: var(--danger-color);
        }
        
    </style>
</head>
<body>
    <div class="sidebar">
    <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
    <h2>QuickCare</h2>

    <a href="dashboard_doctor.php" >Dashboard</a>
    <a href="d_dprofile.php">My Profile</a>
    <a href="mangae_schedule_doctor.php">Manage Schedule</a>
    <a href="appointment_doctor.php">Manage Appointments</a>
    <a href="manage_prescriptions.php">Manage Prescription</a>
    <a href="#">View Medicine</a>
    <a href="#">View Feedback</a>
     <button class="logout-btn">Logout</button>
</div>

   <div class="main-content">
        <div class="container">

                <h3 style="margin-bottom: 20px;">Weekly Schedule</h3>
                
                <div class="schedule-card">
                    <div class="schedule-header">
                        <h3>Regular Schedule</h3>
                        <button class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Schedule
                        </button>
                    </div>
                    
                    <div class="schedule-day">
                        <div class="day-name">Monday</div>
                        <div class="day-time">8:00 AM - 3:00 PM</div>
                        <div class="day-status status-available">Available</div>
                    </div>
                    
                    <div class="schedule-day">
                        <div class="day-name">Tuesday</div>
                        <div class="day-time">Not Available</div>
                        <div class="day-status status-unavailable">Unavailable</div>
                    </div>
                    
                    <div class="schedule-day">
                        <div class="day-name">Wednesday</div>
                        <div class="day-time">Not Available</div>
                        <div class="day-status status-unavailable">Unavailable</div>
                    </div>
                    
                    <div class="schedule-day">
                        <div class="day-name">Thursday</div>
                        <div class="day-time">8:00 AM - 3:00 PM</div>
                        <div class="day-status status-available">Available</div>
                    </div>
                    
                    <div class="schedule-day">
                        <div class="day-name">Friday</div>
                        <div class="day-time">Not Available</div>
                        <div class="day-status status-unavailable">Unavailable</div>
                    </div>
                    
                    <div class="schedule-day">
                        <div class="day-name">Saturday</div>
                        <div class="day-time">Not Available</div>
                        <div class="day-status status-unavailable">Unavailable</div>
                    </div>
                    
                    <div class="schedule-day">
                        <div class="day-name">Sunday</div>
                        <div class="day-time">9:00 AM - 1:00 PM</div>
                        <div class="day-status status-available">Available</div>
                    </div>
                </div>
            </div>