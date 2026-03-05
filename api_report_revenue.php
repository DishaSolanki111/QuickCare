<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

// Session check
if (!isset($_SESSION['LOGGED_IN']) || $_SESSION['LOGGED_IN'] !== true || $_SESSION['USER_TYPE'] !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-6 months'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

$response = [
    'monthly_revenue' => [
        'labels' => [],
        'data' => []
    ],
    'payment_method' => [
        'labels' => [],
        'data' => []
    ],
    'table_data' => []
];

// 1. Monthly Revenue (Bar Chart)
$q1 = "SELECT DATE_FORMAT(PAYMENT_DATE, '%Y-%m') as month_year, SUM(AMOUNT) as total 
       FROM payment_tbl 
       WHERE PAYMENT_DATE BETWEEN '$start_date' AND '$end_date' AND STATUS = 'COMPLETED'
       GROUP BY month_year
       ORDER BY month_year ASC";
$r1 = mysqli_query($conn, $q1);
if ($r1) {
    while ($row = mysqli_fetch_assoc($r1)) {
        $response['monthly_revenue']['labels'][] = $row['month_year'];
        $response['monthly_revenue']['data'][] = $row['total'];
    }
}

// 2. Payment method distribution (Pie Chart)
$q2 = "SELECT PAYMENT_MODE, SUM(AMOUNT) as total 
       FROM payment_tbl 
       WHERE PAYMENT_DATE BETWEEN '$start_date' AND '$end_date' AND STATUS = 'COMPLETED'
       GROUP BY PAYMENT_MODE";
$r2 = mysqli_query($conn, $q2);
if ($r2) {
    while ($row = mysqli_fetch_assoc($r2)) {
        $response['payment_method']['labels'][] = $row['PAYMENT_MODE'];
        $response['payment_method']['data'][] = $row['total'];
    }
}

// 3. Table Data
$q3 = "SELECT p.PAYMENT_DATE, p.AMOUNT, p.PAYMENT_MODE, p.STATUS, p.TRANSACTION_ID, 
              pat.FIRST_NAME, pat.LAST_NAME
       FROM payment_tbl p
       JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
       JOIN patient_tbl pat ON a.PATIENT_ID = pat.PATIENT_ID
       WHERE p.PAYMENT_DATE BETWEEN '$start_date' AND '$end_date' AND p.STATUS = 'COMPLETED'
       ORDER BY p.PAYMENT_DATE DESC";
$r3 = mysqli_query($conn, $q3);
if ($r3) {
    while ($row = mysqli_fetch_assoc($r3)) {
        $response['table_data'][] = [
            'date' => $row['PAYMENT_DATE'],
            'amount' => $row['AMOUNT'],
            'mode' => $row['PAYMENT_MODE'],
            'transaction' => $row['TRANSACTION_ID'],
            'patient' => $row['FIRST_NAME'] . ' ' . $row['LAST_NAME']
        ];
    }
}

echo json_encode($response);
?>