<?php
/// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Add this line to define the loggedIn status
$loggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;

include "connection.php";

// Use $conn for connection error check
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Services - Pangolin Creations</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="StyleSheet.css">
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
          <?php if(!$loggedIn): ?>
            <li><a href="account.php">Account</a></li>
          <?php endif; ?>
        </ul>
        <?php include 'profile_dropdown.php'; ?>
      </div>
    </nav>
  </header>

  <main>
    <section class="services">
      <?php
      // Use $conn for queries
      $sql = "SELECT SID, SName, SPrice, image FROM tblservice";
      $result = $conn->query($sql);

      if (!$result) {
          die("Query failed: " . $conn->error);
      }

      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $imageSrc = $row['image'] ? 'data:image/jpeg;base64,' . base64_encode($row['image']) : 'Pictures/default.jpg';
              echo '
              <div class="product">
                <img src="' . $imageSrc . '" alt="' . htmlspecialchars($row['SName']) . '" class="servipic">
                <h3 class="nameservices">' . htmlspecialchars($row['SName']) . '</h3>
                <p>Rs.' . number_format($row['SPrice'], 2) . '</p>
                <button onclick="addToCart(' . $row['SID'] . ', \'' . htmlspecialchars($row['SName']) . '\', ' . $row['SPrice'] . ', \'' . $imageSrc . '\')">Add to cart</button>
              </div>';
          }
      } else {
          echo '<p class="no-services">No services available at the moment.</p>';
      }
      ?>
    </section>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
  <script>
    const notyf = new Notyf({
      duration: 3000,
      position: { x: 'right', y: 'top' },
      types: [
        {
          type: 'success',
          background: '#4CAF50',
          icon: { className: 'fas fa-check-circle', tagName: 'i', color: '#fff' },
          dismissible: true
        }
      ]
    });

    function addToCart(SID, SName, SPrice, image) {
      let cart = JSON.parse(localStorage.getItem('cart')) || [];
      const existingItem = cart.find(item => item.SID === SID);
      if (existingItem) {
        existingItem.quantity = (existingItem.quantity || 1) + 1;
      } else {
        cart.push({ SID, SName, SPrice, image, quantity: 1 });
      }
      localStorage.setItem('cart', JSON.stringify(cart));
      updateCartCount();
      notyf.success({ message: `${SName} added to cart!` });
    }

    function updateCartCount() {
      const cart = JSON.parse(localStorage.getItem('cart')) || [];
      // Sum all quantities
      const totalQty = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
      document.getElementById('cart-count').textContent = totalQty;
      localStorage.setItem('cart_count', totalQty);
    }
    
    // Initialize cart count
    updateCartCount();
  </script>
</body>
</html>
<?php $conn->close(); ?>