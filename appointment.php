<?php
include "config.php";

$q = "SELECT SPECIALISATION_ID, SPECIALISATION_NAME FROM specialisation_tbl";
$res = mysqli_query($conn,$q);
?>
<!DOCTYPE html>
<html>
<head>
<title>Select Specialization</title>
<style>
body{font-family:Arial;background:#f5f8ff;}
.grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px;width:80%;margin:40px auto;}
.card{background:white;padding:25px;border-radius:15px;box-shadow:0 4px 12px rgba(0,0,0,.1);text-align:center;}
button{margin-top:15px;padding:10px 16px;background:#2e6ad6;color:white;border:none;border-radius:8px;}
</style>
</head>
<body>

<h1 style="text-align:center;">Choose Specialization</h1>

<div class="grid">
<?php while($row = mysqli_fetch_assoc($res)){ ?>
    <div class="card">
        <h2><?php echo $row['SPECIALISATION_NAME']; ?></h2>
        <a href="doctors.php?spec_id=<?php echo $row['SPECIALISATION_ID']; ?>">
            <button>View Doctors</button>
        </a>
    </div>
<?php } ?>
</div>

</body>
</html>
