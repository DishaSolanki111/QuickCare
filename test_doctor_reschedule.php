<?php
// Test file to verify doctor reschedule flow
session_start();

// Simulate doctor session
$_SESSION['USER_TYPE'] = 'doctor';
$_SESSION['DOCTOR_ID'] = 1; // Assuming doctor ID 1 exists
$_SESSION['LOGGED_IN'] = true;

// Simulate reschedule request
$_POST['reschedule_appointment_id'] = 123; // Test appointment ID
$_POST['doctor_id'] = 1;

echo "Doctor Reschedule Flow Test:\n";
echo "Session Type: " . $_SESSION['USER_TYPE'] . "\n";
echo "Doctor ID: " . $_SESSION['DOCTOR_ID'] . "\n";
echo "Reschedule Appointment ID: " . $_POST['reschedule_appointment_id'] . "\n";

// Test book_appointment_date.php access logic
$reschedule_appointment_id = isset($_POST['reschedule_appointment_id']) ? intval($_POST['reschedule_appointment_id']) : (isset($_SESSION['reschedule_appointment_id']) ? (int)$_SESSION['reschedule_appointment_id'] : 0);

if ($reschedule_appointment_id == 0) {
    echo "❌ Would redirect to dashboard (no reschedule ID)\n";
} else {
    echo "✅ Would allow access (has reschedule ID)\n";
}

// Test book_appointment_time.php access logic
$reschedule_appointment_id = isset($_SESSION['reschedule_appointment_id']) ? (int)$_SESSION['reschedule_appointment_id'] : 0;

if ($reschedule_appointment_id == 0) {
    echo "❌ Time page would redirect to dashboard\n";
} else {
    echo "✅ Time page would allow access\n";
}

echo "\nFlow should work: appointment_doctor.php → book_appointment_date.php → book_appointment_time.php → reschedule_appointment_doctor.php\n";
?>
