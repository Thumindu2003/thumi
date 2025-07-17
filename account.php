<?php
// Enable error reporting for debugging (remove or comment out in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'connection.php'; // Ensure connection.php contains the database connection logic

// Handle Sign-Up Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $fName = $_POST['nameWithInitial'];
    $email = $_POST['email'];
    $contact = $_POST['phone'];
    $userName = $_POST['signupUsername'];
    $password = $_POST['signupPassword'];
    $confirmPassword = $_POST['confirmPassword'];

    // Password validation: min 8 chars, 1 uppercase, 1 lowercase, 1 symbol
    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$/';
    if (!preg_match($passwordPattern, $password)) {
        $signupError = "Password must be at least 8 characters, include uppercase, lowercase, and a symbol.";
    } elseif ($password !== $confirmPassword) {
        $signupError = "Passwords do not match.";
    } else {
        // Check if username or email already exists
        $checkQuery = "SELECT User_name, Email FROM tblUser WHERE User_name = ? OR Email = ?";
        $checkStmt = $conn->prepare($checkQuery); // Use $conn
        if (!$checkStmt) {
            die("Error preparing statement: " . $conn->error);
        }
        $checkStmt->bind_param("ss", $userName, $email);
        $checkStmt->execute();
        $checkStmt->store_result();

        if ($checkStmt->num_rows > 0) {
            $signupError = "Username or email already exists.";
        } else {
            // Validate contact is numeric
            if (!is_numeric($contact)) {
                $signupError = "Contact number must contain only digits.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $query = "INSERT INTO tblUser (FName, Email, Contact, User_name, Password) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query); // Use $conn
                if (!$stmt) {
                    die("Error preparing statement: " . $conn->error);
                }
                $stmt->bind_param("ssiss", $fName, $email, $contact, $userName, $hashedPassword);

                if ($stmt->execute()) {
                    $_SESSION['signup_success'] = "Account created successfully!";
                    header("Location: account.php"); // Redirect to login page
                    exit();
                } else {
                    $signupError = "Error creating account: " . $stmt->error;
                }
                $stmt->close();
            }
        }
        $checkStmt->close();
    }
}

// Handle Login Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
  $loginUsername = $_POST['loginUsername'];
  $loginPassword = $_POST['loginPassword'];

  // If admin credentials, redirect to admin login page
  if ($loginUsername === 'admin@123' && $loginPassword === 'admin@123') {
      header("Location: admin_login.php");
      exit();
  }

  // Updated query to include Email
  $query = "SELECT Password, role, Email FROM tblUser WHERE User_name = ?";
  $stmt = $conn->prepare($query); // Use $conn
  
  if (!$stmt) {
      die("Error preparing statement: " . $conn->error);
  }
  
  $stmt->bind_param("s", $loginUsername);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
      $stmt->bind_result($hashedPassword, $role, $email);
      $stmt->fetch();

      if (password_verify($loginPassword, $hashedPassword)) {
          session_regenerate_id(true);
          $_SESSION['loggedin'] = true;
          $_SESSION['username'] = $loginUsername;
          $_SESSION['email'] = $email;
          $_SESSION['role'] = $role;

          if ($role === 'admin') {
              header("Location: admin_dashboard.php");
          } else {
              header("Location: index.php");
          }
          exit();
      } else {
          $loginError = "Invalid username or password.";
      }
  } else {
      $loginError = "Invalid username or password.";
  }
  $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Account - Pangolin Creations</title>
  <link rel="stylesheet" href="StyleSheet.css">
  <link rel="stylesheet" href="account.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .error-message {
      color: #dc3545;
      font-size: 0.8rem;
      margin-top: 0.25rem;
      display: none;
    }

    .input-hint {
      color: #6c757d;
      font-size: 0.8rem;
      display: block;
      margin-top: 0.25rem;
    }

    .password-hint {
      color: #6c757d;
      font-size: 0.8rem;
      margin-top: 0.25rem;
      margin-bottom: 0.5rem;
      display: block;
    }
  </style>
  <script>
    // JavaScript function to show the Sign-Up page
    function showSignUp() {
      document.getElementById('login-page').style.display = 'none';
      document.getElementById('signup-page').style.display = 'block';
    }

    // JavaScript function to show the Login page
    function showLogin() {
      document.getElementById('signup-page').style.display = 'none';
      document.getElementById('login-page').style.display = 'block';
    }

    // Password validation for sign-up (client-side)
    document.addEventListener('DOMContentLoaded', function() {
      var signupForm = document.getElementById('signupForm');
      if (signupForm) {
        signupForm.addEventListener('submit', function(e) {
          var password = document.getElementById('signupPassword').value;
          var confirmPassword = document.getElementById('confirmPassword').value;
          var passwordError = document.getElementById('passwordError');
          var confirmPasswordError = document.getElementById('confirmPasswordError');
          var pattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$/;
          var valid = true;

          passwordError.style.display = 'none';
          confirmPasswordError.style.display = 'none';

          if (!pattern.test(password)) {
            passwordError.textContent = "Password must be at least 8 characters, include uppercase, lowercase, and a symbol.";
            passwordError.style.display = 'block';
            valid = false;
          }
          if (password !== confirmPassword) {
            confirmPasswordError.textContent = "Passwords do not match.";
            confirmPasswordError.style.display = 'block';
            valid = false;
          }
          if (!valid) e.preventDefault();
        });
      }
    });
  </script>
  <script src="account.js"></script>
