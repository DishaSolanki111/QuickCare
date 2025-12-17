<?php
include 'config.php'; // DB connection
include 'header.php';
include 'footer.php';

if (!isset($_GET['id'])) {
    die("Doctor ID missing.");
}

$doctor_id = intval($_GET['id']);

// ===== 1. FETCH DOCTOR BASIC DETAILS =====
$doc_sql = "
    SELECT d.*, s.SPECIALISATION_NAME 
    FROM doctor_tbl d
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE d.DOCTOR_ID = $doctor_id
";

$doc_result = mysqli_query($conn, $doc_sql);
$doctor = mysqli_fetch_assoc($doc_result);

if (!$doctor) {
    die("Doctor not found.");
}

// ===== 2. FETCH DOCTOR SCHEDULE =====
$schedule_sql = "
    SELECT AVAILABLE_DAY, START_TIME, END_TIME
    FROM doctor_schedule_tbl 
    WHERE DOCTOR_ID = $doctor_id
    ORDER BY FIELD(AVAILABLE_DAY,'MON','TUE','WED','THU','FRI','SAT','SUN')
";

$schedule_result = mysqli_query($conn, $schedule_sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Doctor Profile</title>

<style>
body {
    font-family: Arial, sans-serif;
    background: #fdf5f5;
    display: flex;
    justify-content: center;
    padding: 40px;
}

.profile-card {
    width: 800px;
    display: flex;
    background: linear-gradient(135deg, #ffb199 0%, #ff0844 100%);
    border-radius: 15px;
    overflow: hidden;
}

.left {
    width: 40%;
    text-align: center;
    background: #fff;
    padding: 20px;
}

.left img {
    width: 100%;
    border-radius: 10px;
}

.left h2 {
    margin-top: 15px;
}

.right {
    width: 60%;
    padding: 30px;
    color: white;
    position: relative;
}

.fee-tag {
    position: absolute;
    right: 20px;
    top: 20px;
    background: #ff4040;
    padding: 8px 20px;
    border-radius: 10px;
    font-weight: bold;
}

.section-title {
    font-weight: bold;
    margin-top: 25px;
    margin-bottom: 5px;
}

.badge {
    display: inline-block;
    background: rgba(255,255,255,0.2);
    padding: 6px 14px;
    border-radius: 20px;
    margin: 5px 5px 0 0;
}
</style>
</head>
<body>

<div class="profile-card">

    <div class="left">
        <!-- Static Images (Not from DB) -->
        <img src="imgs/doctor_<?php echo $doctor_id; ?>.jpg" alt="Doctor">
        <h2><?php echo "Dr. " . $doctor['FIRST_NAME'] . " " . $doctor['LAST_NAME']; ?></h2>
    </div>

    <div class="right">

        <div class="fee-tag">₹500</div>

        <h2>Profile</h2>
        <p><strong>MBBS</strong></p>
        <p>Experience: 20+ Years</p>

        <div class="section-title">SPECIALTY</div>
        <span class="badge"><?php echo $doctor['SPECIALISATION_NAME']; ?></span>

        <div class="section-title">CONTACT</div>
        <p>+91 <?php echo $doctor['PHONE']; ?></p>
        <p><?php echo $doctor['EMAIL']; ?></p>

        <div class="section-title">AVAILABLE DAYS</div>
        <?php while ($row = mysqli_fetch_assoc($schedule_result)) { ?>
            <p>
                <strong><?php echo $row['AVAILABLE_DAY']; ?>:</strong>
                <?php echo substr($row['START_TIME'], 0, 5) . " - " . substr($row['END_TIME'], 0, 5); ?>
            </p>
        <?php } ?>

    </div>
</div>

</body>
</html>



