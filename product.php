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

// Handle multiple images safely
$product_images = [];
if (!empty($product['image'])) $product_images[] = trim($product['image']);

if (!empty($product['gallery_images'])) {
    $gallery = json_decode($product['gallery_images'], true);
    if (is_array($gallery)) {
        foreach ($gallery as $gimg) {
            if (!empty($gimg)) $product_images[] = trim($gimg);
        }
    }
}

if (empty($product_images)) $product_images[] = 'placeholder.jpg';

// First batch of related products
$related = [];
$stmtRel = $conn->prepare("SELECT id, name, price, image, gallery_images FROM products WHERE id != ? ORDER BY RAND() LIMIT 8");
$stmtRel->bind_param("i", $id);
$stmtRel->execute();
$resRel = $stmtRel->get_result();
while ($row = $resRel->fetch_assoc()) {
    // Make sure related products have an image
    $rel_image = $row['image'];
    if (empty($rel_image) && !empty($row['gallery_images'])) {
        $gallery = json_decode($row['gallery_images'], true);
        if (is_array($gallery) && !empty($gallery[0])) {
            $rel_image = $gallery[0];
        }
    }
    $row['image'] = $rel_image ?: 'placeholder.jpg';
    $related[] = $row;
}
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
.product-images { flex:1; min-width:320px; }
.main-image { width:100%; height:400px; border-radius:12px; box-shadow:0 6px 15px rgba(0,0,0,0.15); object-fit:cover; background:#fff; }
.thumbnails { display:flex; gap:10px; margin-top:10px; flex-wrap:wrap; }
.thumbnails img { width:70px; height:70px; object-fit:cover; border-radius:8px; cursor:pointer; border:2px solid transparent; transition:0.3s; }
.thumbnails img.active { border-color:#28a745; }
.product-details { flex:1; min-width:320px; display:flex; flex-direction:column; justify-content:space-between; }
.product-details h1 { margin-top:0; font-size:2em; }
.product-details p.price { font-size:1.5em; font-weight:bold; margin:10px 0; color:#e91e63; }
.product-details p.stock { font-weight:bold; margin-bottom:20px; }
.product-details p.category { margin-bottom:10px; font-style:italic; }
.product-details p.prod-description { font-size:1em; line-height:1.6; margin-bottom:20px; color:#555; }
.action-buttons { margin-top:20px; text-align:left; }
.action-buttons button { 
    width:auto; min-width:180px; padding:12px 20px; font-size:1em; 
    border:none; border-radius:8px; cursor:pointer; 
    background:#28a745; color:#fff; display:inline-block;
}
.action-buttons button:hover { background:#1c7c31; }

/* Extra Info Section */
.extra-info { background:#fff; border-radius:12px; padding:20px; margin:30px auto; max-width:1200px; box-shadow:0 4px 12px rgba(0,0,0,0.1); }
.extra-info h3 { margin-bottom:15px; font-size:1.2em; text-align:center; }
.extra-info .features { display:flex; flex-wrap:wrap; justify-content:center; gap:30px; }
.extra-info .feature { flex:1 1 200px; display:flex; align-items:center; gap:10px; justify-content:center; }
.extra-info .feature i { font-size:28px; color:#28a745; }

/* Related Products */
.related-section { margin:50px auto; max-width:1200px; }
.related-section h3 { margin-bottom:20px; }
.related-products {
    display:flex;
    gap:15px;
    overflow-x:auto;
    white-space:nowrap;
    padding-bottom:10px;
    scrollbar-width:thin;
}
.related-products::-webkit-scrollbar { height:8px; }
.related-products::-webkit-scrollbar-thumb { background:#ccc; border-radius:10px; }
.related-card {
    background:#fff; border-radius:12px; padding:10px; 
    text-align:center; display:inline-block; width:200px; 
    flex:0 0 auto; box-shadow:0 4px 12px rgba(0,0,0,0.1);
}
.related-card img { width:100%; height:150px; object-fit:cover; border-radius:8px; }
.related-card h4 { margin:10px 0 5px; font-size:1em; white-space:normal; }
.related-card p { font-weight:bold; color:#e91e63; margin:0; }
.related-card form { margin-top:10px; }
.related-card button { background:#28a745; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; }
.related-card button:hover { background:#1c7c31; }

/* Responsive */
@media(max-width:768px){
    .container { flex-direction:column; align-items:center; }
    .product-details { text-align:center; }
    .thumbnails { justify-content:center; }
    .extra-info .features { flex-direction:column; gap:15px; }
}

/* Back Button */
.back-btn {
    position: fixed; top: 80px; left: 20px;
    width: 45px; height: 45px; background: #000; color: #fff;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    text-decoration: none; box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    transition: background 0.3s; z-index: 2000;
}
.back-btn:hover { background: #ff6600; }
</style>
<script>
function changeImage(src, thumb){
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.thumbnails img').forEach(img=>img.classList.remove('active'));
    thumb.classList.add('active');
}

// Infinite scroll for related products
let page = 1;
let loading = false;
const relatedContainer = document.addEventListener("DOMContentLoaded", () => {
    const relatedContainer = document.querySelector(".related-products");
    if (!relatedContainer) return;

    async function loadMoreProducts() {
        if (loading) return;
        loading = true;
        page++;

        const res = await fetch("related_ajax.php?id=<?php echo $id; ?>&page=" + page);
        const html = await res.text();
        if (html.trim() !== "") {
            relatedContainer.insertAdjacentHTML("beforeend", html);
            loading = false;
        }
    }

    relatedContainer.addEventListener("scroll", () => {
        if (relatedContainer.scrollLeft + relatedContainer.clientWidth >= relatedContainer.scrollWidth - 100) {
            loadMoreProducts();
        }
    });
});
</script>
</head>
<body>
<?php
// Detect the category and map it to the right page
$categoryPage = "index.php"; // default fallback
if (!empty($product['category'])) {
    $cat = strtolower($product['category']);
    if ($cat === "bracelets") $categoryPage = "bracelets.php";
    elseif ($cat === "earrings") $categoryPage = "earrings.php";
    elseif ($cat === "pendants") $categoryPage = "pendants.php";
    elseif ($cat === "rings") $categoryPage = "rings.php";
}
?>
<a href="<?php echo $categoryPage; ?>" class="back-btn">
    <span class="material-icons">arrow_back</span>
</a>


<!-- Product Section -->
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
            <p class="prod-description">
                <?php echo !empty($product['description']) ? nl2br(htmlspecialchars($product['description'])) : "No description available for this product."; ?>
            </p>
        </div>
        <div class="action-buttons">
            <form method="POST" action="add_to_cart.php">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <button type="submit">🛒 Add to Cart</button>
            </form>
        </div>
    </div>
</div>

<!-- Extra Info -->
<div class="extra-info w3-animate-opacity">
    <h3>Why buy from Jewelkart?</h3>
    <div class="features">
        <div class="feature"><i class="material-icons">local_shipping</i> <span>Free Delivery</span></div>
        <div class="feature"><i class="material-icons">assignment_return</i> <span>7 Days Replacement</span></div>
        <div class="feature"><i class="material-icons">verified_user</i> <span>Secure Payments</span></div>
        <div class="feature"><i class="material-icons">payment</i> <span>Cash on Delivery</span></div>
        <div class="feature"><i class="material-icons">support_agent</i> <span>24/7 Customer Support</span></div>
    </div>
</div>

<!-- Related Products -->
<div class="related-section w3-container">
    <h3>Suggested for you</h3>
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
