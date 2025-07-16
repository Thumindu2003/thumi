<?php
require_once 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cart - Pangolin Creations</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="StyleSheet.css">
  <link rel="stylesheet" href="cart.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
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
  </ul>
  <?php include 'profile_dropdown.php'; ?>
</div>
  </header>

  <main>
    <section class="cart-container">
      <div class="cart-items" id="cartItems">
        <!-- Cart items will be dynamically inserted here -->
      </div>
      <div class="cart-summary">
        <h3>Order Summary</h3>
        <div class="summary-details">
          <p>Subtotal: <span id="subtotal">Rs.0</span></p>
          <p>Total: <span id="total">Rs.0</span></p>
        </div>
        <button type="button" id="contactSellerBtn" class="checkout-btn"><i class="fas fa-envelope"></i> Contact Seller</button>
      </div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
  <script src="cart.js"></script>
  <script>
    // Initialize cart count
    document.addEventListener('DOMContentLoaded', function() {
      updateCartCount();
    });

    function updateCartCount() {
      const cart = JSON.parse(localStorage.getItem('cart')) || [];
      const count = cart.reduce((total, item) => total + (item.quantity || 1), 0);
      document.getElementById('cart-count').textContent = count;
    }

    function displayCartItems() {
      // You can implement this function to refresh the cart display after clearing
      // For now, just clear the cart items container
      document.getElementById('cartItems').innerHTML = '';
      document.getElementById('subtotal').textContent = 'Rs.0';
      document.getElementById('total').textContent = 'Rs.0';
    }

    document.getElementById('contactSellerBtn').onclick = function() {
      const cart = JSON.parse(localStorage.getItem('cart')) || [];
      if (cart.length === 0) {
        const notyf = new Notyf();
        notyf.error('Your cart is empty. Please add items before contacting the seller.');
        return;
      }

      const total = document.getElementById('total').textContent.replace('Rs.', '');
      fetch('contact_seller.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
          cart: cart,
          total: total
        })
      })
      .then(response => response.json())
      .then(data => {
        const notyf = new Notyf();
        if (data.success) {
          notyf.success(data.message);
          // Clear the cart after successful submission
          localStorage.removeItem('cart');
          displayCartItems();
          updateCartCount();
        } else {
          notyf.error(data.message);
        }
      })
      .catch(error => {
        const notyf = new Notyf();
        notyf.error('An error occurred while contacting the seller.');
        console.error('Error:', error);
      });
    };
  </script>
</body>
</html>
<?php $conn->close(); ?>