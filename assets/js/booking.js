// Mobile Navigation
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');

    if (hamburger) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    }

    // Set minimum date to today
    const dateInput = document.getElementById('event_date');
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);

    // Update summary when package changes
    const packageSelect = document.getElementById('package_id');
    const guestsInput = document.getElementById('guests_count');

    packageSelect.addEventListener('change', updateSummary);
    guestsInput.addEventListener('input', updateSummary);

    // Initial summary update if package is pre-selected
    if (packageSelect.value) {
        updateSummary();
    }

    // Handle form submission
    const bookingForm = document.getElementById('bookingForm');
    bookingForm.addEventListener('submit', handleBookingSubmit);
});

// Update booking summary
function updateSummary() {
    const packageSelect = document.getElementById('package_id');
    const guestsInput = document.getElementById('guests_count');
    const selectedOption = packageSelect.options[packageSelect.selectedIndex];
    
    if (packageSelect.value) {
        const packageName = selectedOption.text.split(' - $')[0];
        const basePrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
        const guests = parseInt(guestsInput.value) || 1;
        
        // Calculate total (you can add more complex calculations here)
        const total = basePrice;
        
        document.getElementById('summary-package').textContent = packageName;
        document.getElementById('summary-price').textContent = '$' + basePrice.toFixed(2);
        document.getElementById('summary-guests').textContent = guests;
        document.getElementById('summary-total').textContent = '$' + total.toFixed(2);
    } else {
        document.getElementById('summary-package').textContent = 'Not selected';
        document.getElementById('summary-price').textContent = '$0.00';
        document.getElementById('summary-guests').textContent = '0';
        document.getElementById('summary-total').textContent = '$0.00';
    }
}

// Handle booking form submission
function handleBookingSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const packageSelect = document.getElementById('package_id');
    const selectedOption = packageSelect.options[packageSelect.selectedIndex];
    const packagePrice = parseFloat(selectedOption.getAttribute('data-price')) || 0;
    
    formData.append('total_amount', packagePrice);
    
    // Show loading state
    const submitBtn = e.target.querySelector('.btn-submit');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    submitBtn.disabled = true;
    
    // Send booking data to server
    fetch('api/create_booking.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success modal
            showSuccessModal();
            // Reset form
            e.target.reset();
            updateSummary();
        } else {
            alert('Booking failed: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Show success modal
function showSuccessModal() {
    const modal = document.getElementById('successModal');
    modal.style.display = 'block';
    
    // Auto close after 5 seconds
    setTimeout(() => {
        closeModal();
    }, 5000);
}

// Close modal
function closeModal() {
    const modal = document.getElementById('successModal');
    modal.style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('successModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Form validation animations
document.querySelectorAll('.form-group input, .form-group select, .form-group textarea').forEach(field => {
    field.addEventListener('invalid', function() {
        this.classList.add('error');
    });
    
    field.addEventListener('input', function() {
        if (this.validity.valid) {
            this.classList.remove('error');
        }
    });
});
