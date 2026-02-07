<?php
include 'config.php';

// --- Database Logic (Unchanged) ---
$doctor_id = $_POST['doctor_id'] ?? $_GET['doctor_id'];
$date = $_POST['date'] ?? $_GET['date'];

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

    /* Main container for the slot selection area */
    .slot-selection-container {
        padding: 24px;
        border-top: 1px solid #e5e7eb;
        background: linear-gradient(to bottom, #ffffff, #f9fafb);
    }

    .slot-selection-container h3 {
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

    /* Flex container for the buttons to align them nicely */
    .slots-container {
        display: flex;
        flex-wrap: wrap; /* Allows buttons to wrap to the next line */
        gap: 14px; /* The space between the buttons */
        justify-content: center;
    }

    /* Styling for each individual slot button */
    .slot {
        padding: 14px 22px; /* Makes the button bigger and more clickable */
        font-size: 16px;
        font-weight: 600;
        color: white;
        background-color: var(--primary-green); /* A pleasant green color */
        border: none;
        border-radius: 8px; /* Rounded corners */
        cursor: pointer;
        transition: all 0.3s ease; /* Smooth animation for hover effects */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
        position: relative;
        overflow: hidden;
    }

    /* Hover effect for the slot button */
    .slot:hover {
        background-color: var(--selected-dark-green); /* A darker green on hover */
        transform: translateY(-3px); /* Slightly lifts the button up */
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); /* Enhances the shadow on hover */
    }

    .slot:active {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Message for when no slots are available */
    .no-slots-message {
        font-family: 'Inter', sans-serif;
        color: #6b7280;
        font-style: italic;
        text-align: center;
        padding: 24px;
        background-color: #f9fafb;
        border-radius: 8px;
        border: 1px dashed #d1d5db;
    }

    /* Responsive adjustments */
    @media (max-width: 600px) {
        .slot-selection-container {
            padding: 18px;
        }
        
        .slot-selection-container h3 {
            font-size: 1.1rem;
        }
        
        .slot {
            padding: 12px 18px;
            font-size: 14px;
        }
        
        .slots-container {
            gap: 10px;
        }
    }
</style>

<div class="slot-selection-container">
    <h3>Select Time Slot</h3>
    <div class="date-info"><?php echo date("l, F j, Y", strtotime($date)); ?></div>
    
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
                    <?= date("h:i A", strtotime($slot)) ?>
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