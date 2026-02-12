<?php
/**
 * Prescription PDF Generator
 * Requires TCPDF library (https://tcpdf.org/)
 * Download TCPDF and place it in a 'tcpdf' folder, or use composer: composer require tecnickcom/tcpdf
 */

// Include TCPDF library
// If TCPDF is installed via composer:
// require_once __DIR__ . '/vendor/autoload.php';

// If TCPDF is in a tcpdf folder:
if (file_exists(__DIR__ . '/tcpdf/tcpdf.php')) {
    require_once(__DIR__ . '/tcpdf/tcpdf.php');
} else {
    // Fallback: Try to use a simple HTML to PDF approach
    // This will work but may not be as feature-rich
}

function generatePrescriptionPDF($prescription, $medicines, $conn) {
    // Try to use TCPDF if available
    if (class_exists('TCPDF')) {
        return generatePDFWithTCPDF($prescription, $medicines);
    } else {
        // Fallback to HTML output that can be converted to PDF by browser
        return generatePDFAsHTML($prescription, $medicines);
    }
}

function generatePDFWithTCPDF($prescription, $medicines) {
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('QuickCare Medical Center');
    $pdf->SetAuthor('QuickCare System');
    $pdf->SetTitle('Prescription - ' . $prescription['PRESCRIPTION_ID']);
    $pdf->SetSubject('Medical Prescription');
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Set margins
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->Cell(0, 10, 'QuickCare Medical Center', 0, 1, 'C');
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 10, 'Medical Prescription', 0, 1, 'C');
    $pdf->Ln(5);
    
    // Doctor Information
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'Doctor Information', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'Name: Dr. ' . $prescription['DOC_FNAME'] . ' ' . $prescription['DOC_LNAME'], 0, 1);
    $pdf->Cell(0, 6, 'Specialization: ' . $prescription['SPECIALISATION_NAME'], 0, 1);
    if (!empty($prescription['EDUCATION'])) {
        $pdf->Cell(0, 6, 'Education: ' . $prescription['EDUCATION'], 0, 1);
    }
    if (!empty($prescription['DOC_PHONE'])) {
        $pdf->Cell(0, 6, 'Contact: ' . $prescription['DOC_PHONE'], 0, 1);
    }
    $pdf->Ln(3);
    
    // Patient Information
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'Patient Information', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'Name: ' . $prescription['PAT_FNAME'] . ' ' . $prescription['PAT_LNAME'], 0, 1);
    if (!empty($prescription['DOB'])) {
        $pdf->Cell(0, 6, 'Date of Birth: ' . date('F d, Y', strtotime($prescription['DOB'])), 0, 1);
    }
    if (!empty($prescription['GENDER'])) {
        $pdf->Cell(0, 6, 'Gender: ' . $prescription['GENDER'], 0, 1);
    }
    if (!empty($prescription['PAT_PHONE'])) {
        $pdf->Cell(0, 6, 'Contact: ' . $prescription['PAT_PHONE'], 0, 1);
    }
    if (!empty($prescription['ADDRESS'])) {
        $pdf->Cell(0, 6, 'Address: ' . $prescription['ADDRESS'], 0, 1);
    }
    $pdf->Ln(3);
    
    // Prescription Details
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, 'Prescription Details', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'Date: ' . date('F d, Y', strtotime($prescription['ISSUE_DATE'])), 0, 1);
    if (!empty($prescription['APPOINTMENT_DATE'])) {
        $pdf->Cell(0, 6, 'Appointment Date: ' . date('F d, Y', strtotime($prescription['APPOINTMENT_DATE'])), 0, 1);
    }
    if (!empty($prescription['HEIGHT_CM'])) {
        $pdf->Cell(0, 6, 'Height: ' . $prescription['HEIGHT_CM'] . ' cm', 0, 1);
    }
    if (!empty($prescription['WEIGHT_KG'])) {
        $pdf->Cell(0, 6, 'Weight: ' . $prescription['WEIGHT_KG'] . ' kg', 0, 1);
    }
    if (!empty($prescription['BLOOD_PRESSURE'])) {
        $pdf->Cell(0, 6, 'Blood Pressure: ' . $prescription['BLOOD_PRESSURE'] . ' mmHg', 0, 1);
    }
    if (!empty($prescription['DIABETES'])) {
        $pdf->Cell(0, 6, 'Diabetes: ' . $prescription['DIABETES'], 0, 1);
    }
    $pdf->Ln(3);
    
    // Symptoms and Diagnosis
    if (!empty($prescription['SYMPTOMS'])) {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 6, 'Symptoms:', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 6, $prescription['SYMPTOMS'], 0, 'L');
        $pdf->Ln(2);
    }
    
    if (!empty($prescription['DIAGNOSIS'])) {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 6, 'Diagnosis:', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 6, $prescription['DIAGNOSIS'], 0, 'L');
        $pdf->Ln(2);
    }
    
    // Medicines Table
    if (!empty($medicines)) {
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, 'Prescribed Medicines', 0, 1, 'L');
        $pdf->Ln(2);
        
        // Table header
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell(70, 8, 'Medicine Name', 1, 0, 'L', true);
        $pdf->Cell(40, 8, 'Dosage', 1, 0, 'L', true);
        $pdf->Cell(40, 8, 'Frequency', 1, 0, 'L', true);
        $pdf->Cell(40, 8, 'Duration', 1, 1, 'L', true);
        
        // Table data
        $pdf->SetFont('helvetica', '', 9);
        foreach ($medicines as $medicine) {
            $pdf->Cell(70, 7, $medicine['MED_NAME'], 1, 0, 'L');
            $pdf->Cell(40, 7, $medicine['DOSAGE'], 1, 0, 'L');
            $pdf->Cell(40, 7, $medicine['FREQUENCY'], 1, 0, 'L');
            $pdf->Cell(40, 7, $medicine['DURATION'], 1, 1, 'L');
        }
        $pdf->Ln(3);
    }
    
    // Additional Notes
    if (!empty($prescription['ADDITIONAL_NOTES'])) {
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(0, 6, 'Additional Notes:', 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->MultiCell(0, 6, $prescription['ADDITIONAL_NOTES'], 0, 'L');
        $pdf->Ln(3);
    }
    
    // Footer
    $pdf->SetFont('helvetica', 'I', 8);
    $pdf->Cell(0, 6, 'This is a digitally generated prescription. For any queries, please contact the medical center.', 0, 1, 'C');
    $pdf->Cell(0, 6, 'Generated on: ' . date('F d, Y'), 0, 1, 'C');
    
    // Output PDF
    $filename = 'prescription_' . $prescription['PRESCRIPTION_ID'] . '.pdf';
    $pdf->Output($filename, 'D'); // 'D' for download
    exit;
}

