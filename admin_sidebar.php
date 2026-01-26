<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sidebar Navigation</title>
<style>
    body {
        margin: 0;
        font-family: Arial, sans-serif;
        background: #D0D7E1;
        display: flex;
    }
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
    
        }

    /* Sidebar */
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
            
                  
                } 
        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #9CCDD8;
        }
         .sidebar::-webkit-scrollbar-thumb {
                 background: var(--light-blue); 
                 border-radius: 4px; 
                } 
            .sidebar::-webkit-scrollbar-thumb:hover { 
                background: var(--gray-blue); 
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

    .logo-img {
        height: 40px;
        margin-right: 12px;
        border-radius: 5px;
    }

    /* Mobile menu toggle */
    .menu-toggle {
        display: none;
        position: fixed;
        top: 20px;
        left: 20px;
        z-index: 1000;
        background: #072D44;
        color: white;
        border: none;
        border-radius: 5px;
        padding: 10px;
        cursor: pointer;
    }

    /* Responsive styles */
    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .menu-toggle {
            display: block;
        }

        .main {
            margin-left: 0;
            width: 100%;
        }
    }
</style>
</head>
<body>

<!-- Mobile Menu Toggle Button -->
<button class="menu-toggle">☰ Menu</button>

<!-- Sidebar -->
<div class="sidebar">
  <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
  <h2 align="center">QuickCare</h2>
    <a href="index.php">Home</a>
    <a href="admin.php">Dashboard</a>  
    <a href="Admin_appoitment.php">View Appointments</a>
    <a href="Admin_doctor.php">Manage Doctors</a>
    <a href="Admin_recept.php">Manage Receptionist</a>
    <a href="Admin_patient.php">Manage Patients</a>
    <a href="Admin_medicine.php">View Medicine</a>
    <a href="Admin_payment.php">View Payments</a>
    <a href="Admin_feedback.php">View Feedback</a>
  
    <button class="logout-btn">logout</button>
</div>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        
        if (menuToggle) {
            menuToggle.addEventListener('click', function() {
                sidebar.classList.toggle('active');
            });
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768 && 
                !sidebar.contains(event.target) && 
                !menuToggle.contains(event.target)) {
                sidebar.classList.remove('active');
            }
        });
    });
</script>

</body>
</html>