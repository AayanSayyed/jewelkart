<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Fetch cart items
$cart_sql = "SELECT p.name, p.price, c.quantity FROM cart c 
             JOIN products p ON c.product_id = p.id 
             WHERE c.user_id=?";
$stmt = $conn->prepare($cart_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$cart_items = $stmt->get_result();

// Fetch orders
$order_sql = "SELECT p.name, p.price, o.created_at FROM orders o
              JOIN products p ON o.product_id = p.id
              WHERE o.user_id=?";
$stmt2 = $conn->prepare($order_sql);
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$order_items = $stmt2->get_result();
?>

<h2>Welcome, <?php echo htmlspecialchars($username); ?> 👋</h2>
<a href="logout.php">Logout</a>

<h3>Your Cart</h3>
<ul>
<?php while($item = $cart_items->fetch_assoc()): ?>
    <li><?php echo $item['name']; ?> - $<?php echo $item['price']; ?> (x<?php echo $item['quantity']; ?>)</li>
<?php endwhile; ?>
</ul>

<h3>Your Orders</h3>
<ul>
<?php while($order = $order_items->fetch_assoc()): ?>
    <li><?php echo $order['name']; ?> - $<?php echo $order['price']; ?> (Ordered on <?php echo $order['created_at']; ?>)</li>
<?php endwhile; ?>
</ul>
