<?php
session_start();

// Check if user is logged in as a receptionist
if (!isset($_SESSION['RECEPTIONIST_ID'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';
 $receptionist_id = $_SESSION['RECEPTIONIST_ID'];

// Fetch receptionist data from database
 $receptionist_query = mysqli_query($conn, "SELECT * FROM receptionist_tbl WHERE RECEPTIONIST_ID = '$receptionist_id'");
 $receptionist = mysqli_fetch_assoc($receptionist_query);

// Handle medicine addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_medicine'])) {
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_medicine'])) {
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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_medicine'])) {
    $medicine_id = mysqli_real_escape_string($conn, $_POST['medicine_id']);
    
    // Check if medicine is used in any prescription
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

// Fetch medicines
 $medicines_query = "SELECT * FROM medicine_tbl";
if (!empty($search)) {
    $medicines_query .= " WHERE MED_NAME LIKE '%$search%' OR DESCRIPTION LIKE '%$search%'";
}
 $medicines_query .= " ORDER BY MED_NAME";
 $medicines_result = mysqli_query($conn, $medicines_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Medicine - QuickCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            --primary-color: #1a3a5f;
            --secondary-color: #3498db;
            --accent-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #17a2b8;
        }
        
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            font-weight: bold;
            background: #F5F8FA;
            display: flex;
        }
        
      
        
        .main-content {
            margin-left: 240px;
            padding: 20px;
            width: calc(100% - 240px);
        }
        
        
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: var(--white);
            border-bottom: 1px solid var(--gray-blue);
            padding: 15px 20px;
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        
        .btn-success {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .btn-success:hover {
            background-color: #27ae60;
            border-color: #27ae60;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            border-color: var(--warning-color);
        }
        
        .btn-warning:hover {
            background-color: #e67e22;
            border-color: #e67e22;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .medicine-card {
            border-left: 4px solid var(--secondary-color);
            margin-bottom: 15px;
            padding: 15px;
            background-color: var(--white);
            border-radius: 0 8px 8px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .medicine-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .medicine-title {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .medicine-id {
            color: #666;
            font-size: 14px;
        }
        
        .medicine-description {
            color: #666;
            margin-bottom: 10px;
        }
        
        .medicine-actions {
            display: flex;
            gap: 10px;
        }
        
        .search-bar {
            display: flex;
            margin-bottom: 20px;
        }
        
        .search-bar input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px 0 0 5px;
            font-size: 16px;
        }
        
        .search-bar button {
            padding: 10px 20px;
            background: var(--secondary-color);
            color: white;
            border: none;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #777;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: none;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar h2, .sidebar a span {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
            
            .medicine-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <?php include 'recept_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        <?php include 'receptionist_header.php'; ?>
        
        <!-- Success/Error Messages -->
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Search Bar -->
        <!-- <div class="search-bar">
            <form method="POST" action="manage_medicine.php" style="display: flex; width: 100%;">
                <input type="text" name="search" placeholder="Search medicines by name or description..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">
                    <i class="bi bi-search"></i> Search
                </button>
            </form>
        </div> -->
        
        <!-- Medicines Card -->
        <div class="card">
            <div class="card-header">
               
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMedicineModal">
                    <i class="bi bi-plus-circle"></i> Add Medicine
                </button>
            </div>
            <div class="card-body">
                <?php
                if (mysqli_num_rows($medicines_result) > 0) {
                    while ($medicine = mysqli_fetch_assoc($medicines_result)) {
                        ?>
                        <div class="medicine-card">
                            <div class="medicine-header">
                                <div class="medicine-title">
                                    <?php echo htmlspecialchars($medicine['MED_NAME']); ?>
                                </div>
                               
                            </div>
                            
                            <div class="medicine-description">
                                <?php echo htmlspecialchars($medicine['DESCRIPTION']); ?>
                            </div>
                            
                            <div class="medicine-actions">
                                <button class="btn btn-warning btn-sm" onclick="editMedicine(<?php echo $medicine['MEDICINE_ID']; ?>, '<?php echo htmlspecialchars($medicine['MED_NAME']); ?>', '<?php echo htmlspecialchars($medicine['DESCRIPTION']); ?>')">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="medicine_id" value="<?php echo $medicine['MEDICINE_ID']; ?>">
                                    <button type="submit" name="delete_medicine" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this medicine?')">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="empty-state">
                        <i class="bi bi-capsule"></i>
                        <h4>No medicines found</h4>
                        <p>' . (!empty($search) ? 'No medicines found matching your search criteria.' : 'No medicines found in the inventory.') . '</p>
                    </div>';
                }
                ?>
            </div>
        </div>
    </div>
    
    <!-- Add Medicine Modal -->
    <div class="modal fade" id="addMedicineModal" tabindex="-1" aria-labelledby="addMedicineModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addMedicineModalLabel">Add New Medicine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="manage_medicine.php">
                        <input type="hidden" name="add_medicine" value="1">
                        
                        <div class="form-group">
                            <label for="med_name">Medicine Name</label>
                            <input type="text" class="form-control" id="med_name" name="med_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check"></i> Add Medicine
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                <i class="bi bi-x"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Medicine Modal -->
    <div class="modal fade" id="editMedicineModal" tabindex="-1" aria-labelledby="editMedicineModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMedicineModalLabel">Edit Medicine</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="manage_medicine.php">
                        <input type="hidden" id="edit_medicine_id" name="medicine_id">
                        <input type="hidden" name="update_medicine" value="1">
                        
                        <div class="form-group">
                            <label for="edit_med_name">Medicine Name</label>
                            <input type="text" class="form-control" id="edit_med_name" name="med_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="edit_description">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="4" required></textarea>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check"></i> Update Medicine
                            </button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                                <i class="bi bi-x"></i> Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to edit medicine
        function editMedicine(medicineId, medName, description) {
            document.getElementById('edit_medicine_id').value = medicineId;
            document.getElementById('edit_med_name').value = medName;
            document.getElementById('edit_description').value = description;
            
            // Show the modal
            const editModal = new bootstrap.Modal(document.getElementById('editMedicineModal'));
            editModal.show();
        }
    </script>
</body>
</html>