<?php
include 'config.php';

// --- Database Logic (Unchanged) ---
 $doctor_id = $_GET['doctor_id'];
 $date = $_GET['date'];

// It's better to select by schedule_id for a specific date if possible,
// but sticking to your original logic for now.
 $q = mysqli_query($conn,"
    SELECT SCHEDULE_ID, START_TIME, END_TIME
    FROM doctor_schedule_tbl
    WHERE DOCTOR_ID=$doctor_id
");

// Check if the doctor has a schedule
if (mysqli_num_rows($q) > 0) {
    $row = mysqli_fetch_assoc($q);
    $start = strtotime($row['START_TIME']);
    $end   = strtotime($row['END_TIME']);
} else {
    // Handle case where no schedule is found
    $start = $end = 0;
}
?>

<!-- CSS for Styling the Slots -->
<style>
    /* Main container for the slot selection area */
    .slot-selection-container {
        margin-top: 20px;
        padding: 20px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background-color: #f9f9f9;
    }

    .slot-selection-container h3 {
        margin-top: 0;
        margin-bottom: 20px;
        font-family: Arial, sans-serif;
        color: #333;
        border-bottom: 2px solid #007bff;
        padding-bottom: 10px;
    }

    /* Flex container for the buttons to align them nicely */
    .slots-container {
        display: flex;
        flex-wrap: wrap; /* Allows buttons to wrap to the next line */
        gap: 12px; /* The space between the buttons */
    }

    /* Styling for each individual slot button */
    .slot {
        padding: 12px 20px; /* Makes the button bigger and more clickable */
        font-size: 16px;
        font-weight: bold;
        color: #fff;
        background-color: #47bb7dff; /* A pleasant blue color */
        border: none;
        border-radius: 6px; /* Rounded corners */
        cursor: pointer;
        transition: all 0.2s ease-in-out; /* Smooth animation for hover effects */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
    }

    /* Hover effect for the slot button */
    .slot:hover {
        background-color: #17852d; /* A darker blue on hover */
        transform: translateY(-2px); /* Slightly lifts the button up */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15); /* Enhances the shadow on hover */
    }

    /* Message for when no slots are available */
    .no-slots-message {
        font-family: Arial, sans-serif;
        color: #777;
        font-style: italic;
    }
</style>


<div class="slot-selection-container">
    <h3>Select Slot for <?php echo date("l, F j, Y", strtotime($date)); ?></h3>
    
    <?php if ($start < $end): ?>
        <div class="slots-container">
            <?php
            // Loop to generate the time slot buttons
            while($start < $end){
                $slot = date("H:i", $start);
                // Construct the URL for the redirect
                $redirectUrl = "login.php?doctor_id=" . urlencode($doctor_id) . "&date=" . urlencode($date) . "&time=" . urlencode($slot) . "&schedule_id=" . urlencode($row['SCHEDULE_ID']);
                ?>
                <button class="slot" onclick="window.location.href='<?= $redirectUrl ?>'">
                    <?= $slot ?>
                </button>
                <?php
                // Increment time by one hour
                $start = strtotime("+1 hour", $start);
            }
            ?>
        </div>
    <?php else: ?>
        <p class="no-slots-message">No slots available for this date.</p>
    <?php endif; ?>
</div>