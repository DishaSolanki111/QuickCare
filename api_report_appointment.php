<?php
session_start();
include 'config.php';
header('Content-Type: application/json');

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$doctor_id = isset($_GET['doctor_id']) ? $_GET['doctor_id'] : '';

$response = [
    'daily_trend' => [
        'labels' => [],
        'data' => []
    ],
    'doctor_wise' => [
        'labels' => [],
        'data' => []
    ],
    'status_distribution' => [
        'labels' => [],
        'data' => []
    ],
    'table_data' => []
];

$doc_filter = "";
if(!empty($doctor_id)) {
    $doc_filter = " AND a.DOCTOR_ID = '$doctor_id'";
}

// 1. Daily Appointment Trend (Line Chart)
$q1 = "SELECT APPOINTMENT_DATE, COUNT(*) as count 
       FROM appointment_tbl a
       WHERE APPOINTMENT_DATE BETWEEN '$start_date' AND '$end_date' $doc_filter
       GROUP BY APPOINTMENT_DATE 
       ORDER BY APPOINTMENT_DATE ASC";
$r1 = mysqli_query($conn, $q1);
if($r1) {
    while($row = mysqli_fetch_assoc($r1)) {
        $response['daily_trend']['labels'][] = $row['APPOINTMENT_DATE'];
        $response['daily_trend']['data'][] = $row['count'];
    }
}

// 2. Doctor-wise appointment count (Bar Chart)
$q2 = "SELECT d.FIRST_NAME, d.LAST_NAME, COUNT(a.APPOINTMENT_ID) as count 
       FROM appointment_tbl a
       JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
       WHERE a.APPOINTMENT_DATE BETWEEN '$start_date' AND '$end_date' $doc_filter
       GROUP BY a.DOCTOR_ID
       ORDER BY count DESC
       LIMIT 10";
$r2 = mysqli_query($conn, $q2);
if($r2) {
    while($row = mysqli_fetch_assoc($r2)) {
        $name = "Dr. " . $row['FIRST_NAME'] . " " . $row['LAST_NAME'];
        $response['doctor_wise']['labels'][] = $name;
        $response['doctor_wise']['data'][] = $row['count'];
    }
}

// 3. Completed vs Cancelled vs Scheduled (Pie Chart)
$q3 = "SELECT STATUS, COUNT(*) as count 
       FROM appointment_tbl a
       WHERE APPOINTMENT_DATE BETWEEN '$start_date' AND '$end_date' $doc_filter
       GROUP BY STATUS";
$r3 = mysqli_query($conn, $q3);
if($r3) {
    while($row = mysqli_fetch_assoc($r3)) {
        $response['status_distribution']['labels'][] = $row['STATUS'];
        $response['status_distribution']['data'][] = $row['count'];
    }
}

// 4. Table Data
$q4 = "SELECT a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, a.STATUS, 
              p.FIRST_NAME as p_first, p.LAST_NAME as p_last, 
              d.FIRST_NAME as d_first, d.LAST_NAME as d_last
       FROM appointment_tbl a
       JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
       JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
       WHERE a.APPOINTMENT_DATE BETWEEN '$start_date' AND '$end_date' $doc_filter
       ORDER BY a.APPOINTMENT_DATE DESC, a.APPOINTMENT_TIME DESC";
$r4 = mysqli_query($conn, $q4);
if($r4) {
    while($row = mysqli_fetch_assoc($r4)) {
        $response['table_data'][] = [
            'date' => $row['APPOINTMENT_DATE'],
            'time' => $row['APPOINTMENT_TIME'],
            'patient' => $row['p_first'] . ' ' . $row['p_last'],
            'doctor' => 'Dr. ' . $row['d_first'] . ' ' . $row['d_last'],
            'status' => $row['STATUS']
        ];
    }
}

echo json_encode($response);
?>
