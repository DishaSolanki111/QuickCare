<?php
session_start();
include "config.php";

 $doctor_id = isset($_POST['doctor_id']) ? (int)$_POST['doctor_id'] : (int)$_GET['doctor_id'];

/* Month & Year */
 $month = isset($_POST['month']) ? (int)$_POST['month'] : (isset($_GET['month']) ? (int)$_GET['month'] : date('n'));
 $year  = isset($_POST['year'])  ? (int)$_POST['year']  : (isset($_GET['year']) ? (int)$_GET['year'] : date('Y'));

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
            /* Green color scheme */
            --primary-green: #2e7d32;
            --secondary-green: #4caf50;
            --light-green: #e8f5e9;
            --date-light-green: #c8e6c9;
            --medium-green: #81c784;
            --dark-green: #1b5e20;
            --accent-green: #388e3c;
            --selected-dark-green: #0d3d0f;
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
            background: linear-gradient(135deg, #f5faf5 0%, #e6f7e6 100%);
            min-height: 100vh;
            padding: 200px 20px 20px; /* Increased top padding to prevent header overlap */
            display: flex;
            justify-content: center;
            align-items: flex-start;
          
        }

        .main-container {
            width: 100%;
            max-width: 800px;
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            animation: fadeIn 0.5s ease-in-out;
          
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .calendar-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            color: white;
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
        }

        .calendar-header h3 {
            font-size: 1.6rem;
            font-weight: 600;
            margin: 0;
            letter-spacing: 0.5px;
        }

        .calendar-header .nav-button {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
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
            padding: 24px;
            flex-grow: 1;
        }

        .legend {
            display: flex;
            justify-content: center;
            margin-bottom: 24px;
            gap: 24px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .legend-color {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 10px;
            margin-bottom: 24px;
        }

        .day-name {
            text-align: center;
            font-weight: 600;
            padding: 12px 0;
            color: var(--primary-green);
            font-size: 14px;
            background-color: var(--light-green);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
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
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .available {
            background: var(--date-light-green);
            color: #333;
        }

        .available:hover {
            background: var(--medium-green);
            color: white;
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .booked {
            background: #ffcc00;
            color: #333;
            cursor: not-allowed;
            opacity: 0.8;
        }

        .unavailable {
            background: #e0e0e0;
            color: #777;
            cursor: not-allowed;
        }

        .today {
            border: 2px solid var(--primary-green);
            font-weight: 700;
        }

        .selected {
            background: var(--selected-dark-green) !important;
            color: white !important;
            border: 2px solid var(--dark-green);
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        #slotBox {
            margin-top: 24px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            display: none;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; max-height: 0; }
            to { opacity: 1; max-height: 500px; }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            body {
                padding-top: 160px; /* Adjusted for mobile */
            }
            
            .main-container {
                max-width: 100%;
            }
            
            .calendar-header {
                padding: 18px;
            }
            
            .calendar-header h3 {
                font-size: 1.3rem;
            }
            
            .calendar-content {
                padding: 18px;
            }
            
            .calendar-grid {
                gap: 6px;
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
            <form method="POST" action="calendar.php" style="display:inline">
            <input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">
            <input type="hidden" name="month" value="<?= $month-1 ?>">
            <input type="hidden" name="year" value="<?= $year ?>">
            <button type="submit" class="nav-button"><i class="fas fa-chevron-left"></i></button>
        </form>
            <h3><?= date('F Y', $firstDayOfMonth) ?></h3>
            <form method="POST" action="calendar.php" style="display:inline">
            <input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">
            <input type="hidden" name="month" value="<?= $month+1 ?>">
            <input type="hidden" name="year" value="<?= $year ?>">
            <button type="submit" class="nav-button"><i class="fas fa-chevron-right"></i></button>
        </form>
        </div>

        <div class="calendar-content">
            <!-- Legend -->
            <div class="legend">
                <div class="legend-item">
                    <div class="legend-color" style="background: var(--date-light-green);"></div>
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
                        echo "<div class='date available $isToday' onclick=\"selectDate(this, '$fullDate')\" title='Available'>$d</div>";
                    }
                    // Otherwise, it's unavailable
                    else {
                        echo "<div class='date unavailable $isToday' title='Unavailable'>$d</div>";
                    }
                }
                ?>
            </div>

            <div id="slotBox"></div>
        </div>
    </div>

    <script>
        // Store the currently selected date element
        let selectedDateElement = null;
        
        // Function to handle date selection
        function selectDate(element, fullDate) {
            // Remove the selected class from the previously selected date
            if (selectedDateElement) {
                selectedDateElement.classList.remove('selected');
            }
            
            // Add the selected class to the newly selected date
            element.classList.add('selected');
            selectedDateElement = element;
            
            // Show slots for the selected date
            showSlots(fullDate);
        }
        
        // Function to show slots
        function showSlots(fullDate) {
            fetch("slots.php?doctor_id=<?= $doctor_id ?>&date=" + fullDate)
                .then(res => res.text())
                .then(data => {
                    document.getElementById("slotBox").style.display = "block";
                    document.getElementById("slotBox").innerHTML = data;
                });
        }
        
        window.parent.postMessage("closeCalendar", "*");
    </script>
</body>
</html>