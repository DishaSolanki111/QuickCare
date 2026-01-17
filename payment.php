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
 $doctor_name = $appointment['doctor_name'];
 $specialization = $appointment['specialization'];
 $date = $appointment['date'];
 $time = $appointment['time'];
 $reason = $appointment['reason'];

// Fetch doctor details for display
include "config.php";
 $doctor_query = mysqli_query($conn, "SELECT FIRST_NAME, LAST_NAME FROM doctor_tbl WHERE DOCTOR_ID = $doctor_id");
 $doctor = mysqli_fetch_assoc($doctor_query);
 $doctor_full_name = $doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Appointment</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
            --success-color: #28a745;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f8ff 0%, #e6f0ff 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .payment-container {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }
        
        .payment-header {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
            color: white;
            padding: 25px 20px;
            text-align: center;
        }
        
        .payment-header h2 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        
        .payment-content {
            padding: 30px;
        }
        
        .appointment-details {
            background: var(--light-blue);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        
        .appointment-details h3 {
            color: var(--dark-blue);
            margin-bottom: 15px;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .appointment-details p {
            margin: 10px 0;
            color: var(--text-dark);
            font-size: 1rem;
            display: flex;
            justify-content: space-between;
        }
        
        .appointment-details strong {
            color: var(--primary-blue);
        }
        
        .amount-container {
            text-align: center;
            margin: 25px 0;
        }
        
        .amount-label {
            font-size: 1rem;
            color: var(--text-dark);
            margin-bottom: 5px;
        }
        
        .amount {
            font-size: 2.5rem;
            color: var(--success-color);
            font-weight: 700;
        }
        
        .payment-methods {
            margin-bottom: 25px;
        }
        
        .payment-methods h3 {
            color: var(--dark-blue);
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .payment-options {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        
        .payment-option {
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .payment-option:hover, .payment-option.selected {
            border-color: var(--primary-blue);
            background: var(--light-blue);
        }
        
        .payment-option i {
            font-size: 1.5rem;
            color: var(--primary-blue);
            margin-bottom: 5px;
        }
        
        .payment-option span {
            display: block;
            font-size: 0.9rem;
            color: var(--text-dark);
        }
        
        .confirm-btn {
            width: 100%;
            padding: 15px;
            background: var(--success-color);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .confirm-btn:hover {
            background: #218838;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .confirm-btn:disabled {
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
            font-size: 0.9rem;
            margin-top: 15px;
            text-align: center;
            font-style: italic;
        }
        
        /* Responsive adjustments */
        @media (max-width: 500px) {
            .payment-container {
                max-width: 100%;
            }
            
            .payment-content {
                padding: 20px;
            }
            
            .payment-options {
                grid-template-columns: 1fr;
            }
            
            .amount {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <h2>Confirm Appointment</h2>
            <p>Complete your booking by making the payment</p>
        </div>
        
        <div class="payment-content">
            <div class="appointment-details">
                <h3><i class="fas fa-calendar-check"></i> Appointment Details</h3>
                <p>
                    <span>Doctor:</span>
                    <strong>Dr. <?php echo htmlspecialchars($doctor_full_name); ?></strong>
                </p>
                <p>
                    <span>Specialization:</span>
                    <strong><?php echo htmlspecialchars($specialization); ?></strong>
                </p>
                <p>
                    <span>Date:</span>
                    <strong><?php echo date('F d, Y', strtotime($date)); ?></strong>
                </p>
                <p>
                    <span>Time:</span>
                    <strong><?php echo date('h:i A', strtotime($time)); ?></strong>
                </p>
                <p>
                    <span>Reason:</span>
                    <strong><?php echo htmlspecialchars($reason); ?></strong>
                </p>
                <p>
                    <span>Consultation Fee:</span>
                    <strong>₹300</strong>
                </p>
            </div>
            
            <div class="amount-container">
                <div class="amount-label">Total Amount</div>
                <div class="amount">₹300</div>
            </div>
            
            <div class="payment-methods">
                <h3>Select Payment Method</h3>
                <div class="payment-options">
                    <div class="payment-option selected" onclick="selectPayment(this)">
                        <i class="fas fa-credit-card"></i>
                        <span>Credit Card</span>
                    </div>
                    <div class="payment-option" onclick="selectPayment(this)">
                        <i class="fab fa-google-pay"></i>
                        <span>Google Pay</span>
                    </div>
                    <div class="payment-option" onclick="selectPayment(this)">
                        <i class="fas fa-mobile-alt"></i>
                        <span>UPI</span>
                    </div>
                    <div class="payment-option" onclick="selectPayment(this)">
                        <i class="fas fa-university"></i>
                        <span>Net Banking</span>
                    </div>
                </div>
            </div>
            
            <form action="payment_success.php" method="post" id="confirmForm">
                <input type="hidden" name="doctor_id" value="<?= $doctor_id ?>">
                <input type="hidden" name="date" value="<?= $date ?>">
                <input type="hidden" name="time" value="<?= $time ?>">
                <input type="hidden" name="reason" value="<?= $reason ?>">
                <input type="hidden" name="payment_method" id="paymentMethod" value="CREDIT CARD">
                
                <button type="submit" class="confirm-btn" id="confirmBtn">
                    <i class="fas fa-spinner fa-spin spinner" id="spinner"></i>
                    <span id="btnText">Confirm and Pay</span>
                </button>
            </form>
            
            <p class="note">Your appointment will be confirmed after successful payment</p>
        </div>
    </div>

    <script>
        function selectPayment(element) {
            // Remove selected class from all options
            document.querySelectorAll('.payment-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Add selected class to clicked option
            element.classList.add('selected');
            
            // Update hidden input value
            const paymentMethod = element.querySelector('span').textContent;
            document.getElementById('paymentMethod').value = paymentMethod;
        }
        
        document.getElementById('confirmForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const button = document.getElementById('confirmBtn');
            const spinner = document.getElementById('spinner');
            const btnText = document.getElementById('btnText');
            
            // Show loading state
            button.disabled = true;
            spinner.style.display = 'inline-block';
            btnText.textContent = 'Processing...';
            
            // Simulate processing
            setTimeout(() => {
                this.submit();
            }, 1500);
        });
    </script>
</body>
</html>