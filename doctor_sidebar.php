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
             height: 100vh; 
             color: white; 
              padding-top: 30px;
               position: fixed; 
               left: 0; 
               top: 0; 
               overflow-y: auto; 
               z-index: 1000;
               box-shadow: 2px 0 5px rgba(0,0,0,0.1);
                transition: transform 0.3s ease;
         } 
         .sidebar::-webkit-scrollbar { 
            width: 8px; 
            } 
            .sidebar::-webkit-scrollbar-track {
                 background: var(--mid-blue); 
                } 
                 .sidebar::-webkit-scrollbar-thumb {
                 background: var(--light-blue); 
                 border-radius: 4px; 
                } 
            .sidebar::-webkit-scrollbar-thumb:hover { 
                background: var(--gray-blue); 
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
        <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
        <h2>QuickCare</h2>
        <a href="index.php">Home</a>
        <a href="doctor_dashboard.php" >Dashboard</a>
        <a href="doctor_profile.php">My Profile</a>
        <a href="mangae_schedule_doctor.php">Manage Schedule</a>
        <a href="appointment_doctor.php">Manage Appointments</a>
        <a href="manage_prescriptions.php">Manage Prescription</a>
        <a href="view_medicine.php" >View Medicine</a>
        <a href="doctor_feedback.php">View Feedback</a>
        <a href="logout.php" class="logout-btn">Logout</a>
    </div>
</script>

</body>
</html>