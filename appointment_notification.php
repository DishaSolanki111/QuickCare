<?php
// // Use the same session handling as doctor_header.php
// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
//}

include 'config.php';

// // Use the same session variable name as in doctor_header.php
//  $doctor_id = $_SESSION['DOCTOR_ID'] ?? 0;

 $sql = "
SELECT 
    a.APPOINTMENT_ID,
    p.FIRST_NAME AS patient_name,
    TIMESTAMP(a.APPOINTMENT_DATE, a.APPOINTMENT_TIME) AS appointment_datetime
FROM appointment_tbl a
JOIN patient_tbl p ON a.PATIENT_ID = p.PATIENT_ID
WHERE a.DOCTOR_ID = ?
AND a.STATUS = 'APPROVED'
AND TIMESTAMP(a.APPOINTMENT_DATE, a.APPOINTMENT_TIME)
    BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
ORDER BY appointment_datetime ASC
";

 $stmt = $conn->prepare($sql);
 $stmt->bind_param("i", $doctor_id);
 $stmt->execute();
 $result = $stmt->get_result();

 $appointments = $result->fetch_all(MYSQLI_ASSOC);
 $reminderCount = count($appointments);
?>

<div class="qc-notification">
    <i class="fa fa-bell" id="qcBell"></i>

    <?php if ($reminderCount > 0): ?>
        <span class="qc-badge"><?= $reminderCount ?></span>
    <?php endif; ?>

    <div class="qc-dropdown" id="qcDropdown">
        <h4>Upcoming Appointments</h4>

        <?php if ($reminderCount === 0): ?>
            <p class="qc-empty">No upcoming appointments</p>
        <?php else: ?>
            <?php foreach ($appointments as $row): ?>
                <div class="qc-item">
                    <strong><?= htmlspecialchars($row['patient_name']) ?></strong>
                    <span>
                        <?= date("d M Y, h:i A", strtotime($row['appointment_datetime'])) ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById("qcBell").onclick = function () {
    const dropdown = document.getElementById("qcDropdown");
    dropdown.style.display = dropdown.style.display === "block" ? "none" : "block";
};
</script>

<style>
/* Put this in a main CSS file if you want globally */
.qc-notification {
    position: relative;
    cursor: pointer;
    margin-right: 20px;
}
.qc-notification i { font-size: 22px; color: #0066cc; }
.qc-badge {
    position: absolute;
    top: -6px;
    right: -8px;
    background: red;
    color: #fff;
    font-size: 11px;
    padding: 3px 6px;
    border-radius: 50%;
}
.qc-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 30px;
    width: 280px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    padding: 12px;
    z-index: 999;
}
.qc-dropdown h4 { margin-bottom: 10px; font-size: 16px; }
.qc-item { padding: 8px; border-bottom: 1px solid #eee; }
.qc-item span { display: block; font-size: 13px; color: #666; }
.qc-empty { text-align: center; color: #888; }
</style>