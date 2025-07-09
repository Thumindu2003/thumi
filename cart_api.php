<?php
require_once 'connection.php';
session_start();

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$response = ['status' => 'error', 'message' => 'Invalid request'];

try {
    switch ($action) {
        case 'add_item':
            if (isset($input['SID'])) {
                $SID = intval($input['SID']);
                $sessionId = session_id();
                
                $stmt = $conn->prepare("INSERT INTO tbltempcart (sessionId, SID, quantity) VALUES (?, ?, 1) 
                                      ON DUPLICATE KEY UPDATE quantity = quantity + 1");
                $stmt->bind_param("si", $sessionId, $SID);
                $stmt->execute();
                $response = ['status' => 'success'];
            }
            break;
            
        case 'remove_item':
            if (isset($input['SID'])) {
                $SID = intval($input['SID']);
                $sessionId = session_id();
                
                $stmt = $conn->prepare("DELETE FROM tbltempcart WHERE sessionId = ? AND SID = ?");
                $stmt->bind_param("si", $sessionId, $SID);
                $stmt->execute();
                $response = ['status' => 'success'];
            }
            break;
            
        case 'save_cart':
            if (isset($input['cart']) && is_array($input['cart'])) {
                $sessionId = session_id();
                $conn->query("DELETE FROM tbltempcart WHERE sessionId = '$sessionId'");
                
                foreach ($input['cart'] as $item) {
                    $SID = intval($item['SID']);
                    $quantity = intval($item['quantity'] ?? 1);
                    
                    $stmt = $conn->prepare("INSERT INTO tbltempcart (sessionId, SID, quantity) VALUES (?, ?, ?)");
                    $stmt->bind_param("sii", $sessionId, $SID, $quantity);
                    $stmt->execute();
                }
                $response = ['status' => 'success'];
            }
            break;
            
        default:
            $response['message'] = 'Unknown action';
    }
} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
}

echo json_encode($response);
$conn->close();
?>