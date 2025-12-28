<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Payments - QuickCare</title>
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

    /* Sidebar */
    .sidebar {
            width: 250px;
            background: #072D44;
            min-height: 100vh;
            color: white;
            padding-top: 30px;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #9CCDD8;
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
    .filter-container {
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .filter-container form {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
    }
    .filter-container input, .filter-container select {
        padding: 10px;
        border: 1px solid #D0D7E1;
        border-radius: 5px;
    }
    .filter-container button {
        padding: 10px 15px;
        background: #5790AB;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .filter-container button:hover {
        background: #064469;
    }
    .action-btn {
        padding: 5px 10px;
        margin: 0 2px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
    }
    .view-btn {
        background-color: #3498db;
        color: white;
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }
    .status-completed {
        background-color: #2ecc71;
        color: white;
    }
    .status-failed {
        background-color: #e74c3c;
        color: white;
    }
    .payment-mode-badge {
        padding: 3px 8px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: bold;
        background-color: #3498db;
        color: white;
    }
    .revenue-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border-left: 8px solid #2ecc71;
    }
    .revenue-card h3 {
        margin: 0;
        color: #072D44;
    }
    .revenue-card p {
        margin-top: 10px;
        font-size: 22px;
        color: #064469;
        font-weight: bold;
    }
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
        <h2>QuickCare</h2>

    
    <a href="admin.php" class="active">Dashboard</a>
    <a href="Admin_appoitment.php" >View Appointments</a>
    <a href="Admin_doctor.php">Manage Doctors</a>
    <a href="Admin_recept.php">Manage Receptionist</a>
    <a href="Admin_patient.php">Manage Patients</a>
    <a href="Admin_medicine.php">View Medicine</a>
    <a href="Admin_payment.php">View Payments</a>
    <a href="Admin_feedback.php">View Feedback</a>
    <a href="Admin_report.php">Reports</a>
    <button class="logout-btn">logout</button>
</div>

<!-- Main Content -->
<div class="main">
    <!-- Topbar -->
    <div class="topbar">
        <h1>View Payments</h1>
        <p>Welcome, Admin</p>
    </div>

    <!-- Revenue Card -->
    <div class="revenue-card">
        <h3>Total Revenue</h3>
        <p>$<?php 
        // PHP code to calculate total revenue
        include 'config.php';
        $revenue_query = "SELECT SUM(AMOUNT) as total FROM payment_tbl WHERE STATUS = 'COMPLETED'";
        $revenue_result = mysqli_query($conn, $revenue_query);
        $revenue_row = mysqli_fetch_assoc($revenue_result);
        echo number_format($revenue_row['total'], 2);
        mysqli_close($conn);
        ?></p>
    </div>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="view_payments.php">
            <input type="date" name="date_filter" placeholder="Filter by Date">
            <select name="status_filter">
                <option value="">All Status</option>
                <option value="COMPLETED">Completed</option>
                <option value="FAILED">Failed</option>
            </select>
            <select name="mode_filter">
                <option value="">All Payment Modes</option>
                <option value="CREDIT CARD">Credit Card</option>
                <option value="GOOGLE PAY">Google Pay</option>
                <option value="UPI">UPI</option>
                <option value="NET BANKING">Net Banking</option>
            </select>
            <button type="submit">Filter</button>
        </form>
    </div>

    <!-- Payments Table -->
    <table>
        <tr>
            <th>Payment ID</th>
            <th>Appointment ID</th>
            <th>Patient Name</th>
            <th>Amount</th>
            <th>Payment Date</th>
            <th>Payment Mode</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php
        // PHP code to fetch payments from database
        include 'config.php';
        
        // Build query based on filters
        $query = "SELECT p.PAYMENT_ID, p.APPOINTMENT_ID, p.AMOUNT, p.PAYMENT_DATE, 
                  p.PAYMENT_MODE, p.STATUS, 
                  pt.FIRST_NAME as p_first, pt.LAST_NAME as p_last
                  FROM payment_tbl p
                  JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
                  JOIN patient_tbl pt ON a.PATIENT_ID = pt.PATIENT_ID
                  WHERE 1=1";
        
        // Apply filters if set
        if(isset($_GET['date_filter']) && !empty($_GET['date_filter'])) {
            $date = mysqli_real_escape_string($conn, $_GET['date_filter']);
            $query .= " AND p.PAYMENT_DATE = '$date'";
        }
        
        if(isset($_GET['status_filter']) && !empty($_GET['status_filter'])) {
            $status = mysqli_real_escape_string($conn, $_GET['status_filter']);
            $query .= " AND p.STATUS = '$status'";
        }
        
        if(isset($_GET['mode_filter']) && !empty($_GET['mode_filter'])) {
            $mode = mysqli_real_escape_string($conn, $_GET['mode_filter']);
            $query .= " AND p.PAYMENT_MODE = '$mode'";
        }
        
        $query .= " ORDER BY p.PAYMENT_DATE DESC";
        
        $result = mysqli_query($conn, $query);
        
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $status_class = "";
                if($row['STATUS'] == 'COMPLETED') {
                    $status_class = "status-completed";
                } else if($row['STATUS'] == 'FAILED') {
                    $status_class = "status-failed";
                }
                
                echo "<tr>
                    <td>".$row['PAYMENT_ID']."</td>
                    <td>".$row['APPOINTMENT_ID']."</td>
                    <td>".$row['p_first']." ".$row['p_last']."</td>
                    <td>$".number_format($row['AMOUNT'], 2)."</td>
                    <td>".$row['PAYMENT_DATE']."</td>
                    <td><span class='payment-mode-badge'>".$row['PAYMENT_MODE']."</span></td>
                    <td><span class='status-badge $status_class'>".$row['STATUS']."</span></td>
                    <td>
                        <button class='action-btn view-btn' onclick='viewPayment(".$row['PAYMENT_ID'].")'>View</button>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No payments found</td></tr>";
        }
        
        mysqli_close($conn);
        ?>
    </table>
</div>

<script>
function viewPayment(id) {
    window.location.href = "view_payment_details.php?id=" + id;
}
</script>

</body>
</html>