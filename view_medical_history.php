<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['PATIENT_ID'])) {
    header("Location: login_for_all.php");
    exit;
}

$patient_id = $_SESSION['PATIENT_ID'];

// Get file path from query parameter
if (!isset($_GET['file']) || empty($_GET['file'])) {
    die("Invalid file request.");
}

$file_path = $_GET['file'];

// Security: Verify the file belongs to the logged-in patient
$patient_query = mysqli_query($conn, "SELECT MEDICAL_HISTORY_FILE FROM patient_tbl WHERE PATIENT_ID = '$patient_id'");
$patient = mysqli_fetch_assoc($patient_query);

if (!$patient || $patient['MEDICAL_HISTORY_FILE'] !== $file_path) {
    die("Access denied. This file does not belong to your account.");
}

// Verify file exists and is within allowed directory
$allowed_dir = realpath(__DIR__ . '/uploads/medical_history/');
$file_full_path = realpath($file_path);

// Security check: Ensure file is within the allowed directory
if (!$allowed_dir) {
    // Directory doesn't exist, create it
    if (!file_exists(__DIR__ . '/uploads/medical_history/')) {
        mkdir(__DIR__ . '/uploads/medical_history/', 0777, true);
    }
    $allowed_dir = realpath(__DIR__ . '/uploads/medical_history/');
}

if (!$file_full_path || !$allowed_dir || strpos($file_full_path, $allowed_dir) !== 0) {
    die("Invalid file path.");
}

// Verify file exists
if (!file_exists($file_path)) {
    die("File not found.");
}

// Get file extension
$file_ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
$allowed_extensions = ['pdf', 'jpg', 'jpeg', 'png'];

if (!in_array($file_ext, $allowed_extensions)) {
    die("Invalid file type.");
}

// Set appropriate headers based on file type
$mime_types = [
    'pdf' => 'application/pdf',
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png'
];

$mime_type = $mime_types[$file_ext];

// Output file
header('Content-Type: ' . $mime_type);
header('Content-Disposition: inline; filename="' . basename($file_path) . '"');
header('Content-Length: ' . filesize($file_path));
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// For images, output directly; for PDFs, let browser handle it
readfile($file_path);
exit;
?>
