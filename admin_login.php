<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// Clear any previous admin session
if(isset($_SESSION['admin_logged_in'])) {
    unset($_SESSION['admin_logged_in']);
}

// Admin credentials
$admin_user = "admin";
$admin_pass = "admin123"; // change to strong password

$msg = "";

// Handle POST login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === $admin_user && $password === $admin_pass) {
        $_SESSION['admin_logged_in'] = true;
        // Redirect to admin panel
        header("Location: admin_panel.php");
        exit();
    } else {
        $msg = "<p class='w3-text-red w3-center'>❌ Invalid username or password</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Jewelkart</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <style>
        body {
            background-color: #111; /* Dark background */
            color: #fff;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
        }
        .login-card input {
            background-color: #f4f4f4;
            color: #000;
        }
        .login-card button {
            background-color: #fff;
            color: #111;
        }
    </style>
</head>
<body>

<div class="w3-container">
    <div class="w3-card w3-dark-grey w3-padding-32 w3-round-xlarge w3-animate-top login-card">
        <h2 class="w3-center w3-text-white">🔑 Admin Login</h2>
        <hr class="w3-white">
        <?php if($msg) echo $msg; ?>

        <form method="POST" class="w3-container">
            <p>
                <input class="w3-input w3-border w3-round w3-light-grey" type="text" name="username" placeholder="Username" required>
            </p>
            <p>
                <input class="w3-input w3-border w3-round w3-light-grey" type="password" name="password" placeholder="Password" required>
            </p>
            <p class="w3-center">
                <button type="submit" class="w3-button w3-white w3-text-black w3-round-large w3-padding">Login</button>
            </p>
        </form>
    </div>
</div>

</body>
</html>
