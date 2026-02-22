<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['PATIENT_ID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

 $patient_id = $_SESSION['PATIENT_ID'];
 $current_time = date('H:i:s');
 $current_date = date('Y-m-d');

// Get reminders for today that haven't been shown yet
 $query = "SELECT ar.REMARKS, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, d.FIRST_NAME, d.LAST_NAME
          FROM appointment_reminder_tbl ar
          JOIN appointment_tbl a ON ar.APPOINTMENT_ID = a.APPOINTMENT_ID
          JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
          WHERE a.PATIENT_ID = $patient_id
          AND a.APPOINTMENT_DATE >= '$current_date'
          AND ar.REMINDER_TIME <= '$current_time'
          AND ar.REMINDER_TIME > DATE_SUB('$current_time', INTERVAL 1 HOUR)
          ORDER BY ar.REMINDER_TIME DESC
          LIMIT 5";

 $result = mysqli_query($conn, $query);

 $reminders = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reminders[] = [
            'message' => $row['REMARKS'],
            'date' => $row['APPOINTMENT_DATE'],
            'time' => $row['APPOINTMENT_TIME'],
            'doctor' => 'Dr. ' . $row['FIRST_NAME'] . ' ' . $row['LAST_NAME'],
            'type' => 'appointment'
        ];
    }
}

// Medicine reminders: today between START_DATE and END_DATE, REMINDER_TIME in last hour
$med_query = "SELECT mr.REMARKS, mr.REMINDER_TIME, m.MED_NAME
    FROM medicine_reminder_tbl mr
    JOIN medicine_tbl m ON mr.MEDICINE_ID = m.MEDICINE_ID
    WHERE mr.PATIENT_ID = $patient_id
    AND '$current_date' BETWEEN mr.START_DATE AND mr.END_DATE
    AND mr.REMINDER_TIME <= '$current_time'
    AND mr.REMINDER_TIME > DATE_SUB('$current_time', INTERVAL 1 HOUR)
    ORDER BY mr.REMINDER_TIME DESC
    LIMIT 5";
$med_result = mysqli_query($conn, $med_query);
if ($med_result) {
    while ($row = mysqli_fetch_assoc($med_result)) {
        $msg = 'Medicine Reminder: Take ' . $row['MED_NAME'];
        if (!empty(trim($row['REMARKS']))) {
            $msg .= ' - ' . $row['REMARKS'];
        }
        $reminders[] = [
            'message' => $msg,
            'date' => $current_date,
            'time' => $row['REMINDER_TIME'],
            'doctor' => '',
            'type' => 'medicine'
        ];
    }
}

// Cancellation notifications (medicine_reminder_tbl with [CANCELLED] prefix)
$cn_query = @mysqli_query($conn, "SELECT REMARKS, START_DATE, REMINDER_TIME FROM medicine_reminder_tbl WHERE PATIENT_ID = $patient_id AND REMARKS LIKE '[CANCELLED]%' ORDER BY START_DATE DESC, REMINDER_TIME DESC LIMIT 5");
if ($cn_query && mysqli_num_rows($cn_query) > 0) {
    while ($row = mysqli_fetch_assoc($cn_query)) {
        $reminders[] = [
            'message' => str_replace('[CANCELLED] ', '', $row['REMARKS']),
            'date' => $row['START_DATE'],
            'time' => $row['REMINDER_TIME'],
            'doctor' => '',
            'type' => 'cancellation'
        ];
    }
}

echo json_encode(['status' => 'success', 'reminders' => $reminders]);
?>