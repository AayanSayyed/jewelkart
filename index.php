<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Jewelkart - Online Jewelry Store</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/5/w3.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />

    <style>
      .dropdown {
        position: relative;
        display: inline-block;
      }
      .dropdown-content {
        display: none;
        position: absolute;
        background-color: #222;
        min-width: 160px;
        z-index: 1;
      }
      .dropdown-content a {
        color: white;
        padding: 10px 15px;
        text-decoration: none;
        display: block;
      }
      .dropdown-content a:hover {
        background-color: #444;
      }
      .profile-menu {
        color: white;
        cursor: pointer;
      }
.cart-link {
  position: relative;
  display: inline-block;
}

.cart-count {
  position: absolute;
  top: -8px;   /* moves it above the cart */
  right: -10px; /* moves it to the corner */
  background: red;
  color: white;
  font-size: 12px;
  font-weight: bold;
  padding: 2px 6px;
  border-radius: 50%;
  line-height: 1;

}

.search-form {
  display: flex;
  align-items: center;
  margin-right: 10px;
  border-radius: 25px;           
  overflow: hidden;               
  border: 1px solid #555;         
  background-color: #111;         
}

.search-form input {
  flex: 1;
  border: none;
  outline: none;
  padding: 5px 15px;
  height: 35px;
  border-radius: 25px 0 0 25px;  
  font-size: 14px;
  background-color: #222;         /* Slightly lighter dark gray */
  color: #fff;                    
}

.search-form input::placeholder {
  color: #bbb;                    
}

.search-form button {
  background-color: #111;         
  border: none;
  cursor: pointer;
  padding: 0 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 0 25px 25px 0;  
  height: 35px;
  color: #fff;             /* Make text white */
  transition: background-color 0.3s;
}

.search-form button:hover {
  background-color: #333;         
}

.search-form button .material-icons {
  color: #fff;                    
  font-size: 20px;
}

/* img contener */
/* Container for all product images */
.image-container {
    width: 100%;            
    height: 260px;          /* Slightly taller for better visibility */
    overflow: hidden;       
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f5f5f5; 
    border-radius: 10px 10px 0 0; 
}

/* Make images cover the container */
.image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;      
    object-position: center; 
    display: block;
}

.logo {
  display: flex;
  align-items: center; /* vertically center in navbar */
  height: 60px; /* navbar height */
}

.logo-img {
  height: 55px;  /* visible but not too big */
  max-height: 60px;
  display: block;
}

nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  height: 60px;
  background-color:black;
  position: relative; /* add this */
  z-index: 999;       /* optional, keep on top */
}

/* Mobile menu */
.nav-links.active {
  display: flex;
  flex-direction: column;
  position: absolute;
  top: 60px;      /* below navbar */
  left: 0;
  width: 100%;
  background-color: #111; /* solid background */
  padding: 15px 0;
}
/* Nav container */
.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 20px;
    background: #fff;
    position: relative;
    z-index: 10;
}

/* Nav links container */
.nav-links {
    display: flex;
    align-items: center;
    gap: 20px;
}

/* Common animation for nav elements */
.nav-links > * {
    opacity: 0;
    transform: translateX(50px); /* start from right */
    animation: navSlideIn 0.8s ease-out forwards;
}

/* Stagger each element with a small delay */
.nav-links > *:nth-child(1) { animation-delay: 0.2s; }
.nav-links > *:nth-child(2) { animation-delay: 0.4s; }
.nav-links > *:nth-child(3) { animation-delay: 0.6s; }
.nav-links > *:nth-child(4) { animation-delay: 0.8s; }
.nav-links > *:nth-child(5) { animation-delay: 1s; }
.nav-links > *:nth-child(6) { animation-delay: 1.2s; }

/* Slide-in keyframes */
@keyframes navSlideIn {
    0% {
        opacity: 0;
        transform: translateX(50px);
    }
    60% {
        opacity: 1;
        transform: translateX(-10px); /* slight overshoot */
    }
    80% {
        transform: translateX(5px); /* settle back */
    }
    100% {
        opacity: 1;
        transform: translateX(0);
    }
}

/* Optional: hamburger icon */
.hamburger {
    cursor: pointer;
    font-size: 28px;
    user-select: none;
    animation: navSlideIn 0.8s ease-out forwards;
    animation-delay: 0.1s;
}
/* Base styles */
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 20px;
  background: #fff;
  border-bottom: 1px solid #ddd;
}

