<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Profile - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a3a5f;
            --secondary-color: #3498db;
            --accent-color: #2ecc71;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
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
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: var(--primary-color);
            color: white;
            padding: 20px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 100;
        }
        
        .logo {
            text-align: center;
            padding: 15px;
            margin-bottom: 30px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .logo h1 {
            font-size: 24px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo i {
            margin-right: 10px;
            color: var(--accent-color);
        }
        
        .nav-menu {
            list-style: none;
        }
        
        .nav-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .nav-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .nav-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            cursor: pointer;
        }
        
        .nav-item.active {
            background-color: rgba(255, 255, 255, 0.2);
            border-left: 4px solid var(--accent-color);
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
        
        .reminder-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .reminder-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 20px;
        }
        
        .reminder-content {
            flex: 1;
        }
        
        .reminder-content h4 {
            margin-bottom: 5px;
            color: var(--primary-color);
        }
        
        .reminder-time {
            color: #666;
            font-size: 14px;
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
            
            .info-grid, .form-row {
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
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h1><i class="fas fa-heartbeat"></i> <span>QuickCare</span></h1>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <i class="fas fa-tachometer-alt"></i> <span>Dashboard</span>
                </li>
                <li class="nav-item active">
                    <i class="fas fa-user"></i> <span>My Profile</span>
                </li>
                <li class="nav-item">
                    <i class="fas fa-calendar-check"></i> <span>Appointments</span>
                </li>
                <li class="nav-item">
                    <i class="fas fa-pills"></i> <span>Medicines</span>
                </li>
                <li class="nav-item">
                    <i class="fas fa-file-medical"></i> <span>Prescriptions</span>
                </li>
                <li class="nav-item">
                    <i class="fas fa-credit-card"></i> <span>Payments</span>
                </li>
                <li class="nav-item">
                    <i class="fas fa-comments"></i> <span>Feedback</span>
                </li>
                <li class="nav-item">
                    <i class="fas fa-cog"></i> <span>Settings</span>
                </li>
                <li class="nav-item">
                    <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
                </li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="welcome-msg">My Profile</div>
                <div class="user-actions">
                    <button class="notification-btn">
                        <i class="far fa-bell"></i>
                        <span class="notification-badge">3</span>
                    </button>
                    <div class="user-dropdown">
                        <div class="user-avatar">JD</div>
                        <span>Jane Doe</span>
                        <i class="fas fa-chevron-down" style="margin-left: 8px;"></i>
                    </div>
                </div>
            </div>
            
            <!-- Profile Section -->
            <div class="profile-section">
                <!-- Personal Information Card -->
                <div class="profile-card">
                    <div class="profile-header">
                        <div class="profile-avatar">JD</div>
                        <div class="profile-title">
                            <h2>Jane Doe</h2>
                            <p>Patient ID: PT001</p>
                        </div>
                    </div>
                    
                    <!-- Profile View -->
                    <div id="profileView">
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">First Name</span>
                                <span class="info-value">Jane</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Last Name</span>
                                <span class="info-value">Doe</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Date of Birth</span>
                                <span class="info-value">January 15, 1985</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Gender</span>
                                <span class="info-value">Female</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Blood Group</span>
                                <span class="info-value">O+</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Phone Number</span>
                                <span class="info-value">+1 (555) 123-4567</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email Address</span>
                                <span class="info-value">jane.doe@example.com</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Address</span>
                                <span class="info-value">123 Main St, Anytown, USA</span>
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
                                    <input type="text" class="form-control" id="firstName" value="Jane">
                                </div>
                                <div class="form-group">
                                    <label for="lastName">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" value="Doe">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="date" class="form-control" id="dob" value="1985-01-15">
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender">
                                        <option value="FEMALE" selected>Female</option>
                                        <option value="MALE">Male</option>
                                        <option value="OTHER">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="bloodGroup">Blood Group</label>
                                    <select class="form-control" id="bloodGroup">
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="O+" selected>O+</option>
                                        <option value="O-">O-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" value="5551234567">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" id="email" value="jane.doe@example.com">
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" id="address" rows="3">123 Main St, Anytown, USA</textarea>
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
                
                <!-- Medical Information Card -->
                <div class="profile-card">
                    <div class="profile-header">
                        <h2>Medical Information</h2>
                    </div>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Allergies</span>
                            <span class="info-value">Penicillin, Peanuts</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Chronic Conditions</span>
                            <span class="info-value">Hypertension</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Current Medications</span>
                            <span class="info-value">Lisinopril 10mg daily</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Last Visit</span>
                            <span class="info-value">June 15, 2023</span>
                        </div>
                    </div>
                    
                    <div class="btn-group">
                        <button class="btn btn-primary">
                            <i class="fas fa-download"></i> Download Medical History
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Tabs Section -->
            <div class="tabs">
                <div class="tab active" data-tab="appointments">Appointments</div>
                <div class="tab" data-tab="prescriptions">Prescriptions</div>
                <div class="tab" data-tab="reminders">Medicine Reminders</div>
            </div>
            
            <!-- Tab Content -->
            <div class="tab-content active" id="appointments">
                <h3 style="margin-bottom: 20px;">Upcoming Appointments</h3>
                
                <div class="appointment-card">
                    <div class="appointment-info">
                        <h3>Dr. Smith</h3>
                        <p>Cardiologist</p>
                        <div class="appointment-details">
                            <div class="appointment-detail">
                                <i class="far fa-calendar"></i>
                                <span>October 12, 2024</span>
                            </div>
                            <div class="appointment-detail">
                                <i class="far fa-clock"></i>
                                <span>10:00 AM</span>
                            </div>
                            <div class="appointment-detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Main Hospital, Room 204</span>
                            </div>
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <span class="status-badge status-confirmed">Confirmed</span>
                        <div class="btn-group" style="margin-top: 15px;">
                            <button class="btn btn-primary">
                                <i class="fas fa-edit"></i> Reschedule
                            </button>
                            <button class="btn btn-danger">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="appointment-card">
                    <div class="appointment-info">
                        <h3>Dr. Johnson</h3>
                        <p>General Physician</p>
                        <div class="appointment-details">
                            <div class="appointment-detail">
                                <i class="far fa-calendar"></i>
                                <span>October 25, 2024</span>
                            </div>
                            <div class="appointment-detail">
                                <i class="far fa-clock"></i>
                                <span>2:30 PM</span>
                            </div>
                            <div class="appointment-detail">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Main Hospital, Room 112</span>
                            </div>
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <span class="status-badge status-confirmed">Confirmed</span>
                        <div class="btn-group" style="margin-top: 15px;">
                            <button class="btn btn-primary">
                                <i class="fas fa-edit"></i> Reschedule
                            </button>
                            <button class="btn btn-danger">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                    </div>
                </div>
                
                <h3 style="margin: 30px 0 20px;">Past Appointments</h3>
                
                <div class="appointment-card">
                    <div class="appointment-info">
                        <h3>Dr. Williams</h3>
                        <p>Dermatologist</p>
                        <div class="appointment-details">
                            <div class="appointment-detail">
                                <i class="far fa-calendar"></i>
                                <span>September 5, 2024</span>
                            </div>
                            <div class="appointment-detail">
                                <i class="far fa-clock"></i>
                                <span>11:30 AM</span>
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
                                <i class="fas fa-star"></i> Leave Feedback
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="appointment-card">
                    <div class="appointment-info">
                        <h3>Dr. Anderson</h3>
                        <p>Orthopedic Surgeon</p>
                        <div class="appointment-details">
                            <div class="appointment-detail">
                                <i class="far fa-calendar"></i>
                                <span>August 14, 2024</span>
                            </div>
                            <div class="appointment-detail">
                                <i class="far fa-clock"></i>
                                <span>3:15 PM</span>
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
                                <i class="fas fa-star"></i> Leave Feedback
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="tab-content" id="prescriptions">
                <h3 style="margin-bottom: 20px;">Recent Prescriptions</h3>
                
                <div class="prescription-card">
                    <div class="prescription-header">
                        <h3>Dr. Smith - Cardiologist</h3>
                        <span class="prescription-date">September 5, 2024</span>
                    </div>
                    
                    <div class="prescription-details">
                        <p><strong>Diagnosis:</strong> Hypertension</p>
                        <p><strong>Symptoms:</strong> Occasional headaches, dizziness</p>
                    </div>
                    
                    <h4 style="margin: 15px 0 10px;">Medications</h4>
                    <div class="medicine-list">
                        <div class="medicine-item">
                            <div>
                                <div class="medicine-name">Lisinopril</div>
                                <div class="medicine-details">10mg - Once daily</div>
                            </div>
                            <div class="medicine-details">30 days</div>
                        </div>
                        <div class="medicine-item">
                            <div>
                                <div class="medicine-name">Aspirin</div>
                                <div class="medicine-details">81mg - Once daily</div>
                            </div>
                            <div class="medicine-details">30 days</div>
                        </div>
                    </div>
                    
                    <div class="btn-group" style="margin-top: 15px;">
                        <button class="btn btn-primary">
                            <i class="fas fa-download"></i> Download PDF
                        </button>
                    </div>
                </div>
                
                <div class="prescription-card">
                    <div class="prescription-header">
                        <h3>Dr. Williams - Dermatologist</h3>
                        <span class="prescription-date">August 14, 2024</span>
                    </div>
                    
                    <div class="prescription-details">
                        <p><strong>Diagnosis:</strong> Eczema</p>
                        <p><strong>Symptoms:</strong> Itchy, red skin patches</p>
                    </div>
                    
                    <h4 style="margin: 15px 0 10px;">Medications</h4>
                    <div class="medicine-list">
                        <div class="medicine-item">
                            <div>
                                <div class="medicine-name">Hydrocortisone Cream</div>
                                <div class="medicine-details">1% - Apply twice daily</div>
                            </div>
                            <div class="medicine-details">14 days</div>
                        </div>
                        <div class="medicine-item">
                            <div>
                                <div class="medicine-name">Cetirizine</div>
                                <div class="medicine-details">10mg - Once daily at night</div>
                            </div>
                            <div class="medicine-details">30 days</div>
                        </div>
                    </div>
                    
                    <div class="btn-group" style="margin-top: 15px;">
                        <button class="btn btn-primary">
                            <i class="fas fa-download"></i> Download PDF
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="tab-content" id="reminders">
                <h3 style="margin-bottom: 20px;">Medicine Reminders</h3>
                
                <div class="reminder-card">
                    <div class="reminder-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <div class="reminder-content">
                        <h4>Lisinopril</h4>
                        <p>Take 1 tablet (10mg) with water</p>
                        <div class="reminder-time">
                            <i class="far fa-clock"></i> Daily at 8:00 AM
                        </div>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <div class="reminder-card">
                    <div class="reminder-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <div class="reminder-content">
                        <h4>Aspirin</h4>
                        <p>Take 1 tablet (81mg) with water</p>
                        <div class="reminder-time">
                            <i class="far fa-clock"></i> Daily at 9:00 AM
                        </div>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <div class="reminder-card">
                    <div class="reminder-icon">
                        <i class="fas fa-pills"></i>
                    </div>
                    <div class="reminder-content">
                        <h4>Cetirizine</h4>
                        <p>Take 1 tablet (10mg) with water</p>
                        <div class="reminder-time">
                            <i class="far fa-clock"></i> Daily at 10:00 PM
                        </div>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <button class="btn btn-success">
                        <i class="fas fa-plus"></i> Add New Reminder
                    </button>
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