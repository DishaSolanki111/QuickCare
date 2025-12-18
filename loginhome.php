<?php
// Start a session to store user information after login
session_start();

// Include your database connection file
require_once 'config.php';

// Check if the form was submitted using POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get username, password, and user type from the form
    $username = $_POST['username'];
    $password = $_POST['pswd']; // 'pswd' matches the 'name' attribute in your HTML
    $user_type = $_POST['user_type']; // This comes from the hidden input in the form

    // Determine which database table and dashboard to use based on user type
    $table = "";
    $dashboard_page = "";
    $id_field = "";
    $name_field = "";

    switch ($user_type) {
        case 'patient':
            $table = "patient_tbl";
            $dashboard_page = "patient.html";
            $id_field = "PATIENT_ID";
            $name_field = "FIRST_NAME";
            break;
        case 'doctor':
            $table = "doctor_tbl";
            $dashboard_page = "dashboard_doctor.html";
            $id_field = "DOCTOR_ID";
            $name_field = "FIRST_NAME";
            break;
        case 'receptionist':
            $table = "receptionist_tbl";
            $dashboard_page = "receptionist.html";
            $id_field = "RECEPTIONIST_ID";
            $name_field = "FIRST_NAME";
            break;
        default:
            // Invalid user type, redirect back with an error
            header("location: login.html?error=1");
            exit();
    }

    // Prepare the SQL statement to prevent SQL injection
    $sql = "SELECT $id_field, $name_field FROM $table WHERE USERNAME = ? AND PSWD = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        // Error preparing the statement, redirect back with an error
        header("location: login.html?error=1");
        exit();
    }

    // Bind parameters (string, string) and execute the query
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user was found
    if ($result->num_rows === 1) {
        // Login successful!
        $row = $result->fetch_assoc();

        // Store user information in session variables
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $row[$id_field];
        $_SESSION['username'] = $username;
        $_SESSION['name'] = $row[$name_field];
        $_SESSION['role'] = $user_type;

        // Redirect to the correct dashboard
        header("location: " . $dashboard_page);
        exit();

    } else {
        // Login failed, redirect back with an error
        header("location: login.html?error=1");
        exit();
    }

} else {
    // If someone tries to access this file directly without submitting a form
    header("location: login.html");
    exit();
}
?>