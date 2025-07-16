<?php
session_start();
header('Content-Type: application/json');
require_once 'connection.php';

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['cart']) || !is_array($input['cart']) || count($input['cart']) === 0) {
    echo json_encode(['success' => false, 'message' => 'Cart is empty or invalid.']);
    exit;
}

$user_name = isset($_SESSION['username']) ? $_SESSION['username'] : null;
if (!$user_name) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$total = isset($input['total']) ? (float) $input['total'] : 0;

// Save each cart item as an order
$success = true;
foreach ($input['cart'] as $item) {
    $SID = intval($item['SID']);
    $SName = $item['SName'];
    $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;
    $stmt = $mysqli->prepare("INSERT INTO cart_orders (user_name, SID, SName, quantity, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->bind_param("ssis", $user_name, $SID, $SName, $quantity);
    if (!$stmt->execute()) {
        $success = false;
        break;
    }
    $stmt->close();
}

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Order confirmed and saved!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to save order.']);
}
$mysqli->close();
?>
