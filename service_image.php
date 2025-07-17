<?php
require_once 'connection.php';

if (!isset($_GET['sid']) || !is_numeric($_GET['sid'])) {
    http_response_code(404);
    exit;
}

$sid = intval($_GET['sid']);
$stmt = $conn->prepare("SELECT image FROM tblservice WHERE SID = ?");
$stmt->bind_param("i", $sid);
$stmt->execute();
$stmt->bind_result($imageData);
if ($stmt->fetch() && !empty($imageData)) {
    // Try to detect image type (default to jpeg)
    $img = base64_decode($imageData);
    $finfo = finfo_open();
    $mime = finfo_buffer($finfo, $img, FILEINFO_MIME_TYPE);
    finfo_close($finfo);
    if (!$mime) $mime = 'image/jpeg';
    header("Content-Type: $mime");
    echo $img;
} else {
    // Output a default image if not found
    header("Content-Type: image/jpeg");
    readfile('Pictures/default.jpg');
}
$stmt->close();
$conn->close();
?>
