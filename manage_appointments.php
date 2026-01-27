<?php
include 'config.php';

// Get today's date in YYYY-MM-DD format
 $today = date('Y-m-d');
 $formattedToday = date('d-m-Y', strtotime($today));

// Fetch specializations for the dropdown
 $specializations = [];
 $sql = "SELECT SPECIALISATION_ID, SPECIALISATION_NAME FROM specialisation_tbl ORDER BY SPECIALISATION_NAME";
 $result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $specializations[] = $row;
    }
}
 $conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quick Care - Doctor Appointment Booking</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-color: #4a6fdc;
            --secondary-color: #f8f9fa;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --light-color: #e9ecef;
            --dark-color: #343a40;
            --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 8px 15px rgba(0, 0, 0, 0.1);
            --mid-blue: #064469;
            --soft-blue: #5790AB;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

body {
    margin: 0;
    background: #f5f7fb;
}

/* Sidebar already fixed */
.sidebar {
    width: 250px;
    position: fixed;
    left: 0;
    top: 0;
    height: 100vh;
}

/* Main content next to sidebar */
.main {
    margin-left: 200px;
    padding: 25px;
    width: calc(100% - 250px);
    min-height: 100vh;
}
        .container {
            max-width: 3000px;            
        }

        .filters {
            background-color: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: var(--shadow);
            margin: 2rem 0;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
        }

        .filter-group input,
        .filter-group select {
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .date-display {
            background-color: var(--secondary-color);
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: 600;
            color: var(--primary-color);
            font-size: 40px;
        }

        .doctors-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .doctor-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .doctor-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .doctor-header {
            display: flex;
            padding: 1.5rem;
            background-color: var(--secondary-color);
            border-bottom: 1px solid #eee;
        }

        .doctor-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 1rem;
            border: 3px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .doctor-info h3 {
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .doctor-specialization {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .doctor-schedule {
            padding: 1.5rem;
        }

        .schedule-title {
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark-color);
        }

        .time-slots {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
            gap: 0.5rem;
        }

        .time-slot {
            padding: 0.5rem;
            text-align: center;
            border-radius: 5px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
            position: relative;
        }

        .time-slot:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .available {
            background-color: var(--success-color);
            color: white;
        }

        .booked {
            background-color: var(--danger-color);
            color: white;
            cursor: not-allowed;
        }

        .unavailable {
            background-color: var(--light-color);
            color: #6c757d;
            cursor: not-allowed;
        }

        .selected {
            background-color: var(--mid-blue);
            color: white;
            transform: scale(1.05);
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        .tooltip {
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--dark-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
            z-index: 10;
        }

        .time-slot:hover .tooltip {
            opacity: 1;
        }

        .no-doctors {
            text-align: center;
            padding: 2rem;
            background-color: white;
            border-radius: 10px;
            box-shadow: var(--shadow);
            margin: 2rem 0;
        }

        .loading {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid var(--light-color);
            border-top: 5px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .btn-book {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s;
            margin-top: 1rem;
            display: block;
            width: 100%;
        }

        .btn-book:hover {
            background-color: #3a5bc9;
        }

        .btn-book:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }

        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .footer-section {
            flex: 1;
            min-width: 200px;
            margin-bottom: 1rem;
        }

        .footer-section h3 {
            margin-bottom: 1rem;
            color: var(--primary-color);
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-section ul li {
            margin-bottom: 0.5rem;
        }

        .footer-section a {
            color: #ddd;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-section a:hover {
            color: var(--primary-color);
        }

        .copyright {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #444;
            font-size: 0.9rem;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 2% auto;
            padding: 0;
            border: none;
            width: 90%;
            max-width: 900px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            max-height: 95vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            background: linear-gradient(135deg, var(--mid-blue) 0%, var(--soft-blue) 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
        }

        .close {
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .close:hover {
            transform: rotate(90deg);
        }

        .modal-body {
            padding: 20px;
            overflow-y: auto;
            flex-grow: 1;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .step {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #ddd;
            margin: 0 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .step.active {
            background-color: var(--primary-color);
        }

        .step.completed {
            background-color: var(--success-color);
        }

        .step-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .step-content.active {
            display: block;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-color);
        }

        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(74, 111, 220, 0.2);
        }

        .booking-summary {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .booking-summary h4 {
            color: var(--primary-color);
            margin-bottom: 10px;
            text-align: center;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #eee;
        }

        .summary-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .summary-row.total {
            font-weight: bold;
            color: var(--success-color);
            font-size: 1.1rem;
        }

        .payment-method {
            display: flex;
            align-items: center;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            background-color: #f8f9fa;
            border-color: var(--primary-color);
        }

        .payment-method.selected {
            background-color: rgba(74, 111, 220, 0.1);
            border-color: var(--primary-color);
        }

        .payment-method i {
            margin-right: 10px;
            color: var(--primary-color);
        }

        .payment-processing {
            text-align: center;
            padding: 40px 20px;
        }

        .payment-icon {
            font-size: 4rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .payment-title {
            font-size: 1.5rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .payment-message {
            color: #666;
            margin-bottom: 30px;
        }

        .razorpay-button {
            background: #3399cc;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .razorpay-button:hover {
            background: #2277bb;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .success-container {
            text-align: center;
            padding: 40px 20px;
        }

        .success-icon {
            font-size: 5rem;
            color: var(--success-color);
            margin-bottom: 20px;
            animation: successPulse 1s ease-in-out;
        }

        @keyframes successPulse {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }

        .success-title {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 15px;
        }

        .success-message {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1rem;
        }

        .appointment-details-card {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px auto;
            max-width: 500px;
            text-align: left;
        }

        .appointment-details-card h4 {
            color: var(--primary-color);
            margin-bottom: 15px;
            text-align: center;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        .btn-primary {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #3a5bc9;
        }

        .btn-success {
            background-color: var(--success-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        @media (max-width: 768px) {
            .doctors-container {
                grid-template-columns: 1fr;
            }
            
            .filters {
                flex-direction: column;
            }
            
            .filter-group {
                width: 100%;
            }

            .modal-content {
                width: 95%;
                margin: 5% auto;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- LEFT SIDEBAR -->
    <?php include 'patient_sidebar.php'; ?>

    <!-- RIGHT MAIN CONTENT -->
    <div class="main">

        <div class="filters">
            <div class="filter-group">
                <label>Select Date</label>
                <input type="date" id="date-picker"
                       value="<?php echo $today; ?>"
                       min="<?php echo $today; ?>">
            </div>

            <div class="filter-group">
                <label>Specialization</label>
                <select id="specialization">
                    <option value="0">All Specializations</option>
                    <?php foreach ($specializations as $spec): ?>
                        <option value="<?= $spec['SPECIALISATION_ID']; ?>">
                            <?= $spec['SPECIALISATION_NAME']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>&nbsp;</label>
                <button id="filter-btn" class="btn-book">Search</button>
            </div>
        </div>

        <div class="date-display" id="date-display">
            Schedule for: <?= $formattedToday; ?>
        </div>

        <div id="doctors-container" class="doctors-container">
            <div class="loading">
                <div class="spinner"></div>
            </div>
        </div>

    </div>
</div>

<!-- Booking Modal -->
<div id="bookingModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Book Appointment</h2>
            <span class="close" onclick="closeBookingModal()">&times;</span>
        </div>
        <div class="modal-body">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step active" id="step1">1</div>
                <div class="step" id="step2">2</div>
                <div class="step" id="step3">3</div>
                <div class="step" id="step4">4</div>
            </div>
            
            <!-- Step 1: Appointment Details -->
            <div class="step-content active" id="step1Content">
                <div class="form-group">
                    <label>Doctor</label>
                    <div class="form-control" id="doctor_name" style="background-color: #f8f9fa;"></div>
                </div>
                
                <div class="form-group">
                    <label>Specialization</label>
                    <div class="form-control" id="doctor_specialization" style="background-color: #f8f9fa;"></div>
                </div>
                
                <div class="form-group">
                    <label>Date</label>
                    <div class="form-control" id="appointment_date" style="background-color: #f8f9fa;"></div>
                </div>
                
                <div class="form-group">
                    <label>Time</label>
                    <div class="form-control" id="appointment_time" style="background-color: #f8f9fa;"></div>
                </div>
                
                <div class="form-group">
                    <label for="reason">Reason for Visit</label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Please describe your symptoms or reason for the appointment"></textarea>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn-primary" onclick="nextStep(2)">
                        Next: Payment <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                    </button>
                </div>
            </div>
            
            <!-- Step 2: Payment Method -->
            <div class="step-content" id="step2Content">
                <div class="form-group">
                    <label>Payment Method</label>
                    <div class="payment-method" onclick="selectPaymentMethod(this, 'razorpay')">
                        <i class="fas fa-credit-card"></i>
                        <span>Pay with Razorpay (Online Payment)</span>
                    </div>
                    <input type="hidden" id="selected_payment_method" name="payment_method" value="razorpay" required>
                </div>
                
                <div class="booking-summary">
                    <h4>Appointment Summary</h4>
                    <div class="summary-row">
                        <span>Doctor:</span>
                        <span id="summary_doctor">-</span>
                    </div>
                    <div class="summary-row">
                        <span>Specialization:</span>
                        <span id="summary_specialization">-</span>
                    </div>
                    <div class="summary-row">
                        <span>Date:</span>
                        <span id="summary_date">-</span>
                    </div>
                    <div class="summary-row">
                        <span>Time:</span>
                        <span id="summary_time">-</span>
                    </div>
                    <div class="summary-row total">
                        <span>Consultation Fee:</span>
                        <span>₹300</span>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="button" class="btn-primary" onclick="prevStep(1)">
                        <i class="fas fa-arrow-left" style="margin-right: 5px;"></i> Back
                    </button>
                    <button type="button" class="btn-primary" onclick="proceedToPayment()" id="proceedToPaymentBtn">
                        Proceed to Payment <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                    </button>
                </div>
            </div>
            
            <!-- Step 3: Payment Processing -->
            <div class="step-content" id="step3Content">
                <div class="payment-processing">
                    <i class="fas fa-lock payment-icon"></i>
                    <h3 class="payment-title">Secure Payment</h3>
                    <p class="payment-message">Complete your payment to confirm the appointment</p>
                    
                    <div class="booking-summary">
                        <h4>Appointment Summary</h4>
                        <div class="summary-row">
                            <span>Doctor:</span>
                            <span id="summary_doctor_payment">-</span>
                        </div>
                        <div class="summary-row">
                            <span>Specialization:</span>
                            <span id="summary_specialization_payment">-</span>
                        </div>
                        <div class="summary-row">
                            <span>Date:</span>
                            <span id="summary_date_payment">-</span>
                        </div>
                        <div class="summary-row">
                            <span>Time:</span>
                            <span id="summary_time_payment">-</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total Amount:</span>
                            <span>₹300</span>
                        </div>
                    </div>
                    
                    <button class="razorpay-button" id="rzp-button">
                        <i class="fas fa-credit-card"></i> Pay with Razorpay
                    </button>
                </div>
            </div>
            
            <!-- Step 4: Payment Success -->
            <div class="step-content" id="step4Content">
                <div class="success-container">
                    <i class="fas fa-check-circle success-icon"></i>
                    <h2 class="success-title">Payment Successful!</h2>
                    <p class="success-message">Your appointment has been booked successfully</p>
                    
                    <div class="appointment-details-card">
                        <h4>Appointment Details</h4>
                        <div class="summary-row">
                            <span>Appointment ID:</span>
                            <span id="appointment_id_display">-</span>
                        </div>
                        <div class="summary-row">
                            <span>Doctor:</span>
                            <span id="summary_doctor_success">-</span>
                        </div>
                        <div class="summary-row">
                            <span>Specialization:</span>
                            <span id="summary_specialization_success">-</span>
                        </div>
                        <div class="summary-row">
                            <span>Date:</span>
                            <span id="summary_date_success">-</span>
                        </div>
                        <div class="summary-row">
                            <span>Time:</span>
                            <span id="summary_time_success">-</span>
                        </div>
                        <div class="summary-row">
                            <span>Payment Status:</span>
                            <span style="color: var(--success-color); font-weight: bold;">Paid</span>
                        </div>
                    </div>
                    
                    <div class="btn-group">
                        <button type="button" class="btn-primary" onclick="downloadReceipt()">
                            <i class="fas fa-download"></i> Download Receipt
                        </button>
                        <button type="button" class="btn-success" onclick="closeBookingModal()">
                            <i class="fas fa-home"></i> Go to Dashboard
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Razorpay Script -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    // Global variables for appointment details
    let selectedDoctor = {
        id: null,
        name: null,
        specialization: null
    };
    let selectedDate = null;
    let selectedTime = null;

    document.addEventListener('DOMContentLoaded', function() {
        // Initial load of doctor schedules
        loadDoctorSchedules();

        // Event listeners for filters
        document.getElementById('date-picker').addEventListener('change', function() {
            updateDateDisplay();
            loadDoctorSchedules();
        });

        document.getElementById('specialization').addEventListener('change', loadDoctorSchedules);
        document.getElementById('filter-btn').addEventListener('click', loadDoctorSchedules);

        // Function to update the date display
        function updateDateDisplay() {
            const datePicker = document.getElementById('date-picker');
            const dateDisplay = document.getElementById('date-display');
            const selectedDate = new Date(datePicker.value);
            const formattedDate = selectedDate.toLocaleDateString('en-GB', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            dateDisplay.textContent = `Schedule for: ${formattedDate}`;
        }

        // Function to load doctor schedules via AJAX
        function loadDoctorSchedules() {
            const date = document.getElementById('date-picker').value;
            const specialization = document.getElementById('specialization').value;
            const container = document.getElementById('doctors-container');
            
            // Show loading spinner
            container.innerHTML = '<div class="loading"><div class="spinner"></div></div>';
            
            // Fetch data from the backend
            fetch('fetch_doctor_schedule.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `date=${date}&specialization=${specialization}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayDoctors(data.doctors);
                } else {
                    container.innerHTML = `<div class="no-doctors">${data.message}</div>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.innerHTML = '<div class="no-doctors">Error loading doctor schedules. Please try again later.</div>';
            });
        }

        // Function to display doctors and their schedules
        function displayDoctors(doctors) {
            const container = document.getElementById('doctors-container');
            
            if (doctors.length === 0) {
                container.innerHTML = '<div class="no-doctors">No doctors available for the selected date and specialization.</div>';
                return;
            }
            
            let html = '';
            doctors.forEach(doctor => {
                html += `
                    <div class="doctor-card">
                        <div class="doctor-header">
                            <img src="${doctor.profile_image || 'https://picsum.photos/seed/doctor' + doctor.doctor_id + '/80/80.jpg'}" alt="${doctor.first_name} ${doctor.last_name}" class="doctor-image">
                            <div class="doctor-info">
                                <h3>Dr. ${doctor.first_name} ${doctor.last_name}</h3>
                                <div class="doctor-specialization">${doctor.specialization_name}</div>
                            </div>
                        </div>
                        <div class="doctor-schedule">
                            <div class="schedule-title">Available Time Slots</div>
                            <div class="time-slots">
                                ${generateTimeSlots(doctor)}
                            </div>
                            <button class="btn-book" id="book-btn-${doctor.doctor_id}" disabled>Select a time slot to book</button>
                        </div>
                    </div>
                `;
            });
            
            container.innerHTML = html;
            
            // Add event listeners to time slots
            document.querySelectorAll('.time-slot.available').forEach(slot => {
                slot.addEventListener('click', function() {
                    // Remove previous selection
                    document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('selected'));
                    
                    // Add selection to clicked slot
                    this.classList.add('selected');
                    
                    // Get doctor details
                    const doctorId = this.getAttribute('data-doctor-id');
                    const doctorName = this.getAttribute('data-doctor-name');
                    const doctorSpecialization = this.getAttribute('data-doctor-specialization');
                    const time = this.getAttribute('data-time');
                    const date = document.getElementById('date-picker').value;
                    
                    // Store selected details
                    selectedDoctor = {
                        id: doctorId,
                        name: doctorName,
                        specialization: doctorSpecialization
                    };
                    selectedDate = date;
                    selectedTime = time;
                    
                    // Enable the book button
                    const bookBtn = document.getElementById(`book-btn-${doctorId}`);
                    bookBtn.disabled = false;
                    bookBtn.textContent = 'Book Appointment';
                    
                    // Set up the book button click event
                    bookBtn.onclick = function() {
                        openBookingModal();
                    };
                });
            });
        }

        // Function to generate time slots for a doctor
        function generateTimeSlots(doctor) {
            if (!doctor.schedule || doctor.schedule.length === 0) {
                return '<div class="time-slot unavailable">No schedule</div>';
            }
            
            let slotsHtml = '';
            const schedule = doctor.schedule[0]; // Assuming one schedule per day for simplicity
            
            // Generate time slots based on start_time, end_time and slot_duration (default 30 minutes)
            const startTime = new Date(`2000-01-01T${schedule.start_time}`);
            const endTime = new Date(`2000-01-01T${schedule.end_time}`);
            const slotDuration = 30; // Default slot duration in minutes
            
            const currentTime = new Date(startTime);
            
            while (currentTime < endTime) {
                const timeString = currentTime.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false
                });
                
                // Check if this slot is booked
                const isBooked = doctor.booked_slots && doctor.booked_slots.includes(timeString);
                
                // Determine slot class
                let slotClass = 'available';
                let tooltipText = 'Available';
                
                if (isBooked) {
                    slotClass = 'booked';
                    tooltipText = 'Booked';
                } else if (!schedule.is_available) {
                    slotClass = 'unavailable';
                    tooltipText = 'Not Available';
                }
                
                slotsHtml += `
                    <div class="time-slot ${slotClass}" 
                         data-doctor-id="${doctor.doctor_id}" 
                         data-doctor-name="Dr. ${doctor.first_name} ${doctor.last_name}"
                         data-doctor-specialization="${doctor.specialization_name}"
                         data-time="${timeString}"
                         ${slotClass === 'available' ? '' : 'style="cursor: not-allowed;"'}>
                        ${timeString}
                        <div class="tooltip">${tooltipText}</div>
                    </div>
                `;
                
                // Move to next slot
                currentTime.setMinutes(currentTime.getMinutes() + slotDuration);
            }
            
            return slotsHtml;
        }
    });

    // Modal functions
    function openBookingModal() {
        // Set appointment details in the modal
        document.getElementById('doctor_name').textContent = selectedDoctor.name;
        document.getElementById('doctor_specialization').textContent = selectedDoctor.specialization;
        document.getElementById('appointment_date').textContent = formatDateForDisplay(selectedDate);
        document.getElementById('appointment_time').textContent = formatTimeForDisplay(selectedTime);
        
        // Update summary
        updateSummary();
        
        // Show modal
        document.getElementById('bookingModal').style.display = 'block';
        
        // Reset to first step
        resetBookingModal();
    }
    
    function closeBookingModal() {
        document.getElementById('bookingModal').style.display = 'none';
        // Refresh the page to show updated appointments
        location.reload();
    }
    
    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const bookingModal = document.getElementById('bookingModal');
        if (event.target == bookingModal) {
            bookingModal.style.display = 'none';
        }
    }
    
    // Step navigation functions
    function nextStep(stepNumber) {
        // Update summary if moving to step 2, 3, or 4
        if (stepNumber >= 2) {
            updateSummary();
        }
        
        // Hide current step
        document.querySelectorAll('.step-content').forEach(content => {
            content.classList.remove('active');
        });
        
        // Show next step
        document.getElementById('step' + stepNumber + 'Content').classList.add('active');
        
        // Update step indicators
        for (let i = 1; i < stepNumber; i++) {
            document.getElementById('step' + i).classList.add('completed');
            document.getElementById('step' + i).classList.remove('active');
        }
        document.getElementById('step' + stepNumber).classList.add('active');
    }
    
    function prevStep(stepNumber) {
        // Hide current step
        document.querySelectorAll('.step-content').forEach(content => {
            content.classList.remove('active');
        });
        
        // Show previous step
        document.getElementById('step' + stepNumber + 'Content').classList.add('active');
        
        // Update step indicators
        for (let i = stepNumber + 1; i <= 4; i++) {
            document.getElementById('step' + i).classList.remove('active');
            document.getElementById('step' + i).classList.remove('completed');
        }
        document.getElementById('step' + stepNumber).classList.add('active');
    }
    
    function resetBookingModal() {
        // Reset all steps
        document.querySelectorAll('.step-content').forEach(content => {
            content.classList.remove('active');
        });
        document.getElementById('step1Content').classList.add('active');
        
        // Reset step indicators
        for (let i = 2; i <= 4; i++) {
            document.getElementById('step' + i).classList.remove('active');
            document.getElementById('step' + i).classList.remove('completed');
        }
        
        // Reset form values
        document.getElementById('reason').value = '';
        document.getElementById('selected_payment_method').value = 'razorpay';
        
        // Reset buttons
        document.getElementById('proceedToPaymentBtn').disabled = false;
        
        // Clear selections
        document.querySelectorAll('.payment-method').forEach(method => {
            method.classList.remove('selected');
        });
        
        // Select the Razorpay payment method by default
        document.querySelector('.payment-method').classList.add('selected');
    }
    
    // Payment method selection
    function selectPaymentMethod(element, method) {
        // Remove selected class from all payment methods
        document.querySelectorAll('.payment-method').forEach(m => {
            m.classList.remove('selected');
        });
        
        // Add selected class to clicked payment method
        element.classList.add('selected');
        
        // Set the hidden input value
        document.getElementById('selected_payment_method').value = method;
        
        // Enable proceed button
        document.getElementById('proceedToPaymentBtn').disabled = false;
    }
    
    // Proceed to payment
    function proceedToPayment() {
        const paymentMethod = document.getElementById('selected_payment_method').value;
        
        if (paymentMethod === 'razorpay') {
            nextStep(3);
        }
    }
    
    // Update summary
    function updateSummary() {
        const doctorName = selectedDoctor.name;
        const specialization = selectedDoctor.specialization;
        const date = selectedDate;
        const time = selectedTime;
        
        // Format date for display
        let formattedDate = formatDateForDisplay(date);
        
        // Format time for display
        let formattedTime = formatTimeForDisplay(time);
        
        // Update summary in all steps
        const summaryElements = [
            { doctor: 'summary_doctor', specialization: 'summary_specialization', date: 'summary_date', time: 'summary_time' },
            { doctor: 'summary_doctor_payment', specialization: 'summary_specialization_payment', date: 'summary_date_payment', time: 'summary_time_payment' },
            { doctor: 'summary_doctor_success', specialization: 'summary_specialization_success', date: 'summary_date_success', time: 'summary_time_success' }
        ];
        
        summaryElements.forEach(elements => {
            if (document.getElementById(elements.doctor)) {
                document.getElementById(elements.doctor).textContent = doctorName || '-';
            }
            if (document.getElementById(elements.specialization)) {
                document.getElementById(elements.specialization).textContent = specialization || '-';
            }
            if (document.getElementById(elements.date)) {
                document.getElementById(elements.date).textContent = formattedDate;
            }
            if (document.getElementById(elements.time)) {
                document.getElementById(elements.time).textContent = formattedTime;
            }
        });
    }
    
    // Razorpay payment
    document.getElementById('rzp-button').onclick = function(e) {
        e.preventDefault();
        
        var options = {
            "key": "rzp_test_S8YWQLeAKtofm8", // Enter your Test Key ID here
            "amount": "30000", // Amount is in currency subunits. Default currency is INR. Hence, 30000 refers to 30000 paise or ₹300
            "currency": "INR",
            "name": "QuickCare",
            "description": "Appointment Booking",
            "image": "https://example.com/your_logo.png", // Your company logo
            "handler": function (response) {
                // Simulate successful payment and move to success step
                setTimeout(() => {
                    // Generate a random appointment ID
                    const appointmentId = 'APT' + Math.floor(Math.random() * 100000);
                    document.getElementById('appointment_id_display').textContent = appointmentId;
                    nextStep(4);
                }, 1000);
            },
            "prefill": {
                "name": "<?php echo isset($_SESSION['PATIENT_ID']) ? htmlspecialchars($_SESSION['FIRST_NAME'] . ' ' . $_SESSION['LAST_NAME']) : 'Patient Name'; ?>",
                "email": "<?php echo isset($_SESSION['PATIENT_ID']) ? htmlspecialchars($_SESSION['EMAIL']) : ''; ?>",
                "contact": "<?php echo isset($_SESSION['PATIENT_ID']) ? htmlspecialchars($_SESSION['PHONE']) : ''; ?>"
            },
            "notes": {
                "address": "QuickCare Appointment",
                "doctor": selectedDoctor.name,
                "appointment_date": selectedDate,
                "appointment_time": selectedTime
            },
            "theme": {
                "color": "#3399cc"
            }
        };
        
        var rzp1 = new Razorpay(options);
        rzp1.open();
    }
    
    // Download receipt
    function downloadReceipt() {
        const appointmentId = document.getElementById('appointment_id_display').textContent;
        const doctorName = selectedDoctor.name;
        const date = formatDateForDisplay(selectedDate);
        const time = formatTimeForDisplay(selectedTime);
        
        // Create receipt content
        const receiptContent = `
            QUICKCARE MEDICAL CENTER
            Payment Receipt
            
            Appointment ID: ${appointmentId}
            Doctor: ${doctorName}
            Date: ${date}
            Time: ${time}
            Amount Paid: ₹300
            Payment Method: Razorpay (Online)
            Payment Date: ${new Date().toLocaleDateString()}
            
            This is a digitally generated receipt.
        `;
        
        // Create a blob and download
        const blob = new Blob([receiptContent], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `receipt_${appointmentId}.txt`;
        a.click();
        window.URL.revokeObjectURL(url);
    }
    
    // Helper functions
    function formatDateForDisplay(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    }
    
    function formatTimeForDisplay(timeString) {
        if (!timeString) return '-';
        const [hours, minutes] = timeString.split(':');
        const timeObj = new Date();
        timeObj.setHours(hours, minutes);
        return timeObj.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        });
    }
</script>

</body>
</html>