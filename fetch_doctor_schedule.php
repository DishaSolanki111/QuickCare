<?php
// Database connection
include 'config.php';

// Get POST data
 $date = isset($_POST['date']) ? $_POST['date'] : date('Y-m-d');
 $specializationId = isset($_POST['specialization']) ? (int)$_POST['specialization'] : 0;

// Convert date to day of week (MON, TUE, etc.)
 $dayOfWeek = date('D', strtotime($date));
 $dayMap = [
    'Mon' => 'MON',
    'Tue' => 'TUE',
    'Wed' => 'WED',
    'Thu' => 'THUR',
    'Fri' => 'FRI',
    'Sat' => 'SAT',
    'Sun' => 'SUN'
];
 $dayName = $dayMap[$dayOfWeek];

// Prepare the main query to get doctors with their schedules
 $sql = "SELECT d.DOCTOR_ID, d.FIRST_NAME, d.LAST_NAME, d.PROFILE_IMAGE, 
               s.SPECIALISATION_NAME, ds.START_TIME, ds.END_TIME, ds.SCHEDULE_ID
        FROM doctor_tbl d
        JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
        LEFT JOIN doctor_schedule_tbl ds ON d.DOCTOR_ID = ds.DOCTOR_ID AND ds.AVAILABLE_DAY = ?
        WHERE (? = 0 OR d.SPECIALISATION_ID = ?)
        ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME, d.LAST_NAME";

 $stmt = $conn->prepare($sql);
 $stmt->bind_param("sii", $dayName, $specializationId, $specializationId);
 $stmt->execute();
 $result = $stmt->get_result();

 $doctors = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $doctorId = $row['DOCTOR_ID'];
        
        // Initialize doctor data if not exists
        if (!isset($doctors[$doctorId])) {
            $doctors[$doctorId] = [
                'doctor_id' => $row['DOCTOR_ID'],
                'first_name' => $row['FIRST_NAME'],
                'last_name' => $row['LAST_NAME'],
                'profile_image' => $row['PROFILE_IMAGE'],
                'specialization_name' => $row['SPECIALISATION_NAME'],
                'schedule' => [],
                'booked_slots' => []
            ];
        }
        
        // Add schedule if exists
        if ($row['SCHEDULE_ID']) {
            $doctors[$doctorId]['schedule'][] = [
                'schedule_id' => $row['SCHEDULE_ID'],
                'start_time' => $row['START_TIME'],
                'end_time' => $row['END_TIME'],
                'is_available' => true // Default to available, will check for leaves next
            ];
        }
    }
    
    // Check for doctor leaves (if doctor_leave_tbl exists)
    // Note: Since doctor_leave_tbl doesn't exist in the provided schema, we'll skip this part
    // In a real implementation, you would check if the doctor is on leave for the selected date
    
    // Get booked slots for each doctor
    foreach ($doctors as $doctorId => &$doctor) {
        // Get booked appointments for this doctor on the selected date
        $appointmentSql = "SELECT APPOINTMENT_TIME FROM appointment_tbl 
                          WHERE DOCTOR_ID = ? AND APPOINTMENT_DATE = ? AND STATUS = 'SCHEDULED'";
        $appointmentStmt = $conn->prepare($appointmentSql);
        $appointmentStmt->bind_param("is", $doctorId, $date);
        $appointmentStmt->execute();
        $appointmentResult = $appointmentStmt->get_result();
        
        $bookedSlots = [];
        if ($appointmentResult->num_rows > 0) {
            while ($appointmentRow = $appointmentResult->fetch_assoc()) {
                // Format time to HH:MM format
                $bookedSlots[] = date('H:i', strtotime($appointmentRow['APPOINTMENT_TIME']));
            }
        }
        
        $doctor['booked_slots'] = $bookedSlots;
        $appointmentStmt->close();
    }
}

// Convert to indexed array for JSON response
 $doctorsArray = array_values($doctors);

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'doctors' => $doctorsArray
]);

 $conn->close();
?>