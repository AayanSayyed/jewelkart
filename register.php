<?php
session_start();
require 'vendor/autoload.php'; // PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// 🔹 Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "jewelkartt";
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("DB Connection Failed: " . $conn->connect_error);

// 🔹 Function to send OTP email
function sendOTPEmail($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = "smtp.gmail.com";
        $mail->SMTPAuth   = true;
        $mail->Username   = "jewelkart41@gmail.com"; // Your Gmail
        $mail->Password   = "aobxqejksmdtphhv";      // App password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom("jewelkart41@gmail.com", "Jewelkart");
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = "Your Jewelkart OTP";
        $mail->Body    = "<h3>Your OTP is: <b>$otp</b></h3><p>Enter this OTP to verify your account.</p>";
        $mail->AltBody = "Your OTP is: $otp";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// 🔹 Handle Registration
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $otp      = rand(100000, 999999); // 6-digit OTP

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $msg = "<p class='w3-text-red'>⚠️ Email already registered. Please login.</p>";
    } else {
        $sql = "INSERT INTO users (username, email, password, otp, email_verified) VALUES (?, ?, ?, ?, 0)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $username, $email, $password, $otp);

        if ($stmt->execute()) {
            if (sendOTPEmail($email, $otp)) {
                $_SESSION['email'] = $email;
                header("Location: verify_otp.php");
                exit;
            } else {
                $msg = "<p class='w3-text-orange'>⚠️ Registered but OTP not sent. Check SMTP settings.</p>";
            }
        } else {
            $msg = "<p class='w3-text-red'>❌ Error: " . $stmt->error . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Register - Jewelkart</title>
  <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
  <style>
      body {
          margin: 0;
          padding: 0;
          font-family: "Segoe UI", sans-serif;
          background: linear-gradient(135deg, #000000, #2c2c2c);
          color: #fff;
      }
      .register-box {
          max-width: 420px;
          margin: 80px auto;
          padding: 30px;
          background: #fff;
          color: #000;
          border-radius: 15px;
          box-shadow: 0 6px 20px rgba(0,0,0,0.6);
      }
      h2 { font-weight: bold; color: #000; }
      label { color: #333; font-weight: 600; }
      .w3-input { background: #f9f9f9; }
      .w3-button { width: 100%; font-size: 16px; background: #000 !important; color: #fff !important; }
      .w3-button:hover { background: #333 !important; }
      .footer-link { color: #555; font-weight: bold; }
      .footer-link:hover { color: #000; text-decoration: underline; }
      .google-btn { display: flex; align-items: center; justify-content: center; width: 100%; padding: 10px; margin-top: 10px; border-radius: 8px; border: 1px solid #ccc; background: #fff; font-size: 16px; font-weight: bold; color: #444; cursor: pointer; text-decoration: none; }
      .google-btn img { width: 20px; margin-right: 10px; }
      .google-btn:hover { background: #f1f1f1; }
  </style>
</head>
<body>

<div class="w3-container">
    <div class="w3-card-4 register-box w3-animate-opacity">
        <h2 class="w3-center">Register</h2>
        <hr>
        <?php if (!empty($msg)) echo $msg; ?>
        
        <form method="POST" class="w3-container">
            <p>
                <label><b>Username</b></label>
                <input class="w3-input w3-border w3-round" type="text" name="username" placeholder="Enter Username" required>
            </p>
            <p>
                <label><b>Email</b></label>
                <input class="w3-input w3-border w3-round" type="email" name="email" placeholder="Enter Email" required>
            </p>
            <p>
                <label><b>Password</b></label>
                <input class="w3-input w3-border w3-round" type="password" name="password" placeholder="Enter Password" required>
            </p>
            <p class="w3-center">
                <button type="submit" class="w3-button w3-round-large w3-padding">Register</button>
            </p>
        </form>
        <div class="w3-center">
            <p>Or login with</p>
            <a href="google_login.php" class="google-btn">
                <img src="https://developers.google.com/identity/images/g-logo.png" alt="Google">
                Continue with Google
            </a>
        </div>
        <div class="w3-center w3-padding">
            <p>Already have an account? <a href="login.php" class="footer-link">Login</a></p>
        </div>
    </div>
</div>

</body>
</html>
