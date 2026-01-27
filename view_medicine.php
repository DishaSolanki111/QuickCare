<?php
// ================== SESSION & ACCESS CONTROL ==================
session_start();

if (
    !isset($_SESSION['LOGGED_IN']) ||
    $_SESSION['LOGGED_IN'] !== true ||
    !isset($_SESSION['USER_TYPE']) ||
    $_SESSION['USER_TYPE'] !== 'doctor'
) {
    header("Location: login.php");
    exit();
}

// ================== DATABASE CONNECTION ==================
include 'config.php';

// ================== DOCTOR INFO ==================
 $doctor_id = $_SESSION['DOCTOR_ID'];
 $doctor_name = "Doctor";

 $doc_sql = "SELECT FIRST_NAME, LAST_NAME FROM doctor_tbl WHERE DOCTOR_ID = ?";
 $doc_stmt = $conn->prepare($doc_sql);
 $doc_stmt->bind_param("i", $doctor_id);
 $doc_stmt->execute();
 $doc_result = $doc_stmt->get_result();

if ($doc_result->num_rows === 1) {
    $doc = $doc_result->fetch_assoc();
    $doctor_name = htmlspecialchars($doc['FIRST_NAME'] . ' ' . $doc['LAST_NAME']);
}
 $doc_stmt->close();

// ================== HANDLE MEDICINE ADDITION ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_medicine'])) {
    $med_name = mysqli_real_escape_string($conn, $_POST['med_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Get a receptionist ID (using ID 1 as default)
    $receptionist_id = 1;
    
    $add_sql = "INSERT INTO medicine_tbl (RECEPTIONIST_ID, MED_NAME, DESCRIPTION) 
                VALUES (?, ?, ?)";
    $add_stmt = $conn->prepare($add_sql);
    $add_stmt->bind_param("iss", $receptionist_id, $med_name, $description);
    
    if ($add_stmt->execute()) {
        $success_message = "Medicine added successfully!";
    } else {
        $error_message = "Error adding medicine: " . $conn->error;
    }
    $add_stmt->close();
}

// ================== HANDLE MEDICINE UPDATE ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_medicine'])) {
    $medicine_id = $_POST['medicine_id'];
    $med_name = mysqli_real_escape_string($conn, $_POST['med_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $update_sql = "UPDATE medicine_tbl SET 
                   MED_NAME = ?, 
                   DESCRIPTION = ? 
                   WHERE MEDICINE_ID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $med_name, $description, $medicine_id);
    
    if ($update_stmt->execute()) {
        $success_message = "Medicine updated successfully!";
    } else {
        $error_message = "Error updating medicine: " . $conn->error;
    }
    $update_stmt->close();
}

// ================== HANDLE MEDICINE DELETION ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_medicine'])) {
    $medicine_id = $_POST['medicine_id'];
    
    // Check if medicine is used in any prescription
    $check_sql = "SELECT COUNT(*) as count FROM prescription_medicine_tbl WHERE MEDICINE_ID = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $medicine_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $count = $check_result->fetch_assoc()['count'];
    $check_stmt->close();
    
    if ($count > 0) {
        $error_message = "Cannot delete medicine as it is used in prescriptions!";
    } else {
        $delete_sql = "DELETE FROM medicine_tbl WHERE MEDICINE_ID = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $medicine_id);
        
        if ($delete_stmt->execute()) {
            $success_message = "Medicine deleted successfully!";
        } else {
            $error_message = "Error deleting medicine: " . $conn->error;
        }
        $delete_stmt->close();
    }
}

// ================== SEARCH FUNCTIONALITY ==================
 $search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// ================== FETCH MEDICINES ==================
 $medicines_query = "SELECT * FROM medicine_tbl";
