<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Registration | QuickCare</title>
    <style>
        :root {
            --primary-blue: #0a4d68;
            --secondary-blue: #0582ca;
            --light-blue: #b3e5fc;
            --accent-blue: #00b4d8;
            --dark-blue: #03045e;
            --white: #ffffff;
            --light-gray: #f5f5f5;
            --error: #ff5252;
              --dark-blue: #072D44;
            --mid-blue: #064469;
            --soft-blue: #5790AB;
            --light-blue: #9CCDD8;
            --gray-blue: #D0D7E1;
            --white: #ffffff;
            --card-bg: #F6F9FB;
            --primary-color: #1a3a5f;
            --secondary-color: #3498db;
            --accent-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #17a2b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            min-height: 100vh;
            display: flex;
            padding: 0;
        }

        /* Main Content */
        .main-content {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Form Container */
        .container {
            background-color: var(--white);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 800px;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--accent-blue), var(--secondary-blue));
        }

        h1 {
            color: var(--dark-blue);
            text-align: center;
            margin-bottom: 25px;
            font-size: 28px;
            position: relative;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 3px;
            background-color: var(--accent-blue);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary-blue);
            font-weight: 600;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 2px rgba(0, 180, 216, 0.2);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 8px;
        }

        .radio-option {
            display: flex;
            align-items: center;
        }

        .radio-option input {
            margin-right: 8px;
        }

        .btn-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .btn {
            background: linear-gradient(90deg, var(--primary-blue), var(--secondary-blue));
            color: var(--white);
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 50px;
            cursor: pointer;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .error-message {
            color: var(--error);
            font-size: 14px;
            margin-top: 5px;
            display: none;
        }

        .success-message {
            background-color: var(--light-blue);
            color: var(--primary-blue);
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: none;
            text-align: center;
        }

        .required {
            color: var(--error);
        }

        .file-upload {
            position: relative;
            display: inline-block;
            cursor: pointer;
            width: 100%;
        }

        .file-upload input[type=file] {
            position: absolute;
            left: -9999px;
        }

        .file-upload-label {
            display: block;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: var(--white);
            color: #666;
            text-align: center;
            transition: all 0.3s;
        }

        .file-upload-label:hover {
            border-color: var(--accent-blue);
            color: var(--primary-blue);
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 20px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <h1>Doctor Registration</h1>
            
            <?php
            // Initialize variables
            $success = false;
            $error = "";
            $profile_image_path = "";
            
            if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $errors = [];

    // ---------------- PROFILE IMAGE (JPG ONLY) ----------------
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $upload_dir = "uploads/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'jpg' && $ext !== 'jpeg') {
            $errors[] = "Profile image must be in JPG format only.";
        } else {
            $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
            $target_file = $upload_dir . $file_name;
            move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file);
            $profile_image_path = $target_file;
        }
    }

    // ---------------- SANITIZE ----------------
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name  = mysqli_real_escape_string($conn, $_POST['last_name']);
    $dob        = $_POST['dob'];
    $doj        = $_POST['doj'];
    $gender     = $_POST['gender'] ?? '';
    $phone      = $_POST['phone'];
    $email      = $_POST['email'];
    $education  = mysqli_real_escape_string($conn, $_POST['education']);
    $username   = $_POST['username'];
    $password   = $_POST['password'];
    $specialisation_id = $_POST['specialisation_id'];

    // ---------------- USERNAME VALIDATION ----------------
    if (
        !preg_match('/^[A-Z][A-Za-z0-9]*(_[A-Za-z0-9]+)*$/', $username) ||
        strlen($username) > 20
    ) {
        $errors[] = "Username must start with a capital letter, max 20 chars, no spaces, no consecutive underscores, and not end with underscore.";
    }

    // ---------------- PASSWORD VALIDATION ----------------
    if (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[\W]/', $password)
    ) {
        $errors[] = "Password must be at least 8 characters and include 1 uppercase letter, 1 digit, and 1 special character.";
    }

    // ---------------- PHONE VALIDATION ----------------
    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "Phone number must be exactly 10 digits.";
    }

    // ---------------- EMAIL VALIDATION ----------------
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // ---------------- FINAL INSERT ----------------
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO doctor_tbl 
        (SPECIALISATION_ID, PROFILE_IMAGE, FIRST_NAME, LAST_NAME, DOB, DOJ, USERNAME, PSWD, PHONE, EMAIL, GENDER, EDUCATION)
        VALUES 
        ('$specialisation_id','$profile_image_path','$first_name','$last_name','$dob','$doj','$username','$hashed_password','$phone','$email','$gender','$education')";

        if ($conn->query($sql) === TRUE) {
            $success = true;
        } else {
            $error = "Database error.";
        }
    } else {
        $error = implode("<br>", $errors);
    }

    $conn->close();
}

            ?>
            
            <?php if ($success): ?>
                <div class="success-message" style="display: block;">
                    Registration successful!
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="error-message" style="display: block;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="doctorForm" enctype="multipart/form-data">
                <!-- First Name and Last Name -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" required>
                        <div class="error-message" id="first_name_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" required>
                        <div class="error-message" id="last_name_error"></div>
                    </div>
                </div>
                
                <!-- Date of Birth and Date of Joining -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob">
                        <div class="error-message" id="dob_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="doj">Date of Joining</label>
                        <input type="date" id="doj" name="doj">
                        <div class="error-message" id="doj_error"></div>
                    </div>
                </div>
                
                <!-- Gender -->
                <div class="form-group">
                    <label>Gender</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="male" name="gender" value="MALE">
                            <label for="male">Male</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="female" name="gender" value="FEMALE">
                            <label for="female">Female</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="other" name="gender" value="OTHER">
                            <label for="other">Other</label>
                        </div>
                    </div>
                </div>
                
                <!-- Phone Number and Email ID -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number <span class="required">*</span></label>
                        <input type="number" id="phone" name="phone" required>
                        <div class="error-message" id="phone_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" required>
                        <div class="error-message" id="email_error"></div>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label for="education">Education</label>
                        <textarea id="education" name="education" rows="3"></textarea>
                        <div class="error-message" id="education_error"></div>
                        

                <!-- Specialisation and Profile Image -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="specialisation_id">Specialisation <span class="required">*</span></label>
                        <select id="specialisation_id" name="specialisation_id" required>
                            <option value="">Select Specialisation</option>
                            <option value="1">Pediatrician</option>
                            <option value="2">Cardiologist</option>
                            <option value="3">Neurologist</option>
                            <option value="4">Orthopedic</option>
                        </select>
                        <div class="error-message" id="specialisation_id_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="profile_image">Profile Image</label>
                        <div class="file-upload">
                            <input type="file" id="profile_image" name="profile_image" accept="image/*">
                            <label for="profile_image" class="file-upload-label" id="file-label">Choose Profile Image</label>
                        </div>
                        <div class="error-message" id="profile_image_error"></div>
                    </div>
                </div>
                
                <!-- Username and Password -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username <span class="required">*</span></label>
                        <input type="text" id="username" name="username" required>
                        <div class="error-message" id="username_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <input type="password" id="password" name="password" required>
                        <div class="error-message" id="password_error"></div>
                    </div>
                </div>
                
                <div class="btn-container">
                    <button type="submit" class="btn">Register</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    document.getElementById('profile_image').addEventListener('change', function () {
    const file = this.files[0];
    const label = document.getElementById('file-label');
    if (file) {
        const ext = file.name.split('.').pop().toLowerCase();
        if (ext !== 'jpg' && ext !== 'jpeg' && ext !== 'png' && ext !== 'PNG') {
            alert('Profile image must be JPG format only');
            this.value = '';
            label.textContent = 'Choose Profile Image';
        } else {
            label.textContent = file.name;
        }
    }
});

document.getElementById('doctorForm').addEventListener('submit', function (e) {
    let valid = true;

    const username = document.getElementById('username');
    const password = document.getElementById('password');
    const phone = document.getElementById('phone');
    const email = document.getElementById('email');

    // USERNAME
    const usernamePattern = /^[A-Z][A-Za-z0-9]*(_[A-Za-z0-9]+)*$/;
    if (!usernamePattern.test(username.value) || username.value.length > 20) {
        alert("Invalid username format.");
        valid = false;
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
    }

    // PHONE
    if (!/^[0-9]{10}$/.test(phone.value)) {
        alert("Phone must be exactly 10 digits.");
        valid = false;
    }

    // EMAIL
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        alert("Invalid email format.");
        valid = false;
    }

    if (!valid) e.preventDefault();
});
</script>

</body>
</html>