<?php
session_start();
include 'config.php';

mysqli_query($conn,"
INSERT INTO patient_tbl
(FIRST_NAME,LAST_NAME,USERNAME,PSW,DOB,GENDER,BLOOD_GROUP,DIABETES,PHONE,EMAIL,ADDRESS)
VALUES
('{$_POST['FIRST_NAME']}','{$_POST['LAST_NAME']}','{$_POST['USERNAME']}',
'{$_POST['PSW']}','{$_POST['DOB']}','{$_POST['GENDER']}',
'{$_POST['BLOOD_GROUP']}','{$_POST['DIABETES']}',
'{$_POST['PHONE']}','{$_POST['EMAIL']}','{$_POST['ADDRESS']}')
");

$_SESSION['P_ID'] = mysqli_insert_id($conn);

header("Location: payment.php?doctor_id={$_POST['doctor_id']}&date={$_POST['date']}&time={$_POST['time']}&schedule_id={$_POST['schedule_id']}");

