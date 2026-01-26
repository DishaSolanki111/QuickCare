<?php include 'config.php'; 
include 'header.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration | QuickCare</title>
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
        
        /* Toast notification styles */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #fff;
            color: #333;
            padding: 15px 25px;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: none;
            z-index: 1000;
            max-width: 300px;
            border-left: 4px solid var(--error);
        }
        
        .toast.success {
            border-left: 4px solid var(--accent-color);
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
                $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
                $last_name  = mysqli_real_escape_string($conn, $_POST['last_name']);
                $dob        = $_POST['dob'];
                $blood_group = $_POST['blood_group'] ?? '';
                $address    = mysqli_real_escape_string($conn, $_POST['address']);
                $gender     = $_POST['gender'] ?? '';
                $phone      = $_POST['phone'];
                $email      = $_POST['email'];
               
                $username   = $_POST['username'];
                $password   = $_POST['password'];
              

                // ---------------- DATE VALIDATION ----------------
                // Validate DOB is not empty and not in the future
                if (empty($dob)) {
                    $errors[] = "Date of Birth is required.";
                } else {
                    $dob_date = new DateTime($dob);
                    $today = new DateTime();
                    if ($dob_date > $today) {
                        $errors[] = "Date of Birth cannot be in the future.";
                    }
                }
                
                
                
                
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

                   $sql = "
                INSERT INTO patient_tbl
                (FIRST_NAME, LAST_NAME, USERNAME, PSWD, DOB, GENDER, BLOOD_GROUP, PHONE, EMAIL, ADDRESS)
                VALUES
                ('$first_name','$last_name','$username','$hashed_password','$dob','$gender',
                 '$blood_group','$phone','$email','$address')
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
                    Registration successful!
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="error-message" style="display: block;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="PatientForm" enctype="multipart/form-data">
                <!-- First Name and Last Name -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($form_data['first_name']); ?>" required>
                        <div class="error-message" id="first_name_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($form_data['last_name']); ?>" required>
                        <div class="error-message" id="last_name_error"></div>
                    </div>
                </div>
                
                <!-- Date of Birth and Blood Group-->
                <div class="form-row">
                    <div class="form-group">
                        <label for="dob">Date of Birth <span class="required">*</span></label>
                        <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($form_data['dob']); ?>" required>
                        <div class="error-message" id="dob_error"></div>
                    </div>
                     <div class="form-group">
        <label>Blood Group<span class="required">*</span></label>
        <select name="blood_group">
            <option value="">Select Blood Group</option>
            <option value="A+" <?php echo ($form_data['blood_group'] == 'A+') ? 'selected' : ''; ?>>A+</option>
            <option value="A-" <?php echo ($form_data['blood_group'] == 'A-') ? 'selected' : ''; ?>>A-</option>
            <option value="B+" <?php echo ($form_data['blood_group'] == 'B+') ? 'selected' : ''; ?>>B+</option>
            <option value="B-" <?php echo ($form_data['blood_group'] == 'B-') ? 'selected' : ''; ?>>B-</option>
            <option value="O+" <?php echo ($form_data['blood_group'] == 'O+') ? 'selected' : ''; ?>>O+</option>
            <option value="O-" <?php echo ($form_data['blood_group'] == 'O-') ? 'selected' : ''; ?>>O-</option>
            <option value="AB+" <?php echo ($form_data['blood_group'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
            <option value="AB-" <?php echo ($form_data['blood_group'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
        </select>
        <div class="field-error" id="blood_group_error"></div>
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
                        <input type="text" id="phone" name="phone" maxlength="10" value="<?php echo htmlspecialchars($form_data['phone']); ?>" required>
                        <div class="error-message" id="phone_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email']); ?>" required>
                        <div class="error-message" id="email_error"></div>
                    </div>
                </div>
                
               
                
               
                
                <!-- Username and Password -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username <span class="required">*</span></label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($form_data['username']); ?>" required>
                        <div class="error-message" id="username_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <input type="password" id="password" name="password" required>
                        <div class="error-message" id="password_error"></div>
                    </div>
                </div>
                <div class="form-group">
                     <label>Address</label>
                     <textarea name="address"><?php echo htmlspecialchars($form_data['address']); ?></textarea>
                     <div class="field-error" id="address_error"></div>
</div>
                <div class="btn-container">
                    <button type="submit" class="btn">Register</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Toast notification function
    function showToast(message, isSuccess = false) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = isSuccess ? 'toast success show' : 'toast show';
        
        setTimeout(() => {
            toast.className = toast.className.replace('show', '');
        }, 5000);
    }

   

    // Form validation and submission
    document.getElementById('PatientForm').addEventListener('submit', function (e) {
        let isValid = true;
        
        // Reset all error messages
        const errorElements = document.querySelectorAll('.error-message');
        errorElements.forEach(element => {
            element.style.display = "none";
            element.textContent = "";
        });

        // Validate First Name
        const firstName = document.getElementById('first_name');
        if (firstName.value.trim() === '') {
            const errorElement = document.getElementById('first_name_error');
            errorElement.textContent = "First name is required.";
            errorElement.style.display = "block";
            isValid = false;
        }

        // Validate Last Name
        const lastName = document.getElementById('last_name');
        if (lastName.value.trim() === '') {
            const errorElement = document.getElementById('last_name_error');
            errorElement.textContent = "Last name is required.";
            errorElement.style.display = "block";
            isValid = false;
        }

        // Validate Date of Birth
        const dob = document.getElementById('dob');
        const dobValue = dob.value;
        if (dobValue === '') {
            const errorElement = document.getElementById('dob_error');
            errorElement.textContent = "Date of Birth is required.";
            errorElement.style.display = "block";
            isValid = false;
        } else {
            const dobDate = new Date(dobValue);
            const today = new Date();
            if (dobDate > today) {
                const errorElement = document.getElementById('dob_error');
                errorElement.textContent = "Date of Birth cannot be in the future.";
                errorElement.style.display = "block";
                isValid = false;
            }
        }

        
        }

        

        // Validate Phone Number
        const phone = document.getElementById('phone');
        if (!/^\d{10}$/.test(phone.value.trim())) {
            const errorElement = document.getElementById('phone_error');
            errorElement.textContent = "Phone number must be exactly 10 digits.";
            errorElement.style.display = "block";
            isValid = false;
        }

        // Validate Email
        const email = document.getElementById('email');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
            const errorElement = document.getElementById('email_error');
            errorElement.textContent = "Invalid email format.";
            errorElement.style.display = "block";
            isValid = false;
        }

       

        // Validate Username
        const username = document.getElementById('username');
        const usernameRegex = /^[A-Z][A-Za-z0-9]*(_[A-Za-z0-9]+)*$/;
        if (username.value.trim() === '') {
            const errorElement = document.getElementById('username_error');
            errorElement.textContent = "Username is required.";
            errorElement.style.display = "block";
            isValid = false;
        } else if (username.value.length > 20) {
            const errorElement = document.getElementById('username_error');
            errorElement.textContent = "Username must be at most 20 characters.";
            errorElement.style.display = "block";
            isValid = false;
        } else if (!usernameRegex.test(username.value)) {
            const errorElement = document.getElementById('username_error');
            errorElement.textContent = "Username must start with a capital letter, no spaces, no consecutive underscores, and not end with underscore.";
            errorElement.style.display = "block";
            isValid = false;
        }

        // Validate Password
        const password = document.getElementById('password');
        if (password.value.length < 8) {
            const errorElement = document.getElementById('password_error');
            errorElement.textContent = "Password must be at least 8 characters long.";
            errorElement.style.display = "block";
            isValid = false;
        } else if (!/[A-Z]/.test(password.value)) {
            const errorElement = document.getElementById('password_error');
            errorElement.textContent = "Password must contain at least one uppercase letter.";
            errorElement.style.display = "block";
            isValid = false;
        } else if (!/[0-9]/.test(password.value)) {
            const errorElement = document.getElementById('password_error');
            errorElement.textContent = "Password must contain at least one digit.";
            errorElement.style.display = "block";
            isValid = false;
        } else if (!/[\W_]/.test(password.value)) {
            const errorElement = document.getElementById('password_error');
            errorElement.textContent = "Password must contain at least one special character.";
            errorElement.style.display = "block";
            isValid = false;
        }

        // Prevent form submission if validation fails
        if (!isValid) {
            e.preventDefault();
            // Show a toast notification for general validation error
            showToast("Please correct the errors in the form.");
        }
    });
    </script>
</body>
</html>