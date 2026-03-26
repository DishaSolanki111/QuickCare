<?php
session_start();
// Check if user is logged in and is a doctor
if (
    !isset($_SESSION['LOGGED_IN']) ||
    $_SESSION['LOGGED_IN'] !== true ||
    !isset($_SESSION['USER_TYPE']) ||
    $_SESSION['USER_TYPE'] !== 'doctor'
) {
    header("Location: login.php");
    exit();
}

// Include your existing config file
require_once 'config.php';

// Check if connection variable $conn exists and is valid
if (!$conn) {
    die("Error: Database connection failed. Please check your config.php file.");
}

// Get doctor information from session
$doctor_id   = $_SESSION['DOCTOR_ID'];
$doctor_name = "Doctor";

// Fetch doctor's name from database
$doc_sql  = "SELECT FIRST_NAME, LAST_NAME FROM doctor_tbl WHERE DOCTOR_ID = ?";
$doc_stmt = $conn->prepare($doc_sql);
$doc_stmt->bind_param("i", $doctor_id);
$doc_stmt->execute();
$doc_result = $doc_stmt->get_result();
if ($doc_result->num_rows === 1) {
    $doc         = $doc_result->fetch_assoc();
    $doctor_name = htmlspecialchars($doc['FIRST_NAME'] . ' ' . $doc['LAST_NAME']);
}
$doc_stmt->close();

// Check for success message in URL
$alert_message = '';
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    if ($status === 'success')        $alert_message = "Prescription added successfully!";
    elseif ($status === 'updated')    $alert_message = "Prescription updated successfully!";
    elseif ($status === 'deleted_success') $alert_message = "Prescription deleted successfully!";
}

// -------------------------------------------------------
// Filter parameters — use POST for searching
// -------------------------------------------------------
// Detect whether the filter form was actually submitted
$filter_submitted = ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['filter_name']) || isset($_POST['filter_date'])));

$filter_name = $filter_submitted ? trim($_POST['filter_name'] ?? '') : '';
$filter_date = $filter_submitted ? trim($_POST['filter_date'] ?? '') : '';

// Build query with optional filters
$conditions = ["a.DOCTOR_ID = ?"];
$types      = "i";
$params     = [$doctor_id];

if ($filter_name !== '') {
    $conditions[] = "(pt.FIRST_NAME LIKE ? OR pt.LAST_NAME LIKE ? OR CONCAT(pt.FIRST_NAME, ' ', pt.LAST_NAME) LIKE ?)";
    $types        .= "sss";
    $name_pattern  = '%' . $filter_name . '%';
    $params[]      = $name_pattern;
    $params[]      = $name_pattern;
    $params[]      = $name_pattern;
}
if ($filter_date !== '') {
    $conditions[] = "a.APPOINTMENT_DATE = ?";
    $types        .= "s";
    $params[]      = $filter_date;
}

$sql = "SELECT p.PRESCRIPTION_ID, pt.PATIENT_ID, pt.FIRST_NAME, pt.LAST_NAME, pt.PHONE, pt.EMAIL,
                a.APPOINTMENT_ID, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME
         FROM prescription_tbl p
         INNER JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
         INNER JOIN patient_tbl pt    ON a.PATIENT_ID = pt.PATIENT_ID
         WHERE " . implode(" AND ", $conditions) . "
         ORDER BY a.APPOINTMENT_DATE DESC, a.APPOINTMENT_TIME DESC";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing query: " . $conn->error);
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$prescriptions_list = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $prescriptions_list[] = $row;
    }
}
$stmt->close();
$conn->close();

