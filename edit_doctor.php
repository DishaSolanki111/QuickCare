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

if (isset($_POST['action']) && $_POST['action'] == 'edit_doctor') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
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
        $errors[] = $first_name_err;
    } else {
        $first_name = $first_name_normalized;
    }

    $last_name_normalized = '';
    $last_name_err = '';
    if (!qc_validate_person_name($last_name, $last_name_normalized, $last_name_err)) {
        $errors[] = $last_name_err;
    } else {
        $last_name = $last_name_normalized;
    }
    
    if (empty($education)) {
        $errors[] = "Education is required.";
    } elseif (!preg_match('/^[a-zA-Z\s\.\,]+$/', $education)) {
        $errors[] = "Education should contain only letters, spaces, dots, and commas.";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "Phone number must be exactly 10 digits.";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    if (empty($specialization_id)) {
        $errors[] = "Specialization is required.";
    } else {
        $spec_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM specialisation_tbl WHERE SPECIALISATION_ID = $specialization_id");
        $spec_row = mysqli_fetch_assoc($spec_check);
        if ($spec_row['count'] == 0) {
            $errors[] = "Invalid specialization selected.";
        }
    }
    
    // Check for duplicate email (excluding current doctor)
    $email_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM doctor_tbl WHERE EMAIL = '$email' AND DOCTOR_ID != $doctor_id");
    $email_row = mysqli_fetch_assoc($email_check);
    if ($email_row['count'] > 0) {
        $errors[] = "Email already exists.";
    }
    
    // Check for duplicate phone (excluding current doctor)
    $phone_check = mysqli_query($conn, "SELECT COUNT(*) as count FROM doctor_tbl WHERE PHONE = '$phone' AND DOCTOR_ID != $doctor_id");
    $phone_row = mysqli_fetch_assoc($phone_check);
    if ($phone_row['count'] > 0) {
        $errors[] = "Phone number already exists.";
    }
    
    if (empty($errors)) {
        // Update doctor data
        $query = "UPDATE doctor_tbl SET 
                 FIRST_NAME = ?, 
                 LAST_NAME = ?, 
                 EDUCATION = ?, 
                 PHONE = ?, 
                 EMAIL = ?, 
                 SPECIALISATION_ID = ?, 
                 PROFILE_IMAGE = ? 
                 WHERE DOCTOR_ID = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssssi", $first_name, $last_name, $education, $phone, $email, $specialization_id, $profile_image, $doctor_id);
        
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
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($doctor_data['LAST_NAME']); ?>" required>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="education">Education *</label>
                    <input type="text" id="education" name="education" value="<?php echo htmlspecialchars($doctor_data['EDUCATION']); ?>" required>
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
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone *</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($doctor_data['PHONE']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($doctor_data['EMAIL']); ?>" required>
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
