<?php
// Remove session_start() here because main pages already start the session
?>
<nav>
  <div class="logo"><span class="material-icons">store</span> Jewelkart</div>
  <span class="hamburger material-icons" onclick="toggleMenu()">menu</span>

  <div class="nav-links" id="navLinks">
    <?php if (isset($_SESSION['username'])): ?>
      <div class="dropdown">
        <a href="#" class="profile-menu" onclick="toggleProfile(event)">
          <span class="material-icons">person</span> 
          <?php echo htmlspecialchars($_SESSION['username']); ?> ▼
        </a>
        <div class="dropdown-content" id="profileDropdown">
          <a href="orders.php">My Orders</a>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    <?php else: ?>
      <a href="login.php"><span class="material-icons">person</span> Login</a>
      <a href="register.php">Register</a>
    <?php endif; ?>

    <div class="dropdown">
      <a href="#" class="dropbtn" onclick="toggleDropdown(event)">Catalogue ▼</a>
      <div class="dropdown-content" id="catalogueDropdown">
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
          if(isset($_SESSION['user_id'])){
            $conn = new mysqli("localhost","root","","jewelkartt");
            if($conn->connect_error) die("DB failed: " . $conn->connect_error);

            $res = $conn->query("SELECT SUM(quantity) AS total FROM cart WHERE user_id=".((int)$_SESSION['user_id']));
            $row = $res->fetch_assoc();
            echo $row['total'] ? $row['total'] : "0";
          } else {
            echo "0";
          }
        ?>
      </span>
    </a>
  </div>
</nav>

<style>
/* Navbar Styling */
nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #222;
  padding: 10px 20px;
  color: #fff;
  position: relative;
}
nav .logo { font-size: 22px; display: flex; align-items: center; gap: 5px; }
nav .nav-links { display: flex; align-items: center; gap: 15px; }
nav a, .dropbtn, .profile-menu { color: #fff; text-decoration: none; cursor: pointer; }
.dropdown { position: relative; }
.dropdown-content {
  display: none;
  position: absolute;
  background: #333;
  min-width: 160px;
  z-index: 1000;
  top: 100%;
  left: 0;
}
.dropdown-content a { display: block; padding: 10px 15px; color: #fff; text-decoration: none; }
.dropdown-content a:hover { background-color: #444; }
.hamburger { display: none; cursor: pointer; }

/* Cart Styling */
.cart-link {
  position: relative;
  display: inline-block;
  color: #fff;
  text-decoration: none;
}
.cart-link .cart-count {
  position: absolute;
  top: -6px;   /* moves above cart */
  right: -8px; /* moves to the corner */
  background: red;
  color: #fff;
  font-size: 12px;
  font-weight: bold;
  padding: 2px 6px;
  border-radius: 50%;
  line-height: 1;
}

/* Responsive */
@media(max-width:768px){
  .hamburger { display: block; }
  .nav-links { 
    display: none; 
    flex-direction: column;
    background: #222;
    width: 100%;
    position: absolute;
    top: 100%;
    left: 0;
  }
  .nav-links.active { display: flex; }
  .nav-links a, .dropdown { padding: 10px; }
  .dropdown-content { position: relative; top: 0; left: 0; min-width: 100%; }
}
</style>

<script>
function toggleMenu() {
  document.getElementById("navLinks").classList.toggle("active");
}

function toggleDropdown(e) {
  e.preventDefault();
  const dd = document.getElementById("catalogueDropdown");
  dd.style.display = dd.style.display === "block" ? "none" : "block";
}

function toggleProfile(e) {
  e.preventDefault();
  const dd = document.getElementById("profileDropdown");
  dd.style.display = dd.style.display === "block" ? "none" : "block";
}

// Close dropdowns if clicked outside
window.onclick = function(e) {
  if (!e.target.matches('.dropbtn') && !e.target.matches('.profile-menu')) {
    document.getElementById("catalogueDropdown").style.display = "none";
    document.getElementById("profileDropdown").style.display = "none";
  }
}
</script>
