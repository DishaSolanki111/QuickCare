<?php
// login.php
// minimal, exact response "success" for admin/123 and "fail" otherwise
// no extra whitespace before/after <?php ... ?and no BOM in file.

session_start();

// Use POST values safely:
$user = isset($_POST['username']) ? trim($_POST['username']) : '';
$pass = isset($_POST['password']) ? trim($_POST['password']) : '';

// For testing only: accept admin/123
if ($user === 'admin' && $pass === '123') {
    $_SESSION['logged_in'] = true;
    // echo exactly success (no whitespace/newlines)
    echo "success";
    exit;
}

// else
echo "fail";
exit;
?>

