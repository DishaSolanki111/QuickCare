<?php
session_start();

// Check if user is logged in as a receptionist
if (!isset($_SESSION['RECEPTIONIST_ID'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';
$receptionist_id = $_SESSION['RECEPTIONIST_ID'];

// Initialize messages
$success_message = "";
$error_message = "";

// Handle medicine addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_medicine') {
    $med_name = mysqli_real_escape_string($conn, $_POST['med_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $add_query = "INSERT INTO medicine_tbl (RECEPTIONIST_ID, MED_NAME, DESCRIPTION) 
                 VALUES ('$receptionist_id', '$med_name', '$description')";
    
    if (mysqli_query($conn, $add_query)) {
        $success_message = "Medicine added successfully!";
    } else {
        $error_message = "Error adding medicine: " . mysqli_error($conn);
    }
}

// Handle medicine update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'edit_medicine') {
    $medicine_id = mysqli_real_escape_string($conn, $_POST['medicine_id']);
    $med_name = mysqli_real_escape_string($conn, $_POST['med_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $update_query = "UPDATE medicine_tbl SET 
                    MED_NAME = '$med_name',
                    DESCRIPTION = '$description'
                    WHERE MEDICINE_ID = '$medicine_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $success_message = "Medicine updated successfully!";
    } else {
        $error_message = "Error updating medicine: " . mysqli_error($conn);
    }
}

// Handle medicine deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $medicine_id = mysqli_real_escape_string($conn, $_POST['id']);
    
    $check_query = "SELECT COUNT(*) as count FROM prescription_medicine_tbl WHERE MEDICINE_ID = '$medicine_id'";
    $check_result = mysqli_query($conn, $check_query);
    $count = mysqli_fetch_assoc($check_result)['count'];
    
    if ($count > 0) {
        $error_message = "Cannot delete medicine as it is used in prescriptions!";
    } else {
        $delete_query = "DELETE FROM medicine_tbl WHERE MEDICINE_ID = '$medicine_id'";
        if (mysqli_query($conn, $delete_query)) {
            $success_message = "Medicine deleted successfully!";
        } else {
            $error_message = "Error deleting medicine: " . mysqli_error($conn);
        }
    }
}

// Search functionality
$search = isset($_POST['search']) ? mysqli_real_escape_string($conn, $_POST['search']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Medicine - QuickCare</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .filter-container form {
        display: flex;
        gap: 15px;
    }

    .filter-container input {
        padding: 10px;
        border: 1px solid #D0D7E1;
        border-radius: 5px;
        min-width: 300px;
    }

    .btn-action-primary {
        padding: 10px 15px;
        background: #5790AB;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    .action-btn {
        padding: 5px 10px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        color: white;
        margin-right: 5px;
        text-decoration: none;
        font-size: 13px;
    }

    .edit-btn { background: #f39c12; }
    .delete-btn { background: #e74c3c; }

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
        max-width: 500px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
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
    
    .form-group input, .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #D0D7E1;
        border-radius: 5px;
        box-sizing: border-box;
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

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
    }
    
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>
</head>
<body>

<?php include 'recept_sidebar.php'; ?>

<div class="main">
    <?php include 'receptionist_header.php'; ?>

    <?php if (!empty($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="filter-container">
        <form method="POST" action="">
            <input type="text" name="search" placeholder="Search by name or description..." 
                value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn-action-primary">Search</button>
        </form>
        <button class="btn-action-primary" onclick="openAddModal()">+ Add Medicine</button>
    </div>

    <table>
        <tr>
            <th width="30%">Medicine Name</th>
            <th width="40%">Description</th>
            <th width="20%">Actions</th>
        </tr>

        <?php
        $query = "SELECT * FROM medicine_tbl";
        if (!empty($search)) {
            $query .= " WHERE MED_NAME LIKE '%$search%' OR DESCRIPTION LIKE '%$search%'";
        }
        $query .= " ORDER BY MED_NAME";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>
                   
                    <td><strong>{$row['MED_NAME']}</strong></td>
                    <td>{$row['DESCRIPTION']}</td>
                    <td>
                        <button class='action-btn edit-btn' 
                            onclick=\"openEditModal({$row['MEDICINE_ID']}, '" . addslashes($row['MED_NAME']) . "', '" . addslashes($row['DESCRIPTION']) . "')\">Edit</button>
                        <button class='action-btn delete-btn' 
                            onclick=\"deleteMedicine({$row['MEDICINE_ID']})\">Delete</button>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='4' style='text-align:center;'>No medicines found</td></tr>";
        }
        ?>
    </table>
</div>

<div id="medicineModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2 id="modalTitle">Add Medicine</h2>
        <form id="medicineForm" method="post" action="manage_medicine.php">
            <input type="hidden" name="action" id="formAction" value="add_medicine">
            <input type="hidden" name="medicine_id" id="medicine_id">
            
            <div class="form-group">
                <label for="med_name">Medicine Name</label>
                <input type="text" id="med_name" name="med_name" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" required></textarea>
            </div>
            
            <div style="margin-top: 20px;">
                <button type="submit" class="btn-save" id="submitBtn">Add Medicine</button>
                <button type="button" class="btn-action-primary" style="background:#6c757d;" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById('medicineModal');

function openAddModal() {
    document.getElementById('modalTitle').textContent = "Add Medicine";
    document.getElementById('formAction').value = "add_medicine";
    document.getElementById('submitBtn').textContent = "Add Medicine";
    document.getElementById('medicineForm').reset();
    modal.style.display = 'block';
}

function openEditModal(id, name, desc) {
    document.getElementById('modalTitle').textContent = "Edit Medicine";
    document.getElementById('formAction').value = "edit_medicine";
    document.getElementById('submitBtn').textContent = "Save Changes";
    document.getElementById('medicine_id').value = id;
    document.getElementById('med_name').value = name;
    document.getElementById('description').value = desc;
    modal.style.display = 'block';
}

function closeModal() {
    modal.style.display = 'none';
}

function deleteMedicine(id) {
    if (confirm("Are you sure you want to delete this medicine?")) {
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = 'manage_medicine.php';
        
        const a = document.createElement('input');
        a.type = 'hidden'; a.name = 'action'; a.value = 'delete';
        
        const i = document.createElement('input');
        i.type = 'hidden'; i.name = 'id'; i.value = id;
        
        f.appendChild(a);
        f.appendChild(i);
        document.body.appendChild(f);
        f.submit();
    }
}

window.onclick = function(event) {
    if (event.target == modal) closeModal();
}
</script>

</body>
</html>