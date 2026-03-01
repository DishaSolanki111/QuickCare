<?php
session_start();
include 'config.php';
require_once 'pdf_helper.php';

$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-d');

$query = "SELECT PAYMENT_MODE, COUNT(*) as count, SUM(AMOUNT) as total 
          FROM payment_tbl 
          WHERE PAYMENT_DATE BETWEEN '" . $conn->real_escape_string($start_date) . "' AND '" . $conn->real_escape_string($end_date) . "' AND STATUS = 'COMPLETED'
          GROUP BY PAYMENT_MODE";
$result = mysqli_query($conn, $query);

$pdf = new SimplePdf();
$pdf->writeTitle('QuickCare - Revenue Report');
$pdf->writeLine('Date range: ' . $start_date . ' to ' . $end_date);
$pdf->writeLine('');
$pdf->writeRow(['Payment Mode', 'Count', 'Total Amount'], [150, 80, 120]);
$pdf->writeLine('');

$total_count = 0;
$total_amount = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $pdf->writeRow([$row['PAYMENT_MODE'], $row['count'], 'Rs ' . number_format($row['total'], 2)], [150, 80, 120]);
    $total_count += $row['count'];
    $total_amount += $row['total'];
}
$pdf->writeRow(['Total', $total_count, 'Rs ' . number_format($total_amount, 2)], [150, 80, 120]);

$pdf->Output('D', 'revenue_report_' . date('Y-m-d') . '.pdf');
