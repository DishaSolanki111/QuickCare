<?php
session_start();

if($_POST['otp'] == $_SESSION['otp']){
    header("Location: reset_password.php");
}else{
    echo "Wrong OTP";
}
?>