<?php
session_start();
require_once 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Get stats for dashboard
$servicesCount = $mysqli->query("SELECT COUNT(*) FROM tblservice")->fetch_row()[0];
$pendingOrders = $mysqli->query("SELECT COUNT(*) FROM tblorders WHERE status = 'pending'")->fetch_row()[0];
$completedOrders = $mysqli->query("SELECT COUNT(*) FROM tblorders WHERE status = 'completed'")->fetch_row()[0];
$recentOrders = $mysqli->query("SELECT * FROM tblorders ORDER BY order_date DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Pangolin Creations</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="admin_style.css">
</head>
<body class="admin-dashboard">
  <div class="admin-container">
    <!-- Sidebar -->
    <div class="admin-sidebar">
      <div class="admin-logo">
        <img src="Pictures/logo pangolin.png" alt="Pangolin Creations">
      </div>
      <nav class="admin-nav">
        <ul>
          <li class="active"><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
          <li><a href="admin_services.php"><i class="fas fa-paint-brush"></i> Services</a></li>
          <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
          <li><a href="admin_slideshow.php"><i class="fas fa-images"></i> Slideshow</a></li>
          <li><a href="admin_settings.php"><i class="fas fa-cog"></i> Settings</a></li>
          <li><a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
      </nav>
    </div>
    
    <!-- Main Content -->
    <div class="admin-content">
      <header class="admin-header">
        <h1>Dashboard Overview</h1>
        <div class="admin-user">
          <span>Welcome, <?php echo $_SESSION['admin_username']; ?></span>
          <i class="fas fa-user-circle"></i>
        </div>
      </header>
      
      <main class="admin-main">
        <!-- Stats Cards -->
        <div class="stats-cards">
          <div class="stat-card">
            <div class="stat-icon" style="background-color: #4e73df;">
              <i class="fas fa-paint-brush"></i>
            </div>
            <div class="stat-info">
              <h3>Total Services</h3>
              <p><?php echo $servicesCount; ?></p>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon" style="background-color: #1cc88a;">
              <i class="fas fa-shopping-cart"></i>
            </div>
            <div class="stat-info">
              <h3>Pending Orders</h3>
              <p><?php echo $pendingOrders; ?></p>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon" style="background-color: #36b9cc;">
              <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-info">
              <h3>Completed Orders</h3>
              <p><?php echo $completedOrders; ?></p>
            </div>
          </div>
        </div>
        
        <!-- Recent Orders -->
        <div class="recent-orders">
          <h2>Recent Orders</h2>
          <table>
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentOrders as $order): ?>
              <tr>
                <td>#<?php echo $order['order_id']; ?></td>
                <td><?php echo $order['customer_name']; ?></td>
                <td><?php echo $order['service_name']; ?></td>
                <td>Rs.<?php echo number_format($order['total_amount'], 2); ?></td>
                <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                <td><span class="status-badge <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>
</body>
</html>