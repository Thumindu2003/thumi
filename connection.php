<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection (update credentials as needed)
$servername = "localhost";
$username = "root";
$password = ""; // or your MySQL root password
$dbname = "pangolin_creationdb"; // <-- Updated to match your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>