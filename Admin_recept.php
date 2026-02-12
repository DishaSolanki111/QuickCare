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
// Get admin name for display
$adminName = $_SESSION['USER_NAME'] ?? 'Admin';

// Initialize messages
 $success_message = "";
 $error_message = "";

// Handle edit form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit_receptionist') {
    $receptionist_id = $_POST['receptionist_id'];
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $doj = mysqli_real_escape_string($conn, $_POST['doj']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $errors = [];
    
    // ---------------- NAME VALIDATION ----------------
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    } elseif (!preg_match("/^[a-zA-Z]+$/", $first_name)) {
        $errors[] = "First name should contain only letters.";
    }
    
    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    } elseif (!preg_match("/^[a-zA-Z]+$/", $last_name)) {
        $errors[] = "Last name should contain only letters.";
    }
    
    // ---------------- DATE VALIDATION ----------------
    if (empty($dob)) {
        $errors[] = "Date of Birth is required.";
    } else {
        $dob_date = new DateTime($dob);
        $today = new DateTime();
        if ($dob_date > $today) {
            $errors[] = "Date of Birth cannot be in future.";
        }
    }
    
    if (empty($doj)) {
        $errors[] = "Date of Joining is required.";
    } else {
        $doj_date = new DateTime($doj);
        $today = new DateTime();
        if ($doj_date > $today) {
            $errors[] = "Date of Joining cannot be in future.";
        }
    }
    
    if (!empty($dob) && !empty($doj)) {
        $dob_date = new DateTime($dob);
        $doj_date = new DateTime($doj);
        if ($doj_date <= $dob_date) {
            $errors[] = "Date of Joining must be after Date of Birth.";
        }
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
    
    // ---------------- GENDER VALIDATION ----------------
    if (empty($gender)) {
        $errors[] = "Gender is required.";
    } elseif (!in_array($gender, ['Male', 'Female', 'Other'])) {
        $errors[] = "Invalid gender value.";
    }
    
    if (empty($errors)) {
        // Update receptionist data
        $query = "UPDATE receptionist_tbl SET 
                 FIRST_NAME = ?, 
                 LAST_NAME = ?, 
                 DOB = ?, 
                 DOJ = ?, 
                 GENDER = ?, 
                 PHONE = ?, 
                 EMAIL = ? 
                 WHERE RECEPTIONIST_ID = ?";
        
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "sssssssi", $first_name, $last_name, $dob, $doj, $gender, $phone, $email, $receptionist_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Receptionist information updated successfully.";
        } else {
            $error_message = "Error updating receptionist: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_message = implode("<br>", $errors);
    }
}

