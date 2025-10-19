<?php
session_start();

// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "jewelkartt";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("DB Connection Failed: " . $conn->connect_error);

// Get search term
$searchTerm = trim($_GET['query'] ?? '');
if ($searchTerm === '') {
    echo "<p class='w3-center w3-text-red'>Please enter a search term.</p>";
    exit;
}

// Prevent SQL Injection
$searchTermLike = "%".$conn->real_escape_string($searchTerm)."%";

// Search products
$sql = "SELECT * FROM products WHERE (name LIKE ? OR category LIKE ?) AND status='Available'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $searchTermLike, $searchTermLike);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Search Results - Jewelkart</title>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
<link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css" />
<link rel="stylesheet" href="style.css">
<style>
body { font-family: Arial; margin: 0; padding: 0; background-color: #fff; color: #000; }

/* Headings */
h3, .product-title { color: #000; } 
h3 { text-align: center; margin: 20px 0; }

/* Navbar */
nav { display: flex; align-items: center; justify-content: space-between; background: #222; padding: 10px 20px; color: #fff; flex-wrap: wrap; }
nav .logo { font-size: 22px; display: flex; align-items: center; gap: 5px; }
nav .nav-links { display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
.dropdown { position: relative; display: inline-block; }
.dropdown-content { display: none; position: absolute; background-color: #333; min-width: 160px; z-index: 1; }
.dropdown-content a { color: white; padding: 10px 15px; text-decoration: none; display: block; }
.dropdown-content a:hover { background-color: #444; }
.profile-menu, .dropbtn { cursor: pointer; color: #fff; text-decoration: none; }
.cart-link { position: relative; display: inline-block; }
.cart-count { position: absolute; top: -8px; right: -10px; background: red; color: white; font-size: 12px; font-weight: bold; padding: 2px 6px; border-radius: 50%; line-height: 1; }

/* Search Form */
.search-form { display: flex; align-items: center; border-radius: 25px; overflow: hidden; border: 1px solid #ccc; background-color: #fff; }
.search-form input { flex: 1; border: none; outline: none; padding: 5px 15px; height: 35px; border-radius: 25px 0 0 25px; font-size: 14px; background-color: #222; color: #fff; }
.search-form button { background-color: #111; border: none; cursor: pointer; padding: 0 15px; display: flex; align-items: center; justify-content: center; border-radius: 0 25px 25px 0; height: 35px; transition: background-color 0.3s; color: #fff; }
.search-form button:hover { background-color: #333; }
.search-form button .material-icons { color: #fff; font-size: 20px; }

/* Product Grid */
.w3-row-padding { margin: 0 -8px; }
.w3-third { width: 32%; margin-bottom: 16px; float: left; padding: 0 8px; box-sizing: border-box; }
@media(max-width:768px){.w3-third{width:48%;}}
@media(max-width:480px){.w3-third{width:100%;}}
.w3-white { background-color: #fff; padding: 16px; }
.w3-container h3, .w3-container h6 { margin: 5px 0; }
.product-title { font-weight: bold; }
.w3-button { border-radius: 5px; transition: background-color 0.3s; text-align: center; display: block; }
.w3-button:hover { background-color: #ff6600 !important; color: #fff !important; }
.w3-opacity { opacity: 0.7; }

/* Image Container */
.image-container {
    width: 100%;            
    height: 260px;          
    overflow: hidden;       
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f5f5f5; 
    border-radius: 10px 10px 0 0; 
}
.image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;      
    object-position: center; 
    display: block;
}
</style>
</head>
<body>

<!-- Navbar -->
<nav>
  <div class="logo"><span class="material-icons">store</span> Jewelkart</div>
  <div class="nav-links">

    <!-- Search -->
    <form method="GET" action="search.php" class="search-form">
        <input type="text" name="query" placeholder="Search products..." required>
        <button type="submit"><span class="material-icons">search</span></button>
    </form>

    <!-- User/Profile -->
    <?php if (isset($_SESSION['username'])): ?>
        <div class="dropdown">
            <span class="profile-menu" onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='block'?'none':'block'">
                <span class="material-icons">person</span> <?php echo htmlspecialchars($_SESSION['username']); ?> ▼
            </span>
            <div class="dropdown-content">
                <a href="orders.php">My Orders</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    <?php else: ?>
        <a href="login.php"><span class="material-icons">person</span> Login</a>
        <a href="register.php">Register</a>
    <?php endif; ?>

    <!-- Catalogue -->
    <div class="dropdown">
        <span class="dropbtn" onclick="this.nextElementSibling.style.display=this.nextElementSibling.style.display==='block'?'none':'block'">Catalogue ▼</span>
        <div class="dropdown-content">
            <a href="bracelets.php">Bracelets</a>
            <a href="earrings.php">Earrings</a>
            <a href="rings.php">Rings</a>
            <a href="pendants.php">Pendants</a>
        </div>
    </div>

    <a href="#about">About</a>
    <a href="#contact">Contact</a>

    <!-- Cart -->
    <a href="cart.php" class="cart-link">
      <span class="material-icons">shopping_cart</span>
      <span class="cart-count">
        <?php 
          if (isset($_SESSION['user_id'])) {
              $res = $conn->query("SELECT SUM(quantity) AS total FROM cart WHERE user_id=".((int)$_SESSION['user_id']));
              $row = $res->fetch_assoc();
              echo $row['total'] ? $row['total'] : "0";
          } else { echo "0"; }
        ?>
      </span>
    </a>

  </div>
</nav>

<!-- Search Results -->
<h3>Search Results for '<?php echo htmlspecialchars($searchTerm); ?>'</h3>

<?php if($result->num_rows === 0): ?>
    <p class="w3-center w3-text-red">No products found.</p> 
<?php else: ?>
    <div class="w3-row-padding w3-padding-16">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="w3-third w3-margin-bottom">
                <div class="image-container">
                    <img src="images/<?php echo $row['image']; ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                </div>
                <div class="w3-container w3-white">
                    <h3 class="product-title"><?php echo htmlspecialchars($row['name']); ?></h3>
                    <h6 class="w3-opacity">₹<?php echo $row['price']; ?></h6>

                    <?php if(isset($_SESSION['user_id'])): ?>
                        <form action="add_cart.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="w3-button w3-black w3-block w3-margin-bottom">
                                Add to Cart
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="login.php" class="w3-button w3-blue w3-block w3-margin-bottom">Login to Add</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php endif; ?>

<?php
$stmt->close();
$conn->close();
?>

</body>
</html>
