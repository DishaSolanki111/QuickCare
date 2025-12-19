<?php
include 'config.php';
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Registration</title>
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
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

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

        .required {
            color: var(--error);
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Receptionist Registration</h1>
        
        <?php
        // // Database connection
        // $servername = "localhost";
        // $username = "username";
        // $password = "password";
        // $dbname = "database_name";
        
        // // Create connection
        // $conn = new mysqli($servername, $username, $password, $dbname);
        
        // // Check connection
        // if ($conn->connect_error) {
        //     die("Connection failed: " . $conn->connect_error);
        // }
        
        // Initialize variables
        $success = false;
        $error = "";
        
        // Check if form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Sanitize and validate inputs
            $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
            $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
            $dob = mysqli_real_escape_string($conn, $_POST['dob']);
            $doj = mysqli_real_escape_string($conn, $_POST['doj']);
            $gender = mysqli_real_escape_string($conn, $_POST['gender']);
            $phone = mysqli_real_escape_string($conn, $_POST['phone']);
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            $username = mysqli_real_escape_string($conn, $_POST['username']);
            $password = mysqli_real_escape_string($conn, $_POST['password']);
            $address = mysqli_real_escape_string($conn, $_POST['address']);
            
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // SQL to insert data
            $sql = "INSERT INTO receptionist_tbl (FIRST_NAME, LAST_NAME, DOB, DOJ, GENDER, PHONE, EMAIL, USERNAME, PASSWORD, ADDRESS) 
                    VALUES ('$first_name', '$last_name', '$dob', '$doj', '$gender', '$phone', '$email', '$username', '$hashed_password', '$address')";
            
            if ($conn->query($sql) === TRUE) {
                $success = true;
            } else {
                $error = "Error: " . $sql . "<br>" . $conn->error;
            }
            
            $conn->close();
        }
        ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="receptionistForm">
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
            
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="number" id="phone" name="phone">
                    <div class="error-message" id="phone_error"></div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email">
                    <div class="error-message" id="email_error"></div>
                </div>
            </div>
            
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
            
            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address"></textarea>
                <div class="error-message" id="address_error"></div>
            </div>
            
            <div class="btn-container">
                <button type="submit" class="btn">Register</button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('receptionistForm').addEventListener('submit', function(event) {
            let isValid = true;
            
            // Validate required fields
            const requiredFields = ['first_name', 'last_name', 'username', 'password'];
            
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
            
            // Validate email format if provided
            const emailField = document.getElementById('email');
            const emailError = document.getElementById('email_error');
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (emailField.value && !emailPattern.test(emailField.value)) {
                emailError.textContent = 'Invalid email format';
                emailError.style.display = 'block';
                isValid = false;
            } else {
                emailError.style.display = 'none';
            }
            
            // Validate phone number if provided
            const phoneField = document.getElementById('phone');
            const phoneError = document.getElementById('phone_error');
            
            if (phoneField.value && (phoneField.value.length < 10 || phoneField.value.length > 15)) {
                phoneError.textContent = 'Invalid phone number';
                phoneError.style.display = 'block';
                isValid = false;
            } else {
                phoneError.style.display = 'none';
            }
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    </script>
</body>
</html>