<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "config.php";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Don't escape the password before verification
    
    // Query to check user credentials in patient table
    $query = "SELECT * FROM patient_tbl WHERE USERNAME = '$username'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password using bcrypt
        if (password_verify($password, $user['PSWD'])) { // Changed from PASSWORD to PSWD
            // Set session variables (align with loginhome.php for consistency)
            $_SESSION['LOGGED_IN'] = true;
            $_SESSION['USER_TYPE'] = 'patient';
            $_SESSION['PATIENT_ID'] = $user['PATIENT_ID'];
            $_SESSION['PATIENT_NAME'] = $user['FIRST_NAME'] . ' ' . $user['LAST_NAME'];
            $_SESSION['PATIENT_USERNAME'] = $user['USERNAME'];
            $_SESSION['USER_NAME'] = $user['FIRST_NAME'];
            $_SESSION['role'] = 'patient';
            
            // Return success response
            echo json_encode(['status' => 'success', 'message' => 'Login successful']);
            exit;
        } else {
            // Return error response
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
            exit;
        }
    } else {
        // Return error response
        echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
        exit;
    }
}
?>