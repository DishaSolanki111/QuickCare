<?php
include 'config.php';

header('Content-Type: application/json');

 $doctor_id = $_POST['doctor_id'] ?? '';

if (empty($doctor_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Doctor ID is required']);
    exit;
}

// Fetch doctor's available days from database
 $query = "SELECT AVAILABLE_DAY FROM doctor_schedule_tbl WHERE DOCTOR_ID = '$doctor_id'";
 $result = mysqli_query($conn, $query);

if (!$result) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . mysqli_error($conn)]);
    exit;
}

 $available_days = [];
while ($row = mysqli_fetch_assoc($result)) {
    $available_days[] = $row['AVAILABLE_DAY'];
}

echo json_encode(['status' => 'success', 'available_days' => $available_days]);
?>