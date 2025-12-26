<?php
session_start();
include 'config.php';

if (!isset($_SESSION['PATIENT_ID'])) {
    header("Location: login.php");
    exit;
}

/* REQUIRED DATA */
 $patient_id = $_SESSION['PATIENT_ID'];
 $doctor_id = $_POST['doctor_id'];
 $schedule_id = $_POST['schedule_id'];
 $date = $_POST['date'];
 $time = $_POST['time'];

// Clear the pending appointment from session
unset($_SESSION['PENDING_APPOINTMENT']);

// ✅ INSERT APPOINTMENT ONLY
 $q = "
INSERT INTO appointment_tbl
(PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, CREATED_AT, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS)
VALUES
('$patient_id', '$doctor_id', '$schedule_id', NOW(), '$date', '$time', 'SCHEDULED')
";

if (mysqli_query($conn, $q)) {
    // Get the appointment ID that was just inserted
    $appointment_id = mysqli_insert_id($conn);
    
    // Create a reminder for when appointment is booked
    $booked_remark = "Your appointment has been booked successfully with Dr. " . getDoctorName($conn, $doctor_id) . " on " . date('F d, Y', strtotime($date)) . " at " . date('h:i A', strtotime($time));
    $reminder_time = date('H:i:s'); // Current time
    
    // Insert booking reminder
    $reminder_query = "INSERT INTO appointment_reminder_tbl 
    (RECEPTIONIST_ID, APPOINTMENT_ID, REMINDER_TIME, REMARKS) 
    VALUES 
    (1, '$appointment_id', '$reminder_time', '$booked_remark')";
    
    mysqli_query($conn, $reminder_query);
    
    // Schedule reminders for 24 hours before and 3 hours before
    scheduleAppointmentReminders($conn, $appointment_id, $date, $time, $doctor_id);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Confirmed</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Arial;
            background: #f5f8ff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 50px;
            margin: 0;
        }
        .success-container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            animation: slideIn 0.5s ease-out;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            animation: scaleIn 0.5s ease-out;
        }
        @keyframes scaleIn {
            from {
                transform: scale(0);
            }
            to {
                transform: scale(1);
            }
        }
        .success-icon i {
            color: white;
            font-size: 36px;
        }
        h2 {
            color: #072D44;
            margin-bottom: 15px;
        }
        .appointment-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }
        .appointment-info p {
            margin: 8px 0;
            color: #333;
            font-size: 16px;
        }
        .appointment-info strong {
            color: #072D44;
            display: inline-block;
            width: 100px;
        }
        .note {
            color: #666;
            font-size: 14px;
            margin-top: 20px;
        }
        .close-message {
            font-size: 18px;
            color: #072D44;
            margin-top: 20px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        <h2>Appointment Confirmed!</h2>
        
        <div class="appointment-info">
            <p><strong>Date:</strong> <?php echo date('F d, Y', strtotime($date)); ?></p>
            <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($time)); ?></p>
            <p><strong>Status:</strong> Scheduled</p>
        </div>
        
        <p class="note">Your appointment has been successfully booked. You will receive reminders before your appointment.</p>
        
        <p class="close-message">This window will close automatically...</p>
    </div>

    <script>
        // Simply close the popup after showing confirmation
        setTimeout(function() {
            // Update the message
            document.querySelector('.close-message').textContent = 'Closing window...';
            
            // Close the popup
            setTimeout(function() {
                try {
                    // Try to close the parent window (calendar modal)
                    if (window.parent && window.parent !== window) {
                        // We're in an iframe, close the parent modal
                        window.parent.closeCalendar();
                        
                        // Also refresh the parent page to show the updated appointment
                        window.parent.location.reload();
                    } else if (window.opener) {
                        // If opened in a popup, close it
                        window.close();
                    } else {
                        // If not a popup, you can redirect if needed
                        window.location.href = 'patient.php';
                    }
                } catch (e) {
                    console.error('Error closing window:', e);
                    // Fallback to redirect
                    window.location.href = 'patient.php';
                }
            }, 2000);
        }, 1000);
    </script>
