<?php
session_start();
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
if (isset($_POST['status'])) {
    $status = $_POST['status'];
    if ($status === 'success') {
        $alert_message = "Prescription added successfully!";
    } elseif ($status === 'deleted_success') {
        $alert_message = "Prescription deleted successfully!";
    }
}

// Filter parameters (GET)
$filter_name = isset($_GET['filter_name']) ? trim(mysqli_real_escape_string($conn, $_GET['filter_name'])) : '';
$filter_date = isset($_GET['filter_date']) ? mysqli_real_escape_string($conn, $_GET['filter_date']) : '';

// Fetch only patients who have appointments with this doctor
// Apply name and date filters when provided
$conditions = ["a.DOCTOR_ID = ?"];
$types = "i";
$params = [$doctor_id];

if (!empty($filter_name)) {
    $conditions[] = "(pt.FIRST_NAME LIKE ? OR pt.LAST_NAME LIKE ? OR CONCAT(pt.FIRST_NAME, ' ', pt.LAST_NAME) LIKE ?)";
    $types .= "sss";
    $name_pattern = '%' . $filter_name . '%';
    $params[] = $name_pattern;
    $params[] = $name_pattern;
    $params[] = $name_pattern;
}
if (!empty($filter_date)) {
    $conditions[] = "a.APPOINTMENT_DATE = ?";
    $types .= "s";
    $params[] = $filter_date;
}

$sql = "SELECT pt.PATIENT_ID, pt.FIRST_NAME, pt.LAST_NAME, pt.PHONE, pt.EMAIL,
                MAX(a.APPOINTMENT_DATE) AS LAST_APPOINTMENT_DATE
         FROM patient_tbl pt
         INNER JOIN appointment_tbl a ON pt.PATIENT_ID = a.PATIENT_ID
         WHERE " . implode(" AND ", $conditions) . "
         GROUP BY pt.PATIENT_ID, pt.FIRST_NAME, pt.LAST_NAME, pt.PHONE, pt.EMAIL
         ORDER BY pt.LAST_NAME, pt.FIRST_NAME";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$patients = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}
$stmt->close();
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
            margin-top:-15px;
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
        
        .btn-primary {
            background-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .filter-group label {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 14px;
        }
        .filter-group input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            min-width: 180px;
        }
        .filter-actions {
            display: flex;
            gap: 10px;
            align-items: flex-end;
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
        
        <?php include 'doctor_sidebar.php'; ?>
        <!-- Main Content -->
        <div class="main-content">
        <?php include 'doctor_header.php'; ?>    
            
            <!-- Content Card -->
            <div class="content-card">
         
                
                <h1>Manage Prescriptions</h1>
                <p style="margin-bottom: 20px; color: #666;">Click "Manage Prescriptions" to view all prescriptions (instructions) for that patient.</p>
                
                <form method="GET" action="manage_prescriptions.php" class="filter-form">
                    <div class="filter-group">
                        <label for="filter_name"><i class="fas fa-user"></i> Patient Name</label>
                        <input type="text" id="filter_name" name="filter_name" placeholder="Search by name..." value="<?php echo htmlspecialchars($filter_name); ?>">
                    </div>
                    <div class="filter-group">
                        <label for="filter_date"><i class="fas fa-calendar"></i> Appointment Date</label>
                        <input type="date" id="filter_date" name="filter_date" value="<?php echo htmlspecialchars($filter_date); ?>">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                        <a href="manage_prescriptions.php" class="btn btn-secondary"><i class="fas fa-times"></i> Clear</a>
                    </div>
                </form>
                
                <table>
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Appointment Date</th>
                            <th>Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($patients) > 0): ?>
                            <?php foreach ($patients as $patient): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></td>
                                    <td><?php 
                                        echo !empty($patient['LAST_APPOINTMENT_DATE']) 
                                            ? htmlspecialchars((new DateTime($patient['LAST_APPOINTMENT_DATE']))->format('F d, Y')) 
                                            : 'N/A'; 
                                    ?></td>
                                    <td>
                                        <?php 
                                        $contact_parts = [];
                                        if (!empty($patient['PHONE'])) $contact_parts[] = 'Ph: ' . htmlspecialchars($patient['PHONE']);
                                        if (!empty($patient['EMAIL'])) $contact_parts[] = htmlspecialchars($patient['EMAIL']);
                                        echo implode(' | ', $contact_parts ?: ['N/A']);
                                        ?>
                                    </td>
                                    <td>
                                        <form method="POST" action="prescription_form.php" style="display:inline">
                                            <input type="hidden" name="patient_id" value="<?php echo (int)$patient['PATIENT_ID']; ?>">
                                            <button type="submit" class="btn">Manage Prescriptions</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" style="text-align: center;">No patients found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
           
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