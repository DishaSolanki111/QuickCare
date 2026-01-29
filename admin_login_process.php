<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

/* ===============================
   GET FORM DATA
================================ */
 $user_type = $_POST['user_type'] ?? '';
 $username  = $_POST['username'] ?? '';
 $pswd  = $_POST['pswd'] ?? '';

/* ===============================
   BASIC VALIDATION
================================ */
if (empty($user_type) || empty($username) || empty($pswd)) {
    header("Location: admin_login.php?error=All fields are required");
    exit();
}

/* ===============================
   ADMIN LOGIN - HARDCODED
================================ */
if ($user_type === 'admin') {
    // Hardcoded admin credentials
    $admin_username = 'admin';
    $admin_password = 'admin123'; // Plain text password
    
    // Verify credentials
    if ($username === $admin_username && $pswd === $admin_password) {
        // Set session variables
        $_SESSION['LOGGED_IN'] = true;
        $_SESSION['USER_TYPE'] = 'admin';
        $_SESSION['ADMIN_ID'] = 1; // Hardcoded admin ID
        $_SESSION['USER_NAME'] = 'Administrator'; // Hardcoded admin name
        
        // Redirect to admin dashboard
        header("Location: admin.php");
        exit();
    }

    // ❌ Admin login failed
    header("Location: admin_login.php?error=Invalid admin username or password");
    exit();
}

/* ===============================
   UNKNOWN USER TYPE
================================ */
header("Location: admin_login.php?error=Invalid login attempt");
exit();
?>