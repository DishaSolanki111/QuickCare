<?php
session_start();
include 'config.php';
require_once 'pdf_helper.php';

$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');

$query = "SELECT d.FIRST_NAME, d.LAST_NAME, s.SPECIALISATION_NAME,
          COUNT(a.APPOINTMENT_ID) as total_appointments,
          SUM(CASE WHEN a.STATUS = 'COMPLETED' THEN 1 ELSE 0 END) as completed,
          SUM(CASE WHEN a.STATUS = 'CANCELLED' THEN 1 ELSE 0 END) as cancelled
          FROM doctor_tbl d
          JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
          LEFT JOIN appointment_tbl a ON d.DOCTOR_ID = a.DOCTOR_ID 
            AND a.APPOINTMENT_DATE BETWEEN '" . $conn->real_escape_string($start_date) . "' AND '" . $conn->real_escape_string($end_date) . "'
          GROUP BY d.DOCTOR_ID
          ORDER BY total_appointments DESC";
$result = mysqli_query($conn, $query);

$pdf = new SimplePdf();
$pdf->writeTitle('QuickCare - Doctor Performance Report');
$pdf->writeLine('Date range: ' . $start_date . ' to ' . $end_date);
$pdf->writeLine('');
$pdf->writeRow(['Doctor', 'Specialization', 'Total', 'Completed', 'Cancelled', 'Rate %'], [90, 80, 50, 55, 55, 55]);
$pdf->writeLine('');

while ($row = mysqli_fetch_assoc($result)) {
    $total = $row['total_appointments'];
    $completed = $row['completed'];
    $cancelled = $row['cancelled'];
    $rate = $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    $pdf->writeRow([
        $row['FIRST_NAME'] . ' ' . $row['LAST_NAME'],
        $row['SPECIALISATION_NAME'],
        $total,
        $completed,
        $cancelled,
        $rate . '%'
    ], [90, 80, 50, 55, 55, 55]);
}

$pdf->Output('D', 'doctor_performance_report_' . date('Y-m-d') . '.pdf');
