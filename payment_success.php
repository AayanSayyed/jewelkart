<?php
session_start();
require('vendor/autoload.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "jewelkartt";
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("DB Connection Failed: " . $conn->connect_error);

// Check session
if (!isset($_SESSION['user_id'], $_SESSION['razorpay_order_id'], $_SESSION['cartItems'], $_SESSION['orderTotal'], $_SESSION['userInfo'])) {
    die("Invalid session. Please try again.");
}

$user_id = (int)$_SESSION['user_id'];
$cartItems = $_SESSION['cartItems'];
$orderTotal = $_SESSION['orderTotal'];
$user_info = $_SESSION['userInfo'];

// Razorpay API
$api = new Api();

// Get payment details from POST
$payment_id = $_POST['razorpay_payment_id'] ?? '';
$order_id_param = $_POST['razorpay_order_id'] ?? '';
$signature = $_POST['razorpay_signature'] ?? '';

if (!$payment_id || !$order_id_param || !$signature) {
    die("Payment failed or invalid parameters.");
}

try {
    // Verify payment signature
    $attributes = [
        'razorpay_order_id' => $order_id_param,
        'razorpay_payment_id' => $payment_id,
        'razorpay_signature' => $signature
    ];
    $api->utility->verifyPaymentSignature($attributes);

    // Insert order into DB
    $stmtOrder = $conn->prepare("INSERT INTO orders (user_id, total, payment_method, payment_status) VALUES (?, ?, ?, ?)");
    $payment_status = 'Paid';
    $payment_method = 'Online Payment';
    $stmtOrder->bind_param("idss", $user_id, $orderTotal, $payment_method, $payment_status);
    $stmtOrder->execute();
    $order_id = $stmtOrder->insert_id;
    $stmtOrder->close();

    // Insert order items
    $stmtItems = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cartItems as $item) {
        $stmtItems->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmtItems->execute();
    }
    $stmtItems->close();

    // Clear cart
    $stmtClear = $conn->prepare("DELETE FROM cart WHERE user_id=?");
    $stmtClear->bind_param("i", $user_id);
    $stmtClear->execute();
    $stmtClear->close();

    // Clear session
    unset($_SESSION['razorpay_order_id'], $_SESSION['cartItems'], $_SESSION['orderTotal'], $_SESSION['userInfo']);

    echo "<script>alert('✅ Payment successful and order placed!'); window.location='index.php';</script>";
    exit;

} catch (SignatureVerificationError $e) {
    unset($_SESSION['razorpay_order_id']); // remove failed order
    echo "<script>alert('❌ Payment verification failed. Your order was not placed.'); window.location='checkout.php';</script>";
    exit;
}
?>
