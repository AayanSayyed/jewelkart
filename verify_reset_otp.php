<?php
session_start();
if(!isset($_SESSION['reset_email'])){
    header("Location: forgot_password.php");
    exit;
}

$email = $_SESSION['reset_email'];
$msg = "";

$host="localhost"; $user="root"; $pass=""; $dbname="jewelkartt";
$conn = new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error) die("DB Connection Failed: ".$conn->connect_error);

if($_SERVER['REQUEST_METHOD']=='POST'){
    $otp = trim($_POST['otp']);

    $stmt = $conn->prepare("SELECT id, otp FROM users WHERE email=?");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows == 1){
        $user = $result->fetch_assoc();
        if($user['otp']===$otp){
            $_SESSION['otp_verified'] = true;
            header("Location: reset_password.php");
            exit;
        } else {
            $msg = "<p class='w3-text-red'>❌ Invalid OTP</p>";
        }
    } else {
        $msg = "<p class='w3-text-red'>❌ Email not found</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Verify OTP - Jewelkart</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-container w3-center" style="margin-top:100px;">
    <div class="w3-card-4 w3-padding w3-white w3-round-large" style="max-width:400px;margin:auto;">
        <h2>Verify OTP</h2>
        <p>An OTP has been sent to: <b><?php echo $email; ?></b></p>
        <?php if(!empty($msg)) echo $msg; ?>
        <form method="POST">
            <p>
                <input class="w3-input w3-border w3-round" type="text" name="otp" placeholder="Enter OTP" required>
            </p>
            <p>
                <button class="w3-button w3-black w3-round-large" type="submit">Verify OTP</button>
            </p>
        </form>
        <p><a href="forgot_password.php">Back</a></p>
    </div>
</body>
</html>
