<?php
session_start();
include 'config.php';
require_once 'pdf_helper.php';

$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');
$status_filter = isset($_POST['status_filter']) ? $_POST['status_filter'] : '';

$query = "SELECT APPOINTMENT_DATE, STATUS, COUNT(*) as count 
          FROM appointment_tbl 
          WHERE APPOINTMENT_DATE BETWEEN '" . $conn->real_escape_string($start_date) . "' AND '" . $conn->real_escape_string($end_date) . "'";
if (!empty($status_filter)) {
    $query .= " AND STATUS = '" . $conn->real_escape_string($status_filter) . "'";
}
$query .= " GROUP BY APPOINTMENT_DATE, STATUS ORDER BY APPOINTMENT_DATE DESC";

$result = mysqli_query($conn, $query);
$appointments_data = [];
while ($row = mysqli_fetch_assoc($result)) {
    $date = $row['APPOINTMENT_DATE'];
    $status = $row['STATUS'];
    $count = $row['count'];
    if (!isset($appointments_data[$date])) {
        $appointments_data[$date] = ['total' => 0, 'completed' => 0, 'cancelled' => 0, 'scheduled' => 0];
    }
    $appointments_data[$date]['total'] += $count;
    if ($status == 'COMPLETED') $appointments_data[$date]['completed'] = $count;
    elseif ($status == 'CANCELLED') $appointments_data[$date]['cancelled'] = $count;
    elseif ($status == 'SCHEDULED') $appointments_data[$date]['scheduled'] = $count;
}

$pdf = new SimplePdf();
$pdf->writeTitle('QuickCare - Appointments Report');
$pdf->writeLine('Date range: ' . $start_date . ' to ' . $end_date);
$pdf->writeLine('');
$pdf->writeRow(['Date', 'Total', 'Completed', 'Cancelled', 'Scheduled'], [100, 70, 70, 70, 70]);
$pdf->writeLine('');

foreach ($appointments_data as $date => $data) {
    $pdf->writeRow([$date, $data['total'], $data['completed'], $data['cancelled'], $data['scheduled']], [100, 70, 70, 70, 70]);
}
if (empty($appointments_data)) {
    $pdf->writeLine('No appointments found in the selected date range.');
}

$pdf->Output('D', 'appointments_report_' . date('Y-m-d') . '.pdf');
