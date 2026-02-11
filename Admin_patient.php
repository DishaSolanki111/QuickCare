<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'config.php';
 $adminName = isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : 'Admin';

// Initialize messages
 $success_message = "";
 $error_message = "";

// Handle edit form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit_patient') {
    $patient_id = $_POST['patient_id'];
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $blood_group = mysqli_real_escape_string($conn, $_POST['blood_group']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($dob) || empty($gender) || empty($blood_group) || empty($phone) || empty($email)) {
        $error_message = "All fields are required.";
    } else {
        // Update patient data
        $query = "UPDATE patient_tbl SET 
                 FIRST_NAME = ?, 
                 LAST_NAME = ?, 
                 DOB = ?, 
                 GENDER = ?, 
                 BLOOD_GROUP = ?, 
                 PHONE = ?, 
                 EMAIL = ? 
                 WHERE PATIENT_ID = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssssi", $first_name, $last_name, $dob, $gender, $blood_group, $phone, $email, $patient_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Patient information updated successfully.";
        } else {
            $error_message = "Error updating patient: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Handle delete
if (isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['id'])) {
    $patient_id = $_POST['id'];
    $query = "DELETE FROM patient_tbl WHERE PATIENT_ID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $patient_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Patient deleted successfully!";
    } else {
        $error_message = "Error deleting patient: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
    
    // Redirect to remove delete parameters from URL
    header("Location: Admin_patient.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Patients - QuickCare</title>
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
        --danger-color: #e74c3c;
    }

    .main {
        margin-left: 250px;
        padding: 20px;
        width: calc(100% - 250px);
    }

    .topbar {
        background: white;
        padding: 15px 25px;
        border-radius: 10px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
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
        padding: 5px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        color: white;
        margin-right: 5px;
    }

    .edit-btn { background: #f39c12; }
    .delete-btn { background: #e74c3c; }

    .blood-group-badge {
        padding: 3px 8px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: bold;
        background-color: #e74c3c;
        color: white;
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
        margin: 5% auto;
        padding: 20px;
        border-radius: 10px;
        width: 80%;
        max-width: 600px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
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
        transition: border-color 0.3s;
    }
    
    .form-group input:focus, .form-group select:focus {
        border-color: var(--soft-blue);
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
        color: var(--danger-color);
        font-size: 12px;
        margin-top: 5px;
        display: none;
    }
    
    .form-group.error input,
    .form-group.error select {
        border-color: var(--danger-color);
    }
    
    .form-group.success input,
    .form-group.success select {
        border-color: #28a745;
    }
    
    /* Toast Notification */
    .toast {
        visibility: hidden;
        min-width: 250px;
        margin-left: -125px;
        background-color: #333;
        color: #fff;
        text-align: center;
        border-radius: 5px;
        padding: 16px;
        position: fixed;
        z-index: 1001;
        left: 50%;
        bottom: 30px;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    
    .toast.show {
        visibility: visible;
        animation: fadein 0.5s, fadeout 0.5s 2.5s;
    }
    
    .toast.success {
        background-color: #28a745;
    }
    
    .toast.error {
        background-color: var(--danger-color);
    }
    
    @keyframes fadein {
        from {bottom: 0; opacity: 0;}
        to {bottom: 30px; opacity: 1;}
    }
    
    @keyframes fadeout {
        from {bottom: 30px; opacity: 1;}
        to {bottom: 0; opacity: 0;}
    }
</style>
</head>

<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">

    <div class="topbar">
        <h1>Manage Patients</h1>
        <p>Welcome, <?php echo htmlspecialchars($adminName); ?></p>
    </div>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- FILTER (UI UNCHANGED, BUG FIXED) -->
    <div class="filter-container">
        <form method="POST" action="">
            <input type="text" name="name_filter" placeholder="Filter by Name"
                value="<?php echo isset($_POST['name_filter']) ? htmlspecialchars($_POST['name_filter']) : ''; ?>">

            <select name="blood_group_filter">
                <option value="">All Blood Groups</option>
                <?php
                $blood_groups = ['A+','A-','B+','B-','O+','O-','AB+','AB-'];
                foreach ($blood_groups as $bg) {
                    $selected = (isset($_POST['blood_group_filter']) && $_POST['blood_group_filter'] === $bg) ? 'selected' : '';
                    echo "<option value='$bg' $selected>$bg</option>";
                }
                ?>
            </select>

            <select name="gender_filter">
                <option value="">All Genders</option>
                <option value="MALE" <?php if(isset($_POST['gender_filter']) && $_POST['gender_filter']=='MALE') echo 'selected'; ?>>Male</option>
                <option value="FEMALE" <?php if(isset($_POST['gender_filter']) && $_POST['gender_filter']=='FEMALE') echo 'selected'; ?>>Female</option>
                <option value="OTHER" <?php if(isset($_POST['gender_filter']) && $_POST['gender_filter']=='OTHER') echo 'selected'; ?>>Other</option>
            </select>

            <button type="submit">Filter</button>
        </form>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Date of Birth</th>
            <th>Gender</th>
            <th>Blood Group</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>

        <?php
        $query = "SELECT * FROM patient_tbl WHERE 1=1";

        if (!empty($_POST['name_filter'])) {
            $name = mysqli_real_escape_string($conn, $_POST['name_filter']);
            $query .= " AND CONCAT(FIRST_NAME,' ',LAST_NAME) LIKE '%$name%'";
        }

        if (!empty($_POST['blood_group_filter'])) {
            $bg = mysqli_real_escape_string($conn, $_POST['blood_group_filter']);
            $query .= " AND BLOOD_GROUP = '$bg'";
        }

        if (!empty($_POST['gender_filter'])) {
            $gender = mysqli_real_escape_string($conn, $_POST['gender_filter']);
            $query .= " AND GENDER = '$gender'";
        }

        $query .= " ORDER BY FIRST_NAME, LAST_NAME";

        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                    <td>{$row['PATIENT_ID']}</td>
                    <td>{$row['FIRST_NAME']} {$row['LAST_NAME']}</td>
                    <td>{$row['DOB']}</td>
                    <td>{$row['GENDER']}</td>
                    <td><span class='blood-group-badge'>{$row['BLOOD_GROUP']}</span></td>
                    <td>{$row['PHONE']}</td>
                    <td>{$row['EMAIL']}</td>
                    <td>
                        <button class='action-btn edit-btn'
                            onclick=\"openEditModal({$row['PATIENT_ID']}, '" . addslashes($row['FIRST_NAME']) . "', '" . addslashes($row['LAST_NAME']) . "', '{$row['DOB']}', '{$row['GENDER']}', '{$row['BLOOD_GROUP']}', '{$row['PHONE']}', '{$row['EMAIL']}')\">Edit</button>
                        <button class='action-btn delete-btn'
                            onclick=\"deletePatient({$row['PATIENT_ID']})\">Delete</button>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='8'>No patients found</td></tr>";
        }

        mysqli_close($conn);
        ?>
    </table>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Patient</h2>
        <form id="editForm" method="post" action="admin_patient.php">
            <input type="hidden" name="action" value="edit_patient">
            <input type="hidden" id="edit_patient_id" name="patient_id">
            
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
            
            <div class="form-row">
                <div class="form-group">
                    <label for="edit_dob">Date of Birth</label>
                    <input type="date" id="edit_dob" name="dob" required>
                    <div class="error-message" id="edit_dob_error"></div>
                </div>
                <div class="form-group">
                    <label for="edit_gender">Gender</label>
                    <select id="edit_gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="MALE">Male</option>
                        <option value="FEMALE">Female</option>
                        <option value="OTHER">Other</option>
                    </select>
                    <div class="error-message" id="edit_gender_error"></div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="edit_blood_group">Blood Group</label>
                    <select id="edit_blood_group" name="blood_group" required>
                        <option value="">Select Blood Group</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                    </select>
                    <div class="error-message" id="edit_blood_group_error"></div>
                </div>
                <div class="form-group">
                    <label for="edit_phone">Phone</label>
                    <input type="tel" id="edit_phone" name="phone" required>
                    <div class="error-message" id="edit_phone_error"></div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="edit_email">Email</label>
                <input type="email" id="edit_email" name="email" required>
                <div class="error-message" id="edit_email_error"></div>
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
function openEditModal(id, firstName, lastName, dob, gender, bloodGroup, phone, email) {
    document.getElementById('edit_patient_id').value = id;
    document.getElementById('edit_first_name').value = firstName;
    document.getElementById('edit_last_name').value = lastName;
    document.getElementById('edit_dob').value = dob;
    document.getElementById('edit_gender').value = gender;
    document.getElementById('edit_blood_group').value = bloodGroup;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_email').value = email;
    
    // Clear any previous error messages
    const errorElements = document.querySelectorAll('.error-message');
    errorElements.forEach(el => el.style.display = 'none');
    
    // Remove error classes from form groups
    const formGroups = document.querySelectorAll('.form-group');
    formGroups.forEach(el => el.classList.remove('error', 'success'));
    
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

function deletePatient(id) {
    if (confirm("Are you sure you want to delete this patient?")) {
        var f = document.createElement('form');
        f.method = 'POST';
        f.action = 'Admin_patient.php';
        var a = document.createElement('input');
        a.type = 'hidden';
        a.name = 'action';
        a.value = 'delete';
        var i = document.createElement('input');
        i.type = 'hidden';
        i.name = 'id';
        i.value = id;
        f.appendChild(a);
        f.appendChild(i);
        document.body.appendChild(f);
        f.submit();
    }
}

// Toast notification function
function showToast(message, isSuccess = false) {
    const toast = document.getElementById('toast');
    toast.innerHTML = isSuccess ? 
        `<i class="fas fa-check-circle"></i> ${message}` : 
        `<i class="fas fa-exclamation-circle"></i> ${message}`;
    toast.className = isSuccess ? 'toast success show' : 'toast error show';
    
    setTimeout(() => {
        toast.className = toast.className.replace('show', '');
    }, 3000);
}

// Validation functions
function validateFirstName() {
    const firstName = document.getElementById('edit_first_name');
    const errorElement = document.getElementById('edit_first_name_error');
    const formGroup = firstName.closest('.form-group');
    
    if (firstName.value.trim() === '') {
        errorElement.textContent = 'First name is required';
        errorElement.style.display = 'block';
        formGroup.classList.add('error');
        formGroup.classList.remove('success');
        return false;
    } else if (!/^[a-zA-Z\s]+$/.test(firstName.value.trim())) {
        errorElement.textContent = 'First name should contain only letters and spaces';
        errorElement.style.display = 'block';
        formGroup.classList.add('error');
        formGroup.classList.remove('success');
        return false;
    } else {
        errorElement.style.display = 'none';
        formGroup.classList.remove('error');
        formGroup.classList.add('success');
        return true;
    }
}

function validateLastName() {
    const lastName = document.getElementById('edit_last_name');
    const errorElement = document.getElementById('edit_last_name_error');
    const formGroup = lastName.closest('.form-group');
    
    if (lastName.value.trim() === '') {
        errorElement.textContent = 'Last name is required';
        errorElement.style.display = 'block';
        formGroup.classList.add('error');
        formGroup.classList.remove('success');
        return false;
    } else if (!/^[a-zA-Z\s]+$/.test(lastName.value.trim())) {
        errorElement.textContent = 'Last name should contain only letters and spaces';
        errorElement.style.display = 'block';
        formGroup.classList.add('error');
        formGroup.classList.remove('success');
        return false;
    } else {
        errorElement.style.display = 'none';
        formGroup.classList.remove('error');
        formGroup.classList.add('success');
        return true;
    }
}

function validateDOB() {
    const dob = document.getElementById('edit_dob');
    const errorElement = document.getElementById('edit_dob_error');
    const formGroup = dob.closest('.form-group');
    const selectedDate = new Date(dob.value);
    const today = new Date();
    
    if (dob.value === '') {
        errorElement.textContent = 'Date of birth is required';
        errorElement.style.display = 'block';
        formGroup.classList.add('error');
        formGroup.classList.remove('success');
        return false;
    } else if (selectedDate > today) {
        errorElement.textContent = 'Date of birth cannot be in the future';
        errorElement.style.display = 'block';
        formGroup.classList.add('error');
        formGroup.classList.remove('success');
        return false;
    } else {
        // Check if age is reasonable (e.g., not more than 120 years)
        const age = today.getFullYear() - selectedDate.getFullYear();
        if (age > 120) {
            errorElement.textContent = 'Invalid date of birth';
            errorElement.style.display = 'block';
            formGroup.classList.add('error');
            formGroup.classList.remove('success');
            return false;
        }
        errorElement.style.display = 'none';
        formGroup.classList.remove('error');
        formGroup.classList.add('success');
        return true;
    }
}

function validateGender() {
    const gender = document.getElementById('edit_gender');
    const errorElement = document.getElementById('edit_gender_error');
    const formGroup = gender.closest('.form-group');
    
    if (gender.value === '') {
        errorElement.textContent = 'Gender is required';
        errorElement.style.display = 'block';
        formGroup.classList.add('error');
        formGroup.classList.remove('success');
        return false;
    } else {
        errorElement.style.display = 'none';
        formGroup.classList.remove('error');
        formGroup.classList.add('success');
        return true;
    }
}

function validateBloodGroup() {
    const bloodGroup = document.getElementById('edit_blood_group');
    const errorElement = document.getElementById('edit_blood_group_error');
    const formGroup = bloodGroup.closest('.form-group');
    
    if (bloodGroup.value === '') {
        errorElement.textContent = 'Blood group is required';
        errorElement.style.display = 'block';
        formGroup.classList.add('error');
        formGroup.classList.remove('success');
        return false;
    } else {
        errorElement.style.display = 'none';
        formGroup.classList.remove('error');
        formGroup.classList.add('success');
        return true;
    }
}

function validatePhone() {
    const phone = document.getElementById('edit_phone');
    const errorElement = document.getElementById('edit_phone_error');
    const formGroup = phone.closest('.form-group');
    
    if (phone.value.trim() === '') {
        errorElement.textContent = 'Phone number is required';
        errorElement.style.display = 'block';
        formGroup.classList.add('error');
        formGroup.classList.remove('success');
        return false;
    } else if (!/^[0-9]{10}$/.test(phone.value.trim())) {
        errorElement.textContent = 'Phone number must be exactly 10 digits';
        errorElement.style.display = 'block';
        formGroup.classList.add('error');
        formGroup.classList.remove('success');
        return false;
    } else {
        errorElement.style.display = 'none';
        formGroup.classList.remove('error');
        formGroup.classList.add('success');
        return true;
    }
}

function validateEmail() {
    const email = document.getElementById('edit_email');
    const errorElement = document.getElementById('edit_email_error');
    const formGroup = email.closest('.form-group');
    
    if (email.value.trim() === '') {
        errorElement.textContent = 'Email is required';
        errorElement.style.display = 'block';
        formGroup.classList.add('error');
        formGroup.classList.remove('success');
        return false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value.trim())) {
        errorElement.textContent = 'Invalid email format';
        errorElement.style.display = 'block';
        formGroup.classList.add('error');
        formGroup.classList.remove('success');
        return false;
    } else {
        errorElement.style.display = 'none';
        formGroup.classList.remove('error');
        formGroup.classList.add('success');
        return true;
    }
}

// Add event listeners for real-time validation
document.getElementById('edit_first_name').addEventListener('input', validateFirstName);
document.getElementById('edit_last_name').addEventListener('input', validateLastName);
document.getElementById('edit_dob').addEventListener('change', validateDOB);
document.getElementById('edit_gender').addEventListener('change', validateGender);
document.getElementById('edit_blood_group').addEventListener('change', validateBloodGroup);
document.getElementById('edit_phone').addEventListener('input', validatePhone);
document.getElementById('edit_email').addEventListener('input', validateEmail);

// Form submission validation
document.getElementById('editForm').addEventListener('submit', function(e) {
    // Run all validation functions
    const isFirstNameValid = validateFirstName();
    const isLastNameValid = validateLastName();
    const isDOBValid = validateDOB();
    const isGenderValid = validateGender();
    const isBloodGroupValid = validateBloodGroup();
    const isPhoneValid = validatePhone();
    const isEmailValid = validateEmail();
    
    // If any validation fails, prevent form submission
    if (!isFirstNameValid || !isLastNameValid || !isDOBValid || !isGenderValid || !isBloodGroupValid || !isPhoneValid || !isEmailValid) {
        e.preventDefault();
        showToast('Please correct the errors in the form', false);
    }
});
</script>

</body>
</html>