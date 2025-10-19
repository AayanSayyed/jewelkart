<?php
session_start();
require('vendor/autoload.php');

use Razorpay\Api\Api;

$api = new Api();

if (isset($_POST['razorpay_payment_id'])) {
    try {
        // Verify payment signature
        $attributes = [
            'razorpay_order_id' => $_POST['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        ];
        $api->utility->verifyPaymentSignature($attributes);

        // ✅ Signature valid → Insert Order into DB
        $host = "localhost"; $user = "root"; $pass = ""; $dbname = "jewelkartt";
        $conn = new mysqli($host, $user, $pass, $dbname);

        $user_id = (int) $_SESSION['user_id'];
        $cart = $conn->query("SELECT c.product_id, c.quantity, p.price 
                              FROM cart c JOIN products p ON c.product_id = p.id 
                              WHERE c.user_id=$user_id");

        $orderTotal = 0;
        while ($row = $cart->fetch_assoc()) {
            $orderTotal += $row['price'] * $row['quantity'];
        }

        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total, status, razorpay_payment_id) VALUES (?, ?, 'Paid', ?)");
        $stmt->bind_param("ids", $user_id, $orderTotal, $_POST['razorpay_payment_id']);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        // Insert order items
        $cart->data_seek(0);
        $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        while ($row = $cart->fetch_assoc()) {
            $itemStmt->bind_param("iiid", $order_id, $row['product_id'], $row['quantity'], $row['price']);
            $itemStmt->execute();
        }
        $itemStmt->close();

        // Clear cart
        $conn->query("DELETE FROM cart WHERE user_id=$user_id");

        echo "<script>alert('✅ Payment Successful & Order Placed!'); window.location='orders.php';</script>";

    } catch (Exception $e) {
        echo "Payment Verification Failed: " . $e->getMessage();
    }
}
?>
