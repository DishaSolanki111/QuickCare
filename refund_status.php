<?php
// ============================================================
// refund_status.php
// Shown to patient after they cancel an appointment
// ============================================================
session_start();

if (!isset($_SESSION['PATIENT_ID'])) {
    header("Location: login.php");
    exit;
}
if (isset($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE'] === 'doctor') {
    header("Location: doctor_dashboard.php");
    exit;
}
if (!isset($_SESSION['CANCEL_RESULT'])) {
    header("Location: manage_appointments.php");
    exit;
}

$result          = $_SESSION['CANCEL_RESULT'];
$refund_eligible = $result['refund_eligible'];
$refund_amount   = $result['refund_amount'];
$refund_txn      = $result['refund_txn'];
$payment_mode    = $result['payment_mode'];
$appt_date       = date('d M Y', strtotime($result['appt_date']));
$appt_time       = date('h:i A', strtotime($result['appt_time']));
$hours_until     = $result['hours_until'];

// Clear the session data so page can't be revisited
unset($_SESSION['CANCEL_RESULT']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $refund_eligible ? 'Refund Initiated' : 'Appointment Cancelled' ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue:   #1a73e8;
            --dark-blue:      #072D44;
            --mid-blue:       #064469;
            --soft-blue:      #5790AB;
            --accent-green:   #2ecc71;
            --accent-orange:  #f39c12;
            --accent-red:     #e74c3c;
            --text-dark:      #202124;
            --light-bg:       #f0f8ff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f5f8ff 0%, #e6f0ff 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: -1;
            background-image: url('uploads/background.png');
            background-size: cover;
            background-position: center;
            filter: blur(3px);
            opacity: 0.8;
            background-color: rgba(0,0,0,0.4);
            background-blend-mode: overlay;
        }

        .main-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            max-width: 580px;
            width: 100%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.12);
            overflow: hidden;
            position: relative;
            text-align: center;
        }

        /* Top accent bar */
        .card::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 5px;
            background: <?= $refund_eligible
                ? 'linear-gradient(90deg, var(--accent-green), var(--primary-blue))'
                : 'linear-gradient(90deg, var(--accent-orange), var(--accent-red))' ?>;
        }

        .card-body { padding: 2.5rem 2rem 2rem; }

        /* Icon */
        .status-icon {
            font-size: 4.5rem;
            margin-bottom: 1.2rem;
            animation: popIn 0.5s ease-out;
        }
        .status-icon.refund  { color: var(--accent-green); }
        .status-icon.norefund { color: var(--accent-orange); }

        @keyframes popIn {
            0%   { transform: scale(0); opacity: 0; }
            60%  { transform: scale(1.15); }
            100% { transform: scale(1); opacity: 1; }
        }

        .status-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.6rem;
        }

        .status-subtitle {
            font-size: 1rem;
            color: #555;
            margin-bottom: 1.8rem;
            line-height: 1.6;
        }

        /* Detail box */
        .detail-box {
            background: var(--light-bg);
            border-radius: 10px;
            padding: 1.4rem 1.6rem;
            margin-bottom: 1.8rem;
            text-align: left;
        }

        .detail-box h4 {
            font-size: 1rem;
            color: var(--mid-blue);
            margin-bottom: 1rem;
            text-align: center;
            font-weight: 600;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 0.55rem 0;
            border-bottom: 1px solid #dde8f5;
            font-size: 0.95rem;
        }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-weight: 600; color: var(--text-dark); }
        .detail-value { color: var(--primary-blue); font-weight: 500; }
        .detail-value.green { color: var(--accent-green); font-weight: 700; }
        .detail-value.orange { color: var(--accent-orange); font-weight: 700; }

        /* No-refund notice */
        .no-refund-notice {
            background: #fff8e1;
            border-left: 4px solid var(--accent-orange);
            border-radius: 8px;
            padding: 1rem 1.2rem;
            margin-bottom: 1.8rem;
            text-align: left;
            font-size: 0.92rem;
            color: #7a5c00;
            line-height: 1.6;
        }
        .no-refund-notice i { margin-right: 6px; }

        /* Policy note */
        .policy-note {
            background: #f0f8ff;
            border-left: 4px solid var(--primary-blue);
            border-radius: 8px;
            padding: 0.9rem 1.2rem;
            margin-bottom: 1.8rem;
            text-align: left;
            font-size: 0.88rem;
            color: #1a3a5f;
            line-height: 1.6;
        }
        .policy-note i { margin-right: 6px; color: var(--primary-blue); }

        /* Buttons */
        .btn-group {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 11px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s;
        }

        .btn-primary {
            background: var(--primary-blue);
            color: #fff;
        }
        .btn-primary:hover { background: #1558c9; transform: translateY(-2px); }

        .btn-outline {
            background: #fff;
            color: var(--primary-blue);
            border: 2px solid var(--primary-blue);
        }
        .btn-outline:hover { background: #e8f0fe; transform: translateY(-2px); }

        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--mid-blue) 100%);
            color: #fff;
            padding: 2.5rem 5%;
            position: relative;
        }

        footer::before {
            content: "";
            position: absolute;
            top: -80px; left: 0;
            width: 100%; height: 80px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1440 320'%3E%3Cpath fill='%23072D44' fill-opacity='1' d='M0,96L80,112C160,128,320,160,480,160C640,160,800,128,960,122.7C1120,117,1280,139,1360,138.7L1440,128L1440,320L0,320Z'%3E%3C/path%3E%3C/svg%3E") no-repeat bottom;
            background-size: cover;
        }

        .footer-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .footer-col h3 {
            font-size: 1.1rem;
            margin-bottom: 0.8rem;
        }

        .footer-col p, .footer-col a {
            font-size: 0.9rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.2rem;
            border-top: 1px solid rgba(255,255,255,0.15);
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
        }

        @media (max-width: 600px) {
            .card-body { padding: 2rem 1.2rem; }
            .status-title { font-size: 1.5rem; }
            .btn-group { flex-direction: column; align-items: center; }
            .btn { width: 100%; max-width: 260px; justify-content: center; }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="main-section">
        <div class="card">
            <div class="card-body">

                <?php if ($refund_eligible): ?>
                    <!-- ── REFUND INITIATED ── -->
                    <i class="fas fa-undo-alt status-icon refund"></i>
                    <h2 class="status-title">Refund Initiated ✅</h2>
                    <p class="status-subtitle">
                        Your appointment has been cancelled and a full refund of
                        <strong>₹<?= number_format($refund_amount, 2) ?></strong> has been processed.
                    </p>

                    <div class="detail-box">
                        <h4>Refund Details</h4>
                        <div class="detail-row">
                            <span class="detail-label">Refund Transaction ID</span>
                            <span class="detail-value"><?= htmlspecialchars($refund_txn) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Refund Amount</span>
                            <span class="detail-value green">₹<?= number_format($refund_amount, 2) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Cancelled Appointment</span>
                            <span class="detail-value"><?= $appt_date ?> at <?= $appt_time ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Refund Method</span>
                            <span class="detail-value"><?= htmlspecialchars($payment_mode) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Processed On</span>
                            <span class="detail-value"><?= date('d M Y, h:i A') ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Status</span>
                            <span class="detail-value green"><i class="fas fa-check-circle"></i> Processed</span>
                        </div>
                    </div>

                    <div class="policy-note">
                        <i class="fas fa-info-circle"></i>
                        The refund will reflect in your original payment method within
                        <strong>5–7 business days</strong> depending on your bank or payment provider.
                    </div>

                <?php else: ?>
                    <!-- ── CANCELLED BUT NO REFUND ── -->
                    <i class="fas fa-calendar-times status-icon norefund"></i>
                    <h2 class="status-title">Appointment Cancelled</h2>
                    <p class="status-subtitle">
                        Your appointment on <strong><?= $appt_date ?> at <?= $appt_time ?></strong> has been cancelled.
                    </p>

                    <div class="no-refund-notice">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>No Refund Applicable</strong><br>
                        Your appointment was cancelled
                        <?php if ($hours_until > 0): ?>
                            only <strong><?= $hours_until ?> hour(s)</strong> before the scheduled time.
                        <?php else: ?>
                            after the appointment time had already passed.
                        <?php endif; ?>
                        Refunds are only available for cancellations made at least
                        <strong>2 hours</strong> in advance.
                    </div>

                    <div class="policy-note">
                        <i class="fas fa-info-circle"></i>
                        <strong>Cancellation Policy:</strong> To receive a full refund, please cancel your
                        appointment at least 2 hours before the scheduled time. Late cancellations and
                        no-shows are non-refundable.
                    </div>
                <?php endif; ?>

                <div class="btn-group">
                    <a href="patient.php" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                    <a href="manage_appointments.php" class="btn btn-outline">
                        <i class="fas fa-calendar-alt"></i> My Appointments
                    </a>
                </div>

            </div><!-- /card-body -->
        </div><!-- /card -->
    </main>

    <footer>
        <div class="footer-inner">
            <div class="footer-col">
                <h3>QuickCare</h3>
                <p>Your trusted partner in healthcare. Book appointments with verified specialists quickly and easily.</p>
            </div>
            <div class="footer-col">
                <h3>Quick Links</h3>
                <p><a href="index.php">Home</a></p>
                <p><a href="appointment.php">Book Appointment</a></p>
            </div>
            <div class="footer-col">
                <h3>Contact Us</h3>
                <p><a href="mailto:quickcare012@gmail.com">quickcare012@gmail.com</a></p>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; <?= date('Y') ?> QuickCare. All rights reserved.
        </div>
    </footer>
</body>
</html>