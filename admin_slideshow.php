<?php
session_start();
require_once 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Handle image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['slideshow_image'])) {
    $image = file_get_contents($_FILES['slideshow_image']['tmp_name']);
    $position = $_POST['position'];
    
    // Check if position already exists
    $checkStmt = $conn->prepare("SELECT id FROM tblslideshow WHERE position = ?");
    $checkStmt->bind_param("i", $position);
    $checkStmt->execute();
    $checkStmt->store_result();
    
    if ($checkStmt->num_rows > 0) {
        // Update existing image
        $stmt = $conn->prepare("UPDATE tblslideshow SET image = ? WHERE position = ?");
    } else {
        // Insert new image
        $stmt = $conn->prepare("INSERT INTO tblslideshow (image, position) VALUES (?, ?)");
    }
    
    $stmt->bind_param("si", $image, $position);
    $stmt->execute();
    
    $_SESSION['message'] = "Slideshow image updated successfully!";
    header("Location: admin_slideshow.php");
    exit();
}

// Get current slideshow images
$slides = $conn->query("SELECT * FROM tblslideshow ORDER BY position")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Slideshow - Pangolin Creations</title>
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
        <h1>Manage Slideshow</h1>
        <div class="admin-user">
          <span>Welcome, <?php echo $_SESSION['admin_username']; ?></span>
          <i class="fas fa-user-circle"></i>
        </div>
      </header>
      
      <main class="admin-main">
        <?php if (isset($_SESSION['message'])): ?>
          <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
        <?php endif; ?>
        
        <div class="slideshow-management">
          <div class="instructions">
            <p>Upload images for the homepage slideshow. There are 4 positions available.</p>
          </div>
          
          <div class="slideshow-positions">
            <?php for ($i = 1; $i <= 4; $i++): ?>
              <?php 
                $currentSlide = array_filter($slides, function($slide) use ($i) {
                  return $slide['position'] == $i;
                });
                $currentSlide = reset($currentSlide);
              ?>
              <div class="slide-position">
                <h3>Slide Position #<?php echo $i; ?></h3>
                
                <?php if ($currentSlide): ?>
                  <div class="current-image">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($currentSlide['image']); ?>" alt="Slide <?php echo $i; ?>">
                  </div>
                <?php else: ?>
                  <div class="no-image">
                    <i class="fas fa-image"></i>
                    <p>No image uploaded</p>
                  </div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data" class="upload-form">
                  <input type="hidden" name="position" value="<?php echo $i; ?>">
                  <div class="form-group">
                    <label for="image_<?php echo $i; ?>">Upload New Image</label>
                    <input type="file" id="image_<?php echo $i; ?>" name="slideshow_image" accept="image/*" required>
                    <small>Recommended size: 1200x600 pixels</small>
                  </div>
                  <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload</button>
                </form>
              </div>
            <?php endfor; ?>
          </div>
        </div>
      </main>
    </div>
  </div>
</body>
</html>