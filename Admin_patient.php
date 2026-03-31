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

// Initialize messages
$success_message = "";
$error_message = "";

// Handle edit form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'edit_patient') {
    $patient_id  = (int)$_POST['patient_id'];
    $first_name  = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name   = mysqli_real_escape_string($conn, $_POST['last_name']);
    $dob         = mysqli_real_escape_string($conn, $_POST['dob']);
    $gender      = mysqli_real_escape_string($conn, $_POST['gender']);
    $blood_group = mysqli_real_escape_string($conn, $_POST['blood_group']);
    $phone       = mysqli_real_escape_string($conn, $_POST['phone']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $address     = isset($_POST['address']) ? mysqli_real_escape_string($conn, $_POST['address']) : '';

    if (empty($first_name) || empty($last_name) || empty($dob) || empty($gender) || empty($blood_group) || empty($phone) || empty($email)) {
        $error_message = "All fields are required.";
    } else {
        $query = "UPDATE patient_tbl SET
                 FIRST_NAME = ?,
                 LAST_NAME = ?,
                 DOB = ?,
                 GENDER = ?,
                 BLOOD_GROUP = ?,
                 PHONE = ?,
                 EMAIL = ?,
                 ADDRESS = ?
                 WHERE PATIENT_ID = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssssssssi", $first_name, $last_name, $dob, $gender, $blood_group, $phone, $email, $address, $patient_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Patient information updated successfully.";
        } else {
            $error_message = "Error updating patient: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// Pull success/error from GET params
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
<title>Manage Patients - QuickCare</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
        color: white;
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

    .actions-td .action-btn {
        margin-right: 0;
    }

    .edit-btn { background: #f39c12; }
    .view-btn { background: #000000; }

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

    .form-group input, .form-group select, .form-group textarea {
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

    .toast.success { background-color: #28a745; }
    .toast.error { background-color: var(--danger-color); }

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

<?php include 'admin_header.php'; ?>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

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
                $pid     = $row['PATIENT_ID'];
                $fname   = addslashes($row['FIRST_NAME']);
                $lname   = addslashes($row['LAST_NAME']);
                $dob     = $row['DOB'];
                $gender  = $row['GENDER'];
                $bg      = $row['BLOOD_GROUP'];
                $phone   = $row['PHONE'];
                $email   = $row['EMAIL'];
                $address = addslashes($row['ADDRESS'] ?? '');

                echo "<tr>
                    <td>{$row['FIRST_NAME']} {$row['LAST_NAME']}</td>
                    <td>$dob</td>
                    <td>$gender</td>
                    <td><span class='blood-group-badge'>$bg</span></td>
                    <td>$phone</td>
                    <td>$email</td>
                    <td class='actions-td'>
                       
                        <button class='action-btn view-btn'
                            onclick=\"viewPatient($pid)\">
                            <i class='bi bi-eye'></i>
                            View
                        </button>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No patients found</td></tr>";
        }

        mysqli_close($conn);
        ?>
    </table>

    <!-- Hidden form for view navigation -->
    <form id="viewPatientForm" method="POST" style="display:none;">
        <input type="hidden" name="patient_id" id="view_patient_id" value="">
    </form>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>
        <h2>Edit Patient</h2>
        <form id="editForm" method="post" action="Admin_patient.php">
            <input type="hidden" name="action" value="edit_patient">
            <input type="hidden" id="edit_patient_id" name="patient_id">

            <div class="form-row">
                <div class="form-group">
                    <label for="edit_first_name">First Name</label>
                    <input type="text" id="edit_first_name" name="first_name" required>
                </div>
                <div class="form-group">
                    <label for="edit_last_name">Last Name</label>
                    <input type="text" id="edit_last_name" name="last_name" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="edit_dob">Date of Birth</label>
                    <input type="date" id="edit_dob" name="dob" required>
                </div>
                <div class="form-group">
                    <label for="edit_gender">Gender</label>
                    <select id="edit_gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="MALE">Male</option>
                        <option value="FEMALE">Female</option>
                        <option value="OTHER">Other</option>
                    </select>
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
                </div>
                <div class="form-group">
                    <label for="edit_phone">Phone</label>
                    <input type="tel" id="edit_phone" name="phone" required>
                </div>
            </div>

            <div class="form-group">
                <label for="edit_email">Email</label>
                <input type="email" id="edit_email" name="email" required>
            </div>

            <div class="form-group">
                <label for="edit_address">Address</label>
                <textarea id="edit_address" name="address" rows="3"></textarea>
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
function viewPatient(id) {
    const form = document.getElementById('viewPatientForm');
    document.getElementById('view_patient_id').value = id;
    form.action = 'admin_patient_profile_view.php';
    form.submit();
}

function openEditModal(id, firstName, lastName, dob, gender, bloodGroup, phone, email, address) {
    document.getElementById('edit_patient_id').value = id;
    document.getElementById('edit_first_name').value = firstName;
    document.getElementById('edit_last_name').value = lastName;
    document.getElementById('edit_dob').value = dob;
    document.getElementById('edit_gender').value = gender;
    document.getElementById('edit_blood_group').value = bloodGroup;
    document.getElementById('edit_phone').value = phone;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_address').value = address;
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('editModal');
    if (event.target === modal) closeEditModal();
};

function showToast(message, isSuccess = false) {
    const toast = document.getElementById('toast');
    toast.innerHTML = isSuccess ?
        `<i class="bi bi-check-circle"></i> ${message}` :
        `<i class="bi bi-exclamation-circle"></i> ${message}`;
    toast.className = isSuccess ? 'toast success show' : 'toast error show';
    setTimeout(() => {
        toast.className = toast.className.replace('show', '');
    }, 3000);
}
</script>

</body>
</html>