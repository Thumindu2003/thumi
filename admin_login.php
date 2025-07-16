<?php
session_start();

$showForm = true;

// If redirected from logout, destroy any existing session and start fresh
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    // Clear session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_start(); // Start a new session for the login page
    $logoutMsg = "You have been logged out.";
}

$usernameValue = '';
$passwordValue = '';

require_once 'connection.php'; // Ensure connection is available before any DB operation

// Load .env admin credentials
$envAdminUsername = null;
$envAdminPassword = null;
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, 'ADMIN_USERNAME=') === 0) {
            $envAdminUsername = trim(substr($line, strlen('ADMIN_USERNAME=')));
        }
        if (strpos($line, 'ADMIN_PASSWORD=') === 0) {
            $envAdminPassword = trim(substr($line, strlen('ADMIN_PASSWORD=')));
        }
    }
}

if (isset($logoutMsg)) {
    $usernameValue = '';
    $passwordValue = '';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if .env admin login (allow even if DB is not available)
    if ($envAdminUsername && $envAdminPassword && $username === $envAdminUsername) {
        if ($password === $envAdminPassword) {
            session_regenerate_id(true);
            $_SESSION['admin_loggedin'] = true;
            $_SESSION['admin_username'] = $envAdminUsername;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $loginError = "Password invalid.";
        }
    } else {
        // Only try DB login if .env admin login did not succeed
        // Check connection
        if (!isset($conn) || $conn === null || $conn->connect_error) {
            $loginError = "Database connection error.";
        } else {
            $stmt = $conn->prepare("SELECT password FROM tbladmin WHERE username = ?");
            if ($stmt) {
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($hashed);
                    $stmt->fetch();
                    if (password_verify($password, $hashed)) {
                        session_regenerate_id(true);
                        $_SESSION['admin_loggedin'] = true;
                        $_SESSION['admin_username'] = $username;
                        header("Location: admin_dashboard.php");
                        exit();
                    } else {
                        $loginError = "Password invalid.";
                    }
                } else {
                    $loginError = "Admin not found.";
                }
                $stmt->close();
            } else {
                $loginError = "Database error: " . $conn->error;
            }
        }
    }
    // Repopulate username if login failed
    $usernameValue = htmlspecialchars($username, ENT_QUOTES); // Preserve entered username
    $passwordValue = htmlspecialchars($password, ENT_QUOTES); // (Not recommended for passwords, but per request)
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
  <style>
    /* Modern login page overrides */
    body.admin-login {
      background: linear-gradient(135deg, #9c27b0 0%, #7b1fa2 100%);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Arial', sans-serif;
    }
    .login-container {
      width: 100%;
      max-width: 380px;
      padding: 0 12px;
      margin: 0 auto;
    }
    .login-box {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 6px 36px rgba(44,62,80,0.18);
      padding: 36px 28px 28px 28px;
      text-align: center;
      position: relative;
    }
    .admin-logo {
      max-width: 140px;
      margin-bottom: 16px;
    }
    .login-box h2 {
      color: #9c27b0;
      margin-bottom: 22px;
      font-size: 1.45rem;
      font-weight: bold;
      letter-spacing: 0.5px;
    }
    .input-group {
      margin-bottom: 18px;
      position: relative;
    }
    .input-group i {
      position: absolute;
      left: 14px;
      top: 50%;
      transform: translateY(-50%);
      color: #b8c7ce;
      font-size: 1.08rem;
    }
    .input-group input {
      width: 100%;
      padding: 11px 12px 11px 40px;
      border: 1px solid #e0e0e0;
      border-radius: 7px;
      font-size: 1.03rem;
      background: #f7f7fa;
      transition: border-color 0.2s, box-shadow 0.2s;
      box-sizing: border-box;
    }
    .input-group input:focus {
      border-color: #9c27b0;
      outline: none;
      background: #fff;
      box-shadow: 0 0 0 2px #e1bee7;
    }
    .login-btn {
      width: 100%;
      padding: 12px;
      background: linear-gradient(90deg, #9c27b0 60%, #7b1fa2 100%);
      color: #fff;
      border: none;
      border-radius: 7px;
      font-size: 1.08rem;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.2s, box-shadow 0.2s;
      margin-top: 8px;
      box-shadow: 0 2px 8px rgba(156,39,176,0.10);
    }
    .login-btn:hover {
      background: linear-gradient(90deg, #7b1fa2 60%, #9c27b0 100%);
      box-shadow: 0 4px 16px rgba(156,39,176,0.13);
    }
    .alert {
      padding: 10px 16px;
      margin-bottom: 16px;
      border-radius: 5px;
      font-size: 0.98rem;
      text-align: left;
    }
    .alert-danger {
      background: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    .alert-info {
      background: #e3f0fb;
      color: #155fa0;
      border: 1px solid #b6e0fe;
    }
    @media (max-width: 500px) {
      .login-box {
        padding: 18px 4px 12px 4px;
      }
      .admin-logo {
        max-width: 90px;
      }
      .login-container {
        max-width: 98vw;
      }
    }
  </style>
</head>
<body class="admin-login">
  <div class="login-container">
    <div class="login-box">
      <img src="Pictures/logo pangolin.png" alt="Pangolin Creations Logo" class="admin-logo">
      <h2>Admin Panel Login</h2>
      
      <?php if (isset($logoutMsg)): ?>
        <div class="alert alert-info"><?php echo $logoutMsg; ?></div>
      <?php endif; ?>
      
      <?php if (isset($loginError)): ?>
        <div class="alert alert-danger"><?php echo $loginError; ?></div>
      <?php endif; ?>
      
      <?php if ($showForm): ?>
      <form method="POST">
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input type="text" name="username" placeholder="Username" required value="<?php echo $usernameValue; ?>">
        </div>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" placeholder="Password" required value="<?php echo $passwordValue; ?>">
        </div>
        <button type="submit" name="login" class="login-btn">Login</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>