/* Hide nav links on small screens */
.nav-links {
  display: flex;
  gap: 15px;
}

.hamburger {
  display: none; /* hidden on desktop */
  cursor: pointer;
}

@media (max-width: 768px) {
  .nav-links {
    display: none;
    flex-direction: column;
    align-items: stretch; /* stretch children to full width */
    background: #1e1e1e; /* cleaner dark background */
    color: #fff;
    position: absolute;
    top: 60px;
    right: 10px;
    width: 270px;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.35);
    z-index: 1000;
  }

  .nav-links a {
    color: #fff;
    padding: 10px 12px;
    text-decoration: none;
    display: block;
    border-radius: 6px;
    transition: background 0.3s;
  }

  .nav-links a:hover {
    background: #333;
    color: #f5c542;
  }

  /* Search form inside dropdown */
  .nav-links form {
    display: flex;
    width: 100%;
    margin-bottom: 15px;
    gap: 8px;
  }

  .nav-links form input {
    flex: 1;
    padding: 10px;
    border-radius: 6px;
    border: 1px solid #444;
    background: #2a2a2a;
    color: #fff;
  }

  .nav-links form button {
    padding: 10px 14px;
    border: none;
    border-radius: 6px;
    background: #f5c542;
    color: #222;
    font-weight: bold;
    cursor: pointer;
  }

  /* Cart link style */
  .cart-link {
    display: flex;
    align-items: center;
    gap: 6px;
    position: relative;
    padding: 10px 12px;
    color: #fff;
  }

  .cart-link .material-icons {
    font-size: 22px;
  }

  .cart-count {
    background: #f5c542;
    color: #222;
    font-size: 12px;
    font-weight: bold;
    border-radius: 50%;
    padding: 3px 7px;
    position: absolute;
    top: 6px;
    right: 6px;
  }

  /* Show menu when toggled */
  .nav-links.show {
    display: flex;
  }

  .hamburger {
    display: block;
    font-size: 30px;
    color: #222;
  }
}


    </style>
  </head>
  <body>
<nav class="navbar">
  <div class="logo"><img src="images/gm_logo_jk.png" alt="JewelKart Logo" class="logo-img"></div>
  <span class="hamburger material-icons" onclick="toggleMenu()">menu</span>

  <div class="nav-links" id="navLinks">

    <!-- Search Bar -->
    <form method="GET" action="search.php" class="search-form">
      <input type="text" name="query" placeholder="Search products...">
      <button type="submit">Search</button>
    </form>

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

    <a href="cart.php" class="cart-link">
      <span class="material-icons">shopping_cart</span>
      <span class="cart-count">
        <?php 
          if (isset($_SESSION['user_id'])) {
              $conn = new mysqli("localhost", "root", "", "jewelkartt");
              if ($conn->connect_error) { die("DB failed: " . $conn->connect_error); }
              $user_id = (int) $_SESSION['user_id'];
              $res = $conn->query("SELECT SUM(quantity) AS total FROM cart WHERE user_id=$user_id");
              $row = $res->fetch_assoc();
              echo $row['total'] ? $row['total'] : "0";
          } else { echo "0"; }
        ?>
      </span>
    </a>

  </div>
</nav>

<section class="hero">
  <div class="hero-content">
    <h1 class="hero-title">Elegant Jewelry for Every Occasion</h1>
    <p class="hero-subtitle">Discover handcrafted collections made with love and luxury.</p>
    <button onclick="scrollToCatalogue()" class="hero-btn">Shop Now</button>
  </div>
</section>

<style>
/* Navbar slide-down faster */
.navbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  height: 60px;
  background-color: black;
  color: white;
  transform: translateY(-100px);
  opacity: 0;
  animation: slideDown 0.5s forwards ease-out 0.1s; /* faster */
}

@keyframes slideDown {
  to { transform: translateY(0); opacity: 1; }
}

/* Hero Section */
.hero {
  background: url('images/silver-jewelry-collection-antique-traditional-old-wodden-53619308.webp') center/cover no-repeat;
  text-align: center;
  padding: 150px 20px;
  color: white;
  overflow: hidden;
}

.hero-title {
  font-size: 3em;
  letter-spacing: 2px;
  opacity: 0;
  transform: translateY(50px);
  animation: fadeUp 0.5s forwards ease-out 0.2s; /* faster & sooner */
}

.hero-subtitle {
  font-size: 1.5em;
  opacity: 0;
  transform: translateY(50px);
  animation: fadeUp 0.5s forwards ease-out 0.4s; /* faster & sooner */
}

