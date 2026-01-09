<?php
session_start();
include 'config.php';

// Get form data
 $username = $_POST['username'] ?? '';
 $password = $_POST['pswd'] ?? '';
 $user_type = $_POST['user_type'] ?? '';

// Validate input
if (empty($username) || empty($password) || empty($user_type)) {
    // Use POST to redirect with error
    echo '<form id="errorForm" action="login_for_all.php" method="post">
            <input type="hidden" name="error" value="Please fill all fields">
            <input type="hidden" name="user_type" value="' . $user_type . '">
          </form>
          <script>document.getElementById("errorForm").submit();</script>';
    exit;
}

// Sanitize input
 $username = mysqli_real_escape_string($conn, $username);

// Check user type and query appropriate table
switch ($user_type) {
    case 'patient':
        $query = "SELECT PATIENT_ID, FIRST_NAME, LAST_NAME, EMAIL, PSWD FROM patient_tbl WHERE USERNAME='$username'";
        $redirect_page = 'patient.php';
        $session_id_key = 'PATIENT_ID';
        $session_name_key = 'PATIENT_NAME';
        break;
        
    case 'doctor':
        $query = "SELECT DOCTOR_ID, FIRST_NAME, LAST_NAME, EMAIL, PSWD FROM doctor_tbl WHERE USERNAME='$username'";
        $redirect_page = 'doctor_dashboard.php';
        $session_id_key = 'DOCTOR_ID';
        $session_name_key = 'DOCTOR_NAME';
        break;
        
    case 'receptionist':
        $query = "SELECT RECEPTIONIST_ID, FIRST_NAME, LAST_NAME, EMAIL, PSWD FROM receptionist_tbl WHERE USERNAME='$username'";
        $redirect_page = 'receptionist.php';
        $session_id_key = 'RECEPTIONIST_ID';
        $session_name_key = 'RECEPTIONIST_NAME';
        break;
        
    default:
        // Use POST to redirect with error
        echo '<form id="errorForm" action="login_for_all.php" method="post">
                <input type="hidden" name="error" value="Invalid user type">
                <input type="hidden" name="user_type" value="' . $user_type . '">
              </form>
              <script>document.getElementById("errorForm").submit();</script>';
        exit;
}

// Execute query
 $result = mysqli_query($conn, $query);

// Check if user exists
if (!$result || mysqli_num_rows($result) !== 1) {
    // Use POST to redirect with error
    echo '<form id="errorForm" action="login_for_all.php" method="post">
            <input type="hidden" name="error" value="Invalid username or password">
            <input type="hidden" name="user_type" value="' . $user_type . '">
          </form>
          <script>document.getElementById("errorForm").submit();</script>';
    exit;
}

// Get user data
 $user = mysqli_fetch_assoc($result);

// Verify password
if (!password_verify($password, $user['PSWD'])) {
    // Use POST to redirect with error
    echo '<form id="errorForm" action="login_for_all.php" method="post">
            <input type="hidden" name="error" value="Invalid username or password">
            <input type="hidden" name="user_type" value="' . $user_type . '">
          </form>
          <script>document.getElementById("errorForm").submit();</script>';
    exit;
}

// Set session variables
 $_SESSION[$session_id_key] = $user[$session_id_key];
 $_SESSION[$session_name_key] = $user['FIRST_NAME'] . ' ' . $user['LAST_NAME'];
 $_SESSION['USER_TYPE'] = $user_type;
 $_SESSION['EMAIL'] = $user['EMAIL'];
 $_SESSION['LOGGED_IN'] = true;

// Use POST to redirect to appropriate dashboard
echo '<form id="redirectForm" action="' . $redirect_page . '" method="post"></form>
      <script>document.getElementById("redirectForm").submit();</script>';
exit;
?>