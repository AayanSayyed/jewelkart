<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "jewelkartt";
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("DB Connection Failed: " . $conn->connect_error);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Bracelets - Jewelkart</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css" />
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
  <style>
    /* Image Container for consistent height */
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
        object-fit: cover; /* keeps grid neat */
        object-position: center;
        display: block;
    }

    /* Product Grid */
    .w3-row-padding { margin: 0 -8px; }
    .w3-third { width: 32%; margin-bottom: 16px; float: left; padding: 0 8px; box-sizing: border-box; }
    @media(max-width:768px){.w3-third{width:48%;}}
    @media(max-width:480px){.w3-third{width:100%;}}
    .w3-container h3, .w3-container h6 { margin: 5px 0; }
    .w3-button { border-radius: 5px; transition: background-color 0.3s; text-align: center; display: block; }
    .w3-button:hover { background-color: #ff6600 !important; color: #fff !important; }
    .w3-opacity { opacity: 0.7; }

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
</head>
<body>

<!-- Back Button -->
<a href="index.php" class="back-btn"><span class="material-icons">arrow_back</span></a>

<?php include('navbar.php'); ?>

<div class="w3-content" style="max-width:1536px">
  <h2 class="w3-center w3-margin-top">Pendants</h2>
  <div class="w3-row-padding w3-padding-16">
    <?php
    // ✅ fetch products properly
    $res = $conn->prepare("SELECT id, name, price, image FROM products WHERE category='Pendants' AND status='Available'");
    $res->execute();
    $result = $res->get_result();

    if($result->num_rows === 0){
        echo "<p class='w3-center'>No products available in this category.</p>";
    } else {
        while($row = $result->fetch_assoc()){
            ?>
            <div class="w3-third w3-margin-bottom">
                <div class="image-container">
                    <!-- Clicking image goes to product page -->
                    <a href="product.php?id=<?php echo $row['id']; ?>">
                        <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
                    </a>
                </div>
                <div class="w3-container w3-white">
                    <!-- Clicking name also goes to product page -->
                    <h3>
                      <a href="product.php?id=<?php echo $row['id']; ?>" style="text-decoration:none;color:#000;">
                        <?php echo htmlspecialchars($row['name']); ?>
                      </a>
                    </h3>
                    <h6 class="w3-opacity">₹ <?php echo $row['price']; ?></h6>
                    
                    <!-- Add to Cart form -->
                    <form action="add_to_cart.php" method="POST">
                        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="w3-button w3-black w3-block w3-margin-bottom">Add to cart</button>
                    </form>
                </div>
            </div>
            <?php
        }
    }
    ?>
  </div>
</div>

<script>
  function toggleMenu() {
    const nav = document.getElementById("navLinks");
    nav.classList.toggle("active");
  }

  function toggleDropdown(event) {
    event.preventDefault();
    document.getElementById("catalogueDropdown").style.display =
      document.getElementById("catalogueDropdown").style.display === "block"
        ? "none"
        : "block";
  }

  function toggleProfile(event) {
    event.preventDefault();
    document.getElementById("profileDropdown").style.display =
      document.getElementById("profileDropdown").style.display === "block"
        ? "none"
        : "block";
  }

  // Close dropdowns when clicking outside
  window.onclick = function(e) {
    if (!e.target.matches('.dropbtn') && !e.target.matches('.profile-menu')) {
      let dropdowns = document.querySelectorAll(".dropdown-content");
      dropdowns.forEach(dd => dd.style.display = "none");
    }
  }
</script>

<script>
function scrollToCatalogue() {
    const catalogue = document.getElementById('catalogue');
    catalogue.scrollIntoView({ behavior: 'smooth' });
}
</script>

</body>
</html>
