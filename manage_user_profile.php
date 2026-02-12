<?php
session_start();

// Check if user is logged in as a receptionist
if (!isset($_SESSION['RECEPTIONIST_ID'])) {
    header("Location: login.php");
    exit;
}

include 'config.php';
include 'recept_sidebar.php';
 $receptionist_id = $_SESSION['RECEPTIONIST_ID'];

// Fetch receptionist data from database
 $receptionist_query = mysqli_query($conn, "SELECT * FROM receptionist_tbl WHERE RECEPTIONIST_ID = '$receptionist_id'");
 $receptionist = mysqli_fetch_assoc($receptionist_query);

// Handle user type selection
 $user_type = isset($_POST['user_type']) ? $_POST['user_type'] : 'patient';

// Handle user creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);
    
    if ($user_type === 'patient') {
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $blood_group = mysqli_real_escape_string($conn, $_POST['blood_group']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        
        $create_query = "INSERT INTO patient_tbl (FIRST_NAME, LAST_NAME, USERNAME, PSWD, DOB, GENDER, BLOOD_GROUP, PHONE, EMAIL, ADDRESS) 
                         VALUES ('$first_name', '$last_name', '$username', '$password', '$dob', '$gender', '$blood_group', '$phone', '$email', '$address')";
        
        if (mysqli_query($conn, $create_query)) {
            $success_message = "Patient account created successfully!";
        } else {
            $error_message = "Error creating patient account: " . mysqli_error($conn);
        }
    } elseif ($user_type === 'doctor') {
        $specialisation_id = mysqli_real_escape_string($conn, $_POST['specialisation_id']);
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);
        $doj = mysqli_real_escape_string($conn, $_POST['doj']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        
        $create_query = "INSERT INTO doctor_tbl (SPECIALISATION_ID, FIRST_NAME, LAST_NAME, USERNAME, PSWD, DOB, DOJ, GENDER, PHONE, EMAIL) 
                         VALUES ('$specialisation_id', '$first_name', '$last_name', '$username', '$password', '$dob', '$doj', '$gender', '$phone', '$email')";
        
        if (mysqli_query($conn, $create_query)) {
            $success_message = "Doctor account created successfully!";
        } else {
            $error_message = "Error creating doctor account: " . mysqli_error($conn);
        }
    } elseif ($user_type === 'receptionist') {
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);
        $doj = mysqli_real_escape_string($conn, $_POST['doj']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        
        $create_query = "INSERT INTO receptionist_tbl (FIRST_NAME, LAST_NAME, DOB, DOJ, GENDER, PHONE, EMAIL, ADDRESS, USERNAME, PSWD) 
                         VALUES ('$first_name', '$last_name', '$dob', '$doj', '$gender', '$phone', '$email', '$address', '$username', '$password')";
        
        if (mysqli_query($conn, $create_query)) {
            $success_message = "Receptionist account created successfully!";
        } else {
            $error_message = "Error creating receptionist account: " . mysqli_error($conn);
        }
    }
}

