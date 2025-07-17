<?php
session_start();
require_once 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Improved database connection error handling
if (!isset($conn) || $conn === null || $conn->connect_error) {
    echo "<div style='color:red;'>";
    echo "Cannot display admin dashboard: ";
    if (isset($conn) && $conn->connect_error) {
        echo "Database connection failed: " . htmlspecialchars($conn->connect_error);
    } else {
        echo "Database connection is not available.";
    }
    echo "</div>";
    exit();
}

// Functions to get order counts
function getPendingOrdersCount($conn) {
    $count = 0;
    $res1 = $conn->query("SELECT COUNT(*) FROM tblorders WHERE status = 'pending'");
    if ($res1) $count += $res1->fetch_row()[0];
    $res2 = $conn->query("SELECT COUNT(*) FROM cart_orders WHERE status = 'pending'");
    if ($res2) $count += $res2->fetch_row()[0];
    return $count;
}

function getCompletedOrdersCount($conn) {
    $count = 0;
    $res1 = $conn->query("SELECT COUNT(*) FROM tblorders WHERE status = 'completed'");
    if ($res1) $count += $res1->fetch_row()[0];
    $res2 = $conn->query("SELECT COUNT(*) FROM cart_orders WHERE status = 'completed'");
    if ($res2) $count += $res2->fetch_row()[0];
    return $count;
}

// Helper function to get contact number by username
function getUserContact($conn, $username) {
    if (!$username) return '';
    $stmt = $conn->prepare("SELECT contactno FROM tbluser WHERE username = ? LIMIT 1");
    if (!$stmt) return '';
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($contact);
    $stmt->fetch();
    $stmt->close();
    return $contact ?: '';
}

// Get stats for dashboard
$servicesCount = $conn->query("SELECT COUNT(*) FROM tblservice")->fetch_row()[0];
$pendingOrders = getPendingOrdersCount($conn);
$completedOrders = getCompletedOrdersCount($conn);

// Fetch recent orders from tblorders
$recentOrders = [];
$result1 = $conn->query("SELECT order_id, customer_name, service_name, total_amount, order_date, status FROM tblorders ORDER BY order_date DESC");
if ($result1 && $result1->num_rows > 0) {
    $result1->data_seek(0); // Reset pointer
    while ($row = $result1->fetch_assoc()) {
        $recentOrders[] = [
            'order_id' => $row['order_id'],
            'customer_name' => $row['customer_name'],
            'service_name' => $row['service_name'],
            'total_amount' => $row['total_amount'],
            'order_date' => $row['order_date'],
            'status' => $row['status'],
            'user_name' => $row['customer_name'] // assuming customer_name is username for tblorders
        ];
    }
}

// Fetch recent cart_orders (show each service as a separate order)
$result2 = $conn->query("
    SELECT co.order_id, co.user_name, co.SID, co.quantity, co.order_date, co.status, ts.SName, ts.SPrice
    FROM cart_orders co
    LEFT JOIN tblservice ts ON co.SID = ts.SID
    ORDER BY co.order_date DESC
");
if ($result2 && $result2->num_rows > 0) {
    $result2->data_seek(0); // Reset pointer
    while ($row = $result2->fetch_assoc()) {
        $amount = '';
        if (is_numeric($row['SPrice']) && is_numeric($row['quantity'])) {
            $amount = $row['SPrice'] * $row['quantity'];
        }
        $recentOrders[] = [
            'order_id' => 'CART-' . $row['order_id'],
            'customer_name' => $row['user_name'],
            'service_name' => $row['SName'] . ' x' . $row['quantity'],
            'total_amount' => $amount,
            'order_date' => $row['order_date'],
            'status' => $row['status'],
            'user_name' => $row['user_name']
        ];
    }
}

// Sort all orders by date descending
usort($recentOrders, function($a, $b) {
    return strtotime($b['order_date']) - strtotime($a['order_date']);
});

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
    <?php include 'admin_sidebar.php'; ?>
    
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
        <!-- Search box for recent orders -->
        <div style="margin-bottom: 20px;">
            <input type="text" id="dashboardOrderSearchInput" placeholder="Search by customer name..." style="padding:8px;width:250px;border-radius:4px;border:1px solid #ccc;">
        </div>
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
          <table id="dashboardOrdersTable">
            <thead>
              <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Amount</th>
                <th>Date</th>
                <th>Status</th>
                <th>Contact</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($recentOrders) === 0): ?>
                <tr>
                  <td colspan="7" style="text-align:center;">No recent orders found.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($recentOrders as $order): ?>
                <tr>
                  <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                  <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                  <td><?php echo htmlspecialchars($order['service_name']); ?></td>
                  <td>
                    <?php
                      if (is_numeric($order['total_amount']) && $order['total_amount'] !== '') {
                        echo 'Rs.' . number_format((float)$order['total_amount'], 2);
                      } else {
                        echo '-';
                      }
                    ?>
                  </td>
                  <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                  <td><span class="status-badge <?php echo htmlspecialchars($order['status']); ?>"><?php echo ucfirst($order['status']); ?></span></td>
                  <td>
                    <?php
                      $contact = getUserContact($conn, $order['user_name']);
                      echo $contact ? htmlspecialchars($contact) : '-';
                    ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </main>
      <script>
        // Filter recent orders table by customer name (username or full name)
        document.getElementById('dashboardOrderSearchInput').addEventListener('input', function() {
            const filter = this.value.trim().toLowerCase();
            const rows = document.querySelectorAll('#dashboardOrdersTable tbody tr');
            rows.forEach(row => {
                const customerCell = row.querySelector('td:nth-child(2)');
                const customer = customerCell ? customerCell.textContent.trim().toLowerCase() : '';
                row.style.display = customer.includes(filter) ? '' : 'none';
            });
        });
      </script>
    </div>
  </div>
</body>
</html>