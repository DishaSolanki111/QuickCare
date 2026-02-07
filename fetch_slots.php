<?php
include "config.php";

 $doctor_id = $_POST['doctor_id'] ?? $_GET['doctor_id'] ?? null;
 $date = $_POST['date'] ?? $_GET['date'] ?? null;

if (!$doctor_id || !$date) {
    die("Doctor or date missing");
}

// Convert date to day name (MON, TUE, etc.)
 $day = strtoupper(date('D', strtotime($date)));
// Convert THU to THUR to match database values
 $day = $day === 'THU' ? 'THUR' : $day;

 $q = mysqli_query($conn,"
    SELECT START_TIME, END_TIME, SCHEDULE_ID
    FROM doctor_schedule_tbl
    WHERE DOCTOR_ID=$doctor_id AND AVAILABLE_DAY='$day'
");

if(mysqli_num_rows($q)==0){
    echo "<div class='no-slots-container'><p>No slots available.</p></div>";
    exit;
}

 $row = mysqli_fetch_assoc($q);

 $start = strtotime($row['START_TIME']);
 $end   = strtotime($row['END_TIME']);
 $schedule_id = $row['SCHEDULE_ID'];
?>

<style>
    :root {
        /* Green color scheme */
        --primary-green: #2e7d32;
        --secondary-green: #4caf50;
        --light-green: #e8f5e9;
        --date-light-green: #c8e6c9;
        --medium-green: #81c784;
        --dark-green: #1b5e20;
        --accent-green: #388e3c;
        --selected-dark-green: #0d3d0f;
        --text-dark: #202124;
        --text-light: #ffffff;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --shadow-hover: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .slots-container {
        padding: 24px;
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        background: linear-gradient(to bottom, #ffffff, #f9fafb);
    }

    .slots-container h4 {
        margin-top: 0;
        margin-bottom: 20px;
        font-family: 'Inter', sans-serif;
        color: var(--dark-green);
        font-size: 1.3rem;
        font-weight: 600;
        text-align: center;
        padding-bottom: 12px;
        border-bottom: 2px solid var(--primary-green);
    }

    .date-info {
        text-align: center;
        margin-bottom: 20px;
        font-size: 1rem;
        color: var(--text-dark);
        font-weight: 500;
        padding: 10px;
        background-color: var(--date-light-green);
        border-radius: 8px;
    }

    .slots-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 14px;
    }

    .slot {
        padding: 14px;
        background-color: var(--primary-green);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 16px;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .slot:hover {
        background-color: var(--selected-dark-green);
        transform: translateY(-3px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .slot:active {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .no-slots-container {
        padding: 24px;
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        text-align: center;
        background: linear-gradient(to bottom, #ffffff, #f9fafb);
    }

    .no-slots-container p {
        color: #6b7280;
        font-style: italic;
        padding: 10px;
        background-color: #f9fafb;
        border-radius: 8px;
        border: 1px dashed #d1d5db;
    }

    /* Responsive adjustments */
    @media (max-width: 600px) {
        .slots-container {
            padding: 18px;
        }
        
        .slots-container h4 {
            font-size: 1.1rem;
        }
        
        .slots-grid {
            grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
            gap: 10px;
        }
        
        .slot {
            padding: 12px;
            font-size: 14px;
        }
    }
</style>

<div class="slots-container">
    <h4>Available Time Slots</h4>
    <div class="date-info"><?php echo date("l, F j, Y", strtotime($date)); ?></div>
    
    <div class="slots-grid">
        <?php
        while($start < $end){
            $slot = date("H:i",$start);
            $displayTime = date("h:i A", strtotime($slot));
            $start = strtotime("+1 hour",$start);

            echo "
            <button class='slot'
                onclick=\"confirmSlot('$slot','$schedule_id')\">
                $displayTime
            </button>";
        }
        ?>
    </div>
</div>

<script>
function confirmSlot(time, scheduleId){
    window.location.href =
        "login.php" +
        "?doctor_id=<?= $doctor_id ?>" +
        "&date=<?= $date ?>" +
        "&time=" + time +
        "&schedule_id=" + scheduleId;
}
</script>