.hero-btn {
  margin-top: 25px;
  padding: 14px 40px;
  font-size: 1.2em;
  border: none;
  background-color: #fff;
  color: #111;
  cursor: pointer;
  font-weight: bold;
  border-radius: 30px;
  opacity: 0;
  transform: translateY(50px);
  animation: fadeUp 0.5s forwards ease-out 0.6s; /* faster & sooner */
  transition: transform 0.2s, box-shadow 0.2s;
}

.hero-btn:hover {
  transform: scale(1.05);
  box-shadow: 0 8px 20px rgba(0,0,0,0.3);
}

/* Fade up animation */
@keyframes fadeUp {
  to { opacity: 1; transform: translateY(0); }
}

/* Optional: typing effect faster */
.hero-title span {
  border-right: 2px solid #fff;
  display: inline-block;
  animation: typing 1.5s steps(40, end), blink 0.5s step-end infinite; /* faster typing & blink */
}

@keyframes typing { from { width: 0; } to { width: 100%; } }
@keyframes blink { 50% { border-color: transparent; } }
</style>


<script>
// Navbar & Dropdown
function toggleDropdown(event){ event.preventDefault(); document.getElementById("catalogueDropdown").style.display =
  document.getElementById("catalogueDropdown").style.display==="block"?"none":"block"; }
function toggleProfile(event){ event.preventDefault(); document.getElementById("profileDropdown").style.display =
  document.getElementById("profileDropdown").style.display==="block"?"none":"block"; }
function toggleMenu(){ document.getElementById("navLinks").classList.toggle("active"); }
window.onclick = function(e){
  if(!e.target.matches('.dropbtn') && !e.target.matches('.profile-menu')){
    let dropdowns = document.querySelectorAll(".dropdown-content");
    dropdowns.forEach(dd => dd.style.display = "none");
  }
}
function scrollToCatalogue(){ document.getElementById('catalogue').scrollIntoView({ behavior:'smooth' }); }
</script>


