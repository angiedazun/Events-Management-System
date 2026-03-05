// Mobile Navigation
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');

    if (hamburger) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });

        document.querySelectorAll('.nav-menu a').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
            });
        });
    }

    // Modal functionality
    const modal = document.getElementById('packageModal');
    const closeModal = document.querySelector('.close-modal');

    if (closeModal) {
        closeModal.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
});

// Open package details modal
function openPackageModal(package) {
    const modal = document.getElementById('packageModal');
    const modalBody = document.getElementById('modalBody');
    
    const features = package.features ? package.features.split('|') : [];
    const imagePath = package.image ? `assets/images/packages/${package.image}` : 'https://images.unsplash.com/photo-1464047736614-af63643285bf?w=500';
    
    modalBody.innerHTML = `
        <div class="modal-package-details">
            <img src="${imagePath}" alt="${package.name}" style="width: 100%; height: 250px; object-fit: cover; border-radius: 10px; margin-bottom: 1.5rem;">
            <h2 style="color: var(--dark-color); margin-bottom: 1rem;">${package.name}</h2>
            <div style="font-size: 2rem; color: var(--primary-color); font-weight: 700; margin-bottom: 1rem;">
                $${parseFloat(package.price).toFixed(2)}
            </div>
            <p style="color: var(--text-color); line-height: 1.8; margin-bottom: 1.5rem;">
                ${package.description}
            </p>
            ${features.length > 0 ? `
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: var(--dark-color); margin-bottom: 1rem;">Package Includes:</h3>
                    <ul style="list-style: none; padding: 0;">
                        ${features.map(feature => `
                            <li style="padding: 0.5rem 0; color: var(--text-color);">
                                <i class="fas fa-check-circle" style="color: var(--primary-color); margin-right: 0.5rem;"></i>
                                ${feature}
                            </li>
                        `).join('')}
                    </ul>
                </div>
            ` : ''}
            <div style="display: flex; gap: 1rem;">
                <a href="booking.php?package=${package.id}" class="btn-primary" style="flex: 1; text-decoration: none; padding: 1rem; text-align: center; border-radius: 10px;">
                    <i class="fas fa-calendar-check"></i> Book This Package
                </a>
                <a href="contact.php" class="btn-secondary" style="flex: 1; text-decoration: none; padding: 1rem; text-align: center; border-radius: 10px;">
                    <i class="fas fa-envelope"></i> Ask Questions
                </a>
            </div>
        </div>
    `;
    
    modal.style.display = 'block';
}

// Animate package cards on scroll
const packageCards = document.querySelectorAll('.package-card');
const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry, index) => {
        if (entry.isIntersecting) {
            setTimeout(() => {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }, index * 100);
            observer.unobserve(entry.target);
        }
    });
}, {
    threshold: 0.1
});

packageCards.forEach(card => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = 'all 0.6s ease';
    observer.observe(card);
});
