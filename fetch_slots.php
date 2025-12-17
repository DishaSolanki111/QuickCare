<?php
include "config.php";

$doctor_id = $_GET['doctor_id'];
$date = $_GET['date'];

$day = strtoupper(date('D', strtotime($date)));

$q = mysqli_query($conn,"
    SELECT START_TIME, END_TIME, SCHEDULE_ID
    FROM doctor_schedule_tbl
    WHERE DOCTOR_ID=$doctor_id AND AVAILABLE_DAY='$day'
");

if(mysqli_num_rows($q)==0){
    echo "<p>No slots available.</p>";
    exit;
}


$row = mysqli_fetch_assoc($q);

$start = strtotime($row['START_TIME']);
$end   = strtotime($row['END_TIME']);
$schedule_id = $row['SCHEDULE_ID'];

echo "<h4>Available Slots</h4>";

while($start < $end){
    $slot = date("H:i",$start);
    $start = strtotime("+1 hour",$start);

    echo "
    <div class='slot'
        onclick=\"confirmSlot('$slot','$schedule_id')\">
        $slot
    </div>";
}
?>

<script>
function confirmSlot(time,scheduleId){
    alert(
        "Selected Time: "+time+
        "\nSchedule ID: "+scheduleId+
        "\n(Next → OTP)"
    );
}
</script>