<div class="w3-content" style="max-width: 1532px">

  <div class="w3-container w3-margin-top" id="rooms">
    <h3 align="center" id="catalogue">Catalogue</h3>
    <p>
      Make yourself shine is our slogan. We offer the finest silver jewellery crafted with care. Wear beautifully, feel confidently.
    </p>

    <!-- Product Rows -->
    <div class="w3-row-padding w3-margin-top product-row">
      <div class="w3-third w3-margin-bottom product-card">
        <div class="image-container">
          <a href="product_detail.php?id=1"><img src="images/ring-1.png" alt="Ring"></a>
        </div>
        <div class="w3-container w3-white">
          <h3><a href="product_detail.php?id=1" class="product-title">Eternal Starlight Solitaire Ring</a></h3>
          <h6 class="w3-opacity">₹ 46,573</h6>
          <form action="add_to_cart.php" method="POST">
            <input type="hidden" name="product_id" value="1">
            <button type="submit" class="w3-button w3-block w3-black">Add to cart</button>
          </form>
        </div>
      </div>

      <div class="w3-third w3-margin-bottom product-card">
        <div class="image-container">
          <a href="product_detail2.php?id=2"><img src="images/server-ring.png" alt="Ring"></a>
        </div>
        <div class="w3-container w3-white">
          <h3><a href="product_detail2.php?id=2" class="product-title">Vintage Charm Diamond Finger Ring</a></h3>
          <h6 class="w3-opacity">₹ 40,499</h6>
          <form action="add_to_cart.php" method="POST">
            <input type="hidden" name="product_id" value="2">
            <button type="submit" class="w3-button w3-block w3-black">Add to cart</button>
          </form>
        </div>
      </div>

      <div class="w3-third w3-margin-bottom product-card">
        <div class="image-container">
          <a href="product_detail3.php?id=3"><img src="images/ring-3.png" alt="Ring"></a>
        </div>
        <div class="w3-container w3-white">
          <h3><a href="product_detail3.php?id=3" class="product-title">Radiance Royale Solitaire Ring</a></h3>
          <h6 class="w3-opacity">₹ 55,699</h6>
          <form action="add_to_cart.php" method="POST">
            <input type="hidden" name="product_id" value="3">
            <button type="submit" class="w3-button w3-block w3-black">Add to cart</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Second Row -->
    <div class="w3-row-padding w3-padding-16 product-row">
      <div class="w3-third w3-margin-bottom product-card">
        <div class="image-container">
          <a href="product_detail4.php?id=4"><img src="images/haaar1.jpg" alt="Necklace"></a>
        </div>
        <div class="w3-container w3-white">
          <h3><a href="product_detail4.php?id=4" class="product-title">Twinkle Berry Diamond Necklace</a></h3>
          <h6 class="w3-opacity">₹ 80,669</h6>
          <form action="add_to_cart.php" method="POST">
            <input type="hidden" name="product_id" value="4">
            <button type="submit" class="w3-button w3-block w3-black">Add to cart</button>
          </form>
        </div>
      </div>

      <div class="w3-third w3-margin-bottom product-card">
        <div class="image-container">
          <a href="product_detail5.php?id=5"><img src="images/haar2.jpg" alt="Necklace"></a>
        </div>
        <div class="w3-container w3-white">
          <h3><a href="product_detail5.php?id=5" class="product-title">Orbit Bloom Silver Necklace</a></h3>
          <h6 class="w3-opacity">₹ 77,599</h6>
          <form action="add_to_cart.php" method="POST">
            <input type="hidden" name="product_id" value="5">
            <button type="submit" class="w3-button w3-block w3-black">Add to cart</button>
          </form>
        </div>
      </div>

      <div class="w3-third w3-margin-bottom product-card">
        <div class="image-container">
          <a href="product_detail6.php?id=6"><img src="images/haar3.jpg" alt="Necklace"></a>
        </div>
        <div class="w3-container w3-white">
          <h3><a href="product_detail6.php?id=6" class="product-title">Haloed Radiance Diamond Necklace</a></h3>
          <h6 class="w3-opacity">₹ 1,98,599</h6>
          <form action="add_to_cart.php" method="POST">
            <input type="hidden" name="product_id" value="6">
            <button type="submit" class="w3-button w3-block w3-black">Add to cart</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Third Row -->
    <div class="w3-row-padding w3-padding-16 product-row">
      <div class="w3-third w3-margin-bottom product-card">
        <div class="image-container">
          <a href="product_detail7.php?id=7"><img src="images/jhumkaaa1.webp" alt="Earrings"></a>
        </div>
        <div class="w3-container w3-white">
          <h3><a href="product_detail7.php?id=7" class="product-title">Haloed Radiance Diamond Necklace</a></h3>
          <h6 class="w3-opacity">₹ 65,599</h6>
          <form action="add_to_cart.php" method="POST">
            <input type="hidden" name="product_id" value="7">
            <button type="submit" class="w3-button w3-block w3-black">Add to cart</button>
          </form>
        </div>
      </div>

      <div class="w3-third w3-margin-bottom product-card">
        <div class="image-container">
          <a href="product_detail8.php?id=8"><img src="images/jhumkaa2.jpg" alt="Earrings"></a>
        </div>
        <div class="w3-container w3-white">
          <h3><a href="product_detail8.php?id=8" class="product-title">Cupid's Delight Diamond Earring</a></h3>
          <h6 class="w3-opacity">₹ 45,899</h6>
          <form action="add_to_cart.php" method="POST">
            <input type="hidden" name="product_id" value="8">
            <button type="submit" class="w3-button w3-block w3-black">Add to cart</button>
          </form>
        </div>
      </div>

      <div class="w3-third w3-margin-bottom product-card">
        <div class="image-container">
          <a href="product_detail9.php?id=9"><img src="images/jhumka3.jpg" alt="Earrings"></a>
        </div>
        <div class="w3-container w3-white">
          <h3><a href="product_detail9.php?id=9" class="product-title">Radiant Single Line Diamond Earring</a></h3>
          <h6 class="w3-opacity">₹ 30,599</h6>
          <form action="add_to_cart.php" method="POST">
            <input type="hidden" name="product_id" value="9">
            <button type="submit" class="w3-button w3-block w3-black">Add to cart</button>
          </form>
        </div>
      </div>
    </div>

  </div>
</div>

<style>
/* Row-wise animation */
.product-row {
  opacity: 0;
  transform: translateY(50px);
  transition: all 1s ease-out; /* increased duration */
}

.product-row.visible {
  opacity: 1;
  transform: translateY(0);
}

/* Remove card hover transform and shadow */
.product-card {
  transition: none; /* no hover effect on card itself */
}

/* Image hover only */
.image-container img {
  width: 100%;
  border-radius: 12px;
  transition: transform 0.4s ease, box-shadow 0.4s ease;
}

.image-container img:hover {
  transform: scale(1.1);
  box-shadow: 0 15px 25px rgba(0,0,0,0.2);
}

