<?php
session_start();
require_once 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    
    
    // Handle image upload
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image = file_get_contents($_FILES['image']['tmp_name']);
    }
    try {
        $image = base64_encode($image);
    } catch (Exception $e) {
        $error = "Error processing image: " . $e->getMessage();
    }
    $stmt = $conn->prepare("INSERT INTO tblservice (SName, SPrice, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sds", $name, $price, $image);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Service added successfully!";
        header("Location: admin_services.php");
        exit();
    } else {
        $error = "Error adding service. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Service - Pangolin Creations</title>
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
        <h1>Add New Service</h1>
        <div class="admin-user">
          <span>Welcome, <?php echo $_SESSION['admin_username']; ?></span>
          <i class="fas fa-user-circle"></i>
        </div>
      </header>
      
      <main class="admin-main">
        <?php if (isset($error)): ?>
          <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" class="service-form">
          <div class="form-group">
            <label for="name">Service Name</label>
            <input type="text" id="name" name="name" required>
          </div>
          
          <div class="form-group">
            <label for="price">Price (Rs.)</label>
            <input type="number" id="price" name="price" step="0.01" min="0" required>
          </div>
          
          
          
          <div class="form-group">
            <label for="image">Service Image</label>
            <input type="file" id="image" name="image" accept="image/*">
            <small>Recommended size: 500x500 pixels</small>
          </div>
          
          <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Service</button>
            <a href="admin_services.php" class="btn btn-cancel">Cancel</a>
          </div>
        </form>
      </main>
    </div>
  </div>
</body>
</html>