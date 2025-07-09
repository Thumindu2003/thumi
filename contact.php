<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact - Pangolin Creations</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="StyleSheet.css">
  <script src="script.js"></script>
  <script src="account.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
    }
    
    
    
    h1 {
      text-align: center;
      font-size: 36px;
      margin-bottom: 60px;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    
    .contact-methods {
      display: flex;
      justify-content: space-around;
      align-items: flex-start;
      flex-wrap: wrap;
      gap: 30px;
    }
    
    .contact-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 300px;
    }
    
    .contact-icon {
      font-size: 120px;
      margin-bottom: 20px;
    }
    
    .contact-info {
      font-size: 18px;
      font-family: Inknut Antiqua;
    }
    
    .contact-info a {
      text-decoration: none;
      
    }
    
    .email-info a {
      color: blue;
      font-family: Inknut Antiqua;
    }
    
    .facebook-info {
      font-weight: bold;
      font-family: Inknut Antiqua;
    }
    
    
  </style>
</head>
<body>
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
    <h1>Contact us</h1>
    
    <div class="contact-methods">
      <!-- Email Contact -->
      <div class="contact-item">
        <div class="contact-icon">
          <i class="fas fa-envelope"></i>
        </div>
        <div class="contact-info email-info">
          <a href="pangolincreations@gmail.com">pangolincreations@gmail.com</a>
        </div>
      </div>
      
      <!-- Facebook Contact -->
      <div class="contact-item">
        <div class="contact-icon">
          <i class="fab fa-facebook-square"></i>
        </div>
        <div class="contact-info facebook-info">
          <a href="https://www.facebook.com/people/Pangolin-Creations/61560553106574/">Pangolin Creations</a>
        </div>
      </div>
      
      <!-- WhatsApp/Phone Contact -->
      <div class="contact-item">
        <div class="contact-icon">
          <i class="fab fa-whatsapp"></i>
        </div>
        <div class="contact-info">
          076 1062 748
        </div>
      </div>
    </div>
  </main>
</body>
</html>