/* Product title underline on hover */
.product-title {
  position: relative;
  text-decoration: none;
  color: black;
  display: inline-block;
  transition: color 0.3s ease;
}

.product-title::after {
  content: "";
  position: absolute;
  width: 0;
  height: 2px;
  left: 0;
  bottom: -4px;
  background-color: #000;
  transition: width 0.4s ease;
}

.product-title:hover {
  color: #111;
}

.product-title:hover::after {
  width: 100%;
}

/* Title and paragraph animation */
#catalogue, #rooms p {
  opacity: 0;
  transform: translateY(20px);
  transition: opacity 0.8s ease-out, transform 0.8s ease-out; /* slightly slower */
}

#catalogue.visible, #rooms p.visible {
  opacity: 1;
  transform: translateY(0);
}
</style>

<script>
function isInViewport(el) {
  const rect = el.getBoundingClientRect();
  return rect.top < window.innerHeight && rect.bottom > 0;
}

function animateRows() {
  const rows = document.querySelectorAll('.product-row');
  const title = document.getElementById('catalogue');
  const para = document.querySelector('#rooms p');

  if(title && isInViewport(title)) title.classList.add('visible');
  if(para && isInViewport(para)) para.classList.add('visible');

  rows.forEach((row, index) => {
    if(isInViewport(row)) {
      setTimeout(() => row.classList.add('visible'), index * 400); // slower stagger
    }
  });
}

window.addEventListener('load', animateRows);
window.addEventListener('scroll', animateRows);
</script>




     <div class="w3-row-padding" id="about">
  <div class="w3-col l4 12 about-text">
    <h3>About</h3>
    <h6 class="about-paragraph">
      Welcome to our world of timeless beauty and elegance. Our silver jewellery store is one of a kind, offering pieces that are not only crafted with precision but also designed to tell a story. Each ring, necklace, and bracelet is made with care, combining tradition with modern artistry to bring you jewellery that feels truly personal. Whether you’re searching for something bold, delicate, or uniquely stylish, we create designs that make you shine in every moment.
    </h6>
    <h6 class="about-paragraph">
      We believe jewellery is more than just an accessory—it’s an expression of your personality, your memories, and your milestones. That’s why every silver piece in our collection is designed to connect with you. Imagine slipping on a silver ring that reminds you of your first celebration, gifting a pendant that carries a secret meaning, or wearing a bracelet that instantly boosts your confidence. Our jewellery isn’t just about beauty; it’s about the stories you create while wearing it.
    </h6>
    <h6 class="about-paragraph">
      Quality is at the heart of everything we do. We use only premium-grade silver, carefully polished to perfection, so that your jewellery not only sparkles but also lasts for generations. From minimal everyday wear to statement pieces for special occasions, our collection is versatile and thoughtfully curated to suit every mood and style.
    </h6>
    <p class="about-paragraph">
      We accept: <i class="fa fa-credit-card w3-large"></i>
      <i class="fa fa-cc-mastercard w3-large"></i>
      <i class="fa fa-cc-amex w3-large"></i>
      <i class="fa fa-cc-cc-visa w3-large"></i>
      <i class="fa fa-cc-paypal w3-large"></i>
    </p>
  </div>

  <div class="w3-col l8 12 about-image">
    <img src="images/fear-model-about-cr.png" class="w3-image w3-greyscale" style="width: 100%" />
  </div>
</div>

<div class="w3-row-padding w3-large w3-center" style="margin: 32px 0">
  <div class="w3-third about-paragraph">
    <i class="fa fa-map-marker w3-text-red"></i> 423 KP street, Chicago, US
  </div>
  <div class="w3-third about-paragraph">
    <i class="fa fa-phone w3-text-red"></i> Phone: +91 7774073427
  </div>
  <div class="w3-third about-paragraph">
    <i class="fa fa-envelope w3-text-red"></i> Email: jwelkart@gmail.com
  </div>
</div>

<div class="w3-panel w3-red w3-leftbar w3-padding-32 about-paragraph">
  <h6>
    <i class="fa fa-info w3-deep-orange w3-padding w3-margin-right"></i>
    On demand, we can offer custom silver rings, personalized name pendants, elegant bracelets, couple bands, and more
  </h6>
</div>

<style>
/* Animate text and image */
.about-text, .about-image {
  opacity: 0;
  transform: translateX(-50px);
  transition: all 0.8s ease-out;
}

