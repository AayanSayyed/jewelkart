<?php
session_start();
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$host="localhost"; $user="root"; $pass=""; $dbname="jewelkartt";
$conn = new mysqli($host,$user,$pass,$dbname);
if ($conn->connect_error) die("DB Connection Failed: ".$conn->connect_error);

if (!isset($_SESSION['email'])) {
    echo "<p style='color:red;text-align:center;'>❌ No email found. Please register first.</p>";
    exit;
}

$email = $_SESSION['email'];
$showPopup = false;
$popupMsg = "";

// Display session message from resend_otp.php
if(isset($_SESSION['msg'])){
    $popupMsg = $_SESSION['msg'];
    $showPopup = true;
    unset($_SESSION['msg']);
}

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = trim($_POST['otp']);

    $stmt = $conn->prepare("SELECT id, otp FROM users WHERE email=? AND email_verified=0");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if ($user['otp'] === $otp) {
            $update = $conn->prepare("UPDATE users SET email_verified=1, otp=NULL WHERE id=?");
            $update->bind_param("i",$user['id']);
            $update->execute();

            unset($_SESSION['email']);
            $popupMsg = "✅ OTP verified successfully! Redirecting to login...";
            $showPopup = true;
            // redirect after 3 seconds
            echo "<meta http-equiv='refresh' content='3;url=login.php'>";
        } else {
            $popupMsg = "❌ Invalid OTP. Please try again.";
            $showPopup = true;
        }
    } else {
        $popupMsg = "❌ No pending verification found.";
        $showPopup = true;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>OTP Verification - Jewelkart</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        body { margin:0; padding:0; font-family:"Segoe UI",sans-serif; background:linear-gradient(135deg,#000,#2c2c2c); color:#fff; }
        .otp-box { max-width:400px; margin:100px auto; padding:30px; background:#fff; color:#000; border-radius:15px; box-shadow:0 6px 20px rgba(0,0,0,0.6);}
        h2 { text-align:center; font-weight:bold; margin-bottom:20px;}
        label { font-weight:600; color:#333; }
        .w3-input { background:#f9f9f9; }
        .w3-button { width:100%; font-size:16px; background:#000 !important; color:#fff !important; margin-top:10px;}
        .w3-button:hover { background:#333 !important;}
        .footer-link { text-align:center; margin-top:15px;}
        .footer-link a { color:#555; font-weight:bold; text-decoration:none;}
        .footer-link a:hover { color:#000; text-decoration:underline;}

        /* Popup styles */
        .popup {
            display:none;
            position: fixed;
            z-index: 100;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            color: #000;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.5);
            text-align: center;
        }
        .popup.show { display:block; }
        .popup p { margin:0; font-size:16px; font-weight:bold; }
    </style>
</head>
<body>

<div class="w3-container">
    <div class="w3-card-4 otp-box w3-animate-opacity">
        <h2>OTP Verification</h2>
        <p class="w3-center">We have sent a One-Time Password (OTP) to your email: <b><?php echo $email; ?></b></p>
        <form method="POST" class="w3-container">
            <p>
                <label>Enter OTP</label>
                <input class="w3-input w3-border w3-round" type="text" name="otp" placeholder="6-digit OTP" required>
            </p>
            <p>
                <button type="submit" class="w3-button w3-round-large w3-padding">Verify OTP</button>
            </p>
        </form>
        <div class="footer-link">
            <p>Didn't receive OTP? <a href="resend_otp.php">Resend OTP</a></p>
        </div>
    </div>
</div>

<!-- Popup -->
<div id="popup" class="popup">
    <p id="popup-msg"><?php echo $popupMsg; ?></p>
</div>

<script>
    <?php if($showPopup): ?>
        let popup = document.getElementById('popup');
        popup.classList.add('show');
        setTimeout(()=>{ popup.classList.remove('show'); }, 3000);
    <?php endif; ?>
</script>

</body>
</html>
