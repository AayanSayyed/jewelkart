<?php
session_start();

// DB Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "jewelkartt";
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("DB Connection Failed: " . $conn->connect_error);

// Validate product id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("❌ Invalid Product ID");
}
$id = intval($_GET['id']);

// Fetch product
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$product = $res->fetch_assoc();
if (!$product) die("❌ Product not found!");

// Handle multiple images
$product_images = [];
if (!empty($product['image'])) $product_images[] = $product['image'];
if (!empty($product['images'])) {
    $extra = explode(",", $product['images']);
    foreach($extra as $img) {
        if (!empty(trim($img))) $product_images[] = trim($img);
    }
}

// Fetch related products
$related = [];
$stmtRel = $conn->prepare("SELECT id, name, price, image FROM products WHERE id != ? ORDER BY RAND() LIMIT 4");
$stmtRel->bind_param("i", $id);
$stmtRel->execute();
$resRel = $stmtRel->get_result();
while($row = $resRel->fetch_assoc()) $related[] = $row;
$stmtRel->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo htmlspecialchars($product['name']); ?> - Jewelkart</title>
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
 <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
body { font-family:"Segoe UI",sans-serif; background:#f8f8f8; margin:0; color:#333; }
.container { max-width: 1200px; margin:50px auto; padding:0 20px; display:flex; gap:40px; flex-wrap:wrap; }

/* Product Image Section */
.product-images { flex:1; min-width:350px; }
.main-image { width:100%; height:400px; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.2); object-fit:contain; background:#fff; }
.thumbnails { display:flex; gap:10px; margin-top:10px; flex-wrap:wrap; }
.thumbnails img { width:70px; height:70px; object-fit:cover; border-radius:8px; cursor:pointer; border:2px solid transparent; transition:0.3s; }
.thumbnails img.active { border-color:#28a745; }

/* Product Details */
.product-details { flex:1; min-width:350px; display:flex; flex-direction:column; justify-content:space-between; }
.product-details h1 { margin-top:0; font-size:2em; }
.product-details p.price { font-size:1.5em; font-weight:bold; margin:10px 0; color:#e91e63; }
.product-details p.stock { font-weight:bold; margin-bottom:20px; }
.product-details p.category { margin-bottom:10px; font-style:italic; }
.product-details p.prod-description { font-size:1em; line-height:1.6; margin-bottom:20px; color:#555; }

/* Buttons */
.action-buttons { margin-top:20px; }
.action-buttons button { width:100%; padding:12px; font-size:1em; border:none; border-radius:8px; cursor:pointer; background:#28a745; color:#fff; }
.action-buttons button:hover { background:#1c7c31; }

/* Related Section */
.related-section { margin-top:50px; }
.related-section h3 { margin-bottom:20px; }
.related-products { display:flex; flex-wrap:wrap; gap:20px; }
.related-card { background:#fff; border-radius:12px; padding:10px; text-align:center; flex:1 1 200px; box-shadow:0 5px 15px rgba(0,0,0,0.2); }
.related-card img { width:100%; height:150px; object-fit:cover; border-radius:8px; }
.related-card h4 { margin:10px 0 5px; font-size:1em; }
.related-card p { font-weight:bold; color:#e91e63; margin:0; }
.related-card form { margin-top:10px; }
.related-card button { background:#28a745; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; }
.related-card button:hover { background:#1c7c31; }

@media(max-width:768px){
    .container { flex-direction:column; align-items:center; }
    .product-details { text-align:center; }
    .thumbnails { justify-content:center; }
}
/* Back Button */
    .back-btn {
        position: fixed;
        top: 80px; /* pushed a bit lower so it won’t clash with navbar */
        left: 20px;
        width: 45px;
        height: 45px;
        background: #000;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        transition: background 0.3s;
        z-index: 2000; /* stays above navbar */
    }
    .back-btn:hover {
        background: #ff6600;
    }
</style>
<script>
function changeImage(src, thumb){
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumbnails img').forEach(img=>img.classList.remove('active'));
    thumb.classList.add('active');
}
</script>
</head>
<body>
<a href="index.php" class="back-btn"><span class="material-icons">arrow_back</span></a>

<div class="container w3-animate-opacity">
    <!-- Product Images -->
    <div class="product-images">
        <img id="mainImage" class="main-image" src="images/<?php echo htmlspecialchars($product_images[0]); ?>" alt="Product Image">
        <div class="thumbnails">
            <?php foreach($product_images as $index => $img): ?>
                <img src="images/<?php echo htmlspecialchars($img); ?>" 
                     onclick="changeImage(this.src,this)" 
                     class="<?php echo $index==0?'active':''; ?>">
            <?php endforeach; ?>
        </div>
    </div>

  
    <!-- Product Details -->
    <div class="product-details">
        <div>
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="price">₹<?php echo number_format($product['price'], 2); ?></p>
            <p class="stock"><?php echo ($product['status']=="Available") ? "Available" : "Out of Stock"; ?></p>
            <p class="category"><strong>Category:</strong> <?php echo htmlspecialchars($product['category']); ?></p>

            <!-- ✅ Custom description for Eternal Starlight Solitaire Ring -->
            <?php if($product['name'] == 'Haloed Radiance Diamond Necklace'): ?>
                <p class="prod-description">
A dazzling diamond necklace that captures the essence of elegance and sophistication. The Twinkle Berry Diamond Necklace showcases intricately set diamonds that sparkle with every movement, making it perfect for formal events or special occasions. Designed to sit gracefully along the neckline, it enhances any outfit with timeless charm. Adjustable in length for a perfect fit, this necklace combines comfort with luxury, making every moment shine brilliantly. A statement piece that reflects style and refinement.

                    

                </p>
            <?php endif; ?>

         <br>
         <br>

        <!-- Add to Cart -->
        <div class="action-buttons">
            <form method="POST" action="add_to_cart.php">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <button type="submit">🛒 Add to Cart</button>
            </form>
        </div>
    </div>
</div>

<!-- Related Products -->
<div class="related-section w3-container">
    <h3>suggested for you </h3>
    <div class="related-products">
        <?php foreach($related as $rel): ?>
            <div class="related-card">
                <img src="images/<?php echo htmlspecialchars($rel['image']); ?>" alt="<?php echo htmlspecialchars($rel['name']); ?>">
                <h4><?php echo htmlspecialchars($rel['name']); ?></h4>
                <p>₹<?php echo number_format($rel['price'],2); ?></p>
                <form method="POST" action="add_to_cart.php">
                    <input type="hidden" name="product_id" value="<?php echo $rel['id']; ?>">
                    <button type="submit">🛒 Add to Cart</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
