<?php
session_start();
require('vendor/autoload.php'); // Make sure Razorpay SDK is installed
use Razorpay\Api\Api;

// Razorpay test keys
$keyId = 'xxxxxxxxxxxxxxxxx';     // replace with your test Key ID
$keySecret = 'xxxxxxxxxxxx';      // replace with your test Key Secret

$api = new Api($keyId, $keySecret);

// Amount in paise (₹1 = 100 paise)
$amount = 50000; // ₹500 for testing

$orderData = [
    'receipt'         => 'order_rcptid_'.time(),
    'amount'          => $amount,
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];

$razorpayOrder = $api->order->create($orderData);

// Store order id in session to verify later
$_SESSION['razorpay_order_id'] = $razorpayOrder['id'];

echo json_encode($razorpayOrder);
?>
