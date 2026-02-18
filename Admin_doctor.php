<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['LOGGED_IN']) || $_SESSION['LOGGED_IN'] !== true || $_SESSION['USER_TYPE'] !== 'admin') {
    // If not logged in or not an admin, redirect to admin login page
    header("Location: admin_login.php");
    exit();
}

include 'config.php';

// Handle edit form submission
if (isset($_POST['action']) && $_POST['action'] == 'edit_doctor') {
    $doctor_id = $_POST['doctor_id'];
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $education = trim($_POST['education']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $specialization_id = $_POST['specialization_id'];
    
    // Handle profile image upload
    $profile_image = $_POST['current_image']; // Keep current image if no new one uploaded
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
    
    // ---------------- NAME VALIDATION ----------------
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $first_name)) {
        $errors[] = "First name should contain only letters and spaces.";
    }
    
    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $last_name)) {
        $errors[] = "Last name should contain only letters and spaces.";
    }
    
    // ---------------- EDUCATION VALIDATION ----------------
    if (empty($education)) {
        $errors[] = "Education is required.";
    } elseif (!preg_match('/^[a-zA-Z\s\.\,]+$/', $education)) {
        $errors[] = "Education should contain only letters, spaces, dots, and commas.";
    }
    
    // ---------------- PHONE VALIDATION ----------------
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "Phone number must be exactly 10 digits.";
    }
    
    // ---------------- EMAIL VALIDATION ----------------
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    // ---------------- SPECIALIZATION VALIDATION ----------------
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
            $success_message = "Doctor information updated successfully.";
        } else {
            $error_message = "Error updating doctor: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = implode("<br>", $errors);
    }
}

// Handle delete
if (isset($_POST['delete'])) {
    $id = (int) $_POST['delete'];
    if ($id > 0) {
        $check = mysqli_query($conn, "SELECT COUNT(*) as c FROM appointment_tbl WHERE DOCTOR_ID = $id");
        $row = mysqli_fetch_assoc($check);
        if ((int) $row['c'] > 0) {
            header("Location: Admin_doctor.php?error=Cannot delete doctor with appointments");
        } else {
            mysqli_query($conn, "DELETE FROM doctor_tbl WHERE DOCTOR_ID = $id");
            header("Location: Admin_doctor.php");
        }
        exit;
    }
}

// Get doctor data for editing
 $doctor_data = null;
if (isset($_POST['edit'])) {
    $doctor_id = (int) $_POST['edit'];
    $query = "SELECT * FROM doctor_tbl WHERE DOCTOR_ID = $doctor_id";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $doctor_data = mysqli_fetch_assoc($result);
    }
}

