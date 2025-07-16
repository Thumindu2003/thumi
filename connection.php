<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection (update credentials as needed)
$servername = "localhost";
$username = "root";
$password = ""; // or your MySQL root password
$dbname = "pangolin_creationdb"; // <-- Updated to match your actual database name

$conn = @new mysqli($servername, $username, $password, $dbname);
$mysqli = $conn; // For compatibility with account.php

if ($conn->connect_error) {
    // Show warning but do not stop script execution
    echo "<div style='color:red;'>Warning: Database connection failed: " . htmlspecialchars($conn->connect_error) . "</div>";
    // Optionally, set $conn to null to indicate no connection
    $conn = null;
}
?>