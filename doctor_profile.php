<?php
include 'config.php';
include 'header.php';

if (!isset($_GET['id'])) {
    die("Doctor ID missing.");
}

$doctor_id = intval($_GET['id']);

/* FETCH DOCTOR DETAILS */
$doctor_sql = "
    SELECT d.DOCTOR_ID, d.FIRST_NAME, d.LAST_NAME, d.PHONE, d.EMAIL,
           d.PROFILE_IMAGE, s.SPECIALISATION_NAME
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

/* FETCH DOCTOR SCHEDULE */
$schedule_sql = "
    SELECT AVAILABLE_DAY, START_TIME, END_TIME
    FROM doctor_schedule_tbl
    WHERE DOCTOR_ID = $doctor_id
    ORDER BY FIELD(AVAILABLE_DAY,'MON','TUE','WED','THU','FRI','SAT','SUN')
";

$schedule_res = mysqli_query($conn, $schedule_sql);

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
:root {
  --primary: #0066cc;
  --dark: #1a3a5f;
}

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

.right{
    width:65%;
    padding:30px;
}

.badge{
    background:#2e6ad6;
    color:#fff;
    padding:6px 14px;
    border-radius:20px;
}
footer{
    background:var(--dark);
    color:white;
    padding:2rem;
    text-align:center;
}
</style>
</head>

<body>

<div class="profile-card">
    <div class="left">
        <img src="<?= htmlspecialchars($image_path) ?>" alt="Doctor">
        <h2>Dr. <?= htmlspecialchars($doctor['FIRST_NAME'].' '.$doctor['LAST_NAME']) ?></h2>
    </div>

    <div class="right">
        <div class="badge"><?= htmlspecialchars($doctor['SPECIALISATION_NAME']) ?></div>

        <p>📞 <?= htmlspecialchars($doctor['PHONE']) ?></p>
        <p>📧 <?= htmlspecialchars($doctor['EMAIL']) ?></p>

        <h3>Available Schedule</h3>
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

<footer>
<p>&copy; <?= date('Y') ?> QuickCare</p>
</footer>

</body>
</html>
