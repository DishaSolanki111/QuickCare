<?php
session_start();
include "doctor_sidebar.php";
// Check if user is logged in and is a doctor
if (
    !isset($_SESSION['LOGGED_IN']) ||
    $_SESSION['LOGGED_IN'] !== true ||
    !isset($_SESSION['USER_TYPE']) ||
    $_SESSION['USER_TYPE'] !== 'doctor'
) {
    header("Location: login.php");
    exit();
}

// Include your existing config file
require_once 'config.php';

// Check if connection variable $conn exists and is valid
if (!$conn) {
    die("Error: Database connection failed. Please check your config.php file.");
}

// Get doctor information from session
 $doctor_id = $_SESSION['DOCTOR_ID'];
 $doctor_name = "Doctor";

// Fetch doctor's name from database
 $doc_sql = "SELECT FIRST_NAME, LAST_NAME FROM doctor_tbl WHERE DOCTOR_ID = ?";
 $doc_stmt = $conn->prepare($doc_sql);
 $doc_stmt->bind_param("i", $doctor_id);
 $doc_stmt->execute();
 $doc_result = $doc_stmt->get_result();

if ($doc_result->num_rows === 1) {
    $doc = $doc_result->fetch_assoc();
    $doctor_name = htmlspecialchars($doc['FIRST_NAME'] . ' ' . $doc['LAST_NAME']);
}
 $doc_stmt->close();

// Check for success message in URL
 $alert_message = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        $alert_message = "Prescription added successfully!";
    } elseif ($_GET['status'] === 'deleted_success') {
        $alert_message = "Prescription deleted successfully!";
    }
}

// Fetch all patients from database
 $sql = "SELECT PATIENT_ID, FIRST_NAME, LAST_NAME, DOB, PHONE FROM patient_tbl ORDER BY LAST_NAME, FIRST_NAME";
 $result = $conn->query($sql);

 $patients = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}
 $conn->close(); // Close connection when done
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Prescriptions - QuickCare</title>
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
        
        /* Table Styles */
        .content-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #0056b3;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        thead {
            background-color: #007bff;
            color: white;
        }
        
        tbody tr:hover {
            background-color: #f5f5f5;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            color: #fff;
            background-color: #28a745;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        
        .btn:hover {
            background-color: #218838;
        }
        
        /* Footer Styles */
        footer {
            background: var(--dark);
            color: white;
            padding: 3rem 5%;
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-link {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .social-link:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }
        
        .logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar h2 span, .sidebar a span {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
      
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="welcome-msg">Manage Patient Prescriptions</div>
                <div class="user-actions">
                    <button class="notification-btn">
                        <i class="far fa-bell"></i>
                        <span class="notification-badge">5</span>
                    </button>
                    <div class="user-dropdown">
                        <div class="user-avatar"><?php echo substr($doctor_name, 0, 1); ?></div>
                        <span>Dr. <?php echo $doctor_name; ?></span>
                        <i class="fas fa-chevron-down" style="margin-left: 8px;"></i>
                    </div>
                </div>
            </div>
            
            <!-- Content Card -->
            <div class="content-card">
                <p>Select a patient to view, add, or delete their prescriptions.</p>
                
                <table>
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Date of Birth</th>
                            <th>Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($patients) > 0): ?>
                            <?php foreach ($patients as $patient): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['DOB']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['PHONE']); ?></td>
                                    <td>
                                        <a href="prescription_form.php?patient_id=<?php echo $patient['PATIENT_ID']; ?>" class="btn">Manage Prescriptions</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No patients found in the database.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Footer -->
            <footer>
                <div class="footer-content">
                    <p>&copy; <span id="year"></span> QuickCare — Revolutionizing Healthcare Access</p>
                    <div class="social-links">
                        <a href="#" class="social-link"><span>f</span></a>
                        <a href="#" class="social-link"><span>𝕏</span></a>
                        <a href="#" class="social-link"><span>in</span></a>
                        <a href="#" class="social-link"><span>📷</span></a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <!-- JavaScript to show pop-up alert -->
    <?php if (!empty($alert_message)): ?>
        <script>
            // Wait for page to fully load before showing alert
            window.onload = function() {
                alert('<?php echo addslashes($alert_message); ?>');
            }
        </script>
    <?php endif; ?>
    
    <script>
        document.getElementById('year').textContent = new Date().getFullYear();
    </script>
</body>
</html>