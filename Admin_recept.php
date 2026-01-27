<?php
// Include configuration file
include 'config.php';

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
    
    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($dob) || empty($doj) || empty($gender) || empty($phone) || empty($email)) {
        $error_message = "All fields are required.";
    } else {
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
    }
}

// Handle delete
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $receptionist_id = $_GET['id'];
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
</style>
</head>

<body>

<?php include 'admin_sidebar.php'; ?>

<div class="main">

    <div class="topbar">
        <h1>Manage Receptionists</h1>
        <p>Welcome, Admin</p>
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
        <form method="GET">
            <input type="text" name="name_filter" placeholder="Filter by Name"
                value="<?php echo isset($_GET['name_filter']) ? htmlspecialchars($_GET['name_filter']) : ''; ?>">
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

        if (isset($_GET['name_filter']) && $_GET['name_filter'] !== '') {
            $name = mysqli_real_escape_string($conn, $_GET['name_filter']);
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
                    <label for="edit_doj">Date of Joining</label>
                    <input type="date" id="edit_doj" name="doj" required>
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
            
            <div style="margin-top: 20px;">
                <button type="submit" class="btn-save">Save Changes</button>
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

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
        window.location.href = "Admin_recept.php?action=delete&id=" + id;
    }
}

// Handle form submission to close modal after successful update
document.getElementById('editForm').addEventListener('submit', function() {
    // The form will submit normally and the page will refresh
    // showing the updated data and success message
});
</script>

</body>
</html>