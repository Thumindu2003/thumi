<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us - Pangolin Creations</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="StyleSheet.css">
  <script src="script.js"></script>
  <style>  
    body {
      font-family: Inknut Antiqua;
      margin: 0;
      padding: 0;
      line-height: 1.6;
      font-weight: bold;
     
    }
    
    
    
    main {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px 40px;
    }
    
    h1 {
      text-align: center;
      font-size: 36px;
      margin-bottom: 60px;
      font-family: Inknut Antiqua;
      position: relative;
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
    }
    
    h1::after {
      content: "";
      position: absolute;
      width: 90px;
      height: 5px;
      background-color: #f0f0f0;
      bottom: -10px;
      left: 50%;
      transform: translateX(-50%);
      border-radius: 2px;
    }
    
    .about-content {
      font-size: larger;
      line-height: 1.8;
      margin-bottom: 30px;
      text-align: justify;
    }
    
    .tagline {
      font-family: Inknut Antiqua;
      font-size: 44px;
      color: #9c27b0;
      text-align: center;
      margin: 30px 0;
      line-height: 1.4;
      font-weight: bold;
    }
    
    .tagline-container {
      display: flex;
      flex-direction: column;
      align-items: center;
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
    <li><a href="cart.php">Cart <span id="cart-count" class="cart-count">0</span></a></li>
    <li><a href="about.php">About Us</a></li>
    <li><a href="contact.php">Contact</a></li>
    <li><a href="account.php">Account</a></li>
  </ul>
  
</div>
    </nav>
  </header>
  
  <main>
    <h1>About us</h1>
    
    <div class="about-content">
      We are skilled graphic design team with over 2000+ designs created, blending creativity and marketing expertise to deliver impactful visual solutions.
      Our extensive experience helps brands stand out and communicate effectively through high - quality, tailored designs. Let's bring your vision to life...!
    </div>
    
    <div class="tagline-container">
      <div class="tagline">
        "Design That<br>
        Tell Your Story"
      </div>
    </div>
  </main>
</body>
</html>
