<?php
session_start();
require_once 'connection.php';

if (!isset($_SESSION['username'])) {
    echo "<p>Please log in to view your cart.</p>";
    exit;
}

$user_name = $_SESSION['username'];
// Fetch all pending cart items for the user, only for existing services
$sql = "SELECT c.SID, c.SName, s.SPrice 
        FROM cart_orders c 
        INNER JOIN tblservice s ON c.SID = s.SID 
        WHERE c.user_name = ? AND c.status = 'pending'";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $user_name);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}
$stmt->close();
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
        <!-- Cart items will be rendered by JS -->
      </div>
      <div class="cart-summary">
        <h3>Order Summary</h3>
        <div class="summary-details">
          <p>Subtotal: <span id="subtotal">Rs.0</span></p>
          <p>Total: <span id="total">Rs.0</span></p>
        </div>
        <button type="button" id="confirmOrderBtn" class="checkout-btn"><i class="fas fa-check"></i> Confirm Order</button>
      </div>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
  <script>
    function renderCartItems() {
      const cart = JSON.parse(localStorage.getItem('cart')) || [];
      const cartItemsContainer = document.getElementById('cartItems');
      if (cart.length === 0) {
        cartItemsContainer.innerHTML = '<p class="empty-cart">Your cart is empty</p>';
        document.getElementById('cart-count').textContent = 0;
        document.getElementById('subtotal').textContent = 'Rs.0';
        document.getElementById('total').textContent = 'Rs.0';
        return;
      }
      let itemsHTML = '';
      let subtotal = 0;
      cart.forEach((item, idx) => {
        const qty = item.quantity || 1;
        const itemTotal = item.SPrice * qty;
        subtotal += itemTotal;
        itemsHTML += `
          <div class="cart-item" data-sid="${item.SID}">
            <img src="${item.image}" alt="${item.SName}" class="cart-item-image">
            <span>${item.SName}</span>
            <span class="item-qty">
              Qty: 
              <button onclick="changeCartItemQty(${item.SID}, -1)" class="qty-btn">−</button>
              <span id="qty-value-${item.SID}" style="margin:0 8px;">${qty}</span>
              <button onclick="changeCartItemQty(${item.SID}, 1)" class="qty-btn">+</button>
            </span>
            <span class="item-total">Rs.${itemTotal.toFixed(2)}</span>
            <div style="margin-left:auto;">
              <button class="remove-cart-item" onclick="removeCartItem(${item.SID})"><i class="fas fa-trash"></i> Remove</button>
            </div>
          </div>
        `;
      });
      cartItemsContainer.innerHTML = itemsHTML;
      document.getElementById('cart-count').textContent = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
      document.getElementById('subtotal').textContent = 'Rs.' + subtotal.toFixed(2);
      document.getElementById('total').textContent = 'Rs.' + subtotal.toFixed(2);
    }

    function removeCartItem(SID) {
      let cart = JSON.parse(localStorage.getItem('cart')) || [];
      const idx = cart.findIndex(item => item.SID === SID);
      if (idx !== -1) {
        if ((cart[idx].quantity || 1) > 1) {
          cart[idx].quantity -= 1;
        } else {
          cart.splice(idx, 1);
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCartItems();
        new Notyf().success('Item removed from cart.');
      }
    }

    function changeCartItemQty(SID, delta) {
      let cart = JSON.parse(localStorage.getItem('cart')) || [];
      const idx = cart.findIndex(item => item.SID === SID);
      if (idx !== -1) {
        let newQty = (cart[idx].quantity || 1) + delta;
        if (newQty < 1) {
          new Notyf().error('Quantity must be at least 1.');
          return;
        }
        cart[idx].quantity = newQty;
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCartItems();
        new Notyf().success('Quantity updated.');
      }
    }

    document.getElementById('confirmOrderBtn').onclick = function() {
      const cart = JSON.parse(localStorage.getItem('cart')) || [];
      if (cart.length === 0) {
        new Notyf().error('Your cart is empty. Please add items before confirming the order.');
        return;
      }
      const total = document.getElementById('total').textContent.replace('Rs.', '');
      fetch('save_order.php', {
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
          localStorage.removeItem('cart');
          document.getElementById('cart-count').textContent = 0;
          document.getElementById('cartItems').innerHTML = '<p class="empty-cart">Your cart is empty</p>';
          document.getElementById('subtotal').textContent = 'Rs.0';
          document.getElementById('total').textContent = 'Rs.0';
        } else {
          notyf.error(data.message);
        }
      })
      .catch(error => {
        new Notyf().error('An error occurred while confirming the order.');
        console.error('Error:', error);
      });
    };

    // Initial render
    renderCartItems();
  </script>
<?php
$mysqli->close();
?>
</body>
</html>