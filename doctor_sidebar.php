<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sidebar Component</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
/* ================== GLOBAL STYLES & VARIABLES ================== */
:root {
    --dark-blue: #072D44;
    --mid-blue: #064469;
    --soft-blue: #5790AB;
    --light-blue: #9CCDD8;
    --gray-blue: #D0D7E1;
    --white: #ffffff;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

body {
    background-color: #f5f8fa;
    color: #333;
    line-height: 1.6;
    overflow-x: hidden;
}

/* ================== LAYOUT CONTAINER ================== */
.container {
    display: flex;
    min-height: 100vh;
}

/* ================== SIDEBAR STYLES ================== */
.sidebar {
    width: 250px;
    background: var(--dark-blue);
    min-height: 100vh;
    color: white;
    padding-top: 30px;
    position: fixed; /* Keeps sidebar in place while scrolling */
    z-index: 100;    /* Ensures sidebar is on top of other content */
    transition: transform 0.3s ease; /* Smooth slide-in/out for mobile */
}

.logo-img {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    margin-bottom: 15px;
    object-fit: cover;
    border: 3px solid var(--light-blue);
    display: block;
    margin-left: auto;
    margin-right: auto;
}

.sidebar h2 {
    text-align: center;
    margin-bottom: 40px;
    color: var(--light-blue);
    font-size: 24px;
}

.sidebar a {
    display: block;
    padding: 15px 25px;
    color: var(--gray-blue);
    text-decoration: none;
    font-size: 17px;
    border-left: 4px solid transparent;
    transition: all 0.3s ease;
}

.sidebar a:hover, .sidebar a.active {
    background: var(--mid-blue);
    border-left: 4px solid var(--light-blue);
    color: var(--white);
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

.logout-btn:hover {
    background-color: var(--light-blue);
}



/* ================== RESPONSIVE DESIGN ================== */

/* Tablet and smaller */
@media (max-width: 992px) {
    .sidebar {
        width: 70px;
    }
    
    .sidebar h2, .sidebar a span {
        display: none; /* Hide text */
    }
    
    .sidebar a {
        text-align: center; /* Center the icons */
    }
    
    .sidebar a i {
        margin: 0; /* Remove any icon margin */
        font-size: 20px;
    }
    
    .main-content {
        margin-left: 70px; /* Adjust margin for collapsed sidebar */
    }
}

/* Mobile */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%); /* Hide sidebar off-screen */
    }
    
    .sidebar.active {
        transform: translateX(0); /* Show sidebar when active class is added */
    }
    
  




</style>
</head>
<body>



    <!-- ================== SIDEBAR HTML ================== -->
    <div class="sidebar" id="sidebar"> 
        <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img">  
        <h2>QuickCare</h2>

        <a href="doctor_dashboard.php" class="active">
         <span>Dashboard</span>
        </a>
        <a href="d_profile.php">
            <span>My Profile</span>
        </a>
        <a href="mangae_schedule_doctor.php">
          <span>Manage Schedule</span>
        </a>
        <a href="appointment_doctor.php">
          <span>Manage Appointments</span>
        </a>
        <a href="manage_prescriptions.php">
            <span>Manage Prescription</span>
        </a>
        <a href="view_medicine.php">
       <span>View Medicine</span>
        </a>
        <a href="doctor_feedback.php">
          <span>View Feedback</span>
        </a>
        <button class="logout-btn">
         Logout
        </button>
    </div>

</script>

</body>
</html>