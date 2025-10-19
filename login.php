<?php
session_start();

// 🔹 Database Connection
$host = "localhost";
$user = "root"; 
$pass = "";
$dbname = "jewelkartt";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("DB Connection Failed: " . $conn->connect_error);

$msg = "";

// 🔹 Handle Login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (!password_verify($password, $user['password'])) {
            $msg = "<p class='w3-text-red'>❌ Invalid password.</p>";
        } elseif ($user['email_verified'] == 0) {
            $_SESSION['email'] = $email;
            header("Location: verify_otp.php");
            exit;
        } else {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit;
        }
    } else {
        $msg = "<p class='w3-text-red'>❌ Email not found.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login - Jewelkart</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <style>
      body {
          margin: 0; padding: 0;
          font-family: "Segoe UI", sans-serif;
          background: linear-gradient(135deg, #000000, #2c2c2c);
          color: #fff;
      }
      .login-box {
          max-width: 420px; margin: 80px auto; padding: 30px;
          background: #fff; color: #000;
          border-radius: 15px; box-shadow: 0 6px 20px rgba(0,0,0,0.6);
      }
      h2 { font-weight: bold; color: #000; text-align: center; }
      label { color: #333; font-weight: 600; }
      .w3-input { background: #f9f9f9; }
      .w3-button { width:100%; font-size:16px; background:#000 !important; color:#fff !important; margin-top:10px; }
      .w3-button:hover { background:#333 !important; }
      .footer-link { text-align:center; margin-top:15px; }
      .footer-link a { color:#555; font-weight:bold; text-decoration:none; }
      .footer-link a:hover { color:#000; text-decoration:underline; }
      .google-btn { display:flex; align-items:center; justify-content:center; width:100%; padding:10px; margin-top:10px; border-radius:8px; border:1px solid #ccc; background:#fff; font-size:16px; font-weight:bold; color:#444; cursor:pointer; text-decoration:none; }
      .google-btn img { width:20px; margin-right:10px; }
      .google-btn:hover { background:#f1f1f1; }
  </style>
</head>
<body>

<div class="w3-container">
    <div class="w3-card-4 login-box w3-animate-opacity">
        <h2>Login</h2>
        <hr>
        <?php if (!empty($msg)) echo $msg; ?>
        
        <form method="POST" class="w3-container">
            <p>
                <label><b>Email</b></label>
                <input class="w3-input w3-border w3-round" type="email" name="email" placeholder="Enter Email" required>
            </p>
            <p>
                <label><b>Password</b></label>
                <input class="w3-input w3-border w3-round" type="password" name="password" placeholder="Enter Password" required>
            </p>
            <p class="w3-center">
                <button type="submit" class="w3-button w3-round-large w3-padding">Login</button>
            </p>
        </form>

        <!-- 🔹 Google Login Button -->
        <div class="w3-center">
            <p>Or login with</p>
            <a href="google_login.php" class="google-btn">
                <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google">
                Continue with Google
            </a>
        </div>

        <!-- 🔹 Forgot password and register links -->
        <div class="w3-center w3-padding">
            <p><a href="forgot_password.php" class="footer-link">Forgot Password?</a></p>
            <p>Don’t have an account? <a href="register.php" class="footer-link">Register</a></p>
        </div>
    </div>
</div>

</body>
</html>
