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

/* Fetch booked days for the current month/year */
 $bookedQuery = mysqli_query($conn, "
    SELECT DISTINCT DATE(APPOINTMENT_DATE) as BOOKED_DATE
    FROM appointment_tbl
    WHERE DOCTOR_ID = $doctor_id 
    AND MONTH(APPOINTMENT_DATE) = $month 
    AND YEAR(APPOINTMENT_DATE) = $year
    AND STATUS != 'cancelled'
");

 $bookedDays = [];
while ($row = mysqli_fetch_assoc($bookedQuery)) {
    $bookedDays[] = $row['BOOKED_DATE'];
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
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 10px;
    background-color: #f5f5f5;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

.main-container {
    width: 500px;
    height: 500px;
    background-color: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.calendar-header a {
    text-decoration: none;
    font-size: 18px;
    font-weight: bold;
    color: #47bb7dff;
    padding: 5px 10px;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.calendar-header a:hover {
    background-color: rgba(71, 187, 125, 0.1);
}

.calendar-header strong {
    font-size: 18px;
    color: #333;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    grid-template-rows: repeat(7, 1fr);
    gap: 4px;
    flex-grow: 1;
}

.day-name {
    text-align: center;
    font-weight: bold;
    padding: 8px 0;
    color: #333;
    font-size: 14px;
    background-color: #f8f9fa;
    border-radius: 4px;
}

.date {
    padding: 5px;
    border-radius: 6px;
    text-align: center;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.2s;
}

.available {
    background: #47bb7dff;
    color: white;
    cursor: pointer;
}

.available:hover {
    background: #3a9466;
    transform: scale(1.05);
}

.booked {
    background: #ffcc00;
    color: #333;
    cursor: not-allowed;
}

.unavailable {
    background: #d9d9d9;
    color: #777;
    cursor: not-allowed;
}

.legend {
    display: flex;
    justify-content: center;
    margin-top: 12px;
    gap: 15px;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
}

.legend-color {
    width: 16px;
    height: 16px;
    border-radius: 4px;
}

#slotBox {
    margin-top: 15px;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 15px;
}

/* Responsive adjustments */
@media (max-width: 600px) {
    .main-container {
        width: 400px;
        height: 400px;
        padding: 10px;
    }
    
    .calendar-header a, .calendar-header strong {
        font-size: 16px;
    }
    
    .day-name {
        font-size: 12px;
        padding: 6px 0;
    }
    
    .date {
        font-size: 12px;
        padding: 4px;
    }
    
    .legend-item {
        font-size: 12px;
    }
}

@media (max-width: 450px) {
    .main-container {
        width: 350px;
        height: 350px;
    }
}
</style>
</head>
<body>

<div class="main-container">
    <h3 style="font-size: 18px; margin-bottom: 10px; text-align: center;">Select Date</h3>

    <!-- Month Navigation -->
    <div class="calendar-header">
        <a href="?doctor_id=<?= $doctor_id ?>&month=<?= $month-1 ?>&year=<?= $year ?>">◀</a>
        <strong><?= date('F Y', $firstDayOfMonth) ?></strong>
        <a href="?doctor_id=<?= $doctor_id ?>&month=<?= $month+1 ?>&year=<?= $year ?>">▶</a>
    </div>

    <!-- Legend -->
    <div class="legend">
        <div class="legend-item">
            <div class="legend-color" style="background:#47bb7dff;"></div>
            <span>Available</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background:#ffcc00;"></div>
            <span>Booked</span>
        </div>
        <div class="legend-item">
            <div class="legend-color" style="background:#d9d9d9;"></div>
            <span>Unavailable</span>
        </div>
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
            
            // Check if it's a booked day
            if (in_array($fullDate, $bookedDays)) {
                echo "<div class='date booked' title='Booked'>$d</div>";
            }
            // Check if it's an available day and not in the past
            else if (in_array($dayName, $availableDays) && $fullDate >= $today) {
                echo "<div class='date available' onclick=\"showSlots('$fullDate')\">$d</div>";
            }
            // Otherwise, it's unavailable
            else {
                echo "<div class='date unavailable'>$d</div>";
            }
        }
        ?>
    </div>

    <div id="slotBox"></div>
</div>

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