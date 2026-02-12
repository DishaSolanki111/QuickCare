<?php
include "config.php";

if(isset($_POST['user_type']) && isset($_POST['user_id'])){

    $user_type = $_POST['user_type'];
    $user_id = $_POST['user_id'];

    if($user_type == "patient"){
        $query = mysqli_query($conn,"SELECT * FROM patient_tbl WHERE PATIENT_ID='$user_id'");
    }
    elseif($user_type == "doctor"){
        $query = mysqli_query($conn,"SELECT * FROM doctor_tbl WHERE DOCTOR_ID='$user_id'");
    }
    elseif($user_type == "receptionist"){
        $query = mysqli_query($conn,"SELECT * FROM receptionist_tbl WHERE RECEPTIONIST_ID='$user_id'");
    }

    $data = mysqli_fetch_assoc($query);

    echo json_encode($data);
}
?>
