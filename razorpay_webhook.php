<?php
require_once('config.php');

// Get the posted data
 $razorpay_payment_id = $_POST['razorpay_payment_id'];
 $razorpay_order_id = $_POST['razorpay_order_id'];
 $razorpay_signature = $_POST['razorpay_signature'];

// Your Razorpay Key Secret
 $key_secret = "YOUR_TEST_KEY_SECRET"; // Replace with your actual key secret

// Verify the signature
 $generated_signature = hash_hmac('sha256', $razorpay_order_id . "|" . $razorpay_payment_id, $key_secret);

if ($generated_signature == $razorpay_signature) {
    // Payment is verified, save to database
    // Your existing code to save the appointment
    echo "Payment verified successfully";
} else {
    // Payment verification failed
    echo "Payment verification failed";
}
?>