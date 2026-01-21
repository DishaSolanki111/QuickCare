<?php
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Registration | QuickCare</title>
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
            --sidebar-bg: #072D44;
            --sidebar-hover: #064469;
            --sidebar-active: #9CCDD8;
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
            position: relative;
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
        
        input.error, select.error, textarea.error {
            border-color: var(--error);
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
        }
    </style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <h1>Receptionist Registration</h1>
            
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
                'doj' => '',
                'gender' => '',
                'phone' => '',
                'email' => '',
                'username' => '',
                'password' => '',
                'address' => ''
            ];
            
            // Check if form is submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Store form data
                $form_data = [
                    'first_name' => $_POST['first_name'] ?? '',
                    'last_name' => $_POST['last_name'] ?? '',
                    'dob' => $_POST['dob'] ?? '',
                    'doj' => $_POST['doj'] ?? '',
                    'gender' => $_POST['gender'] ?? '',
                    'phone' => $_POST['phone'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'username' => $_POST['username'] ?? '',
                    'password' => $_POST['password'] ?? '',
                    'address' => $_POST['address'] ?? ''
                ];
                
                // Validation
                $errors = [];
                
                // Validate required fields
                if (empty($form_data['first_name'])) {
                    $errors['first_name'] = 'First name is required';
                }
                
                if (empty($form_data['last_name'])) {
                    $errors['last_name'] = 'Last name is required';
                }
                
                if (empty($form_data['username'])) {
                    $errors['username'] = 'Username is required';
                }
                
                if (empty($form_data['password'])) {
                    $errors['password'] = 'Password is required';
                } elseif (strlen($form_data['password']) < 6) {
                    $errors['password'] = 'Password must be at least 6 characters';
                }
                
                // Validate email format if provided
                if (!empty($form_data['email'])) {
                    if (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
                        $errors['email'] = 'Invalid email format';
                    }
                }
                
                // Validate phone number if provided
                if (!empty($form_data['phone'])) {
                    if (strlen($form_data['phone']) < 10 || strlen($form_data['phone']) > 15) {
                        $errors['phone'] = 'Invalid phone number';
                    }
                }
                
                // If no errors, proceed with database insertion
                if (empty($errors)) {
                    // Sanitize inputs
                    $first_name = mysqli_real_escape_string($conn, $form_data['first_name']);
                    $last_name = mysqli_real_escape_string($conn, $form_data['last_name']);
                    $dob = mysqli_real_escape_string($conn, $form_data['dob']);
                    $doj = mysqli_real_escape_string($conn, $form_data['doj']);
                    $gender = mysqli_real_escape_string($conn, $form_data['gender']);
                    $phone = mysqli_real_escape_string($conn, $form_data['phone']);
                    $email = mysqli_real_escape_string($conn, $form_data['email']);
                    $username = mysqli_real_escape_string($conn, $form_data['username']);
                    $password = $form_data['password'];
                    $address = mysqli_real_escape_string($conn, $form_data['address']);
                    
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // SQL to insert data
                    $sql = "INSERT INTO receptionist_tbl (FIRST_NAME, LAST_NAME, DOB, DOJ, GENDER, PHONE, EMAIL, USERNAME, PSWD, ADDRESS) 
                            VALUES ('$first_name', '$last_name', '$dob', '$doj', '$gender', '$phone', '$email', '$username', '$hashed_password', '$address')";
                    
                    if ($conn->query($sql) === TRUE) {
                        $success = true;
                        // Reset form data on successful submission
                        $form_data = [
                            'first_name' => '',
                            'last_name' => '',
                            'dob' => '',
                            'doj' => '',
                            'gender' => '',
                            'phone' => '',
                            'email' => '',
                            'username' => '',
                            'password' => '',
                            'address' => ''
                        ];
                    } else {
                        $error = "Error: " . $sql . "<br>" . $conn->error;
                    }
                    
                    $conn->close();
                }
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
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="receptionistForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name <span class="required">*</span></label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($form_data['first_name']); ?>" required>
                        <div class="error-message" id="first_name_error"><?php echo $errors['first_name'] ?? ''; ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name <span class="required">*</span></label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($form_data['last_name']); ?>" required>
                        <div class="error-message" id="last_name_error"><?php echo $errors['last_name'] ?? ''; ?></div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($form_data['dob']); ?>">
                        <div class="error-message" id="dob_error"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="doj">Date of Joining</label>
                        <input type="date" id="doj" name="doj" value="<?php echo htmlspecialchars($form_data['doj']); ?>">
                        <div class="error-message" id="doj_error"></div>
                    </div>
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
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($form_data['phone']); ?>">
                        <div class="error-message" id="phone_error"><?php echo $errors['phone'] ?? ''; ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_data['email']); ?>">
                        <div class="error-message" id="email_error"><?php echo $errors['email'] ?? ''; ?></div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Username <span class="required">*</span></label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($form_data['username']); ?>" required>
                        <div class="error-message" id="username_error"><?php echo $errors['username'] ?? ''; ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password <span class="required">*</span></label>
                        <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($form_data['password']); ?>" required>
                        <div class="error-message" id="password_error"><?php echo $errors['password'] ?? ''; ?></div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"><?php echo htmlspecialchars($form_data['address']); ?></textarea>
                    <div class="error-message" id="address_error"></div>
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
        document.getElementById('receptionistForm').addEventListener('submit', function(event) {
            let isValid = true;
            
            // Reset all error messages and input styles
            const errorElements = document.querySelectorAll('.error-message');
            errorElements.forEach(element => {
                element.style.display = 'none';
            });
            
            const inputElements = document.querySelectorAll('input, select, textarea');
            inputElements.forEach(element => {
                element.classList.remove('error');
            });
            
            // Validate required fields
            const requiredFields = ['first_name', 'last_name', 'username', 'password'];
            
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                const errorElement = document.getElementById(fieldId + '_error');
                
                if (!field.value.trim()) {
                    errorElement.textContent = 'This field is required';
                    errorElement.style.display = 'block';
                    field.classList.add('error');
                    isValid = false;
                }
            });
            
            // Validate email format if provided
            const emailField = document.getElementById('email');
            const emailError = document.getElementById('email_error');
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (emailField.value && !emailPattern.test(emailField.value)) {
                emailError.textContent = 'Invalid email format';
                emailError.style.display = 'block';
                emailField.classList.add('error');
                isValid = false;
            }
            
            // Validate phone number if provided
            const phoneField = document.getElementById('phone');
            const phoneError = document.getElementById('phone_error');
            
            if (phoneField.value && (phoneField.value.length < 10 || phoneField.value.length > 15)) {
                phoneError.textContent = 'Invalid phone number';
                phoneError.style.display = 'block';
                phoneField.classList.add('error');
                isValid = false;
            }
            
            // Validate password length
            const passwordField = document.getElementById('password');
            const passwordError = document.getElementById('password_error');
            
            if (passwordField.value && passwordField.value.length < 6) {
                passwordError.textContent = 'Password must be at least 6 characters';
                passwordError.style.display = 'block';
                passwordField.classList.add('error');
                isValid = false;
            }
            
            if (!isValid) {
                event.preventDefault();
                showToast('Please correct the errors in the form.');
            }
        });
    </script>
</body>
</html>