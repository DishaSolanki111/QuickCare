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
    }

    .main {
        margin-left: 250px;
        padding: 20px;
        width: calc(100% - 250px);
    }

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

    .filter-container input,
    .filter-container select {
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
        color: white;
    }

    .status-completed { background: #2ecc71; }
    .status-scheduled { background: #3498db; }
    .status-cancelled { background: #e74c3c; }

  

</style>
</head>

<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">

    <div class="topbar">
        <h1>View Appointments</h1>
        <p>Welcome, Admin</p>
    </div>

    <div class="filter-container">
        <form method="POST" action="">
            <input type="date" name="date_filter">

            <select name="status_filter">
                <option value="">All Status</option>
                <option value="SCHEDULED">Scheduled</option>
                <option value="COMPLETED">Completed</option>
                <option value="CANCELLED">Cancelled</option>
            </select>

            <select name="doctor_filter">
                <option value="">All Doctors</option>
                <?php
                include 'config.php';
                $doctor_query = "SELECT DOCTOR_ID, FIRST_NAME, LAST_NAME FROM doctor_tbl";
                $doctor_result = mysqli_query($conn, $doctor_query);
                while($row = mysqli_fetch_assoc($doctor_result)) {
                    echo "<option value='".$row['DOCTOR_ID']."'>".$row['FIRST_NAME']." ".$row['LAST_NAME']."</option>";
                }
                ?>
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

    <table>
        <tr>
            <th>Appointment ID</th>
            <th>Patient Name</th>
            <th>Doctor Name</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
         
        </tr>

        <?php
        $query = "SELECT a.APPOINTMENT_ID,
                         p.FIRST_NAME p_first, p.LAST_NAME p_last,
                         d.FIRST_NAME d_first, d.LAST_NAME d_last,
                         a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, a.STATUS
                  FROM appointment_tbl a
                  JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
                  JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
                  WHERE 1=1";

        if(!empty($_POST['date_filter'])) {
            $date = mysqli_real_escape_string($conn, $_POST['date_filter']);
            $query .= " AND a.APPOINTMENT_DATE='$date'";
        }

        if(!empty($_POST['status_filter'])) {
            $status = mysqli_real_escape_string($conn, $_POST['status_filter']);
            $query .= " AND a.STATUS='$status'";
        }

        if(!empty($_POST['doctor_filter'])) {
            $doc = mysqli_real_escape_string($conn, $_POST['doctor_filter']);
            $query .= " AND a.DOCTOR_ID='$doc'";
        }

        $result = mysqli_query($conn, $query);

        if(mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) {

                $cls = ($row['STATUS']=="COMPLETED") ? "status-completed" :
                       (($row['STATUS']=="SCHEDULED") ? "status-scheduled" : "status-cancelled");

                echo "<tr>
                    <td>{$row['APPOINTMENT_ID']}</td>
                    <td>{$row['p_first']} {$row['p_last']}</td>
                    <td>{$row['d_first']} {$row['d_last']}</td>
                    <td>{$row['APPOINTMENT_DATE']}</td>
                    <td>{$row['APPOINTMENT_TIME']}</td>
                    <td><span class='status-badge $cls'>{$row['STATUS']}</span></td>
                  
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No appointments found</td></tr>";
        }

        mysqli_close($conn);
        ?>
    </table>

</div>


</body>
</html>
