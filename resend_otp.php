<?php
session_start();
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$host="localhost"; $user="root"; $pass=""; $dbname="jewelkartt";
$conn = new mysqli($host,$user,$pass,$dbname);
if ($conn->connect_error) die("DB Connection Failed: ".$conn->connect_error);

if(!isset($_SESSION['email'])){
    $_SESSION['msg'] = "<p class='w3-text-red'>❌ No email found. Please register first.</p>";
    header("Location: verify_otp.php");
    exit;
}

$email = $_SESSION['email'];

// Generate new 6-digit OTP
$otp = rand(100000, 999999);

// Update OTP in database
$update = $conn->prepare("UPDATE users SET otp=? WHERE email=? AND email_verified=0");
$update->bind_param("is",$otp,$email);
$update->execute();

// Send OTP via PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host       = "smtp.gmail.com";
    $mail->SMTPAuth   = true;
    $mail->Username   = "jewelkart41@gmail.com"; // your Gmail
    $mail->Password   = "aobxqejksmdtphhv";      // App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom("jewelkart41@gmail.com","Jewelkart");
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = "Your Jewelkart OTP";
    $mail->Body    = "
        <h3>Hi,</h3>
        <p>Your new OTP is: <b>$otp</b></p>
        <p>Use this OTP to verify your account.</p>
        <br><p>Regards,<br>Jewelkart Team</p>
    ";
    $mail->AltBody = "Your new OTP is: $otp";

    $mail->send();
    $_SESSION['msg'] = "<p class='w3-text-green'>✅ New OTP has been sent to your email.</p>";
} catch(Exception $e){
    $_SESSION['msg'] = "<p class='w3-text-red'>❌ OTP could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
}

header("Location: verify_otp.php");
exit;
