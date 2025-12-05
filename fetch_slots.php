<?php
@include 'config.php';

$doctor_id   = $_GET['doctor_id'] ?? 0;
$date        = $_GET['date'] ?? '';
$start_time  = $_GET['start'] ?? '';
$end_time    = $_GET['end'] ?? '';
$schedule_id = $_GET['schedule_id'] ?? 0;

if (!$doctor_id || !$date || !$start_time || !$end_time || !$schedule_id) {
    echo "<p>Error: Missing parameters.</p>";
    exit;
}

// -------------------------------
// STEP 1: Convert times to DateTime
// -------------------------------
$start = new DateTime($start_time);
$end   = new DateTime($end_time);

// -------------------------------
// STEP 2: Fetch booked slots for this doctor on this date
// -------------------------------
$booked = [];

$aptQuery = "
    SELECT appointment_time 
    FROM appointment_tbl
    WHERE doctor_id = '$doctor_id'
      AND appointment_date = '$date'
      AND status != 'cancelled'
";
$aptRes = mysqli_query($conn, $aptQuery);

while ($row = mysqli_fetch_assoc($aptRes)) {
    $booked[] = $row['appointment_time'];
}

// -------------------------------
// STEP 3: Generate 1-hour slots
// -------------------------------
$slots = [];
$curr = clone $start;

while ($curr < $end) {
    $next = clone $curr;
    $next->modify('+1 hour');

    if ($next > $end) break;

    $slot_time = $curr->format("H:i");

    $slots[] = [
        'time' => $slot_time,
        'available' => !in_array($slot_time, $booked)
    ];

    $curr->modify('+1 hour');
}

// -------------------------------
// STEP 4: Output slot cards (HTML)
// -------------------------------
echo "<h2>Available Slots for $date</h2>";

echo "<div style='display:flex; flex-wrap:wrap; gap:10px;'>";

foreach ($slots as $slot) {

    if ($slot['available']) {
        echo "
        <div onclick=\"confirmSlot('$date', '{$slot['time']}', '$schedule_id')\"
             style='padding:15px 20px; background:#b6ffb6; cursor:pointer; border-radius:10px;'>
            {$slot['time']}
        </div>";
    } else {
        echo "
        <div style='padding:15px 20px; background:#ccc; color:#555; border-radius:10px;'>
            {$slot['time']}
        </div>";
    }
}

echo "</div>";
?>