// Determine the "no results" message to show
$no_results_message = '';
if (empty($prescriptions_list)) {
    if ($filter_submitted && ($filter_name !== '' || $filter_date !== '')) {
        $no_results_message = "No prescriptions found matching your search.";
    } else {
        $no_results_message = "No prescriptions found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Prescriptions - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f5f7fa; color: #333; line-height: 1.6; }
        .container { display: flex; min-height: 100vh; }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
            margin-top: -15px;
        }

        .content-card {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h1 {
            color: #072d44;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        thead { background-color: #072d44; color: white; }
        tbody tr:hover { background-color: #f5f5f5; }

        .btn {
            display: inline-block;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            color: #fff;
            background-color: #28a745;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        .btn:hover          { background-color: #218838; }
        .btn-primary        { background-color: #072d44; }
        .btn-primary:hover  { background-color: #0056b3; }
        .btn-secondary      { background-color: #6c757d; }
        .btn-secondary:hover{ background-color: #5a6268; }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: flex-end;
            margin-bottom: 25px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        .filter-group label { font-weight: 600; color: var(--primary-color); font-size: 14px; }
        .filter-group input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; min-width: 180px; }
        .filter-actions { display: flex; gap: 10px; align-items: flex-end; }

        

        /* No results */
        .no-results {
            text-align: center;
            color: #777;
            padding: 30px 0;
            font-size: 15px;
        }
        .no-results i { font-size: 36px; display: block; margin-bottom: 10px; color: #ccc; }

        @media (max-width: 992px) { .main-content { margin-left: 70px; } }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'doctor_sidebar.php'; ?>
        <div class="main-content">
            <?php include 'doctor_header.php'; ?>

            <div class="content-card">
                <h1>Manage Prescriptions</h1>


                <!-- Filter Form (POST to avoid exposing values in URL) -->
                <form method="POST" action="manage_prescriptions.php" class="filter-form">
                    <div class="filter-group">
                        <label for="filter_name"><i class="fas fa-user"></i> Patient Name</label>
                        <input type="text" id="filter_name" name="filter_name"
                               placeholder="Search by name..."
                               value="<?php echo htmlspecialchars($filter_name); ?>">
                    </div>
                    <div class="filter-group">
                        <label for="filter_date"><i class="fas fa-calendar"></i> Appointment Date</label>
                        <input type="date" id="filter_date" name="filter_date"
                               value="<?php echo htmlspecialchars($filter_date); ?>">
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Search</button>
                        <button type="button" class="btn btn-secondary"
                                onclick="window.location.href='manage_prescriptions.php'">
                            <i class="fas fa-times"></i> Clear
                        </button>
                    </div>
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>Patient Name</th>
                            <th>Appointment Date</th>
                            <th>Contact</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($prescriptions_list)): ?>
                            <?php foreach ($prescriptions_list as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['FIRST_NAME'] . ' ' . $row['LAST_NAME']); ?></td>
                                    <td><?php
                                        $dt = !empty($row['APPOINTMENT_DATE']) ? $row['APPOINTMENT_DATE'] : '';
                                        $tm = !empty($row['APPOINTMENT_TIME']) ? $row['APPOINTMENT_TIME'] : '';
                                        if ($dt) echo htmlspecialchars((new DateTime($dt))->format('F d, Y') . ($tm ? ' ' . date('h:i A', strtotime($tm)) : ''));
                                        else echo 'N/A';
                                    ?></td>
                                    <td><?php
                                        $contact_parts = [];
                                        if (!empty($row['PHONE'])) $contact_parts[] = 'Ph: ' . htmlspecialchars($row['PHONE']);
                                        if (!empty($row['EMAIL'])) $contact_parts[] = htmlspecialchars($row['EMAIL']);
                                        echo implode(' | ', $contact_parts ?: ['N/A']);
                                    ?></td>
                                    <td>
                                        <!-- Edit uses POST form — IDs never shown in URL -->
                                        <form method="POST" action="prescription_form.php" style="display:inline;">
                                            <input type="hidden" name="patient_id"      value="<?php echo (int)$row['PATIENT_ID']; ?>">
                                            <input type="hidden" name="prescription_id" value="<?php echo (int)$row['PRESCRIPTION_ID']; ?>">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-edit"></i> Edit Prescription
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">
                                    <div class="no-results">
                                        <i class="fas fa-file-medical-alt"></i>
                                        <?php echo htmlspecialchars($no_results_message); ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if (!empty($alert_message)): ?>
        <script>
            window.onload = function () {
                alert('<?php echo addslashes($alert_message); ?>');
            };
        </script>
    <?php endif; ?>
</body>
</html>