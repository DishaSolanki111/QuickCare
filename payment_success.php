<?php
session_start();
include 'config.php';

if (!isset($_SESSION['PATIENT_ID'])) {
    header("Location: login.php");
    exit;
}

/* REQUIRED DATA */
 $patient_id = $_SESSION['PATIENT_ID'];
 $doctor_id = $_POST['doctor_id'];
 $schedule_id = $_POST['schedule_id'];
 $date        = $_POST['date'];
 $time        = $_POST['time'];

// Clear the pending appointment from session
unset($_SESSION['PENDING_APPOINTMENT']);

// ✅ CORRECT INSERT — MATCHES TABLE STRUCTURE
 $q = "
INSERT INTO appointment_tbl
(PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, CREATED_AT, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS)
VALUES
('$patient_id', '$doctor_id', '$schedule_id', NOW(), '$date', '$time', 'scheduled')
";

if (mysqli_query($conn, $q)) {
    // Redirect to dashboard after successful payment
    header("Location: patient.php?payment=success");
    exit;
} else {
    echo "<div style='padding: 20px; text-align: center;'>";
    echo "<h2>Payment Failed</h2>";
    echo "<p>There was an error processing your appointment:</p>";
    echo "<pre>" . mysqli_error($conn) . "</pre>";
    echo "<a href='patient.php' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #072D44; color: white; text-decoration: none; border-radius: 5px;'>Return to Dashboard</a>";
    echo "</div>";
}
?>