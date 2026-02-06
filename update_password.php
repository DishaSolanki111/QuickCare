<?php
session_start();
include "config.php";

$pass = md5($_POST['pass']);
$phone = $_SESSION['phone'];

mysqli_query($conn,"UPDATE users SET password='$pass' WHERE phone='$phone'");

echo "Password updated";
?>