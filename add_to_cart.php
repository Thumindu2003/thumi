<?php
session_start();
header('Content-Type: application/json');
require_once 'connection.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['SID'], $data['SName'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit;
}

$SID = $data['SID'];
$SName = $data['SName'];
$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : null;

if ($user_name) {
    // Check if item already exists in cart
    $stmt = $mysqli->prepare("SELECT SID FROM cart_orders WHERE user_name = ? AND SID = ? AND status = 'pending'");
    $stmt->bind_param("si", $user_name, $SID);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        // Item already exists, no further action needed
        $success = true;
    } else {
        $stmt->close();
        // Insert new item
        $insert = $mysqli->prepare("INSERT INTO cart_orders (user_name, SID, SName, status) VALUES (?, ?, ?, 'pending')");
        $insert->bind_param("sis", $user_name, $SID, $SName);
        $success = $insert->execute();
        $insert->close();
    }

    // Notify realtime server
    $payload = json_encode([
        'user' => $user_name
    ]);
    $opts = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/json\r\n",
            'content' => $payload
        ]
    ];
    @file_get_contents('http://localhost:3000/cart-update', false, stream_context_create($opts));

    $mysqli->close();
    echo json_encode([
        'success' => $success
    ]);
} else {
    // For guests, just return success
    echo json_encode(['success' => true]);
}
?>
