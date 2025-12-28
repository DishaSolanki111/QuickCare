<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Appointments - QuickCare</title>
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
    .status-scheduled {
        background-color: #3498db;
        color: white;
    }
    .status-cancelled {
        background-color: #e74c3c;
        color: white;
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
    .edit-btn {
        background-color: #f39c12;
        color: white;
    }
    .delete-btn {
        background-color: #e74c3c;
        color: white;
    }
    .add-btn {
        background: #2ecc71;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        margin-bottom: 20px;
        text-decoration: none;
        display: inline-block;
    }
    .add-btn:hover {
        background: #27ae60;
    }
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
  <img src="uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">  
        <h2>QuickCare</h2>

    <a href="admin.php">Dashboard</a>
    <a href="Admin_appoitment.php" class="active">View Appointments</a>
    <a href="Admin_doctors.php">Manage Doctors</a>
    <a href="Admin_recept.php">Manage Receptionist</a>
    <a href="Admin_patient.php">Manage Patients</a>
    <a href="Admin_medicine.php">View Medicine</a>
    <a href="Admin_payments.php">View Payments</a>
    <a href="Admin_feedback.php">View Feedback</a>
    <a href="Admin_reports.php">Reports</a>
    <button class="logout-btn">logout</button>
</div>

<!-- Main Content -->
<div class="main">
    <!-- Topbar -->
    <div class="topbar">
        <h1>View Appointments</h1>
        <p>Welcome, Admin</p>
    </div>

    <!-- Add New Appointment Button -->
    <a href="add_appointment.php" class="add-btn">+ Add New Appointment</a>

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="view_appointments.php">
            <input type="date" name="date_filter" placeholder="Filter by Date">
            <select name="status_filter">
                <option value="">All Status</option>
                <option value="SCHEDULED">Scheduled</option>
                <option value="COMPLETED">Completed</option>
                <option value="CANCELLED">Cancelled</option>
            </select>
            <select name="doctor_filter">
                <option value="">All Doctors</option>
                <?php
                // PHP code to populate doctors from database
                include 'db_connection.php';
                $doctor_query = "SELECT DOCTOR_ID, FIRST_NAME, LAST_NAME FROM doctor_tbl ORDER BY FIRST_NAME";
                $doctor_result = mysqli_query($conn, $doctor_query);
                while($doctor_row = mysqli_fetch_assoc($doctor_result)) {
                    echo "<option value='".$doctor_row['DOCTOR_ID']."'>".$doctor_row['FIRST_NAME']." ".$doctor_row['LAST_NAME']."</option>";
                }
                ?>
            </select>
            <button type="submit">Filter</button>
        </form>
    </div>

    <!-- Appointments Table -->
    <table>
        <tr>
            <th>Appointment ID</th>
            <th>Patient Name</th>
            <th>Doctor Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php
        // PHP code to fetch appointments from database
        include 'db_connection.php';
        
        // Build query based on filters
        $query = "SELECT a.APPOINTMENT_ID, p.FIRST_NAME as p_first, p.LAST_NAME as p_last, 
                  d.FIRST_NAME as d_first, d.LAST_NAME as d_last, 
                  a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, a.STATUS 
                  FROM appointment_tbl a
                  JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
                  JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
                  WHERE 1=1";
        
        // Apply filters if set
        if(isset($_GET['date_filter']) && !empty($_GET['date_filter'])) {
            $date = mysqli_real_escape_string($conn, $_GET['date_filter']);
            $query .= " AND a.APPOINTMENT_DATE = '$date'";
        }
        
        if(isset($_GET['status_filter']) && !empty($_GET['status_filter'])) {
            $status = mysqli_real_escape_string($conn, $_GET['status_filter']);
            $query .= " AND a.STATUS = '$status'";
        }
        
        if(isset($_GET['doctor_filter']) && !empty($_GET['doctor_filter'])) {
            $doctor_id = mysqli_real_escape_string($conn, $_GET['doctor_filter']);
            $query .= " AND a.DOCTOR_ID = $doctor_id";
        }
        
        $query .= " ORDER BY a.APPOINTMENT_DATE DESC, a.APPOINTMENT_TIME DESC";
        
        $result = mysqli_query($conn, $query);
        
        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {
                $status_class = "";
                if($row['STATUS'] == 'COMPLETED') {
                    $status_class = "status-completed";
                } else if($row['STATUS'] == 'SCHEDULED') {
                    $status_class = "status-scheduled";
                } else if($row['STATUS'] == 'CANCELLED') {
                    $status_class = "status-cancelled";
                }
                
                echo "<tr>
                    <td>".$row['APPOINTMENT_ID']."</td>
                    <td>".$row['p_first']." ".$row['p_last']."</td>
                    <td>".$row['d_first']." ".$row['d_last']."</td>
                    <td>".$row['APPOINTMENT_DATE']."</td>
                    <td>".$row['APPOINTMENT_TIME']."</td>
                    <td><span class='status-badge $status_class'>".$row['STATUS']."</span></td>
                    <td>
                        <button class='action-btn view-btn' onclick='viewAppointment(".$row['APPOINTMENT_ID'].")'>View</button>
                        <button class='action-btn edit-btn' onclick='editAppointment(".$row['APPOINTMENT_ID'].")'>Edit</button>
                        <button class='action-btn delete-btn' onclick='deleteAppointment(".$row['APPOINTMENT_ID'].")'>Delete</button>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No appointments found</td></tr>";
        }
        
        mysqli_close($conn);
        ?>
    </table>
</div>

<script>
function viewAppointment(id) {
    window.location.href = "view_appointment_details.php?id=" + id;
}

function editAppointment(id) {
    window.location.href = "edit_appointment.php?id=" + id;
}

function deleteAppointment(id) {
    if(confirm("Are you sure you want to delete this appointment?")) {
        window.location.href = "delete_appointment.php?id=" + id;
    }
}
</script>

</body>
</html>