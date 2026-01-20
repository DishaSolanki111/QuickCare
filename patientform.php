<?php
session_start();
include 'config.php';
include 'header.php';

$success = false;
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 🔹 RAW INPUTS
    $first_name  = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name   = mysqli_real_escape_string($conn, $_POST['last_name']);
    $username    = trim($_POST['username']);
    $password    = $_POST['password'];
    $dob         = $_POST['dob'];
    $gender      = $_POST['gender'] ?? '';
    $blood_group = $_POST['blood_group'] ?? '';
    $phone       = trim($_POST['phone']);
    $email       = trim($_POST['email']);
    $address     = mysqli_real_escape_string($conn, $_POST['address']);

    /* =========================
       USERNAME VALIDATION
       ========================= */
    if (empty($username)) {
        $error = "Username is required.";
    }
    elseif (strlen($username) > 20) {
        $error = "Username must not exceed 20 characters.";
    }
    elseif (!preg_match('/^[A-Z][A-Za-z0-9_]*$/', $username)) {
        $error = "Username must start with a capital letter and contain only letters, digits, or underscore.";
    }
    elseif (strpos($username, '__') !== false) {
        $error = "Username must not contain consecutive underscores.";
    }
    elseif (substr($username, -1) === '_') {
        $error = "Username must not end with an underscore.";
    }

    /* =========================
       PASSWORD VALIDATION
       ========================= */
    elseif (empty($password)) {
        $error = "Password is required.";
    }
    elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    }
    elseif (!preg_match('/[A-Z]/', $password)) {
        $error = "Password must contain at least one uppercase letter.";
    }
    elseif (!preg_match('/[0-9]/', $password)) {
        $error = "Password must contain at least one digit.";
    }
    elseif (!preg_match('/[\W_]/', $password)) {
        $error = "Password must contain at least one special character.";
    }

    /* =========================
       PHONE VALIDATION
       ========================= */
    elseif (empty($phone)) {
        $error = "Phone number is required.";
    }
    elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Phone number must contain exactly 10 digits.";
    }

    /* =========================
       EMAIL VALIDATION
       ========================= */
    elseif (empty($email)) {
        $error = "Email is required.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    }

    else {

        // 🔹 CHECK USERNAME UNIQUENESS
        $username_safe = mysqli_real_escape_string($conn, $username);
        $check = mysqli_query($conn, "SELECT PATIENT_ID FROM patient_tbl WHERE USERNAME='$username_safe'");

        if (mysqli_num_rows($check) > 0) {
            $error = "Username already exists!";
        } else {

            // 🔹 HASH PASSWORD ONLY AFTER ALL VALIDATION
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // 🔹 INSERT PATIENT
            $sql = "
                INSERT INTO patient_tbl
                (FIRST_NAME, LAST_NAME, USERNAME, PSWD, DOB, GENDER, BLOOD_GROUP, PHONE, EMAIL, ADDRESS)
                VALUES
                ('$first_name','$last_name','$username_safe','$hashed_password','$dob','$gender',
                 '$blood_group','$phone','$email','$address')
            ";

            if (mysqli_query($conn, $sql)) {
                $success = true;
            } else {
                $error = mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Patient Registration</title>

<style>
body{
    font-family:Arial;
    background:linear-gradient(135deg,#0a4d68,#0582ca);
    margin:0;
    padding:80px;
    min-height:100vh;
    padding-top:100px;
}
.container{
    background:#fff;
    width:800px;
    max-width:90%;
    margin:20px auto;
    padding:30px;
    border-radius:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}
h1{text-align:center;color:#03045e;margin-bottom:25px;}
.row{display:flex;gap:20px;margin-bottom:15px;}
.form-group{
    flex:1;
    display:flex;
    flex-direction:column;
}
label{
    margin-bottom:5px;
    font-weight:bold;
    color:#03045e;
}
input,select,textarea{
    width:100%;
    padding:10px;
    border-radius:6px;
    border:1px solid #ccc;
    box-sizing:border-box;
}
textarea{height:80px;}
button{
    background:#0582ca;
    color:white;
    border:none;
    padding:12px 30px;
    border-radius:25px;
    cursor:pointer;
    font-size:16px;
    transition:background 0.3s;
}
button:hover{
    background:#0a4d68;
}
.success{color:green;text-align:center;margin-bottom:15px;padding:10px;background:#e8f5e9;border-radius:5px;}
.error{color:red;text-align:center;margin-bottom:15px;padding:10px;background:#ffebee;border-radius:5px;}

@media (max-width: 768px) {
    .row {
        flex-direction: column;
        gap: 10px;
    }
    .container {
        width:95%;
        padding:20px;
    }
}
</style>
</head>

<body>

<div class="container">
<h1>Patient Registration</h1>

<?php if($success): ?>
    <div class="success">Registration Successful! You can login now.</div>
<?php endif; ?>

<?php if($error): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST">

<div class="row">
    <div class="form-group">
        <label>First Name</label>
        <input type="text" name="first_name" required>
    </div>
    <div class="form-group">
        <label>Last Name</label>
        <input type="text" name="last_name">
    </div>
</div>

<div class="row">
    <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" required>
    </div>
    <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
    </div>
</div>

<div class="row">
    <div class="form-group">
        <label>Date of Birth</label>
        <input type="date" name="dob">
    </div>
</div>

<div class="row">
    <div class="form-group">
        <label>Gender</label>
        <select name="gender">
            <option value="">Select Gender</option>
            <option value="MALE">Male</option>
            <option value="FEMALE">Female</option>
            <option value="OTHER">Other</option>
        </select>
    </div>
    <div class="form-group">
        <label>Blood Group</label>
        <select name="blood_group">
            <option value="">Select Blood Group</option>
            <option>A+</option><option>A-</option>
            <option>B+</option><option>B-</option>
            <option>O+</option><option>O-</option>
            <option>AB+</option><option>AB-</option>
        </select>
    </div>
</div>

<div class="row">
    <div class="form-group">
        <label>Phone Number</label>
        <input type="tel" name="phone" required>
    </div>
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
</div>

<div class="form-group">
    <label>Address</label>
    <textarea name="address"></textarea>
</div>

<div style="text-align:center;margin-top:20px;">
    <button type="submit">Register</button>
</div>

</form>
</div>

</body>
</html>
