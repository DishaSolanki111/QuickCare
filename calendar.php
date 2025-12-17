<?php
include 'config.php';

$doctor_id = (int)$_GET['doctor_id'];

/* Fetch schedule */
$q = mysqli_query($conn,"
    SELECT SCHEDULE_ID, AVAILABLE_DAY, START_TIME, END_TIME
    FROM doctor_schedule_tbl
    WHERE DOCTOR_ID = $doctor_id
");

$schedule = mysqli_fetch_assoc($q);

/* Month setup */
$month = date('m');
$year  = date('Y');
$totalDays = date('t');
$dayMap = ['SUN','MON','TUE','WED','THU','FRI','SAT'];
?>
<!DOCTYPE html>
<html>
<head>
<style>
.date{padding:10px;border-radius:6px;text-align:center}
.available{background:green;color:white;cursor:pointer}
.unavailable{background:#ccc}
#slotBox{display:none;margin-top:20px}
.slot{padding:8px;margin:5px;background:#2e6ad6;color:white;cursor:pointer}
</style>
</head>
<body>

<h3>Select Date</h3>

<div style="display:grid;grid-template-columns:repeat(7,1fr);gap:10px">
<?php
for($d=1;$d<=$totalDays;$d++){

    $fullDate = sprintf('%04d-%02d-%02d', $year, $month, $d);
    $dayName  = strtoupper($dayMap[date('w', strtotime($fullDate))]);

    if($dayName === $schedule['AVAILABLE_DAY']){
        echo "<div class='date available'
              onclick=\"showSlots('$fullDate')\">$d</div>";
    } else {
        echo "<div class='date unavailable'>$d</div>";
    }
}
?>
</div>

<div id="slotBox"></div>

<script>
function showSlots(fullDate){
    fetch("slots.php?doctor_id=<?= $doctor_id ?>&date=" + fullDate)
    .then(res => res.text())
    .then(data => {
        document.getElementById("slotBox").style.display = "block";
        document.getElementById("slotBox").innerHTML = data;
    });
}
</script>

</body>
</html>
