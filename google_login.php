<?php
require 'vendor/autoload.php';
session_start();

$client = new Google_Client();
$client->setClientId("");
$client->setClientSecret("");
$client->setRedirectUri("");
$client->addScope("email");
$client->addScope("profile");

// Generate login URL
$login_url = $client->createAuthUrl();

// 🚀 Redirect user directly to Google login
header("Location: " . $login_url);
exit();