</body>
</html>
<?php
} else {
    // Display error message
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Booking Failed</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            body {
                font-family: Arial;
                background: #f5f8ff;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                padding: 50px;
            }
            .error-container {
                background: white;
                padding: 40px;
                border-radius: 12px;
                text-align: center;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
                max-width: 500px;
            }
            .error-icon {
                width: 80px;
                height: 80px;
                background: #dc3545;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 20px;
            }
            .error-icon i {
                color: white;
                font-size: 36px;
            }
            h2 {
                color: #072D44;
                margin-bottom: 15px;
            }
            .btn {
                display: inline-block;
                background: #072D44;
                color: white;
                padding: 12px 30px;
                border-radius: 5px;
                text-decoration: none;
                margin-top: 20px;
                transition: background 0.3s;
                cursor: pointer;
                border: none;
                font-size: 16px;
            }
            .btn:hover {
                background: #064469;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-icon">
                <i class="fas fa-times"></i>
            </div>
            <h2>Booking Failed</h2>
            <p>There was an error booking your appointment. Please try again.</p>
            <button class="btn" onclick="closeWindow()">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
        
        <script>
            function closeWindow() {
                try {
                    // Try to close the parent window (calendar modal)
                    if (window.parent && window.parent !== window) {
                        // We're in an iframe, close the parent modal
                        window.parent.closeCalendar();
                    } else if (window.opener) {
                        // If opened in a popup, close it
                        window.close();
                    } else {
                        // If not a popup, you can redirect if needed
                        window.location.href = 'patient.php';
                    }
                } catch (e) {
                    console.error('Error closing window:', e);
                    // Fallback to redirect
                    window.location.href = 'patient.php';
                }
            }
        </script>
    </body>
    </html>
    <?php
}

// Helper function to get doctor name
function getDoctorName($conn, $doctor_id) {
    $query = "SELECT FIRST_NAME, LAST_NAME FROM doctor_tbl WHERE DOCTOR_ID = $doctor_id";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        return "Dr. " . $row['FIRST_NAME'] . " " . $row['LAST_NAME'];
    }
    return "Doctor";
}

// Function to schedule future reminders
function scheduleAppointmentReminders($conn, $appointment_id, $date, $time, $doctor_id) {
    $doctor_name = getDoctorName($conn, $doctor_id);
    $appointment_datetime = new DateTime($date . ' ' . $time);
    
    // Schedule 24 hours before reminder
    $day_before = clone $appointment_datetime;
    $day_before->sub(new DateInterval('P1D')); // Subtract 1 day
    
    // Only schedule if it's in the future
    $now = new DateTime();
    if ($day_before > $now) {
        $day_before_time = $day_before->format('H:i:s');
        $day_before_remark = "Reminder: You have an appointment with $doctor_name tomorrow at " . $appointment_datetime->format('h:i A') . " on " . $appointment_datetime->format('F d, Y');
        
        $reminder_query = "INSERT INTO appointment_reminder_tbl 
        (RECEPTIONIST_ID, APPOINTMENT_ID, REMINDER_TIME, REMARKS) 
        VALUES 
        (1, '$appointment_id', '$day_before_time', '$day_before_remark')";
        
        mysqli_query($conn, $reminder_query);
    }
    
    // Schedule 3 hours before reminder
    $hours_before = clone $appointment_datetime;
    $hours_before->sub(new DateInterval('PT3H')); // Subtract 3 hours
    
    // Only schedule if it's in the future
    if ($hours_before > $now) {
        $hours_before_time = $hours_before->format('H:i:s');
        $hours_before_remark = "Reminder: You have an appointment with $doctor_name in 3 hours at " . $appointment_datetime->format('h:i A') . " today";
        
        $reminder_query = "INSERT INTO appointment_reminder_tbl 
        (RECEPTIONIST_ID, APPOINTMENT_ID, REMINDER_TIME, REMARKS) 
        VALUES 
        (1, '$appointment_id', '$hours_before_time', '$hours_before_remark')";
        
        mysqli_query($conn, $reminder_query);
    }
}
?>