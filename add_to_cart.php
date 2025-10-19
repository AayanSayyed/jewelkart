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
    header("Location: login.php?msg=login-required");
    exit;
}

// Only accept POST with product_id
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_id'])) {
    $user_id = (int) $_SESSION['user_id'];
    $product_id = (int) $_POST['product_id'];
    if ($product_id <= 0) {
        die("Invalid product.");
    }

    // Verify product exists and get info
    $chkProd = $conn->prepare("SELECT id, name, price, image FROM products WHERE id=? AND status='Available'");
    $chkProd->bind_param("i", $product_id);
    $chkProd->execute();
    $resProd = $chkProd->get_result();
    if ($resProd->num_rows === 0) {
        $chkProd->close();
        die("Product not found or unavailable.");
    }
    $product = $resProd->fetch_assoc();
    $chkProd->close();

    // Check if product already in cart
    $check = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id=? AND product_id=?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $res = $check->get_result();

    if ($row = $res->fetch_assoc()) {
        $newQty = $row['quantity'] + 1;
        $upd = $conn->prepare("UPDATE cart SET quantity=? WHERE id=?");
        $upd->bind_param("ii", $newQty, $row['id']);
        $upd->execute();
        $upd->close();
    } else {
        $ins = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $ins->bind_param("ii", $user_id, $product_id);
        $ins->execute();
        $ins->close();
    }
    $check->close();

    // Redirect back
    $redirect = $_SERVER['HTTP_REFERER'] ?? 'index.php';
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Adding to Cart</title>
        <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css">
        <style>
            body {
                margin: 0; font-family: Arial; background: #f5f5f5;
            }
            .overlay {
                position: fixed;
                top: 0; left: 0; width: 100%; height: 100%;
                background: rgba(0,0,0,0.6);
                display: flex; align-items: center; justify-content: center;
                z-index: 9999; color: #fff; flex-direction: column;
            }
            .overlay img {
                width: 120px; height: 120px; object-fit: cover; border-radius: 10px; margin-bottom: 15px; border: 2px solid #fff;
            }
            .overlay h2 { margin: 0; font-size: 20px; font-weight: bold; }
            .overlay p { margin: 5px 0 0; font-size: 16px; opacity: 0.9; }
            .spinner {
                margin-top: 20px;
                border: 5px solid #fff;
                border-top: 5px solid #ff9900;
                border-radius: 50%;
                width: 40px; height: 40px;
                animation: spin 1s linear infinite;
            }
            @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        </style>
        <script>
            setTimeout(function() {
                window.location.href = "<?php echo addslashes($redirect); ?>";
            }, 1200); // 1.2s delay
        </script>
    </head>
    <body>
        <div class="overlay">
            <img src="images/<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p>₹ <?php echo number_format($product['price'], 2); ?> added to cart</p>
            <div class="spinner"></div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Fallback
header("Location: index.php");
exit;
?>
