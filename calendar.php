<?php
include 'config.php';

$doctor_id = (int)$_GET['doctor_id'];

/* Month & Year */
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year  = isset($_GET['year'])  ? (int)$_GET['year']  : date('Y');

/* Normalize month overflow */
if ($month < 1) { $month = 12; $year--; }
if ($month > 12) { $month = 1; $year++; }

/* Fetch ALL doctor schedules */
$q = mysqli_query($conn, "
    SELECT AVAILABLE_DAY
    FROM doctor_schedule_tbl
    WHERE DOCTOR_ID = $doctor_id
");

/* Store available days */
$availableDays = [];
while ($row = mysqli_fetch_assoc($q)) {
    $availableDays[] = strtoupper($row['AVAILABLE_DAY']);
}

/* Calendar calculations */
$firstDayOfMonth = strtotime("$year-$month-01");
$totalDays = date('t', $firstDayOfMonth);
$startWeekDay = date('w', $firstDayOfMonth); // 0 = SUN
$dayMap = ['SUN','MON','TUE','WED','THU','FRI','SAT'];
$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html>
<head>
<title>Select Date</title>
<style>
body{font-family:Arial;margin:0}
.calendar-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}
.calendar-header a{
    text-decoration:none;
    font-size:20px;
    font-weight:bold;
}
.calendar-grid{
    display:grid;
    grid-template-columns:repeat(7,1fr);
    gap:10px;
}
.day-name{
    text-align:center;
    font-weight:bold;
}
.date{
    padding:10px;
    border-radius:6px;
    text-align:center;
}
.available{
    background:#1fa739;
    color:white;
    cursor:pointer;
}
.available:hover{background:#17852d}
.unavailable{
    background:#d9d9d9;
    color:#777;
}
#slotBox{
    display:none;
    margin-top:20px;
}
</style>
</head>
<body>

<h3>Select Date</h3>

<!-- Month Navigation -->
<div class="calendar-header">
    <a href="?doctor_id=<?= $doctor_id ?>&month=<?= $month-1 ?>&year=<?= $year ?>">◀</a>
    <strong><?= date('F Y', $firstDayOfMonth) ?></strong>
    <a href="?doctor_id=<?= $doctor_id ?>&month=<?= $month+1 ?>&year=<?= $year ?>">▶</a>
</div>

<div class="calendar-grid">
<?php foreach ($dayMap as $day): ?>
    <div class="day-name"><?= $day ?></div>
<?php endforeach; ?>

<?php
/* Empty slots before month starts */
for ($i = 0; $i < $startWeekDay; $i++) {
    echo "<div></div>";
}

/* Dates */
for ($d = 1; $d <= $totalDays; $d++) {

    $fullDate = sprintf('%04d-%02d-%02d', $year, $month, $d);
    $dayName  = strtoupper($dayMap[date('w', strtotime($fullDate))]);

    if (in_array($dayName, $availableDays) && $fullDate >= $today) {
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
