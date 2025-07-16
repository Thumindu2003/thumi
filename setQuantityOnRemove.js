document.addEventListener('DOMContentLoaded', function() {
    // Select all remover buttons
    document.querySelectorAll('.remover-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            // Find the closest quantity input (adjust selector as needed)
            const quantityInput = btn.closest('.item-row')?.querySelector('.quantity-input');
            if (quantityInput) {
                quantityInput.value = 0;
                // Optionally, trigger change event if needed
                quantityInput.dispatchEvent(new Event('change'));
            }
        });
    });
});
