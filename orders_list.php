<button class="remove-btn" data-order-id="<?php echo $order['id']; ?>">Remove</button>

<script>
document.querySelectorAll('.remove-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var orderId = this.getAttribute('data-order-id');
        fetch('remove_order.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'order_id=' + encodeURIComponent(orderId)
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === 'success') {
                location.reload();
            } else {
                alert('Failed to remove order');
            }
        });
    });
});
</script>
