<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once 'connection.php'; // Ensure this file has valid DB credentials

// Get raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (!$data || !isset($data['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid cart data']);
    exit;
}

session_start();
if (!isset($_SESSION['user_name'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_name = $_SESSION['user_name'];
$cart_json = json_encode($data['cart']);
$total = isset($data['total']) ? (float) str_replace('Rs.', '', $data['total']) : 0;

try {
    // Insert into cart_orders table
    $stmt = $conn->prepare("INSERT INTO cart_orders (user_name, order_data, total_amount) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $user_name, $cart_json, $total);
    
    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Order submitted successfully! Seller will contact you soon.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to save order: ' . $conn->error
        ]);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
$conn->close();
?>