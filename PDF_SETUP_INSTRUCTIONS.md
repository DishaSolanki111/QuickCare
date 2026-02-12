# PDF Download Setup Instructions

## Overview
The prescription PDF download functionality has been implemented. The system will work in two modes:

1. **With TCPDF Library** (Recommended): Generates proper PDF files
2. **Without TCPDF Library** (Fallback): Generates HTML that can be printed/saved as PDF by the browser

## Installation Options

### Option 1: Install TCPDF via Composer (Recommended)

1. Navigate to your project root directory
2. Run the following command:
   ```bash
   composer require tecnickcom/tcpdf
   ```
3. The library will be installed in the `vendor` folder
4. Update `generate_prescription_pdf.php` line 8 to:
   ```php
   require_once __DIR__ . '/vendor/autoload.php';
   ```

### Option 2: Manual TCPDF Installation

1. Download TCPDF from: https://tcpdf.org/
2. Extract the TCPDF folder to your project root directory
3. The folder structure should be: `your-project/tcpdf/`
4. The system will automatically detect and use it

### Option 3: Use Browser Print-to-PDF (Current Fallback)

If TCPDF is not installed, the system will automatically generate an HTML page with:
- Professional styling
- Print-optimized layout
- A "Print / Save as PDF" button
- Users can use their browser's print function to save as PDF

## Files Modified

1. **generate_prescription_pdf.php** - New PDF generation module
2. **patinet_prescriptions.php** - Updated to use PDF generator
3. **view_prescription.php** - Updated to use PDF generator

## Testing

1. Log in as a patient and go to "My Prescriptions"
2. Click "Download PDF" on any prescription
3. The PDF should download automatically

## Features

- Professional PDF layout with QuickCare branding
- Complete prescription information including:
  - Doctor information
  - Patient information
  - Prescription details (date, symptoms, diagnosis)
  - All prescribed medicines with dosage, frequency, and duration
  - Additional notes
- Proper formatting and styling
- Print-ready layout

## Troubleshooting

If PDFs are not downloading:
1. Check PHP error logs
2. Ensure file permissions are correct
3. Verify TCPDF installation (if using Option 1 or 2)
4. Check that `generate_prescription_pdf.php` is in the project root
