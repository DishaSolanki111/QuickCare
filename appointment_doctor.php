<html>
    <head>
        <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: #F5F8FA;
  
}
.main-content {
    padding: 20px;
    background-color: #f9f9f9;
    min-height: 100vh;
    margin-left: 240px;
    position: fixed;
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
.sidebar a:hover, .sidebar a.active {
    background: #064469;
    border-left: 4px solid #9CCDD8;
    color: white;
}

       .appointment-card {
                background-color: #fff;
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 15px;
                margin-bottom: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .appointment-info h3 {
                margin: 0 0 10px 0;
            }
            .appointment-details {
                display: flex;
                gap: 15px;
            }
            .appointment-detail {
                display: flex;
                align-items: center;
                gap: 5px;
            }
            .appointment-actions {
                text-align: right;
            }
            .status-badge {
                padding: 5px 10px;
                border-radius: 3px;
                color: #fff;
                font-size: 0.9em;
            }
            .status-confirmed {
                background-color: #28a745;
            }
            .status-completed {
                background-color: #6c757d;
            }
            .btn-group button {
                margin-left: 10px;
            }
            .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        .btn-success {
            background-color: var(--accent-color);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #27ae60;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
            </style>
    </head>
<body>
<div class="sidebar">
    <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
    <h2>QuickCare</h2>

    <a href="doctor_dashboard.php" >Dashboard</a>
    <a href="d_profile.php">My Profile</a>
    <a href="mangae_schedule_doctor.php">Manage Schedule</a>
    <a href="appointment_doctor.php">Manage Appointments</a>
    <a href="manage_prescriptions.php">Manage Prescription</a>
    <a href="#">View Medicine</a>
    <a href="doctor_feedback.php">View Feedback</a>
     <button class="logout-btn">Logout</button>
</div>

   <div class="main-content">
        <div class="container">
<h3 style="margin-bottom: 20px;">Upcoming Appointments</h3>
                
                <div class="appointment-card">
                    <div class="appointment-info">
                        <h3>Sunil Kapoor</h3>
                        <p>Patient ID: PT003</p>
                        <div class="appointment-details">
                            <div class="appointment-detail">
                                <i class="far fa-calendar"></i>
                                <span>August 1, 2024</span>
                            </div>
                            <div class="appointment-detail">
                                <i class="far fa-clock"></i>
                                <span>10:00 AM</span>
                            </div>
                            <div class="appointment-detail">
                                <i class="fas fa-stethoscope"></i>
                                <span>Follow-up Consultation</span>
                            </div>
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <span class="status-badge status-confirmed">Confirmed</span>
                        <div class="btn-group" style="margin-top: 15px;">
                            <button class="btn btn-primary">
                                <i class="fas fa-user"></i> View Patient Details
                            </button>
                            <button class="btn btn-success">
                                <i class="fas fa-file-medical"></i> Create Prescription
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="appointment-card">
                    <div class="appointment-info">
                        <h3>Vikram Singh</h3>
                        <p>Patient ID: PT009</p>
                        <div class="appointment-details">
                            <div class="appointment-detail">
                                <i class="far fa-calendar"></i>
                                <span>August 5, 2024</span>
                            </div>
                            <div class="appointment-detail">
                                <i class="far fa-clock"></i>
                                <span>2:30 PM</span>
                            </div>
                            <div class="appointment-detail">
                                <i class="fas fa-stethoscope"></i>
                                <span>Initial Consultation</span>
                            </div>
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <span class="status-badge status-confirmed">Confirmed</span>
                        <div class="btn-group" style="margin-top: 15px;">
                            <button class="btn btn-primary">
                                <i class="fas fa-user"></i> View Patient Details
                            </button>
                            <button class="btn btn-success">
                                <i class="fas fa-file-medical"></i> Create Prescription
                            </button>
                        </div>
                    </div>
                </div>
                
                <h3 style="margin: 30px 0 20px;">Past Appointments</h3>
                
                <div class="appointment-card">
                    <div class="appointment-info">
                        <h3>Meera Nair</h3>
                        <p>Patient ID: PT004</p>
                        <div class="appointment-details">
                            <div class="appointment-detail">
                                <i class="far fa-calendar"></i>
                                <span>July 15, 2024</span>
                            </div>
                            <div class="appointment-detail">
                                <i class="far fa-clock"></i>
                                <span>11:30 AM</span>
                            </div>
                            <div class="appointment-detail">
                                <i class="fas fa-stethoscope"></i>
                                <span>Follow-up Consultation</span>
                            </div>
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <span class="status-badge status-completed">Completed</span>
                        <div class="btn-group" style="margin-top: 15px;">
                            <button class="btn btn-primary">
                                <i class="fas fa-file-medical"></i> View Prescription
                            </button>
                            <button class="btn btn-success">
                                <i class="fas fa-calendar-plus"></i> Book Follow-up
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="appointment-card">
                    <div class="appointment-info">
                        <h3>Rohan Verma</h3>
                        <p>Patient ID: PT001</p>
                        <div class="appointment-details">
                            <div class="appointment-detail">
                                <i class="far fa-calendar"></i>
                                <span>June 28, 2024</span>
                            </div>
                            <div class="appointment-detail">
                                <i class="far fa-clock"></i>
                                <span>3:15 PM</span>
                            </div>
                            <div class="appointment-detail">
                                <i class="fas fa-stethoscope"></i>
                                <span>Initial Consultation</span>
                            </div>
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <span class="status-badge status-completed">Completed</span>
                        <div class="btn-group" style="margin-top: 15px;">
                            <button class="btn btn-primary">
                                <i class="fas fa-file-medical"></i> View Prescription
                            </button>
                            <button class="btn btn-success">
                                <i class="fas fa-calendar-plus"></i> Book Follow-up
                            </button>
                        </div>
                    </div>
                </div>