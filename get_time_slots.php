<?php
include 'config.php';

header('Content-Type: application/json');

 $doctor_id = $_POST['doctor_id'] ?? '';
 $date = $_POST['date'] ?? '';

if (empty($doctor_id) || empty($date)) {
    echo json_encode(['status' => 'error', 'message' => 'Doctor ID and date are required']);
    exit;
}

// Get day of week from date
 $day_of_week = date('D', strtotime($date));

// Fetch doctor's schedule for that day
 $query = "SELECT START_TIME, END_TIME FROM doctor_schedule_tbl 
          WHERE DOCTOR_ID = '$doctor_id' AND AVAILABLE_DAY = '$day_of_week'";
 $result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    echo json_encode(['status' => 'error', 'message' => 'No schedule found for this day']);
    exit;
}

 $row = mysqli_fetch_assoc($result);
 $start_time = strtotime($row['START_TIME']);
 $end_time = strtotime($row['END_TIME']);

// Generate time slots (hourly slots)
 $time_slots = [];
while ($start_time < $end_time) {
    $time_slots[] = date('H:i', $start_time);
    $start_time = strtotime('+1 hour', $start_time);
}

echo json_encode(['status' => 'success', 'time_slots' => $time_slots]);
?>