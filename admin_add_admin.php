<?php
session_start();
require_once 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (strlen($username) < 3) {
        $error = "Username must be at least 3 characters.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM tbladmin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = "Username already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO tbladmin (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed);
            if ($stmt->execute()) {
                $success = "Admin added successfully!";
            } else {
                $error = "Error adding admin.";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Admin - Pangolin Creations</title>
  <link rel="stylesheet" href="admin_style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-dashboard">
  <div class="admin-container">
    <?php include 'admin_sidebar.php'; ?>
    <div class="admin-content">
      <header class="admin-header">
        <h1>Add New Admin</h1>
        <div class="admin-user">
          <span>Welcome, <?php echo $_SESSION['admin_username']; ?></span>
          <i class="fas fa-user-circle"></i>
        </div>
      </header>
      <main class="admin-main">
        <?php if ($error): ?>
          <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif ($success): ?>
          <div class="alert alert-success" id="successMsg"><?php echo $success; ?></div>
          <script>
            setTimeout(function() {
              var msg = document.getElementById('successMsg');
              if (msg) msg.style.display = 'none';
            }, 3000);
          </script>
        <?php endif; ?>
        <form method="POST" class="service-form" autocomplete="off">
          <div class="form-group">
            <label for="username">Admin Username</label>
            <input type="text" name="username" id="username" required minlength="3">
          </div>
          <div class="form-group">
            <label for="password">Admin Password</label>
            <input type="password" name="password" id="password" required minlength="8">
          </div>
          <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" name="confirm_password" id="confirm_password" required minlength="8">
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus"></i> Add Admin</button>
            <a href="admin_dashboard.php" class="btn btn-cancel">Cancel</a>
          </div>
        </form>
      </main>
    </div>
  </div>
</body>
</html>
