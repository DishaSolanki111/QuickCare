<?php
include "config.php";

 $doctor_id = $_GET['doctor_id'] ?? null;
 $date = $_GET['date'] ?? null;

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
        /* Blue color scheme */
        --primary-blue: #1a73e8;
        --secondary-blue: #4285f4;
        --light-blue: #e8f0fe;
        --medium-blue: #8ab4f8;
        --dark-blue: #174ea6;
        --accent-blue: #0b57d0;
        --text-dark: #202124;
        --text-light: #ffffff;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --shadow-hover: 0 10px 20px rgba(0, 0, 0, 0.15);
    }

    .slots-container {
        padding: 20px;
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .slots-container h4 {
        margin-top: 0;
        margin-bottom: 20px;
        font-family: 'Inter', sans-serif;
        color: var(--dark-blue);
        font-size: 1.2rem;
        font-weight: 600;
        text-align: center;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--primary-blue);
    }

    .date-info {
        text-align: center;
        margin-bottom: 15px;
        font-size: 1rem;
        color: var(--text-dark);
        font-weight: 500;
    }

    .slots-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 12px;
    }

    .slot {
        padding: 12px;
        background-color: var(--primary-blue);
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 16px;
        text-align: center;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .slot:hover {
        background-color: var(--accent-blue);
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .slot:active {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .no-slots-container {
        padding: 20px;
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .no-slots-container p {
        color: #666;
        font-style: italic;
    }

    /* Responsive adjustments */
    @media (max-width: 600px) {
        .slots-container {
            padding: 15px;
        }
        
        .slots-container h4 {
            font-size: 1.1rem;
        }
        
        .slots-grid {
            grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            gap: 8px;
        }
        
        .slot {
            padding: 10px;
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