</head>
<body>
  <!-- Login Page -->
  <div id="login-page">
    <header>
      <nav class="navbar">
        <div class="nav-left">
            <img src="Pictures/logo pangolin.png" alt="Pangolin Creations Logo" class="logo">
        </div>
        <div class="nav-right">
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="services.php">Services</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
                <li><a href="account.php">Account</a></li>
            </ul>
        </div>
    </nav>
    </header>
    
    <main>
      <div class="form-container">
        <form id="loginForm" method="POST">
          <img src="Pictures/logo pangolin.png" alt="form logo" class="frmLogo">
          <h2>Sign into your Account</h2>
          
          <?php 
          if (isset($_SESSION['signup_success'])) {
              echo "<div class='success-message'>".$_SESSION['signup_success']."</div>";
              unset($_SESSION['signup_success']);
          }
          if (isset($loginError)) { 
              echo "<div class='error-message'>$loginError</div>"; 
          } 
          ?>
          
          <div class="input-group">
            <i class="fas fa-user input-icon"></i>
            <input type="text" name="loginUsername" id="loginUsername" placeholder="Username" required>
            <div id="loginUsernameError" class="error-message"></div>
          </div>
          
          <div class="input-group">
            <i class="fas fa-lock input-icon"></i>
            <input type="password" name="loginPassword" id="loginPassword" placeholder="Password" required>
            <div id="loginPasswordError" class="error-message"></div>
          </div>
          
          <div class="forgot-password">
            <a href="#">Forgot password?</a>
          </div>
          
          <button type="submit" name="login" class="form-button">LOGIN</button>
          
          <div class="divider">OR</div>
          
          <div class="alternate-action">
            <button type="button" class="signup-button" onclick="showSignUp()">SIGN UP</button>
          </div>
        </form>
      </div>
    </main>
  </div>

  <!-- Sign Up Page -->
  <div id="signup-page" style="display: none;">
    <header>
      <nav class="navbar">
        <div class="nav-left">
            <img src="Pictures/logo pangolin.png" alt="Pangolin Creations Logo" class="logo">
        </div>
        <div class="nav-right">
  <ul class="nav-links">
    <li><a href="index.php">Home</a></li>
    <li><a href="services.php">Services</a></li>
    <li><a href="cart.php">Cart <span id="cart-count" class="cart-count">0</span></a></li>
    <li><a href="about.php">About Us</a></li>
    <li><a href="contact.php">Contact</a></li>
    <li><a href="account.php">Account</a></li>
  </ul>
 
</div>
    </nav>
    </header>
    
    <main>
      <div class="form-container">
        <form id="signupForm" method="POST">
          <img src="Pictures/logo pangolin.png" alt="form logo" class="frmLogo">
          <h1>Sign Up</h1>
          
          <?php if (isset($signupError)) { echo "<div class='error-message'>$signupError</div>"; } ?>
          
          <div class="input-container">
            <div class="input-group">
              <i class="fas fa-id-card input-icon"></i>
              <input type="text" name="nameWithInitial" id="nameWithInitial" placeholder="Name with initial" required>
              <div id="nameWithInitialError" class="error-message"></div>
            </div>
          </div>
          
          <div class="input-container">
            <div class="input-group">
              <i class="fas fa-envelope input-icon"></i>
              <input type="email" name="email" id="email" placeholder="Email" required>
              <div id="emailError" class="error-message"></div>
            </div>
          </div>
          
          <div class="input-container">
            <div class="input-group">
              <i class="fas fa-phone input-icon"></i>
              <input type="tel" name="phone" id="phone" placeholder="Contact no" maxlength="10" required>
              <div id="phoneError" class="error-message"></div>
            </div>
          </div>
          
          <div class="input-container">
            <div class="input-group">
              <i class="fas fa-user input-icon"></i>
              <input type="text" name="signupUsername" id="signupUsername" placeholder="Username" required>
              <div id="usernameError" class="error-message"></div>
            </div>
          </div>
          
          <div class="input-container">
            <div class="input-group">
              <i class="fas fa-key input-icon"></i>
              <input type="password" name="signupPassword" id="signupPassword" placeholder="Password" required>
              <span class="password-hint">
                Password must be at least 8 characters, include uppercase, lowercase, and a symbol.
              </span>
              <div id="passwordError" class="error-message"></div>
            </div>
          </div>
          
          <div class="input-container">
            <div class="input-group">
              <i class="fas fa-lock input-icon"></i>
              <input type="password" name="confirmPassword" id="confirmPassword" placeholder="Confirm Password" required>
              <div id="confirmPasswordError" class="error-message"></div>
            </div>
          </div>
          
          <button type="submit" name="signup" class="signup-button">SIGN UP</button>
          <div class="alternate-action">
            Already have an account? <a href="#" onclick="showLogin()">Log in</a>
          </div>
        </form>
      </div>
    </main>
  </div>
</body>
<script>
(function(){
  if(!window.chatbase||window.chatbase("getState")!=="initialized"){
    window.chatbase=(...arguments)=>{
      if(!window.chatbase.q){window.chatbase.q=[]}
      window.chatbase.q.push(arguments)
    };
    window.chatbase=new Proxy(window.chatbase,{
      get(target,prop){
        if(prop==="q"){return target.q}
        return(...args)=>target(prop,...args)
      }
    })
  }
  const onLoad=function(){
    const script=document.createElement("script");
    script.src="https://www.chatbase.co/embed.min.js";
    script.id="F7zGClTygFqVUqLlFTPAj";
    script.domain="www.chatbase.co";
    document.body.appendChild(script)
  };
  if(document.readyState==="complete"){onLoad()}
  else{window.addEventListener("load",onLoad)}
})();
</script>
</html>