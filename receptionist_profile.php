<?php
// ================== SESSION & ACCESS CONTROL ==================
session_start();

if (
    !isset($_SESSION['LOGGED_IN']) ||
    $_SESSION['LOGGED_IN'] !== true ||
    $_SESSION['USER_TYPE'] !== 'receptionist'
) {
    header("Location: login.php");
    exit();
}

// ================== DATABASE CONNECTION ==================
include 'config.php';

// ================== RECEPTIONIST INFO ==================
$receptionist_id = $_SESSION['RECEPTIONIST_ID'];
$receptionist = [];

$sql = "SELECT * FROM receptionist_tbl WHERE RECEPTIONIST_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $receptionist_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $receptionist = $result->fetch_assoc();
}
$stmt->close();

// ================== UPDATE PROFILE ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    $update_sql = "UPDATE receptionist_tbl 
                   SET PHONE = ?, EMAIL = ? 
                   WHERE RECEPTIONIST_ID = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $phone, $email, $receptionist_id);

    if ($update_stmt->execute()) {
        $success_message = "Profile updated successfully!";
    } else {
        $error_message = "Profile update failed!";
    }
    $update_stmt->close();
}

// ================== CHANGE PASSWORD ==================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new === $confirm) {
        if (password_verify($current, $receptionist['PSWD'])) {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $pwd_sql = "UPDATE receptionist_tbl SET PSWD=? WHERE RECEPTIONIST_ID=?";
            $pwd_stmt = $conn->prepare($pwd_sql);
            $pwd_stmt->bind_param("si", $hash, $receptionist_id);
            $pwd_stmt->execute();
            $password_success = "Password changed successfully!";
            $pwd_stmt->close();
        } else {
            $password_error = "Current password incorrect!";
        }
    } else {
        $password_error = "Passwords do not match!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Receptionist Profile</title>
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
      
        /* Main Content */
        .main-content {
            margin-left: 260px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            width: 1500px;
            min-height: 100vh;
        }
/* Top bar */
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
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding: 10px;
            background: var(--white);
            border-radius: 12px;
            box-shadow: var(--shadow-md);
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 25px;
            border: 4px solid var(--primary-light);
        }

        .profile-info h2 {
            font-size: 28px;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .profile-info p {
            color: var(--text-light);
            margin-bottom: 10px;
        }

        .profile-tabs {
            display: flex;
            margin-bottom: 25px;
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
        }

        .tab {
            flex: 1;
            padding: 15px 20px;
            text-align: center;
            cursor: pointer;
            background: var(--white);
            color: var(--text-light);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .tab.active {
            background: var(--primary);
            color: var(--white);
        }

        .tab-content {
            display: none;
            background: var(--white);
            border-radius: 12px;
            padding: 25px;
            box-shadow: var(--shadow-md);
        }

        .tab-content.active {
            display: block;
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

        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
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

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .info-item {
            margin-bottom: 15px;
        }

        .info-label {
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 5px;
            display: block;
        }

        .info-value {
            color: #555;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            
            .sidebar-header h2 {
                display: none;
            }
            
            .sidebar-nav a span {
                display: none;
            }
            
            .sidebar-nav a {
                justify-content: center;
            }
            
            .sidebar-nav a i {
                margin: 0;
            }
            
            .main-content {
                margin-left: 70px;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.active {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .topbar {
                padding: 15px 20px;
            }
            
            .profile-content {
                padding: 20px;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-avatar {
                margin-right: 0;
                margin-bottom: 20px;
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

<?php include 'recept_sidebar.php'; ?>


<div class="main-content">
    <div class="topbar">
        <h1>My Profile</h1>
        <p>Welcome,<?= $receptionist['FIRST_NAME'].' '.$receptionist['LAST_NAME'] ?></p>
    </div>
  
<div class="profile-content">



<!-- TABS -->
<div class="profile-tabs">
    <div class="tab active" data-tab="personal">Personal Information</div>
    <div class="tab" data-tab="security">Security</div>
</div>

<!-- PERSONAL INFO -->
<div class="tab-content active" id="personal">
<form method="POST">
<input type="hidden" name="update_profile">

<div class="info-grid">
    <div class="form-group">
        <label>First Name</label>
        <input class="form-control" value="<?= $receptionist['FIRST_NAME'] ?>" disabled>
    </div>

    <div class="form-group">
        <label>Last Name</label>
        <input class="form-control" value="<?= $receptionist['LAST_NAME'] ?>" disabled>
    </div>

    <div class="form-group">
        <label>Date of Birth</label>
        <input class="form-control" value="<?= $receptionist['DOB'] ?>" disabled>
    </div>

    <div class="form-group">
        <label>Gender</label>
        <input class="form-control" value="<?= $receptionist['GENDER'] ?>" disabled>
    </div>

    <div class="form-group">
        <label>Phone</label>
        <input name="phone" class="form-control" value="<?= $receptionist['PHONE'] ?>">
    </div>

    <div class="form-group">
        <label>Email</label>
        <input name="email" class="form-control" value="<?= $receptionist['EMAIL'] ?>">
    </div>
</div>

<button class="btn btn-primary">
<i class="fas fa-save"></i> Save Changes
</button>
</form>
</div>

<!-- SECURITY -->
<div class="tab-content" id="security">
<form method="POST">
<input type="hidden" name="change_password">

<div class="form-group">
<label>Current Password</label>
<input type="password" name="current_password" class="form-control">
</div>

<div class="form-group">
<label>New Password</label>
<input type="password" name="new_password" class="form-control">
</div>

<div class="form-group">
<label>Confirm Password</label>
<input type="password" name="confirm_password" class="form-control">
</div>

<button class="btn btn-primary">
<i class="fas fa-key"></i> Change Password
</button>
</form>
</div>

</div>
</div>

<script>
document.querySelectorAll('.tab').forEach(tab=>{
tab.onclick=()=>{
document.querySelectorAll('.tab,.tab-content').forEach(e=>e.classList.remove('active'));
tab.classList.add('active');
document.getElementById(tab.dataset.tab).classList.add('active');
};
});
</script>

</body>
</html>
