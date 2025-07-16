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
    document.getElementById('cart-count').textContent = 0;
    return;
  }
  let itemsHTML = '';
  cart.forEach((item, index) => {
    itemsHTML += `
      <div class="cart-item">
        <img src="${item.image}" alt="${item.SName}" class="cart-item-image">
        <div class="cart-item-details">
          <h3>${item.SName}</h3>
          <p>Rs.${item.SPrice.toFixed(2)}</p>
        </div>
        <div class="cart-item-total">
          Rs.${item.SPrice.toFixed(2)}
        </div>
        <div class="cart-item-actions" style="margin-left:auto;">
          <button onclick="removeItem(${index})"><i class="fas fa-trash"></i></button>
        </div>
      </div>
    `;
  });
  cartItemsContainer.innerHTML = itemsHTML;
  document.getElementById('cart-count').textContent = cart.length;
}

function removeItem(index) {
  let cart = JSON.parse(localStorage.getItem('cart')) || [];
  cart.splice(index, 1);
  localStorage.setItem('cart', JSON.stringify(cart));
  displayCartItems();
  document.getElementById('cart-count').textContent = cart.length;
}