<?php
include "config.php";

$spec_id = $_GET['spec_id'];

$q = "SELECT DOCTOR_ID, FIRST_NAME, LAST_NAME FROM doctor_tbl WHERE SPECIALISATION_ID = $spec_id";
$res = mysqli_query($conn,$q);
?>
<!DOCTYPE html>
<html>
<head>
<title>Doctors</title>
<style>
body{font-family:Arial;background:#f5f8ff;}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:20px;width:85%;margin:40px auto;}
.card{background:white;padding:20px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,.1);text-align:center;}
img{width:100px;height:100px;border-radius:50%;}
button{margin:5px;padding:8px 14px;background:#2e6ad6;color:white;border:none;border-radius:6px;}
.secondary{background:#555;}
</style>
</head>
<body>

<h1 style="text-align:center;">Doctors</h1>

<div class="grid">
<?php while($row = mysqli_fetch_assoc($res)){ ?>
    <div class="card">
        <img src="imgs/default.jpg">
        <h3><?php echo $row['FIRST_NAME']." ".$row['LAST_NAME']; ?></h3>

        <a href="doctor_profile.php?id=<?php echo $row['DOCTOR_ID']; ?>">
            <button>View Profile</button>
        </a>

        <a href="calendar.php?doctor_id=<?php echo $row['DOCTOR_ID']; ?>">
            <button>Book Now</button> 
            </a>

    </div>
<?php } ?>
</div>

</body>
</html>
