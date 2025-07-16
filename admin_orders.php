<?php
session_start();
require_once 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle order status update
if (isset($_POST['update_status'])) {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE tblorders SET status = ? WHERE order_id = ?");
    $stmt->bind_param("si", $status, $orderId);
    $stmt->execute();
    
    $_SESSION['message'] = "Order status updated successfully!";
    header("Location: admin_orders.php");
    exit();
}

// Handle order deletion
if (isset($_GET['delete'])) {
    $orderId = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM tblorders WHERE order_id = ?");
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    
    $_SESSION['message'] = "Order deleted successfully!";
    header("Location: admin_orders.php");
    exit();
}

// Get all orders
$orders = $conn->query("SELECT * FROM tblorders ORDER BY order_date DESC")->fetch_all(MYSQLI_ASSOC);
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
        
        <div class="orders-table">
          <table>
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Contact</th>
                <th>Email</th>
                <th>Service</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $order): ?>
              <tr>
                <td>#<?php echo $order['order_id']; ?></td>
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($order['customer_contact']); ?></td>
                <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                <td><?php echo htmlspecialchars($order['service_name']); ?></td>
                <td>Rs.<?php echo number_format($order['total_amount'], 2); ?></td>
                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                <td>
                  <form method="POST" class="status-form">
                    <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                    <select name="status" onchange="this.form.submit()">
                      <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                      <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                      <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                    </select>
                  </form>
                </td>
                <td class="actions">
                  <a href="admin_view_order.php?id=<?php echo $order['order_id']; ?>" class="btn btn-view"><i class="fas fa-eye"></i> View</a>
                  <a href="admin_orders.php?delete=<?php echo $order['order_id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this order?')"><i class="fas fa-trash"></i> Delete</a>
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
  </script>
</body>
</html>