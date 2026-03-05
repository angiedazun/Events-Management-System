// Mobile Navigation Toggle
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navMenu = document.querySelector('.nav-menu');

    if (hamburger) {
        hamburger.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            hamburger.classList.toggle('active');
        });

        // Close menu when clicking on a link
        document.querySelectorAll('.nav-menu a').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('active');
                hamburger.classList.remove('active');
            });
        });
    }

    // Navbar scroll effect
    window.addEventListener('scroll', () => {
        const navbar = document.querySelector('.navbar');
        if (window.scrollY > 50) {
            navbar.style.boxShadow = '0 5px 20px rgba(0,0,0,0.1)';
        } else {
            navbar.style.boxSadow = '0 2px 10px rgba(0,0,0,0.1)';
        }
    });

    // Load packages on home page
    loadHomePackages();

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'fadeInUp 0.8s ease forwards';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe service cards, package cards, feature cards
    document.querySelectorAll('.service-card, .package-card, .feature-card, .step-card').forEach(el => {
        observer.observe(el);
    });
});

// Load featured packages for home page
function loadHomePackages() {
    const packagesGrid = document.getElementById('packagesGrid');
    if (!packagesGrid) return;

    fetch('api/get_packages.php?limit=3')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.packages.length > 0) {
                packagesGrid.innerHTML = '';
                data.packages.forEach(package => {
                    const packageCard = createPackageCard(package);
                    packagesGrid.innerHTML += packageCard;
                });
            } else {
                packagesGrid.innerHTML = '<p style="text-align:center; grid-column: 1/-1;">No packages available at the moment.</p>';
            }
        })
        .catch(error => {
            console.error('Error loading packages:', error);
            packagesGrid.innerHTML = '<p style="text-align:center; grid-column: 1/-1;">Error loading packages.</p>';
        });
}

// Create package card HTML
function createPackageCard(package) {
    const features = package.features ? package.features.split('|').slice(0, 4) : [];
    const imagePath = package.image ? `assets/images/packages/${package.image}` : 'https://images.unsplash.com/photo-1464047736614-af63643285bf?w=500';
    
    return `
        <div class="package-card">
            <div class="package-image" style="background-image: url('${imagePath}')">
                <span class="package-badge">Popular</span>
            </div>
            <div class="package-content">
                <h3>${package.name}</h3>
                <p>${package.description.substring(0, 100)}...</p>
                <div class="package-price">$${parseFloat(package.price).toFixed(2)}</div>
                <ul class="package-features">
                    ${features.map(feature => `<li><i class="fas fa-check"></i> ${feature}</li>`).join('')}
                </ul>
                <a href="booking.php?package=${package.id}" class="btn-primary" style="width: 100%; text-align: center;">Book Now</a>
            </div>
        </div>
    `;
}

// Counter animation for stats
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    const timer = setInterval(() => {
        start += increment;
        if (start >= target) {
            element.textContent = target + '+';
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(start) + '+';
        }
    }, 16);
}

// Initialize counters when visible
const statItems = document.querySelectorAll('.stat-item h3');
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const text = entry.target.textContent;
            const number = parseInt(text.replace(/\D/g, ''));
            if (number) {
                animateCounter(entry.target, number);
                statsObserver.unobserve(entry.target);
            }
        }
    });
}, { threshold: 0.5 });

statItems.forEach(item => statsObserver.observe(item));
