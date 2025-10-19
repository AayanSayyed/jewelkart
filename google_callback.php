<?php
require 'vendor/autoload.php';
session_start();

// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "jewelkartt";
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("DB Connection Failed: " . $conn->connect_error);

$client = new Google_Client();
$client->setClientId("");
$client->setClientSecret("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");
$client->setRedirectUri("http://localhost/E-commerce/google_callback.php");
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token["error"])) {
        $client->setAccessToken($token['access_token']);
        $oauth = new Google_Service_Oauth2($client);
        $profile = $oauth->userinfo->get();

        $google_id = $profile->id;
        $name = $profile->name;
        $email = $profile->email;

        // Check if user already exists
        $sql = "SELECT * FROM users WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            // User exists → log them in
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
        } else {
            // New user → insert into DB
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, '', NOW())");
            $stmt->bind_param("ss", $name, $email);
            $stmt->execute();
            $newUserId = $stmt->insert_id;
            $_SESSION['user_id'] = $newUserId;
            $_SESSION['username'] = $name;
        }

        header("Location: index.php");
        exit();
    } else {
        echo "Login failed: " . $token["error"];
    }
} else {
    echo "No code parameter from Google.";
}
