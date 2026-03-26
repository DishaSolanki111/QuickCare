<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['LOGGED_IN']) || $_SESSION['LOGGED_IN'] !== true || $_SESSION['USER_TYPE'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

include 'config.php';

// Add the missing validation function
function qc_validate_person_name($raw, &$normalized, &$error) {
    $val = trim($raw);
    if ($val === '') { $error = "This field is required."; return false; }
    if (strlen($val) < 2 || strlen($val) > 50) { $error = "Must be 2–50 characters."; return false; }
    if (!preg_match('/^[A-Za-z\s]+$/', $val)) { $error = "Only letters are allowed."; return false; }
    if (preg_match('/(.)\1{3,}/', $val)) { $error = "No letter may repeat more than 3 times consecutively."; return false; }
    $normalized = $val;
    return true;
}

// Determine doctor ID from POST (preferred on form submit) or GET for initial load
$doctor_id = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doctor_id']) && is_numeric($_POST['doctor_id'])) {
    $doctor_id = (int) $_POST['doctor_id'];
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $doctor_id = (int) $_GET['id'];
}

if ($doctor_id === null) {
    header("Location: Admin_doctor.php");
    exit();
}

// Fetch doctor data
$query = "SELECT * FROM doctor_tbl WHERE DOCTOR_ID = $doctor_id";
$result = mysqli_query($conn, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: Admin_doctor.php?error=" . urlencode("Doctor not found."));
    exit();
}

$doctor_data = mysqli_fetch_assoc($result);

// Handle form submission
$error_message = '';
$success_message = '';
$field_errors = [];

