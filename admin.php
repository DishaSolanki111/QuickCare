<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['LOGGED_IN']) || $_SESSION['LOGGED_IN'] !== true || $_SESSION['USER_TYPE'] !== 'admin') {
    // If not logged in or not an admin, redirect to admin login page
    header("Location: admin_login.php");
    exit();
}

// Get admin name for display
$adminName = $_SESSION['USER_NAME'] ?? 'Admin';

// Fetch dashboard stats from database
include 'config.php';

$total_patients = 0;
$r = mysqli_query($conn, "SELECT COUNT(*) as c FROM patient_tbl");
if ($r && $row = mysqli_fetch_assoc($r)) $total_patients = (int) $row['c'];

$total_appointments = 0;
$r = mysqli_query($conn, "SELECT COUNT(*) as c FROM appointment_tbl");
if ($r && $row = mysqli_fetch_assoc($r)) $total_appointments = (int) $row['c'];

$total_doctors = 0;
$r = mysqli_query($conn, "SELECT COUNT(*) as c FROM doctor_tbl");
if ($r && $row = mysqli_fetch_assoc($r)) $total_doctors = (int) $row['c'];

$total_revenue = 0;
$r = mysqli_query($conn, "SELECT COALESCE(SUM(AMOUNT), 0) as c FROM payment_tbl WHERE STATUS='COMPLETED'");
if ($r && $row = mysqli_fetch_assoc($r)) $total_revenue = (float) $row['c'];

$new_registrations = 0;
$r = mysqli_query($conn, "SELECT COUNT(*) as c FROM doctor_tbl WHERE STATUS != 'approved' OR STATUS IS NULL");
if ($r && $row = mysqli_fetch_assoc($r)) $new_registrations = (int) $row['c'];

$recent_appointments = [];
$r = mysqli_query($conn, "
    SELECT p.FIRST_NAME as p_first, p.LAST_NAME as p_last, d.FIRST_NAME as d_first, d.LAST_NAME as d_last,
           a.APPOINTMENT_DATE, a.STATUS
    FROM appointment_tbl a
    JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    ORDER BY a.APPOINTMENT_DATE DESC, a.APPOINTMENT_TIME DESC
    LIMIT 10
");
if ($r && mysqli_num_rows($r) > 0) {
    while ($row = mysqli_fetch_assoc($r)) $recent_appointments[] = $row;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
 <?php include 'admin_sidebar.php'; ?>
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
    --accent-color: #2ecc71;
    --danger-color: #e74c3c;
    --warning-color: #f39c12;
    --info-color: #17a2b8;
        }

    /* Main content */
    .main {
        margin-left: 250px;
        padding: 20px;
        width: calc(100% - 250px);
    }

    /* Top bar */
    .topbar {
        background: white;
        padding: 15px 25px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .topbar h1 {
        margin: 0;
        color: #064469;
    }

    /* Cards */
    .cards {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
    }

    .card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        flex: 1;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border-left: 8px solid #5790AB;
    }

    .card h3 {
        margin: 0;
        color: #072D44;
    }

    .card p {
        margin-top: 10px;
        font-size: 22px;
        color: #064469;
        font-weight: bold;
    }

    /* Table Section */
    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    th, td {
        padding: 14px;
        border-bottom: 1px solid #D0D7E1;
    }

    th {
        background: #5790AB;
        color: white;
        text-align: left;
    }

    tr:hover {
        background: #F2F9FB;
    }
    .logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
        }
    .reg-btn {
        background: linear-gradient(135deg, #5790AB 0%, #064469 100%);
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 4px 8px rgba(6, 68, 105, 0.2);
        position: relative;
        overflow: hidden;
    }
    .reg-btn:before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: 0.5s;
    }
    .reg-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(6, 68, 105, 0.3);
    }
    .reg-btn:hover:before {
        left: 100%;
    }
    .reg-btn:active {
        transform: translateY(1px);
        box-shadow: 0 2px 4px rgba(6, 68, 105, 0.2);
    }

</style>
</head>

<body>

<!-- Include Sidebar -->
<?php include 'admin_sidebar.php'; ?>

<!-- Main Content -->
<div class="main">

    <!-- Topbar -->
    <div class="topbar">
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($adminName); ?></p>
    </div>

    <!-- Cards -->
    <div class="cards">
        <div class="card">
            <h3>Total Patients</h3>
            <p><?php echo $total_patients; ?></p>
        </div>

        <div class="card">
            <h3>Total Appointments</h3>
            <p><?php echo $total_appointments; ?></p>
        </div>

        <div class="card">
            <h3>Total Doctors</h3>
            <p><?php echo $total_doctors; ?></p>
        </div>
        <div class="card">
            <h3>Total Revenue</h3>
            <p>₹<?php echo number_format($total_revenue, 2); ?></p>
        </div>
        
        <div class="card">
          <h3>New Registrations</h3>
          <p><?php echo $new_registrations; ?></p>
        </div>
    </div>
    <!-- Registration Links -->
    <div class="cards">
        <div class="card">
            <a href="doctorform.php" class="reg-btn">Doctor New Registrations</a>
        </div>
        <div class="card">
            <a href="recpt_regis.php" class="reg-btn">Receptionist New Registrations</a>       
        </div>
    </div>  

    <!-- Table -->
    <h2 style="color:#064469;">Recent Appointments</h2>
    <table>
        <tr>
            <th>Patient</th>
            <th>Doctor</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
        <?php if (count($recent_appointments) > 0): ?>
            <?php foreach ($recent_appointments as $ra): ?>
        <tr>
            <td><?php echo htmlspecialchars($ra['p_first'] . ' ' . $ra['p_last']); ?></td>
            <td>Dr. <?php echo htmlspecialchars($ra['d_first'] . ' ' . $ra['d_last']); ?></td>
            <td><?php echo date('d M', strtotime($ra['APPOINTMENT_DATE'])); ?></td>
            <td><?php echo htmlspecialchars($ra['STATUS']); ?></td>
        </tr>
            <?php endforeach; ?>
        <?php else: ?>
        <tr><td colspan="4">No appointments found</td></tr>
        <?php endif; ?>
    </table>

</div>

</body>
</html>