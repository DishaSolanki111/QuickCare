<?php
include "config.php";

// Test login with hardcoded credentials
 $username = "testuser"; // Replace with a real username from your database
 $password = "testpass"; // Replace with the corresponding password

 $query = "SELECT * FROM patient_tbl WHERE USERNAME = '$username' AND PSWD = '$password'";
 $result = mysqli_query($conn, $query);

echo "Query: " . $query . "<br>";
echo "Number of rows: " . mysqli_num_rows($result) . "<br>";

if (mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);
    echo "User found:<br>";
    echo "ID: " . $user['PATIENT_ID'] . "<br>";
    echo "Name: " . $user['FIRST_NAME'] . ' ' . $user['LAST_NAME'] . "<br>";
    echo "Username: " . $user['USERNAME'] . "<br>";
} else {
    echo "User not found or incorrect password";
}

// Show all users for debugging (remove this in production)
echo "<br><br>All users:<br>";
 $all_users = mysqli_query($conn, "SELECT PATIENT_ID, USERNAME, FIRST_NAME, LAST_NAME FROM patient_tbl LIMIT 5");
while ($row = mysqli_fetch_assoc($all_users)) {
    echo "ID: " . $row['PATIENT_ID'] . ", Username: " . $row['USERNAME'] . ", Name: " . $row['FIRST_NAME'] . ' ' . $row['LAST_NAME'] . "<br>";
}
?>