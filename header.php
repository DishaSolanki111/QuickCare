<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

 $currentPage = basename($_SERVER['PHP_SELF']);
?>

<!-- Header CSS -->
<style>
    :root {
        --primary: #0066cc;
        --primary-dark: #0052a3;
        --primary-light: #e6f2ff;
        --secondary: #00a8cc;
        --accent: #00a86b;
        --warning: #ff6b6b;
        --dark: #1a3a5f;
        --light: #f8fafc;
        --white: #ffffff;
        --text: #2c5282;
        --text-light: #4a6fa5;
        --gradient-1: linear-gradient(135deg, #0066cc 0%, #00a8cc 100%);
        --gradient-2: linear-gradient(135deg, #00a8cc 0%, #00a86b 100%);
        --gradient-3: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
        --shadow-sm: 0 2px 4px rgba(0,0,0,0.06);
        --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
        --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
        --shadow-xl: 0 20px 25px rgba(0,0,0,0.1);
        --shadow-2xl: 0 25px 50px rgba(0,0,0,0.25);
    }

    /* Header Styles */
    header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(0, 102, 204, 0.1);
    }

    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        padding: 1rem 2rem;
        margin: 0;
    }

    /* Logo Area */
    .logo-area {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .logo-area:hover {
        transform: scale(1.05);
    }

    .logo-img {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        object-fit: cover;
        box-shadow: var(--shadow-md);
    }

    .site-name {
        font-size: 1.8rem;
        font-weight: 700;
        background: var(--gradient-1);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }

    /* Navigation */
    .nav-right-container {
        display: flex;
        align-items: center;
        gap: 2rem;
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .nav-links a {
        text-decoration: none;
        color: var(--text);
        font-weight: 500;
        font-size: 1rem;
        position: relative;
        transition: all 0.3s ease;
        padding: 0.5rem 0;
    }

    .nav-links a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--primary);
        transition: width 0.3s ease;
    }

    .nav-links a:hover::after {
        width: 100%;
    }

    .nav-links a:hover {
        color: var(--primary);
    }

    .nav-links a.active {
        color: var(--primary);
        font-weight: 600;
    }

    /* User Profile - KEY PART */
    .user-profile {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        position: relative;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .user-avatar:hover {
        transform: scale(1.05);
        box-shadow: var(--shadow-md);
    }

    .user-info {
        display: flex;
        flex-direction: column;
    }

    .user-name {
        font-weight: 600;
        color: var(--text);
        font-size: 0.9rem;
    }

    .user-type {
        font-size: 0.8rem;
        color: #4a6fa5; /* Explicit color - pages must not override (index/doctors use --text-light: #fff for hero) */
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Auth Buttons */
    .auth-buttons {
        display: flex;
        gap: 0.8rem;
    }

    .btn-login, .btn-register {
        padding: 0.6rem 1.2rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-login {
        background: transparent;
        color: var(--primary);
        border: 2px solid var(--primary);
    }

    .btn-login:hover {
        background: var(--primary);
        color: white;
    }

    .btn-register {
        background: var(--primary);
        color: white;
    }

    .btn-register:hover {
        background: var(--primary-dark);
    }

    /* Profile Dropdown */
    .profile-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow-lg);
        min-width: 200px;
        padding: 0.5rem 0;
        margin-top: 0.5rem;
        display: none;
        z-index: 1001;
    }

    .profile-dropdown.active {
        display: block;
    }

    .profile-dropdown a {
        display: block;
        padding: 0.7rem 1rem;
        color: var(--text);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .profile-dropdown a:hover {
        background: var(--primary-light);
        color: var(--primary);
    }

    /* Mobile Menu */
    .mobile-menu-btn {
        display: none;
        background: var(--primary);
        border: none;
        border-radius: 8px;
        width: 40px;
        height: 40px;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .mobile-nav {
        position: fixed;
        top: 0;
        right: -350px;
        width: 350px;
        height: 100vh;
        background: white;
        box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
        z-index: 1001;
        transition: right 0.3s ease;
        overflow-y: auto;
    }

    .mobile-nav.active {
        right: 0;
    }

    .mobile-nav-header {
        background: var(--primary);
        padding: 1.5rem;
        color: white;
        position: relative;
    }

    .close-menu {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1rem;
        cursor: pointer;
    }

    .mobile-nav-links {
        padding: 1.5rem;
    }

    .mobile-nav-links a {
        display: block;
        text-decoration: none;
        color: var(--text);
        font-weight: 500;
        padding: 0.8rem;
        margin-bottom: 0.5rem;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .mobile-nav-links a:hover {
        background: var(--primary-light);
        color: var(--primary);
    }

    .mobile-user-profile {
        padding: 1rem 1.5rem;
        border-top: 1px solid #eee;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .mobile-user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--primary-light);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-weight: 600;
    }

    .mobile-user-info {
        flex: 1;
    }

    .mobile-user-name {
        font-weight: 600;
        color: var(--text);
    }

    .mobile-user-type {
        font-size: 0.8rem;
        color: #4a6fa5; /* Explicit color - consistent with desktop user-type */
        display: block !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Responsive */
    @media(max-width: 992px) {
        .nav-right-container {
            display: none;
        }
        .mobile-menu-btn {
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }

    @media(max-width: 768px) {
        .mobile-nav {
            width: 100%;
            right: -100%;
        }
    }
</style>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- Header HTML -->
<header>
    <nav class="navbar">
        <div class="logo-area">
            <img src="./uploads/logo.JPG" alt="QuickCare Logo" class="logo-img">
            <span class="site-name">QuickCare</span>
        </div>
        
        <div class="nav-right-container">
            <div class="nav-links">
                <a href="index.php" class="<?= ($currentPage == 'index.php') ? 'active' : '' ?>">Home</a>
                <a href="service.php" class="<?= ($currentPage == 'service.php') ? 'active' : '' ?>">Our services</a>
                <a href="schedule.php" class="<?= ($currentPage == 'schedule.php') ? 'active' : '' ?>">Schedule</a>
                <a href="appointment.php" class="<?= ($currentPage == 'appointment.php') ? 'active' : '' ?>">Doctors</a>
                <a href="aboutus.php" class="<?= ($currentPage == 'aboutus.php') ? 'active' : '' ?>">About us</a>
            </div>
            
            <?php if (isset($_SESSION['PATIENT_ID']) || isset($_SESSION['DOCTOR_ID']) || isset($_SESSION['RECEPTIONIST_ID']) || (isset($_SESSION['LOGGED_IN']) && $_SESSION['LOGGED_IN'] === true && isset($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE'] === 'admin')): ?>
                <!-- User Profile Section -->
                <div class="user-profile">
                    <div class="user-avatar" id="profile-icon">
                        <?php 
                        if (isset($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE'] === 'admin') {
                            echo isset($_SESSION['USER_NAME']) ? strtoupper(substr($_SESSION['USER_NAME'], 0, 2)) : 'AD';
                        } else if (isset($_SESSION['PATIENT_ID'])) {
                            echo isset($_SESSION['USER_NAME']) ? strtoupper(substr($_SESSION['USER_NAME'], 0, 2)) : 'PA';
                        } else if (isset($_SESSION['DOCTOR_ID'])) {
                            echo isset($_SESSION['USER_NAME']) ? strtoupper(substr($_SESSION['USER_NAME'], 0, 2)) : 'DR';
                        } else {
                            echo isset($_SESSION['USER_NAME']) ? strtoupper(substr($_SESSION['USER_NAME'], 0, 2)) : 'RE';
                        }
                        ?>
                    </div>
                    <div class="user-info">
                        <div class="user-name">
                            <?php 
                            if (isset($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE'] === 'admin') {
                                echo isset($_SESSION['USER_NAME']) ? htmlspecialchars($_SESSION['USER_NAME']) : 'Administrator';
                            } else if (isset($_SESSION['PATIENT_ID'])) {
                                echo isset($_SESSION['USER_NAME']) ? htmlspecialchars($_SESSION['USER_NAME']) : 'Patient';
                            } else if (isset($_SESSION['DOCTOR_ID'])) {
                                echo isset($_SESSION['USER_NAME']) ? 'Dr. ' . htmlspecialchars($_SESSION['USER_NAME']) : 'Doctor';
                            } else {
                                echo isset($_SESSION['USER_NAME']) ? htmlspecialchars($_SESSION['USER_NAME']) : 'Receptionist';
                            }
                            ?>
                        </div>
                        <!-- USER TYPE - ALWAYS VISIBLE -->
                        <div class="user-type">
                            <?php 
                            if (isset($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE'] === 'admin') {
                                echo 'Admin';
                            } else if (isset($_SESSION['PATIENT_ID'])) {
                                echo 'Patient';
                            } else if (isset($_SESSION['DOCTOR_ID'])) {
                                echo 'Doctor';
                            } else {
                                echo 'Receptionist';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <!-- Profile Dropdown Menu -->
                    <div class="profile-dropdown" id="profile-dropdown">
                        <?php 
                        $profile_link = '#';
                        $dashboard_link = '#';
                        
                        if (isset($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE'] === 'admin') {
                            $profile_link = 'admin.php';
                            $dashboard_link = 'admin.php';
                        } else if (isset($_SESSION['PATIENT_ID'])) {
                            $profile_link = 'patient_profile.php';
                            $dashboard_link = 'patient.php';
                        } else if (isset($_SESSION['DOCTOR_ID'])) {
                            $profile_link = 'doctor_profile.php';
                            $dashboard_link = 'doctor_dashboard.php';
                        } else if (isset($_SESSION['RECEPTIONIST_ID'])) {
                            $profile_link = 'receptionist_profile.php';
                            $dashboard_link = 'receptionist.php';
                        }
                        ?>
                        <a href="<?php echo $profile_link; ?>">
                            <i class="fas fa-user"></i> My Profile
                        </a>
                        <a href="<?php echo $dashboard_link; ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                        <a href="logout.php">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Auth Buttons -->
                <div class="auth-buttons">
                    <a href="login_for_all.php" class="btn-login">Login</a>
                    <a href="patientform.php" class="btn-register">Register</a>
                </div>
            <?php endif; ?>
        </div>
        
        <button class="mobile-menu-btn">☰</button>
    </nav>
</header>

<!-- Mobile Navigation -->
<div class="mobile-nav">
    <div class="mobile-nav-header">
        <h2>QuickCare Menu</h2>
        <button class="close-menu">✕</button>
    </div>
    <div class="mobile-nav-links">
        <a href="index.php">Home</a>
        <a href="schedule.php">Schedule</a>
        <a href="doctors.php">Doctors</a>
        <a href="aboutus.php">About Us</a>
        <a href="contactus.php">Contact</a>
        
        <?php if (isset($_SESSION['PATIENT_ID']) || isset($_SESSION['DOCTOR_ID']) || isset($_SESSION['RECEPTIONIST_ID']) || (isset($_SESSION['LOGGED_IN']) && $_SESSION['LOGGED_IN'] === true && isset($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE'] === 'admin')): ?>
            <div class="mobile-user-profile">
                <div class="mobile-user-avatar">
                    <?php 
                    if (isset($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE'] === 'admin') {
                        echo isset($_SESSION['USER_NAME']) ? strtoupper(substr($_SESSION['USER_NAME'], 0, 2)) : 'AD';
                    } else if (isset($_SESSION['PATIENT_ID'])) {
                        echo isset($_SESSION['USER_NAME']) ? strtoupper(substr($_SESSION['USER_NAME'], 0, 2)) : 'PA';
                    } else if (isset($_SESSION['DOCTOR_ID'])) {
                        echo isset($_SESSION['USER_NAME']) ? strtoupper(substr($_SESSION['USER_NAME'], 0, 2)) : 'DR';
                    } else {
                        echo isset($_SESSION['USER_NAME']) ? strtoupper(substr($_SESSION['USER_NAME'], 0, 2)) : 'RE';
                    }
                    ?>
                </div>
                <div class="mobile-user-info">
                    <div class="mobile-user-name">
                        <?php 
                        if (isset($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE'] === 'admin') {
                            echo isset($_SESSION['USER_NAME']) ? htmlspecialchars($_SESSION['USER_NAME']) : 'Administrator';
                        } else if (isset($_SESSION['PATIENT_ID'])) {
                            echo isset($_SESSION['USER_NAME']) ? htmlspecialchars($_SESSION['USER_NAME']) : 'Patient';
                        } else if (isset($_SESSION['DOCTOR_ID'])) {
                            echo isset($_SESSION['USER_NAME']) ? 'Dr. ' . htmlspecialchars($_SESSION['USER_NAME']) : 'Doctor';
                        } else {
                            echo isset($_SESSION['USER_NAME']) ? htmlspecialchars($_SESSION['USER_NAME']) : 'Receptionist';
                        }
                        ?>
                    </div>
                    <div class="mobile-user-type">
                        <?php 
                        if (isset($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE'] === 'admin') {
                            echo 'Admin';
                        } else if (isset($_SESSION['PATIENT_ID'])) {
                            echo 'Patient';
                        } else if (isset($_SESSION['DOCTOR_ID'])) {
                            echo 'Doctor';
                        } else {
                            echo 'Receptionist';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <?php 
            $profile_link = (isset($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE'] === 'admin') ? 'admin.php' : (isset($_SESSION['PATIENT_ID']) ? 'patient_profile.php' : (isset($_SESSION['DOCTOR_ID']) ? 'doctor_profile.php' : 'receptionist_profile.php'));
            $dashboard_link = (isset($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE'] === 'admin') ? 'admin.php' : (isset($_SESSION['PATIENT_ID']) ? 'patient.php' : (isset($_SESSION['DOCTOR_ID']) ? 'doctor_dashboard.php' : 'receptionist.php'));
            echo '<a href="' . $profile_link . '">My Profile</a>';
            echo '<a href="' . $dashboard_link . '">Dashboard</a>';
            ?>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login_for_all.php">Login</a>
            <a href="patientform.php">Register</a>
        <?php endif; ?>
    </div>
</div>

<!-- Add padding to body to account for fixed header -->
<style>
    body {
        padding-top: 80px !important;
    }
</style>

<script>
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const closeMenu = document.querySelector('.close-menu');
    const mobileNav = document.querySelector('.mobile-nav');

    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', () => {
            mobileNav.classList.add('active');
        });
    }

    if (closeMenu) {
        closeMenu.addEventListener('click', () => {
            mobileNav.classList.remove('active');
        });
    }

    // Profile dropdown toggle
    const profileIcon = document.getElementById('profile-icon');
    const profileDropdown = document.getElementById('profile-dropdown');
    
    if (profileIcon && profileDropdown) {
        profileIcon.addEventListener('click', () => {
            profileDropdown.classList.toggle('active');
        });
        
        document.addEventListener('click', (e) => {
            if (!profileIcon.contains(e.target) && !profileDropdown.contains(e.target)) {
                profileDropdown.classList.remove('active');
            }
        });
    }
</script>