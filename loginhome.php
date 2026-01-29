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
 $pswd      = $_POST['pswd'] ?? '';

/* ===============================
   BASIC VALIDATION
================================ */
if (empty($user_type) || empty($username) || empty($pswd)) {
    // Return JSON Error for AJAX to catch
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
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

            // ✅ Return JSON Success
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'message' => 'Login Successful! Redirecting to Dashboard...',
                'redirect' => 'doctor_dashboard.php'
            ]);
            exit();
        }
    }

    // ❌ Doctor login failed
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid doctor username or password']);
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
            $_SESSION['role'] = 'patient';


            // ✅ Return JSON Success
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'message' => 'Login Successful! Redirecting to Portal...',
                'redirect' => 'patient.php'
            ]);
            exit();
        }
    }

    // ❌ Patient login failed
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid patient username or password']);
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

            // ✅ Return JSON Success
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'message' => 'Login Successful! Redirecting to Dashboard...',
                'redirect' => 'receptionist.php'
            ]);
            exit();
        }
    }

    // ❌ Receptionist login failed
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Invalid receptionist username or password']);
    exit();
}

/* ===============================
   UNKNOWN USER TYPE
================================ */
header('Content-Type: application/json');
echo json_encode(['status' => 'error', 'message' => 'Invalid login attempt']);
exit();
?>