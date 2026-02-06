<?php
session_start();
include "config.php";

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: forgot_password.php");
    exit();
}

 $otp = $_POST['otp'];
 $newPassword = $_POST['new_password'];
 $confirmPassword = $_POST['confirm_password'];

// 1. Verify OTP
if ($otp != $_SESSION['otp']) {
    $_SESSION['error'] = "Invalid OTP. Please try again.";
    header("Location: reset_password_form.php");
    exit();
}

// 2. Verify Passwords Match
if ($newPassword !== $confirmPassword) {
    $_SESSION['error'] = "Passwords do not match.";
    header("Location: reset_password_form.php");
    exit();
}

// 3. Get user info from session
 $userId = $_SESSION['reset_user_id'];
 $userType = $_SESSION['reset_user_type'];
 $idColumn = $_SESSION['reset_id_column'];

// 4. Determine the correct table
 $tableName = '';
switch ($userType) {
    case 'patient':
        $tableName = 'patient_tbl';
        break;
    case 'user':
        $tableName = 'user_tbl';
        break;
    case 'doctor':
        $tableName = 'doctor_tbl';
        break;
}

if (empty($tableName)) {
    $_SESSION['error'] = "An error occurred. Please start over.";
    header("Location: forgot_password.php");
    exit();
}

// 5. Hash the new password SECURELY
 $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Uses the strongest algorithm available

// 6. Update the password in the correct table using a PREPARED STATEMENT
 $sql = "UPDATE $tableName SET password = ? WHERE $idColumn = ?";
 $stmt = $conn->prepare($sql);
 $stmt->bind_param("si", $hashedPassword, $userId);

if ($stmt->execute()) {
    // Success! Clean up session and redirect to login
    unset($_SESSION['otp'], $_SESSION['phone'], $_SESSION['reset_user_id'], $_SESSION['reset_user_type'], $_SESSION['reset_id_column']);
    $_SESSION['status'] = "success"; // Use a status flag for the login page
    $_SESSION['message'] = "Your password has been reset successfully. Please log in.";
    header("Location: login.php");
} else {
    // Error
    $_SESSION['error'] = "Failed to update password. Please try again.";
    header("Location: reset_password_form.php");
}

 $stmt->close();
 $conn->close();
?>