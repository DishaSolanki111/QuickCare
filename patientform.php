<?php
session_start();
include 'config.php';   // 🔴 REQUIRED — FIXES YOUR ERROR
include 'header.php';
 $success = false;
 $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 🔹 SANITIZE INPUTS
    $first_name  = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name   = mysqli_real_escape_string($conn, $_POST['last_name']);
    $username    = mysqli_real_escape_string($conn, $_POST['username']);
    $password    = $_POST['password'];
    $dob         = $_POST['dob'];
    $gender      = $_POST['gender'] ?? '';
    $blood_group = $_POST['blood_group'] ?? '';
    $phone       = $_POST['phone'];
    $email       = $_POST['email'];
    $address     = mysqli_real_escape_string($conn, $_POST['address']);

    // 🔹 HASH PASSWORD
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 🔹 CHECK USERNAME ALREADY EXISTS
    $check = mysqli_query($conn, "SELECT PATIENT_ID FROM patient_tbl WHERE USERNAME='$username'");
    if(mysqli_num_rows($check) > 0){
        $error = "Username already exists!";
    } else {

        // 🔹 INSERT PATIENT
        $sql = "
        INSERT INTO patient_tbl
        (FIRST_NAME, LAST_NAME, USERNAME, PSWD, DOB, GENDER, BLOOD_GROUP, PHONE, EMAIL, ADDRESS)
        VALUES
        ('$first_name','$last_name','$username','$hashed_password','$dob','$gender',
         '$blood_group','$phone','$email','$address')
        ";

        if(mysqli_query($conn, $sql)){
            $success = true;
        } else {
            $error = mysqli_error($conn);
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
    padding:0;
    min-height:100vh;
    padding-top:100px; /* Adjust based on your header height */
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

/* Responsive adjustments */
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
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
    </div>
    <div class="form-group">
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" placeholder="Enter your last name">
    </div>
</div>

<div class="row">
    <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Choose a username" required>
    </div>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Create a password" required>
    </div>
</div>

<div class="row">
    <div class="form-group">
        <label for="dob">Date of Birth</label>
        <input type="date" id="dob" name="dob">
    </div>
</div>

<div class="row">
    <div class="form-group">
        <label for="gender">Gender</label>
        <select id="gender" name="gender">
            <option value="">Select Gender</option>
            <option value="MALE">Male</option>
            <option value="FEMALE">Female</option>
            <option value="OTHER">Other</option>
        </select>
    </div>
    <div class="form-group">
        <label for="blood_group">Blood Group</label>
        <select id="blood_group" name="blood_group">
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
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
    </div>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email address" required>
    </div>
</div>

<div class="form-group">
    <label for="address">Address</label>
    <textarea id="address" name="address" placeholder="Enter your address"></textarea>
</div>

<div style="text-align:center;margin-top:20px;">
    <button type="submit">Register</button>
</div>

</form>
</div>

</body>
</html>