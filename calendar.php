<?php
include 'config.php';

$doctor_id = intval($_GET['doctor_id']);

/* Fetch available days + time */
$q = mysqli_query($conn, "
    SELECT AVAILABLE_DAY, START_TIME, END_TIME, SCHEDULE_ID
    FROM doctor_schedule_tbl
    WHERE DOCTOR_ID = $doctor_id
");

$schedule = [];
while($r = mysqli_fetch_assoc($q)){
    $schedule[$r['AVAILABLE_DAY']] = $r;
}

/* Month setup */
$month = date('m');
$year  = date('Y');
$totalDays = date('t');
$startDay = date('N', strtotime("$year-$month-01"));

$dayMap = [1=>'MON',2=>'TUE',3=>'WED',4=>'THU',5=>'FRI',6=>'SAT',7=>'SUN'];
?>
<!DOCTYPE html>
<html>
<head>
<title>Calendar</title>

<style>
body{font-family:Arial;background:#f5f8ff;padding:20px;}
.calendar{background:#fff;padding:20px;border-radius:12px;}
.grid{display:grid;grid-template-columns:repeat(7,1fr);gap:8px;}
.day{font-weight:bold;text-align:center;}
.date{padding:12px;text-align:center;border-radius:6px;}
.available{background:#3cb371;color:#fff;cursor:pointer;}
.unavailable{background:#ddd;color:#777;}
/* SLOT POPUP */
#slotBox{
    display:none;
    position:fixed;
    top:0;left:0;width:100%;height:100%;
    background:rgba(0,0,0,.6);
    justify-content:center;
    align-items:center;
}
#slotContent{
    background:#fff;
    padding:20px;
    width:300px;
    border-radius:10px;
}
.slot{
    background:#2e6ad6;
    color:#fff;
    padding:10px;
    margin:6px 0;
    border-radius:6px;
    cursor:pointer;
    text-align:center;
}
</style>
</head>

<body>

<div class="calendar">
<h3><?php echo date('F Y'); ?></h3>

<div class="grid">
<?php
// Day headers
foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d)
    echo "<div class='day'>$d</div>";

// Empty cells
for($i=1;$i<$startDay;$i++) echo "<div></div>";

// Dates
for($d=1;$d<=$totalDays;$d++){
    $dayNum = date('N', strtotime("$year-$month-$d"));
    $dbDay = $dayMap[$dayNum];

    if(isset($schedule[$dbDay])){
        echo "<div class='date available'
              onclick=\"openSlots('$dbDay','$d')\">$d</div>";
    }else{
        echo "<div class='date unavailable'>$d</div>";
    }
}
?>
</div>
</div>

<!-- SLOT POPUP -->
<div id="slotBox">
    <div id="slotContent">
        <h4>Available Slots</h4>
        <div id="slots"></div>
        <button onclick="closeSlots()">Close</button>
    </div>
</div>

<script>
const schedule = <?php echo json_encode($schedule); ?>;

function openSlots(day,date){
    let s = schedule[day];
    let start = s.START_TIME.substring(0,5);
    let end   = s.END_TIME.substring(0,5);

    let slotsDiv = document.getElementById("slots");
    slotsDiv.innerHTML = "";

    let [sh,sm] = start.split(":").map(Number);
    let [eh,em] = end.split(":").map(Number);

    let t = sh;

    while(t < eh){
        let slot = (t<10?"0":"")+t+":00";
        slotsDiv.innerHTML += `
            <div class="slot"
                onclick="confirmSlot('${date}','${slot}','${s.SCHEDULE_ID}')">
                ${slot}
            </div>`;
        t++;
    }

    document.getElementById("slotBox").style.display="flex";
}

function closeSlots(){
    document.getElementById("slotBox").style.display="none";
}

function confirmSlot(date,time,scheduleId){
    alert(
        "Date: "+date+
        "\nTime: "+time+
        "\nSchedule ID: "+scheduleId+
    );
}
</script>

</body>
</html>
