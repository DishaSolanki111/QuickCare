<?php
session_start();
@include 'config.php';

$username = $_POST['username'];
$password = $_POST['password'];
$doc_id   = $_POST['doc_id'];

$q = mysqli_query($conn, "SELECT * FROM patient_tbl WHERE USERNAME='$username' AND PSW='$password'");

if(mysqli_num_rows($q) == 1){
    $row = mysqli_fetch_assoc($q);
    $_SESSION['P_ID'] = $row['P_ID'];

    header("Location: send_otp.php?doc_id=".$doc_id);
    exit;
}
else{
    echo "<script>alert('Invalid Username or Password'); window.history.back();</script>";
}
