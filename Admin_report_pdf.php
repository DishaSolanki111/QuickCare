<?php
session_start();
include 'config.php';

// Basic admin check
if (!isset($_SESSION['LOGGED_IN']) || $_SESSION['LOGGED_IN'] !== true || $_SESSION['USER_TYPE'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

$type = isset($_GET['type']) ? $_GET['type'] : 'patient';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$doctor_id = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : '';

$title = '';
$columns = [];
$rows = [];

if ($type === 'patient') {
    $title = 'Patient Report';
    $columns = ['Patient ID', 'Name', 'Gender', 'Phone', 'Registration Date'];

    $q = "SELECT PATIENT_ID, FIRST_NAME, LAST_NAME, GENDER, PHONE, CREATED_AT
          FROM patient_tbl
          WHERE DATE(CREATED_AT) BETWEEN '$start_date' AND '$end_date'
          ORDER BY CREATED_AT DESC";
    $res = mysqli_query($conn, $q);
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $rows[] = [
                $r['PATIENT_ID'],
                trim($r['FIRST_NAME'] . ' ' . $r['LAST_NAME']),
                $r['GENDER'],
                $r['PHONE'],
                substr($r['CREATED_AT'], 0, 10),
            ];
        }
    }
} elseif ($type === 'appointment') {
    $title = 'Appointment Report';
    $columns = ['Date', 'Time', 'Patient Name', 'Doctor Name', 'Status'];

    $doc_filter = '';
    if (!empty($doctor_id)) {
        $doctor_id = mysqli_real_escape_string($conn, $doctor_id);
        $doc_filter = " AND a.DOCTOR_ID = '$doctor_id'";
    }

    $q = "SELECT a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, a.STATUS,
                 p.FIRST_NAME AS p_first, p.LAST_NAME AS p_last,
                 d.FIRST_NAME AS d_first, d.LAST_NAME AS d_last
          FROM appointment_tbl a
          JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
          JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
          WHERE a.APPOINTMENT_DATE BETWEEN '$start_date' AND '$end_date' $doc_filter
          ORDER BY a.APPOINTMENT_DATE DESC, a.APPOINTMENT_TIME DESC";
    $res = mysqli_query($conn, $q);
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $rows[] = [
                $r['APPOINTMENT_DATE'],
                $r['APPOINTMENT_TIME'],
                trim($r['p_first'] . ' ' . $r['p_last']),
                'Dr. ' . trim($r['d_first'] . ' ' . $r['d_last']),
                $r['STATUS'],
            ];
        }
    }
} elseif ($type === 'revenue') {
    // revenue
    $type = 'revenue';
    $title = 'Revenue Report';
    $columns = ['Payment Date', 'Amount (₹)', 'Payment Mode', 'Transaction ID', 'Patient Name'];

    $q = "SELECT p.PAYMENT_DATE, p.AMOUNT, p.PAYMENT_MODE, p.TRANSACTION_ID,
                 pat.FIRST_NAME, pat.LAST_NAME
          FROM payment_tbl p
          JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
          JOIN patient_tbl pat ON a.PATIENT_ID = pat.PATIENT_ID
          WHERE p.PAYMENT_DATE BETWEEN '$start_date' AND '$end_date'
            AND p.STATUS = 'COMPLETED'
          ORDER BY p.PAYMENT_DATE DESC";
    $res = mysqli_query($conn, $q);
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $rows[] = [
                $r['PAYMENT_DATE'],
                number_format((float) $r['AMOUNT'], 2),
                $r['PAYMENT_MODE'],
                $r['TRANSACTION_ID'],
                trim($r['FIRST_NAME'] . ' ' . $r['LAST_NAME']),
            ];
        }
    }
}

// Fallback HTML-based "PDF" (printable) output: table only, no charts/graphs.
if (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($title) ?> - QuickCare</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #1f2933;
        }

        h1 {
            margin: 0 0 5px 0;
            font-size: 22px;
        }

        .meta {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 13px;
        }

        th,
        td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
            text-align: left;
        }

        th {
            background: #072D44;
            color: #ffffff;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }
    </style>
</head>

<body>
    <h1><?= htmlspecialchars($title) ?></h1>
    <div class="meta">
        Date range:
        <?= htmlspecialchars($start_date) ?> to <?= htmlspecialchars($end_date) ?><br>
        Generated on: <?= date('Y-m-d H:i') ?>
    </div>

    <table>
        <thead>
            <tr>
                <?php foreach ($columns as $col): ?>
                    <th><?= htmlspecialchars($col) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($rows)): ?>
                    <?php foreach ($rows as $r): ?>
                    <tr>
                                <?php foreach ($r as $cell): ?>
                            <td><?= htmlspecialchars((string) $cell) ?></td>
                      <?php endforeach; ?>
                    </tr>
              <?php endforeach; ?>
          <?php else: ?>
                <tr>
                    <td colspan="<?= count($columns) ?>">No records found for the selected filters.</td>
                </tr>
          <?php endif; ?>
        </tbody>
    </table>

    <script>
        // Automatically open the browser's print dialog so the user can save as PDF.
        window.onload = function () {
            window.print();
        };
    </script>
</body>

</html>