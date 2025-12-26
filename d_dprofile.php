<!DOCTYPE html>
<html lang="en">
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
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles - Replaced with patient.php sidebar styles */
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
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }
        
        .welcome-msg {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .user-actions {
            display: flex;
            align-items: center;
        }
        
        .notification-btn {
            position: relative;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--dark-color);
            margin-right: 20px;
            cursor: pointer;
        }
        
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
        }
        
        .user-dropdown {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .profile-section {
            display: flex;
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .profile-card {
            flex: 1;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 25px;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: bold;
            margin-right: 20px;
            overflow: hidden;
        }
        
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .profile-title h2 {
            font-size: 28px;
            color: var(--primary-color);
            margin-bottom: 5px;
        }
        
        .profile-title p {
            color: #777;
            font-size: 16px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .info-item {
            margin-bottom: 15px;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 5px;
            display: block;
        }
        
        .info-value {
            color: #555;
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
        
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 12px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            color: #777;
            transition: all 0.3s ease;
        }
        
        .tab.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--secondary-color);
        }
        
        .tab:hover {
            color: var(--primary-color);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .appointment-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .appointment-info h3 {
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .appointment-details {
            display: flex;
            gap: 20px;
            margin-top: 10px;
        }
        
        .appointment-detail {
            display: flex;
            align-items: center;
            color: #666;
        }
        
        .appointment-detail i {
            margin-right: 5px;
            color: var(--secondary-color);
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-confirmed {
            background-color: rgba(46, 204, 113, 0.2);
            color: var(--accent-color);
        }
        
        .status-completed {
            background-color: rgba(52, 152, 219, 0.2);
            color: var(--secondary-color);
        }
        
        .status-cancelled {
            background-color: rgba(231, 76, 60, 0.2);
            color: var(--danger-color);
        }
        
        .prescription-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .prescription-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .prescription-header h3 {
            color: var(--primary-color);
        }
        
        .prescription-date {
            color: #777;
            font-size: 14px;
        }
        
        .medicine-list {
            margin-top: 15px;
        }
        
        .medicine-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .medicine-name {
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .medicine-details {
            color: #666;
            font-size: 14px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #777;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }
        
        .feedback-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .feedback-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .patient-info {
            display: flex;
            align-items: center;
        }
        
        .patient-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-weight: bold;
        }
        
        .patient-name {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .rating {
            display: flex;
            color: #f1c40f;
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
        
        .edit-profile-form {
            display: none;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            display: flex;
            align-items: center;
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 20px;
        }
        
        .stat-icon.appointments {
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--secondary-color);
        }
        
        .stat-icon.patients {
            background-color: rgba(46, 204, 113, 0.1);
            color: var(--accent-color);
        }
        
        .stat-icon.prescriptions {
            background-color: rgba(241, 196, 15, 0.1);
            color: var(--warning-color);
        }
        
        .stat-icon.rating {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
        }
        
        .stat-info h3 {
            font-size: 24px;
            margin-bottom: 5px;
            color: var(--primary-color);
        }
        
        .stat-info p {
            color: #777;
            font-size: 14px;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            
            .logo h1 span, .nav-item span {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .info-grid, .form-row, .stats-container {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .profile-section {
                flex-direction: column;
            }
            
            .appointment-card {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .appointment-details {
                flex-direction: column;
                gap: 10px;
            }
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        .logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar - Replaced with patient.php sidebar -->
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
    <button class="logout-btn">logout</button>
</div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="welcome-msg">My Profile</div>
                <div class="user-actions">
                    <button class="notification-btn">
                        <i class="far fa-bell"></i>
                        <span class="notification-badge">5</span>
                    </button>
                    <div class="user-dropdown">
                        <div class="user-avatar">AS</div>
                        <span>Dr. Amar Kumar</span>
                        <i class="fas fa-chevron-down" style="margin-left: 8px;"></i>
                    </div>
                </div>
            </div>
            
            <!-- Stats Section -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon appointments">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3>24</h3>
                        <p>Total Appointments</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon patients">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3>18</h3>
                        <p>Patients Treated</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon prescriptions">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <div class="stat-info">
                        <h3>15</h3>
                        <p>Prescriptions Written</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon rating">
                        <i class="fas fa-star"></i>
                    </div>
                    <div class="stat-info">
                        <h3>4.8</h3>
                        <p>Average Rating</p>
                    </div>
                </div>
            </div>
            
            <!-- Profile Section -->
            <div class="profile-section">
                <!-- Personal Information Card -->
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">
                            <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Doctor Profile">
                        </div>
                        <div class="profile-title">
                            <h2>Dr. Amar Kumar</h2>
                            <p>Doctor ID: DOC004</p>
                        </div>
                    </div>
                    
                    <!-- Profile View -->
                    <div id="profileView">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">First Name</span>
                                <span class="info-value">Amar</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Last Name</span>
                                <span class="info-value">Kumar</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Date of Birth</span>
                                <span class="info-value">November 25, 1978</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Gender</span>
                                <span class="info-value">Male</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Date of Joining</span>
                                <span class="info-value">March 10, 2015</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Phone Number</span>
                                <span class="info-value">+1 (555) 987-6543</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email Address</span>
                                <span class="info-value">a.kumar.cardio@hospital.com</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Username</span>
                                <span class="info-value">a.kumar</span>
                            </div>
                        </div>
                        
                        <div class="btn-group">
                            <button class="btn btn-primary" id="editProfileBtn">
                                <i class="fas fa-edit"></i> Edit Profile
                            </button>
                            <button class="btn btn-danger">
                                <i class="fas fa-key"></i> Change Password
                            </button>
                        </div>
                    </div>
                    
                    <!-- Edit Profile Form -->
                    <div id="editProfileForm" class="edit-profile-form">
                        <form>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="firstName">First Name</label>
                                    <input type="text" class="form-control" id="firstName" value="Amar">
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" value="Kumar">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="date" class="form-control" id="dob" value="1978-11-25">
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender">
                                        <option value="MALE" selected>Male</option>
                                        <option value="FEMALE">Female</option>
                                        <option value="OTHER">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" value="5559876543">
                                </div>
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control" id="email" value="a.kumar.cardio@hospital.com">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" value="a.kumar">
                            </div>
                            
                            <div class="btn-group">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Save Changes
                                </button>
                                <button type="button" class="btn btn-danger" id="cancelEditBtn">
                                    <i class="fas fa-times"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Professional Information Card -->
                <div class="profile-card">
                    <div class="profile-header">
                        <h2>Professional Information</h2>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Specialization</span>
                            <span class="info-value">Cardiologist</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Experience</span>
                            <span class="info-value">9 years</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">License Number</span>
                            <span class="info-value">MED-2025-IND-0421</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Education</span>
                            <span class="info-value">MD, DM Cardiology</span>
                        </div>
                    </div>
                    
                    <div class="btn-group">
                        <button class="btn btn-primary">
                            <i class="fas fa-download"></i> Download CV
                        </button>
                        <button class="btn btn-success">
                            <i class="fas fa-certificate"></i> View Certificates
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Tabs Section -->
            <div class="tabs">
                <div class="tab active" data-tab="appointments">Appointments</div>
                <div class="tab" data-tab="schedule">Schedule</div>
                <div class="tab" data-tab="prescriptions">Prescriptions</div>
                <div class="tab" data-tab="feedback">Patient Feedback</div>
            </div>
            
            <!-- Tab Content -->
            <div class="tab-content active" id="appointments">
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
            </div>
            
            <div class="tab-content" id="schedule">
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
            
            <div class="tab-content" id="prescriptions">
                <h3 style="margin-bottom: 20px;">Recent Prescriptions</h3>
                
                <div class="prescription-card">
                    <div class="prescription-header">
                        <h3>Sunil Kapoor</h3>
                        <span class="prescription-date">July 15, 2024</span>
                    </div>
                    
                    <div class="prescription-details">
                        <p><strong>Patient ID:</strong> PT003</p>
                        <p><strong>Diagnosis:</strong> Stable Angina</p>
                        <p><strong>Symptoms:</strong> Chest pain on exertion, relieved by rest</p>
                    </div>
                    
                    <h4 style="margin: 15px 0 10px;">Medications Prescribed</h4>
                    <div class="medicine-list">
                        <div class="medicine-item">
                            <div>
                                <div class="medicine-name">Aspirin</div>
                                <div class="medicine-details">75mg - Once daily</div>
                            </div>
                            <div class="medicine-details">1 month</div>
                        </div>
                        <div class="medicine-item">
                            <div>
                                <div class="medicine-name">Atorvastatin</div>
                                <div class="medicine-details">20mg - Once daily at night</div>
                            </div>
                            <div class="medicine-details">3 months</div>
                        </div>
                    </div>
                    
                    <div class="btn-group" style="margin-top: 15px;">
                        <button class="btn btn-primary">
                            <i class="fas fa-download"></i> Download PDF
                        </button>
                        <button class="btn btn-success">
                            <i class="fas fa-edit"></i> Edit Prescription
                        </button>
                    </div>
                </div>
                
                <div class="prescription-card">
                    <div class="prescription-header">
                        <h3>Meera Nair</h3>
                        <span class="prescription-date">June 28, 2024</span>
                    </div>
                    
                    <div class="prescription-details">
                        <p><strong>Patient ID:</strong> PT004</p>
                        <p><strong>Diagnosis:</strong> Hypertension with Diabetes</p>
                        <p><strong>Symptoms:</strong> Occasional palpitations and fatigue</p>
                    </div>
                    
                    <h4 style="margin: 15px 0 10px;">Medications Prescribed</h4>
                    <div class="medicine-list">
                        <div class="medicine-item">
                            <div>
                                <div class="medicine-name">Metoprolol</div>
                                <div class="medicine-details">50mg - Twice daily</div>
                            </div>
                            <div class="medicine-details">3 months</div>
                        </div>
                        <div class="medicine-item">
                            <div>
                                <div class="medicine-name">Aspirin</div>
                                <div class="medicine-details">75mg - Once daily</div>
                            </div>
                            <div class="medicine-details">3 months</div>
                        </div>
                    </div>
                    
                    <div class="btn-group" style="margin-top: 15px;">
                        <button class="btn btn-primary">
                            <i class="fas fa-download"></i> Download PDF
                        </button>
                        <button class="btn btn-success">
                            <i class="fas fa-edit"></i> Edit Prescription
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="tab-content" id="feedback">
                <h3 style="margin-bottom: 20px;">Patient Feedback</h3>
                
                <div class="feedback-card">
                    <div class="feedback-header">
                        <div class="patient-info">
                            <div class="patient-avatar">MN</div>
                            <div>
                                <div class="patient-name">Meera Nair</div>
                                <div style="color: #777; font-size: 14px;">July 15, 2024</div>
                            </div>
                        </div>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    
                    <p style="margin-top: 15px;">Dr. Sharma is very empathetic and provided a clear plan of action. She took time to explain my condition and treatment options in detail. Highly recommended!</p>
                </div>
                
                <div class="feedback-card">
                    <div class="feedback-header">
                        <div class="patient-info">
                            <div class="patient-avatar">SK</div>
                            <div>
                                <div class="patient-name">Sunil Kapoor</div>
                                <div style="color: #777; font-size: 14px;">June 28, 2024</div>
                            </div>
                        </div>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="far fa-star"></i>
                        </div>
                    </div>
                    
                    <p style="margin-top: 15px;">Dr. Kumar is thorough. The consultation was good but the waiting time was a bit long. However, once I was in the consultation room, he gave me his full attention and answered all my questions.</p>
                </div>
                
                <div class="feedback-card">
                    <div class="feedback-header">
                        <div class="patient-info">
                            <div class="patient-avatar">RV</div>
                            <div>
                                <div class="patient-name">Rohan Verma</div>
                                <div style="color: #777; font-size: 14px;">May 10, 2024</div>
                            </div>
                        </div>
                        <div class="rating">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                    </div>
                    
                    <p style="margin-top: 15px;">Dr. Kumar's detailed explanation put my mind at ease. He was very patient and made sure I understood everything about my condition and treatment. Highly recommend!</p>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabId = tab.getAttribute('data-tab');
                    
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    tab.classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                });
            });
            
            // Edit profile functionality
            const editProfileBtn = document.getElementById('editProfileBtn');
            const cancelEditBtn = document.getElementById('cancelEditBtn');
            const profileView = document.getElementById('profileView');
            const editProfileForm = document.getElementById('editProfileForm');
            
            editProfileBtn.addEventListener('click', () => {
                profileView.style.display = 'none';
                editProfileForm.style.display = 'block';
            });
            
            cancelEditBtn.addEventListener('click', () => {
                profileView.style.display = 'block';
                editProfileForm.style.display = 'none';
            });
        });
    </script>
</body>
</html>