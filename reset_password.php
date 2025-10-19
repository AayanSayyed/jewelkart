<?php
session_start();
if(!isset($_SESSION['reset_email']) || !isset($_SESSION['otp_verified'])){
    header("Location: forgot_password.php");
    exit;
}

$email = $_SESSION['reset_email'];
$msg = "";

$host="localhost"; $user="root"; $pass=""; $dbname="jewelkartt";
$conn = new mysqli($host,$user,$pass,$dbname);
if($conn->connect_error) die("DB Connection Failed: ".$conn->connect_error);

if($_SERVER['REQUEST_METHOD']=='POST'){
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);

    if($password!==$confirm){
        $msg = "<p class='w3-text-red'>❌ Passwords do not match</p>";
    } else {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $update = $conn->prepare("UPDATE users SET password=?, otp=NULL WHERE email=?");
        $update->bind_param("ss",$hashed,$email);
        if($update->execute()){
            unset($_SESSION['reset_email']);
            unset($_SESSION['otp_verified']);
            $msg = "<p class='w3-text-green'>✅ Password reset successfully! <a href='login.php'>Login here</a>.</p>";
        } else {
            $msg = "<p class='w3-text-red'>❌ Error resetting password</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password - Jewelkart</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-container w3-center" style="margin-top:100px;">
    <div class="w3-card-4 w3-padding w3-white w3-round-large" style="max-width:400px;margin:auto;">
        <h2>Reset Password</h2>
        <?php if(!empty($msg)) echo $msg; ?>
        <form method="POST">
            <p>
                <input class="w3-input w3-border w3-round" type="password" name="password" placeholder="New Password" required>
            </p>
            <p>
                <input class="w3-input w3-border w3-round" type="password" name="confirm" placeholder="Confirm Password" required>
            </p>
            <p>
                <button class="w3-button w3-black w3-round-large" type="submit">Reset Password</button>
            </p>
        </form>
    </div>
</body>
</html>
