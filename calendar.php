<?php
include 'config.php';

$doctor_id = intval($_GET['doctor_id']);

/* Fetch schedule */
$schedule = [];
$q = mysqli_query($conn,"
    SELECT AVAILABLE_DAY, START_TIME, END_TIME 
    FROM doctor_schedule_tbl 
    WHERE DOCTOR_ID = $doctor_id
");
while($r = mysqli_fetch_assoc($q)){
    $schedule[$r['AVAILABLE_DAY']] = $r;
}

/* Calendar setup */
$month = date('m');
$year  = date('Y');
$totalDays = date('t');
$firstDay = date('N', strtotime("$year-$month-01"));

$map = [
    1=>'MON',2=>'TUE',3=>'WED',
    4=>'THU',5=>'FRI',6=>'SAT',7=>'SUN'
];
?>
<!DOCTYPE html>
<html>
<head>
<title>Calendar</title>
<style>
body{font-family:Arial;background:#f5f8ff;padding:20px}
.calendar{background:white;padding:20px;border-radius:12px}
.grid{display:grid;grid-template-columns:repeat(7,1fr);gap:8px}
.day{font-weight:bold;text-align:center}
.date{padding:12px;text-align:center;border-radius:8px}
.available{background:#3cb371;color:white;cursor:pointer}
.unavailable{background:#ccc;color:#666}
#slots{margin-top:20px}
.slot{padding:10px;margin:6px 0;background:#2e6ad6;color:white;border-radius:6px}
</style>
</head>
<body>

<div class="calendar">
<h3><?php echo date('F Y'); ?></h3>

<div class="grid">
<?php foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $d) echo "<div class='day'>$d</div>"; ?>

<?php
for($i=1;$i<$firstDay;$i++) echo "<div></div>";

for($d=1;$d<=$totalDays;$d++){
    $dayNum = date('N', strtotime("$year-$month-$d"));
    $dbDay  = $map[$dayNum];

    if(isset($schedule[$dbDay])){
        echo "<div class='date available'
              onclick=\"loadSlots('$dbDay','$year-$month-$d')\">$d</div>";
    }else{
        echo "<div class='date unavailable'>$d</div>";
    }
}
?>
</div>

<div id="slots"></div>
</div>

<script>
const schedule = <?php echo json_encode($schedule); ?>;

function loadSlots(day,date){
    let s = schedule[day];
    let html = "<h4>Available Slots for "+date+"</h4>";

    let start = s.START_TIME.split(':')[0];
    let end   = s.END_TIME.split(':')[0];

    for(let h=parseInt(start); h<parseInt(end); h++){
        let time = (h<10?'0':'')+h+":00";
        html += `<div class="slot" onclick="selectSlot('${date}','${time}')">
        ${time}
        </div>`;
    }

    document.getElementById("slots").innerHTML = html;
}
</script>
<script>
let selectedDate = "";
let selectedTime = "";

function selectSlot(date, time){
    selectedDate = date;
    selectedTime = time;

    // highlight selected slot
    document.querySelectorAll(".slot").forEach(s => {
        s.style.background = "#2e6ad6";
    });

    event.target.style.background = "#1b4fb5";

    document.getElementById("confirmBox").style.display = "block";
}
</script>
<div id="confirmBox" style="display:none; margin-top:20px;">
    <button onclick="confirmSlot()"
        style="
            padding:12px 20px;
            background:#28a745;
            color:white;
            border:none;
            border-radius:8px;
            font-size:16px;
            cursor:pointer;
        ">
        Confirm Slot
    </button>
</div>
<script>
function confirmSlot(){
    if(!selectedDate || !selectedTime){
        alert("Select a slot first");
        return;
    }

    window.location.href =
        "confirm_booking.php?doctor_id=<?php echo $doctor_id; ?>" +
        "&date=" + selectedDate +
        "&time=" + selectedTime;
}
</script>

</body>
</html>