if (isset($_POST['action']) && $_POST['action'] == 'edit_doctor') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $dob = trim($_POST['dob']);
    $doj = trim($_POST['doj']);
    $gender = trim($_POST['gender']);
    $education = trim($_POST['education']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $specialization_id = $_POST['specialization_id'];
    
    // Handle profile image upload
    $profile_image = $doctor_data['PROFILE_IMAGE']; // Keep current image if no new one uploaded
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $filetmp = $_FILES['profile_image']['tmp_name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $newname = "doctor_" . $doctor_id . "_" . time() . "." . $ext;
            $folder = "uploads/";
            
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }
            
            if (move_uploaded_file($filetmp, $folder . $newname)) {
                $profile_image = $folder . $newname;
            }
        }
    }
    
    // Validation
    $errors = [];

    $first_name_normalized = '';
    $first_name_err = '';
    if (!qc_validate_person_name($first_name, $first_name_normalized, $first_name_err)) {
        $field_errors['first_name'] = $first_name_err;
        $errors[] = $first_name_err;
    } else {
        $first_name = $first_name_normalized;
    }

    $last_name_normalized = '';
    $last_name_err = '';
    if (!qc_validate_person_name($last_name, $last_name_normalized, $last_name_err)) {
        $field_errors['last_name'] = $last_name_err;
        $errors[] = $last_name_err;
    } else {
        $last_name = $last_name_normalized;
    }
    
    if (empty($education)) {
        $field_errors['education'] = "Education is required.";
        $errors[] = "Education is required.";
    } elseif (!preg_match('/^[a-zA-Z\s\.\,\(\)]+$/', $education)) {
        $field_errors['education'] = "Education should contain only letters, spaces, dots, commas, and parentheses.";
        $errors[] = "Education should contain only letters, spaces, dots, commas, and parentheses.";
    }
    
    if (empty($phone)) {
        $field_errors['phone'] = "Phone number is required.";
        $errors[] = "Phone number is required.";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $field_errors['phone'] = "Phone number must be exactly 10 digits.";
        $errors[] = "Phone number must be exactly 10 digits.";
    }
    
    if (empty($email)) {
        $field_errors['email'] = "Email is required.";
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $field_errors['email'] = "email should be in example@gmail.com format.";
        $errors[] = "email should be in example@gmail.com format.";
    }
    
    if (empty($specialization_id)) {
        $field_errors['specialization_id'] = "Specialization is required.";
        $errors[] = "Specialization is required.";
    } else {
        $spec_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM specialisation_tbl WHERE SPECIALISATION_ID = $specialization_id");
        $spec_row = mysqli_fetch_assoc($spec_check);
        if ($spec_row['count'] == 0) {
            $field_errors['specialization_id'] = "Invalid specialization selected.";
            $errors[] = "Invalid specialization selected.";
        }
    }
    
    // Validation for new fields
    if (empty($dob)) {
        $field_errors['dob'] = "Date of birth is required.";
        $errors[] = "Date of birth is required.";
    } elseif (!strtotime($dob)) {
        $field_errors['dob'] = "Invalid date of birth format.";
        $errors[] = "Invalid date of birth format.";
    }
    
    if (empty($doj)) {
        $field_errors['doj'] = "Date of joining is required.";
        $errors[] = "Date of joining is required.";
    } elseif (!strtotime($doj)) {
        $field_errors['doj'] = "Invalid date of joining format.";
        $errors[] = "Invalid date of joining format.";
    }
    
    // Validate DOJ is after DOB
    if (!empty($dob) && !empty($doj) && strtotime($dob) && strtotime($doj)) {
        if (strtotime($doj) < strtotime($dob)) {
            $field_errors['doj'] = "Date of joining cannot be before date of birth.";
            $errors[] = "Date of joining cannot be before date of birth.";
        }
    }
    
    // Gender is optional, but if provided, validate
    if (!empty($gender) && !in_array($gender, ['MALE', 'FEMALE', 'OTHER'])) {
        $field_errors['gender'] = "Invalid gender selection.";
        $errors[] = "Invalid gender selection.";
    }
    
    // Check for duplicate email (excluding current doctor)
    $email_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM doctor_tbl WHERE EMAIL = '$email' AND DOCTOR_ID != $doctor_id");
    $email_row = mysqli_fetch_assoc($email_check);
    if ($email_row['count'] > 0) {
        $field_errors['email'] = "Email already exists.";
        $errors[] = "Email already exists.";
    }
    
    // Check for duplicate phone (excluding current doctor)
    $phone_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM doctor_tbl WHERE PHONE = '$phone' AND DOCTOR_ID != $doctor_id");
    $phone_row = mysqli_fetch_assoc($phone_check);
    if ($phone_row['count'] > 0) {
        $field_errors['phone'] = "Phone number already exists.";
        $errors[] = "Phone number already exists.";
    }
    
    if (empty($errors)) {
        // Update doctor data
        $query = "UPDATE doctor_tbl SET 
                 FIRST_NAME = ?, 
                 LAST_NAME = ?, 
                 DOB = ?, 
                 DOJ = ?, 
                 GENDER = ?, 
                 EDUCATION = ?, 
                 PHONE = ?, 
                 EMAIL = ?, 
                 SPECIALISATION_ID = ?, 
                 PROFILE_IMAGE = ? 
                 WHERE DOCTOR_ID = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssssssisi", $first_name, $last_name, $dob, $doj, $gender, $education, $phone, $email, $specialization_id, $profile_image, $doctor_id);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: Admin_doctor.php?success=" . urlencode("Doctor information updated successfully."));
            exit();
        } else {
            $error_message = "Error updating doctor: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = implode("<br>", $errors);
    }
}

include 'admin_sidebar.php';
$adminName = isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Doctor - QuickCare</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --dark-blue: #072D44;
            --mid-blue: #064469;
            --soft-blue: #5790AB;
            --light-blue: #9CCDD8;
            --gray-blue: #D0D7E1;
            --white: #ffffff;
            --card-bg: #F6F9FB;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #D0D7E1;
            display: flex;
        }

        .main {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            padding: 30px;
            max-width: 600px;
            margin: 0 auto;
        }

        h2 {
            color: var(--dark-blue);
            margin-top: 0;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--dark-blue);
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #D0D7E1;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        input:focus, select:focus {
            border-color: var(--soft-blue);
            outline: none;
            box-shadow: 0 0 5px rgba(87, 144, 171, 0.3);
        }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            flex: 1;
            text-align: center;
        }

        .btn-save {
            background: var(--soft-blue);
            color: white;
        }

        .btn-save:hover {
            background: var(--mid-blue);
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
        }

        .btn-cancel:hover {
            background: #5a6268;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .error-message {
            color: #dc3545;
            font-size: 13px;
            margin-top: 5px;
            display: none;
        }

        .form-group.error input,
        .form-group.error select,
        .form-group.error textarea {
            border-color: #dc3545;
        }

        .current-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
            border: 2px solid var(--soft-blue);
        }

        .image-upload-label {
            display: inline-block;
            padding: 8px 15px;
            background: var(--soft-blue);
            color: white;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .image-upload-label:hover {
            background: var(--mid-blue);
        }

        #profile_image {
            display: none;
        }
    </style>
