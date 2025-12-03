<?php
@include 'config.php';  // your DB connection

$spec_id = $_GET['spec_id'] ?? 0;

#$query = "SELECT * FROM doctor_tbl WHERE SPECIALISATION_ID = '$spec_id'";
$query = "
SELECT d.*, s.SPECIALISATION_NAME 
FROM doctor_tbl d
JOIN specialisation_tbl s
    ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
WHERE d.SPECIALISATION_ID = '$spec_id'
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Specialists</title>
<link rel="stylesheet" href="specialists.css">
</head>

<body>

<h1 class="main-title">Book Your Appointment</h1>

<div class="container">
    <h2 class="sub-title">Available Specialists</h2>

    <div class="cards">
        <?php 
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){ ?>
                
                <div class="card">
                    <img src="imgs/doctor_<?php echo $row['DOCTOR_ID']; ?>.jpg" onerror="this.src='imgs/default.jpg'" class="doc-img" alt="doctor">
                    <h3 class="doc-name">Dr. <?php echo $row['FIRST_NAME']; ?></h3>
                    <p class="doc-type"><?php echo $row['SPECIALISATION_NAME']; ?></p>
                    <a href="book.php?doc_id=<?php echo $row['DOCTOR_ID']; ?>" class="btn">Book Now</a>
                    <a class="btn">View Profile</a>
                </div>

        <?php }} else { ?>
            <p class="no-data">No doctors found for this specialization.</p>
        <?php } ?>
    </div>

</div>

</body>
</html>
