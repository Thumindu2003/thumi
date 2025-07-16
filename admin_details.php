<?php
session_start();
require_once 'connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

$admins = [];
if ($conn) {
    $result = $conn->query("SELECT id, username, created_at FROM tbladmin ORDER BY id ASC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $admins[] = $row;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Details - Pangolin Creations</title>
  <link rel="stylesheet" href="admin_style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-dashboard">
  <div class="admin-container">
    <?php include 'admin_sidebar.php'; ?>
    <div class="admin-content">
      <header class="admin-header">
        <h1>Admin Details</h1>
        <div class="admin-user">
          <span>Welcome, <?php echo $_SESSION['admin_username']; ?></span>
          <i class="fas fa-user-circle"></i>
        </div>
      </header>
      <main class="admin-main">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Username</th>
              <th>Created At</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($admins)): ?>
              <tr><td colspan="3">No admins found.</td></tr>
            <?php else: ?>
              <?php foreach ($admins as $admin): ?>
                <tr>
                  <td><?php echo htmlspecialchars($admin['id']); ?></td>
                  <td><?php echo htmlspecialchars($admin['username']); ?></td>
                  <td><?php echo htmlspecialchars($admin['created_at']); ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </main>
    </div>
  </div>
</body>
</html>
