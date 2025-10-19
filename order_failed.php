<?php
$error = isset($_GET['error']) ? urldecode($_GET['error']) : "Unknown error";
?>
<!DOCTYPE html>
<html>
<head>
  <title>Payment Failed - JewelKart</title>
</head>
<body>
  <h2>❌ Payment Failed</h2>
  <p>Reason: <?php echo htmlspecialchars($error); ?></p>
  <a href="checkout.php">🔄 Try Again</a> | <a href="index.php">🏠 Go Home</a>
</body>
</html>
