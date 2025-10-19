<?php
session_start();
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$host="localhost"; $user="root"; $pass=""; $dbname="jewelkartt";
$conn = new mysqli($host,$user,$pass,$dbname);
if ($conn->connect_error) die("DB Connection Failed: ".$conn->connect_error);

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);

    // Check if email exists and verified
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=? AND email_verified=1");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $otp = rand(100000,999999);
        $update = $conn->prepare("UPDATE users SET otp=? WHERE email=?");
        $update->bind_param("is",$otp,$email);
        $update->execute();

        // Send OTP via email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = "smtp.gmail.com";
            $mail->SMTPAuth   = true;
            $mail->Username   = "jewelkart41@gmail.com"; // Your Gmail
            $mail->Password   = "aobxqejksmdtphhv";      // App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom("jewelkart41@gmail.com","Jewelkart");
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Jewelkart Password Reset OTP";
            $mail->Body    = "<h3>Hi,</h3>
                              <p>Your OTP to reset your password is: <b>$otp</b></p>
                              <p>If you did not request this, ignore this email.</p>
                              <br><p>Jewelkart Team</p>";
            $mail->AltBody = "Your OTP is: $otp";

            $mail->send();
            $_SESSION['reset_email'] = $email;
            header("Location: verify_reset_otp.php");
            exit;
        } catch (Exception $e) {
            $msg = "<p class='w3-text-red'>❌ OTP could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
        }

    } else {
        $msg = "<p class='w3-text-red'>❌ Email not found or not verified.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password - Jewelkart</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body class="w3-container w3-center" style="margin-top:100px;">
    <div class="w3-card-4 w3-padding w3-white w3-round-large" style="max-width:400px;margin:auto;">
        <h2>Forgot Password</h2>
        <?php if(!empty($msg)) echo $msg; ?>
        <form method="POST">
            <p>
                <input class="w3-input w3-border w3-round" type="email" name="email" placeholder="Enter your registered email" required>
            </p>
            <p>
                <button class="w3-button w3-black w3-round-large" type="submit">Send OTP</button>
            </p>
        </form>
        <p><a href="login.php">Back to Login</a></p>
    </div>
</body>
</html>
