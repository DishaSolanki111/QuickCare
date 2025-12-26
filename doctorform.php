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
            
            // Check if form is submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Handle profile image upload
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                    $upload_dir = "uploads/";
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $file_name = time() . '_' . basename($_FILES['profile_image']['name']);
                    $target_file = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                        $profile_image_path = $target_file;
                    }
                }
                
                // Sanitize and validate inputs
                $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
                $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
                $dob = mysqli_real_escape_string($conn, $_POST['dob']);
                $doj = mysqli_real_escape_string($conn, $_POST['doj']);
                $gender = mysqli_real_escape_string($conn, $_POST['gender']);
                $phone = mysqli_real_escape_string($conn, $_POST['phone']);
                $email = mysqli_real_escape_string($conn, $_POST['email']);
                $specialisation_id = mysqli_real_escape_string($conn, $_POST['specialisation_id']);
                $username = mysqli_real_escape_string($conn, $_POST['username']);
                $password = mysqli_real_escape_string($conn, $_POST['password']);
                
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // SQL to insert data
                $sql = "INSERT INTO doctor_tbl (SPECIALISATION_ID, PROFILE_IMAGE, FIRST_NAME, LAST_NAME, DOB, DOJ, USERNAME, PSWD, PHONE, EMAIL, GENDER) 
                        VALUES ('$specialisation_id', '$profile_image_path', '$first_name', '$last_name', '$dob', '$doj', '$username', '$hashed_password', '$phone', '$email', '$gender')";
                
                if ($conn->query($sql) === TRUE) {
                    $success = true;
                } else {
                    $error = "Error: " . $sql . "<br>" . $conn->error;
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
        // Handle file upload label update
        document.getElementById('profile_image').addEventListener('change', function() {
            const fileName = this.files[0]?.name || 'Choose Profile Image';
            document.getElementById('file-label').textContent = fileName;
        });
        
        document.getElementById('doctorForm').addEventListener('submit', function(event) {
            let isValid = true;
            
            // Validate required fields
            const requiredFields = ['first_name', 'last_name', 'phone', 'email', 'specialisation_id', 'username', 'password'];
            
            requiredFields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                const errorElement = document.getElementById(fieldId + '_error');
                
                if (!field.value.trim()) {
                    errorElement.textContent = 'This field is required';
                    errorElement.style.display = 'block';
                    isValid = false;
                } else {
                    errorElement.style.display = 'none';
                }
            });
            
            // Validate email format
            const emailField = document.getElementById('email');
            const emailError = document.getElementById('email_error');
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (emailField.value && !emailPattern.test(emailField.value)) {
                emailError.textContent = 'Invalid email format';
                emailError.style.display = 'block';
                isValid = false;
            }
            
            // Validate phone number
            const phoneField = document.getElementById('phone');
            const phoneError = document.getElementById('phone_error');
            
            if (phoneField.value && (phoneField.value.length < 10 || phoneField.value.length > 15)) {
                phoneError.textContent = 'Invalid phone number';
                phoneError.style.display = 'block';
                isValid = false;
            }
            
            // Validate password length
            const passwordField = document.getElementById('password');
            const passwordError = document.getElementById('password_error');
            
            if (passwordField.value && passwordField.value.length < 6) {
                passwordError.textContent = 'Password must be at least 6 characters';
                passwordError.style.display = 'block';
                isValid = false;
            }
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>