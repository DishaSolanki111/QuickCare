<?php
session_start();
include 'config.php';

$doctor_id   = intval($_GET['doctor_id']);
$date        = $_GET['date'];
$schedule_id = intval($_GET['schedule_id']);

/* 1️⃣ Fetch schedule time */
$q = mysqli_query($conn,"
    SELECT START_TIME, END_TIME 
    FROM doctor_schedule_tbl 
    WHERE SCHEDULE_ID = $schedule_id
");
$s = mysqli_fetch_assoc($q);

$start = strtotime($s['START_TIME']);
$end   = strtotime($s['END_TIME']);

/* 2️⃣ Fetch already booked slots */
$booked = [];
$bq = mysqli_query($conn,"
    SELECT appointment_time 
    FROM appointment_tbl 
    WHERE doctor_id = $doctor_id 
    AND appointment_date = '$date'
    AND status = 'scheduled'
");
while($r = mysqli_fetch_assoc($bq)){
    $booked[] = substr($r['appointment_time'],0,5);
}

/* 3️⃣ Generate 1-hour slots */
while($start < $end){
    $slot = date("H:i",$start);

    if(in_array($slot,$booked)){
        echo "<div class='slot disabled'>$slot (Booked)</div>";
    }else{
        echo "<div class='slot'
              onclick=\"confirmSlot('$date','$slot',$schedule_id)\">
              $slot</div>";
    }

    $start = strtotime("+1 hour",$start);
}
