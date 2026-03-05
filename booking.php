<?php
require_once 'config/database.php';

$package_id = isset($_GET['package']) ? intval($_GET['package']) : 0;
$selected_package = null;

if ($package_id > 0) {
    $sql = "SELECT * FROM packages WHERE id = $package_id AND status = 'active'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $selected_package = mysqli_fetch_assoc($result);
    }
}

// Get all active packages for dropdown
$sql = "SELECT id, name, price FROM packages WHERE status = 'active' ORDER BY name ASC";
$packages_result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Event - DreamEvents</title>
    <link rel="stylesheet" href="assets/css/booking.css">
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
                <li><a href="packages.php">PACKAGES</a></li>
                <li><a href="gallery.php">GALLERY</a></li>
                <li><a href="about.php">ABOUT US</a></li>
                <li><a href="contact.php">CONTACT</a></li>
                <li><a href="booking.php" class="btn-book active">BOOK NOW</a></li>
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
            <h1>Book Your Dream Event</h1>
            <p>Fill in the details and let us create magic for you</p>
        </div>
    </section>

    <!-- Booking Form Section -->
    <section class="booking-section">
        <div class="container">
            <div class="booking-wrapper">
                <!-- Booking Form -->
                <div class="booking-form-container">
                    <div class="form-header">
                        <i class="fas fa-calendar-alt"></i>
                        <h2>Event Booking Form</h2>
                        <p>Please provide your event details</p>
                    </div>

                    <form id="bookingForm" class="booking-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="package_id"><i class="fas fa-box"></i> Select Package *</label>
                                <select id="package_id" name="package_id" required>
                                    <option value="">Choose a package</option>
                                    <?php while($pkg = mysqli_fetch_assoc($packages_result)): ?>
                                        <option value="<?php echo $pkg['id']; ?>" 
                                                data-price="<?php echo $pkg['price']; ?>"
                                                <?php echo ($selected_package && $selected_package['id'] == $pkg['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($pkg['name']); ?> - $<?php echo number_format($pkg['price'], 2); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="customer_name"><i class="fas fa-user"></i> Full Name *</label>
                                <input type="text" id="customer_name" name="customer_name" required placeholder="Enter your full name">
                            </div>
                            <div class="form-group">
                                <label for="customer_email"><i class="fas fa-envelope"></i> Email Address *</label>
                                <input type="email" id="customer_email" name="customer_email" required placeholder="your@email.com">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="customer_phone"><i class="fas fa-phone"></i> Phone Number *</label>
                                <input type="tel" id="customer_phone" name="customer_phone" required placeholder="+1 234 567 8900">
                            </div>
                            <div class="form-group">
                                <label for="guests_count"><i class="fas fa-users"></i> Number of Guests *</label>
                                <input type="number" id="guests_count" name="guests_count" min="1" required placeholder="2">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="event_date"><i class="fas fa-calendar"></i> Event Date *</label>
                                <input type="date" id="event_date" name="event_date" required>
                            </div>
                            <div class="form-group">
                                <label for="event_time"><i class="fas fa-clock"></i> Event Time *</label>
                                <input type="time" id="event_time" name="event_time" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="location"><i class="fas fa-map-marker-alt"></i> Event Location *</label>
                            <input type="text" id="location" name="location" required placeholder="Enter venue or location">
                        </div>

                        <div class="form-group">
                            <label for="special_requests"><i class="fas fa-comment"></i> Special Requests</label>
                            <textarea id="special_requests" name="special_requests" rows="4" placeholder="Any special requirements or preferences?"></textarea>
                        </div>

                        <div class="payment-section">
                            <h3><i class="fas fa-credit-card"></i> Payment Method</h3>
                            <div class="payment-methods">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="credit_card" checked>
                                    <span class="payment-label">
                                        <i class="fas fa-credit-card"></i>
                                        <span>Credit Card</span>
                                    </span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="paypal">
                                    <span class="payment-label">
                                        <i class="fab fa-paypal"></i>
                                        <span>PayPal</span>
                                    </span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="bank_transfer">
                                    <span class="payment-label">
                                        <i class="fas fa-university"></i>
                                        <span>Bank Transfer</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn-submit">
                                <i class="fas fa-check-circle"></i> Proceed to Payment
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Booking Summary -->
                <div class="booking-summary">
                    <h3><i class="fas fa-file-invoice"></i> Booking Summary</h3>
                    <div class="summary-content">
                        <div class="summary-item">
                            <span>Package:</span>
                            <strong id="summary-package">Not selected</strong>
                        </div>
                        <div class="summary-item">
                            <span>Base Price:</span>
                            <strong id="summary-price">$0.00</strong>
                        </div>
                        <div class="summary-item">
                            <span>Guests:</span>
                            <strong id="summary-guests">0</strong>
                        </div>
                        <div class="summary-divider"></div>
                        <div class="summary-item total">
                            <span>Total Amount:</span>
                            <strong id="summary-total">$0.00</strong>
                        </div>
                    </div>

                    <div class="summary-info">
                        <i class="fas fa-info-circle"></i>
                        <p>A 30% deposit is required to confirm your booking. The remaining balance is due 7 days before the event.</p>
                    </div>

                    <div class="summary-features">
                        <h4>What's Included:</h4>
                        <ul id="package-features">
                            <li><i class="fas fa-check"></i> Professional Event Planning</li>
                            <li><i class="fas fa-check"></i> Full Setup & Decoration</li>
                            <li><i class="fas fa-check"></i> On-site Coordination</li>
                            <li><i class="fas fa-check"></i> Photography Coverage</li>
                        </ul>
                    </div>

                    <div class="contact-support">
                        <i class="fas fa-headset"></i>
                        <p>Need help? <a href="contact.php">Contact our support team</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content success">
            <i class="fas fa-check-circle"></i>
            <h2>Booking Confirmed!</h2>
            <p>Thank you for choosing DreamEvents. We've sent a confirmation email with your booking details.</p>
            <div class="modal-actions">
                <a href="index.php" class="btn-primary">Back to Home</a>
                <button onclick="closeModal()" class="btn-secondary">Close</button>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2026 DreamEvents. All rights reserved. | Developed by Angie.Dazun</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/booking.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
