<?php
// Database Installation Script
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'events_management');

// Create connection without database
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if (mysqli_query($conn, $sql)) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . mysqli_error($conn) . "<br>";
}

// Select database
mysqli_select_db($conn, DB_NAME);

// Create admin table
$sql = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $sql)) {
    echo "Table 'admins' created successfully<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// Create packages table
$sql = "CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    features TEXT,
    image VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $sql)) {
    echo "Table 'packages' created successfully<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// Create bookings table
$sql = "CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    package_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    location VARCHAR(255) NOT NULL,
    guests_count INT NOT NULL,
    special_requests TEXT,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    booking_status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (package_id) REFERENCES packages(id)
)";
if (mysqli_query($conn, $sql)) {
    echo "Table 'bookings' created successfully<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// Create gallery table
$sql = "CREATE TABLE IF NOT EXISTS gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    image VARCHAR(255) NOT NULL,
    category VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $sql)) {
    echo "Table 'gallery' created successfully<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// Create testimonials table
$sql = "CREATE TABLE IF NOT EXISTS testimonials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_image VARCHAR(255),
    rating INT NOT NULL,
    review TEXT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $sql)) {
    echo "Table 'testimonials' created successfully<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// Create contact messages table
$sql = "CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read', 'replied') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $sql)) {
    echo "Table 'contact_messages' created successfully<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// Insert default admin user (password: admin123)
$default_password = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "INSERT INTO admins (username, email, password, full_name) VALUES 
('admin', 'admin@eventsmanagement.com', '$default_password', 'System Administrator')
ON DUPLICATE KEY UPDATE username=username";
if (mysqli_query($conn, $sql)) {
    echo "Default admin user created successfully<br>";
    echo "<strong>Default Login:</strong> Username: admin, Password: admin123<br>";
} else {
    echo "Error inserting admin: " . mysqli_error($conn) . "<br>";
}

// Insert sample packages
$sql = "INSERT INTO packages (name, description, price, features, image) VALUES 
('Romantic Proposal', 'Create the perfect moment to pop the question with a custom-designed proposal setup', 5000.00, 'Beach/Garden Setup|Professional Photography|Flower Decorations|Romantic Music|Candlelight Ambiance', 'proposal.jpg'),
('Wedding Anniversary', 'Celebrate your love milestone with an elegant anniversary celebration', 7500.00, 'Premium Venue Decoration|Private Dining Setup|Live Music|Video Coverage|Custom Cake|Gift Hamper', 'anniversary.jpg'),
('Birthday Surprise', 'Make their special day unforgettable with a personalized birthday celebration', 4500.00, 'Themed Decorations|Birthday Cake|Balloon Arrangements|Entertainment|Photo Session', 'birthday.jpg'),
('Romantic Dinner', 'Intimate dining experience at a stunning location with premium setup', 3500.00, 'Beachside/Rooftop Setting|Gourmet Meal|Wine Selection|Candle Lighting|Background Music', 'dinner.jpg'),
('Honeymoon Setup', 'Transform your honeymoon suite into a romantic paradise', 6000.00, 'Room Decoration|Flower Arrangements|Champagne & Chocolates|Bath Setup|Photography', 'honeymoon.jpg'),
('Engagement Celebration', 'Celebrate your engagement with friends and family in style', 8500.00, 'Venue Booking|Full Decoration|Catering|DJ/Live Band|Photography & Videography', 'engagement.jpg')
ON DUPLICATE KEY UPDATE name=name";
mysqli_query($conn, $sql);
echo "Sample packages inserted successfully<br>";

echo "<br><strong>Installation completed successfully!</strong><br>";
echo "<a href='../index.php'>Go to Home Page</a> | <a href='../admin/login.php'>Go to Admin Login</a>";

mysqli_close($conn);
?>