// Handle delete
if (isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['id'])) {
    $receptionist_id = $_POST['id'];
    $query = "DELETE FROM receptionist_tbl WHERE RECEPTIONIST_ID = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $receptionist_id);
    
    if (mysqli_stmt_execute($stmt)) {
        $success_message = "Receptionist deleted successfully!";
    } else {
        $error_message = "Error deleting receptionist: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
    
    // Redirect to remove delete parameters from URL
    header("Location: Admin_recept.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Receptionists - QuickCare</title>
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
    }

    .filter-container input {
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
        margin-right: 5px;
    }

    .edit-btn { background: #f39c12; color: white; }
    .delete-btn { background: #e74c3c; color: white; }

    .add-btn {
        background: #2ecc71;
        color: white;
        padding: 10px 15px;
        border-radius: 5px;
        text-decoration: none;
        display: inline-block;
        margin-bottom: 20px;
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
</style>
</head>

<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">

    <div class="topbar">
        <h1>Manage Receptionists</h1>
        <p>Welcome, <?php echo htmlspecialchars($adminName); ?></p>
    </div>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <a href="recpt_regis.php" class="add-btn">+ Add New Receptionist</a>

    <!-- FILTER BY NAME ONLY -->
    <div class="filter-container">
        <form method="POST">
            <input type="text" name="name_filter" placeholder="Filter by Name"
                value="<?php echo isset($_POST['name_filter']) ? htmlspecialchars($_POST['name_filter']) : ''; ?>">
            <button type="submit">Filter</button>
        </form>
    </div>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Date of Birth</th>
            <th>Date of Joining</th>
            <th>Gender</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>

        <?php
        $query = "SELECT * FROM receptionist_tbl";

        if (isset($_POST['name_filter']) && $_POST['name_filter'] !== '') {
            $name = mysqli_real_escape_string($conn, $_POST['name_filter']);
            $query .= " WHERE CONCAT(FIRST_NAME,' ',LAST_NAME) LIKE '%$name%'";
        }

        $query .= " ORDER BY FIRST_NAME, LAST_NAME";

        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><?php echo $row['RECEPTIONIST_ID']; ?></td>
                    <td><?php echo $row['FIRST_NAME']." ".$row['LAST_NAME']; ?></td>
                    <td><?php echo $row['DOB']; ?></td>
                    <td><?php echo $row['DOJ']; ?></td>
                    <td><?php echo $row['GENDER']; ?></td>
                    <td><?php echo $row['PHONE']; ?></td>
                    <td><?php echo $row['EMAIL']; ?></td>
                    <td>
                        <button class="action-btn edit-btn"
                            onclick="openEditModal(<?php echo $row['RECEPTIONIST_ID']; ?>, '<?php echo addslashes($row['FIRST_NAME']); ?>', '<?php echo addslashes($row['LAST_NAME']); ?>', '<?php echo $row['DOB']; ?>', '<?php echo $row['DOJ']; ?>', '<?php echo $row['GENDER']; ?>', '<?php echo $row['PHONE']; ?>', '<?php echo $row['EMAIL']; ?>')">Edit</button>
                        <button class="action-btn delete-btn"
                            onclick="deleteReceptionist(<?php echo $row['RECEPTIONIST_ID']; ?>)">Delete</button>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='8'>No receptionists found</td></tr>";
        }

        mysqli_close($conn);
        ?>
    </table>

</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Receptionist</h2>
        <form id="editForm" method="post" action="Admin_recept.php">
            <input type="hidden" name="action" value="edit_receptionist">
            <input type="hidden" id="edit_receptionist_id" name="receptionist_id">
            
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
                    <label for="edit_doj">Date of Joining</label>
                    <input type="date" id="edit_doj" name="doj" required>
                    <div class="error-message" id="edit_doj_error"></div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="edit_gender">Gender</label>
                    <select id="edit_gender" name="gender" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                    <div class="error-message" id="edit_gender_error"></div>
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
function openEditModal(id, firstName, lastName, dob, doj, gender, phone, email) {
    document.getElementById('edit_receptionist_id').value = id;
    document.getElementById('edit_first_name').value = firstName;
    document.getElementById('edit_last_name').value = lastName;
    document.getElementById('edit_dob').value = dob;
    document.getElementById('edit_doj').value = doj;
    document.getElementById('edit_gender').value = gender;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_email').value = email;
    
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

function deleteReceptionist(id) {
    if (confirm("Are you sure you want to delete this receptionist?")) {
        var f = document.createElement('form');
        f.method = 'POST';
        f.action = 'Admin_recept.php';
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
    } else if (!/^[a-zA-Z]+$/.test(firstName.value.trim())) {
        errorElement.textContent = 'First name should contain only letters';
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
    } else if (!/^[a-zA-Z]+$/.test(lastName.value.trim())) {
        errorElement.textContent = 'Last name should contain only letters';
        errorElement.style.display = 'block';
        return false;
    } else {
        errorElement.style.display = 'none';
        return true;
    }
}

function validateDOB() {
    const dob = document.getElementById('edit_dob');
    const errorElement = document.getElementById('edit_dob_error');
    
    if (dob.value === '') {
        errorElement.textContent = 'Date of Birth is required';
        errorElement.style.display = 'block';
        return false;
    } else {
        const dobDate = new Date(dob.value);
        const today = new Date();
        if (dobDate > today) {
            errorElement.textContent = 'Date of Birth cannot be in future';
            errorElement.style.display = 'block';
            return false;
        } else {
            errorElement.style.display = 'none';
            return true;
        }
    }
}

function validateDOJ() {
    const doj = document.getElementById('edit_doj');
    const dob = document.getElementById('edit_dob');
    const errorElement = document.getElementById('edit_doj_error');
    
    if (doj.value === '') {
        errorElement.textContent = 'Date of Joining is required';
        errorElement.style.display = 'block';
        return false;
    } else {
        const dojDate = new Date(doj.value);
        const today = new Date();
        if (dojDate > today) {
            errorElement.textContent = 'Date of Joining cannot be in future';
            errorElement.style.display = 'block';
            return false;
        } else if (dob.value !== '') {
            const dobDate = new Date(dob.value);
            if (dojDate <= dobDate) {
                errorElement.textContent = 'Date of Joining must be after Date of Birth';
                errorElement.style.display = 'block';
                return false;
            }
        }
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

function validateGender() {
    const gender = document.getElementById('edit_gender');
    const errorElement = document.getElementById('edit_gender_error');
    
    if (gender.value === '') {
        errorElement.textContent = 'Gender is required';
        errorElement.style.display = 'block';
        return false;
    } else {
        errorElement.style.display = 'none';
        return true;
    }
}

// Add event listeners for real-time validation
document.getElementById('edit_first_name').addEventListener('input', validateFirstName);
document.getElementById('edit_last_name').addEventListener('input', validateLastName);
document.getElementById('edit_dob').addEventListener('change', function() {
    validateDOB();
    validateDOJ(); // Re-validate DOJ when DOB changes
});
document.getElementById('edit_doj').addEventListener('change', validateDOJ);
document.getElementById('edit_phone').addEventListener('input', validatePhone);
document.getElementById('edit_email').addEventListener('input', validateEmail);
document.getElementById('edit_gender').addEventListener('change', validateGender);

// Form submission validation
document.getElementById('editForm').addEventListener('submit', function(e) {
    // Run all validation functions
    const isFirstNameValid = validateFirstName();
    const isLastNameValid = validateLastName();
    const isDOBValid = validateDOB();
    const isDOJValid = validateDOJ();
    const isPhoneValid = validatePhone();
    const isEmailValid = validateEmail();
    const isGenderValid = validateGender();
    
    // If any validation fails, prevent form submission
    if (!isFirstNameValid || !isLastNameValid || !isDOBValid || !isDOJValid || !isPhoneValid || !isEmailValid || !isGenderValid) {
        e.preventDefault();
        showToast('Please correct the errors in the form');
    }
});
</script>

</body>
</html>