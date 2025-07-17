<?php
// Enable error reporting for debugging (remove or comment out in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Use database credentials (update these with your actual DB info)
$servername = "localhost"; // or your database host
$username = "root"; // replace with your actual username, e.g., 'root' for local WAMP
$password = ""; // replace with your actual password, often empty for local WAMP
$dbname = "pangolin_creationdb"; // use your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>