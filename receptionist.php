<?php
session_start();

if (!isset($_SESSION['RECEPTIONIST_ID'])) {
    header("Location: login_for_all.php");
    exit;
}

include 'config.php';
$receptionist_id = $_SESSION['RECEPTIONIST_ID'];

// Fetch receptionist name from database
$receptionist_name = 'Receptionist';
$rec_query = mysqli_query($conn, "SELECT FIRST_NAME, LAST_NAME FROM receptionist_tbl WHERE RECEPTIONIST_ID = '$receptionist_id'");
if ($rec_query && $rec_row = mysqli_fetch_assoc($rec_query)) {
    $receptionist_name = htmlspecialchars($rec_row['FIRST_NAME'] . ' ' . $rec_row['LAST_NAME']);
}

// Fetch stats from database
$total_appointments = 0;
$apt_result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM appointment_tbl");
if ($apt_result && $row = mysqli_fetch_assoc($apt_result)) {
    $total_appointments = (int) $row['cnt'];
}

$medicine_reminders = 0;
$rem_result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM medicine_reminder_tbl");
if ($rem_result && $row = mysqli_fetch_assoc($rem_result)) {
    $medicine_reminders = (int) $row['cnt'];
}

$medicine_count = 0;
$med_result = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM medicine_tbl");
if ($med_result && $row = mysqli_fetch_assoc($med_result)) {
    $medicine_count = (int) $row['cnt'];
}

$conn->close();
?>
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
    --white: #ffffff;
    
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


    

    

/* ---------------- MAIN CONTENT ---------------- */
.main-content {
    margin-left: 240px;
    padding: 20px;
    width: calc(100% - 240px);
}

.page-title {
    font-size: 26px;
    font-weight: bold;
    color: var(--dark-blue);
}

/* ---------------- STATS CARDS ---------------- */
.stats-container {
    display: flex;
    gap: 20px;
    margin-top: 20px;
}

.stat-card {
    flex: 1;
    background: var(--white);
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0px 3px 10px rgba(0,0,0,0.1);
}

.stat-card h3 {
    margin: 0;
    font-size: 22px;
    color: var(--dark-blue);
}

.stat-card p {
    font-size: 14px;
    color: #666;
}
.logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
        }
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
            padding: 20px;
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
</style>

</head>
<?php include 'recept_sidebar.php'; ?>
<!-- ---------------- MAIN CONTENT ---------------- -->
<div class="main-content">
    <!-- Header -->
    <?php include 'receptionist_header.php'; ?>
    <!-- Stats Cards -->
    <div class="card">
        <div class="card-header">
         
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="stats-card">
                        <i class="bi bi-calendar-check stats-icon"></i>
                        <div class="stats-number"><?php echo $total_appointments; ?></div>
                        <div class="stats-label">Total Appointments</div>
                    </div>
                </div>
               
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="stats-card">
                        <i class="bi bi-bell stats-icon"></i>
                        <div class="stats-number"><?php echo $medicine_reminders; ?></div>
                        <div class="stats-label">Medicine Reminders</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="stats-card">
                        <i class="bi bi-capsule stats-icon"></i>
                        <div class="stats-number"><?php echo $medicine_count; ?></div>
                        <div class="stats-label">Medicines</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>
