<?php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Profile - QuickCare Medical System</title>
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
            --card-bg: #F6F9FB;
            --primary-color: #1a3a5f;
            --secondary-color: #3498db;
            --accent-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #17a2b8;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
         
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
            border-left: 10px solid #9CCDD8;
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
        
        
        .container-fluid {
            padding: 0;
        }
        
        .row {
            margin: 0;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        
        .page-header {
            margin-bottom: 30px;
        }
        
        .page-title {
            font-size: 28px;
            font-weight: 600;
            color: var(--primary-dark-blue);
            margin-bottom: 5px;
        }
        
        .breadcrumb {
            background-color: transparent;
            padding: 0;
            margin-bottom: 0;
        }
        
        .breadcrumb-item {
            color: var(--gray-dark);
        }
        
        .breadcrumb-item.active {
            color: var(--primary-dark-blue);
        }
        
        /* Card Styles */
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
            padding: 50px;
        }
        
        /* Personal Information Styles */
        .profile-img-container {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .profile-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--light-blue);
        }
        
        .profile-name {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-dark-blue);
            margin-top: 15px;
        }
        
        .profile-id {
            color: var(--gray-dark);
            font-size: 14px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 15px;
        }
        
        .info-label {
            width: 150px;
            font-weight: 500;
            color: var(--gray-dark);
        }
        
        .info-value {
            flex: 1;
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
        
        /* Professional Information Styles */
        .professional-info {
            background-color: var(--white);
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }
        
        .info-group {
            margin-bottom: 20px;
        }
        
        .info-group-title {
            font-weight: 600;
            color: var(--primary-dark-blue);
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid var(--gray-medium);
        }
        
        /* Button Styles */
        .btn-primary {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
            font-weight: 500;
            padding: 8px 20px;
            border-radius: 4px;
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-blue);
            border-color: var(--secondary-blue);
        }
        
        .btn-outline-primary {
            color: var(--accent-blue);
            border-color: var(--accent-blue);
            font-weight: 500;
            padding: 8px 20px;
            border-radius: 4px;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--accent-blue);
            border-color: var(--accent-blue);
        }
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .main-content {
                margin-left: 70px;
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .mobile-menu-toggle {
                display: block;
                position: fixed;
                top: 20px;
                left: 20px;
                z-index: 101;
                background-color: var(--primary-dark-blue);
                color: var(--white);
                border: none;
                border-radius: 4px;
                padding: 8px 12px;
            }
        }
        
        @media (min-width: 769px) {
            .mobile-menu-toggle {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
              <div class="container">
        <!-- Sidebar - Replaced with patient.php sidebar -->
        <div class="sidebar">
    <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
    <h2>QuickCare</h2>

    <a href="receptionist.php" >Dashboard</a>
    <a href="recep_profile.php">My Profile</a>
    <a href="#">Appoitment</a>
    <a href="#">Doctor</a>
    <a href="#">patient</a>
    <a href="#">View Medicine</a>
    <a href="#">Reminder</a>
    <a href="#">payment</a>
    <button class="logout-btn">logout</button>

    
</div>    
            <!-- Main Content -->
            <div class="main-content">
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="bi bi-list"></i>
                </button>
                
                <div class="page-header">
                    <h1 class="page-title">My Profile</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">My Profile</li>
                        </ol>
                    </nav>
                </div>
                
                <div class="row">
                    <!-- Personal Information -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="profile-img-container">
                                    <img src="https://picsum.photos/seed/receptionist1/300/300.jpg" alt="Profile Picture" class="profile-img">
                                    <h2 class="profile-name">John Doe</h2>
                                    <p class="profile-id">Receptionist ID: REC001</p>
                                </div>
                                
                                <div class="info-row">
                                    <div class="info-label">First Name:</div>
                                    <div class="info-value">John</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Last Name:</div>
                                    <div class="info-value">Doe</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Date of Birth:</div>
                                    <div class="info-value">March 15, 1992</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Gender:</div>
                                    <div class="info-value">Male</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Phone:</div>
                                    <div class="info-value">9911111111</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Email:</div>
                                    <div class="info-value">john.doe@hospital.com</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Address:</div>
                                    <div class="info-value">Near Main Gate</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Username:</div>
                                    <div class="info-value">j.doe</div>
                                </div>
                                <div class="info-row">
                                    <div class="info-label">Date of Joining:</div>
                                    <div class="info-value">January 10, 2022</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistics and Professional Information -->
                    <div class="col-lg-8">
                        <!-- Statistics -->
                        <div class="card">
                            <div class="card-header">
                                Statistics
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
                                            <i class="bi bi-people stats-icon"></i>
                                            <div class="stats-number">10</div>
                                            <div class="stats-label">Doctors Managed</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-sm-6 mb-4">
                                        <div class="stats-card">
                                            <i class="bi bi-bell stats-icon"></i>
                                            <div class="stats-number">9</div>
                                            <div class="stats-label">Reminders</div>
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
                        
                        <!-- Professional Information -->
                        <div class="card">
                            <div class="card-header">
                                Professional Information
                            </div>
                            <div class="card-body">
                                <div class="professional-info">
                                    <div class="info-group">
                                        <div class="info-group-title">Employee Details</div>
                                        <div class="info-row">
                                            <div class="info-label">Employee ID:</div>
                                            <div class="info-value">REC001</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label">Department:</div>
                                            <div class="info-value">Reception</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label">Work Location:</div>
                                            <div class="info-value">Main Reception</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label">Shift:</div>
                                            <div class="info-value">9:00 AM - 5:00 PM</div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary">
                                            <i class="bi bi-download me-2"></i>Download Profile
                                        </button>
                                        <button class="btn btn-outline-primary">
                                            <i class="bi bi-award me-2"></i>View Certificates
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Mobile menu toggle
        document.getElementById('mobileMenuToggle').addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar) {
                sidebar.classList.toggle('active');
            }
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.querySelector('.sidebar');
            const toggle = document.getElementById('mobileMenuToggle');
            
            if (window.innerWidth <= 768 && 
                sidebar && 
                !sidebar.contains(event.target) && 
                toggle && 
                !toggle.contains(event.target) && 
                sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                const sidebar = document.querySelector('.sidebar');
                if (sidebar) {
                    sidebar.classList.remove('active');
                }
            }
        });
    </script>
</body>
</html>