<?php
include 'config.php';

header('Content-Type: application/json');

$doctor_id = $_POST['doctor_id'] ?? '';
$month     = isset($_POST['month']) ? (int)$_POST['month'] : null; // 1-12
$year      = isset($_POST['year']) ? (int)$_POST['year'] : null;

if (empty($doctor_id) || !$month || !$year) {
    echo json_encode(['status' => 'error', 'message' => 'Doctor ID, month and year are required']);
    exit;
}

// Normalize month to 1-12
if ($month < 1 || $month > 12) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid month']);
    exit;
}

$fullyBookedDates = [];

// For each day in the given month, check if all slots are booked
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

for ($day = 1; $day <= $daysInMonth; $day++) {
    $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);

    // Only consider dates from today up to +1 month (same constraint as UI)
    $today    = new DateTime('today');
    $maxDate  = (new DateTime('today'))->modify('+1 month');
    $current  = new DateTime($dateStr);
    if ($current < $today || $current > $maxDate) {
        continue;
    }

    // Find schedule for that weekday
    $dayOfWeek = date('D', strtotime($dateStr));
    $scheduleQuery = "
        SELECT START_TIME, END_TIME 
        FROM doctor_schedule_tbl 
        WHERE DOCTOR_ID = '" . mysqli_real_escape_string($conn, $doctor_id) . "'
          AND AVAILABLE_DAY = '" . mysqli_real_escape_string($conn, $dayOfWeek) . "'
    ";
    $scheduleResult = mysqli_query($conn, $scheduleQuery);
    if (!$scheduleResult || mysqli_num_rows($scheduleResult) === 0) {
        // Doctor not available this day
        continue;
    }

    $scheduleRow = mysqli_fetch_assoc($scheduleResult);
    $start_time = strtotime($scheduleRow['START_TIME']);
    $end_time   = strtotime($scheduleRow['END_TIME']);

    // Build all theoretical slots (same logic as get_time_slots.php)
    $allSlots = [];
    while ($start_time < $end_time) {
        $allSlots[] = date('H:i', $start_time);
        $start_time = strtotime('+1 hour', $start_time);
    }

    if (empty($allSlots)) {
        continue;
    }

    // Fetch booked appointment times for this date
    $apptQuery = "
        SELECT APPOINTMENT_TIME 
        FROM appointment_tbl 
        WHERE DOCTOR_ID = '" . mysqli_real_escape_string($conn, $doctor_id) . "'
          AND APPOINTMENT_DATE = '" . mysqli_real_escape_string($conn, $dateStr) . "'
          AND STATUS IN ('SCHEDULED','COMPLETED')
    ";
    $apptResult = mysqli_query($conn, $apptQuery);
    $bookedTimes = [];
    if ($apptResult && mysqli_num_rows($apptResult) > 0) {
        while ($row = mysqli_fetch_assoc($apptResult)) {
            // Assume APPOINTMENT_TIME is stored as HH:MM:SS, normalize to HH:MM
            $time = substr($row['APPOINTMENT_TIME'], 0, 5);
            $bookedTimes[$time] = true;
        }
    }

    // Check if every slot is booked
    $allBooked = true;
    foreach ($allSlots as $slot) {
        if (empty($bookedTimes[$slot])) {
            $allBooked = false;
            break;
        }
    }

    if ($allBooked) {
        $fullyBookedDates[] = $dateStr;
    }
}

echo json_encode([
    'status' => 'success',
    'fully_booked_dates' => $fullyBookedDates
]);
?>

