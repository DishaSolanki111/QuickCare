<?php
$host = "localhost";
$user = "root";
$pass = "your_password";
$db   = "quick_care";

$conn = mysqli_connect($host, $user, $pass, $db);

if(!$conn){
    die("Database connection failed: " . mysqli_connect_error());
}
?>
