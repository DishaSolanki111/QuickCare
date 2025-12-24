<?php
session_start();
include 'config.php';

/* ---------- SAFE INPUT FETCH ---------- */
$first_name  = $_POST['FIRST_NAME']   ?? '';
$last_name   = $_POST['LAST_NAME']    ?? '';
$username    = $_POST['USERNAME']     ?? '';
$pswd        = $_POST['PSWD']         ?? '';
$dob         = $_POST['DOB']          ?? '';
$gender      = $_POST['GENDER']       ?? '';
$blood_group = $_POST['BLOOD_GROUP']  ?? '';
$phone       = $_POST['PHONE']        ?? '';
$email       = $_POST['EMAIL']        ?? '';
$address     = $_POST['ADDRESS']      ?? '';

$doctor_id   = $_POST['doctor_id']    ?? '';
$date        = $_POST['date']         ?? '';
$time        = $_POST['time']         ?? '';
$schedule_id = $_POST['schedule_id']  ?? '';

/* ---------- MINIMUM VALIDATION ---------- */
if ($username == '' || $pswd == '') {
    die("Username or Password missing");
}

/* ---------- INSERT PATIENT ---------- */
$q = "
INSERT INTO patient_tbl
(FIRST_NAME, LAST_NAME, USERNAME, PSWD, DOB, GENDER, BLOOD_GROUP, PHONE, EMAIL, ADDRESS)
VALUES
('$first_name', '$last_name', '$username', '$pswd', '$dob', '$gender',
 '$blood_group', '$phone', '$email', '$address')
";

if (!mysqli_query($conn, $q)) {
    die(mysqli_error($conn));
}

/* ---------- SAVE PATIENT ID ---------- */
$_SESSION['P_ID'] = mysqli_insert_id($conn);

/* ---------- REDIRECT ---------- */
header("Location: payment.php?doctor_id=$doctor_id&date=$date&time=$time&schedule_id=$schedule_id");
exit;
