<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['LOGGED_IN']) || $_SESSION['LOGGED_IN'] !== true || $_SESSION['USER_TYPE'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

$adminName = $_SESSION['USER_NAME'] ?? 'Admin';
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Appointments - QuickCare</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --dark-blue: #072D44;
            --mid-blue: #064469;
            --soft-blue: #5790AB;
            --light-blue: #9CCDD8;
            --white: #ffffff;
            --bg-gray: #f4f7f6;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-gray);
            display: flex;
        }

        .main {
            margin-left: 250px;
            padding: 30px;
            width: calc(100% - 250px);
        }

        /* Filter Section */
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .filter-section form {
            display: flex;
            gap: 15px;
            width: 100%;
            flex-wrap: wrap;
        }

        .filter-input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .filter-input.flex-1 { flex: 1; min-width: 120px; }

        .btn-filter {
            background: var(--mid-blue);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-filter:hover { background: var(--soft-blue); }

        /* Doctor Card Layout */
        .doctor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
            gap: 25px;
        }

        .doctor-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .doctor-header {
            background: var(--dark-blue);
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .doctor-header i { font-size: 24px; color: var(--light-blue); }
        .doctor-header h3 { margin: 0; font-size: 1.2rem; }

        .appointment-list {
            padding: 15px;
            max-height: 400px;
            overflow-y: auto;
        }

        .patient-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px;
            border-bottom: 1px solid #eee;
            transition: 0.2s;
        }

        .patient-row:last-child { border-bottom: none; }
        .patient-row:hover { background: #f9f9f9; }

        .patient-info { display: flex; flex-direction: column; }
        .patient-name { font-weight: bold; color: var(--mid-blue); }
        .apt-time { font-size: 0.85rem; color: #666; }

        .status-pill {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-scheduled { background: #e3f2fd; color: #1976d2; }
        .status-completed { background: #e8f5e9; color: #2e7d32; }
        .status-cancelled { background: #ffebee; color: #c62828; }

        .empty-state {
            text-align: center;
            padding: 50px;
            color: #888;
            grid-column: 1 / -1;
        }
    </style>
</head>
<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">
    <?php include 'admin_header.php'; ?>

    <div class="filter-section">
        <form method="POST">
            <input type="date" name="date_filter" class="filter-input" value="<?php echo htmlspecialchars($_POST['date_filter'] ?? ''); ?>">
            <select name="status_filter" class="filter-input">
                <option value="">All Statuses</option>
                <option value="SCHEDULED" <?php echo (isset($_POST['status_filter']) && $_POST['status_filter'] === 'SCHEDULED') ? 'selected' : ''; ?>>Scheduled</option>
                <option value="COMPLETED" <?php echo (isset($_POST['status_filter']) && $_POST['status_filter'] === 'COMPLETED') ? 'selected' : ''; ?>>Completed</option>
                <option value="CANCELLED" <?php echo (isset($_POST['status_filter']) && $_POST['status_filter'] === 'CANCELLED') ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            <select name="doctor_filter" class="filter-input flex-1">
                <option value="">All Doctors</option>
                <?php
                $doctor_query = "SELECT DOCTOR_ID, FIRST_NAME, LAST_NAME FROM doctor_tbl ORDER BY FIRST_NAME, LAST_NAME";
                $doctor_result = mysqli_query($conn, $doctor_query);
                $sel_doc = $_POST['doctor_filter'] ?? '';
                while ($dr = mysqli_fetch_assoc($doctor_result)) {
                    $sel = ($sel_doc == $dr['DOCTOR_ID']) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($dr['DOCTOR_ID']) . "' $sel>" . htmlspecialchars($dr['FIRST_NAME'] . ' ' . $dr['LAST_NAME']) . "</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn-filter">Search Appointments</button>
        </form>
    </div>

    <div class="doctor-grid">
        <?php
        $query = "SELECT d.DOCTOR_ID, d.FIRST_NAME as d_first, d.LAST_NAME as d_last,
                         p.FIRST_NAME as p_first, p.LAST_NAME as p_last,
                         a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, a.STATUS
                  FROM doctor_tbl d
                  LEFT JOIN appointment_tbl a ON d.DOCTOR_ID = a.DOCTOR_ID
                  LEFT JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
                  WHERE 1=1";

        if (!empty($_POST['date_filter'])) {
            $date = mysqli_real_escape_string($conn, $_POST['date_filter']);
            $query .= " AND a.APPOINTMENT_DATE='$date'";
        }
        if (!empty($_POST['status_filter'])) {
            $status = mysqli_real_escape_string($conn, $_POST['status_filter']);
            $query .= " AND a.STATUS='$status'";
        }
        if (!empty($_POST['doctor_filter'])) {
            $doc = mysqli_real_escape_string($conn, $_POST['doctor_filter']);
            $query .= " AND d.DOCTOR_ID='$doc'";
        }

        $query .= " ORDER BY d.DOCTOR_ID, a.APPOINTMENT_TIME ASC";
        $result = mysqli_query($conn, $query);

        $current_doctor = null;
        $has_any = false;

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                if ($current_doctor !== $row['DOCTOR_ID']) {
                    if ($current_doctor !== null) echo '</div></div>';
                    $current_doctor = $row['DOCTOR_ID'];
                    ?>
                    <div class="doctor-card">
                        <div class="doctor-header">
                            <i class="fa-solid fa-user-doctor"></i>
                            <h3>Dr. <?php echo htmlspecialchars($row['d_first'] . ' ' . $row['d_last']); ?></h3>
                        </div>
                        <div class="appointment-list">
                    <?php
                }

                if ($row['p_first'] !== null) {
                    $has_any = true;
                    $status_cls = strtolower($row['STATUS']);
                    ?>
                    <div class="patient-row">
                        <div class="patient-info">
                            <span class="patient-name"><?php echo htmlspecialchars($row['p_first'] . ' ' . $row['p_last']); ?></span>
                            <span class="apt-time">
                                <i class="bi bi-clock"></i> <?php echo date("h:i A", strtotime($row['APPOINTMENT_TIME'])); ?>
                                | <i class="bi bi-calendar3"></i> <?php echo htmlspecialchars($row['APPOINTMENT_DATE']); ?>
                            </span>
                        </div>
                        <span class="status-pill status-<?php echo $status_cls; ?>">
                            <?php echo htmlspecialchars($row['STATUS']); ?>
                        </span>
                    </div>
                    <?php
                } else {
                    echo "<p style='padding:20px; color:#999; text-align:center;'>No appointments assigned.</p>";
                }
            }
            echo '</div></div>';
        } else {
            echo "<div class='empty-state'><h3>No data found for the selected filters</h3></div>";
        }
        mysqli_close($conn);
        ?>
    </div>
</div>

</body>
</html>
