<?php
session_start();
require_once 'connection.php'; // Ensure this file defines $conn

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Check admin credentials
    $query = "SELECT * FROM tblAdmin WHERE username = ?";
    $stmt = $conn->prepare($query); // Use $conn instead of $mysqli
    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            if (password_verify($password, $admin['password'])) {
                session_regenerate_id(true);
                $_SESSION['admin_loggedin'] = true;
                $_SESSION['admin_username'] = $username;
                header("Location: admin_dashboard.php");
                exit();
            } else {
                $loginError = "Invalid username or password.";
            }
        } else {
            $loginError = "Invalid username or password.";
        }
        $stmt->close();
    } else {
        $loginError = "Database query failed: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login - Pangolin Creations</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="admin_style.css">
</head>
<body class="admin-login">
  <div class="login-container">
    <div class="login-box">
      <img src="Pictures/logo pangolin.png" alt="Pangolin Creations Logo" class="admin-logo">
      <h2>Admin Panel Login</h2>
      
      <?php if (isset($loginError)): ?>
        <div class="alert alert-danger"><?php echo $loginError; ?></div>
      <?php endif; ?>
      
      <form method="POST">
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input type="text" name="username" placeholder="Username" required>
        </div>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" name="login" class="login-btn">Login</button>
      </form>
    </div>
  </div>
</body>
</html>