.about-image {
  transform: translateX(50px); /* image slides from right */
}

.about-text.visible, .about-image.visible {
  opacity: 1;
  transform: translateX(0);
}

/* Animate paragraphs individually */
.about-paragraph {
  opacity: 0;
  transform: translateX(-50px);
  transition: all 0.7s ease-out;
}

.about-paragraph.visible {
  opacity: 1;
  transform: translateX(0);
}

/* Fast animation for bottom section */
.about-bottom {
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.5s ease-out;
}

.about-bottom.visible {
  opacity: 1;
  transform: translateY(0);
}

/* Optional hover for icons */
.about-text i, .about-bottom i {
  transition: transform 0.3s ease;
}

.about-text i:hover, .about-bottom i:hover {
  transform: scale(1.2);
}
</style>

<script>
// Check if element is in viewport
function isInViewport(el) {
  const rect = el.getBoundingClientRect();
  return rect.top < window.innerHeight && rect.bottom > 0;
}

// Animate About Section
function animateAbout() {
  const textContainer = document.querySelector('.about-text');
  const image = document.querySelector('.about-image');
  const paragraphs = document.querySelectorAll('.about-paragraph');
  const bottomElements = document.querySelectorAll('.about-bottom');

  if (textContainer && isInViewport(textContainer)) textContainer.classList.add('visible');
  if (image && isInViewport(image)) image.classList.add('visible');

  paragraphs.forEach((para, index) => {
    if(isInViewport(para)) {
      setTimeout(() => para.classList.add('visible'), index * 200); // faster stagger
    }
  });

  bottomElements.forEach(el => {
    if(isInViewport(el)) {
      el.classList.add('visible'); // fast, no stagger
    }
  });
}

// Trigger animations on load and scroll
window.addEventListener('load', animateAbout);
window.addEventListener('scroll', animateAbout);
</script>


      <style>