// Handle user update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    
    if ($user_type === 'patient') {
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $blood_group = mysqli_real_escape_string($conn, $_POST['blood_group']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        
        $update_query = "UPDATE patient_tbl SET 
                        FIRST_NAME = '$first_name',
                        LAST_NAME = '$last_name',
                        DOB = '$dob',
                        GENDER = '$gender',
                        BLOOD_GROUP = '$blood_group',
                        PHONE = '$phone',
                        EMAIL = '$email',
                        ADDRESS = '$address'
                        WHERE PATIENT_ID = '$user_id'";
        
        if (mysqli_query($conn, $update_query)) {
            $success_message = "Patient profile updated successfully!";
        } else {
            $error_message = "Error updating patient profile: " . mysqli_error($conn);
        }
    } elseif ($user_type === 'doctor') {
        $specialisation_id = mysqli_real_escape_string($conn, $_POST['specialisation_id']);
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        
        $update_query = "UPDATE doctor_tbl SET 
                        SPECIALISATION_ID = '$specialisation_id',
                        FIRST_NAME = '$first_name',
                        LAST_NAME = '$last_name',
                        DOB = '$dob',
                        GENDER = '$gender',
                        PHONE = '$phone',
                        EMAIL = '$email'
                        WHERE DOCTOR_ID = '$user_id'";
        
        if (mysqli_query($conn, $update_query)) {
            $success_message = "Doctor profile updated successfully!";
        } else {
            $error_message = "Error updating doctor profile: " . mysqli_error($conn);
        }
    } elseif ($user_type === 'receptionist') {
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
        $dob = mysqli_real_escape_string($conn, $_POST['dob']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $phone = mysqli_real_escape_string($conn, $_POST['phone']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        
        $update_query = "UPDATE receptionist_tbl SET 
                        FIRST_NAME = '$first_name',
                        LAST_NAME = '$last_name',
                        DOB = '$dob',
                        GENDER = '$gender',
                        PHONE = '$phone',
                        EMAIL = '$email',
                        ADDRESS = '$address'
                        WHERE RECEPTIONIST_ID = '$user_id'";
        
        if (mysqli_query($conn, $update_query)) {
            $success_message = "Receptionist profile updated successfully!";
        } else {
            $error_message = "Error updating receptionist profile: " . mysqli_error($conn);
        }
    }
}

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_type = mysqli_real_escape_string($conn, $_POST['user_type']);
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    
    if ($user_type === 'patient') {
        // Check if patient has any appointments
        $check_query = "SELECT COUNT(*) as count FROM appointment_tbl WHERE PATIENT_ID = '$user_id'";
        $check_result = mysqli_query($conn, $check_query);
        $count = mysqli_fetch_assoc($check_result)['count'];
        
        if ($count > 0) {
            $error_message = "Cannot delete patient as they have appointments scheduled!";
        } else {
            $delete_query = "DELETE FROM patient_tbl WHERE PATIENT_ID = '$user_id'";
            
            if (mysqli_query($conn, $delete_query)) {
                $success_message = "Patient deleted successfully!";
            } else {
                $error_message = "Error deleting patient: " . mysqli_error($conn);
            }
        }
    } elseif ($user_type === 'doctor') {
        // Check if doctor has any appointments
        $check_query = "SELECT COUNT(*) as count FROM appointment_tbl WHERE DOCTOR_ID = '$user_id'";
        $check_result = mysqli_query($conn, $check_query);
        $count = mysqli_fetch_assoc($check_result)['count'];
        
        if ($count > 0) {
            $error_message = "Cannot delete doctor as they have appointments scheduled!";
        } else {
            $delete_query = "DELETE FROM doctor_tbl WHERE DOCTOR_ID = '$user_id'";
            
            if (mysqli_query($conn, $delete_query)) {
                $success_message = "Doctor deleted successfully!";
            } else {
                $error_message = "Error deleting doctor: " . mysqli_error($conn);
            }
        }
    } elseif ($user_type === 'receptionist') {
        $delete_query = "DELETE FROM receptionist_tbl WHERE RECEPTIONIST_ID = '$user_id'";
        
        if (mysqli_query($conn, $delete_query)) {
            $success_message = "Receptionist deleted successfully!";
        } else {
            $error_message = "Error deleting receptionist: " . mysqli_error($conn);
        }
    }
}

// Fetch users based on selected type
if ($user_type === 'patient') {
    $users_query = "SELECT * FROM patient_tbl ORDER BY FIRST_NAME, LAST_NAME";
} elseif ($user_type === 'doctor') {
    $users_query = "
        SELECT d.*, s.SPECIALISATION_NAME 
        FROM doctor_tbl d
        JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
        ORDER BY s.SPECIALISATION_NAME, d.FIRST_NAME
    ";
} elseif ($user_type === 'receptionist') {
    $users_query = "SELECT * FROM receptionist_tbl ORDER BY FIRST_NAME, LAST_NAME";
}

 $users_result = mysqli_query($conn, $users_query);

// Fetch specializations for dropdown
 $specialisations_query = mysqli_query($conn, "SELECT * FROM specialisation_tbl ORDER BY SPECIALISATION_NAME");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage User Profile - QuickCare</title>
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
            
       
        }
        
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            font-weight: normal;
            background: #F5F8FA;
            display: flex;
        }
        
       
        
        .main-content {
            margin-left: 240px;
            padding: 20px;
            width: calc(100% - 240px);
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

        .topbar h1 {
            margin: 0;
            color: #064469;
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
        
        .user-card {
            border-left: 4px solid var(--secondary-color);
            margin-bottom: 15px;
            padding: 15px;
            background-color: var(--white);
            border-radius: 0 8px 8px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .user-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .user-title {
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .user-type {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background-color: rgba(52, 152, 219, 0.1);
            color: var(--secondary-color);
        }
        
        .user-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .user-detail {
            display: flex;
            align-items: center;
            color: #666;
        }
        
        .user-detail i {
            margin-right: 8px;
            color: var(--secondary-color);
        }
        
        .user-actions {
            display: flex;
            gap: 10px;
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
        
        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
        .nav-tabs {
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .nav-tabs .nav-link {
            color: var(--primary-color);
            border: none;
            border-bottom: 3px solid transparent;
            border-radius: 0;
            padding: 10px 15px;
            margin-right: 5px;
        }
        
        .nav-tabs .nav-link.active {
            color: var(--secondary-color);
            border-bottom-color: var(--secondary-color);
            background: none;
        }
        
        .nav-tabs .nav-link:hover {
            border-bottom-color: #eee;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @media (max-width: 768px) {
           
            
            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .user-details {
                grid-template-columns: 1fr;
            }
            
            .user-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
   
   

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <h1>Manage User Profile</h1>
            <p>Welcome, <?php echo htmlspecialchars($receptionist['FIRST_NAME'] . ' ' . $receptionist['LAST_NAME']); ?></p>
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
        
        <!-- User Type Selection -->
        <div class="card">
            
            <div class="card-body">
                <!-- Tabs -->
                <form id="navPatientForm" method="POST" action="manage_user_profile.php" style="display:none"><input type="hidden" name="user_type" value="patient"></form>
                <form id="navDoctorForm" method="POST" action="manage_user_profile.php" style="display:none"><input type="hidden" name="user_type" value="doctor"></form>
                
                <ul class="nav nav-tabs" id="userTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link <?php echo $user_type === 'patient' ? 'active' : ''; ?>" onclick="document.getElementById('navPatientForm').submit()">Patients</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button type="button" class="nav-link <?php echo $user_type === 'doctor' ? 'active' : ''; ?>" onclick="document.getElementById('navDoctorForm').submit()">Doctors</button>
                    </li>
                    
                </ul>
                
                <!-- Tab Content -->
                <div class="tab-content <?php echo $user_type === 'patient' ? 'active' : ''; ?>" id="patient">
                    <?php
                    if (mysqli_num_rows($users_result) > 0) {
                        while ($user = mysqli_fetch_assoc($users_result)) {
                            ?>
                            <div class="user-card">
                                <div class="user-header">
                                    <div class="user-title">
                                        <?php echo htmlspecialchars($user['FIRST_NAME'] . ' ' . $user['LAST_NAME']); ?>
                                    </div>
                           
                                </div>
                                <div class="user-details">
                                    <div class="user-detail">
                                        <i class="bi bi-telephone"></i>
                                        <span><?php echo htmlspecialchars($user['PHONE']); ?></span>
                                    </div>
                                    <div class="user-detail">
                                        <i class="bi bi-envelope"></i>
                                        <span><?php echo htmlspecialchars($user['EMAIL']); ?></span>
                                    </div>
                                    <div class="user-detail">
                                        <i class="bi bi-geo-alt"></i>
                                        <span><?php echo htmlspecialchars($user['ADDRESS']); ?></span>
                                    </div>
                                </div>
                                
                                <div class="user-actions">
                                    <button class="btn btn-warning btn-sm" onclick="editUser('patient', <?php echo $user['PATIENT_ID']; ?>)">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="user_type" value="patient">
                                        <input type="hidden" name="user_id" value="<?php echo $user['PATIENT_ID']; ?>">
                                        <input type="hidden" name="delete_user" value="1">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="empty-state">
                            <i class="bi bi-person-x"></i>
                            <h4>No patients found</h4>
                            <p>No patients have been registered yet.</p>
                        </div>';
                    }
                    ?>
                </div>
                
                <div class="tab-content <?php echo $user_type === 'doctor' ? 'active' : ''; ?>" id="doctor">
                    <?php
                    // Reset the result pointer for doctors
                    if ($user_type === 'doctor') {
                        mysqli_data_seek($users_result, 0);
                    }
                    
                    if (mysqli_num_rows($users_result) > 0) {
                        while ($user = mysqli_fetch_assoc($users_result)) {
                            ?>
                            <div class="user-card">
                                <div class="user-header">
                                    <div class="user-title">
                                        Dr. <?php echo htmlspecialchars($user['FIRST_NAME'] . ' ' . $user['LAST_NAME']); ?>
                                    </div>
                              
                                </div>
                                
                                <div class="user-details">
                                    <div class="user-detail">
                                        <i class="bi bi-person-badge"></i>
                                        <span><?php echo htmlspecialchars($user['SPECIALISATION_NAME']); ?></span>
                                    </div>
                                   
                                    <div class="user-detail">
                                        <i class="bi bi-telephone"></i>
                                        <span><?php echo htmlspecialchars($user['PHONE']); ?></span>
                                    </div>
                                    <div class="user-detail">
                                        <i class="bi bi-envelope"></i>
                                        <span><?php echo htmlspecialchars($user['EMAIL']); ?></span>
                                    </div>
                                    <div class="user-detail">
                                        <i class="bi bi-calendar-check"></i>
                                        <span>DOJ: <?php echo date('F d, Y', strtotime($user['DOJ'])); ?></span>
                                    </div>
                                </div>
                                
                                <div class="user-actions">
                                    <button class="btn btn-warning btn-sm" onclick="editUser('doctor', <?php echo $user['DOCTOR_ID']; ?>)">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="user_type" value="doctor">
                                        <input type="hidden" name="user_id" value="<?php echo $user['DOCTOR_ID']; ?>">
                                        <input type="hidden" name="delete_user" value="1">
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="empty-state">
                            <i class="bi bi-person-x"></i>
                            <h4>No doctors found</h4>
                            <p>No doctors have been registered yet.</p>
                        </div>';
                    }
                    ?>
                </div>
                
                
            </div>
        </div>
    </div>
    
    <!-- Create User Modal -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="manage_user_profile.php">
                        <input type="hidden" name="create_user" value="1">
                        
                        <div class="form-group">
                            <label for="user_type">User Type</label>
                            <select class="form-control" id="user_type" name="user_type" required onchange="updateUserForm()">
                                <option value="">Select User Type</option>
                                <option value="patient">Patient</option>
                                <option value="doctor">Doctor</option>
                                <option value="receptionist">Receptionist</option>
                            </select>
                        </div>
                        
                        <!-- Patient Form -->
                        <div id="patientForm">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="date" class="form-control" id="dob" name="dob" required>
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" name="gender" required>
                                        <option value="MALE">Male</option>
                                        <option value="FEMALE">Female</option>
                                        <option value="OTHER">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="blood_group">Blood Group</label>
                                    <select class="form-control" id="blood_group" name="blood_group" required>
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
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Doctor Form -->
                        <div id="doctorForm" style="display: none;">
                            <div class="form-group">
                                <label for="specialisation_id">Specialization</label>
                                <select class="form-control" id="specialisation_id" name="specialisation_id" required>
                                    <option value="">Select Specialization</option>
                                    <?php
                                    if (mysqli_num_rows($specialisations_query) > 0) {
                                        while ($specialisation = mysqli_fetch_assoc($specialisations_query)) {
                                            echo '<option value="' . $specialisation['SPECIALISATION_ID'] . '">' . 
                                                 htmlspecialchars($specialisation['SPECIALISATION_NAME']) . '</option>';
                                        }
                                        // Reset the result pointer
                                        mysqli_data_seek($specialisations_query, 0);
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="date" class="form-control" id="dob" name="dob" required>
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" name="gender" required>
                                        <option value="MALE">Male</option>
                                        <option value="FEMALE">Female</option>
                                        <option value="OTHER">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="doj">Date of Joining</label>
                                    <input type="date" class="form-control" id="doj" name="doj" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Receptionist Form -->
                        <div id="receptionistForm" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="dob">Date of Birth</label>
                                    <input type="date" class="form-control" id="dob" name="dob" required>
                                </div>
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" name="gender" required>
                                        <option value="MALE">Male</option>
                                        <option value="FEMALE">Female</option>
                                        <option value="OTHER">Other</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="doj">Date of Joining</label>
                                    <input type="date" class="form-control" id="doj" name="doj" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check"></i> Create User
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
    
    <!-- Edit User Modal -->
    <div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">&times;</span>

        <!-- Dynamic Title -->
        <h2 id="modalTitle">Edit User</h2>

        <!-- ================= PATIENT FORM ================= -->
        <form id="editPatientForm" method="post" action="manage_user_profile.php" style="display:none;">
            <input type="hidden" name="action" value="edit_patient">
            <input type="hidden" id="edit_patient_id" name="patient_id">

            <!-- PASTE YOUR PATIENT FORM FIELDS HERE EXACTLY -->
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


        <!-- ================= DOCTOR FORM ================= -->
        <form id="editDoctorForm" method="post" action="manage_user_profile.php" enctype="multipart/form-data" style="display:none;">
            <input type="hidden" name="action" value="edit_doctor">
            <input type="hidden" id="edit_doctor_id" name="doctor_id">
            <input type="hidden" id="current_image" name="current_image">

           
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
        </form>

    </div>
</div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

        // Function to edit user
        function editUser(userType, userId) {

    // Hide both forms first
    document.getElementById("editPatientForm").style.display = "none";
    document.getElementById("editDoctorForm").style.display = "none";

    // Set modal title
    document.getElementById("modalTitle").innerText = "Edit " + userType.charAt(0).toUpperCase() + userType.slice(1);

    if(userType === "patient"){
        document.getElementById("editPatientForm").style.display = "block";
        document.getElementById("edit_patient_id").value = userId;
    }

    if(userType === "doctor"){
        document.getElementById("editDoctorForm").style.display = "block";
        document.getElementById("edit_doctor_id").value = userId;
    }

    document.getElementById("editModal").style.display = "block";
}

        
        // Function to update user form based on selected user type
        function updateUserForm() {
            const userType = document.getElementById('user_type').value;
            
            // Hide all forms
            document.getElementById('patientForm').style.display = 'none';
            document.getElementById('doctorForm').style.display = 'none';
            document.getElementById('receptionistForm').style.display = 'none';
            
            // Show the appropriate form
            if (userType === 'patient') {
                document.getElementById('patientForm').style.display = 'block';
            } else if (userType === 'doctor') {
                document.getElementById('doctorForm').style.display = 'block';
            } else if (userType === 'receptionist') {
                document.getElementById('receptionistForm').style.display = 'block';
            }
        }
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