</head>
<body>

<div class="main">
    <?php include 'admin_header.php'; ?>
    
    <div class="card">
        <h2>Edit Doctor Information</h2>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit_doctor">
            <input type="hidden" name="doctor_id" value="<?php echo $doctor_id; ?>">
            
            <div class="form-group">
                <label>Current Profile Image</label>
                <img src="<?php echo !empty($doctor_data['PROFILE_IMAGE']) ? $doctor_data['PROFILE_IMAGE'] : 'uploads/default_doctor.png'; ?>" class="current-image" alt="Doctor Image">
                <br>
                <label for="profile_image" class="image-upload-label">
                    <i class="bi bi-cloud-upload"></i> Change Profile Image
                </label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($doctor_data['FIRST_NAME']); ?>" required>
                    <div class="error-message" id="first_name_error"><?php echo htmlspecialchars($field_errors['first_name'] ?? ''); ?></div>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($doctor_data['LAST_NAME']); ?>" required>
                    <div class="error-message" id="last_name_error"><?php echo htmlspecialchars($field_errors['last_name'] ?? ''); ?></div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="dob">Date of Birth *</label>
                    <input type="date" id="dob" name="dob" value="<?php echo htmlspecialchars($doctor_data['DOB']); ?>" required>
                    <div class="error-message" id="dob_error"><?php echo htmlspecialchars($field_errors['dob'] ?? ''); ?></div>
                </div>
                <div class="form-group">
                    <label for="doj">Date of Joining *</label>
                    <input type="date" id="doj" name="doj" value="<?php echo htmlspecialchars($doctor_data['DOJ']); ?>" required>
                    <div class="error-message" id="doj_error"><?php echo htmlspecialchars($field_errors['doj'] ?? ''); ?></div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender">
                        <option value="">Select Gender</option>
                        <option value="MALE" <?php echo ($doctor_data['GENDER'] == 'MALE') ? 'selected' : ''; ?>>Male</option>
                        <option value="FEMALE" <?php echo ($doctor_data['GENDER'] == 'FEMALE') ? 'selected' : ''; ?>>Female</option>
                        <option value="OTHER" <?php echo ($doctor_data['GENDER'] == 'OTHER') ? 'selected' : ''; ?>>Other</option>
                    </select>
                    <div class="error-message" id="gender_error"><?php echo htmlspecialchars($field_errors['gender'] ?? ''); ?></div>
                </div>
                <div class="form-group">
                    <!-- Empty cell for layout balance -->
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="education">Education *</label>
                    <input type="text" id="education" name="education" value="<?php echo htmlspecialchars($doctor_data['EDUCATION']); ?>" required>
                    <div class="error-message" id="education_error"><?php echo htmlspecialchars($field_errors['education'] ?? ''); ?></div>
                </div>
                <div class="form-group">
                    <label for="specialization_id">Specialization *</label>
                    <select id="specialization_id" name="specialization_id" required>
                        <option value="">Select Specialization</option>
                        <?php
                        $spec_query = mysqli_query($conn, "SELECT * FROM specialisation_tbl ORDER BY SPECIALISATION_NAME");
                        while ($spec = mysqli_fetch_assoc($spec_query)) {
                            $selected = ($spec['SPECIALISATION_ID'] == $doctor_data['SPECIALISATION_ID']) ? 'selected' : '';
                            echo "<option value='{$spec['SPECIALISATION_ID']}' $selected>{$spec['SPECIALISATION_NAME']}</option>";
                        }
                        ?>
                    </select>
                    <div class="error-message" id="specialization_id_error"><?php echo htmlspecialchars($field_errors['specialization_id'] ?? ''); ?></div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone *</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($doctor_data['PHONE']); ?>" required>
                    <div class="error-message" id="phone_error"><?php echo htmlspecialchars($field_errors['phone'] ?? ''); ?></div>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($doctor_data['EMAIL']); ?>" required>
                    <div class="error-message" id="email_error"><?php echo htmlspecialchars($field_errors['email'] ?? ''); ?></div>
                </div>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn btn-save">
                    <i class="bi bi-check-circle"></i> Save Changes
                </button>
                <a href="Admin_doctor.php" class="btn btn-cancel" style="text-decoration: none;">
                    <i class="bi bi-x-circle"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Error message handling functions