include 'admin_sidebar.php';
 $adminName = isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Doctors - QuickCare</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #D0D7E1;
            display: flex;
        }

        :root {
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
        }

        .main {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        th, td {
            padding: 14px;
            border-bottom: 1px solid #D0D7E1;
        }

        th {
            background: #5790AB;
            color: white;
            text-align: left;
        }

        .filter-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .filter-container form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-container input,
        .filter-container select {
            padding: 10px;
            border: 1px solid #D0D7E1;
            border-radius: 5px;
        }

        .filter-container button {
            padding: 10px 15px;
            background: #5790AB;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .action-btn {
            padding: 6px 12px;
            margin: 0 3px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: white;
        }

        .edit-btn { background-color: #f39c12; }
        .delete-btn { background-color: #e74c3c; }

        .add-btn {
            background: #2ecc71;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 20px;
        }

        .doctor-info {
            display: flex;
            align-items: center;
        }

        .doctor-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .doctor-details {
            margin-left: 10px;
        }

        .actions-td {
            white-space: nowrap;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #fff;
            margin: 3% auto;
            padding: 20px;
            border-radius: 10px;
            width: 80%;
            max-width: 700px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: #000;
        }
        
        .form-group {
            margin-bottom: 15px;
            position: relative;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: var(--dark-blue);
        }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #D0D7E1;
            border-radius: 5px;
            box-sizing: border-box;
        }
        
        .form-group input:focus, .form-group select:focus {
            border-color: #5790AB;
            outline: none;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .btn-save {
            background: var(--soft-blue);
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        
        .btn-save:hover {
            background: var(--mid-blue);
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-left: 10px;
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
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
        
        /* Toast Styles */
        .toast {
            visibility: hidden;
            min-width: 250px;
            margin-left: -125px;
            background-color: #e74c3c;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            padding: 16px;
            position: fixed;
            z-index: 2000;
            left: 50%;
            top: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        }
        
        .toast.show {
            visibility: visible;
            animation: fadein 0.5s, fadeout 0.5s 2.5s;
        }
        
        .toast.success {
            background-color: #2ecc71;
        }
        
        @keyframes fadein {
            from {top: 0; opacity: 0;}
            to {top: 20px; opacity: 1;}
        }
        
        @keyframes fadeout {
            from {opacity: 1;}
            to {opacity: 0;}
        }
        
        .current-image {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 10px;
        }
        
        .image-upload-label {
            display: inline-block;
            padding: 8px 15px;
            background: #5790AB;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 10px;
        }
        
        .image-upload-label:hover {
            background: #064469;
        }
        
        #profile_image {
            display: none;
        }
    </style>
</head>

<body>

<div class="main">

    <?php include 'admin_header.php'; ?>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <a href="doctorform.php" class="add-btn">+ Add New Doctor</a>

    <div class="filter-container">
        <form method="POST">
            <input type="text" name="name_filter" placeholder="Filter by Name"
                value="<?php echo isset($_POST['name_filter']) ? htmlspecialchars($_POST['name_filter']) : ''; ?>">

            <select name="specialization_filter">
                <option value="">All Specializations</option>
                <?php
                $spec_q = mysqli_query($conn, "SELECT * FROM specialisation_tbl ORDER BY SPECIALISATION_NAME");
                while ($spec = mysqli_fetch_assoc($spec_q)) {
                    $selected = (isset($_POST['specialization_filter']) && $_POST['specialization_filter'] == $spec['SPECIALISATION_ID']) ? 'selected' : '';
                    echo "<option value='{$spec['SPECIALISATION_ID']}' $selected>{$spec['SPECIALISATION_NAME']}</option>";
                }
                ?>
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

    <table>
        <tr>
            <th>Doctor</th>
            <th>Specialization</th>
            <th>Education</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>

        <?php
        $query = "
            SELECT d.DOCTOR_ID, d.FIRST_NAME, d.LAST_NAME, d.PROFILE_IMAGE,
                   d.EDUCATION, d.PHONE, d.EMAIL,
                   s.SPECIALISATION_NAME, s.SPECIALISATION_ID
            FROM doctor_tbl d
            LEFT JOIN specialisation_tbl s
            ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
            WHERE 1=1
        ";

        if (!empty($_POST['name_filter'])) {
            $name = mysqli_real_escape_string($conn, $_POST['name_filter']);
            $query .= " AND CONCAT(IFNULL(d.FIRST_NAME,''),' ',IFNULL(d.LAST_NAME,'')) LIKE '%$name%'";
        }

        if (!empty($_POST['specialization_filter'])) {
            $spec_id = (int)$_POST['specialization_filter'];
            $query .= " AND d.SPECIALISATION_ID = $spec_id";
        }

        $query .= " ORDER BY d.FIRST_NAME, d.LAST_NAME";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $img = !empty($row['PROFILE_IMAGE'])
                    ? $row['PROFILE_IMAGE']
                    : 'uploads/default_doctor.png';
        ?>
        <tr>
            <td>
                <div class="doctor-info">
                    <img src="<?php echo $img; ?>" class="doctor-img">
                    <div class="doctor-details">
                        <strong><?php echo $row['FIRST_NAME'].' '.$row['LAST_NAME']; ?></strong>
                    </div>
                </div>
            </td>
            <td><?php echo $row['SPECIALISATION_NAME']; ?></td>
            <td><?php echo $row['EDUCATION']; ?></td>
            <td><?php echo $row['PHONE']; ?></td>
            <td><?php echo $row['EMAIL']; ?></td>
            <td class="actions-td">
                <button type="button" class="action-btn edit-btn"
                    onclick="openEditModal(<?php echo $row['DOCTOR_ID']; ?>, 
                    '<?php echo addslashes($row['FIRST_NAME']); ?>', 
                    '<?php echo addslashes($row['LAST_NAME']); ?>', 
                    '<?php echo addslashes($row['EDUCATION']); ?>', 
                    '<?php echo $row['PHONE']; ?>', 
                    '<?php echo $row['EMAIL']; ?>', 
                    '<?php echo $row['SPECIALISATION_ID']; ?>', 
                    '<?php echo $row['PROFILE_IMAGE']; ?>')">
                    Edit
                </button>

                <button type="button" class="action-btn delete-btn"
                    onclick="confirmDelete(<?php echo $row['DOCTOR_ID']; ?>)">
                    Delete
                </button>
            </td>
        </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='6'>No doctors found</td></tr>";
        }

        mysqli_close($conn);
        ?>
    </table>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Doctor</h2>
        <form id="editForm" method="post" action="Admin_doctor.php" enctype="multipart/form-data">
            <input type="hidden" name="action" value="edit_doctor">
            <input type="hidden" id="edit_doctor_id" name="doctor_id">
            <input type="hidden" id="current_image" name="current_image">
            
            <div class="form-group">
                <label>Current Profile Image</label>
                <img id="current_img_display" class="current-image" src="uploads/default_doctor.png">
                <br>
                <label for="profile_image" class="image-upload-label">Change Profile Image</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
                <div class="error-message" id="edit_image_error"></div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="edit_first_name">First Name</label>
                    <input type="text" id="edit_first_name" name="first_name" required>
                    <div class="error-message" id="edit_first_name_error"></div>
                </div>
                <div class="form-group">
                    <label for="edit_last_name">Last Name</label>
                    <input type="text" id="edit_last_name" name="last_name" required>
                    <div class="error-message" id="edit_last_name_error"></div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="edit_education">Education</label>
                <input type="text" id="edit_education" name="education" required>
                <div class="error-message" id="edit_education_error"></div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="edit_phone">Phone</label>
                    <input type="tel" id="edit_phone" name="phone" required>
                    <div class="error-message" id="edit_phone_error"></div>
                </div>
                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" required>
                    <div class="error-message" id="edit_email_error"></div>
                </div>
            </div>
            
            
            <div style="margin-top: 20px;">
                <button type="submit" class="btn-save">Save Changes</button>
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast"></div>

<script>
// Modal functions
function openEditModal(id, firstName, lastName, education, phone, email, specializationId, profileImage) {
    document.getElementById('edit_doctor_id').value = id;
    document.getElementById('edit_first_name').value = firstName;
    document.getElementById('edit_last_name').value = lastName;
    document.getElementById('edit_education').value = education;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_email').value = email;
    
    document.getElementById('current_image').value = profileImage;
    
    // Display current image
    const imgDisplay = document.getElementById('current_img_display');
    imgDisplay.src = profileImage && profileImage !== '' ? profileImage : 'uploads/default_doctor.png';
    
    // Clear any previous error messages
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(el => el.style.display = 'none');
    
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Close modal when clicking outside of it
window.onclick = function(event) {
    const modal = document.getElementById('editModal');
    if (event.target == modal) {
        closeEditModal();
    }
}

function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this doctor?")) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = 'Admin_doctor.php';
        var input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete';
        input.value = id;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

// Toast notification function
function showToast(message, isSuccess = false) {
    const toast = document.getElementById('toast');
    toast.innerHTML = isSuccess ? 
        `<i class="fas fa-check-circle"></i> ${message}` : 
        `<i class="fas fa-exclamation-circle"></i> ${message}`;
    toast.className = isSuccess ? 'toast success show' : 'toast show';
    
    setTimeout(() => {
        toast.className = toast.className.replace('show', '');
    }, 3000);
}

// Validation functions
function validateFirstName() {
    const firstName = document.getElementById('edit_first_name');
    const errorElement = document.getElementById('edit_first_name_error');
    
    if (firstName.value.trim() === '') {
        errorElement.textContent = 'First name is required';
        errorElement.style.display = 'block';
        return false;
    } else if (!/^[a-zA-Z\s]+$/.test(firstName.value.trim())) {
        errorElement.textContent = 'First name should contain only letters and spaces';
        errorElement.style.display = 'block';
        return false;
    } else {
        errorElement.style.display = 'none';
        return true;
    }
}

function validateLastName() {
    const lastName = document.getElementById('edit_last_name');
    const errorElement = document.getElementById('edit_last_name_error');
    
    if (lastName.value.trim() === '') {
        errorElement.textContent = 'Last name is required';
        errorElement.style.display = 'block';
        return false;
    } else if (!/^[a-zA-Z\s]+$/.test(lastName.value.trim())) {
        errorElement.textContent = 'Last name should contain only letters and spaces';
        errorElement.style.display = 'block';
        return false;
    } else {
        errorElement.style.display = 'none';
        return true;
    }
}

function validateEducation() {
    const education = document.getElementById('edit_education');
    const errorElement = document.getElementById('edit_education_error');
    
    if (education.value.trim() === '') {
        errorElement.textContent = 'Education is required';
        errorElement.style.display = 'block';
        return false;
    } else if (!/^[a-zA-Z\s\.\,]+$/.test(education.value.trim())) {
        errorElement.textContent = 'Education should contain only letters, spaces, dots, and commas';
        errorElement.style.display = 'block';
        return false;
    } else {
        errorElement.style.display = 'none';
        return true;
    }
}

function validatePhone() {
    const phone = document.getElementById('edit_phone');
    const errorElement = document.getElementById('edit_phone_error');
    
    if (phone.value.trim() === '') {
        errorElement.textContent = 'Phone number is required';
        errorElement.style.display = 'block';
        return false;
    } else if (!/^[0-9]{10}$/.test(phone.value.trim())) {
        errorElement.textContent = 'Phone number must be exactly 10 digits';
        errorElement.style.display = 'block';
        return false;
    } else {
        errorElement.style.display = 'none';
        return true;
    }
}

function validateEmail() {
    const email = document.getElementById('edit_email');
    const errorElement = document.getElementById('edit_email_error');
    
    if (email.value.trim() === '') {
        errorElement.textContent = 'Email is required';
        errorElement.style.display = 'block';
        return false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        errorElement.textContent = 'Invalid email format';
        errorElement.style.display = 'block';
        return false;
    } else {
        errorElement.style.display = 'none';
        return true;
    }
}

function validateSpecialization() {
    const specialization = document.getElementById('edit_specialization');
    const errorElement = document.getElementById('edit_specialization_error');
    
    if (specialization.value === '') {
        errorElement.textContent = 'Specialization is required';
        errorElement.style.display = 'block';
        return false;
    } else {
        errorElement.style.display = 'none';
        return true;
    }
}

function validateImage() {
    const imageInput = document.getElementById('profile_image');
    const errorElement = document.getElementById('edit_image_error');
    
    if (imageInput.files && imageInput.files[0]) {
        const file = imageInput.files[0];
        const allowed = ['jpg', 'jpeg', 'png', 'gif'];
        const ext = file.name.split('.').pop().toLowerCase();
        
        if (!allowed.includes(ext)) {
            errorElement.textContent = 'Only JPG, JPEG, PNG, and GIF files are allowed';
            errorElement.style.display = 'block';
            return false;
        } else if (file.size > 5 * 1024 * 1024) { // 5MB limit
            errorElement.textContent = 'Image size must be less than 5MB';
            errorElement.style.display = 'block';
            return false;
        }
    }
    
    errorElement.style.display = 'none';
    return true;
}

// Add event listeners for real-time validation
document.getElementById('edit_first_name').addEventListener('input', validateFirstName);
document.getElementById('edit_last_name').addEventListener('input', validateLastName);
document.getElementById('edit_education').addEventListener('input', validateEducation);
document.getElementById('edit_phone').addEventListener('input', validatePhone);
document.getElementById('edit_email').addEventListener('input', validateEmail);
document.getElementById('edit_specialization').addEventListener('change', validateSpecialization);
document.getElementById('profile_image').addEventListener('change', validateImage);

// Preview image when selected
document.getElementById('profile_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('current_img_display').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

// Form submission validation
document.getElementById('editForm').addEventListener('submit', function(e) {
    // Run all validation functions
    const isFirstNameValid = validateFirstName();
    const isLastNameValid = validateLastName();
    const isEducationValid = validateEducation();
    const isPhoneValid = validatePhone();
    const isEmailValid = validateEmail();
    const isSpecializationValid = validateSpecialization();
    const isImageValid = validateImage();
    
    // If any validation fails, prevent form submission
    if (!isFirstNameValid || !isLastNameValid || !isEducationValid || !isPhoneValid || !isEmailValid || !isSpecializationValid || !isImageValid) {
        e.preventDefault();
        showToast('Please correct the errors in the form');
    }
});

// Open edit modal if edit parameter is in POST
<?php if (isset($_POST['edit']) && $doctor_data): ?>
window.addEventListener('load', function() {
    openEditModal(
        <?php echo $doctor_data['DOCTOR_ID']; ?>,
        '<?php echo addslashes($doctor_data['FIRST_NAME']); ?>',
        '<?php echo addslashes($doctor_data['LAST_NAME']); ?>',
        '<?php echo addslashes($doctor_data['EDUCATION']); ?>',
        '<?php echo $doctor_data['PHONE']; ?>',
        '<?php echo $doctor_data['EMAIL']; ?>',
      
        '<?php echo $doctor_data['PROFILE_IMAGE']; ?>'
    );
});
<?php endif; ?>
</script>

</body>
</html>