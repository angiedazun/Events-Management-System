<?php
require_once 'config/database.php';

$sql = "SELECT * FROM gallery WHERE status = 'active' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - DreamEvents</title>
    <link rel="stylesheet" href="assets/css/gallery.css">
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
                <li><a href="gallery.php" class="active">GALLERY</a></li>
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
            <h1>Our Gallery</h1>
            <p>Witness the magic we create for every special moment</p>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section">
        <div class="container">
            <div class="gallery-filters">
                <button class="filter-btn active" data-filter="all">All</button>
                <button class="filter-btn" data-filter="proposals">Proposals</button>
                <button class="filter-btn" data-filter="birthdays">Birthdays</button>
                <button class="filter-btn" data-filter="anniversaries">Anniversaries</button>
                <button class="filter-btn" data-filter="weddings">Weddings</button>
            </div>

            <div class="gallery-grid">
                <!-- Sample images - You should populate from database -->
                <?php
                $sample_images = [
                    ['title' => 'Beach Proposal', 'category' => 'proposals', 'img' => 'https://images.unsplash.com/photo-1519741497674-611481863552?w=500'],
                    ['title' => 'Romantic Dinner', 'category' => 'anniversaries', 'img' => 'https://images.unsplash.com/photo-1464047736614-af63643285bf?w=500'],
                    ['title' => 'Birthday Celebration', 'category' => 'birthdays', 'img' => 'https://images.unsplash.com/photo-1530103862676-de8c9debad1d?w=500'],
                    ['title' => 'Wedding Setup', 'category' => 'weddings', 'img' => 'https://images.unsplash.com/photo-1519167758481-83f29da8c856?w=500'],
                    ['title' => 'Garden Proposal', 'category' => 'proposals', 'img' => 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?w=500'],
                    ['title' => 'Anniversary Dinner', 'category' => 'anniversaries', 'img' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=500'],
                    ['title' => 'Surprise Party', 'category' => 'birthdays', 'img' => 'https://images.unsplash.com/photo-1527529482837-4698179dc6ce?w=500'],
                    ['title' => 'Beachside Wedding', 'category' => 'weddings', 'img' => 'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?w=500'],
                    ['title' => 'Rooftop Proposal', 'category' => 'proposals', 'img' => 'https://images.unsplash.com/photo-1469371670807-013ccf25f16a?w=500'],
                ];

                foreach ($sample_images as $image):
                ?>
                    <div class="gallery-item" data-category="<?php echo $image['category']; ?>">
                        <img src="<?php echo $image['img']; ?>" alt="<?php echo $image['title']; ?>">
                        <div class="gallery-overlay">
                            <h3><?php echo $image['title']; ?></h3>
                            <button class="btn-view" onclick="openLightbox('<?php echo $image['img']; ?>', '<?php echo $image['title']; ?>')">
                                <i class="fas fa-search-plus"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Lightbox -->
    <div id="lightbox" class="lightbox">
        <span class="close-lightbox">&times;</span>
        <div class="lightbox-content">
            <img id="lightboxImg" src="" alt="">
            <div class="lightbox-caption" id="lightboxCaption"></div>
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

    <script src="assets/js/gallery.js"></script>
</body>
</html>
<?php mysqli_close($conn); ?>
