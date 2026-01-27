<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

/* ===============================
   GET FORM DATA
================================ */
 $user_type = $_POST['user_type'] ?? '';
 $username  = $_POST['username'] ?? '';
 $pswd  = $_POST['pswd'] ?? '';

/* ===============================
   BASIC VALIDATION
================================ */
if (empty($user_type) || empty($username) || empty($pswd)) {
    $_POST['error'] = 'All fields are required';
    $_POST['user_type'] = $user_type;
    include 'login_for_all.php';
    exit();
}

/* ===============================
   DOCTOR LOGIN
================================ */
if ($user_type === 'doctor') {

    $stmt = $conn->prepare(
        "SELECT DOCTOR_ID, FIRST_NAME, PSWD 
         FROM doctor_tbl 
         WHERE USERNAME = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $row = $res->fetch_assoc();

        // 🔐 Verify bcrypt password
        if (password_verify($pswd, $row['PSWD'])) {

            $_SESSION['LOGGED_IN'] = true;
            $_SESSION['USER_TYPE'] = 'doctor';
            $_SESSION['DOCTOR_ID'] = $row['DOCTOR_ID'];
            $_SESSION['USER_NAME'] = $row['FIRST_NAME'];
            header("Location: doctor_dashboard.php");
            exit();
        }
    }

    // ❌ Doctor login failed
    $_POST['error'] = 'Invalid doctor username or password';
    $_POST['user_type'] = 'doctor';
    include 'login_for_all.php';
    exit();
}

/* ===============================
   PATIENT LOGIN
================================ */
if ($user_type === 'patient') {

    $stmt = $conn->prepare(
        "SELECT PATIENT_ID, FIRST_NAME, PSWD 
         FROM patient_tbl 
         WHERE USERNAME = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $row = $res->fetch_assoc();

        // 🔐 Verify bcrypt password
        if (password_verify($pswd, $row['PSWD'])) {

            $_SESSION['LOGGED_IN'] = true;
            $_SESSION['USER_TYPE'] = 'patient';
            $_SESSION['PATIENT_ID'] = $row['PATIENT_ID'];
            $_SESSION['USER_NAME'] = $row['FIRST_NAME']; 

            header("Location: patient.php");
            exit();
        }
    }

    // ❌ Patient login failed
    $_POST['error'] = 'Invalid patient username or password';
    $_POST['user_type'] = 'patient';
    include 'login_for_all.php';
    exit();
}

/* ===============================
   RECEPTIONIST LOGIN
================================ */
if ($user_type === 'receptionist') {

    $stmt = $conn->prepare(
        "SELECT RECEPTIONIST_ID, FIRST_NAME, PSWD 
         FROM receptionist_tbl 
         WHERE USERNAME = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res && $res->num_rows === 1) {
        $row = $res->fetch_assoc();

        // 🔐 Verify bcrypt password
        if (password_verify($pswd, $row['PSWD'])) {

            $_SESSION['LOGGED_IN'] = true;
            $_SESSION['USER_TYPE'] = 'receptionist';
            $_SESSION['RECEPTIONIST_ID'] = $row['RECEPTIONIST_ID'];
            $_SESSION['USER_NAME'] = $row['FIRST_NAME'];
            header("Location: receptionist.php");
            exit();
        }
    }

    // ❌ Receptionist login failed
    $_POST['error'] = 'Invalid receptionist username or password';
    $_POST['user_type'] = 'receptionist';
    include 'login_for_all.php';
    exit();
}

/* ===============================
   UNKNOWN USER TYPE
================================ */
 $_POST['error'] = 'Invalid login attempt';
include 'login_for_all.php';
exit();
?>