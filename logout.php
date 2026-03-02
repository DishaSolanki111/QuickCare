<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset all session variables
$_SESSION = array();

// Clear the session cookie so browser doesn't send it again
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'] ?? false, $p['httponly'] ?? false);
}

// Destroy the session
session_destroy();

// Redirect to the home page
header('Location: index.php');
exit;