function hideError(fieldId) {
    const errorElement = document.getElementById(fieldId + '_error');
    const formGroup = errorElement.closest('.form-group');
    if (errorElement) {
        errorElement.style.display = 'none';
        errorElement.textContent = '';
    }
    if (formGroup) {
        formGroup.classList.remove('error');
    }
}

function showError(fieldId, message) {
    const errorElement = document.getElementById(fieldId + '_error');
    const formGroup = errorElement.closest('.form-group');
    if (errorElement) {
        errorElement.textContent = message;
        errorElement.style.display = 'block';
    }
    if (formGroup) {
        formGroup.classList.add('error');
    }
}

// Real-time validation
document.addEventListener('DOMContentLoaded', function() {
    // First Name validation
    const firstName = document.getElementById('first_name');
    if (firstName) {
        firstName.addEventListener('input', function() {
            const value = this.value.trim();
            if (value.length >= 2 && value.length <= 50 && /^[A-Za-z\s]+$/.test(value) && !/(.)\1{3,}/.test(value)) {
                hideError('first_name');
            }
        });
    }

    // Last Name validation
    const lastName = document.getElementById('last_name');
    if (lastName) {
        lastName.addEventListener('input', function() {
            const value = this.value.trim();
            if (value.length >= 2 && value.length <= 50 && /^[A-Za-z\s]+$/.test(value) && !/(.)\1{3,}/.test(value)) {
                hideError('last_name');
            }
        });
    }

    // Education validation
    const education = document.getElementById('education');
    if (education) {
        education.addEventListener('input', function() {
            const value = this.value.trim();
            if (value && /^[a-zA-Z\s\.\,\(\)]+$/.test(value)) {
                hideError('education');
            }
        });
    }

    // Phone validation
    const phone = document.getElementById('phone');
    if (phone) {
        phone.addEventListener('input', function() {
            const value = this.value.trim();
            if (/^[0-9]{10}$/.test(value)) {
                hideError('phone');
            }
        });
    }

    // Email validation
    const email = document.getElementById('email');
    if (email) {
        email.addEventListener('input', function() {
            const value = this.value.trim();
            if (value && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                hideError('email');
            }
        });
    }

    // Specialization validation
    const specialization = document.getElementById('specialization_id');
    if (specialization) {
        specialization.addEventListener('change', function() {
            if (this.value) {
                hideError('specialization_id');
            }
        });
    }

    // DOB validation
    const dob = document.getElementById('dob');
    if (dob) {
        dob.addEventListener('change', function() {
            const value = this.value;
            if (value && !isNaN(Date.parse(value))) {
                hideError('dob');
            }
        });
    }

    // DOJ validation
    const doj = document.getElementById('doj');
    if (doj) {
        doj.addEventListener('change', function() {
            const value = this.value;
            const dobValue = document.getElementById('dob').value;
            if (value && !isNaN(Date.parse(value))) {
                hideError('doj');
                // Check DOJ vs DOB
                if (dobValue && Date.parse(value) < Date.parse(dobValue)) {
                    showError('doj', 'Date of joining cannot be before date of birth.');
                }
            }
        });
    }

    // Gender validation
    const gender = document.getElementById('gender');
    if (gender) {
        gender.addEventListener('change', function() {
            if (this.value) {
                hideError('gender');
            }
        });
    }
});

// Preview image when selected
document.getElementById('profile_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.current-image').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>
