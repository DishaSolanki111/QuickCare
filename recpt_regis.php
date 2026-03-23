<?php
ob_start();
include 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receptionist Registration | QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@700&display=swap" rel="stylesheet">
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
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background: linear-gradient(135deg, var(--light-blue) 0%, var(--card-bg) 100%); min-height: 100vh; display: flex; padding-top: 10px; }
        .main-content { margin-left: 250px; width: calc(100% - 250px); padding: 30px; display: flex; justify-content: center; align-items: center; }
        .container { background-color: var(--white); border-radius: 12px; box-shadow: 0 8px 20px rgba(0,0,0,0.15); width: 100%; max-width: 1400px; padding: 30px 40px; position: relative; overflow: hidden; }
        .container::before { content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px; background: linear-gradient(90deg, var(--accent-blue), var(--secondary-blue)); }
        .back-btn { display: inline-block; padding: 8px 18px; margin-bottom: 15px; background-color: #0a3d62; color: #ffffff; border: 2px solid #0a3d62; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 500; transition: 0.3s ease; }
        h1 { color: var(--dark-blue); text-align: center; margin-bottom: 25px; font-size: 28px; position: relative; }
        h1::after { content: ''; position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 80px; height: 3px; background-color: var(--accent-blue); }
        .form-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px; }
        .form-section { margin-bottom: 0; }
        .form-section-title { font-family: 'Inter', sans-serif; font-size: 1.25rem; font-weight: 700; color: var(--primary-blue); letter-spacing: 0.5px; margin-bottom: 16px; padding-bottom: 10px; border-bottom: 3px solid var(--light-blue); }
        .form-group { margin-bottom: 15px; position: relative; }
        label { display: block; margin-bottom: 6px; color: var(--primary-blue); font-weight: 600; font-size: 14px; }
        .required { color: var(--error); }
        .password-wrapper { position: relative; }
        .toggle-password { position: absolute; top: 50%; right: 15px; transform: translateY(-50%); cursor: pointer; color: var(--soft-blue); transition: color 0.3s; }
        .toggle-password:hover { color: var(--primary-blue); }
        input[type="text"], input[type="number"], input[type="email"], input[type="password"], input[type="date"], input[type="file"], select, textarea { width: 100%; padding: 10px 12px; border: 1px solid var(--gray-blue); border-radius: 6px; font-size: 14px; transition: all 0.3s ease; background-color: var(--white); }
        input[type="password"] { padding-right: 40px; }
        input[type="text"]:focus, input[type="number"]:focus, input[type="email"]:focus, input[type="password"]:focus, input[type="date"]:focus, select:focus, textarea:focus { outline: none; border-color: var(--accent-blue); box-shadow: 0 0 0 2px rgba(0,180,216,0.1); }
        select { cursor: pointer; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%230a4d68' d='M10.293 3.293L6 7.586 1.707 3.293A1 1 0 00.293 4.707l5 5a1 1 0 001.414 0l5-5a1 1 0 10-1.414-1.414z'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; padding-right: 35px; }
        textarea { resize: vertical; min-height: 70px; max-height: 100px; }
        .radio-group { display: flex; gap: 15px; margin-top: 6px; flex-wrap: wrap; }
        .radio-option { display: flex; align-items: center; cursor: pointer; }
        .radio-option input { margin-right: 8px; cursor: pointer; width: auto; }
        .radio-option label { margin-bottom: 0; font-weight: normal; cursor: pointer; }
        .btn-container { display: flex; justify-content: center; margin-top: 20px; grid-column: 1 / -1; }
        .btn { background: linear-gradient(90deg, var(--primary-blue), var(--secondary-blue)); color: var(--white); border: none; padding: 12px 35px; font-size: 16px; font-weight: 600; border-radius: 6px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 4px 8px rgba(10,77,104,0.2); display: flex; align-items: center; gap: 8px; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(10,77,104,0.3); }
        .error-message { color: var(--error); font-size: 13px; margin-top: 5px; display: none; }
        .toast { position: fixed; top: 20px; right: 20px; background-color: var(--white); color: var(--primary-blue); padding: 15px 20px; border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: none; z-index: 1000; max-width: 300px; border-left: 4px solid var(--error); }
        .toast.success { border-left: 4px solid var(--accent-blue); }
        .toast.show { display: block; animation: slideIn 0.3s ease-out; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @media (max-width: 1200px) { .form-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 768px) { .main-content { margin-left: 0; width: 100%; padding: 20px; } .form-grid { grid-template-columns: 1fr; } .container { padding: 25px; max-width: 100%; } h1 { font-size: 24px; } }
    </style>
</head>
<body>
    <?php include 'admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="container">
            <a class="back-btn" href="admin.php">back</a>
            <h1>Receptionist Registration</h1>

            <div id="toast" class="toast"></div>

            <?php
            // ── Inline helper functions (replaces missing config.php functions) ──

            function local_validate_person_name($raw, &$normalized, &$error) {
                $val = trim($raw);
                if ($val === '') { $error = "This field is required."; return false; }
                if (strlen($val) < 2 || strlen($val) > 50) { $error = "Must be 2–50 characters."; return false; }
                if (!preg_match('/^[A-Za-z]+$/', $val)) { $error = "Only letters are allowed."; return false; }
                if (preg_match('/(.)\1{3,}/', $val)) { $error = "No letter may repeat more than 3 times consecutively."; return false; }
                $normalized = $val;
                return true;
            }

            function local_validate_username($raw, &$normalized, &$error) {
                $val = trim($raw);
                $reserved = ['admin','root','support','api','system'];
                if ($val === '') { $error = "Username is required."; return false; }
                if (strlen($val) < 3 || strlen($val) > 30) { $error = "Username must be 3–30 characters."; return false; }
                if (preg_match('/\s/', $val)) { $error = "Username cannot contain spaces."; return false; }
                if (!preg_match('/^[A-Za-z0-9_.]+$/', $val)) { $error = "Only letters, numbers, _ and . are allowed."; return false; }
                if (preg_match('/^[_.]|[_.]$/', $val)) { $error = "Username cannot start or end with _ or ."; return false; }
                if (preg_match('/[_.]{2,}/', $val)) { $error = "Consecutive _ or . are not allowed."; return false; }
                if (preg_match('/^\d+$/', $val)) { $error = "Username cannot be numbers only."; return false; }
                if (in_array(strtolower($val), $reserved)) { $error = "This username is reserved."; return false; }
                $normalized = strtolower($val);
                return true;
            }

            function local_validate_password($password, $username, $emailLower, &$error) {
                if (strlen($password) < 8) { $error = "Password must be at least 8 characters long."; return false; }
                if (!preg_match('/^[A-Z]/', $password)) { $error = "Password must start with a capital letter."; return false; }
                if (!preg_match('/[A-Z]/', $password)) { $error = "Password must contain at least one uppercase letter."; return false; }
                if (!preg_match('/[a-z]/', $password)) { $error = "Password must contain at least one lowercase letter."; return false; }
                if (!preg_match('/[0-9]/', $password)) { $error = "Password must contain at least one digit."; return false; }
                if (!preg_match('/[^A-Za-z0-9]/', $password)) { $error = "Password must contain at least one special character."; return false; }
                return true;
            }

            // ── Init ──
            $success = false;
            $error   = "";
            $field_errors = [
                'first_name' => '', 'last_name' => '', 'dob' => '', 'doj' => '',
                'phone' => '', 'email' => '', 'username' => '', 'password' => '', 'address' => ''
            ];
            $form_data = [
                'first_name' => '', 'last_name' => '', 'dob' => '', 'doj' => '',
                'gender' => '', 'phone' => '', 'email' => '',
                'username' => '', 'password' => '', 'address' => ''
            ];

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $form_data = [
                    'first_name' => $_POST['first_name'] ?? '',
                    'last_name'  => $_POST['last_name']  ?? '',
                    'dob'        => $_POST['dob']        ?? '',
                    'doj'        => $_POST['doj']        ?? '',
                    'gender'     => $_POST['gender']     ?? '',
                    'phone'      => $_POST['phone']      ?? '',
                    'email'      => $_POST['email']      ?? '',
                    'username'   => $_POST['username']   ?? '',
                    'password'   => $_POST['password']   ?? '',
                    'address'    => $_POST['address']    ?? ''
                ];

                $dob      = $form_data['dob'];
                $doj      = $form_data['doj'];
                $gender   = $form_data['gender'];
                $phone    = $form_data['phone'];
                $email    = $form_data['email'];
                $username = $form_data['username'];
                $password = $form_data['password'];
                $address  = mysqli_real_escape_string($conn, $form_data['address']);

                // Date validation
                if (empty($dob)) {
                    $field_errors['dob'] = "Date of Birth is required.";
                } else {
                    $dob_date = new DateTime($dob);
                    $today    = new DateTime();
                    if ($dob_date > $today) $field_errors['dob'] = "Date of Birth cannot be in the future.";
                }

                if (empty($doj)) {
                    $field_errors['doj'] = "Date of Joining is required.";
                } else {
                    $doj_date = new DateTime($doj);
                    $today    = new DateTime();
                    if ($doj_date > $today) {
                        $field_errors['doj'] = "Date of Joining cannot be in the future.";
                    } elseif (!empty($dob)) {
                        $dob_date = new DateTime($dob);
                        if ($doj_date <= $dob_date) $field_errors['doj'] = "Date of Joining must be after Date of Birth.";
                    }
                }

                // Age >= 23 at DOJ
                if (!empty($dob) && !empty($doj) && empty($field_errors['dob']) && empty($field_errors['doj'])) {
                    $dob_dt = DateTime::createFromFormat('Y-m-d', $dob);
                    $doj_dt = DateTime::createFromFormat('Y-m-d', $doj);
                    if ($dob_dt && $doj_dt) {
                        $age_diff = $dob_dt->diff($doj_dt)->y;
                        if ($age_diff < 23) {
                            $field_errors['dob'] = "Receptionist must be at least 23 years old at date of joining.";
                            $field_errors['doj'] = "Receptionist must be at least 23 years old at date of joining.";
                        }
                    }
                }

                // Name validation
                $first_nameNormalized = '';
                $last_nameNormalized  = '';
                if (!local_validate_person_name($form_data['first_name'], $first_nameNormalized, $first_nameErr)) {
                    $field_errors['first_name'] = $first_nameErr;
                } else {
                    $first_name = mysqli_real_escape_string($conn, $first_nameNormalized);
                }
                if (!local_validate_person_name($form_data['last_name'], $last_nameNormalized, $last_nameErr)) {
                    $field_errors['last_name'] = $last_nameErr;
                } else {
                    $last_name = mysqli_real_escape_string($conn, $last_nameNormalized);
                }

                // Username validation
                $usernameNormalized = '';
                $usernameError      = '';
                if (!local_validate_username($username, $usernameNormalized, $usernameError)) {
                    $field_errors['username'] = $usernameError;
                } else {
                    $username = $usernameNormalized;
                }

                // Password validation
                $passwordError = '';
                $emailLower    = strtolower(trim($email));
                if (!local_validate_password($password, $username, $emailLower, $passwordError)) {
                    $field_errors['password'] = $passwordError;
                }

                // Phone validation
                if (!preg_match('/^[0-9]{10}$/', $phone)) {
                    $field_errors['phone'] = "Phone number must be exactly 10 digits.";
                }

                // Email validation
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $field_errors['email'] = "Invalid email format.";
                }

                // Username exists check
                if (empty($field_errors['username'])) {
                    $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM receptionist_tbl WHERE LOWER(USERNAME) = ?");
                    $stmt->bind_param("s", $username);
                    $stmt->execute();
                    $check_result = $stmt->get_result();
                    if ($check_result && $check_result->fetch_assoc()['cnt'] > 0) {
                        $field_errors['username'] = "Username already exists.";
                    }
                    $stmt->close();
                }

                // Final insert
                if (empty(array_filter($field_errors))) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "INSERT INTO receptionist_tbl
                        (FIRST_NAME, LAST_NAME, DOB, DOJ, GENDER, PHONE, EMAIL, ADDRESS, USERNAME, PSWD, SECURITY_QUESTION, SECURITY_ANSWER)
                        VALUES ('$first_name','$last_name','$dob','$doj','$gender','$phone','$email','$address','$username','$hashed_password','','')";

                    if ($conn->query($sql) === TRUE) {
                        $conn->close();
                        ob_end_clean();
                        header("Location: admin.php");
                        exit;
                    } else {
                        $error = "Database error: " . $conn->error;
                    }

                    $conn->close();
                }
            }
            ?>

            <?php if (!empty($error)): ?>
                <div class="toast show"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="receptionistForm" enctype="multipart/form-data">
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
                            <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($form_data['dob']); ?>" required>
                            <div class="error-message" id="dob_error"<?php if (!empty($field_errors['dob'])) echo ' style="display:block"'; ?>><?php echo htmlspecialchars($field_errors['dob'] ?? ''); ?></div>
                        </div>

                        <div class="form-group">
                            <label for="doj">Date of Joining <span class="required">*</span></label>
                            <input type="date" id="doj" name="doj" value="<?php echo htmlspecialchars($form_data['doj']); ?>" required>
                            <div class="error-message" id="doj_error"<?php if (!empty($field_errors['doj'])) echo ' style="display:block"'; ?>><?php echo htmlspecialchars($field_errors['doj'] ?? ''); ?></div>
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
                            <textarea id="address" name="address" placeholder="e.g. 221B, Vinod Residency, Mumbai"><?php echo htmlspecialchars($form_data['address']); ?></textarea>
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
                                <input type="password" id="password" name="password" placeholder="Min 8 chars, 1 Uppercase, 1 Digit, 1 Special" required>
                                <i class="fas fa-eye-slash toggle-password" id="togglePassword"></i>
                            </div>
                            <div class="error-message" id="password_error"<?php if (!empty($field_errors['password'])) echo ' style="display:block"'; ?>><?php echo htmlspecialchars($field_errors['password'] ?? ''); ?></div>
                        </div>
                    </div>
                </div>

                <div class="btn-container">
                    <button type="submit" class="btn"><i class="fas fa-user-plus"></i> Register</button>
                </div>
            </form>
        </div>
    </div>

    <script>
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput  = document.querySelector('#password');
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function () {
            const isHidden = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isHidden ? 'text' : 'password');
            this.classList.toggle('fa-eye-slash', !isHidden);
            this.classList.toggle('fa-eye', isHidden);
        });
    }

    function hideError(id) {
        const el = document.getElementById(id + '_error');
        if (el) { el.style.display = "none"; el.textContent = ""; }
    }
    function showError(id, msg) {
        const el = document.getElementById(id + '_error');
        if (el) { el.textContent = msg; el.style.display = "block"; }
    }

    function calcAge(dobVal, dojVal) {
        const d = new Date(dobVal), j = new Date(dojVal);
        let age = j.getFullYear() - d.getFullYear();
        if (j.getMonth() < d.getMonth() || (j.getMonth() === d.getMonth() && j.getDate() < d.getDate())) age--;
        return age;
    }

    document.getElementById('first_name').addEventListener('input', function () {
        const v = this.value.trim();
        if (v.length >= 2 && v.length <= 50 && /^[A-Za-z]+$/.test(v) && !(/(.)\1{3,}/.test(v))) hideError('first_name');
    });
    document.getElementById('last_name').addEventListener('input', function () {
        const v = this.value.trim();
        if (v.length >= 2 && v.length <= 50 && /^[A-Za-z]+$/.test(v) && !(/(.)\1{3,}/.test(v))) hideError('last_name');
    });
    document.getElementById('dob').addEventListener('input', function () {
        const dobVal = this.value, dojVal = document.getElementById('doj').value;
        if (!dobVal) return;
        if (new Date(dobVal) > new Date()) { showError('dob', 'Date of Birth cannot be in the future.'); return; }
        if (dojVal && calcAge(dobVal, dojVal) < 23) {
            showError('dob', 'DOB and DOJ must have at least a 23-year gap.');
            showError('doj', 'DOB and DOJ must have at least a 23-year gap.');
        } else {
            hideError('dob');
            if (dojVal) hideError('doj');
        }
    });
    document.getElementById('doj').addEventListener('input', function () {
        const dojVal = this.value, dobVal = document.getElementById('dob').value;
        if (!dojVal) return;
        if (new Date(dojVal) > new Date()) { showError('doj', 'Date of Joining cannot be in the future.'); return; }
        if (dobVal && calcAge(dobVal, dojVal) < 23) {
            showError('dob', 'DOB and DOJ must have at least a 23-year gap.');
            showError('doj', 'DOB and DOJ must have at least a 23-year gap.');
        } else {
            hideError('doj');
            if (dobVal) hideError('dob');
        }
    });
    document.getElementById('phone').addEventListener('input', function () {
        if (/^\d{10}$/.test(this.value.trim())) hideError('phone');
    });
    document.getElementById('email').addEventListener('input', function () {
        if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value.trim())) hideError('email');
    });
    document.getElementById('username').addEventListener('input', function () {
        const v = this.value.trim();
        const ok = v.length >= 3 && v.length <= 30 && /^[A-Za-z0-9_.]+$/.test(v)
            && !/^[_.]|[_.]$/.test(v) && !/[_.]{2,}/.test(v) && !/^\d+$/.test(v)
            && !['admin','root','support','api','system'].includes(v.toLowerCase());
        if (ok) hideError('username');
    });
    document.getElementById('password').addEventListener('input', function () {
        const v = this.value;
        if (v.length >= 8 && /^[A-Z]/.test(v) && /[a-z]/.test(v) && /[0-9]/.test(v) && /[^A-Za-z0-9]/.test(v)) hideError('password');
    });

    document.getElementById('receptionistForm').addEventListener('submit', function (e) {
        let isValid = true;

        const firstName = document.getElementById('first_name').value.trim();
        if (!firstName) { showError('first_name', "First name is required."); isValid = false; }
        else if (!/^[A-Za-z]+$/.test(firstName) || firstName.length < 2 || firstName.length > 50 || /(.)\1{3,}/.test(firstName)) {
            showError('first_name', "First name must be 2–50 letters only."); isValid = false;
        } else hideError('first_name');

        const lastName = document.getElementById('last_name').value.trim();
        if (!lastName) { showError('last_name', "Last name is required."); isValid = false; }
        else if (!/^[A-Za-z]+$/.test(lastName) || lastName.length < 2 || lastName.length > 50 || /(.)\1{3,}/.test(lastName)) {
            showError('last_name', "Last name must be 2–50 letters only."); isValid = false;
        } else hideError('last_name');

        const dobVal = document.getElementById('dob').value;
        const dojVal = document.getElementById('doj').value;

        if (!dobVal) { showError('dob', "Date of Birth is required."); isValid = false; }
        else if (new Date(dobVal) > new Date()) { showError('dob', "Date of Birth cannot be in the future."); isValid = false; }
        else hideError('dob');

        if (!dojVal) { showError('doj', "Date of Joining is required."); isValid = false; }
        else if (new Date(dojVal) > new Date()) { showError('doj', "Date of Joining cannot be in the future."); isValid = false; }
        else if (dobVal && new Date(dojVal) <= new Date(dobVal)) { showError('doj', "Date of Joining must be after Date of Birth."); isValid = false; }
        else hideError('doj');

        if (dobVal && dojVal && new Date(dobVal) < new Date() && new Date(dojVal) <= new Date() && new Date(dojVal) > new Date(dobVal)) {
            if (calcAge(dobVal, dojVal) < 23) {
                showError('dob', "Receptionist must be at least 23 years old at date of joining."); isValid = false;
                showError('doj', "Receptionist must be at least 23 years old at date of joining."); isValid = false;
            }
        }

        const phone = document.getElementById('phone').value.trim();
        if (!/^\d{10}$/.test(phone)) { showError('phone', "Phone number must be exactly 10 digits."); isValid = false; }
        else hideError('phone');

        const email = document.getElementById('email').value.trim();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { showError('email', "Invalid email format."); isValid = false; }
        else hideError('email');

        const uname = document.getElementById('username').value.trim();
        const unameOk = uname.length >= 3 && uname.length <= 30 && /^[A-Za-z0-9_.]+$/.test(uname)
            && !/^[_.]|[_.]$/.test(uname) && !/[_.]{2,}/.test(uname) && !/^\d+$/.test(uname)
            && !['admin','root','support','api','system'].includes(uname.toLowerCase());
        if (!uname) { showError('username', "Username is required."); isValid = false; }
        else if (!unameOk) { showError('username', "Invalid username. Use 3–30 chars: letters/numbers/_ or ."); isValid = false; }
        else hideError('username');

        const pw = document.getElementById('password').value;
        if (pw.length < 8) { showError('password', "Password must be at least 8 characters long."); isValid = false; }
        else if (!/^[A-Z]/.test(pw)) { showError('password', "Password must start with a capital letter."); isValid = false; }
        else if (!/[a-z]/.test(pw)) { showError('password', "Password must contain at least one lowercase letter."); isValid = false; }
        else if (!/[0-9]/.test(pw)) { showError('password', "Password must contain at least one digit."); isValid = false; }
        else if (!/[^A-Za-z0-9]/.test(pw)) { showError('password', "Password must contain at least one special character."); isValid = false; }
        else hideError('password');

        if (!isValid) { e.preventDefault(); showToast("Please correct errors in the form."); }
    });

    function showToast(message, isSuccess = false) {
        const toast = document.getElementById('toast');
        toast.innerHTML = isSuccess ? `<i class="fas fa-check-circle"></i> ${message}` : `<i class="fas fa-exclamation-circle"></i> ${message}`;
        toast.className = isSuccess ? 'toast success show' : 'toast show';
        setTimeout(() => { toast.className = toast.className.replace('show', ''); }, 5000);
    }
    </script>
</body>
</html>