<?php
session_start();

// DB Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "jewelkartt";
$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("DB Connection Failed: " . $conn->connect_error);
}

// Require login
if (!isset($_SESSION['user_id'])) {
    echo "Please <a href='login.php'>login</a> to view your orders.";
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Fetch orders with items
$sql = "SELECT o.id AS order_id, o.created_at, o.status,
               p.name, p.price, p.image, oi.quantity
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ?
        ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Orders - Jewelkart</title>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<style>
body {
    background: linear-gradient(135deg, #111, #2c2c2c);
    font-family: "Segoe UI", sans-serif;
    color: #fff;
    margin: 0;
}
.container {
    max-width: 1000px;
    margin: 50px auto;
    padding: 0 20px;
}
h2 {
    text-align: center;
    margin-bottom: 30px;
    font-size: 2em;
    font-weight: bold;
}
.order-card {
    background: #fff;
    color: #000;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.6);
}
.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    flex-wrap: wrap;
}
.order-items {
    display: flex;
    flex-direction: column;
    gap: 15px;
}
.order-item {
    display: flex;
    gap: 15px;
    align-items: center;
    border-bottom: 1px solid #ddd;
    padding-bottom: 10px;
}
.order-item:last-child {
    border-bottom: none;
}
.order-item img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
}
.order-item-details {
    flex: 1;
}
.order-item-details h4 {
    margin: 0 0 5px;
    font-size: 1.1em;
}
.order-item-details p {
    margin: 2px 0;
}
.status-badge {
    padding: 5px 10px;
    border-radius: 12px;
    font-weight: bold;
    color: #fff;
    font-size: 0.9em;
}
.status-pending { background: #f0ad4e; }
.status-processing { background: #5bc0de; }
.status-shipped { background: #800080; }
.status-delivered { background: #28a745; }
.status-cancelled { background: #d9534f; }
.status-other { background: #6c757d; }
.back-btn, .invoice-btn {
    display: inline-block;
    margin: 10px 5px 0 0;
    background: #333;
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    transition: 0.2s;
}
.back-btn:hover, .invoice-btn:hover {
    background: #555;
}
@media(max-width:768px){
    .order-item { flex-direction: column; align-items: center; text-align: center; }
    .order-item img { width: 100%; height: auto; }
    .order-item-details { align-items: center; }
    .order-header { flex-direction: column; align-items: flex-start; }
}
</style>
</head>
<body>

<div class="container">
    <h2>📦 My Orders</h2>
    <div class="w3-center">
        <a href="index.php" class="back-btn">⬅ Back to Shop</a>
    </div>

    <?php if ($result->num_rows === 0): ?>
        <p class="w3-center">You have no orders yet.</p>

    <?php else: 
        $currentOrder = 0;
        while ($row = $result->fetch_assoc()):
            if ($currentOrder != $row['order_id']):
                if ($currentOrder != 0) {
                    // close previous order items + add invoice button
                    echo '</div>
                          <div class="w3-center">
                              <a href="invoice.php?order_id=' . $currentOrder . '" class="invoice-btn">🧾 Download Invoice</a>
                          </div>
                        </div>';
                }
                $currentOrder = $row['order_id'];
                ?>
                <div class="order-card">
                    <div class="order-header">
                        <div><strong>Order #<?php echo $row['order_id']; ?></strong></div>
                        <div><?php echo $row['created_at']; ?></div>
                        <div>
                            <?php 
                            $status = $row['status'] ?? 'Pending';
                            $statusClass = match($status) {
                                'Pending' => 'status-pending',
                                'Processing' => 'status-processing',
                                'Shipped' => 'status-shipped',
                                'Delivered' => 'status-delivered',
                                'Cancelled' => 'status-cancelled',
                                default => 'status-other',
                            };
                            echo "<span class='status-badge $statusClass'>$status</span>";
                            ?>
                        </div>
                    </div>
                    <div class="order-items">
            <?php endif; ?>
                        <div class="order-item">
                            <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                            <div class="order-item-details">
                                <h4><?php echo htmlspecialchars($row['name']); ?></h4>
                                <p>Price: ₹<?php echo number_format($row['price']); ?></p>
                                <p>Quantity: <?php echo $row['quantity']; ?></p>
                                <p>Total: ₹<?php echo number_format($row['price'] * $row['quantity']); ?></p>
                            </div>
                        </div>
    <?php endwhile; ?>
                    </div>
                    <div class="w3-center">
                        <a href="invoice.php?order_id=<?php echo $currentOrder; ?>" class="invoice-btn">🧾 Download Invoice</a>
                    </div>
                </div>
    <?php endif; ?>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
