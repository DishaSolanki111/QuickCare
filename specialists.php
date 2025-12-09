<?php
session_start();
@include 'config.php';

$spec_id = $_GET['spec_id'] ?? 0;

$query = "
SELECT d.DOCTOR_ID, d.FIRST_NAME, d.LAST_NAME, s.SPECIALISATION_NAME
FROM doctor_tbl d
JOIN specialisation_tbl s 
    ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
WHERE d.SPECIALISATION_ID = '$spec_id'
";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Available Specialists</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f8ff;
            margin: 0;
            padding: 40px 0;
            display: flex;
            justify-content: center;
        }

        .container {
            width: 85%;
            text-align: center;
        }

        h1 {
            font-size: 32px;
            color: #1a3c6e;
            margin-bottom: 30px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 25px;
            margin-top: 25px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0px 6px 20px rgba(0,0,0,0.15);
        }

        .doc-img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .doc-name {
            font-size: 20px;
            font-weight: bold;
            color: #1a3c6e;
        }

        .doc-type {
            color: #616161;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .btn-row {
            margin-top: 10px;
        }

        .btn {
            display: inline-block;
            background: #2e6ad6;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            margin: 5px;
            font-size: 15px;
            transition: 0.2s;
        }

        .btn:hover {
            background: #1f56b3;
        }

        .no-data {
            font-size: 20px;
            margin-top: 30px;
            color: #455a64;
        }
    </style>
</head>

<body>

<div class="container">
    <h1>Available Specialists</h1>

    <div class="cards">
        <?php 
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_assoc($result)){
        ?>

        <div class="card">
            <img src="imgs/doctor_<?php echo $row['DOCTOR_ID']; ?>.jpg"
                 onerror="this.src='imgs/default.jpg'"
                 class="doc-img">

            <h3 class="doc-name">Dr. <?php echo $row['FIRST_NAME'] . " " . $row['LAST_NAME']; ?></h3>
            <p class="doc-type"><?php echo $row['SPECIALISATION_NAME']; ?></p>

            <div class="btn-row">
                <a class="btn" href="javascript:void(0)" onclick="bookNow(<?php echo $row['DOCTOR_ID']; ?>)">Book Now</a>

                <a href="doctor_profile.php?id=<?php echo $row['DOCTOR_ID']; ?>" class="btn">View Profile</a>

            </div>
        </div>

        <?php 
            }
        } else {
            echo '<p class="no-data">No doctors available for this specialization.</p>';
        }
        ?>
    </div>

</div>
<script>
function bookNow(docId) {
    fetch("check_login.php")
    .then(response => response.text())
    .then(status => {

        if(status === "NOT_LOGGED_IN") {
            // show login popup
            document.getElementById("loginPopup").style.display = "flex";
            document.getElementById("docIdInput").value = docId;
        } 
        else if(status === "LOGGED_IN") {
            // start otp flow
            window.location.href = "send_otp.php?doc_id=" + docId;
        }
    });
}
</script>

<!-- LOGIN POPUP -->
<div id="loginPopup" style="
    display:none; 
    position:fixed; 
    top:0; left:0; 
    width:100%; height:100%;
    background:rgba(0,0,0,0.6); 
    justify-content:center; 
    align-items:center;
">
    <div style="
        background:white;
        padding:30px;
        width:350px;
        border-radius:12px;
        text-align:center;
    ">

        <h2>Patient Login</h2>

        <form method="POST" action="login_process.php">
            <input type="hidden" name="doc_id" id="docIdInput">

            <input type="text" name="username" placeholder="Username" required
              style="width:100%; padding:10px; margin:10px 0; border-radius:8px; border:1px solid #ccc;">

            <input type="password" name="password" placeholder="Password" required
              style="width:100%; padding:10px; margin:10px 0; border-radius:8px; border:1px solid #ccc;">

            <button style="
                width:100%; padding:10px; background:#2e6ad6;
                color:white; border:none; border-radius:8px; margin-top:10px;
            ">Login</button>

        </form>

        <p style="margin-top:10px;">
            New Patient?  
            <a href="register.php" style="color:#2e6ad6; font-weight:bold;">Register</a>
        </p>
    </div>
</div>

</body>
</html>