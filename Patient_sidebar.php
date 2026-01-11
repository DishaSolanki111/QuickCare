<!DOCTYPE html>
<html lang="en"> 
<head> <meta charset="UTF-8"> 
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Scrollable Sidebar - QuickCare</title> <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
 <style> 
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
    } 
    body {
             font-family: Arial, sans-serif; 
             background: #D0D7E1; 
             display:flex;
             height: 100vh;
              overflow: hidden; 
         } 
              /* Container for the entire layout */ 
     .container { 
        display: flex;
         width: 100%; 
         height: 100%; 
        } 
        /* Sidebar with scrolling */
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
                } .sidebar .logo-img { 
                    display: block; 
                    margin: 0 auto 20px auto; 
                    width: 80px; 
                    height: 80px;
                     border-radius: 50%;
                      border: 3px solid var(--light-blue); 
                      } 
                      .sidebar .nav {
                         padding-bottom: 30px; 
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
            .sidebar a i {
                 margin-right: 10px; 
                 width: 20px; 
                 text-align: center; 
                } 
            .sidebar a:hover, .sidebar a.active { 
                background: var(--mid-blue); 
                border-left: 4px solid var(--light-blue); 
                color: var(--white); 
                } 
            .sidebar a:hover::before {
                 content: ''; 
                 position: absolute; 
                 left: 0; 
                 top: 0; 
                 height: 100%; 
                 width: 4px; 
                 background: var(--soft-blue); 
                 }
             .logout-btn { 
                display: block;
                 width: 80%;
                  margin: 20px auto 30px auto;
                   padding: 12px; 
                   background-color: var(--soft-blue);
                    color: var(--white); 
                 border: none; 
                 border-radius: 5px; 
                 cursor: pointer; 
                 font-size: 16px; 
                 text-align: center; 
                 transition: all 0.3s ease; 
                 font-weight: 600; }
                  .logout-btn:hover { 
                    background-color: var(--light-blue); transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); } /* Main content */ .main { margin-left: 250px; padding: 20px; width: calc(100% - 250px); height: 100vh; overflow-y: auto; background: linear-gradient(135deg, #f5f8ff 0%, #e6f0ff 100%); } /* Custom scrollbar for main content */ .main::-webkit-scrollbar { width: 8px; } .main::-webkit-scrollbar-track { background: #f1f1f1; } .main::-webkit-scrollbar-thumb { background: var(--soft-blue); border-radius: 4px; } .main::-webkit-scrollbar-thumb:hover { background: var(--mid-blue); } /* Header */ .header { background: white; padding: 20px 30px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); display: flex; justify-content: space-between; align-items: center; } .header h1 { color: var(--dark-blue); font-size: 28px; font-weight: 700; } .menu-toggle { display: none; background: none; border: none; font-size: 24px; color: var(--dark-blue); cursor: pointer; } /* Content cards */ .content-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); margin-bottom: 20px; } .content-card h2 { color: var(--dark-blue); margin-bottom: 15px; } .content-card p { color: #666; line-height: 1.6; } /* Features grid */ .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px; } .feature-card { background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); text-align: center; transition: transform 0.3s ease; } .feature-card:hover { transform: translateY(-5px); } .feature-card i { font-size: 40px; color: var(--soft-blue); margin-bottom: 15px; } .feature-card h3 { color: var(--dark-blue); margin-bottom: 10px; } /* Mobile responsiveness */ @media (max-width: 768px) { .sidebar { width: 250px; transform: translateX(-100%); } .sidebar.active { transform: translateX(0); } .main { margin-left: 0; width: 100%; } .menu-toggle { display: block; } .features-grid { grid-template-columns: 1fr; } } </style> 
                    </head> 
                    <body> 
                        <div class="container"> <!-- SIDEBAR --> <div class="sidebar"> <img src="./uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;"> <h2>QuickCare</h2> <div class="nav"> <a href="patient.php">Dashboard</a> <a href="patient_profile.php"> My Profile</a> <a href="view_doctor_patient.php">View Doctor Profile</a> <a href="manage_appointments.php">Manage Appointments</a> <a href="doctor_schedule.php">View Doctor Schedule</a> <a href="patinet_prescriptions.php">My Prescriptions</a> <a href="medicine_reminder.php">Medicine Reminder</a> <a href="patient_payments.php">Payments</a> <a href="patient_feedback.php">Feedback</a> <a href="logout.php" class="logout-btn">logout</a> </div> </div> </script> </body> </html>