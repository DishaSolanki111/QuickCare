<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['PATIENT_ID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

 $patient_id = $_SESSION['PATIENT_ID'];
 $current_time = date('H:i:s');
 $current_date = date('Y-m-d');

// Get reminders for today that haven't been shown yet
 $query = "SELECT ar.REMARKS, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, d.FIRST_NAME, d.LAST_NAME
          FROM appointment_reminder_tbl ar
          JOIN appointment_tbl a ON ar.APPOINTMENT_ID = a.APPOINTMENT_ID
          JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
          WHERE a.PATIENT_ID = $patient_id
          AND a.APPOINTMENT_DATE >= '$current_date'
          AND ar.REMINDER_TIME <= '$current_time'
          AND ar.REMINDER_TIME > DATE_SUB('$current_time', INTERVAL 1 HOUR)
          ORDER BY ar.REMINDER_TIME DESC
          LIMIT 5";

 $result = mysqli_query($conn, $query);

 $reminders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $reminders[] = [
        'message' => $row['REMARKS'],
        'date' => $row['APPOINTMENT_DATE'],
        'time' => $row['APPOINTMENT_TIME'],
        'doctor' => 'Dr. ' . $row['FIRST_NAME'] . ' ' . $row['LAST_NAME']
    ];
}

echo json_encode(['status' => 'success', 'reminders' => $reminders]);
?>