<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

 $step = isset($_POST['step']) ? $_POST['step'] : '';

switch ($step) {
    case 'date':
        $_SESSION['booking_date'] = isset($_POST['appointment_date']) ? $_POST['appointment_date'] : '';
        echo json_encode(['status' => 'success']);
        break;
        
    case 'time':
        $_SESSION['booking_time'] = isset($_POST['appointment_time']) ? $_POST['appointment_time'] : '';
        $_SESSION['booking_reason'] = isset($_POST['reason']) ? $_POST['reason'] : '';
        echo json_encode(['status' => 'success']);
        break;
        
    case 'payment':
        include "config.php";
        
        $doctor_id = $_SESSION['booking_doctor_id'];
        $appointment_date = $_SESSION['booking_date'];
        $appointment_time = $_SESSION['booking_time'];
        $reason = $_SESSION['booking_reason'];
        $patient_id = $_SESSION['PATIENT_ID'];
        
        $insert_query = "INSERT INTO appointment_tbl (PATIENT_ID, DOCTOR_ID, APPOINTMENT_DATE, APPOINTMENT_TIME, REASON, STATUS, PAYMENT_STATUS) 
                       VALUES ($patient_id, $doctor_id, '$appointment_date', '$appointment_time', '$reason', 'Confirmed', 'Paid')";
        
        $result = mysqli_query($conn, $insert_query);
        
        if ($result) {
            unset($_SESSION['booking_doctor_id']);
            unset($_SESSION['booking_doctor_name']);
            unset($_SESSION['booking_specialization']);
            unset($_SESSION['booking_date']);
            unset($_SESSION['booking_time']);
            unset($_SESSION['booking_reason']);
            
            echo json_encode(['status' => 'success', 'message' => 'Appointment booked successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to book appointment']);
        }
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid step']);
}
?>