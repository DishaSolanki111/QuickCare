<?php
session_start();

if (!isset($_SESSION['PATIENT_ID'])) {
    header("Location: login.php");
    exit;
}

// Check if there's a pending appointment in session
if (!isset($_SESSION['PENDING_APPOINTMENT'])) {
    header("Location: patient.php");
    exit;
}

 $appointment = $_SESSION['PENDING_APPOINTMENT'];
 $doctor_id = $appointment['doctor_id'];
 $date = $appointment['date'];
 $time = $appointment['time'];
 $schedule_id = $appointment['schedule_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Appointment</title>
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
        .box {
            background: white;
            padding: 40px;
            width: 400px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #072D44;
            margin-bottom: 20px;
        }
        .appointment-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: left;
        }
        .appointment-details p {
            margin: 8px 0;
            color: #333;
            font-size: 16px;
        }
        .appointment-details strong {
            color: #072D44;
            display: inline-block;
            width: 80px;
        }
        .amount {
            font-size: 32px;
            color: #28a745;
            font-weight: bold;
            margin: 20px 0;
        }
        button {
            background: #28a745;
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        button:hover {
            background: #218838;
            transform: translateY(-2px);
        }
        button:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
        }
        .spinner {
            display: none;
            margin-right: 10px;
        }
        .note {
            color: #666;
            font-size: 14px;
            margin-top: 15px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="box">
        <h2>Confirm Appointment</h2>
        
        <div class="appointment-details">
            <p><strong>Date:</strong> <?php echo date('F d, Y', strtotime($date)); ?></p>
            <p><strong>Time:</strong> <?php echo date('h:i A', strtotime($time)); ?></p>
            <p><strong>Amount:</strong> ₹300</p>
        </div>
        
        <form action="payment_success.php" method="post" id="confirmForm">
            <input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">
            <input type="hidden" name="date" value="<?= $date ?>">
            <input type="hidden" name="time" value="<?= $time ?>">
            <input type="hidden" name="schedule_id" value="<?= $schedule_id ?>">
            
            <button type="submit" id="confirmBtn">
                <i class="fas fa-spinner fa-spin spinner" id="spinner"></i>
                <span id="btnText">Confirm Appointment</span>
            </button>
        </form>
        
        <p class="note">Click to confirm your appointment booking</p>
    </div>

    <script>
        document.getElementById('confirmForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const button = document.getElementById('confirmBtn');
            const spinner = document.getElementById('spinner');
            const btnText = document.getElementById('btnText');
            
            // Show loading state
            button.disabled = true;
            spinner.style.display = 'inline-block';
            btnText.textContent = 'Confirming...';
            
            // Simulate processing
            setTimeout(() => {
                this.submit();
            }, 1500);
        });
    </script>
</body>
</html>