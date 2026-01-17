<?php
session_start();
include "config.php";

// Check if user is logged in
if (!isset($_SESSION['PATIENT_ID'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

// Get appointment data from POST
 $doctor_id = $_POST['doctor_id'];
 $doctor_name = $_POST['doctor_name'];
 $specialization = $_POST['specialization'];
 $appointment_date = $_POST['appointment_date'];
 $appointment_time = $_POST['appointment_time'];
 $reason = $_POST['reason'];

// Validate data
if (empty($doctor_id) || empty($appointment_date) || empty($appointment_time)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required appointment information']);
    exit;
}

// Store appointment data in session
 $_SESSION['PENDING_APPOINTMENT'] = array(
    'doctor_id' => $doctor_id,
    'doctor_name' => $doctor_name,
    'specialization' => $specialization,
    'date' => $appointment_date,
    'time' => $appointment_time,
    'reason' => $reason
);

echo json_encode(['status' => 'success', 'message' => 'Appointment data stored successfully']);
?>