<?php
ob_start();
include 'config.php'; 

include 'header.php';

// ---------------- VALIDATION FUNCTIONS ----------------

function qc_validate_person_name($name, &$normalized, &$error) {
    $name = trim($name);
    
    if (empty($name)) {
        $error = "Name is required.";
        return false;
    }
    
    if (strlen($name) < 2 || strlen($name) > 50) {
        $error = "Name must be between 2 and 50 characters.";
        return false;
    }
    
    if (!preg_match('/^[A-Za-z\s]+$/', $name)) {
        $error = "Name can only contain letters and spaces.";
        return false;
    }
    
    if (preg_match('/(.)\1{3,}/', $name)) {
        $error = "No character can repeat more than 3 times consecutively.";
        return false;
    }
    
    $normalized = ucwords(strtolower($name));
    return true;
}

function qc_validate_username($username, &$normalized, &$error) {
    $username = trim($username);
    
    if (empty($username)) {
        $error = "Username is required.";
        return false;
    }
    
    if (strlen($username) < 3 || strlen($username) > 30) {
        $error = "Username must be between 3 and 30 characters.";
        return false;
    }
    
    if (preg_match('/\s/', $username)) {
        $error = "Username cannot contain spaces.";
        return false;
    }
    
    if (!preg_match('/^[A-Za-z0-9_.]+$/', $username)) {
        $error = "Username can only contain letters, numbers, underscores, and dots.";
        return false;
    }
    
    if (preg_match('/^[_.]|[_.]$/', $username)) {
        $error = "Username cannot start or end with underscore or dot.";
        return false;
    }
    
    if (preg_match('/[_.]{2,}/', $username)) {
        $error = "Username cannot contain consecutive underscores or dots.";
        return false;
    }
    
    if (preg_match('/^\d+$/', $username)) {
        $error = "Username cannot be only numbers.";
        return false;
    }
    
    $reserved = ['admin', 'root', 'support', 'api', 'system'];
    if (in_array(strtolower($username), $reserved)) {
        $error = "Username is reserved. Please choose another one.";
        return false;
    }
    
    $normalized = strtolower($username);
    return true;
}

