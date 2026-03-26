<?php
// ============================================================
// cancel_appointment.php
// Cancels a patient appointment and initiates a refund
// ============================================================
session_start();

// Only patients can cancel
if (!isset($_SESSION['PATIENT_ID'])) {
    header("Location: login.php");
    exit;
}
if (isset($_SESSION['USER_TYPE']) && $_SESSION['USER_TYPE'] === 'doctor') {
    header("Location: doctor_dashboard.php");
    exit;
}

include "config.php";

$patient_id     = (int) $_SESSION['PATIENT_ID'];
$appointment_id = isset($_POST['appointment_id']) ? (int) $_POST['appointment_id'] : 0;

if (!$appointment_id) {
    header("Location: manage_appointments.php?error=invalid");
    exit;
}

// ── 1. Verify the appointment belongs to this patient and is still SCHEDULED ──
$appt_result = mysqli_query($conn,
    "SELECT a.APPOINTMENT_ID, a.APPOINTMENT_DATE, a.APPOINTMENT_TIME, a.STATUS,
            p.PAYMENT_ID, p.AMOUNT, p.STATUS AS PAY_STATUS, p.TRANSACTION_ID,
            p.PAYMENT_MODE
     FROM   appointment_tbl a
     LEFT JOIN payment_tbl p ON p.APPOINTMENT_ID = a.APPOINTMENT_ID
     WHERE  a.APPOINTMENT_ID = $appointment_id
       AND  a.PATIENT_ID     = $patient_id
     LIMIT 1"
);

if (!$appt_result || mysqli_num_rows($appt_result) === 0) {
    header("Location: manage_appointments.php?error=notfound");
    exit;
}

$appt = mysqli_fetch_assoc($appt_result);

if ($appt['STATUS'] !== 'SCHEDULED') {
    header("Location: manage_appointments.php?error=already_cancelled");
    exit;
}

// ── 2. Check cancellation window (no refund if appointment is within 2 hours) ──
$appt_datetime    = new DateTime($appt['APPOINTMENT_DATE'] . ' ' . $appt['APPOINTMENT_TIME']);
$now              = new DateTime();
$hours_until_appt = ($appt_datetime->getTimestamp() - $now->getTimestamp()) / 3600;

$refund_eligible = ($hours_until_appt >= 2);   // must cancel at least 2 hrs before
$refund_amount   = $refund_eligible ? (float) $appt['AMOUNT'] : 0.00;

// ── 3. Begin transaction ──────────────────────────────────────────────────────
mysqli_begin_transaction($conn);

try {
    // 3a. Cancel the appointment
    $cancel = mysqli_query($conn,
        "UPDATE appointment_tbl
         SET    STATUS = 'CANCELLED'
         WHERE  APPOINTMENT_ID = $appointment_id
           AND  PATIENT_ID     = $patient_id"
    );
    if (!$cancel) throw new Exception("Failed to cancel appointment");

    // 3b. Process refund only if a completed payment exists and within window
    $refund_id  = null;
    $refund_txn = null;

    if ($appt['PAYMENT_ID'] && $appt['PAY_STATUS'] === 'COMPLETED' && $refund_eligible) {

        $payment_id  = (int) $appt['PAYMENT_ID'];
        $refund_txn  = 'RFD' . strtoupper(uniqid());   // e.g. RFDXXX

        // 3c. Mark original payment as REFUNDED
        $upd_pay = mysqli_query($conn,
            "UPDATE payment_tbl
             SET    STATUS = 'REFUNDED'
             WHERE  PAYMENT_ID = $payment_id"
        );
        if (!$upd_pay) throw new Exception("Failed to update payment status");

        // 3d. Insert refund record
        $reason      = "Patient cancelled appointment";
        $refund_date = date('Y-m-d');

        $ins_refund = mysqli_query($conn,
            "INSERT INTO refund_tbl
               (PAYMENT_ID, APPOINTMENT_ID, PATIENT_ID, REFUND_AMOUNT,
                REFUND_DATE, REFUND_STATUS, REFUND_REASON, REFUND_TXN_ID)
             VALUES
               ($payment_id, $appointment_id, $patient_id, $refund_amount,
                '$refund_date', 'PROCESSED', '$reason', '$refund_txn')"
        );
        if (!$ins_refund) throw new Exception("Failed to create refund record");

        $refund_id = mysqli_insert_id($conn);
    }

    // 3e. Log cancellation in appointment_reminder_tbl (matches your existing pattern)
    $appt_formatted = date('M d, Y', strtotime($appt['APPOINTMENT_DATE']))
                    . ' at ' . date('h:i A', strtotime($appt['APPOINTMENT_TIME']));
    $patient_name_res = mysqli_query($conn,
        "SELECT CONCAT(FIRST_NAME, ' ', LAST_NAME) AS FULL_NAME
         FROM patient_tbl WHERE PATIENT_ID = $patient_id"
    );
    $pname = 'Patient';
    if ($patient_name_res && $row = mysqli_fetch_assoc($patient_name_res)) {
        $pname = mysqli_real_escape_string($conn, $row['FULL_NAME']);
    }
    $reminder_msg = "[CANCELLED_BY_PATIENT] $pname cancelled the appointment for $appt_formatted.";
    mysqli_query($conn,
        "INSERT INTO appointment_reminder_tbl
           (RECEPTIONIST_ID, APPOINTMENT_ID, REMINDER_TIME, REMARKS)
         VALUES (1, $appointment_id, '" . date('H:i:s') . "', '$reminder_msg')"
    );

    mysqli_commit($conn);

    // ── 4. Redirect to result page ──────────────────────────────────────────
    $_SESSION['CANCEL_RESULT'] = [
        'appointment_id'  => $appointment_id,
        'refund_eligible' => $refund_eligible,
        'refund_amount'   => $refund_amount,
        'refund_txn'      => $refund_txn,
        'refund_id'       => $refund_id,
        'payment_mode'    => $appt['PAYMENT_MODE'] ?? 'Online Payment',
        'appt_date'       => $appt['APPOINTMENT_DATE'],
        'appt_time'       => $appt['APPOINTMENT_TIME'],
        'hours_until'     => round($hours_until_appt, 1),
    ];
    header("Location: refund_status.php");
    exit;

} catch (Exception $e) {
    mysqli_rollback($conn);
    // Log error in production — for now redirect with generic error
    header("Location: manage_appointments.php?error=cancel_failed");
    exit;
}