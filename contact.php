<?php
require_once 'config/database.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $phone = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $subject = mysqli_real_escape_string($conn, trim($_POST['subject']));
    $message = mysqli_real_escape_string($conn, trim($_POST['message']));
    
    if (!empty($name) && !empty($email) && !empty($subject) && !empty($message)) {
        $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) 
                VALUES ('$name', '$email', '$phone', '$subject', '$message')";
        
        if (mysqli_query($conn, $sql)) {
            $success = 'Thank you for contacting us! We will get back to you soon.';
        } else {
            $error = 'Something went wrong. Please try again.';
        }
    } else {
        $error = 'Please fill in all required fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - DreamEvents</title>
    <link rel="stylesheet" href="assets/css/contact.css">
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
                <li><a href="contact.php" class="active">CONTACT</a></li>
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
            <h1>Get In Touch</h1>
            <p>We'd love to hear from you</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="contact-wrapper">
                <!-- Contact Form -->
                <div class="contact-form-container">
                    <h2>Send Us a Message</h2>
                    <p>Have questions? We're here to help!</p>

                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="" class="contact-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name"><i class="fas fa-user"></i> Full Name *</label>
                                <input type="text" id="name" name="name" required placeholder="Your full name">
                            </div>
                            <div class="form-group">
                                <label for="email"><i class="fas fa-envelope"></i> Email Address *</label>
                                <input type="email" id="email" name="email" required placeholder="your@email.com">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                                <input type="tel" id="phone" name="phone" placeholder="+1 234 567 8900">
                            </div>
                            <div class="form-group">
                                <label for="subject"><i class="fas fa-tag"></i> Subject *</label>
                                <input type="text" id="subject" name="subject" required placeholder="What is this about?">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="message"><i class="fas fa-comment"></i> Message *</label>
                            <textarea id="message" name="message" rows="6" required placeholder="Tell us more..."></textarea>
                        </div>

                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane"></i> Send Message
                        </button>
                    </form>
                </div>

                <!-- Contact Info -->
                <div class="contact-info-container">
                    <h2>Contact Information</h2>
                    <p>Reach out to us through any of these channels</p>

                    <div class="info-cards">
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-content">
                                <h3>Our Location</h3>
                                <p>123 Event Street, Suite 100<br>New York, NY 10001</p>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="info-content">
                                <h3>Phone Number</h3>
                                <p>+1 (234) 567-8900<br>+1 (234) 567-8901</p>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-content">
                                <h3>Email Address</h3>
                                <p>info@dreamevents.com<br>support@dreamevents.com</p>
                            </div>
                        </div>

                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="info-content">
                                <h3>Business Hours</h3>
                                <p>Mon - Fri: 9:00 AM - 6:00 PM<br>Sat: 10:00 AM - 4:00 PM</p>
                            </div>
                        </div>
                    </div>

                    <div class="social-section">
                        <h3>Follow Us</h3>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fab fa-youtube"></i></a>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d193595.15830869428!2d-74.119763973046!3d40.69766374874431!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2s!4v1234567890" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2026 DreamEvents. All rights reserved. | Developed by Angie.Dazun</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/contact.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