function generatePDFAsHTML($prescription, $medicines) {
    // Fallback: Generate HTML that can be printed as PDF
    header('Content-Type: text/html; charset=UTF-8');
    
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Prescription - ' . htmlspecialchars($prescription['PRESCRIPTION_ID']) . '</title>
    <style>
        @media print {
            @page { margin: 1cm; }
            body { margin: 0; }
        }
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px; 
            line-height: 1.6;
        }
        .header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 3px solid #064469;
            padding-bottom: 15px;
        }
        .header h1 { 
            color: #064469; 
            margin: 0;
            font-size: 24px;
        }
        .header h2 { 
            color: #064469; 
            margin: 5px 0;
            font-size: 18px;
        }
        .section { 
            margin-bottom: 20px; 
            padding: 10px;
            background-color: #f9f9f9;
            border-left: 4px solid #064469;
        }
        .section h3 {
            color: #064469;
            margin-top: 0;
            font-size: 14px;
            text-transform: uppercase;
        }
        .info-row {
            margin: 5px 0;
        }
        .info-label {
            font-weight: bold;
            color: #064469;
            display: inline-block;
            width: 150px;
        }
        .medicine-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin: 15px 0; 
        }
        .medicine-table th, .medicine-table td { 
            border: 1px solid #ddd; 
            padding: 10px; 
            text-align: left; 
        }
        .medicine-table th { 
            background-color: #064469; 
            color: white;
            font-weight: bold;
        }
        .medicine-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .footer { 
            margin-top: 40px; 
            text-align: center; 
            font-style: italic; 
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #064469;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .print-button:hover {
            background: #072D44;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Print / Save as PDF</button>
    
    <div class="header">
        <h1>QuickCare Medical Center</h1>
        <h2>Medical Prescription</h2>
    </div>
    
    <div class="section">
        <h3>Doctor Information</h3>
        <div class="info-row">
            <span class="info-label">Name:</span>
            <span>Dr. ' . htmlspecialchars($prescription['DOC_FNAME'] . ' ' . $prescription['DOC_LNAME']) . '</span>
        </div>
        <div class="info-row">
            <span class="info-label">Specialization:</span>
            <span>' . htmlspecialchars($prescription['SPECIALISATION_NAME']) . '</span>
        </div>';
    
    if (!empty($prescription['EDUCATION'])) {
        $html .= '<div class="info-row">
            <span class="info-label">Education:</span>
            <span>' . htmlspecialchars($prescription['EDUCATION']) . '</span>
        </div>';
    }
    
    if (!empty($prescription['DOC_PHONE'])) {
        $html .= '<div class="info-row">
            <span class="info-label">Contact:</span>
            <span>' . htmlspecialchars($prescription['DOC_PHONE']);
        if (!empty($prescription['DOC_EMAIL'])) {
            $html .= ' | ' . htmlspecialchars($prescription['DOC_EMAIL']);
        }
        $html .= '</span>
        </div>';
    }
    
    $html .= '</div>
    
    <div class="section">
        <h3>Patient Information</h3>
        <div class="info-row">
            <span class="info-label">Name:</span>
            <span>' . htmlspecialchars($prescription['PAT_FNAME'] . ' ' . $prescription['PAT_LNAME']) . '</span>
        </div>';
    
    if (!empty($prescription['DOB'])) {
        $html .= '<div class="info-row">
            <span class="info-label">Date of Birth:</span>
            <span>' . date('F d, Y', strtotime($prescription['DOB'])) . '</span>
        </div>';
    }
    
    if (!empty($prescription['GENDER'])) {
        $html .= '<div class="info-row">
            <span class="info-label">Gender:</span>
            <span>' . htmlspecialchars($prescription['GENDER']) . '</span>
        </div>';
    }
    
    if (!empty($prescription['PAT_PHONE'])) {
        $html .= '<div class="info-row">
            <span class="info-label">Contact:</span>
            <span>' . htmlspecialchars($prescription['PAT_PHONE']);
        if (!empty($prescription['PAT_EMAIL'])) {
            $html .= ' | ' . htmlspecialchars($prescription['PAT_EMAIL']);
        }
        $html .= '</span>
        </div>';
    }
    
    if (!empty($prescription['ADDRESS'])) {
        $html .= '<div class="info-row">
            <span class="info-label">Address:</span>
            <span>' . nl2br(htmlspecialchars($prescription['ADDRESS'])) . '</span>
        </div>';
    }
    
    $html .= '</div>
    
    <div class="section">
        <h3>Prescription Details</h3>
        <div class="info-row">
            <span class="info-label">Date:</span>
            <span>' . date('F d, Y', strtotime($prescription['ISSUE_DATE'])) . '</span>
        </div>';
    
    if (!empty($prescription['APPOINTMENT_DATE'])) {
        $html .= '<div class="info-row">
            <span class="info-label">Appointment Date:</span>
            <span>' . date('F d, Y', strtotime($prescription['APPOINTMENT_DATE'])) . '</span>
        </div>';
    }
    
    if (!empty($prescription['HEIGHT_CM'])) {
        $html .= '<div class="info-row">
            <span class="info-label">Height:</span>
            <span>' . $prescription['HEIGHT_CM'] . ' cm</span>
        </div>';
    }
    
    if (!empty($prescription['WEIGHT_KG'])) {
        $html .= '<div class="info-row">
            <span class="info-label">Weight:</span>
            <span>' . $prescription['WEIGHT_KG'] . ' kg</span>
        </div>';
    }
    
    if (!empty($prescription['BLOOD_PRESSURE'])) {
        $html .= '<div class="info-row">
            <span class="info-label">Blood Pressure:</span>
            <span>' . $prescription['BLOOD_PRESSURE'] . ' mmHg</span>
        </div>';
    }
    
    if (!empty($prescription['DIABETES'])) {
        $html .= '<div class="info-row">
            <span class="info-label">Diabetes:</span>
            <span>' . htmlspecialchars($prescription['DIABETES']) . '</span>
        </div>';
    }
    
    if (!empty($prescription['SYMPTOMS'])) {
        $html .= '<div class="info-row">
            <span class="info-label">Symptoms:</span>
            <span>' . nl2br(htmlspecialchars($prescription['SYMPTOMS'])) . '</span>
        </div>';
    }
    
    if (!empty($prescription['DIAGNOSIS'])) {
        $html .= '<div class="info-row">
            <span class="info-label">Diagnosis:</span>
            <span>' . nl2br(htmlspecialchars($prescription['DIAGNOSIS'])) . '</span>
        </div>';
    }
    
    if (!empty($prescription['ADDITIONAL_NOTES'])) {
        $html .= '<div class="info-row">
            <span class="info-label">Additional Notes:</span>
            <span>' . nl2br(htmlspecialchars($prescription['ADDITIONAL_NOTES'])) . '</span>
        </div>';
    }
    
    $html .= '</div>';
    
    if (!empty($medicines)) {
        $html .= '<div class="section">
        <h3>Prescribed Medicines</h3>
        <table class="medicine-table">
            <tr>
                <th>Medicine Name</th>
                <th>Dosage</th>
                <th>Frequency</th>
                <th>Duration</th>
            </tr>';
        
        foreach ($medicines as $medicine) {
            $html .= '<tr>
                <td>' . htmlspecialchars($medicine['MED_NAME']) . '</td>
                <td>' . htmlspecialchars($medicine['DOSAGE']) . '</td>
                <td>' . htmlspecialchars($medicine['FREQUENCY']) . '</td>
                <td>' . htmlspecialchars($medicine['DURATION']) . '</td>
            </tr>';
        }
        
        $html .= '</table>
        </div>';
    }
    
    $html .= '<div class="footer">
        <p>This is a digitally generated prescription. For any queries, please contact the medical center.</p>
        <p>Generated on: ' . date('F d, Y') . '</p>
    </div>
</body>
</html>';
    
    echo $html;
    exit;
}
?>
