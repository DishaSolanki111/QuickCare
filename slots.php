<?php
include 'config.php';

$doctor_id = $_GET['doctor_id'];
$date = $_GET['date'];

$q = mysqli_query($conn,"
SELECT SCHEDULE_ID, START_TIME, END_TIME
FROM doctor_schedule_tbl
WHERE DOCTOR_ID=$doctor_id
");

$row = mysqli_fetch_assoc($q);

$start = strtotime($row['START_TIME']);
$end   = strtotime($row['END_TIME']);
?>
<h3>Select Slot</h3>


<?php
while($start < $end){
    $slot = date("H:i",$start);
    ?>
    <a href="login.php?doctor_id=<?= $doctor_id ?>&date=<?= $date ?>&time=<?= $slot ?>&schedule_id=<?= $row['SCHEDULE_ID'] ?>">
        <div class="slot"><?= $slot ?></div>
    </a>
    <?php
    $start = strtotime("+1 hour",$start);
}
?>
