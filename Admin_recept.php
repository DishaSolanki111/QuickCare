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

$adminName = $_SESSION['USER_NAME'] ?? 'Admin';

$success_message = "";
$error_message = "";

// ============================================================
// FIX: Handle DELETE first — before ANY HTML output so that
//      header() redirect works without "headers already sent"
// ============================================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['id'])) {
    $receptionist_id = (int)$_POST['id'];

    if ($receptionist_id > 0) {
        // Cascade delete manually to fix foreign key constraint block
        mysqli_query($conn, "DELETE FROM appointment_reminder_tbl WHERE RECEPTIONIST_ID = $receptionist_id");
        mysqli_query($conn, "DELETE FROM medicine_reminder_tbl WHERE CREATOR_ROLE = 'RECEPTIONIST' AND CREATOR_ID = $receptionist_id");
        mysqli_query($conn, "DELETE FROM doctor_schedule_tbl WHERE RECEPTIONIST_ID = $receptionist_id");
        mysqli_query($conn, "DELETE FROM medicine_tbl WHERE RECEPTIONIST_ID = $receptionist_id");

        $stmt = mysqli_prepare($conn, "DELETE FROM receptionist_tbl WHERE RECEPTIONIST_ID = ?");
        mysqli_stmt_bind_param($stmt, "i", $receptionist_id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header("Location: Admin_recept.php?success=" . urlencode("Receptionist deleted successfully!"));
        } else {
            $err = mysqli_error($conn);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header("Location: Admin_recept.php?error=" . urlencode("Error deleting receptionist: " . $err));
        }
        exit();
    }
}

// Handle edit form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit_receptionist') {
    $receptionist_id = (int)$_POST['receptionist_id'];
    $first_name = trim($_POST['first_name']);
    $last_name   = trim($_POST['last_name']);
    $dob         = trim($_POST['dob']);
    $doj         = trim($_POST['doj']);
    $gender      = trim($_POST['gender']);
    $phone       = trim($_POST['phone']);
    $email       = trim($_POST['email']);
    $address     = isset($_POST['address']) ? trim($_POST['address']) : '';

    $errors = [];

    // Name validation
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

    // Date validation
    if (empty($dob)) {
        $errors[] = "Date of Birth is required.";
    } else {
        $dob_date = new DateTime($dob);
        $today    = new DateTime();
        if ($dob_date > $today) {
            $errors[] = "Date of Birth cannot be in the future.";
        }
    }

    if (empty($doj)) {
        $errors[] = "Date of Joining is required.";
    } else {
        $doj_date = new DateTime($doj);
        $today    = new DateTime();
        if ($doj_date > $today) {
            $errors[] = "Date of Joining cannot be in the future.";
        }
    }

    if (!empty($dob) && !empty($doj)) {
        $dob_date = new DateTime($dob);
        $doj_date = new DateTime($doj);
        if ($doj_date <= $dob_date) {
            $errors[] = "Date of Joining must be after Date of Birth.";
        }
        $age_diff = $dob_date->diff($doj_date)->y;
        if ($age_diff < 23) {
            $errors[] = "Receptionist must be at least 23 years old at date of joining.";
        }
    }

    // Phone validation
    if (empty($phone)) {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = "Phone number must be exactly 10 digits.";
    }

    // Email validation
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Gender validation
    if (empty($gender)) {
        $errors[] = "Gender is required.";
    } elseif (!in_array($gender, ['Male', 'Female', 'Other'])) {
        $errors[] = "Invalid gender value.";
    }

    if (empty($errors)) {
        $query = "UPDATE receptionist_tbl SET 
                 FIRST_NAME = ?, 
                 LAST_NAME  = ?, 
                 DOB        = ?, 
                 DOJ        = ?, 
                 GENDER     = ?, 
                 PHONE      = ?, 
                 EMAIL      = ?, 
                 ADDRESS    = ?
                 WHERE RECEPTIONIST_ID = ?";

        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssssssi",
            $first_name, $last_name, $dob, $doj,
            $gender, $phone, $email, $address, $receptionist_id
        );

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