/* Heading animation */
.display-heading {
  opacity: 0;
  transform: translateY(20px);
  transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.display-heading.visible {
  opacity: 1;
  transform: translateY(0);
}

/* Gallery images animation */
.display-gallery img {
  opacity: 0;
  transform: translateX(0) translateY(40px);
  transition: all 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Different directions for each image */
.display-gallery img.from-left { transform: translateX(-50px); }
.display-gallery img.from-right { transform: translateX(50px); }
.display-gallery img.from-top { transform: translateY(-50px); }
.display-gallery img.from-bottom { transform: translateY(50px); }

.display-gallery img.visible {
  opacity: 1;
  transform: translateX(0) translateY(0);
}
</style>

<div class="w3-container">
  <h3 class="display-heading">Our Display</h3>
  <h6 class="display-heading">You can find our hot selling jewelleris in the world:</h6>
</div>

<div class="w3-row-padding w3-padding-16 w3-text-white w3-large display-gallery">
  <div class="w3-half w3-margin-bottom">
    <div class="w3-display-container">
      <img src="images/dmc.jpg" alt="Cinque Terre" class="from-left" style="width:100%" />
      <span class="w3-display-bottomleft w3-padding">Cinque Terre</span>
    </div>
  </div>
  <div class="w3-half">
    <div class="w3-row-padding" style="margin:0 -16px">
      <div class="w3-half w3-margin-bottom">
        <div class="w3-display-container">
          <img src="images/fear-ac.webp" alt="New York" class="from-right" style="width:100%" />
          <span class="w3-display-bottomleft w3-padding">New York</span>
        </div>
      </div>
      <div class="w3-half w3-margin-bottom">
        <div class="w3-display-container">
          <img src="images/dark_ac.avif" alt="San Francisco" class="from-top" style="width:100%" />
          <span class="w3-display-bottomleft w3-padding">San Francisco</span>
        </div>
      </div>
    </div>
    <div class="w3-row-padding" style="margin:0 -16px">
      <div class="w3-half w3-margin-bottom">
        <div class="w3-display-container">
          <img src="images/dark-ac3.jpg" alt="Pisa" class="from-bottom" style="width:100%" />
          <span class="w3-display-bottomleft w3-padding">Pisa</span>
        </div>
      </div>
      <div class="w3-half w3-margin-bottom">
        <div class="w3-display-container">
          <img src="images/fear-ac3.jpg" alt="Paris" class="from-left" style="width:100%" />
          <span class="w3-display-bottomleft w3-padding">India</span>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function isInViewport(el) {
  const rect = el.getBoundingClientRect();
  return rect.top < window.innerHeight && rect.bottom > 0;
}

function animateGallery() {
  // Animate headings
  document.querySelectorAll('.display-heading').forEach(h => {
    if(isInViewport(h)) h.classList.add('visible');
  });

  // Animate each image individually with slower, smoother stagger
  document.querySelectorAll('.display-gallery img').forEach((img, index) => {
    if(isInViewport(img)) {
      setTimeout(() => img.classList.add('visible'), index * 180); // slower stagger
    }
  });
}

window.addEventListener('load', animateGallery);
window.addEventListener('scroll', animateGallery);
</script>


<style>
/* Flash Sale container animation */
.flash-sale {
  opacity: 0;
  transform: translateY(30px);
  transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

.flash-sale.visible {
  opacity: 1;
  transform: translateY(0);
}

/* Countdown styling (simple black) */
#countdown {
  display: inline-block;
  font-size: 2.2em;
  font-weight: bold;
  letter-spacing: 3px;
  background: #fff;
  padding: 12px 28px;
  border: 1px solid #ccc;
  font-family: 'Courier New', monospace;
  color: #111; /* simple black */
}

/* Button hover effect */
.flash-sale button:hover {
  transform: scale(1.05);
  transition: transform 0.3s ease;
}
</style>

<div class="w3-container w3-padding-48 w3-center w3-card flash-sale" 
     style="margin: 32px 0; 
            background: linear-gradient(135deg, #ffffff, #f2f2f2); 
            color: #111; 
            border-radius: 0; 
            border: 1px solid #ddd;">

  <h2 style="color: #111; font-size: 2em; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">
    Flash Sale Ends Soon
  </h2>
  <p style="font-size: 1.1em; margin-bottom: 20px; color:#444;">
    Grab exclusive jewelry at unbeatable prices.
  </p>

  <!-- Countdown (static or dynamic) -->
  <div id="countdown">12:34:56</div>

  <br>
  <button class="w3-button w3-black w3-large" 
          style="padding: 12px 32px; font-weight: bold; border-radius: 0; border:1px solid #000;"
          onclick="document.getElementById('catalogue').scrollIntoView({ behavior: 'smooth' });">
    Shop the Sale
  </button>
</div>

<script>
// Animate Flash Sale section when in viewport
function isInViewport(el) {
  const rect = el.getBoundingClientRect();
  return rect.top < window.innerHeight && rect.bottom > 0;
}

function animateFlashSale() {
  const flashSale = document.querySelector('.flash-sale');
  if(flashSale && isInViewport(flashSale)) flashSale.classList.add('visible');
}

window.addEventListener('load', animateFlashSale);
window.addEventListener('scroll', animateFlashSale);
</script>

<style>
/* Start hidden */
#contact:not(.visible),
#contact form:not(.visible),
footer:not(.visible) {
  opacity: 0;
}

/* Contact: Slide in from left */
#contact.visible {
  animation: slideInLeft 1s ease-out forwards;
}

/* Form: Zoom bounce */
#contact form.visible {
  animation: zoomBounce 0.8s ease-out forwards;
}

/* Footer: Flip-up */
footer.visible {
  animation: flipUp 1s ease-out forwards;
}

/* Social icons bounce one by one */
footer .w3-xlarge i {
  opacity: 0;
  display: inline-block;
  transform: translateY(20px) scale(0.8);
}

footer .w3-xlarge i.animated {
  animation: iconBounce 0.6s ease-out forwards; /* 👈 keep final state */
}

footer .w3-xlarge i:nth-child(1).animated { animation-delay: 0.3s; }
footer .w3-xlarge i:nth-child(2).animated { animation-delay: 0.45s; }
footer .w3-xlarge i:nth-child(3).animated { animation-delay: 0.6s; }
footer .w3-xlarge i:nth-child(4).animated { animation-delay: 0.75s; }
footer .w3-xlarge i:nth-child(5).animated { animation-delay: 0.9s; }
footer .w3-xlarge i:nth-child(6).animated { animation-delay: 1.05s; }

/* Keyframes */
@keyframes slideInLeft {
  from { opacity: 0; transform: translateX(-100px); }
  to { opacity: 1; transform: translateX(0); }
}

@keyframes zoomBounce {
  0% { opacity: 0; transform: scale(0.7); }
  70% { opacity: 1; transform: scale(1.1); }
  100% { transform: scale(1); }
}

@keyframes flipUp {
  from { opacity: 0; transform: rotateX(90deg); transform-origin: bottom; }
  to { opacity: 1; transform: rotateX(0); transform-origin: bottom; }
}

@keyframes iconBounce {
  0% { opacity: 0; transform: translateY(20px) scale(0.8); }
  60% { opacity: 1; transform: translateY(-5px) scale(1.1); }
  100% { opacity: 1; transform: translateY(0) scale(1); } /* 👈 ensure opacity:1 */
}

/* Input focus effect */
#contact input:focus, #contact textarea:focus {
  border-color: #111;
  box-shadow: 0 2px 8px rgba(0,0,0,0.2);
  outline: none;
  transition: all 0.3s ease;
}

