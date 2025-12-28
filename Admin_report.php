<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports - QuickCare</title>
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

    /* Cards */
    .cards {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        flex: 1;
        min-width: 250px;
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
    
    .logo-img {
            height: 40px;
            margin-right: 12px;
            border-radius: 5px;
        }
    
    .report-container {
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    
    .report-container h2 {
        color: #064469;
        margin-top: 0;
    }
    
    .report-container form {
        display: flex;
        gap: 15px;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    
    .report-container input, .report-container select {
        padding: 10px;
        border: 1px solid #D0D7E1;
        border-radius: 5px;
    }
    
    .report-container button {
        padding: 10px 15px;
        background: #5790AB;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    
    .report-container button:hover {
        background: #064469;
    }
    
    .report-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    
    .report-table th, .report-table td {
        padding: 10px;
        border-bottom: 1px solid #D0D7E1;
        text-align: left;
    }
    
    .report-table th {
        background: #5790AB;
        color: white;
    }
    
    .report-table tr:hover {
        background: #F2F9FB;
    }
    
    .chart-container {
        height: 300px;
        margin-top: 20px;
    }
    
    .export-btn {
        background: #2ecc71;
        color: white;
        padding: 8px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-left: 10px;
    }
    
    .export-btn:hover {
        background: #27ae60;
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
        <h1>Reports</h1>
        <p>Welcome, Admin</p>
    </div>

    <!-- Statistics Cards -->
    <div class="cards">
        <div class="card">
            <h3>Total Patients</h3>
            <p><?php 
            // PHP code to count total patients
            include 'config.php';
            $patients_query = "SELECT COUNT(*) as count FROM patient_tbl";
            $patients_result = mysqli_query($conn, $patients_query);
            $patients_row = mysqli_fetch_assoc($patients_result);
            echo $patients_row['count'];
            ?></p>
        </div>

        <div class="card">
            <h3>Total Appointments</h3>
            <p><?php 
            // PHP code to count total appointments
            $appointments_query = "SELECT COUNT(*) as count FROM appointment_tbl";
            $appointments_result = mysqli_query($conn, $appointments_query);
            $appointments_row = mysqli_fetch_assoc($appointments_result);
            echo $appointments_row['count'];
            ?></p>
        </div>

        <div class="card">
            <h3>Total Doctors</h3>
            <p><?php 
            // PHP code to count total doctors
            $doctors_query = "SELECT COUNT(*) as count FROM doctor_tbl";
            $doctors_result = mysqli_query($conn, $doctors_query);
            $doctors_row = mysqli_fetch_assoc($doctors_result);
            echo $doctors_row['count'];
            ?></p>
        </div>
        <div class="card">
            <h3>Total Revenue</h3>
            <p>$<?php 
            // PHP code to calculate total revenue
            $revenue_query = "SELECT SUM(AMOUNT) as total FROM payment_tbl WHERE STATUS = 'COMPLETED'";
            $revenue_result = mysqli_query($conn, $revenue_query);
            $revenue_row = mysqli_fetch_assoc($revenue_result);
            echo number_format($revenue_row['total'], 2);
            ?></p>
        </div>
    </div>

    <!-- Appointments Report -->
    <div class="report-container">
        <h2>Appointments Report</h2>
        <form method="GET" action="reports.php">
            <input type="date" name="start_date" placeholder="Start Date" value="<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); ?>">
            <input type="date" name="end_date" placeholder="End Date" value="<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'); ?>">
            <select name="status_filter">
                <option value="">All Status</option>
                <option value="SCHEDULED" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'SCHEDULED') ? 'selected' : ''; ?>>Scheduled</option>
                <option value="COMPLETED" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'COMPLETED') ? 'selected' : ''; ?>>Completed</option>
                <option value="CANCELLED" <?php echo (isset($_GET['status_filter']) && $_GET['status_filter'] == 'CANCELLED') ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            <button type="submit">Generate Report</button>
            <button type="button" class="export-btn" onclick="exportAppointmentReport()">Export</button>
        </form>
        
        <table class="report-table">
            <tr>
                <th>Date</th>
                <th>Total Appointments</th>
                <th>Completed</th>
                <th>Cancelled</th>
                <th>Scheduled</th>
            </tr>
            <?php
            // PHP code to generate appointments report
            $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
            $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
            $status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';
            
            // Build query based on filters
            $query = "SELECT APPOINTMENT_DATE, STATUS, COUNT(*) as count 
                      FROM appointment_tbl 
                      WHERE APPOINTMENT_DATE BETWEEN '$start_date' AND '$end_date'";
            
            if(!empty($status_filter)) {
                $query .= " AND STATUS = '$status_filter'";
            }
            
            $query .= " GROUP BY APPOINTMENT_DATE, STATUS 
                        ORDER BY APPOINTMENT_DATE DESC";
            
            $result = mysqli_query($conn, $query);
            
            // Store results in an array
            $appointments_data = array();
            while($row = mysqli_fetch_assoc($result)) {
                $date = $row['APPOINTMENT_DATE'];
                $status = $row['STATUS'];
                $count = $row['count'];
                
                if(!isset($appointments_data[$date])) {
                    $appointments_data[$date] = array(
                        'total' => 0,
                        'completed' => 0,
                        'cancelled' => 0,
                        'scheduled' => 0
                    );
                }
                
                $appointments_data[$date]['total'] += $count;
                
                if($status == 'COMPLETED') {
                    $appointments_data[$date]['completed'] = $count;
                } else if($status == 'CANCELLED') {
                    $appointments_data[$date]['cancelled'] = $count;
                } else if($status == 'SCHEDULED') {
                    $appointments_data[$date]['scheduled'] = $count;
                }
            }
            
            // Display the data
            foreach($appointments_data as $date => $data) {
                echo "<tr>
                    <td>$date</td>
                    <td>".$data['total']."</td>
                    <td>".$data['completed']."</td>
                    <td>".$data['cancelled']."</td>
                    <td>".$data['scheduled']."</td>
                </tr>";
            }
            
            // If no data found
            if(empty($appointments_data)) {
                echo "<tr><td colspan='5'>No appointments found in the selected date range</td></tr>";
            }
            ?>
        </table>
    </div>

    <!-- Revenue Report -->
    <div class="report-container">
        <h2>Revenue Report</h2>
        <form method="GET" action="reports.php">
            <input type="date" name="revenue_start_date" placeholder="Start Date" value="<?php echo isset($_GET['revenue_start_date']) ? $_GET['revenue_start_date'] : date('Y-m-01'); ?>">
            <input type="date" name="revenue_end_date" placeholder="End Date" value="<?php echo isset($_GET['revenue_end_date']) ? $_GET['revenue_end_date'] : date('Y-m-d'); ?>">
            <button type="submit">Generate Report</button>
            <button type="button" class="export-btn" onclick="exportRevenueReport()">Export</button>
        </form>
        
        <table class="report-table">
            <tr>
                <th>Payment Mode</th>
                <th>Count</th>
                <th>Total Amount</th>
            </tr>
            <?php
            // PHP code to generate revenue report
            $revenue_start_date = isset($_GET['revenue_start_date']) ? $_GET['revenue_start_date'] : date('Y-m-01');
            $revenue_end_date = isset($_GET['revenue_end_date']) ? $_GET['revenue_end_date'] : date('Y-m-d');
            
            $query = "SELECT PAYMENT_MODE, COUNT(*) as count, SUM(AMOUNT) as total 
                      FROM payment_tbl 
                      WHERE PAYMENT_DATE BETWEEN '$revenue_start_date' AND '$revenue_end_date' AND STATUS = 'COMPLETED'
                      GROUP BY PAYMENT_MODE";
            
            $result = mysqli_query($conn, $query);
            
            $total_count = 0;
            $total_amount = 0;
            
            while($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>".$row['PAYMENT_MODE']."</td>
                    <td>".$row['count']."</td>
                    <td>$".number_format($row['total'], 2)."</td>
                </tr>";
                
                $total_count += $row['count'];
                $total_amount += $row['total'];
            }
            
            echo "<tr style='font-weight:bold; background:#F2F9FB;'>
                <td>Total</td>
                <td>$total_count</td>
                <td>$".number_format($total_amount, 2)."</td>
            </tr>";
            ?>
        </table>
    </div>

    <!-- Doctor Performance Report -->
    <div class="report-container">
        <h2>Doctor Performance Report</h2>
        <form method="GET" action="reports.php">
            <input type="date" name="doctor_start_date" placeholder="Start Date" value="<?php echo isset($_GET['doctor_start_date']) ? $_GET['doctor_start_date'] : date('Y-m-01'); ?>">
            <input type="date" name="doctor_end_date" placeholder="End Date" value="<?php echo isset($_GET['doctor_end_date']) ? $_GET['doctor_end_date'] : date('Y-m-d'); ?>">
            <button type="submit">Generate Report</button>
            <button type="button" class="export-btn" onclick="exportDoctorReport()">Export</button>
        </form>
        
        <table class="report-table">
            <tr>
                <th>Doctor Name</th>
                <th>Specialization</th>
                <th>Total Appointments</th>
                <th>Completed</th>
                <th>Cancelled</th>
                <th>Completion Rate</th>
            </tr>
            <?php
            // PHP code to generate doctor performance report
            $doctor_start_date = isset($_GET['doctor_start_date']) ? $_GET['doctor_start_date'] : date('Y-m-01');
            $doctor_end_date = isset($_GET['doctor_end_date']) ? $_GET['doctor_end_date'] : date('Y-m-d');
            
            $query = "SELECT d.DOCTOR_ID, d.FIRST_NAME, d.LAST_NAME, s.SPECIALISATION_NAME,
                      COUNT(a.APPOINTMENT_ID) as total_appointments,
                      SUM(CASE WHEN a.STATUS = 'COMPLETED' THEN 1 ELSE 0 END) as completed,
                      SUM(CASE WHEN a.STATUS = 'CANCELLED' THEN 1 ELSE 0 END) as cancelled
                      FROM doctor_tbl d
                      JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
                      LEFT JOIN appointment_tbl a ON d.DOCTOR_ID = a.DOCTOR_ID 
                        AND a.APPOINTMENT_DATE BETWEEN '$doctor_start_date' AND '$doctor_end_date'
                      GROUP BY d.DOCTOR_ID
                      ORDER BY total_appointments DESC";
            
            $result = mysqli_query($conn, $query);
            
            while($row = mysqli_fetch_assoc($result)) {
                $total = $row['total_appointments'];
                $completed = $row['completed'];
                $cancelled = $row['cancelled'];
                
                $completion_rate = $total > 0 ? round(($completed / $total) * 100, 2) : 0;
                
                echo "<tr>
                    <td>".$row['FIRST_NAME']." ".$row['LAST_NAME']."</td>
                    <td>".$row['SPECIALISATION_NAME']."</td>
                    <td>".$total."</td>
                    <td>".$completed."</td>
                    <td>".$cancelled."</td>
                    <td>".$completion_rate."%</td>
                </tr>";
            }
            ?>
        </table>
    </div>
</div>

<script>
function exportAppointmentReport() {
    window.location.href = "export_appointments_report.php?" + 
        "start_date=<?php echo isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); ?>" + 
        "&end_date=<?php echo isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d'); ?>" + 
        "&status_filter=<?php echo isset($_GET['status_filter']) ? $_GET['status_filter'] : ''; ?>";
}

function exportRevenueReport() {
    window.location.href = "export_revenue_report.php?" + 
        "start_date=<?php echo isset($_GET['revenue_start_date']) ? $_GET['revenue_start_date'] : date('Y-m-01'); ?>" + 
        "&end_date=<?php echo isset($_GET['revenue_end_date']) ? $_GET['revenue_end_date'] : date('Y-m-d'); ?>";
}

function exportDoctorReport() {
    window.location.href = "export_doctor_report.php?" + 
        "start_date=<?php echo isset($_GET['doctor_start_date']) ? $_GET['doctor_start_date'] : date('Y-m-01'); ?>" + 
        "&end_date=<?php echo isset($_GET['doctor_end_date']) ? $_GET['doctor_end_date'] : date('Y-m-d'); ?>";
}
</script>

</body>
</html>