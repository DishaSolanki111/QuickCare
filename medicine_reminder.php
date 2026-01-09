<?php
session_start();

// Check if user is logged in as a patient
if (!isset($_SESSION['PATIENT_ID'])) {
    header("Location: login_for_all.php");
    exit;
}

include 'config.php';
 $patient_id = $_SESSION['PATIENT_ID'];

// Fetch patient data from database
 $patient_query = mysqli_query($conn, "SELECT * FROM patient_tbl WHERE PATIENT_ID = '$patient_id'");
 $patient = mysqli_fetch_assoc($patient_query);

// Handle form submission for adding new reminder
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_reminder'])) {
    $medicine_id = mysqli_real_escape_string($conn, $_POST['medicine_id']);
    $reminder_time = mysqli_real_escape_string($conn, $_POST['reminder_time']);
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
    
    $add_query = "INSERT INTO medicine_reminder_tbl (MEDICINE_ID, CREATOR_ROLE, CREATOR_ID, PATIENT_ID, REMINDER_TIME, REMARKS) 
                  VALUES ('$medicine_id', 'PATIENT', '$patient_id', '$patient_id', '$reminder_time', '$remarks')";
    
    if (mysqli_query($conn, $add_query)) {
        $success_message = "Medicine reminder added successfully!";
    } else {
        $error_message = "Error adding reminder: " . mysqli_error($conn);
    }
}

// Handle form submission for updating reminder
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_reminder'])) {
    $reminder_id = mysqli_real_escape_string($conn, $_POST['reminder_id']);
    $reminder_time = mysqli_real_escape_string($conn, $_POST['reminder_time']);
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
    
    $update_query = "UPDATE medicine_reminder_tbl SET 
                    REMINDER_TIME = '$reminder_time',
                    REMARKS = '$remarks'
                    WHERE MEDICINE_REMINDER_ID = '$reminder_id' AND PATIENT_ID = '$patient_id'";
    
    if (mysqli_query($conn, $update_query)) {
        $success_message = "Medicine reminder updated successfully!";
    } else {
        $error_message = "Error updating reminder: " . mysqli_error($conn);
    }
}

// Handle reminder deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_reminder'])) {
    $reminder_id = mysqli_real_escape_string($conn, $_POST['reminder_id']);
    
    $delete_query = "DELETE FROM medicine_reminder_tbl WHERE MEDICINE_REMINDER_ID = '$reminder_id' AND PATIENT_ID = '$patient_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        $success_message = "Medicine reminder deleted successfully!";
    } else {
        $error_message = "Error deleting reminder: " . mysqli_error($conn);
    }
}

// Get prescription ID from URL if coming from prescriptions page
 $prescription_id = isset($_GET['prescription']) ? mysqli_real_escape_string($conn, $_GET['prescription']) : '';

// Fetch medicines for dropdown
 $medicines_query = mysqli_query($conn, "SELECT * FROM medicine_tbl ORDER BY MED_NAME");

