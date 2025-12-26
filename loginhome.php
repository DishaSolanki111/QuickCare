<?php
session_start();
include 'config.php';

// Get form data
 $username = $_POST['username'] ?? '';
 $password = $_POST['pswd'] ?? '';
 $user_type = $_POST['user_type'] ?? '';

// Validate input
if (empty($username) || empty($password) || empty($user_type)) {
    header("Location: login_for_all.php?error=Please fill all fields&user_type=$user_type");
    exit;
}

// Sanitize input
 $username = mysqli_real_escape_string($conn, $username);

// Check user type and query appropriate table
switch ($user_type) {
    case 'patient':
        $query = "SELECT PATIENT_ID, FIRST_NAME, LAST_NAME, EMAIL, PSWD FROM patient_tbl WHERE USERNAME='$username'";
        $redirect_page = 'patient.php';
        $session_id_key = 'PATIENT_ID';
        $session_name_key = 'PATIENT_NAME';
        break;
        
    case 'doctor':
        $query = "SELECT DOCTOR_ID, FIRST_NAME, LAST_NAME, EMAIL, PSWD FROM doctor_tbl WHERE USERNAME='$username'";
        $redirect_page = 'doctor_dashboard.php';
        $session_id_key = 'DOCTOR_ID';
        $session_name_key = 'DOCTOR_NAME';
        break;
        
    case 'receptionist':
        $query = "SELECT RECEPTIONIST_ID, FIRST_NAME, LAST_NAME, EMAIL, PSWD FROM receptionist_tbl WHERE USERNAME='$username'";
        $redirect_page = 'receptionist_dashboard.php';
        $session_id_key = 'RECEPTIONIST_ID';
        $session_name_key = 'RECEPTIONIST_NAME';
        break;
        
    default:
        header("Location: login_for_all.php?error=Invalid user type&user_type=$user_type");
        exit;
}

// Execute query
 $result = mysqli_query($conn, $query);

// Check if user exists
if (!$result || mysqli_num_rows($result) !== 1) {
    header("Location: login_for_all.php?error=Invalid username or password&user_type=$user_type");
    exit;
}

// Get user data
 $user = mysqli_fetch_assoc($result);

// Verify password
if (!password_verify($password, $user['PSWD'])) {
    header("Location: login_for_all.php?error=Invalid username or password&user_type=$user_type");
    exit;
}

// Set session variables
 $_SESSION[$session_id_key] = $user[$session_id_key];
 $_SESSION[$session_name_key] = $user['FIRST_NAME'] . ' ' . $user['LAST_NAME'];
 $_SESSION['USER_TYPE'] = $user_type;
 $_SESSION['EMAIL'] = $user['EMAIL'];
 $_SESSION['LOGGED_IN'] = true;

// Redirect to appropriate dashboard
header("Location: $redirect_page");
exit;
?>