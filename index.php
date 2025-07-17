<?php
session_start(); // Start session at the very beginning
$loggedIn = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false; // Properly initialize the variable
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pangolin Creations</title>
  <link rel="stylesheet" href="StyleSheet.css" />
  <script src="script.js"></script>
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
          <li><a href="cart.php">Cart </a></li>
          <li><a href="about.php">About Us</a></li>
          <li><a href="contact.php">Contact</a></li>
          <?php if(!$loggedIn): ?>
            <li><a href="account.php">Account</a></li>
          <?php endif; ?>
          <?php include 'profile_dropdown.php'; ?>
        </ul>
        
      </div>
    </nav>
  </header>

  <main>
   <section class="section">
    <div class = "slider">
      <div class="slide">
        <input type ="radio" name="radio-btn" id ="radio1">
        <input type ="radio" name="radio-btn" id ="radio2">
        <input type ="radio" name="radio-btn" id ="radio3">
        <input type ="radio" name="radio-btn" id ="radio4">
         <div class="st">
          <img src="Pictures/slideshow1.jpg" alt = "">
         </div>
         <div class="st first">
          <img src="Pictures/slideshow2.jpg" alt = "">
         </div>
         <div class="st">
          <img src="Pictures/slideshow3.jpg" alt = "">
         </div>
         <div class="st">
          <img src="Pictures/slideshow4.jpg" alt = "">
         </div>
         <div class="nav-auto">
          <div class="a-b1"></div>
          <div class="a-b2"></div>
          <div class="a-b3"></div>
          <div class="a-b4"></div>
         </div>
      </div>
          <div class="nav-m">
            <lable for="radio1" class="m-btn"></lable>
            <lable for="radio2" class="m-btn"></lable>
            <lable for="radio3" class="m-btn"></lable>
            <lable for="radio4" class="m-btn"></lable>
          </div>
    </div>
   
   </section>
   <div class="shop-now-container">
    <a href="services.php" class="shop-now-btn">Shop now</a>
  </div>
  </main>

  <script>
    var counter = 1;
    setInterval(function(){
      document.getElementById("radio"+counter).checked=true;
      counter++;
      if(counter>4){
        counter=1;
      }
    },5000);
  </script>
</body>
</html>
