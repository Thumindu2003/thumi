<?php
session_start();
require_once 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle order status update for tblorders
if (isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];
    // Only allow 'pending' or 'completed'
    if (in_array($status, ['pending', 'completed'])) {
        $stmt = $conn->prepare("UPDATE tblorders SET status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $status, $orderId);
        $stmt->execute();
        $_SESSION['message'] = "Order status updated successfully!";
    } else {
        $_SESSION['message'] = "Invalid status value.";
    }
    header("Location: admin_orders.php");
    exit();
}

// Handle cart_orders status update
if (isset($_POST['update_cart_status'])) {
    $orderId = $_POST['cart_order_id'];
    $status = $_POST['status'];
    if (in_array($status, ['pending', 'completed'])) {
        $stmt = $conn->prepare("UPDATE cart_orders SET status = ? WHERE order_id = ?");
        $stmt->bind_param("si", $status, $orderId);
        $stmt->execute();
        $_SESSION['message'] = "Cart order status updated successfully!";
    } else {
        $_SESSION['message'] = "Invalid status value.";
    }
    header("Location: admin_orders.php");
    exit();
}

// Handle order deletion
if (isset($_GET['delete'])) {
    $orderId = $_GET['delete'];
    if (is_numeric($orderId)) {
        $stmt = $conn->prepare("DELETE FROM tblorders WHERE order_id = ?");
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $stmt->close();
        $_SESSION['message'] = "Order deleted successfully!";
    } else {
        $_SESSION['message'] = "Invalid order ID.";
    }
    header("Location: admin_orders.php");
    exit();
}

// Get all orders (no join with tblUser, since tblorders does not have user_name)
$order_result = $conn->query("
    SELECT * FROM tblorders
    ORDER BY order_date DESC
");
if ($order_result === false) {
    $orders = [];
    $_SESSION['message'] = "Failed to fetch orders: " . $conn->error;
} else {
    $orders = $order_result->fetch_all(MYSQLI_ASSOC);
}

// Fetch all cart_orders for admin view, join with tblservice and tblUser for FName
$cart_result = $conn->query("
    SELECT co.*, ts.SName AS service_name, u.FName AS user_fname
    FROM cart_orders co
    LEFT JOIN tblservice ts ON co.SID = ts.SID
    LEFT JOIN tblUser u ON co.user_name = u.User_name
    ORDER BY co.order_date DESC
");
if ($cart_result === false) {
    $cart_orders = [];
    $_SESSION['message'] = "Failed to fetch cart orders: " . $conn->error;
} else {
    $cart_orders = $cart_result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Orders - Pangolin Creations</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="admin_style.css">
</head>
<body class="admin-dashboard">
  <div class="admin-container">
    <!-- Sidebar (same as dashboard) -->
    <?php include 'admin_sidebar.php'; ?>
    
    <!-- Main Content -->
    <div class="admin-content">
      <header class="admin-header">
        <h1>Manage Orders</h1>
        <div class="admin-user">
          <span>Welcome, <?php echo $_SESSION['admin_username']; ?></span>
          <i class="fas fa-user-circle"></i>
        </div>
      </header>
      
      <main class="admin-main">
        <?php if (isset($_SESSION['message'])): ?>
          <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        
        <!-- Search box -->
        <div style="margin-bottom: 20px;">
            <input type="text" id="orderSearchInput" placeholder="Search by username or full name..." style="padding:8px;width:250px;border-radius:4px;border:1px solid #ccc;">
        </div>

        <!-- Updated: cart_orders table -->
        <div class="orders-table" style="margin-top:40px;">
         
          <table id="ordersTable">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>User Name</th>
                <th>Full Name</th>
                <th>SID</th>
                <th>Service Name</th>
                <th>Quantity</th>
                <th>Amount</th> <!-- Add this column -->
                <th>Order Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cart_orders as $order): ?>
              <tr>
                <td><?php echo $order['order_id']; ?></td>
                <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                <td><?php echo htmlspecialchars($order['user_fname'] ?? ''); ?></td>
                <td><?php echo $order['SID']; ?></td>
                <td><?php echo htmlspecialchars($order['service_name'] ?? 'N/A'); ?></td>
                <td><?php echo $order['quantity']; ?></td>
                <td>
                  <?php
                    if (isset($order['total_amount']) && is_numeric($order['total_amount'])) {
                      echo 'Rs.' . number_format((float)$order['total_amount'], 2);
                    } else {
                      echo '-';
                    }
                  ?>
                </td>
                <td><?php echo $order['order_date']; ?></td>
                <td>
                  <form method="POST" class="status-form">
                    <input type="hidden" name="cart_order_id" value="<?php echo $order['order_id']; ?>">
                    <select name="status" onchange="this.form.submit()">
                      <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                      <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                    <input type="hidden" name="update_cart_status" value="1">
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>
  
  <script>
    // Auto-delete completed orders after 5 days
    function checkCompletedOrders() {
      fetch('admin_cleanup.php')
        .then(response => response.json())
        .then(data => {
          if (data.deleted > 0) {
            console.log(`Automatically deleted ${data.deleted} completed orders older than 5 days.`);
          }
        });
    }
    
    // Run cleanup on page load
    document.addEventListener('DOMContentLoaded', checkCompletedOrders);

    // Filter orders table by username or full name
    document.getElementById('orderSearchInput').addEventListener('input', function() {
        const filter = this.value.trim().toLowerCase();
        const rows = document.querySelectorAll('#ordersTable tbody tr');
        rows.forEach(row => {
            const usernameCell = row.querySelector('td:nth-child(2)');
            const fullnameCell = row.querySelector('td:nth-child(3)');
            const username = usernameCell ? usernameCell.textContent.trim().toLowerCase() : '';
            const fullname = fullnameCell ? fullnameCell.textContent.trim().toLowerCase() : '';
            row.style.display = (username.includes(filter) || fullname.includes(filter)) ? '' : 'none';
        });
    });
  </script>
</body>
</html>