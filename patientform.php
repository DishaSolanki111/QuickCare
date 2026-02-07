<?php include 'config.php'; 
include 'header.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration | QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            --mid-blue: #064469;
            --soft-blue: #5790AB;
            --gray-blue: #D0D7E1;
            --card-bg: #F6F9FB;
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
            padding-top: 80px;
        }

        /* Main Content */
        .main-content {
            margin-left: 150px;
            width: calc(100% - 250px);
            padding: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Form Container */
        .container {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 800px;
            padding: 35px;
            position: relative;
        }

        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-blue), var(--secondary-blue));
        }

        h1 {
            color: var(--dark-blue);
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            position: relative;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--accent-blue);
        }

        /* Simplified spacing since headers are removed */
        .form-section {
            margin-bottom: 15px;
        }

        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary-blue);
            font-weight: 600;
            font-size: 15px;
        }

        .required {
            color: var(--error);
        }

        /* Password Toggle Styles */
        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--soft-blue);
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: var(--primary-blue);
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
            padding: 12px 15px;
            border: 1px solid var(--gray-blue);
            border-radius: 6px;
            font-size: 15px;
            transition: all 0.3s ease;
            background-color: var(--white);
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="date"]:focus,
        input[type="file"]:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: var(--accent-blue);
            box-shadow: 0 0 0 2px rgba(0, 180, 216, 0.1);
        }

        /* Adjust padding for password field to accommodate icon */
        input[type="password"] {
            padding-right: 40px; 
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%230a4d68' d='M10.293 3.293L6 7.586 1.707 3.293A1 1 0 00.293 4.707l5 5a1 1 0 001.414 0l5-5a1 1 0 10-1.414-1.414z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 35px;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 8px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .radio-option input {
            margin-right: 8px;
            cursor: pointer;
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
            padding: 12px 35px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(10, 77, 104, 0.2);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(10, 77, 104, 0.3);
        }

        .error-message {
            color: var(--error);
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }

        .success-message {
            background-color: rgba(179, 229, 252, 0.3);
            color: var(--primary-blue);
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            display: none;
            text-align: center;
            font-weight: 500;
            border: 1px solid var(--light-blue);
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
            border: 1px dashed var(--gray-blue);
            border-radius: 6px;
            background-color: var(--white);
            color: var(--soft-blue);
            text-align: center;
            transition: all 0.3s;
        }

        .file-upload-label:hover {
            border-color: var(--accent-blue);
            color: var(--primary-blue);
            background-color: rgba(0, 180, 216, 0.05);
        }

        /* Toast notification styles */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: var(--white);
            color: var(--primary-blue);
            padding: 15px 20px;
            border-radius: 6px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: none;
            z-index: 1000;
            max-width: 300px;
            border-left: 4px solid var(--error);
        }
        
        .toast.success {
            border-left: 4px solid var(--accent-blue);
        }
        
        .toast.show {
            display: block;
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
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

            .container {
                padding: 25px;
            }

            h1 {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <h1>Patient Registration</h1>
            
            <!-- Toast notification for errors -->
            <div id="toast" class="toast"></div>
            
            <?php
            // Initialize variables
            $success = false;
            $error = "";
           
            
            
            // Store submitted values to repopulate form if needed
            $form_data = [
                'first_name' => '',
                'last_name' => '',
                'dob' => '',
               'blood_group' => '',
                'gender' => '',
                'phone' => '',
                'email' => '',
               'address' => '',
                'username' => '',
               
            ];
            
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Store form data
                $form_data = [
                    'first_name' => $_POST['first_name'] ?? '',
                    'last_name' => $_POST['last_name'] ?? '',
                    'dob' => $_POST['dob'] ?? '',
               'blood_group' => $_POST['blood_group'] ?? '',
                    'gender' => $_POST['gender'] ?? '',
                    'phone' => $_POST['phone'] ?? '',
                    'email' => $_POST['email'] ?? '',
                  'address' => $_POST['address'] ?? '',
                    'username' => $_POST['username'] ?? '',
                    
                ];

                $errors = [];

                // ---------------- SANITIZE ----------------
                $first_name = strtoupper(trim(mysqli_real_escape_string($conn, $_POST['first_name'])));
                $last_name  = strtoupper(trim(mysqli_real_escape_string($conn, $_POST['last_name'])));
                $dob        = $_POST['dob'];
                $blood_group = $_POST['blood_group'] ?? '';
                $address    = mysqli_real_escape_string($conn, $_POST['address']);
                $gender     = $_POST['gender'] ?? '';
                $phone      = $_POST['phone'];
                $email      = $_POST['email'];
               
                $username   = $_POST['username'];
                $password   = $_POST['password'];
              

                // ---------------- FIRST NAME / LAST NAME - MUST BE CAPITAL ----------------
                if (!preg_match('/^[A-Z\s]+$/', $first_name)) {
                    $errors[] = "First name must contain only capital letters.";
                }
                if (!preg_match('/^[A-Z\s]+$/', $last_name)) {
                    $errors[] = "Last name must contain only capital letters.";
                }

                // ---------------- DATE VALIDATION ----------------
                // DOB must be BEFORE current date (not on or after)
                if (empty($dob)) {
                    $errors[] = "Date of Birth is required.";
                } else {
                    $dob_date = new DateTime($dob);
                    $today = new DateTime('today');
                    if ($dob_date >= $today) {
                        $errors[] = "Date of Birth must be before today (not today or in the future).";
                    }
                }
                
                
                
                
                // ---------------- USERNAME VALIDATION ----------------
                if (
                    !preg_match('/^[A-Z][A-Za-z0-9]*(_[A-Za-z0-9]+)*$/', $username) ||
                    strlen($username) > 20 ||
                    !preg_match('/\d/', $username)
                ) {
                    $errors[] = "Username must start with a capital letter, max 20 chars, no spaces, no consecutive underscores, not end with underscore, and include at least 1 digit (e.g. Dr_rajesh05).";
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
                    $errors[] = "e.g. abc@gmail.com";
                }

                // ---------------- FINAL INSERT ----------------
                if (empty($errors)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                   $blood_val = ($blood_group === '' || $blood_group === null) ? 'NULL' : "'$blood_group'";
                   $sql = "
                INSERT INTO patient_tbl
                (FIRST_NAME, LAST_NAME, USERNAME, PSWD, DOB, GENDER, BLOOD_GROUP, PHONE, EMAIL, ADDRESS)
                VALUES
                ('$first_name','$last_name','$username','$hashed_password','$dob','$gender',
                 $blood_val,'$phone','$email','$address')
            ";
                    if ($conn->query($sql) === TRUE) {
                        $success = true;
                        // Reset form data on successful submission
                        $form_data = [
                            'first_name' => '',
                            'last_name' => '',
                            'dob' => '',
                   
                            'gender' => '',
                            'phone' => '',
                            'email' => '',
                           

                            'username' => '',
                         

                        ];
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
                    <i class="fas fa-check-circle"></i> Registration successful! Your account has been created.
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="toast" style="display: block;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="PatientForm" enctype="multipart/form-data">
                
                <!-- First Name and Last Name -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" placeholder="e.g. John" value="<?php echo htmlspecialchars($form_data['first_name']); ?>" required>
                        <div class="error-message" id="first_name_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" placeholder="e.g.Doe" value="<?php echo htmlspecialchars($form_data['last_name']); ?>" required>
                        <div class="error-message" id="last_name_error"></div>
                    </div>
                </div>
                
                <!-- Date of Birth and Blood Group-->
                <div class="form-row">
                    <div class="form-group">
                        <label for="dob">Date of Birth <span class="required">*</span></label>
                        <input type="date" id="dob" name="dob" placeholder="YYYY-MM-DD" value="<?php echo htmlspecialchars($form_data['dob']); ?>" required>
                        <div class="error-message" id="dob_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label>Blood Group <span style="color:#888;">(Optional)</span></label>
                        <select name="blood_group">
                            <option value="">Select Blood Group (Optional)</option>
                            <option value="A+" <?php echo ($form_data['blood_group'] == 'A+') ? 'selected' : ''; ?>>A+</option>
                            <option value="A-" <?php echo ($form_data['blood_group'] == 'A-') ? 'selected' : ''; ?>>A-</option>
                            <option value="B+" <?php echo ($form_data['blood_group'] == 'B+') ? 'selected' : ''; ?>>B+</option>
                            <option value="B-" <?php echo ($form_data['blood_group'] == 'B-') ? 'selected' : ''; ?>>B-</option>
                            <option value="O+" <?php echo ($form_data['blood_group'] == 'O+') ? 'selected' : ''; ?>>O+</option>
                            <option value="O-" <?php echo ($form_data['blood_group'] == 'O-') ? 'selected' : ''; ?>>O-</option>
                            <option value="AB+" <?php echo ($form_data['blood_group'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                            <option value="AB-" <?php echo ($form_data['blood_group'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
                        </select>
                        <div class="error-message" id="blood_group_error"></div>
                    </div>
                </div>
                
                <!-- Gender -->
                <div class="form-group">
                    <label>Gender</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="male" name="gender" value="MALE" <?php echo ($form_data['gender'] == 'MALE') ? 'checked' : ''; ?>>
                            <label for="male">Male</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="female" name="gender" value="FEMALE" <?php echo ($form_data['gender'] == 'FEMALE') ? 'checked' : ''; ?>>
                            <label for="female">Female</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="other" name="gender" value="OTHER" <?php echo ($form_data['gender'] == 'OTHER') ? 'checked' : ''; ?>>
                            <label for="other">Other</label>
                        </div>
                    </div>
                </div>

                <!-- Phone Number and Email ID -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number <span class="required">*</span></label>
                        <input type="text" id="phone" name="phone" maxlength="10" placeholder="e.g 1234567891" value="<?php echo htmlspecialchars($form_data['phone']); ?>" required>
                        <div class="error-message" id="phone_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" placeholder="e.g. john@example.com" value="<?php echo htmlspecialchars($form_data['email']); ?>" required>
                        <div class="error-message" id="email_error"></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" placeholder="Street, City, Zip Code"><?php echo htmlspecialchars($form_data['address']); ?></textarea>
                    <div class="error-message" id="address_error"></div>
                </div>
                
                <!-- Username and Password -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username <span class="required">*</span></label>
                        <input type="text" id="username" name="username" placeholder="e.g. Arjun_m01" value="<?php echo htmlspecialchars($form_data['username']); ?>" required>
                        <div class="error-message" id="username_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" id="password" name="password" placeholder="John@123" required>
                            <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                        </div>
                        <div class="error-message" id="password_error"></div>
                    </div>
                </div>
                
                <div class="btn-container">
                    <button type="submit" class="btn">
                        <i class="fas fa-user-plus"></i>
                        Register
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // --- Password Toggle Functionality ---
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput = document.querySelector('#password');

    togglePassword.addEventListener('click', function (e) {
        // Toggle the type attribute
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle the eye slash icon
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    // --- Real-time Error Clearing Helper Functions ---
    
    function hideError(elementId) {
        const errorElement = document.getElementById(elementId + '_error');
        if (errorElement) {
            errorElement.style.display = "none";
            errorElement.textContent = "";
        }
    }

    function showError(elementId, message) {
        const errorElement = document.getElementById(elementId + '_error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = "block";
        }
    }

    // --- Validation Logic Functions ---

    function validateFirstName(input) {
        if (input.value.trim() !== '') {
            hideError('first_name');
        }
    }

    function validateLastName(input) {
        if (input.value.trim() !== '') {
            hideError('last_name');
        }
    }

    function validateDOB(input) {
        const value = input.value;
        if (value !== '') {
            const dobDate = new Date(value);
            const today = new Date();
            if (dobDate <= today) {
                hideError('dob');
            }
        }
    }

    function validateBloodGroup(select) {
        if (select.value !== '') {
            hideError('blood_group');
        }
    }

    function validatePhone(input) {
        if (/^\d{10}$/.test(input.value.trim())) {
            hideError('phone');
        }
    }

    function validateEmail(input) {
        if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(input.value.trim())) {
            hideError('email');
        }
    }

    function validateUsername(input) {
        const usernameRegex = /^[A-Z][A-Za-z0-9]*(_[A-Za-z0-9]+)*$/;
        const val = input.value;
        const hasDigit = /\d/.test(val);
        if (val.trim() !== '' && val.length <= 20 && usernameRegex.test(val) && hasDigit) {
            hideError('username');
        }
    }

    function validatePassword(input) {
        const val = input.value;
        if (val.length >= 8 && /[A-Z]/.test(val) && /[0-9]/.test(val) && /[\W_]/.test(val)) {
            hideError('password');
        }
    }

    // --- Attach Real-time Listeners ---
    
    document.getElementById('first_name').addEventListener('input', function() { validateFirstName(this); });
    document.getElementById('last_name').addEventListener('input', function() { validateLastName(this); });
    document.getElementById('dob').addEventListener('input', function() { validateDOB(this); });
    document.querySelector('select[name="blood_group"]').addEventListener('change', function() { validateBloodGroup(this); });
    document.getElementById('phone').addEventListener('input', function() { validatePhone(this); });
    document.getElementById('email').addEventListener('input', function() { validateEmail(this); });
    document.getElementById('username').addEventListener('input', function() { validateUsername(this); });
    document.getElementById('password').addEventListener('input', function() { validatePassword(this); });


    // --- Main Form Submission Validation ---

    document.getElementById('PatientForm').addEventListener('submit', function (e) {
        let isValid = true;
        
        // 1. Validate First Name - must be capital letters only
        const firstName = document.getElementById('first_name');
        if (firstName.value.trim() === '') {
            showError('first_name', "First name is required.");
            isValid = false;
        } else if (!/^[A-Za-z\s]+$/.test(firstName.value.trim())) {
            showError('first_name', "First name must contain only letters (stored in CAPITAL).");
            isValid = false;
        } else {
            hideError('first_name');
        }

        // 2. Validate Last Name - must be capital letters only
        const lastName = document.getElementById('last_name');
        if (lastName.value.trim() === '') {
            showError('last_name', "Last name is required.");
            isValid = false;
        } else if (!/^[A-Za-z\s]+$/.test(lastName.value.trim())) {
            showError('last_name', "Last name must contain only letters (stored in CAPITAL).");
            isValid = false;
        } else {
            hideError('last_name');
        }

        // 3. Validate Date of Birth - must be BEFORE today (not on or after)
        const dob = document.getElementById('dob');
        if (dob.value === '') {
            showError('dob', "Date of Birth is required.");
            isValid = false;
        } else {
            const dobDate = new Date(dob.value);
            const today = new Date();
            today.setHours(23, 59, 59, 999);
            if (dobDate >= today) {
                showError('dob', "Date of Birth must be before today.");
                isValid = false;
            } else {
                hideError('dob');
            }
        }

        // 4. Blood Group - optional (no validation required)
        const bloodGroup = document.querySelector('select[name="blood_group"]');
        hideError('blood_group');

        // 5. Validate Phone Number
        const phone = document.getElementById('phone');
        if (!/^\d{10}$/.test(phone.value.trim())) {
            showError('phone', "Phone number must be exactly 10 digits.");
            isValid = false;
        } else {
            hideError('phone');
        }

        // 6. Validate Email
        const email = document.getElementById('email');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
            showError('email', "e.g. abc@gmail.com");
            isValid = false;
        } else {
            hideError('email');
        }

        // 7. Validate Username
        const username = document.getElementById('username');
        const usernameRegex = /^[A-Z][A-Za-z0-9]*(_[A-Za-z0-9]+)*$/;
        if (username.value.trim() === '') {
            showError('username', "Username is required.");
            isValid = false;
        } else if (username.value.length > 20) {
            showError('username', "Username must be at most 20 characters.");
            isValid = false;
        } else if (!usernameRegex.test(username.value)) {
            showError('username', "Username must start with capital, no spaces, no consecutive underscores, not end with underscore.");
            isValid = false;
        } else if (!/\d/.test(username.value)) {
            showError('username', "Username must include at least 1 digit (e.g. Arjun_m01).");
            isValid = false;
        } else {
            hideError('username');
        }

        // 8. Validate Password
        const password = document.getElementById('password');
        if (password.value.length < 8) {
            showError('password', "Password must be at least 8 characters long.");
            isValid = false;
        } else if (!/[A-Z]/.test(password.value)) {
            showError('password', "Password must contain at least one uppercase letter.");
            isValid = false;
        } else if (!/[0-9]/.test(password.value)) {
            showError('password', "Password must contain at least one digit.");
            isValid = false;
        } else if (!/[\W_]/.test(password.value)) {
            showError('password', "Password must contain at least one special character.");
            isValid = false;
        } else {
            hideError('password');
        }

        // Prevent form submission if validation fails
        if (!isValid) {
            e.preventDefault();
            showToast("Please correct the errors in the form.");
        }
    });

    // Toast notification function
    function showToast(message, isSuccess = false) {
        const toast = document.getElementById('toast');
        toast.innerHTML = isSuccess ? 
            `<i class="fas fa-check-circle"></i> ${message}` : 
            `<i class="fas fa-exclamation-circle"></i> ${message}`;
        toast.className = isSuccess ? 'toast success show' : 'toast show';
        
        setTimeout(() => {
            toast.className = toast.className.replace('show', '');
        }, 5000);
    }
    </script>
</body>
</html>