<?php
session_start();
include 'config.php';   // 🔴 REQUIRED — FIXES YOUR ERROR

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
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
}
.container{
    background:#fff;
    width:800px;
    padding:30px;
    border-radius:15px;
}
h1{text-align:center;color:#03045e;}
.row{display:flex;gap:20px;margin-bottom:15px;}
input,select,textarea{
    width:100%;
    padding:10px;
    border-radius:6px;
    border:1px solid #ccc;
}
textarea{height:80px;}
button{
    background:#0582ca;
    color:white;
    border:none;
    padding:12px 30px;
    border-radius:25px;
    cursor:pointer;
}
.success{color:green;text-align:center;margin-bottom:15px;}
.error{color:red;text-align:center;margin-bottom:15px;}
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
    <input type="text" name="first_name" placeholder="First Name" required>
    <input type="text" name="last_name" placeholder="Last Name">
</div>

<div class="row">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
</div>

<div class="row">
    <input type="date" name="dob">
</div>

<div class="row">
    <select name="gender">
        <option value="">Gender</option>
        <option value="MALE">Male</option>
        <option value="FEMALE">Female</option>
        <option value="OTHER">Other</option>
    </select>

    <select name="blood_group">
        <option value="">Blood Group</option>
        <option>A+</option><option>A-</option>
        <option>B+</option><option>B-</option>
        <option>O+</option><option>O-</option>
        <option>AB+</option><option>AB-</option>
    </select>
</div>

<div class="row">
    <input type="number" name="phone" placeholder="Phone Number" required>
    <input type="email" name="email" placeholder="Email" required>
</div>

<textarea name="address" placeholder="Address"></textarea>

<div style="text-align:center;margin-top:20px;">
    <button type="submit">Register</button>
</div>

</form>
</div>

</body>
</html>
