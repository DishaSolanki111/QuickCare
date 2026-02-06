<?php
session_start();
include "config.php"; // Your database connection file

 $phone = $_POST['phone'];

// --- Security: Use Prepared Statements ---
 $userFound = false;
 $userId = null;
 $userType = null;
 $idColumn = 'id'; // Default column name

// 1. Check patient_tbl
 $stmt = $conn->prepare("SELECT patient_id FROM patient_tbl WHERE phone = ?");
 $stmt->bind_param("s", $phone);
 $stmt->execute();
 $result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    // FIX: Use the correct column name 'patient_id'
    $userId = $user['patient_id']; 
    $userType = 'patient';
    $idColumn = 'patient_id'; // Specify the correct column for the update
    $userFound = true;
}
 $stmt->close();

// 2. If not found, check user_tbl
if (!$userFound) {
    $stmt = $conn->prepare("SELECT id FROM user_tbl WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $userId = $user['id'];
        $userType = 'user';
        $userFound = true;
    }
    $stmt->close();
}

// 3. If still not found, check doctor_tbl
if (!$userFound) {
    $stmt = $conn->prepare("SELECT id FROM doctor_tbl WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $userId = $user['id'];
        $userType = 'doctor';
        $userFound = true;
    }
    $stmt->close();
}

// 4. If phone not found in any table, stop the script.
if (!$userFound) {
    // Use a session message instead of die() for better UX
    $_SESSION['error'] = "Phone number not registered.";
    header("Location: forgot_password.php");
    exit();
}

// --- OTP Generation and Session Storage ---
 $otp = rand(100000, 999999);

// Store all necessary info in the session for the next step
 $_SESSION['otp'] = $otp;
 $_SESSION['phone'] = $phone;
 $_SESSION['reset_user_id'] = $userId;
 $_SESSION['reset_user_type'] = $userType;
 $_SESSION['reset_id_column'] = $idColumn; // Store the ID column name

// --- SMS Sending (Your existing code with error checking) ---
 $fields = [
    "sender_id" => "TXTIND",
    "message" => "Your QuickCare OTP is $otp",
    "route" => "v3",
    "numbers" => $phone,
];

 $message = "Your QuickCare OTP is $otp";

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://www.fast2sms.com/dev/bulkV2",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => json_encode([
      "route" => "otp",
      "variables_values" => $otp,
      "numbers" => $phone
  ]),
  CURLOPT_HTTPHEADER => array(
    "authorization: NL3GLbx9tegd3xipH8QyIkF5bxkqj3UyKzLgTde19OsZwVl9MgURlhYd8qyL",
    "content-type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
  die("SMS Error: " . $err);
}

echo $response; // TEMP: see Fast2SMS reply
exit;


// --- Redirect to Verification Page ---
header("Location: reset_password_form.php");
exit();
?>