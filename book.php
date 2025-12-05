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
</head>
<body>

<h1>Select Date</h1>

<div id="calendar"></div>
<br>
<div id="slots"></div>

<script>
const availability = <?php echo json_encode($schedules); ?>;

// -------------------- GENERATE CALENDAR --------------------
function generateCalendar() {
    const cal = document.getElementById("calendar");
    cal.innerHTML = "";

    const today = new Date();
    today.setDate(1);

    const month = today.getMonth();
    const year = today.getFullYear();

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    const weekdays = ["SUN","MON","TUE","WED","THU","FRI","SAT"];

    let html = "<table border='1' cellpadding='10'><tr>";
    html += weekdays.map(w => `<th>${w}</th>`).join("");
    html += "</tr><tr>";

    for (let i = 0; i < firstDay; i++) html += "<td></td>";

    for (let day = 1; day <= daysInMonth; day++) {
        let d = new Date(year, month, day);
        let weekday = weekdays[d.getDay()];
        let dateStr = `${year}-${month+1}-${day}`;

        if (availability[weekday]) {
            html += `<td style='background:#b6ffb6;cursor:pointer'
                     onclick="selectDate('${dateStr}','${weekday}')">${day}</td>`;
        } else {
            html += `<td style='background:#ddd'>${day}</td>`;
        }

        if (d.getDay() === 6) html += "</tr><tr>";
    }

    html += "</tr></table>";
    cal.innerHTML = html;
}

generateCalendar();

// -------------------- SELECT DATE --------------------
function selectDate(date, weekday) {
    const info = availability[weekday];

    fetch(`fetch_slots.php?doctor_id=<?php echo $doctor_id; ?>&date=${date}&start=${info.start}&end=${info.end}&schedule_id=${info.schedule_id}`)
    .then(res => res.text())
    .then(html => {
        document.getElementById("slots").innerHTML = html;
    });
}

// -------------------- CONFIRM SLOT --------------------
function confirmSlot(date, time, schedule_id){
    if(confirm("Confirm appointment at " + time + " ?")){
        window.location.href = `confirm.php?doctor_id=<?php echo $doctor_id; ?>&date=${date}&time=${time}&schedule_id=${schedule_id}`;
    }
}
</script>

</body>
</html>
