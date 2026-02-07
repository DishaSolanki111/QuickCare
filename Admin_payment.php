<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Payments - QuickCare</title>
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
        padding: 10px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        border-left: 8px solid #2ecc71;
    }
</style>
</head>

<body>

<div class="main">

    <div class="topbar">
        <h1>View Payments</h1>
        <p>Welcome, Admin</p>
    </div>

    <!-- TOTAL REVENUE -->
    <div class="revenue-card">
        <h3>Total Revenue</h3>
        <p>₹<?php
            include 'config.php';
            $rev = mysqli_query($conn,
                "SELECT SUM(AMOUNT) AS total FROM payment_tbl WHERE STATUS='COMPLETED'");
            $row = mysqli_fetch_assoc($rev);
            echo number_format($row['total'], 2);
            mysqli_close($conn);
        ?></p>
    </div>

    <!-- FILTER (FIXED) -->
    <div class="filter-container">
        <form method="POST" action="">
            <input type="date" name="date_filter"
                value="<?php echo $_POST['date_filter'] ?? ''; ?>">

            <select name="status_filter">
                <option value="">All Status</option>
                <option value="COMPLETED" <?php if(($_POST['status_filter'] ?? '')=='COMPLETED') echo 'selected'; ?>>Completed</option>
                <option value="FAILED" <?php if(($_POST['status_filter'] ?? '')=='FAILED') echo 'selected'; ?>>Failed</option>
            </select>

            <select name="mode_filter">
                <option value="">All Payment Modes</option>
                <option value="CREDIT CARD" <?php if(($_POST['mode_filter'] ?? '')=='CREDIT CARD') echo 'selected'; ?>>Credit Card</option>
                <option value="GOOGLE PAY" <?php if(($_POST['mode_filter'] ?? '')=='GOOGLE PAY') echo 'selected'; ?>>Google Pay</option>
                <option value="UPI" <?php if(($_POST['mode_filter'] ?? '')=='UPI') echo 'selected'; ?>>UPI</option>
                <option value="NET BANKING" <?php if(($_POST['mode_filter'] ?? '')=='NET BANKING') echo 'selected'; ?>>Net Banking</option>
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

    <!-- TABLE -->
    <table>
        <tr>
            <th>Payment ID</th>
            <th>Appointment ID</th>
            <th>Patient Name</th>
            <th>Amount</th>
            <th>Payment Date</th>
            <th>Payment Mode</th>
            <th>Status</th>
        </tr>

        <?php
        include 'config.php';

        $query = "SELECT p.*, pt.FIRST_NAME, pt.LAST_NAME
                  FROM payment_tbl p
                  JOIN appointment_tbl a ON p.APPOINTMENT_ID=a.APPOINTMENT_ID
                  JOIN patient_tbl pt ON a.PATIENT_ID=pt.PATIENT_ID
                  WHERE 1=1";

        if (!empty($_POST['date_filter'])) {
            $date = mysqli_real_escape_string($conn, $_POST['date_filter']);
            $query .= " AND DATE(p.PAYMENT_DATE) = '$date'";
        }

        if (!empty($_POST['status_filter'])) {
            $status = mysqli_real_escape_string($conn, $_POST['status_filter']);
            $query .= " AND p.STATUS = '$status'";
        }

        if (!empty($_POST['mode_filter'])) {
            $mode = mysqli_real_escape_string($conn, $_POST['mode_filter']);
            $query .= " AND p.PAYMENT_MODE = '$mode'";
        }

        $query .= " ORDER BY p.PAYMENT_DATE DESC";

        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {

                $statusClass = $row['STATUS']=='COMPLETED'
                    ? 'status-completed' : 'status-failed';

                echo "<tr>
                    <td>{$row['PAYMENT_ID']}</td>
                    <td>{$row['APPOINTMENT_ID']}</td>
                    <td>{$row['FIRST_NAME']} {$row['LAST_NAME']}</td>
                    <td>₹".number_format($row['AMOUNT'],2)."</td>
                    <td>{$row['PAYMENT_DATE']}</td>
                    <td><span class='payment-mode-badge'>{$row['PAYMENT_MODE']}</span></td>
                    <td><span class='status-badge {$statusClass}'>{$row['STATUS']}</span></td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No payments found</td></tr>";
        }

        mysqli_close($conn);
        ?>
    </table>
</div>

</body>
</html>
