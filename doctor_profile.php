<?php
include 'config.php';
include 'header.php';
if (!isset($_GET['id'])) {
    die("Doctor ID missing.");
}

$doctor_id = intval($_GET['id']);

/* =========================
   FETCH DOCTOR DETAILS
========================= */
$doctor_sql = "
    SELECT 
        d.DOCTOR_ID,
        d.FIRST_NAME,
        d.LAST_NAME,
        d.PHONE,
        d.EMAIL,
        d.PROFILE_IMAGE,
        s.SPECIALISATION_NAME
    FROM doctor_tbl d
    JOIN specialisation_tbl s 
        ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE d.DOCTOR_ID = $doctor_id
";

$doctor_res = mysqli_query($conn, $doctor_sql);
$doctor = mysqli_fetch_assoc($doctor_res);

if (!$doctor) {
    die("Doctor not found.");
}

/* =========================
   FETCH DOCTOR SCHEDULE
========================= */
$schedule_sql = "
    SELECT AVAILABLE_DAY, START_TIME, END_TIME
    FROM doctor_schedule_tbl
    WHERE DOCTOR_ID = $doctor_id
    ORDER BY FIELD(AVAILABLE_DAY,'MON','TUE','WED','THU','FRI','SAT','SUN')
";

$schedule_res = mysqli_query($conn, $schedule_sql);

/* =========================
   PROFILE IMAGE PATH
========================= */
$image_path = !empty($doctor['PROFILE_IMAGE']) 
    ? $doctor['PROFILE_IMAGE'] 
    : 'imgs/default.jpg';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Doctor Profile</title>

<style>
body{
    font-family: Arial, sans-serif;
    background:#f5f7fb;
    display:flex;
    justify-content:center;
    padding:40px;
}

.profile-card{
    width:800px;
    display:flex;
    background:#fff;
    border-radius:15px;
    overflow:hidden;
    box-shadow:0 6px 20px rgba(0,0,0,.1);
}

.left{
    width:35%;
    background:#eef3ff;
    text-align:center;
    padding:25px;
}

.left img{
    width:100%;
    height:280px;
    object-fit:cover;
    border-radius:12px;
}

.left h2{
    margin-top:15px;
    color:#2e3a59;
}

.right{
    width:65%;
    padding:30px;
}

.section-title{
    margin-top:20px;
    font-weight:bold;
    color:#555;
}

.badge{
    display:inline-block;
    background:#2e6ad6;
    color:#fff;
    padding:6px 14px;
    border-radius:20px;
    margin-top:6px;
}

.schedule p{
    margin:6px 0;
    color:#444;
}
</style>
</head>

<body>

<div class="profile-card">

    <!-- LEFT -->
    <div class="left">
        <img src="<?php echo htmlspecialchars($image_path); ?>" alt="Doctor">
        <h2>Dr. <?php echo htmlspecialchars($doctor['FIRST_NAME'].' '.$doctor['LAST_NAME']); ?></h2>
    </div>

    <!-- RIGHT -->
    <div class="right">

        <div class="section-title">SPECIALISATION</div>
        <span class="badge"><?php echo htmlspecialchars($doctor['SPECIALISATION_NAME']); ?></span>

        <div class="section-title">CONTACT</div>
        <p>📞 <?php echo htmlspecialchars($doctor['PHONE']); ?></p>
        <p>📧 <?php echo htmlspecialchars($doctor['EMAIL']); ?></p>

        <div class="section-title">AVAILABLE SCHEDULE</div>
        <div class="schedule">
            <?php
            if (mysqli_num_rows($schedule_res) > 0) {
                while ($row = mysqli_fetch_assoc($schedule_res)) {
                    echo "<p><strong>{$row['AVAILABLE_DAY']}:</strong> "
                        . substr($row['START_TIME'],0,5)
                        . " - "
                        . substr($row['END_TIME'],0,5)
                        . "</p>";
                }
            } else {
                echo "<p>No schedule available</p>";
            }
            ?>
        </div>

    </div>
</div>

</body>
</html>
