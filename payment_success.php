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
 $date        = $_POST['date'];
 $time        = $_POST['time'];

// Clear the pending appointment from session
unset($_SESSION['PENDING_APPOINTMENT']);

// ✅ INSERT APPOINTMENT ONLY
 $q = "
INSERT INTO appointment_tbl
(PATIENT_ID, DOCTOR_ID, SCHEDULE_ID, CREATED_AT, APPOINTMENT_DATE, APPOINTMENT_TIME, STATUS)
VALUES
('$patient_id', '$doctor_id', '$schedule_id', NOW(), '$date', '$time', 'scheduled')
";

if (mysqli_query($conn, $q)) {
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
        .note {
            color: #666;
            font-size: 14px;
            margin-top: 20px;
        }
        .countdown {
            font-size: 18px;
            color: #072D44;
            margin: 15px 0;
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
        
        <div class="countdown" id="countdown">Redirecting to home page...</div>
        
        <p class="note">Your appointment has been successfully booked.</p>
    </div>

    <script>
        
    window.onload = function () {
    let countdownEl = document.getElementById('countdown');
    let dots = 0;

    const loadingInterval = setInterval(function () {
        dots = (dots + 1) % 4;
        countdownEl.textContent = 'Redirecting to home page' + '.'.repeat(dots);
    }, 500);

    setTimeout(function () {
        clearInterval(loadingInterval);

        // 🔥 Tell parent to close modal
        window.parent.postMessage({
            action: "APPOINTMENT_SUCCESS"
        }, "*");

    }, 2000);
};

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
            <button class="btn" onclick="window.close()">
                <i class="fas fa-times"></i> Close
            </button>
        </div>
    </body>
    </html>
    <?php
}
?>