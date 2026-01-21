
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receptionist Dashboard - QuickCare</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
<style>
/* ----------------- COLOR PALETTE ----------------- */
:root {
    --dark-blue: #072D44;     /* Sidebar */
    --mid-blue: #064469;      /* Top Navbar */
    --soft-blue: #5790AB;     /* Hover / Active */
    --light-blue: #9CCDD8;    /* Cards */
    --gray-blue: #D0D7E1;     /* Text/Icons */
    --white: #ffffff;
    --dark-blue: #072D44;
    --mid-blue: #064469;
    --soft-blue: #5790AB;
    --light-blue: #9CCDD8;
    --gray-blue: #D0D7E1;
   
}

/* ---------------- GLOBAL STYLES ---------------- */
body {
    margin: 0;
    font-family: Arial, sans-serif;
    font-weight: bold;
    background: #F5F8FA;
    display: flex;
}

/* ---------------- SIDEBAR ---------------- */
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
         /* Custom scrollbar for sidebar */ 
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
                  position: relative; 
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
        
</style>
</head>     
<body>

<!-- ---------------- SIDEBAR ---------------- -->
<div class="sidebar">
    <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
        <h2>QuickCare</h2>
    

    <a href="receptionist.php" class="active">Dashboard</a>
    <a href="recep_profile.php">View My Profile</a>
    <a href="appointment_recep.php">Manage Appointments</a>
    <a href="doctor_schedule_recep.php">Manage Doctor Schedule</a>
    <a href="manage_medicine.php">Manage Medicine</a>
    <a href="st_reminder.php">Set Reminder</a>
    <a href="manage_user_profile.php">Manage User Profile</a>
    <a href="view_prescription.php">View Prescription</a>
    <a href="logout.php" class="logout-btn">logout</a>
   
</div>
</body>
</html>