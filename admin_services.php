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
    echo "Cannot display admin services: ";
    if (isset($conn) && $conn->connect_error) {
        echo "Database connection failed: " . htmlspecialchars($conn->connect_error);
    } else {
        echo "Database connection is not available.";
    }
    echo "</div>";
    exit();
}

// Handle service deletion
if (isset($_GET['delete'])) {
    $sid = $_GET['delete'];
    if (is_numeric($sid)) {
        // Delete all cart_orders entries for this service
        $stmt_cart = $conn->prepare("DELETE FROM cart_orders WHERE SID = ?");
        $stmt_cart->bind_param("i", $sid);
        $stmt_cart->execute();
        $stmt_cart->close();

        // Delete the service itself
        $stmt = $conn->prepare("DELETE FROM tblservice WHERE SID = ?");
        $stmt->bind_param("i", $sid);
        $stmt->execute();
        $stmt->close();

        $_SESSION['message'] = "Service deleted successfully!";
    } else {
        $_SESSION['message'] = "Invalid service ID.";
    }
    header("Location: admin_services.php");
    exit();
}

// Get all services
$services = $conn->query("SELECT * FROM tblservice ORDER BY SID DESC")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Services - Pangolin Creations</title>
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
        <h1>Manage Services</h1>
        <div class="admin-user">
          <span>Welcome, <?php echo $_SESSION['admin_username']; ?></span>
          <i class="fas fa-user-circle"></i>
        </div>
      </header>
      
      <main class="admin-main">
        <?php if (isset($_SESSION['message'])): ?>
          <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        
        <div class="action-buttons">
          <a href="admin_add_service.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add New Service</a>
        </div>
        
        <div class="services-table">
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Service Name</th>
                <th>Price</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($services as $service): ?>
              <tr>
                <td><?php echo $service['SID']; ?></td>
                <td>
                  <?php
                    $imgPath = $service['image'];
                    // If image is a BLOB, use service_image.php
                    if (!empty($imgPath) && !preg_match('/\.(jpg|jpeg|png|gif)$/i', $imgPath)) {
                      echo '<img src="service_image.php?sid=' . $service['SID'] . '" alt="' . htmlspecialchars($service['SName']) . '" class="service-thumbnail">';
                    } elseif (!empty($imgPath) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $imgPath) && file_exists(__DIR__ . '/' . $imgPath)) {
                      echo '<img src="' . htmlspecialchars($imgPath) . '" alt="' . htmlspecialchars($service['SName']) . '" class="service-thumbnail">';
                    } else {
                      echo '<img src="Pictures/default.jpg" alt="Default Image" class="service-thumbnail">';
                    }
                  ?>
                </td>
                <td><?php echo htmlspecialchars($service['SName']); ?></td>
                <td>Rs.<?php echo number_format($service['SPrice'], 2); ?></td>
                <td class="actions">
                  <a href="admin_edit_service.php?id=<?php echo $service['SID']; ?>" class="btn btn-edit"><i class="fas fa-edit"></i> Edit</a>
                  <a href="admin_services.php?delete=<?php echo $service['SID']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this service?')"><i class="fas fa-trash"></i> Delete</a>
                </td>
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