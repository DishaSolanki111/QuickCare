<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "quick_care";
$pass = "your_password";
$db   = "quick_care";


$conn = mysqli_connect(hostname: $host, username: $user, password: $pass, database: $db);

if(!$conn){
    die("Database connection failed: " . mysqli_connect_error());
}
?>
