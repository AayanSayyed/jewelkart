<?php
session_start();

// Database connection
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
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

// Handle quantity update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['quantity'])) {
        $cart_id = (int) $_POST['cart_id'];
        $new_qty = (int) $_POST['quantity'];
        if ($new_qty > 0) {
            $upd = $conn->prepare("UPDATE cart SET quantity=? WHERE id=? AND user_id=?");
            $upd->bind_param("iii", $new_qty, $cart_id, $user_id);
            $upd->execute();
            $upd->close();
        }
        header("Location: cart.php");
        exit;
    }
    if (isset($_POST['delete_item'])) {
        $cart_id = (int) $_POST['cart_id'];
        $del = $conn->prepare("DELETE FROM cart WHERE id=? AND user_id=?");
        $del->bind_param("ii", $cart_id, $user_id);
        $del->execute();
        $del->close();
        header("Location: cart.php");
        exit;
    }
}

// Get cart items (newest first)
$sql = "SELECT c.id, p.name, p.price, p.image, c.quantity, c.created_at
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ? 
        ORDER BY c.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Cart - Jewelkart</title>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<style>
/* Opposite Back Button */
.back-btn {
    position: fixed;
    top: 80px;
    left: 20px;
    width: 45px;
    height: 45px;
    background: #fff;  
    color: #000;      
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    transition: background 0.3s, color 0.3s;
    z-index: 2000;
}
.back-btn:hover {
    background: #ff6600;
    color: #fff;
}

/* Cart Layout */
body {
    margin: 0;
    font-family: "Segoe UI", sans-serif;
    background: #f5f5f5;
}
.cart-container {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}
h2 {
    text-align: center;
    margin-bottom: 30px;
}
.cart-item {
    display: flex;
    gap: 20px;
    background: #fff;
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 20px;
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    align-items: center;
    transition: transform 0.2s;
}
.cart-item:hover {
    transform: translateY(-4px);
}
.cart-item img {
    width: 160px;
    height: 160px;
    object-fit: cover;
    border-radius: 10px;
}
.cart-details {
    flex: 1;
}
.cart-details h3 {
    margin: 0 0 10px;
}
.cart-details p {
    margin: 6px 0;
    font-size: 1em;
}
.cart-actions {
    margin-top: 10px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.cart-actions input[type="number"] {
    width: 60px;
    padding: 5px;
    border-radius: 6px;
    border: 1px solid #ccc;
    text-align: center;
}

/* Buttons */
.w3-button {
    border-radius: 8px;
    font-weight: bold;
    padding: 8px 15px;
    text-decoration: none;
    transition: all 0.2s;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.btn-black {
    background: #000 !important;
    color: #fff !important;
}
.btn-black:hover {
    background: #222 !important;
}
.btn-red {
    background: #d9534f !important;
    color: #fff !important;
}
.btn-red:hover {
    background: #b52b27 !important;
}
.btn-grey {
    background: #6c757d !important;
    color: #fff !important;
}
.btn-grey:hover {
    background: #5a6268 !important;
}

.grand-total {
    text-align: right;
    font-size: 1.4em;
    font-weight: bold;
    margin-top: 20px;
}
.checkout-buttons {
    margin-top: 30px;
    text-align: center;
}
.checkout-buttons a {
    margin: 10px;
    display: inline-block;
}
@media(max-width:768px){
    .cart-item { flex-direction: column; align-items: center; text-align: center; }
    .cart-item img { width: 100%; height: auto; }
}
</style>
<script>
function updateQuantity(cartId, input) {
    let form = document.createElement('form');
    form.method = 'POST';
    form.style.display = 'none';
    let inputId = document.createElement('input');
    inputId.name = 'cart_id';
    inputId.value = cartId;
    let inputQty = document.createElement('input');
    inputQty.name = 'quantity';
    inputQty.value = input.value;
    form.appendChild(inputId);
    form.appendChild(inputQty);
    document.body.appendChild(form);
    form.submit();
}
</script>
</head>
<body>

<!-- Back Button -->
<a href="index.php" class="back-btn"><span class="material-icons">arrow_back</span></a>

<div class="cart-container">
    <h2>🛒 Your Shopping Cart</h2>

    <?php if ($result->num_rows === 0): ?>
        <p class="w3-center">Your cart is empty.</p>
        <div class="w3-center w3-margin-top">
            <a href="index.php" class="w3-button btn-black w3-large">⬅ Back to Home</a>
        </div>
    <?php else: 
        $grandTotal = 0;
        while ($row = $result->fetch_assoc()):
            $itemTotal = $row['price'] * $row['quantity'];
            $grandTotal += $itemTotal;
    ?>
    <div class="cart-item">
        <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
        <div class="cart-details">
            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
            <p>Price: ₹<?php echo number_format($row['price']); ?></p>
            <p>Total: ₹<?php echo number_format($itemTotal); ?></p>
            <div class="cart-actions">
                <input type="number" min="1" value="<?php echo $row['quantity']; ?>" 
                    onchange="updateQuantity(<?php echo $row['id']; ?>, this)">
                <form method="POST" style="display:inline-block;">
                    <input type="hidden" name="cart_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" name="delete_item" class="w3-button btn-red">Remove</button>
                </form>
            </div>
        </div>
    </div>
    <?php endwhile; ?>

    <div class="grand-total">Grand Total: ₹<?php echo number_format($grandTotal); ?></div>

    <div class="checkout-buttons">
        <a href="checkout.php" class="w3-button btn-black w3-xlarge">Proceed to Checkout</a>
        <a href="index.php" class="w3-button btn-grey w3-xlarge">⬅ Back to Home</a>
    </div>
    <?php endif; ?>
</div>

</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
