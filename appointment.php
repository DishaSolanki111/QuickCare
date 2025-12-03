<?php
// database connection
$conn = new mysqli("localhost", "root", "your_password", "quick_care");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT SPECIALISATION_ID, SPECIALISATION_NAME FROM specialisation_tbl";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Appointment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f9fc;
            display: flex;
            justify-content: center;
            padding-top: 40px;
        }

        .container {
            width: 80%;
            text-align: center;
        }

        h1 {
            color: #0a3d62;
            margin-bottom: 20px;
        }

        .btn-box {
            margin-top: 25px;
        }

        a.special-btn {
            display: inline-block;
            margin: 10px;
            padding: 12px 25px;
            border: 2px solid #0a3d62;
            border-radius: 6px;
            text-decoration: none;
            color: #0a3d62;
            font-size: 17px;
            transition: 0.3s;
        }

        a.special-btn:hover {
            background: #0a3d62;
            color: white;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Book Your Appointment</h1>
    <h3>Select Doctor Specialization</h3>

    <div class="btn-box">
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<a class="special-btn" href="specialists.php?spec_id=' . $row['SPECIALISATION_ID'] . '">' 
                     . $row['SPECIALISATION_NAME'] . 
                     '</a>';
            }
        } else {
            echo "<p>No specializations found.</p>";
        }
        ?>
    </div>
</div>

</body>
</html>
