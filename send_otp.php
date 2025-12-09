<?php
session_start();
@include 'config.php';

if(!isset($_SESSION['P_ID'])) {
    header("Location: specialists.php");
    exit;
}

$doc_id = $_GET['doc_id'];
$p_id = $_SESSION['P_ID'];

$q = mysqli_query($conn, "SELECT PHONE FROM patient_tbl WHERE P_ID='$p_id'");
$row = mysqli_fetch_assoc($q);
$phone = $row['PHONE'];

$otp = rand(100000, 999999);

$_SESSION['OTP'] = $otp;

file_put_contents("otp_log.txt", "Send OTP: $otp to $phone");

header("Location: otp_verify.php?doc_id=".$doc_id);
?>
