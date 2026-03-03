<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

// Session check
if (!isset($_SESSION['admin_logged_in']) && !isset($_SESSION['admin'])) {
    // Uncomment for strict security
    // echo json_encode(['error' => 'Unauthorized']);
    // exit;
}

// Add CREATED_AT to patient_tbl if it doesn't exist (Migration)
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM `patient_tbl` LIKE 'CREATED_AT'");
if(mysqli_num_rows($check_col) == 0) {
    mysqli_query($conn, "ALTER TABLE `patient_tbl` ADD `CREATED_AT` TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
}

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-6 months'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

$response = [
    'monthly_registration' => [
        'labels' => [],
        'data' => []
    ],
    'gender_distribution' => [
        'labels' => [],
        'data' => []
    ],
    'table_data' => []
];

// 1. Monthly Registration (Bar Chart)
$query1 = "SELECT DATE_FORMAT(CREATED_AT, '%Y-%m') as month_year, COUNT(*) as count 
           FROM patient_tbl 
           WHERE DATE(CREATED_AT) BETWEEN '$start_date' AND '$end_date' 
           GROUP BY month_year 
           ORDER BY month_year ASC";
$res1 = mysqli_query($conn, $query1);
if($res1) {
    while($row = mysqli_fetch_assoc($res1)) {
        $response['monthly_registration']['labels'][] = $row['month_year'];
        $response['monthly_registration']['data'][] = $row['count'];
    }
}

// 2. Gender distribution (Pie Chart)
// Here we ignore date filter to get overall distribution, or apply it. Applied it here.
$query2 = "SELECT GENDER, COUNT(*) as count 
           FROM patient_tbl 
           WHERE DATE(CREATED_AT) BETWEEN '$start_date' AND '$end_date' 
           GROUP BY GENDER";
$res2 = mysqli_query($conn, $query2);
if($res2) {
    while($row = mysqli_fetch_assoc($res2)) {
        $gender = $row['GENDER'] ? $row['GENDER'] : 'Unknown';
        $response['gender_distribution']['labels'][] = $gender;
        $response['gender_distribution']['data'][] = $row['count'];
    }
}

// 3. Table Data
$query3 = "SELECT PATIENT_ID, FIRST_NAME, LAST_NAME, GENDER, PHONE, CREATED_AT 
           FROM patient_tbl 
           WHERE DATE(CREATED_AT) BETWEEN '$start_date' AND '$end_date' 
           ORDER BY CREATED_AT DESC";
$res3 = mysqli_query($conn, $query3);
if($res3) {
    while($row = mysqli_fetch_assoc($res3)) {
        $response['table_data'][] = $row;
    }
}

echo json_encode($response);
?>
