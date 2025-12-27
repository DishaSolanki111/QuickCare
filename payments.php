<?php
session_start();

// Check if user is logged in as a patient
if (!isset($_SESSION['PATIENT_ID'])) {
    header("Location: login_for_all.php");
    exit;
}

include 'config.php';
 $patient_id = $_SESSION['PATIENT_ID'];

// Fetch patient data from database
 $patient_query = mysqli_query($conn, "SELECT * FROM patient_tbl WHERE PATIENT_ID = '$patient_id'");
 $patient = mysqli_fetch_assoc($patient_query);

// Fetch payments data
 $payments_query = mysqli_query($conn, "
    SELECT p.*, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME
    FROM payment_tbl p
    JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE a.PATIENT_ID = '$patient_id'
    ORDER BY p.PAYMENT_DATE DESC
");

// Fetch unpaid appointments
 $unpaid_query = mysqli_query($conn, "
    SELECT a.*, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME
    FROM appointment_tbl a
    JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
    JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
    WHERE a.PATIENT_ID = '$patient_id'
    AND a.APPOINTMENT_ID NOT IN (SELECT APPOINTMENT_ID FROM payment_tbl)
    AND a.STATUS = 'COMPLETED'
    ORDER BY a.APPOINTMENT_DATE DESC
");

// Handle payment processing
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['process_payment'])) {
    $appointment_id = mysqli_real_escape_string($conn, $_POST['appointment_id']);
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    $payment_mode = mysqli_real_escape_string($conn, $_POST['payment_mode']);
    
    // Generate a transaction ID
    $transaction_id = uniqid('txn_');
    
    $payment_query = "INSERT INTO payment_tbl (APPOINTMENT_ID, AMOUNT, PAYMENT_DATE, PAYMENT_MODE, STATUS, TRANSACTION_ID) 
                     VALUES ('$appointment_id', '$amount', CURDATE(), '$payment_mode', 'COMPLETED', '$transaction_id')";
    
    if (mysqli_query($conn, $payment_query)) {
        $success_message = "Payment processed successfully!";
        // Refresh the payments query
        $payments_query = mysqli_query($conn, "
            SELECT p.*, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME
            FROM payment_tbl p
            JOIN appointment_tbl a ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
            JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
            JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
            WHERE a.PATIENT_ID = '$patient_id'
            ORDER BY p.PAYMENT_DATE DESC
        ");
        
        // Refresh unpaid appointments query
        $unpaid_query = mysqli_query($conn, "
            SELECT a.*, d.FIRST_NAME as DOC_FNAME, d.LAST_NAME as DOC_LNAME, s.SPECIALISATION_NAME
            FROM appointment_tbl a
            JOIN doctor_tbl d ON a.DOCTOR_ID = d.DOCTOR_ID
            JOIN specialisation_tbl s ON d.SPECIALISATION_ID = s.SPECIALISATION_ID
            WHERE a.PATIENT_ID = '$patient_id'
            AND a.APPOINTMENT_ID NOT IN (SELECT APPOINTMENT_ID FROM payment_tbl)
            AND a.STATUS = 'COMPLETED'
            ORDER BY a.APPOINTMENT_DATE DESC
        ");
    } else {
        $error_message = "Error processing payment: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - QuickCare</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a3a5f;
            --secondary-color: #3498db;
            --accent-color: #2ecc71;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #17a2b8;
            --dark-blue: #072D44;
            --mid-blue: #064469;
            --soft-blue: #5790AB;
            --light-blue: #9CCDD8;
            --gray-blue: #D0D7E1;
            --white: #ffffff;
            --card-bg: #F6F9FB;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background: #072D44;
            min-height: 100vh;
            color: white;
            padding-top: 30px;
            position: fixed;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 40px;
            color: #9CCDD8;
        }

        .sidebar a {
            display: block;
            padding: 15px 25px;
            color: #D0D7E1;
            text-decoration: none;
            font-size: 17px;
            border-left: 4px solid transparent;
        }

        .sidebar a:hover, .sidebar a.active {
            background: #064469;
            border-left: 4px solid #9CCDD8;
            color: white;
        }

        .logout-btn:hover{
            background-color: var(--light-blue);
        }
        .logout-btn {
            display: block;
            width: 80%;
            margin: 20px auto 0 auto;
            padding: 10px;
            background-color: var(--soft-blue);
            color: var(--white);    
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            transition: background-color 0.3s;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }
        
        .welcome-msg {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .user-actions {
            display: flex;
            align-items: center;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: var(--secondary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-weight: bold;
        }
        
        .payment-card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .payment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .payment-header h3 {
            color: var(--primary-color);
        }
        
        .payment-details {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }
        
        .payment-item {
            display: flex;
            align-items: center;
            color: #666;
        }
        
        .payment-item i {
            margin-right: 10px;
            color: var(--secondary-color);
            font-size: 18px;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
        }
        
        .status-completed {
            background-color: rgba(46, 204, 113, 0.2);
            color: var(--accent-color);
        }
        
        .status-failed {
            background-color: rgba(231, 76, 60, 0.2);
            color: var(--danger-color);
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
        }
        
        .btn-success {
            background-color: var(--accent-color);
            color: white;
        }
        
        .btn-success:hover {
            background-color: #27ae60;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c0392b;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .tabs {
            display: flex;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 12px 20px;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            color: #777;
            transition: all 0.3s ease;
        }
        
        .tab.active {
            color: var(--primary-color);
            border-bottom: 3px solid var(--secondary-color);
        }
        
        .tab:hover {
            color: var(--primary-color);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #777;
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #ddd;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border: none;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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
            border-color: var(--secondary-color);
            outline: none;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }
        
        .payment-summary {
            background-color: var(--light-color);
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .summary-item:last-child {
            margin-bottom: 0;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            font-weight: bold;
        }
        
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            
            .logo h1 span, .nav-item span {
                display: none;
            }
            
            .main-content {
                margin-left: 70px;
            }
        }
        
        @media (max-width: 768px) {
            .payment-details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <img src="./uploads/logo.JPG" alt="QuickCare Logo" class="logo-img" style="display:block; margin: 0 auto 10px auto; width:80px; height:80px; border-radius:50%;">
            <h2>QuickCare</h2>
            <div class="nav">
                <a href="patient.php">Dashboard</a>
                <a href="patient_profile.php">My Profile</a>
                <a href="manage_appointments.php">Manage Appointments</a>
                <a href="doctor_schedule.php">View Doctor Schedule</a>
                <a href="prescriptions.php">My Prescriptions</a>
                <a href="medicine_reminder.php">Medicine Reminder</a>
                <a class="active">Payments</a>
                <a href="feedback.php">Feedback</a>
                <a href="doctor_profiles.php">View Doctor Profile</a>
                <button class="logout-btn">logout</button>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <div class="header">
                <div class="welcome-msg">Payments</div>
                <div class="user-actions">
                    <div class="user-dropdown">
                        <div class="user-avatar"><?php echo strtoupper(substr($patient['FIRST_NAME'], 0, 1) . substr($patient['LAST_NAME'], 0, 1)); ?></div>
                        <span><?php echo htmlspecialchars($patient['FIRST_NAME'] . ' ' . $patient['LAST_NAME']); ?></span>
                        <i class="fas fa-chevron-down" style="margin-left: 8px;"></i>
                    </div>
                </div>
            </div>
            
            <!-- Success/Error Messages -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <!-- Tabs Section -->
            <div class="tabs">
                <div class="tab active" data-tab="pending">Pending Payments</div>
                <div class="tab" data-tab="history">Payment History</div>
            </div>
            
            <!-- Tab Content -->
            <div class="tab-content active" id="pending">
                <?php
                if (mysqli_num_rows($unpaid_query) > 0) {
                    while ($appointment = mysqli_fetch_assoc($unpaid_query)) {
                        ?>
                        <div class="payment-card">
                            <div class="payment-header">
                                <h3>Dr. <?php echo htmlspecialchars($appointment['DOC_FNAME'] . ' ' . $appointment['DOC_LNAME']); ?></h3>
                                <span><?php echo htmlspecialchars($appointment['SPECIALISATION_NAME']); ?></span>
                            </div>
                            
                            <div class="payment-details">
                                <div class="payment-item">
                                    <i class="far fa-calendar"></i>
                                    <span><?php echo date('F d, Y', strtotime($appointment['APPOINTMENT_DATE'])); ?></span>
                                </div>
                                <div class="payment-item">
                                    <i class="far fa-clock"></i>
                                    <span><?php echo date('h:i A', strtotime($appointment['APPOINTMENT_TIME'])); ?></span>
                                </div>
                                <div class="payment-item">
                                    <i class="fas fa-rupee-sign"></i>
                                    <span>Consultation Fee: ₹500</span>
                                </div>
                            </div>
                            
                            <div class="btn-group">
                                <button class="btn btn-success" onclick="openPaymentModal(<?php echo $appointment['APPOINTMENT_ID']; ?>)">
                                    <i class="fas fa-credit-card"></i> Pay Now
                                </button>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <p>No pending payments</p>
                    </div>';
                }
                ?>
            </div>
            
            <div class="tab-content" id="history">
                <?php
                if (mysqli_num_rows($payments_query) > 0) {
                    while ($payment = mysqli_fetch_assoc($payments_query)) {
                        $status_class = ($payment['STATUS'] == 'COMPLETED') ? 'status-completed' : 'status-failed';
                        ?>
                        <div class="payment-card">
                            <div class="payment-header">
                                <h3>Dr. <?php echo htmlspecialchars($payment['DOC_FNAME'] . ' ' . $payment['DOC_LNAME']); ?></h3>
                                <span class="status-badge <?php echo $status_class; ?>"><?php echo $payment['STATUS']; ?></span>
                            </div>
                            
                            <div class="payment-details">
                                <div class="payment-item">
                                    <i class="far fa-calendar"></i>
                                    <span><?php echo date('F d, Y', strtotime($payment['APPOINTMENT_DATE'])); ?></span>
                                </div>
                                <div class="payment-item">
                                    <i class="far fa-clock"></i>
                                    <span><?php echo date('h:i A', strtotime($payment['APPOINTMENT_TIME'])); ?></span>
                                </div>
                                <div class="payment-item">
                                    <i class="fas fa-rupee-sign"></i>
                                    <span>Amount: ₹<?php echo $payment['AMOUNT']; ?></span>
                                </div>
                                <div class="payment-item">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Mode: <?php echo $payment['PAYMENT_MODE']; ?></span>
                                </div>
                                <div class="payment-item">
                                    <i class="far fa-calendar-alt"></i>
                                    <span>Payment Date: <?php echo date('F d, Y', strtotime($payment['PAYMENT_DATE'])); ?></span>
                                </div>
                                <div class="payment-item">
                                    <i class="fas fa-receipt"></i>
                                    <span>Transaction ID: <?php echo $payment['TRANSACTION_ID']; ?></span>
                                </div>
                            </div>
                            
                            <div class="btn-group">
                                <button class="btn btn-primary">
                                    <i class="fas fa-download"></i> Download Receipt
                                </button>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="empty-state">
                        <i class="fas fa-history"></i>
                        <p>No payment history found</p>
                    </div>';
                }
                ?>
            </div>
        </div>
    </div>
    
    <!-- Payment Modal -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closePaymentModal()">&times;</span>
            <h2>Process Payment</h2>
            <form method="POST" action="payments.php">
                <input type="hidden" id="payment_appointment_id" name="appointment_id">
                
                <div class="form-group">
                    <label for="amount">Amount</label>
                    <input type="text" class="form-control" id="amount" name="amount" value="500" readonly>
                </div>
                
                <div class="form-group">
                    <label for="payment_mode">Payment Mode</label>
                    <select class="form-control" id="payment_mode" name="payment_mode" required>
                        <option value="">-- Select Payment Mode --</option>
                        <option value="CREDIT CARD">Credit Card</option>
                        <option value="GOOGLE PAY">Google Pay</option>
                        <option value="UPI">UPI</option>
                        <option value="NET BANKING">Net Banking</option>
                    </select>
                </div>
                
                <div class="payment-summary">
                    <div class="summary-item">
                        <span>Consultation Fee:</span>
                        <span>₹500</span>
                    </div>
                    <div class="summary-item">
                        <span>Tax:</span>
                        <span>₹0</span>
                    </div>
                    <div class="summary-item">
                        <span>Total Amount:</span>
                        <span>₹500</span>
                    </div>
                </div>
                
                <div class="btn-group">
                    <button type="submit" name="process_payment" class="btn btn-success">
                        <i class="fas fa-check"></i> Process Payment
                    </button>
                    <button type="button" class="btn btn-danger" onclick="closePaymentModal()">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabId = tab.getAttribute('data-tab');
                    
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    tab.classList.add('active');
                    document.getElementById(tabId).classList.add('active');
                });
            });
        });
        
        function openPaymentModal(appointmentId) {
            document.getElementById('payment_appointment_id').value = appointmentId;
            document.getElementById('paymentModal').style.display = 'block';
        }
        
        function closePaymentModal() {
            document.getElementById('paymentModal').style.display = 'none';
        }
        
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('paymentModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>