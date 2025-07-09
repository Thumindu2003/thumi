const cartNotyf = new Notyf({
  duration: 3000,
  position: { x: 'right', y: 'top' },
  types: [
    {
      type: 'success',
      background: '#4CAF50',
      icon: { className: 'fas fa-check-circle', tagName: 'i', color: '#fff' },
      dismissible: true
    },
    {
      type: 'warning',
      background: '#FF9800',
      icon: { className: 'fas fa-trash-alt', tagName: 'i', color: '#fff' },
      dismissible: true
    },
    {
      type: 'info',
      background: '#2196F3',
      icon: { className: 'fas fa-phone-alt', tagName: 'i', color: '#fff' },
      dismissible: true
    }
  ]
});

document.addEventListener('DOMContentLoaded', displayCartItems);

function displayCartItems() {
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  const cartItemsContainer = document.getElementById('cartItems');
  
  if (cart.length === 0) {
    cartItemsContainer.innerHTML = '<p class="empty-cart">Your cart is empty</p>';
    updateTotals(0);
    return;
  }

  let itemsHTML = '';
  let subtotal = 0;

  cart.forEach((item, index) => {
    const itemTotal = item.price * (item.quantity || 1);
    subtotal += itemTotal;
    
    itemsHTML += `
      <div class="cart-item">
        <img src="${item.image}" alt="${item.name}" class="cart-item-image">
        <div class="cart-item-details">
          <h3>${item.name}</h3>
          <p>Rs.${item.price.toFixed(2)} ${item.quantity > 1 ? `× ${item.quantity}` : ''}</p>
        </div>
        <div class="cart-item-actions">
          <button onclick="removeItem(${index})"><i class="fas fa-trash"></i></button>
        </div>
        <div class="cart-item-total">
          Rs.${itemTotal.toFixed(2)}
        </div>
      </div>
    `;
  });

  cartItemsContainer.innerHTML = itemsHTML;
  updateTotals(subtotal);
}

function removeItem(index) {
  let cart = JSON.parse(localStorage.getItem('cart')) || [];
  const removedItem = cart[index];
  cart.splice(index, 1);
  localStorage.setItem('cart', JSON.stringify(cart));
  displayCartItems();
  updateCartCount();
  
  cartNotyf.warning({
    message: `${removedItem.name} removed from cart!`,
    icon: { className: 'fas fa-trash-alt', tagName: 'i' }
  });
  
  fetch('cart_api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'remove_item', SID: removedItem.SID })
  }).catch(error => console.error('Error:', error));
}

function updateTotals(subtotal) {
  document.getElementById('subtotal').textContent = `Rs.${subtotal.toFixed(2)}`;
  document.getElementById('total').textContent = `Rs.${subtotal.toFixed(2)}`;
}

function contactSeller() {
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  if (cart.length === 0) {
    cartNotyf.error('Your cart is empty. Please add items before contacting the seller.');
    return;
  }
  
  fetch('cart_api.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'save_cart', cart: cart })
  }).catch(error => console.error('Error:', error));
  
  cartNotyf.info({
    message: 'Seller will contact you shortly about your order!',
    icon: { className: 'fas fa-phone-alt', tagName: 'i' }
  });
}

function updateCartCount() {
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  const count = cart.reduce((total, item) => total + (item.quantity || 1), 0);
  document.getElementById('cart-count').textContent = count;
}