/* Button hover */
#contact button:hover {
  transform: scale(1.08) rotate(-1deg);
  background: #333;
  transition: transform 0.3s ease, background 0.3s ease;
}
</style>

<div class="w3-container" id="contact">
  <h2>Contact</h2>
  <p>If you have any questions, do not hesitate to ask them.</p>
  <i class="fa fa-map-marker w3-text-red" style="width: 30px"></i> Chicago, US<br />
  <i class="fa fa-phone w3-text-red" style="width: 30px"></i> Phone: +91 7774073427<br />
  <i class="fa fa-envelope w3-text-red" style="width: 30px"></i> Email: jwelkart@gmail.com<br />

  <form action="save_message.php" method="POST">
    <p><input class="w3-input w3-padding-16 w3-border" type="text" placeholder="Name" required name="name"></p>
    <p><input class="w3-input w3-padding-16 w3-border" type="email" placeholder="Email" required name="email"></p>
    <p><textarea class="w3-input w3-padding-16 w3-border" placeholder="Message" required name="message" rows="4"></textarea></p>
    <p><button class="w3-button w3-black w3-padding-large" type="submit">SEND MESSAGE</button></p>
  </form>
</div>

<footer class="w3-padding-32 w3-black w3-center w3-margin-top">
  <h5>Find Us On</h5>
  <div class="w3-xlarge w3-padding-16">
    <i class="fa fa-facebook-official w3-hover-opacity"></i>
    <i class="fa fa-instagram w3-hover-opacity"></i>
    <i class="fa fa-snapchat w3-hover-opacity"></i>
    <i class="fa fa-pinterest-p w3-hover-opacity"></i>
    <i class="fa fa-twitter w3-hover-opacity"></i>
    <i class="fa fa-linkedin w3-hover-opacity"></i>
  </div>
</footer>

<script>
function isInViewport(el) {
  const rect = el.getBoundingClientRect();
  return rect.top < window.innerHeight && rect.bottom > 0;
}

function animateContactFooter() {
  const contact = document.getElementById('contact');
  const form = contact.querySelector('form');
  const footer = document.querySelector('footer');

  if(contact && isInViewport(contact)) contact.classList.add('visible');
  if(form && isInViewport(form)) form.classList.add('visible');
  if(footer && isInViewport(footer)) {
    footer.classList.add('visible');

    // Animate icons only once
    const icons = footer.querySelectorAll('.w3-xlarge i');
    icons.forEach((icon, index) => {
      if (!icon.classList.contains('animated')) {
        setTimeout(() => icon.classList.add('animated'), 300 + index * 150);
      }
    });
  }
}

window.addEventListener('load', animateContactFooter);
window.addEventListener('scroll', animateContactFooter);
</script>



   <!-- ✅ keep your full catalogue + about + footer sections here (same as before) -->
   <!-- I only replaced the navbar/profile part with PHP -->
<script>
  // Simple countdown (24 hours from now)
  var countdownDate = new Date().getTime() + (24*60*60*1000);
  var x = setInterval(function() {
    var now = new Date().getTime();
    var distance = countdownDate - now;
    var hours = Math.floor((distance % (1000*60*60*24))/(1000*60*60));
    var minutes = Math.floor((distance % (1000*60*60))/(1000*60));
    var seconds = Math.floor((distance % (1000*60))/1000);

    document.getElementById("countdown").innerHTML =
      (hours<10 ? "0"+hours : hours) + " : " +
      (minutes<10 ? "0"+minutes : minutes) + " : " +
      (seconds<10 ? "0"+seconds : seconds);

    if (distance < 0) {
      clearInterval(x);
      document.getElementById("countdown").innerHTML = "EXPIRED";
    }
  }, 1000);
</script>
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
<script>
function toggleMenu() {
  const navLinks = document.getElementById("navLinks");
  navLinks.classList.toggle("show");
}
</script>

  </body>
</html>
