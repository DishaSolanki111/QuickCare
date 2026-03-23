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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    :root {
        --dark-blue: #072D44;
        --mid-blue: #064469;
        --soft-blue: #5790AB;
        --light-blue: #9CCDD8;
        --gray-blue: #D0D7E1;
        --white: #ffffff;
        --bg-gray: #f4f7f6;
    }

    body {
        margin: 0;
        font-family: 'Inter', 'Open Sans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: var(--bg-gray);
        display: flex;
    }

    .main {
        margin-left: 250px;
        padding: 15px;
        width: calc(100% - 250px);
    }

    .revenue-card {
        background: white;
        padding: 15px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        border-left: 5px solid #2ecc71;
        display: inline-block;
    }

    .revenue-card h3 { margin: 0 0 5px 0; font-size: 1rem; color: var(--mid-blue); }
    .revenue-card p { margin: 0; font-size: 1.5rem; font-weight: bold; color: #2ecc71; }

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
        transition: 0.3s;
    }

    .filter-container button:hover { background: var(--mid-blue); }

    .doctor-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 15px;
    }
    @media (min-width: 1200px) {
        .doctor-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    .doctor-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }

    .doctor-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    }

    .doctor-header {
        background: var(--dark-blue);
        color: white;
        padding: 12px 15px;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .doctor-header i.header-icon { font-size: 20px; color: var(--light-blue); }
    .doctor-header h3 { margin: 0; font-size: 1.1rem; }
    
    .doctor-specialization {
        font-size: 0.85rem;
        color: var(--light-blue);
        margin-top: 2px;
    }

    .appointment-list {
        padding: 10px;
        max-height: 350px;
        overflow-y: auto;
    }

    .patient-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 10px;
        border-bottom: 1px solid #eee;
        transition: 0.2s;
    }

    .patient-row:last-child { border-bottom: none; }
    .patient-row:hover { background: #f9f9f9; }

    .patient-info { display: flex; flex-direction: column; gap: 4px; width: 100%; }
    .patient-name { font-weight: bold; color: var(--mid-blue); font-size: 0.95rem; width: 100%; display: flex; justify-content: space-between; align-items: center; }
    .payment-amount { color: #2ecc71; font-weight: 700; font-size: 1rem; }
    .apt-time { font-size: 0.8rem; color: #666; display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-top: 2px; }

    .status-badge {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: bold;
    }
    .status-completed { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
    .status-failed { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }

    .payment-mode-badge {
        padding: 3px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: bold;
        background-color: #e3f2fd;
        color: #1976d2;
        border: 1px solid #bbdefb;
    }
</style>
</head>

<body>

<?php include 'admin_sidebar.php'; ?>
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
        ?></p>
    </div>

    <!-- FILTER -->
    <div class="filter-container">
        <form method="POST" action="">
            <input type="date" name="date_filter"
                value="<?php echo htmlspecialchars($_POST['date_filter'] ?? ''); ?>">

            <select name="status_filter">
                <option value="">All Status</option>
                <option value="COMPLETED" <?php if(($_POST['status_filter'] ?? '')=='COMPLETED') echo 'selected'; ?>>Completed</option>
                <option value="FAILED" <?php if(($_POST['status_filter'] ?? '')=='FAILED') echo 'selected'; ?>>Failed</option>
            </select>

            <select name="mode_filter">
                <option value="">All Payment Modes</option>
                <option value="CARD" <?php if(($_POST['mode_filter'] ?? '')=='CARD') echo 'selected'; ?>>Credit Card</option>
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
    <div class="doctor-grid">
        <?php
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

                    echo '<div class="doctor-card">';
                    echo '  <div class="doctor-header">';
                    echo '      <i class="fa-solid fa-user-doctor header-icon"></i>';
                    echo '      <div>';
                    echo '          <h3>' . htmlspecialchars($doctorName) . '</h3>';
                    echo '          <div class="doctor-specialization">';
                    echo '              <i class="bi bi-award"></i> ' . htmlspecialchars($specialisation);
                    echo '          </div>';
                    echo '      </div>';
                    echo '  </div>';
                    echo '  <div class="appointment-list">';
                }

                $statusClass = $row['STATUS'] == 'COMPLETED'
                    ? 'status-completed'
                    : 'status-failed';

                $patientName = $row['p_first'] . ' ' . $row['p_last'];
                $dateText = !empty($row['PAYMENT_DATE'])
                    ? date('F d, Y', strtotime($row['PAYMENT_DATE']))
                    : '';
                $amountText = '₹' . number_format($row['AMOUNT'], 2);

                echo '      <div class="patient-row">';
                echo '          <div class="patient-info">';
                echo '              <span class="patient-name">' . htmlspecialchars($patientName) . ' <span class="payment-amount">' . htmlspecialchars($amountText) . '</span></span>';
                if ($dateText !== '') {
                    echo '          <span class="apt-time">';
                    echo '              <i class="fa-regular fa-calendar-days" style="color: var(--soft-blue);"></i> ' . htmlspecialchars($dateText);
                    echo '              <span style="color:#ddd; margin:0 4px;">|</span>';
                    echo '              <span class="payment-mode-badge">' . htmlspecialchars($row['PAYMENT_MODE']) . '</span>';
                    echo '              <span class="status-badge ' . $statusClass . '">' . htmlspecialchars($row['STATUS']) . '</span>';
                    echo '          </span>';
                }
                echo '          </div>';
                echo '      </div>';
            }

            if ($currentDoctorKey !== '') {
                // Close last group
                echo '  </div></div>';
            }
        } else {
            echo "<div style='grid-column: 1 / -1; background:white; padding: 40px; text-align:center; border-radius: 10px;'><h3 style='color: #888;'>No payments found matching the selected filters.</h3></div>";
        }

        mysqli_close($conn);
        ?>
    </div>
</div>

</body>
</html>
