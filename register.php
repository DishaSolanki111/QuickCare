<?php
session_start();
include 'config.php';   // 🔴 REQUIRED — FIXES YOUR ERROR
require_once 'username_validation.php';
require_once 'account_validation.php';

 $success = false;
 $error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 🔹 SANITIZE INPUTS
    $first_name_raw  = $_POST['first_name'] ?? '';
    $last_name_raw   = $_POST['last_name'] ?? '';
    $usernameRaw = $_POST['username'] ?? '';
    $passwordRaw = $_POST['password'] ?? '';
    $dob         = $_POST['dob'];
    $gender      = $_POST['gender'] ?? '';
    $blood_group = $_POST['blood_group'] ?? '';
    $phone       = $_POST['phone'];
    $email_raw   = $_POST['email'] ?? '';
    $address     = mysqli_real_escape_string($conn, $_POST['address']);

    // 🔹 CHECK USERNAME ALREADY EXISTS (case-insensitive) + strict validation
    $usernameNormalized = '';
    $usernameError = '';
    if (!qc_validate_username($usernameRaw, $usernameNormalized, $usernameError)) {
        $error = $usernameError;
    } else {
        $username = $usernameNormalized;
        $emailLower = strtolower(trim((string)$email_raw));

        // Validate first/last name (no spaces; letters + - + ')
        $first_nameNormalized = '';
        $last_nameNormalized = '';
        if (!qc_validate_person_name($first_name_raw, $first_nameNormalized, $first_nameErr)) {
            $error = $first_nameErr;
        } elseif (!qc_validate_person_name($last_name_raw, $last_nameNormalized, $last_nameErr)) {
            $error = $last_nameErr;
        } else {
            $passwordError = '';
            if (!qc_validate_password($passwordRaw, $username, $emailLower, $passwordError)) {
                $error = $passwordError;
            } else {
                $first_name = mysqli_real_escape_string($conn, $first_nameNormalized);
                $last_name = mysqli_real_escape_string($conn, $last_nameNormalized);
                $email = mysqli_real_escape_string($conn, $email_raw);
                $hashed_password = password_hash($passwordRaw, PASSWORD_DEFAULT);
            }
        }

        // Only proceed if name/password validation passed
        if ($error === '') {
        $stmt = $conn->prepare("SELECT PATIENT_ID FROM patient_tbl WHERE LOWER(USERNAME) = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $check = $stmt->get_result();
        $exists = ($check && $check->num_rows > 0);
        $stmt->close();

        if ($exists) {
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
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Patient Registration</title>
<style>
body{
    font-family:Arial;
    background:linear-gradient(135deg,#0a4d68,#0582ca);
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
    margin:0;
    padding:0;
}
.container{
    background:#fff;
    width:800px;
    padding:30px;
    border-radius:15px;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
    max-height:90vh;
    overflow-y:auto;
}
h1{text-align:center;color:#03045e;margin-bottom:20px;}
.row{display:flex;gap:20px;margin-bottom:15px;}
input,select,textarea{
    width:100%;
    padding:10px;
    border-radius:6px;
    border:1px solid #ccc;
    font-size:14px;
    transition:border-color 0.3s;
}
input:focus,select:focus,textarea:focus{
    border-color:#0582ca;
    outline:none;
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
.success{
    color:green;
    text-align:center;
    margin-bottom:15px;
    padding:10px;
    background:#e6f7e6;
    border-radius:5px;
}
.error{
    color:red;
    text-align:center;
    margin-bottom:15px;
    padding:10px;
    background:#ffe6e6;
    border-radius:5px;
}
.form-group {
    margin-bottom: 15px;
}
.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}
.close-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #333;
}
.close-btn:hover {
    color: #0582ca;
}
</style>
</head>

<body>

<div class="container">
<button class="close-btn" onclick="window.close()">&times;</button>
<h1>Patient Registration</h1>

<?php if($success): ?>
    <div class="success">Registration Successful! Redirecting to login page...</div>
<?php endif; ?>

<?php if($error): ?>
    <div class="error"><?php echo $error; ?></div>
<?php endif; ?>

<form method="POST">
    <div class="form-group">
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" placeholder="First Name" required>
    </div>

    <div class="form-group">
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" placeholder="Last Name">
    </div>

    <div class="row">
        <div class="form-group" style="flex: 1;">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Username" required>
        </div>
        <div class="form-group" style="flex: 1;">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Password" required>
        </div>
    </div>

    <div class="form-group">
        <label for="dob">Date of Birth</label>
        <input type="date" id="dob" name="dob">
    </div>

    <div class="row">
        <div class="form-group" style="flex: 1;">
            <label for="gender">Gender</label>
            <select id="gender" name="gender">
                <option value="">Select Gender</option>
                <option value="MALE">Male</option>
                <option value="FEMALE">Female</option>
                <option value="OTHER">Other</option>
            </select>
        </div>
        <div class="form-group" style="flex: 1;">
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
        <div class="form-group" style="flex: 1;">
            <label for="phone">Phone Number</label>
            <input type="tel" id="phone" name="phone" placeholder="Phone Number" required>
        </div>
        <div class="form-group" style="flex: 1;">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Email" required>
        </div>
    </div>

    <div class="form-group">
        <label for="address">Address</label>
        <textarea id="address" name="address" placeholder="Address"></textarea>
    </div>

    <div style="text-align:center;margin-top:20px;">
        <button type="submit">Register</button>
    </div>
</form>
</div>

<script>
// Redirect to login page after successful registration
<?php if($success): ?>
setTimeout(function() {
    // If opened in a popup
    if (window.opener) {
        window.close();
        // If window.close() doesn't work (popup blocked), redirect the parent window
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'login.php';
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'standalone';
        input.value = 'true';
        form.appendChild(input);
        if (window.opener && window.opener.document) {
            window.opener.document.body.appendChild(form);
            form.submit();
        }
    } else {
        // If opened in a regular tab/window
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'login.php';
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'standalone';
        input.value = 'true';
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}, 2000);
<?php endif; ?>
</script>

</body>
</html>