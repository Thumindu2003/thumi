<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection (update credentials as needed)
$servername = "localhost";
$username = "root";
$password = ""; // or your MySQL root password
$dbname = "pangolin_creationdb"; // <-- Updated to match your actual database name

$mysqli = new mysqli($servername, $username, $password, $dbname);
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
?>