<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    echo "success";
} else {
    echo "invalid request";
}
?>
