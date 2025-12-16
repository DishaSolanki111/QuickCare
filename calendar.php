<?php
include 'config.php';

if (!isset($_GET['doctor_id'])) {
    die("Doctor ID missing");
}

$doctor_id = intval($_GET['doctor_id']);

/* 1️⃣ Fetch doctor available days */
$days = [];
$q = mysqli_query($conn, "
    SELECT AVAILABLE_DAY 
    FROM doctor_schedule_tbl 
    WHERE DOCTOR_ID = $doctor_id
");

while ($row = mysqli_fetch_assoc($q)) {
    $days[] = $row['AVAILABLE_DAY']; // MON, TUE...
}

/* 2️⃣ Month setup */
$month = date('m');
$year  = date('Y');
$firstDay = mktime(0,0,0,$month,1,$year);
$totalDays = date('t', $firstDay);
$startDay = date('N', $firstDay); // 1 (Mon) - 7 (Sun)

/* Map PHP day to DB format */
$dayMap = [
    1 => 'MON',
    2 => 'TUE',
    3 => 'WED',
    4 => 'THU',
    5 => 'FRI',
    6 => 'SAT',
    7 => 'SUN'
];
?>
<!DOCTYPE html>
<html>
<head>
<title>Doctor Availability</title>
<style>
body {
    font-family: Arial;
    background: #f5f8ff;
    display: flex;
    justify-content: center;
    padding: 40px;
}
.calendar {
    background: white;
    padding: 20px;
    border-radius: 12px;
    width: 420px;
}
.calendar h2 {
    text-align: center;
    color: #1a3c6e;
}
.grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
    margin-top: 20px;
}
.day {
    font-weight: bold;
    text-align: center;
}
.date {
    padding: 12px;
    text-align: center;
    border-radius: 8px;
}
.available {
    background: #3cb371;
    color: white;
    cursor: pointer;
}
.unavailable {
    background: #d3d3d3;
    color: #666;
}
.available:hover {
    background: #2e8b57;
}
</style>
</head>
<body>

<div class="calendar">
    <h2><?php echo date('F Y'); ?></h2>

    <div class="grid">
        <div class="day">Mon</div>
        <div class="day">Tue</div>
        <div class="day">Wed</div>
        <div class="day">Thu</div>
        <div class="day">Fri</div>
        <div class="day">Sat</div>
        <div class="day">Sun</div>

        <?php
        // Empty cells before first date
        for ($i = 1; $i < $startDay; $i++) {
            echo "<div></div>";
        }

        // Dates
        for ($d = 1; $d <= $totalDays; $d++) {
            $dayNum = date('N', mktime(0,0,0,$month,$d,$year));
            $dbDay = $dayMap[$dayNum];

            if (in_array($dbDay, $days)) {
                echo "<div class='date available'>$d</div>";
            } else {
                echo "<div class='date unavailable'>$d</div>";
            }
        }
        ?>
    </div>
</div>

</body>
</html>