// Pull success/error from GET (set after delete redirect)
if (empty($success_message) && isset($_GET['success'])) {
    $success_message = htmlspecialchars($_GET['success']);
}
if (empty($error_message) && isset($_GET['error'])) {
    $error_message = htmlspecialchars($_GET['error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Receptionists - QuickCare</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
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
        background: #072D44;
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
        background: var(--dark-blue);
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .action-btn {
        padding: 5px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        margin-right: 5px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .actions-td {
        white-space: nowrap;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }

    .actions-td .action-btn { margin-right: 0; }

    .edit-btn   { background: #f39c12; color: white; }
    .view-btn   { background: #000000; color: white; }
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

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0; top: 0;
        width: 100%; height: 100%;
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

    .close:hover { color: #000; }

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

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #D0D7E1;
        border-radius: 5px;
        box-sizing: border-box;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #5790AB;
        outline: none;
    }

    .form-row {
        display: flex;
        gap: 15px;
    }

    .form-row .form-group { flex: 1; }

    .btn-save {
        background: var(--soft-blue);
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    .btn-save:hover { background: var(--mid-blue); }

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

    .btn-cancel:hover { background: #5a6268; }

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

    /* Toast */
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

    .toast.success { background-color: #2ecc71; }

    @keyframes fadein {
        from { top: 0; opacity: 0; }
        to   { top: 20px; opacity: 1; }
    }

    @keyframes fadeout {
        from { opacity: 1; }
        to   { opacity: 0; }
    }
</style>
</head>

<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">

    <?php include 'admin_header.php'; ?>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <!-- Add New Receptionist button removed as requested -->

    <!-- Filter -->
    <div class="filter-container">
        <form method="POST">
            <input type="text" name="name_filter" placeholder="Filter by Name"
                value="<?php echo isset($_POST['name_filter']) ? htmlspecialchars($_POST['name_filter']) : ''; ?>">
            <button type="submit">
                <i class="bi bi-funnel"></i>
                Filter
            </button>
        </form>
    </div>

    <table>
        <tr>
            <th>Name</th>
            <th>Date of Birth</th>
            <th>Date of Joining</th>
            <th>Gender</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>

        <?php
        // Build query with prepared statement for filter
        if (isset($_POST['name_filter']) && $_POST['name_filter'] !== '') {
            $stmt_list = mysqli_prepare($conn, "SELECT * FROM receptionist_tbl WHERE CONCAT(FIRST_NAME,' ',LAST_NAME) LIKE ? ORDER BY FIRST_NAME, LAST_NAME");
            $name_param = '%' . $_POST['name_filter'] . '%';
            mysqli_stmt_bind_param($stmt_list, "s", $name_param);
        } else {
            $stmt_list = mysqli_prepare($conn, "SELECT * FROM receptionist_tbl ORDER BY FIRST_NAME, LAST_NAME");
        }

        mysqli_stmt_execute($stmt_list);
        $result = mysqli_stmt_get_result($stmt_list);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['FIRST_NAME'] . " " . $row['LAST_NAME']); ?></td>
                    <td><?php echo htmlspecialchars($row['DOB']); ?></td>
                    <td><?php echo htmlspecialchars($row['DOJ']); ?></td>
                    <td><?php echo htmlspecialchars($row['GENDER']); ?></td>
                    <td><?php echo htmlspecialchars($row['PHONE']); ?></td>
                    <td><?php echo htmlspecialchars($row['EMAIL']); ?></td>
                    <td class="actions-td">
                        <button class="action-btn edit-btn"
                            onclick="openEditModal(
                                <?php echo (int)$row['RECEPTIONIST_ID']; ?>,
                                '<?php echo addslashes(htmlspecialchars($row['FIRST_NAME'])); ?>',
                                '<?php echo addslashes(htmlspecialchars($row['LAST_NAME'])); ?>',
                                '<?php echo htmlspecialchars($row['DOB']); ?>',
                                '<?php echo htmlspecialchars($row['DOJ']); ?>',
                                '<?php echo htmlspecialchars($row['GENDER']); ?>',
                                '<?php echo htmlspecialchars($row['PHONE']); ?>',
                                '<?php echo htmlspecialchars($row['EMAIL']); ?>',
                                '<?php echo addslashes(htmlspecialchars($row['ADDRESS'] ?? '')); ?>'
                            )">
                            <i class="bi bi-pencil"></i> Edit
                        </button>

                        <button class="action-btn view-btn" type="button"
                            onclick="window.location.href='admin_receptionist_profile_view.php?receptionist_id=<?php echo (int)$row['RECEPTIONIST_ID']; ?>';">
                            <i class="bi bi-eye"></i> View
                        </button>

                        <button class="action-btn delete-btn"
                            onclick="deleteReceptionist(<?php echo (int)$row['RECEPTIONIST_ID']; ?>)">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </td>
                </tr>
                <?php
            }
        } else {
            echo "<tr><td colspan='7'>No receptionists found</td></tr>";
        }

        mysqli_stmt_close($stmt_list);
        // NOTE: $conn is intentionally NOT closed here.
        // It is closed at the very bottom of the file.
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

            <div class="form-group">
                <label for="edit_address">Address</label>
                <textarea id="edit_address" name="address" rows="2"
                    style="min-height:40px; max-height:80px; resize:vertical; font-size:14px;"
                    placeholder="e.g. 221B, Vinod Residency, Mumbai"></textarea>
                <div class="error-message" id="edit_address_error"></div>
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
function openEditModal(id, firstName, lastName, dob, doj, gender, phone, email, address) {
    document.getElementById('edit_receptionist_id').value = id;
    document.getElementById('edit_first_name').value = firstName;
    document.getElementById('edit_last_name').value  = lastName;
    document.getElementById('edit_dob').value    = dob;
    document.getElementById('edit_doj').value    = doj;
    document.getElementById('edit_gender').value = gender;
    document.getElementById('edit_phone').value  = phone;
    document.getElementById('edit_email').value  = email;
    document.getElementById('edit_address').value = address || '';

    document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('editModal');
    if (event.target == modal) closeEditModal();
};

// ============================================================
// FIX: deleteReceptionist posts action='delete' — matches the
//      PHP handler at the TOP of the file (before HTML output)
//      so header() redirect works perfectly.
// ============================================================
function deleteReceptionist(id) {
    if (confirm("Are you sure you want to delete this receptionist?")) {
        var f = document.createElement('form');
        f.method = 'POST';
        f.action = 'Admin_recept.php';

        var actionInput = document.createElement('input');
        actionInput.type  = 'hidden';
        actionInput.name  = 'action';
        actionInput.value = 'delete';

        var idInput = document.createElement('input');
        idInput.type  = 'hidden';
        idInput.name  = 'id';
        idInput.value = id;

        f.appendChild(actionInput);
        f.appendChild(idInput);
        document.body.appendChild(f);
        f.submit();
    }
}

function showToast(message, isSuccess = false) {
    const toast = document.getElementById('toast');
    toast.innerHTML = isSuccess
        ? `<i class="bi bi-check-circle"></i> ${message}`
        : `<i class="bi bi-exclamation-circle"></i> ${message}`;
    toast.className = isSuccess ? 'toast success show' : 'toast show';
    setTimeout(() => {
        toast.className = toast.className.replace('show', '');
    }, 3000);
}

// Validation
function validateFirstName() {
    const el = document.getElementById('edit_first_name');
    const err = document.getElementById('edit_first_name_error');
    if (el.value.trim() === '') {
        err.textContent = 'First name is required'; err.style.display = 'block'; return false;
    } else if (!/^[a-zA-Z]+$/.test(el.value.trim())) {
        err.textContent = 'First name should contain only letters'; err.style.display = 'block'; return false;
    }
    err.style.display = 'none'; return true;
}

function validateLastName() {
    const el = document.getElementById('edit_last_name');
    const err = document.getElementById('edit_last_name_error');
    if (el.value.trim() === '') {
        err.textContent = 'Last name is required'; err.style.display = 'block'; return false;
    } else if (!/^[a-zA-Z]+$/.test(el.value.trim())) {
        err.textContent = 'Last name should contain only letters'; err.style.display = 'block'; return false;
    }
    err.style.display = 'none'; return true;
}

function validateDOB() {
    const el  = document.getElementById('edit_dob');
    const err = document.getElementById('edit_dob_error');
    if (el.value === '') {
        err.textContent = 'Date of Birth is required'; err.style.display = 'block'; return false;
    }
    if (new Date(el.value) > new Date()) {
        err.textContent = 'Date of Birth cannot be in future'; err.style.display = 'block'; return false;
    }
    err.style.display = 'none'; return true;
}

function validateDOJ() {
    const doj = document.getElementById('edit_doj');
    const dob = document.getElementById('edit_dob');
    const err = document.getElementById('edit_doj_error');

    if (doj.value === '') {
        err.textContent = 'Date of Joining is required'; err.style.display = 'block'; return false;
    }
    if (new Date(doj.value) > new Date()) {
        err.textContent = 'Date of Joining cannot be in future'; err.style.display = 'block'; return false;
    }
    if (dob.value !== '') {
        const dobDate = new Date(dob.value);
        const dojDate = new Date(doj.value);
        if (dojDate <= dobDate) {
            err.textContent = 'Date of Joining must be after Date of Birth'; err.style.display = 'block'; return false;
        }
        // Age check: at least 23 years
        const ageDiff = dojDate.getFullYear() - dobDate.getFullYear();
        const m = dojDate.getMonth() - dobDate.getMonth();
        const adjustedAge = (m < 0 || (m === 0 && dojDate.getDate() < dobDate.getDate())) ? ageDiff - 1 : ageDiff;
        if (adjustedAge < 23) {
            err.textContent = 'Receptionist must be at least 23 years old at date of joining'; err.style.display = 'block'; return false;
        }
    }
    err.style.display = 'none'; return true;
}

function validatePhone() {
    const el  = document.getElementById('edit_phone');
    const err = document.getElementById('edit_phone_error');
    if (el.value.trim() === '') {
        err.textContent = 'Phone number is required'; err.style.display = 'block'; return false;
    } else if (!/^[0-9]{10}$/.test(el.value.trim())) {
        err.textContent = 'Phone number must be exactly 10 digits'; err.style.display = 'block'; return false;
    }
    err.style.display = 'none'; return true;
}

function validateEmail() {
    const el  = document.getElementById('edit_email');
    const err = document.getElementById('edit_email_error');
    if (el.value.trim() === '') {
        err.textContent = 'Email is required'; err.style.display = 'block'; return false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(el.value.trim())) {
        err.textContent = 'Invalid email format'; err.style.display = 'block'; return false;
    }
    err.style.display = 'none'; return true;
}

function validateGender() {
    const el  = document.getElementById('edit_gender');
    const err = document.getElementById('edit_gender_error');
    if (el.value === '') {
        err.textContent = 'Gender is required'; err.style.display = 'block'; return false;
    }
    err.style.display = 'none'; return true;
}

// Real-time listeners
document.getElementById('edit_first_name').addEventListener('input', validateFirstName);
document.getElementById('edit_last_name').addEventListener('input', validateLastName);
document.getElementById('edit_dob').addEventListener('change', function() { validateDOB(); validateDOJ(); });
document.getElementById('edit_doj').addEventListener('change', validateDOJ);
document.getElementById('edit_phone').addEventListener('input', validatePhone);
document.getElementById('edit_email').addEventListener('input', validateEmail);
document.getElementById('edit_gender').addEventListener('change', validateGender);

document.getElementById('editForm').addEventListener('submit', function(e) {
    const valid =
        validateFirstName() &
        validateLastName()  &
        validateDOB()       &
        validateDOJ()       &
        validatePhone()     &
        validateEmail()     &
        validateGender();

    if (!valid) {
        e.preventDefault();
        showToast('Please correct the errors in the form');
    }
});
</script>

</body>
</html>
<?php
// Close connection at the very end — after ALL queries are done
mysqli_close($conn);
?>