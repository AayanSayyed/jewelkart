<?php
session_start();
require('vendor/autoload.php'); // Razorpay SDK
use Razorpay\Api\Api;

// Enable errors for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "jewelkartt";
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("DB Connection Failed: " . $conn->connect_error);

// Require login
if (!isset($_SESSION['user_id'])) {
    echo "Please <a href='login.php'>login</a> to checkout.";
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// Get cart items
$sql = "SELECT c.id, c.product_id, c.quantity, p.price, p.name
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Your cart is empty. <a href='index.php'>Shop now</a>";
    exit;
}

$cartItems = [];
$orderTotal = 0;
while ($row = $result->fetch_assoc()) {
    $cartItems[] = $row;
    $orderTotal += $row['price'] * $row['quantity'];
}

// Fetch previous user info
$stmt = $conn->prepare("SELECT * FROM user_info WHERE user_id=? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userInfoResult = $stmt->get_result();
$user_info = $userInfoResult->fetch_assoc();
$stmt->close();

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $pincode = $_POST['pincode'];
    $payment_method = $_POST['payment_method'];

    // Save/update user info
    if ($user_info) {
        $stmt = $conn->prepare("UPDATE user_info SET full_name=?, address=?, city=?, state=?, pincode=?, payment_method=? WHERE user_id=?");
        $stmt->bind_param("ssssssi", $full_name, $address, $city, $state, $pincode, $payment_method, $user_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO user_info (user_id, full_name, address, city, state, pincode, payment_method) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssss", $user_id, $full_name, $address, $city, $state, $pincode, $payment_method);
    }
    $stmt->execute();
    $stmt->close();

    if ($payment_method === 'Cash on Delivery') {
        // Insert order for COD
        $stmtOrder = $conn->prepare("INSERT INTO orders (user_id, total, payment_method, payment_status) VALUES (?, ?, ?, ?)");
        $payment_status = 'Pending';
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

        echo "<script>alert('✅ Order placed successfully!');window.location='orders.php';</script>";
        exit;
    } else {
        // Online Payment (Razorpay)
        $api = new Api('rzp_test_RBbDWQ440DjZPP', 'l7YIcn235BQ3jpoz1bzCM93b');
        $razorpayOrder = $api->order->create([
            'receipt' => (string)time(),
            'amount' => $orderTotal * 100,
            'currency' => 'INR',
            'payment_capture' => 1
        ]);

        $_SESSION['cartItems'] = $cartItems;
        $_SESSION['orderTotal'] = $orderTotal;
        $_SESSION['userInfo'] = ['full_name'=>$full_name, 'email'=>$user_info['email']??''];
        $_SESSION['razorpay_order_id'] = $razorpayOrder['id'];
        ?>

        <!DOCTYPE html>
        <html>
        <head><title>Redirecting to Payment</title></head>
        <body>
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script>
        var options = {
            "key": "rzp_test_RBbDWQ440DjZPP",
            "amount": "<?php echo $orderTotal*100; ?>",
            "currency": "INR",
            "name": "Om Jewelry Store",
            "description": "Order Payment",
            "order_id": "<?php echo $razorpayOrder['id']; ?>",
            "handler": function (response){
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = 'payment_success.php';

                var fields = ['razorpay_payment_id','razorpay_order_id','razorpay_signature'];
                fields.forEach(function(field){
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = field;
                    input.value = response[field];
                    form.appendChild(input);
                });

                document.body.appendChild(form);
                form.submit();
            },
            "prefill": {
                "name": "<?php echo htmlspecialchars($full_name); ?>",
                "email": "<?php echo htmlspecialchars($user_info['email'] ?? ''); ?>"
            },
            "theme": {"color":"#3399cc"}
        };
        var rzp1 = new Razorpay(options);
        rzp1.open();
        </script>
        </body>
        </html>

        <?php
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Checkout - Jewelkart</title>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<style>
/* New Light Grayish Theme */
body { 
    background: #f2f2f7; 
    color:#333; 
    font-family:"Segoe UI",sans-serif; 
    margin:0; 
}
.container { 
    max-width: 700px; 
    margin:50px auto; 
    padding:0 20px; 
}
h2 { 
    text-align:center; 
    font-size:2em; 
    margin-bottom:30px; 
    font-weight:bold; 
    color:#222; 
}
.w3-card { 
    padding:20px; 
    border-radius:12px; 
    background:#fff; 
    color:#000; 
    box-shadow:0 4px 15px rgba(0,0,0,0.1); 
}
.w3-input, .w3-select, textarea { 
    margin-bottom:15px; 
    border-radius:6px; 
    border:1px solid #ccc; 
}
.payment-options { 
    display:flex; 
    gap:20px; 
    flex-wrap:wrap; 
    margin-top:15px; 
}
.payment-option {
    flex:1; display:flex; align-items:center; justify-content:center;
    padding:15px; border:2px solid #ccc; border-radius:12px;
    cursor:pointer; transition:0.3s; gap:10px;
    background:#fafafa;
}
.payment-option.selected { 
    border-color:#28a745; 
    background:#e9f9ee; 
}
.payment-option img { 
    width:40px; height:40px; object-fit:contain; 
}
.payment-option span { 
    font-weight:bold; font-size:1em; 
}
.place-btn { 
    background:#28a745; 
    color:#fff; 
    font-weight:bold; 
    padding:12px; 
    border:none; 
    border-radius:8px; 
    cursor:pointer; 
    width:100%; 
    font-size:1.1em; 
}
.place-btn:hover { background:#1c7c31; }
</style>
<script>
function selectPayment(option){
    document.getElementById('payment_method').value = option.dataset.value;
    document.querySelectorAll('.payment-option').forEach(el=>el.classList.remove('selected'));
    option.classList.add('selected');
}
</script>
</head>
<body>
<div class="container">
<h2>Checkout</h2>
<div class="w3-card">
<form method="POST">
<p><label>Full Name</label>
<input class="w3-input w3-border" type="text" name="full_name" value="<?php echo htmlspecialchars($user_info['full_name'] ?? ''); ?>" required></p>

<p><label>Address</label>
<textarea class="w3-input w3-border" name="address" required><?php echo htmlspecialchars($user_info['address'] ?? ''); ?></textarea></p>

<p><label>City</label>
<input class="w3-input w3-border" type="text" name="city" value="<?php echo htmlspecialchars($user_info['city'] ?? ''); ?>" required></p>

<p><label>State</label>
<input class="w3-input w3-border" type="text" name="state" value="<?php echo htmlspecialchars($user_info['state'] ?? ''); ?>" required></p>

<p><label>Pincode</label>
<input class="w3-input w3-border" type="text" name="pincode" value="<?php echo htmlspecialchars($user_info['pincode'] ?? ''); ?>" required></p>

<p><label>Payment Method</label></p>
<input type="hidden" id="payment_method" name="payment_method" required>
<div class="payment-options">
    <div class="payment-option" onclick="selectPayment(this)" data-value="Cash on Delivery">
        <img src="images/wallet-svgrepo-com.svg" alt="Cash">
        <span>Cash on Delivery</span>
    </div>
    <div class="payment-option" onclick="selectPayment(this)" data-value="Online Payment">
        <img src="images/card-machine-atm-svgrepo-com.svg" alt="Online">
        <span>Online Payment</span>
    </div>
</div>

<p><button type="submit" class="place-btn">Place Order</button></p>
</form>
</div>
</div>
</body>
</html>
