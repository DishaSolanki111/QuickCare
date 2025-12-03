<?php
@include 'config.php';
$doctor_id = $_GET['doc_id'] ?? 0;

// Fetch schedule 
$scheduleQuery = "
SELECT * FROM doctor_schedule_tbl
WHERE doctor_id = '$doctor_id'
";
$scheduleResult = mysqli_query($conn, $scheduleQuery);

$schedules = [];
while ($row = mysqli_fetch_assoc($scheduleResult)) {
    $schedules[$row['AVAILABLE_DAY']] = [
        'start' => $row['START_TIME'],
        'end'   => $row['END_TIME'],
        'schedule_id' => $row['SCHEDULE_ID']
    ];
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment</title>
    <style>
        /* OPTIONAL: You can add styling here later */
    </style>
</head>
<body>

<h1>Select Date</h1>

<div id="calendar">
    <div id="calendar"></div>

<script>
const availability = <?php echo json_encode($schedules); ?>;
</script>

<script>
function generateCalendar() {
    const calendar = document.getElementById("calendar");
    calendar.innerHTML = "";

    const today = new Date();
    today.setDate(1);

    const month = today.getMonth();
    const year = today.getFullYear();

    const firstDay = new Date(year, month, 1).getDay(); 
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    let table = "<table border='1' cellpadding='10'><tr>";

    const weekdays = ["SUN","MON","TUE","WED","THU","FRI","SAT"];
    table += weekdays.map(d => `<th>${d}</th>`).join("") + "</tr><tr>";

    for (let i = 0; i < firstDay; i++) {
        table += "<td></td>";
    }

    for (let day = 1; day <= daysInMonth; day++) {
        let d = new Date(year, month, day);
        let weekday = weekdays[d.getDay()];

        if (availability[weekday]) {
            // AVAILABLE
            table += `<td style='background:#b6ffb6;cursor:pointer' onclick="selectDate('${year}-${month+1}-${day}','${weekday}')">${day}</td>`;
        } else {
            // NOT AVAILABLE
            table += `<td style='background:#ddd'>${day}</td>`;
        }

        if (d.getDay() === 6) table += "</tr><tr>";
    }

    table += "</tr></table>";
    calendar.innerHTML = table;
}

generateCalendar();
</script>

</div>

<script>
   // calendar
   
<script>
function selectDate(date, weekday) {
    const info = availability[weekday];
    const start = info.start;
    const end = info.end;
    const schedule_id = info.schedule_id;

    fetch(`fetch_slots.php?doctor_id=<?php echo $doctor_id; ?>&date=${date}&start=${start}&end=${end}&schedule_id=${schedule_id}`)
    .then(res => res.text())
    .then(html => {
        document.getElementById("slots").innerHTML = html;
    });
}


<script>
function confirmSlot(date, time, schedule_id){
    if(confirm("Confirm appointment at " + time + " ?")){
        window.location.href = `confirm.php?doctor_id=<?php echo $doctor_id; ?>&date=${date}&time=${time}&schedule_id=${schedule_id}`;
    }
}
</script>

</body>
</html>