// Fetch medicine reminders
 $reminders_query = mysqli_query($conn, "
    SELECT mr.*, m.MED_NAME
    FROM medicine_reminder_tbl mr
    JOIN medicine_tbl m ON mr.MEDICINE_ID = m.MEDICINE_ID
    WHERE mr.PATIENT_ID = '$patient_id'
    ORDER BY mr.REMINDER_TIME
");

// If prescription ID is provided, get medicines from that prescription
 $prescription_medicines = [];
if (!empty($prescription_id)) {
    $prescription_medicines_query = mysqli_query($conn, "
        SELECT pm.MEDICINE_ID, m.MED_NAME
        FROM prescription_medicine_tbl pm
        JOIN medicine_tbl m ON pm.MEDICINE_ID = m.MEDICINE_ID
        WHERE pm.PRESCRIPTION_ID = '$prescription_id'
    ");
    
    while ($medicine = mysqli_fetch_assoc($prescription_medicines_query)) {
        $prescription_medicines[] = $medicine;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicine Reminder - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a3a5f;
            --secondary-color: #3498db;
            --accent-color: #2ecc71;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #17a2b8;
            --dark-blue: #072D44;
            --mid-blue: #064469;
            --soft-blue: #5790AB;
            --light-blue: #9CCDD8;
            --gray-blue: #D0D7E1;
            --white: #ffffff;
            --card-bg: #F6F9FB;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }
        
        .welcome-msg {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .user-actions {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .reminder-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .reminder-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--danger-color);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 20px;
        }
        
        .reminder-content {
            flex: 1;
        }
        
        .reminder-content h4 {
            margin-bottom: 5px;
            color: var(--primary-color);
        }
        
        .reminder-time {
            color: #666;
            font-size: 14px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-success {
            background-color: var(--accent-color);
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
        
        .btn-group {
            display: flex;
            gap: 10px;
        }
        
        .add-reminder-section {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
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
            margin: 10% auto;
            padding: 20px;
            border: none;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
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
        
        .prescription-medicines {
            margin-bottom: 20px;
        }
        
        .medicine-chip {
            display: inline-block;
            padding: 5px 10px;
            margin: 5px;
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--secondary-color);
            border-radius: 20px;
            font-size: 14px;
            cursor: pointer;
        }
        
        .medicine-chip:hover {
            background-color: rgba(52, 152, 219, 0.2);
        }
        
        @media (max-width: 992px) {
            .main-content {
                margin-left: 200px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .reminder-card {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .reminder-icon {
                margin-bottom: 15px;
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Import Sidebar -->
        <?php include 'patient_sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="welcome-msg">Medicine Reminders</div>
                <div class="user-actions">
                    <div class="user-dropdown">
                        <div class="user-avatar"><?php echo strtoupper(substr($patient['FIRST_NAME'], 0, 1) . substr($patient['LAST_NAME'], 0, 1)); ?></div>
                        <span><?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></span>
                        <i class="fas fa-chevron-down" style="margin-left: 8px;"></i>
                    </div>
                </div>
            </div>
            
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
            
            <!-- Add Reminder Section -->
            <div class="add-reminder-section">
                <h3 style="margin-bottom: 20px;">Add New Medicine Reminder</h3>
                
                <?php if (!empty($prescription_medicines)): ?>
                    <div class="prescription-medicines">
                        <p><strong>Medicines from your prescription:</strong></p>
                        <div>
                            <?php foreach ($prescription_medicines as $medicine): ?>
                                <span class="medicine-chip" onclick="selectMedicine(<?php echo $medicine['MEDICINE_ID']; ?>, '<?php echo htmlspecialchars($medicine['MED_NAME']); ?>')">
                                    <?php echo htmlspecialchars($medicine['MED_NAME']); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="medicine_reminder.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="medicine_id">Medicine</label>
                            <select class="form-control" id="medicine_id" name="medicine_id" required>
                                <option value="">-- Select Medicine --</option>
                                <?php
                                if (mysqli_num_rows($medicines_query) > 0) {
                                    while ($medicine = mysqli_fetch_assoc($medicines_query)) {
                                        echo '<option value="' . $medicine['MEDICINE_ID'] . '">' . 
                                             htmlspecialchars($medicine['MED_NAME']) . '</option>';
                                    }
                                    // Reset the result pointer
                                    mysqli_data_seek($medicines_query, 0);
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="reminder_time">Reminder Time</label>
                            <input type="time" class="form-control" id="reminder_time" name="reminder_time" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="remarks">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Add any notes or dosage instructions"></textarea>
                    </div>
                    
                    <button type="submit" name="add_reminder" class="btn btn-success">
                        <i class="fas fa-plus"></i> Add Reminder
                    </button>
                </form>
            </div>
            
            <!-- Existing Reminders -->
            <h3 style="margin-bottom: 20px;">Your Medicine Reminders</h3>
            
            <?php
            if (mysqli_num_rows($reminders_query) > 0) {
                while ($reminder = mysqli_fetch_assoc($reminders_query)) {
                    ?>
                    <div class="reminder-card">
                        <div class="reminder-icon">
                            <i class="fas fa-pills"></i>
                        </div>
                        <div class="reminder-content">
                            <h4><?php echo htmlspecialchars($reminder['MED_NAME']); ?></h4>
                            <p><?php echo htmlspecialchars($reminder['REMARKS']); ?></p>
                            <div class="reminder-time">
                                <i class="far fa-clock"></i> Daily at <?php echo date('h:i A', strtotime($reminder['REMINDER_TIME'])); ?>
                            </div>
                        </div>
                        <div class="btn-group">
                            <button class="btn btn-primary" onclick="openEditModal(<?php echo $reminder['MEDICINE_REMINDER_ID']; ?>, '<?php echo htmlspecialchars($reminder['MED_NAME']); ?>', '<?php echo $reminder['REMINDER_TIME']; ?>', '<?php echo htmlspecialchars($reminder['REMARKS']); ?>')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="reminder_id" value="<?php echo $reminder['MEDICINE_REMINDER_ID']; ?>">
                                <button type="submit" name="delete_reminder" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this reminder?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="empty-state">
                    <i class="fas fa-bell-slash"></i>
                    <p>No medicine reminders set</p>
                </div>';
            }
            ?>
        </div>
    </div>
    
    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Medicine Reminder</h2>
            <form method="POST" action="medicine_reminder.php">
                <input type="hidden" id="edit_reminder_id" name="reminder_id">
                
                <div class="form-group">
                    <label for="edit_medicine_name">Medicine</label>
                    <input type="text" class="form-control" id="edit_medicine_name" readonly>
                </div>
                
                <div class="form-group">
                    <label for="edit_reminder_time">Reminder Time</label>
                    <input type="time" class="form-control" id="edit_reminder_time" name="reminder_time" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_remarks">Remarks</label>
                    <textarea class="form-control" id="edit_remarks" name="remarks" rows="3"></textarea>
                </div>
                
                <div class="btn-group">
                    <button type="submit" name="update_reminder" class="btn btn-success">
                        <i class="fas fa-save"></i> Update Reminder
                    </button>
                    <button type="button" class="btn btn-danger" onclick="closeEditModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function selectMedicine(medicineId, medicineName) {
            document.getElementById('medicine_id').value = medicineId;
            // Focus on the next field
            document.getElementById('reminder_time').focus();
        }
        
        function openEditModal(reminderId, medicineName, reminderTime, remarks) {
            document.getElementById('edit_reminder_id').value = reminderId;
            document.getElementById('edit_medicine_name').value = medicineName;
            document.getElementById('edit_reminder_time').value = reminderTime;
            document.getElementById('edit_remarks').value = remarks;
            document.getElementById('editModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('editModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>