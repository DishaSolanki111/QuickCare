<?php
session_start();

// Get the step from the request
 $step = isset($_POST['step']) ? $_POST['step'] : '';

// Store data based on the step
switch ($step) {
    case 'date':
        // Store selected date
        $_SESSION['booking_date'] = isset($_POST['appointment_date']) ? $_POST['appointment_date'] : '';
        break;
        
    case 'time':
        // Store selected time and reason
        $_SESSION['booking_time'] = isset($_POST['appointment_time']) ? $_POST['appointment_time'] : '';
        $_SESSION['booking_reason'] = isset($_POST['reason']) ? $_POST['reason'] : '';
        break;
        
    case 'payment':
        // Store payment details
        $_SESSION['payment_card_number'] = isset($_POST['card_number']) ? $_POST['card_number'] : '';
        $_SESSION['payment_expiry_month'] = isset($_POST['expiry_month']) ? $_POST['expiry_month'] : '';
        $_SESSION['payment_expiry_year'] = isset($_POST['expiry_year']) ? $_POST['expiry_year'] : '';
        $_SESSION['payment_cvv'] = isset($_POST['cvv']) ? $_POST['cvv'] : '';
        $_SESSION['payment_card_name'] = isset($_POST['card_name']) ? $_POST['card_name'] : '';
        
        // Save appointment to database
        $doctor_id = $_SESSION['booking_doctor_id'];
        $appointment_date = $_SESSION['booking_date'];
        $appointment_time = $_SESSION['booking_time'];
        $reason = $_SESSION['booking_reason'];
        
        include "config.php";
        
        // Get patient ID from session (assuming it's set after login)
        $patient_id = isset($_SESSION['patient_id']) ? $_SESSION['patient_id'] : 0;
        
        if ($patient_id > 0) {
            // Insert appointment into database
            $insert_query = "INSERT INTO appointment_tbl (PATIENT_ID, DOCTOR_ID, APPOINTMENT_DATE, APPOINTMENT_TIME, REASON, STATUS, PAYMENT_STATUS) 
                           VALUES ($patient_id, $doctor_id, '$appointment_date', '$appointment_time', '$reason', 'Confirmed', 'Paid')";
            
            $result = mysqli_query($conn, $insert_query);
            
            if ($result) {
                // Clear booking session data
                unset($_SESSION['booking_doctor_id']);
                unset($_SESSION['booking_doctor_name']);
                unset($_SESSION['booking_specialization']);
                unset($_SESSION['booking_date']);
                unset($_SESSION['booking_time']);
                unset($_SESSION['booking_reason']);
                unset($_SESSION['payment_card_number']);
                unset($_SESSION['payment_expiry_month']);
                unset($_SESSION['payment_expiry_year']);
                unset($_SESSION['payment_cvv']);
                unset($_SESSION['payment_card_name']);
                
                echo json_encode(['status' => 'success', 'message' => 'Appointment booked successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to book appointment']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Patient not logged in']);
        }
        exit;
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid step']);
        exit;
}

echo json_encode(['status' => 'success', 'message' => 'Data saved successfully']);
?>