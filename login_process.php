<?php
session_start();
include 'config.php';

$user = $_POST['username'];
$pass = $_POST['password'];

$q = mysqli_query($conn,"
SELECT P_ID FROM patient_tbl
WHERE USERNAME='$user' AND PSW='$pass'
");

if(mysqli_num_rows($q)==1){
    $row = mysqli_fetch_assoc($q);
    $_SESSION['P_ID']=$row['P_ID'];

    header("Location: payment.php?".http_build_query($_POST));
    exit;
}else{
    echo "Invalid Login";
}
