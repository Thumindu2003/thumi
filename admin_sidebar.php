<?php
// Load .env admin username
$envAdminUsername = null;
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, 'ADMIN_USERNAME=') === 0) {
            $envAdminUsername = trim(substr($line, strlen('ADMIN_USERNAME=')));
            break;
        }
    }
}
$currentAdmin = $_SESSION['admin_username'] ?? null;
$isEnvAdmin = ($currentAdmin && $envAdminUsername && $currentAdmin === $envAdminUsername);
?>
<div class="admin-sidebar">
  <div class="admin-logo">
    <img src="Pictures/logo pangolin.png" alt="Pangolin Creations">
  </div>
  <nav class="admin-nav">
    <ul>
      <li><a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
      <li><a href="admin_services.php"><i class="fas fa-paint-brush"></i> Services</a></li>
      <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
      <li><a href="admin_slideshow.php"><i class="fas fa-images"></i> Slideshow</a></li>
      <?php if ($isEnvAdmin): ?>
      <li><a href="admin_add_admin.php"><i class="fas fa-user-plus"></i> Add Admin</a></li>
      <li><a href="admin_details.php"><i class="fas fa-users-cog"></i> Admin Details</a></li>
      <?php endif; ?>
      <li><a href="admin_settings.php"><i class="fas fa-cog"></i> Settings</a></li>
      <li><a href="admin_login.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
  </nav>
</div>
