<?php
require_once 'config/database.php';

// Get all active packages
$sql = "SELECT * FROM packages WHERE status = 'active' ORDER BY price ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Packages - DreamEvents</title>
    <link rel="stylesheet" href="assets/css/packages.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h1><i class="fas fa-heart"></i> DreamEvents</h1>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">HOME</a></li>
                <li><a href="packages.php" class="active">PACKAGES</a></li>
                <li><a href="gallery.php">GALLERY</a></li>
                <li><a href="about.php">ABOUT US</a></li>
                <li><a href="contact.php">CONTACT</a></li>
                <li><a href="booking.php" class="btn-book">BOOK NOW</a></li>
            </ul>
            <div class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="header-overlay"></div>
        <div class="container">
            <h1>Our Premium Packages</h1>
            <p>Choose the perfect package for your special moment</p>
        </div>
    </section>

    <!-- Packages Section -->
    <section class="packages-section">
        <div class="container">
            <div class="packages-grid">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while($package = mysqli_fetch_assoc($result)): ?>
                        <?php
                        $features = $package['features'] ? explode('|', $package['features']) : [];
                        $imagePath = $package['image'] ? "assets/images/packages/{$package['image']}" : "https://images.unsplash.com/photo-1464047736614-af63643285bf?w=500";
                        ?>
                        <div class="package-card">
                            <div class="package-image" style="background-image: url('<?php echo $imagePath; ?>')">
                                <div class="package-overlay">
                                    <a href="booking.php?package=<?php echo $package['id']; ?>" class="btn-view">Book This Package</a>
                                </div>
                            </div>
                            <div class="package-content">
                                <h3><?php echo htmlspecialchars($package['name']); ?></h3>
                                <div class="package-price">
                                    <span class="currency">$</span>
                                    <span class="amount"><?php echo number_format($package['price'], 2); ?></span>
                                </div>
                                <p class="package-description"><?php echo htmlspecialchars($package['description']); ?></p>
                                
                                <?php if (!empty($features)): ?>
                                <div class="package-features">
                                    <h4>Package Includes:</h4>
                                    <ul>
                                        <?php foreach($features as $feature): ?>
                                            <li><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($feature); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <?php endif; ?>

                                <div class="package-actions">
                                    <a href="booking.php?package=<?php echo $package['id']; ?>" class="btn-primary">
                                        <i class="fas fa-calendar-check"></i> Book Now
                                    </a>
                                    <button class="btn-secondary" onclick="openPackageModal(<?php echo htmlspecialchars(json_encode($package)); ?>)">
                                        <i class="fas fa-info-circle"></i> More Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="no-packages">
                        <i class="fas fa-box-open"></i>
                        <h3>No packages available at the moment</h3>
                        <p>Please check back later or contact us for custom packages</p>
                        <a href="contact.php" class="btn-primary">Contact Us</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Custom Package CTA -->
    <section class="custom-package-section">
        <div class="container">
            <div class="custom-package-content">
                <i class="fas fa-magic"></i>
                <h2>Need Something Custom?</h2>
                <p>We can create a personalized package tailored to your specific needs and budget</p>
                <a href="contact.php" class="btn-primary">Request Custom Package</a>
            </div>
        </div>
    </section>

    <!-- Package Details Modal -->
    <div id="packageModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <div id="modalBody"></div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>DreamEvents</h3>
                    <p>Creating unforgettable romantic moments and luxury events that last a lifetime.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="packages.php">Packages</a></li>
                        <li><a href="gallery.php">Gallery</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Contact Info</h3>
                    <ul>
                        <li><i class="fas fa-phone"></i> +1 234 567 8900</li>
                        <li><i class="fas fa-envelope"></i> info@dreamevents.com</li>
                        <li><i class="fas fa-map-marker-alt"></i> 123 Event Street, City</li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Business Hours</h3>
                    <ul>
                        <li>Monday - Friday: 9am - 6pm</li>
                        <li>Saturday: 10am - 4pm</li>
                        <li>Sunday: Closed</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 DreamEvents. All rights reserved. | Developed by Angie.Dazun</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/packages.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
