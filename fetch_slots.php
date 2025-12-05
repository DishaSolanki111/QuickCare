<?php
session_start();
@include 'config.php';

$doctor_id   = $_GET['doctor_id'];
$date        = $_GET['date'];
$start       = $_GET['start'];
$end         = $_GET['end'];
$schedule_id = $_GET['schedule_id'];

$startT = new DateTime($start);
$endT   = new DateTime($end);

$booked = [];
$q = "SELECT appointment_time FROM appointment_tbl 
      WHERE doctor_id='$doctor_id' 
      AND appointment_date='$date' 
      AND status!='cancelled'";
$res = mysqli_query($conn, $q);
while($r = mysqli_fetch_assoc($res)){
    $booked[] = $r['appointment_time'];
}

echo "<h3>Slots for $date</h3>";
echo "<div style='display:flex;gap:10px;flex-wrap:wrap;'>";

while($startT < $endT){
    $slot = $startT->format("H:i");
    $startT->modify("+1 hour");

    if(in_array($slot, $booked)){
        echo "<div style='background:#ccc;padding:10px 15px;border-radius:6px;'>$slot</div>";
    } else {
        echo "<div onclick=\"confirmSlot('$date','$slot','$schedule_id')\" 
                   style='background:#b6ffb6;padding:10px 15px;border-radius:6px;cursor:pointer;'>$slot</div>";
    }
}
echo "</div>";
?>
