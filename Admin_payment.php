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
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Payments - QuickCare</title>
<?php include 'admin_sidebar.php'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
        background: #072D44;
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
        background: var(--dark-blue);
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
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

    .payment-groups {
        margin-top: 10px;
    }

    .doctor-group {
        margin-bottom: 28px;
        background: #f8fafc;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        overflow: hidden;
    }

    .doctor-header-bar {
        background: var(--dark-blue);
        color: #ffffff;
        padding: 14px 18px;
        border-radius: 10px 10px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .doctor-header-title {
        font-weight: 600;
        font-size: 1rem;
    }

    .doctor-header-subtitle {
        font-size: 0.9rem;
        opacity: 0.95;
    }

    .payment-list {
        margin-top: 0;
        padding: 10px 16px 12px;
    }

    .payment-card {
        padding: 10px 0;
        border-bottom: 1px solid #e1e7ef;
    }

    .payment-card:last-child {
        border-bottom: none;
    }

    .payment-meta {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 8px;
        font-size: 0.9rem;
        color: #555;
        margin-bottom: 4px;
    }

    .payment-meta .payment-patient {
        font-weight: 600;
        font-size: 1.05rem;
    }

    .payment-meta .payment-date {
        font-style: italic;
    }

    .payment-amount {
        font-weight: 600;
    }
</style>
</head>

<body>

<div class="main">

    <?php include 'admin_header.php'; ?>

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
                <option value="CARD" <?php if(($_POST['mode_filter'] ?? '')=='CREDIT CARD') echo 'selected'; ?>>Credit Card</option>
                <!-- <option value="GOOGLE PAY" <?php if(($_POST['mode_filter'] ?? '')=='GOOGLE PAY') echo 'selected'; ?>>Google Pay</option> -->
                <option value="UPI" <?php if(($_POST['mode_filter'] ?? '')=='UPI') echo 'selected'; ?>>UPI</option>
                <option value="NET BANKING" <?php if(($_POST['mode_filter'] ?? '')=='NET BANKING') echo 'selected'; ?>>Net Banking</option>
            </select>

            <button type="submit">
                <i class="bi bi-funnel"></i>
                Filter
            </button>
        </form>
    </div>

    <!-- PAYMENTS GROUPED BY DOCTOR -->
    <div class="payment-groups">
        <?php
        include 'config.php';

        $query = "SELECT
                    p.PAYMENT_ID,
                    p.AMOUNT,
                    p.PAYMENT_DATE,
                    p.PAYMENT_MODE,
                    p.STATUS,
                    pt.FIRST_NAME AS p_first,
                    pt.LAST_NAME AS p_last,
                    d.FIRST_NAME AS d_first,
                    d.LAST_NAME AS d_last,
                    s.SPECIALISATION_NAME
                  FROM payment_tbl p
                  JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
                  JOIN patient_tbl pt ON a.PATIENT_ID = pt.PATIENT_ID
                  JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
                  JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
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

        $query .= " ORDER BY d.LAST_NAME, d.FIRST_NAME, p.PAYMENT_DATE DESC, p.PAYMENT_ID DESC";

        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $currentDoctorKey = '';

            while ($row = mysqli_fetch_assoc($result)) {
                $doctorName = 'Dr. ' . $row['d_first'] . ' ' . $row['d_last'];
                $specialisation = $row['SPECIALISATION_NAME'];
                $doctorKey = $doctorName . '|' . $specialisation;

                if ($doctorKey !== $currentDoctorKey) {
                    if ($currentDoctorKey !== '') {
                        // Close previous group
                        echo '</div></div>';
                    }

                    $currentDoctorKey = $doctorKey;

                    echo '<div class="doctor-group">';
                    echo '  <div class="doctor-header-bar">';
                    echo '      <div class="doctor-header-title">' . htmlspecialchars($doctorName) . '</div>';
                    echo '      <div class="doctor-header-subtitle">' . htmlspecialchars($specialisation) . '</div>';
                    echo '  </div>';
                    echo '  <div class="payment-list">';
                }

                $statusClass = $row['STATUS'] == 'COMPLETED'
                    ? 'status-completed'
                    : 'status-failed';

                $patientName = $row['p_first'] . ' ' . $row['p_last'];
                $dateText = !empty($row['PAYMENT_DATE'])
                    ? date('F d, Y', strtotime($row['PAYMENT_DATE']))
                    : '';
                $amountText = '₹' . number_format($row['AMOUNT'], 2);

                echo '      <div class="payment-card">';
                echo '          <div class="payment-meta">';
                echo '              <span class="payment-patient">' . htmlspecialchars($patientName) . '</span>';
                if ($dateText !== '') {
                    echo '          <span class="payment-date">' . htmlspecialchars($dateText) . '</span>';
                }
                echo '              <span class="payment-amount">' . htmlspecialchars($amountText) . '</span>';
                echo '          </div>';
                echo '          <div>';
                echo '              <span class="payment-mode-badge">' . htmlspecialchars($row['PAYMENT_MODE']) . '</span> ';
                echo '              <span class="status-badge ' . $statusClass . '">' . htmlspecialchars($row['STATUS']) . '</span>';
                echo '          </div>';
                echo '      </div>';
            }

            if ($currentDoctorKey !== '') {
                // Close last group
                echo '  </div></div>';
            }
        } else {
            echo "<p>No payments found</p>";
        }

        mysqli_close($conn);
        ?>
    </div>
</div>

</body>
</html>