function qc_validate_password($password, $username, $email, &$error) {
    if (empty($password)) {
        $error = "Password is required.";
        return false;
    }
    
    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
        return false;
    }
    
    if (!preg_match('/^[A-Z]/', $password)) {
        $error = "Password must start with a capital letter.";
        return false;
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $error = "Password must contain at least one uppercase letter.";
        return false;
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $error = "Password must contain at least one lowercase letter.";
        return false;
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $error = "Password must contain at least one digit.";
        return false;
    }
    
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        $error = "Password must contain at least one special character.";
        return false;
    }
    
    if (strtolower($password) === strtolower($username)) {
        $error = "Password cannot be same as username.";
        return false;
    }
    
    if (strtolower($password) === strtolower($email)) {
        $error = "Password cannot be same as email.";
        return false;
    }
    
    return true;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration | QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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
            background: linear-gradient(135deg, var(--light-blue) 0%, var(--card-bg) 100%);
            min-height: 100vh;
            display: flex;
            padding-top: 40px;
            padding-bottom: 30px;
        }

        /* Main Content */
        .main-content {
            margin-left: 150px;
            width: calc(100% - 250px);
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        /* Form Container */
        .container {
            background-color: var(--white);
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 1400px;
            padding: 20px 30px 22px 30px;
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
            margin-bottom: 16px;
            font-size: 24px;
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

        /* Multi-column form layout */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 16px;
        }

        .form-section {
            margin-bottom: 0;
        }

        .form-section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--light-blue);
            letter-spacing: 0.5px;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .form-group {
            margin-bottom: 10px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 4px;
            color: var(--primary-blue);
            font-weight: 600;
            font-size: 13px;
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
            padding: 8px 10px;
            border: 1px solid var(--gray-blue);
            border-radius: 6px;
            font-size: 13px;
            transition: all 0.3s ease;
            background-color: var(--white);
        }

        textarea {
            resize: none;
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
            min-height: 70px;
            max-height: 100px;
        }

        .radio-group {
            display: flex;
            gap: 15px;
            margin-top: 6px;
            flex-wrap: wrap;
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
            margin-top: 10px;
            grid-column: 1 / -1;
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
        @media (max-width: 1200px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .container {
                padding: 25px;
                max-width: 100%;
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
            $field_errors = [
                'first_name' => '', 'last_name' => '', 'dob' => '', 'blood_group' => '',
                'phone' => '', 'email' => '', 'address' => '',
                'username' => '', 'password' => '', 
                'security_question' => '', 'security_answer' => ''
            ];
            
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
                'security_question' => '',
                'security_answer' => ''
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
                    'security_question' => $_POST['security_question'] ?? '',
                    'security_answer' => $_POST['security_answer'] ?? ''
                ];

                // ---------------- SANITIZE ----------------
                $first_name_raw = $_POST['first_name'] ?? '';
                $last_name_raw  = $_POST['last_name'] ?? '';
                $dob        = $_POST['dob'];
                $blood_group = $_POST['blood_group'] ?? '';
                $address    = mysqli_real_escape_string($conn, $_POST['address']);
                $gender            = $_POST['gender'] ?? '';
                $phone             = $_POST['phone'];
                $email             = $_POST['email'];
                $username          = $_POST['username'];
                $password          = $_POST['password'];
                $security_question = $_POST['security_question'] ?? '';
                $security_answer   = trim($_POST['security_answer'] ?? '');

              

                // ---------------- DATE VALIDATION ----------------
                if (empty($dob)) {
                    $field_errors['dob'] = "Date of Birth is required.";
                } else {
                    $dob_date = new DateTime($dob);
                    $today = new DateTime('today');
                    if ($dob_date >= $today) {
                        $field_errors['dob'] = "Date of Birth must be before today (not today or in the future).";
                    }
                }

                // ---------------- USERNAME VALIDATION ----------------
                $usernameNormalized = '';
                $usernameError = '';
                if (!qc_validate_username($username, $usernameNormalized, $usernameError)) {
                    $field_errors['username'] = $usernameError;
                } else {
                    $username = $usernameNormalized;
                }
                
                // ---------------- PASSWORD VALIDATION ----------------
                $passwordError = '';
                $emailLower = strtolower((string)trim($email));
                if (!qc_validate_password($password, $username, $emailLower, $passwordError)) {
                    $field_errors['passwor  d'] = $passwordError;
                }

                // ---------------- PHONE VALIDATION ----------------
                if (!preg_match('/^[0-9]{10}$/', $phone)) {
                    $field_errors['phone'] = "Phone number must be exactly 10 digits.";
                }

                // ---------------- EMAIL VALIDATION ----------------
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $field_errors['email'] = "e.g. vinod.sharma@example.com";
                }

                // ---------------- SECURITY QUESTION / ANSWER VALIDATION ----------------
                if (empty($security_question)) {
                    $field_errors['security_question'] = "Please select a security question.";
                }
                if ($security_answer === '') {
                    $field_errors['security_answer'] = "Please provide an answer to the security question.";
                }

                // ---------------- USERNAME EXISTS CHECK (patient_tbl) ----------------
                if (empty($field_errors['username'])) {
                    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM patient_tbl WHERE LOWER(USERNAME) = ?");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $check_result = $stmt->get_result();
                    if ($check_result && $check_result->fetch_assoc()['cnt'] > 0) {
                        $field_errors['username'] = "Username already exists. Please choose another one.";
                    }
                    $stmt->close();
                }

                // ---------------- FINAL INSERT ----------------
                if (empty(array_filter($field_errors))) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    $blood_val = ($blood_group === '' || $blood_group === null) ? 'NULL' : "'$blood_group'";
                  
                    $sec_q = mysqli_real_escape_string($conn, $security_question);
                    $sec_a = mysqli_real_escape_string($conn, $security_answer);
                   
                    $sql = "
                        INSERT INTO patient_tbl
                        (FIRST_NAME, LAST_NAME, USERNAME, PSWD, DOB, GENDER, BLOOD_GROUP, PHONE, EMAIL, ADDRESS, SECURITY_QUESTION, SECURITY_ANSWER)
                        VALUES
                        ('$first_name','$last_name','$username','$hashed_password','$dob','$gender',
                         $blood_val,'$phone','$email','$address','$sec_q','$sec_a')
                    ";
                    if ($conn->query($sql) === TRUE) {
                        $success = true;
                        // ✅ FIXED: Removed duplicate $conn->close() from here
                    } else {
                        $error = "Database error: " . mysqli_error($conn);
                    }
                }

                // ✅ FIXED: Single $conn->close() at the end — called only once
                $conn->close();
            }
            ?>
            
            <?php if ($success): ?>
                <div class="success-message" style="display: block;">
                    <i class="fas fa-check-circle"></i> Registration successful! Your account has been created.
                </div>
                <script>
                    // Show success message for 3 seconds, then redirect
                    setTimeout(function() {
                        window.location.href = 'book_appointment_login.php';
                    }, 3000);
                </script>
            <?php endif; ?>
            
            <?php if (!empty($error)): ?>
                <div class="toast" style="display: block;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="PatientForm" enctype="multipart/form-data">
                <div class="form-grid">
                    <!-- Column 1: Personal Details -->
                    <div class="form-section">
                        <div class="form-section-title">Personal Details</div>
                        
                        <div class="form-group">
                            <label for="first_name">First Name <span class="required">*</span></label>
                            <input type="text" id="first_name" name="first_name" placeholder="e.g. Vinod" value="<?php echo htmlspecialchars($form_data['first_name']); ?>" required>
                            <div class="error-message" id="first_name_error"<?php if (!empty($field_errors['first_name'])) echo ' style="display:block"'; ?>><?php echo htmlspecialchars($field_errors['first_name'] ?? ''); ?></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Last Name <span class="required">*</span></label>
                            <input type="text" id="last_name" name="last_name" placeholder="e.g. Sharma" value="<?php echo htmlspecialchars($form_data['last_name']); ?>" required>
                            <div class="error-message" id="last_name_error"<?php if (!empty($field_errors['last_name'])) echo ' style="display:block"'; ?>><?php echo htmlspecialchars($field_errors['last_name'] ?? ''); ?></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="dob">Date of Birth <span class="required">*</span></label>
                            <input type="date" id="dob" name="dob" placeholder="YYYY-MM-DD" value="<?php echo htmlspecialchars($form_data['dob']); ?>" required>
                            <div class="error-message" id="dob_error"<?php if (!empty($field_errors['dob'])) echo ' style="display:block"'; ?>><?php echo htmlspecialchars($field_errors['dob'] ?? ''); ?></div>
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
                    </div>
                    
                    <!-- Column 2: Contact Details -->
                    <div class="form-section">
                        <div class="form-section-title">Contact Details</div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number <span class="required">*</span></label>
                            <input type="text" id="phone" name="phone" maxlength="10" placeholder="e.g. 9876543210" value="<?php echo htmlspecialchars($form_data['phone']); ?>" required>
                            <div class="error-message" id="phone_error"<?php if (!empty($field_errors['phone'])) echo ' style="display:block"'; ?>><?php echo htmlspecialchars($field_errors['phone'] ?? ''); ?></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email <span class="required">*</span></label>
                            <input type="email" id="email" name="email" placeholder="e.g. vinod.sharma@example.com" value="<?php echo htmlspecialchars($form_data['email']); ?>" required>
                            <div class="error-message" id="email_error"<?php if (!empty($field_errors['email'])) echo ' style="display:block"'; ?>><?php echo htmlspecialchars($field_errors['email'] ?? ''); ?></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea name="address" id="address" placeholder="Street, City, Zip Code"><?php echo htmlspecialchars($form_data['address']); ?></textarea>
                            <div class="error-message" id="address_error"></div>
                        </div>
                    </div>
                    
                    <!-- Column 3: Account Details -->
                    <div class="form-section">
                        <div class="form-section-title">Account Details</div>
                        
                        <div class="form-group">
                            <label for="username">Username <span class="required">*</span></label>
                            <input type="text" id="username" name="username" placeholder="e.g. Vinod_Sharma01" value="<?php echo htmlspecialchars($form_data['username']); ?>" required>
                            <div class="error-message" id="username_error"<?php if (!empty($field_errors['username'])) echo ' style="display:block"'; ?>><?php echo htmlspecialchars($field_errors['username'] ?? ''); ?></div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password <span class="required">*</span></label>
                            <div class="password-wrapper">
                                <input type="password" id="password" name="password" placeholder="vinod@123" required>
                                <i class="fas fa-eye-slash toggle-password" id="togglePassword"></i>
                            </div>
                            <div class="error-message" id="password_error"<?php if (!empty($field_errors['password'])) echo ' style="display:block"'; ?>><?php echo htmlspecialchars($field_errors['password'] ?? ''); ?></div>
                        </div>

                        <div class="form-group">
                            <label for="security_question">Security Question <span class="required">*</span></label>
                            <select id="security_question" name="security_question" required>
                                <option value="">Select a security question</option>
                                <option value="What was the name of your first school?" <?php echo ($form_data['security_question'] == 'What was the name of your first school?') ? 'selected' : ''; ?>>What was the name of your first school?</option>
                                <option value="What is your favorite food from childhood?" <?php echo ($form_data['security_question'] == 'What is your favorite food from childhood?') ? 'selected' : ''; ?>>What is your favorite food from childhood?</option>
                                <option value="Where did you go for your first school trip?" <?php echo ($form_data['security_question'] == 'Where did you go for your first school trip?') ? 'selected' : ''; ?>>Where did you go for your first school trip?</option>
                                <option value="What was the nickname your family calls you?" <?php echo ($form_data['security_question'] == 'What was the nickname your family calls you?') ? 'selected' : ''; ?>>What was the nickname your family calls you?</option>
                            </select>
                            <div class="error-message" id="security_question_error"<?php if (!empty($field_errors['security_question'])) echo ' style="display:block"'; ?>><?php echo htmlspecialchars($field_errors['security_question'] ?? ''); ?></div>
                        </div>

                        <div class="form-group">
                            <label for="security_answer">Security Answer <span class="required">*</span></label>
                            <input type="text" id="security_answer" name="security_answer" placeholder="Your answer" value="<?php echo htmlspecialchars($form_data['security_answer']); ?>" required>
                            <div class="error-message" id="security_answer_error"<?php if (!empty($field_errors['security_answer'])) echo ' style="display:block"'; ?>><?php echo htmlspecialchars($field_errors['security_answer'] ?? ''); ?></div>
                        </div>
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

    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            const isHidden = passwordInput.getAttribute('type') === 'password';
            if (isHidden) {
                passwordInput.setAttribute('type', 'text');
                this.classList.remove('fa-eye-slash');
                this.classList.add('fa-eye');
            } else {
                passwordInput.setAttribute('type', 'password');
                this.classList.remove('fa-eye');
                this.classList.add('fa-eye-slash');
            }
        });
    }

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
        const val = input.value.trim();
        const nameRegex = /^[A-Za-z]+$/;
        if (val.length >= 2 && val.length <= 50 && nameRegex.test(val) && !/(.)\1{3,}/.test(val)) {
            hideError('first_name');
        }
    }

    function validateLastName(input) {
        const val = input.value.trim();
        const nameRegex = /^[A-Za-z]+$/;
        if (val.length >= 2 && val.length <= 50 && nameRegex.test(val) && !/(.)\1{3,}/.test(val)) {
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
        const val = input.value.trim();
        const reserved = ['admin', 'root', 'support', 'api', 'system'];
        const lower = val.toLowerCase();

        const usernameRegex = /^[A-Za-z0-9_.]+$/;
        const isNumericOnly = /^\d+$/.test(val);
        const isValid =
            val.length >= 3 &&
            val.length <= 30 &&
            !/\s/.test(val) &&
            usernameRegex.test(val) &&
            !/^[_.]|[_.]$/.test(val) &&
            !/[_.]{2,}/.test(val) &&
            !isNumericOnly &&
            reserved.indexOf(lower) === -1;

        if (val !== '' && isValid) {
            hideError('username');
        }
    }

    function validatePassword(input) {
        const val = input.value;
        const specialRegex = /[^A-Za-z0-9]/;
        const hasUpper = /[A-Z]/.test(val);
        const hasLower = /[a-z]/.test(val);
        const hasDigit = /[0-9]/.test(val);
        const hasSpecial = specialRegex.test(val);
        const startsWithCapital = /^[A-Z]/.test(val);

        if (val.length >= 8 && startsWithCapital && hasUpper && hasLower && hasDigit && hasSpecial) {
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
    document.getElementById('security_question').addEventListener('change', function() { 
        if (this.value !== '') hideError('security_question'); 
    });
    document.getElementById('security_answer').addEventListener('input', function() { 
        if (this.value.trim() !== '') hideError('security_answer'); 
    });

    // --- Main Form Submission Validation ---

    document.getElementById('PatientForm').addEventListener('submit', function (e) {
        let isValid = true;
        
        // 1. Validate First Name
        const firstName = document.getElementById('first_name');
        if (firstName.value.trim() === '') {
            showError('first_name', "First name is required.");
            isValid = false;
        } else if (!/^[A-Za-z]+$/.test(firstName.value.trim()) || firstName.value.trim().length < 2 || firstName.value.trim().length > 50 || /(.)\1{3,}/.test(firstName.value.trim())) {
            showError('first_name', "First name must be 2–50 letters only and no letter can repeat more than 3 times consecutively.");
            isValid = false;
        } else {
            hideError('first_name');
        }

        // 2. Validate Last Name
        const lastName = document.getElementById('last_name');
        if (lastName.value.trim() === '') {
            showError('last_name', "Last name is required.");
            isValid = false;
        } else if (!/^[A-Za-z]+$/.test(lastName.value.trim()) || lastName.value.trim().length < 2 || lastName.value.trim().length > 50 || /(.)\1{3,}/.test(lastName.value.trim())) {
            showError('last_name', "Last name must be 2–50 letters only and no letter can repeat more than 3 times consecutively.");
            isValid = false;
        } else {
            hideError('last_name');
        }

        // 3. Validate Date of Birth
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

        // 4. Blood Group - optional
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
            showError('email', "e.g. vinod.sharma@example.com");
            isValid = false;
        } else {
            hideError('email');
        }

        // 7. Validate Username
        const username = document.getElementById('username');
        const unameVal = username.value.trim();
        const reserved = ['admin', 'root', 'support', 'api', 'system'];
        const lower = unameVal.toLowerCase();

        const usernameRegex = /^[A-Za-z0-9_.]+$/;
        const isNumericOnly = /^\d+$/.test(unameVal);
        const isValidUsername =
            unameVal.length >= 3 &&
            unameVal.length <= 30 &&
            !/\s/.test(unameVal) &&
            usernameRegex.test(unameVal) &&
            !/^[_.]|[_.]$/.test(unameVal) &&
            !/[_.]{2,}/.test(unameVal) &&
            !isNumericOnly &&
            reserved.indexOf(lower) === -1;

        if (unameVal === '') {
            showError('username', "Username is required.");
            isValid = false;
        } else if (!isValidUsername) {
            showError('username', "Invalid username. Use 3–30 chars with letters/numbers and _ or ., no spaces, no starting/ending with _/., and no consecutive _/.");
            isValid = false;
        } else {
            hideError('username');
        }

        // 8. Validate Password
        const password = document.getElementById('password');
        const pw = password.value;
        const specialRegex = /[^A-Za-z0-9]/;

        if (pw.length < 8) {
            showError('password', "Password must be at least 8 characters long.");
            isValid = false;
        } else if (!/^[A-Z]/.test(pw)) {
            showError('password', "Password must start with a capital letter (A-Z).");
            isValid = false;
        } else if (!/[A-Z]/.test(pw)) {
            showError('password', "Password must contain at least one uppercase letter (A-Z).");
            isValid = false;
        } else if (!/[a-z]/.test(pw)) {
            showError('password', "Password must contain at least one lowercase letter (a-z).");
            isValid = false;
        } else if (!/[0-9]/.test(pw)) {
            showError('password', "Password must contain at least one digit.");
            isValid = false;
        } else if (!specialRegex.test(pw)) {
            showError('password', "Password must contain at least one special character.");
            isValid = false;
        } else {
            hideError('password');
        }

        // 9. Validate Security Question
        const secQ = document.getElementById('security_question');
        if (secQ.value === '') {
            showError('security_question', "Please select a security question.");
            isValid = false;
        } else {
            hideError('security_question');
        }

        // 10. Validate Security Answer
        const secA = document.getElementById('security_answer');
        if (secA.value.trim() === '') {
            showError('security_answer', "Please provide an answer to the security question.");
            isValid = false;
        } else {
            hideError('security_answer');
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

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#dob", {
                dateFormat: "Y-m-d",
                maxDate: "today",
                allowInput: true,
                disableMobile: "true"
            });
        });
    </script>
</body>
</html>