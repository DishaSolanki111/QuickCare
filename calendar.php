<?php
include "config.php";

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
    // Convert THUR to THU to match date() function output
    $day = $row['AVAILABLE_DAY'] === 'THUR' ? 'THU' : $row['AVAILABLE_DAY'];
    $availableDays[] = strtoupper($day);
}

/* Fetch booked days for the current month/year */
 $bookedQuery = mysqli_query($conn, "
    SELECT DISTINCT DATE(APPOINTMENT_DATE) as BOOKED_DATE
    FROM appointment_tbl
    WHERE DOCTOR_ID = $doctor_id 
    AND MONTH(APPOINTMENT_DATE) = $month 
    AND YEAR(APPOINTMENT_DATE) = $year
    AND STATUS != 'CANCELLED'
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Date</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Blue color scheme */
            --primary-blue: #1a73e8;
            --secondary-blue: #4285f4;
            --light-blue: #e8f0fe;
            --medium-blue: #8ab4f8;
            --dark-blue: #174ea6;
            --accent-blue: #0b57d0;
            --text-dark: #202124;
            --text-light: #ffffff;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 10px 20px rgba(0, 0, 0, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f8ff 0%, #e6f0ff 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .main-container {
            width: 100%;
            max-width: 600px;
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .calendar-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .calendar-header h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .calendar-header .nav-button {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.2rem;
        }

        .calendar-header .nav-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .calendar-content {
            padding: 20px;
            flex-grow: 1;
        }

        .legend {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
        }

        .legend-color {
            width: 16px;
            height: 16px;
            border-radius: 4px;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 20px;
        }

        .day-name {
            text-align: center;
            font-weight: 600;
            padding: 10px 0;
            color: var(--primary-blue);
            font-size: 14px;
            background-color: var(--light-blue);
            border-radius: 8px;
        }

        .date {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            font-weight: 500;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .available {
            background: var(--primary-blue);
            color: white;
        }

        .available:hover {
            background: var(--accent-blue);
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .booked {
            background: #ffcc00;
            color: #333;
            cursor: not-allowed;
        }

        .unavailable {
            background: #e0e0e0;
            color: #777;
            cursor: not-allowed;
        }

        .today {
            border: 2px solid var(--primary-blue);
        }

        #slotBox {
            margin-top: 20px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: none;
        }

        /* Responsive adjustments */
        @media (max-width: 600px) {
            .main-container {
                max-width: 100%;
            }
            
            .calendar-header {
                padding: 15px;
            }
            
            .calendar-header h3 {
                font-size: 1.2rem;
            }
            
            .calendar-content {
                padding: 15px;
            }
            
            .calendar-grid {
                gap: 5px;
            }
            
            .day-name {
                font-size: 12px;
                padding: 8px 0;
            }
            
            .date {
                font-size: 14px;
            }
            
            .legend-item {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="calendar-header">
            <a href="?doctor_id=<?= $doctor_id ?>&month=<?= $month-1 ?>&year=<?= $year ?>" class="nav-button">
                <i class="fas fa-chevron-left"></i>
            </a>
            <h3><?= date('F Y', $firstDayOfMonth) ?></h3>
            <a href="?doctor_id=<?= $doctor_id ?>&month=<?= $month+1 ?>&year=<?= $year ?>" class="nav-button">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>

        <div class="calendar-content">
            <!-- Legend -->
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color" style="background: var(--primary-blue);"></div>
                    <span>Available</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #ffcc00;"></div>
                    <span>Booked</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: #e0e0e0;"></div>
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
                    
                    // Check if it's today
                    $isToday = ($fullDate === $today) ? 'today' : '';
                    
                    // Check if it's a booked day
                    if (in_array($fullDate, $bookedDays)) {
                        echo "<div class='date booked $isToday' title='Booked'>$d</div>";
                    }
                    // Check if it's an available day and not in the past
                    else if (in_array($dayName, $availableDays) && $fullDate >= $today) {
                        echo "<div class='date available $isToday' onclick=\"showSlots('$fullDate')\">$d</div>";
                    }
                    // Otherwise, it's unavailable
                    else {
                        echo "<div class='date unavailable $isToday'>$d</div>";
                    }
                }
                ?>
            </div>

            <div id="slotBox"></div>
        </div>
    </div>

    <script>
        window.parent.postMessage("closeCalendar", "*");
    </script>

    <script>
        function showSlots(fullDate) {
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