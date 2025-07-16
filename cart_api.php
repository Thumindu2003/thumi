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
                
                $stmt = $mysqli->prepare("INSERT INTO tbltempcart (sessionId, SID) VALUES (?, ?) 
                                      ON DUPLICATE KEY UPDATE SID = SID");
                $stmt->bind_param("si", $sessionId, $SID);
                $stmt->execute();
                $stmt->close();
                $response = ['status' => 'success'];
                // Notify realtime server
                $payload = json_encode([
                    'user' => $sessionId
                ]);
                $opts = [
                    'http' => [
                        'method' => 'POST',
                        'header' => "Content-Type: application/json\r\n",
                        'content' => $payload
                    ]
                ];
                @file_get_contents('http://localhost:3000/cart-update', false, stream_context_create($opts));
            }
            break;
            
        case 'remove_item':
            if (isset($input['SID'])) {
                $SID = intval($input['SID']);
                if (isset($_SESSION['username'])) {
                    $user_name = $_SESSION['username'];
                    $stmt = $mysqli->prepare("DELETE FROM cart_orders WHERE user_name = ? AND SID = ? AND status = 'pending'");
                    $stmt->bind_param("si", $user_name, $SID);
                    $stmt->execute();
                    $stmt->close();
                    $response = ['status' => 'success'];
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
                } else {
                    $sessionId = session_id();
                    $stmt = $mysqli->prepare("DELETE FROM tbltempcart WHERE sessionId = ? AND SID = ?");
                    $stmt->bind_param("si", $sessionId, $SID);
                    $stmt->execute();
                    $stmt->close();
                    $response = ['status' => 'success'];
                    // Notify realtime server
                    $payload = json_encode([
                        'user' => $sessionId
                    ]);
                    $opts = [
                        'http' => [
                            'method' => 'POST',
                            'header' => "Content-Type: application/json\r\n",
                            'content' => $payload
                        ]
                    ];
                    @file_get_contents('http://localhost:3000/cart-update', false, stream_context_create($opts));
                }
            }
            break;
            
        default:
            $response['message'] = 'Unknown action';
    }
} catch (Exception $e) {
    $response['message'] = 'Server error: ' . $e->getMessage();
}

echo json_encode($response);
$mysqli->close();
?>