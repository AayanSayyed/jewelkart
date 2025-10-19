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

    // Check if product exists
    $stmt = $conn->prepare("SELECT id FROM products WHERE id=?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
        $stmt->close();
        die("Product not found.");
    }
    $stmt->close();

    // Check if product is already in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        // Update quantity
        $newQty = $row['quantity'] + 1;
        $upd = $conn->prepare("UPDATE cart SET quantity=? WHERE id=?");
        $upd->bind_param("ii", $newQty, $row['id']);
        $upd->execute();
        $upd->close();
    } else {
        // Insert new row
        $ins = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $ins->bind_param("ii", $user_id, $product_id);
        $ins->execute();
        $ins->close();
    }
    $stmt->close();

    // Redirect back to the page user came from
    $redirectUrl = $_SERVER['HTTP_REFERER'] ?? 'index.php'; // fallback to home
    // Add query param to show alert
    $redirectUrl .= (strpos($redirectUrl, '?') === false ? '?' : '&') . "added=1";
    header("Location: $redirectUrl");
    exit;
}

// Fallback redirect
header("Location: index.php");
exit;
?>
