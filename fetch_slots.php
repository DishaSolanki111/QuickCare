<?php
@include 'config.php';

$doctor_id = $_GET['doctor_id'];
$date = $_GET['date'];
$start = $_GET['start'];
$end = $_GET['end'];
$schedule_id = $_GET['schedule_id'];

function createSlots($start, $end) {
    $slots = [];
    $st = strtotime($start);
    $en = strtotime($end);

    while ($st < $en) {
        $slotStart = date("H:i", $st);
        $slotEnd   = date("H:i", $st + 3600);
        $slots[] = "$slotStart - $slotEnd";
        $st += 3600;
    }
    return $slots;
}

$slots = createSlots($start, $end);

// Fetch booked slots
$bookedQ = "
SELECT appointment_time FROM appointment_tbl
WHERE doctor_id='$doctor_id' AND appointment_date='$date' AND status='scheduled'
";
$bookedR = mysqli_query($conn, $bookedQ);

$booked = [];
while($b = mysqli_fetch_assoc($bookedR)) {
    $booked[] = $b['appointment_time'];
}

$output = "<h2>Select Time Slot</h2>";

foreach($slots as $slot){
    $time = explode(" - ", $slot)[0];

    if(in_array($time, $booked)){
        $output .= "<button disabled style='background:#ccc;margin:5px'>$slot</button>";
    } else {
        $output .= "<button onclick='confirmSlot(\"$date\",\"$time\",$schedule_id)' style='background:#b6d4ff;margin:5px'>$slot</button>";
    }
}

echo $output;