if (!empty($search)) {
    $medicines_query .= " WHERE MED_NAME LIKE '%$search%' OR DESCRIPTION LIKE '%$search%'";
}
 $medicines_query .= " ORDER BY MED_NAME";
 $medicines_result = $conn->query($medicines_query);

 $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Medicine - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0066cc;
            --primary-dark: #0052a3;
            --primary-light: #e6f2ff;
            --secondary: #00a8cc;
            --accent: #00a86b;
            --warning: #ff6b6b;
            --dark: #1a3a5f;
            --light: #f8fafc;
            --white: #ffffff;
            --text: #2c5282;
            --text-light: #4a6fa5;
            --gradient-1: linear-gradient(135deg, #0066cc 0%, #00a8cc 100%);
            --gradient-2: linear-gradient(135deg, #00a8cc 0%, #00a86b 100%);
            --gradient-3: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.06);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --shadow-xl: 0 20px 25px rgba(0,0,0,0.1);
            --shadow-2xl: 0 25px 50px rgba(0,0,0,0.25);
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }

        body {
            background-color: #f5f8fa;
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
       
        /* Main Content */
        .main-content {
           
            margin-left: 250px;
            min-height: 100vh;
        }


    

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
            border: 2px solid var(--primary-light);
        }

        .user-details h3 {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .user-details p {
            font-size: 13px;
            color: var(--text-light);
            margin: 0;
        }

        /* Medicine Content */
        .medicine-content {
            padding: 30px;
        }

        .medicine-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .medicine-header h2 {
            font-size: 28px;
            color: var(--dark);
        }

        .search-bar {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .search-bar input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px 0 0 6px;
            font-size: 16px;
        }

        .search-bar button {
            padding: 12px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 0 6px 6px 0;
            cursor: pointer;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-success {
            background-color: var(--accent);
            color: white;
        }

        .btn-success:hover {
            background-color: #27ae60;
        }

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-warning {
            background-color: var(--warning-color);
            color: white;
        }

        .btn-warning:hover {
            background-color: #e67e22;
        }

        .medicine-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }

        .medicine-card {
            background: var(--white);
            border-radius: 12px;
            padding: 25px;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
        }

        .medicine-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .medicine-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .medicine-card-header h3 {
            font-size: 20px;
            color: var(--dark);
            margin: 0;
        }

        .medicine-id {
            color: var(--text-light);
            font-size: 14px;
        }

        .medicine-description {
            color: #666;
            margin-bottom: 20px;
        }

        .medicine-actions {
            display: flex;
            gap: 10px;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #777;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow-md);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
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

        /* Modal Styles */
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
            margin: 10% auto;
            padding: 20px;
            border: none;
            width: 80%;
            max-width: 600px;
            border-radius: 12px;
            box-shadow: var(--shadow-xl);
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark);
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 2px rgba(0, 102, 204, 0.2);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
     
            .main-content {
                margin-left: 70px;
            }
            
            .medicine-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            }
        }

        @media (max-width: 768px) {
          
            
            .main-content {
                margin-left: 0;
            }
            
          
            .medicine-content {
                padding: 20px;
            }
            
            .medicine-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .search-bar {
                width: 100%;
            }
            
            .medicine-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--dark);
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include 'doctor_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
        
        <?php include 'doctor_header.php'; ?>
        <!-- Medicine Content -->
        <div class="medicine-content">
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
            
            <!-- Medicine Header -->
            <div class="medicine-header">
                <h2>Medicine Inventory</h2>
                <!-- <button class="btn btn-primary" onclick="openAddModal()">
                    <i class="fas fa-plus"></i> Add Medicine
                </button> -->
            </div>
            
            <!-- Search Bar -->
            <div class="search-bar">
                <form method="GET" action="view_medicine.php" style="display: flex; width: 100%;">
                    <input type="text" name="search" placeholder="Search medicines by name or description..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit">
                        <i class="fas fa-search"></i> Search
                    </button>
                </form>
            </div>
            
            <!-- Medicine Grid -->
            <?php if ($medicines_result->num_rows > 0): ?>
                <div class="medicine-grid">
                    <?php while ($medicine = $medicines_result->fetch_assoc()): ?>
                        <div class="medicine-card">
                            <div class="medicine-card-header">
                                <h3><?php echo htmlspecialchars($medicine['MED_NAME']); ?></h3>
                                <!-- <span class="medicine-id">ID: #<?php echo $medicine['MEDICINE_ID']; ?></span> -->
                            </div>
                            
                            <div class="medicine-description">
                                <?php echo htmlspecialchars($medicine['DESCRIPTION']); ?>
                            </div>
                            
                            <!-- <div class="medicine-actions">
                                <button class="btn btn-warning" onclick="openEditModal(<?php echo $medicine['MEDICINE_ID']; ?>, '<?php echo htmlspecialchars($medicine['MED_NAME']); ?>', '<?php echo htmlspecialchars($medicine['DESCRIPTION']); ?>')">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="medicine_id" value="<?php echo $medicine['MEDICINE_ID']; ?>">
                                    <button type="submit" name="delete_medicine" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this medicine?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </div> -->
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-pills"></i>
                    <h3>No Medicines Found</h3>
                    <p><?php if (!empty($search)): ?>No medicines found matching your search criteria.<?php else: ?>No medicines found in the inventory.<?php endif; ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Add Medicine Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeAddModal()">&times;</span>
            <h2>Add New Medicine</h2>
            <form method="POST" action="view_medicine.php">
                <input type="hidden" name="add_medicine" value="1">
                
                <div class="form-group">
                    <label for="med_name">Medicine Name</label>
                    <input type="text" class="form-control" id="med_name" name="med_name" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Add Medicine
                </button>
            </form>
        </div>
    </div>
    
    <!-- Edit Medicine Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Medicine</h2>
            <form method="POST" action="view_medicine.php">
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
                
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Update Medicine
                </button>
            </form>
        </div>
    </div>

    <script>
        // Modal functions
        function openAddModal() {
            document.getElementById('addModal').style.display = 'block';
        }
        
        function closeAddModal() {
            document.getElementById('addModal').style.display = 'none';
        }
        
        function openEditModal(medicineId, medName, description) {
            document.getElementById('edit_medicine_id').value = medicineId;
            document.getElementById('edit_med_name').value = medName;
            document.getElementById('edit_description').value = description;
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('active');
            });
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                        sidebar.classList.remove('active');
                    }
                }
            });
            
            // Close modals when clicking outside
            window.onclick = function(event) {
                const addModal = document.getElementById('addModal');
                const editModal = document.getElementById('editModal');
                
                if (event.target == addModal) {
                    addModal.style.display = 'none';
                }
                if (event.target == editModal) {
                    editModal.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>