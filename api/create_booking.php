<?php
header('Content-Type: application/json');
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get form data
$package_id = isset($_POST['package_id']) ? intval($_POST['package_id']) : 0;
$customer_name = isset($_POST['customer_name']) ? mysqli_real_escape_string($conn, trim($_POST['customer_name'])) : '';
$customer_email = isset($_POST['customer_email']) ? mysqli_real_escape_string($conn, trim($_POST['customer_email'])) : '';
$customer_phone = isset($_POST['customer_phone']) ? mysqli_real_escape_string($conn, trim($_POST['customer_phone'])) : '';
$event_date = isset($_POST['event_date']) ? mysqli_real_escape_string($conn, $_POST['event_date']) : '';
$event_time = isset($_POST['event_time']) ? mysqli_real_escape_string($conn, $_POST['event_time']) : '';
$location = isset($_POST['location']) ? mysqli_real_escape_string($conn, trim($_POST['location'])) : '';
$guests_count = isset($_POST['guests_count']) ? intval($_POST['guests_count']) : 0;
$special_requests = isset($_POST['special_requests']) ? mysqli_real_escape_string($conn, trim($_POST['special_requests'])) : '';
$payment_method = isset($_POST['payment_method']) ? mysqli_real_escape_string($conn, $_POST['payment_method']) : '';
$total_amount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;

// Validate required fields
if (empty($package_id) || empty($customer_name) || empty($customer_email) || empty($customer_phone) || 
    empty($event_date) || empty($event_time) || empty($location) || $guests_count < 1) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
    exit;
}

// Validate email
if (!filter_var($customer_email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Verify package exists
$package_check = mysqli_query($conn, "SELECT id, price FROM packages WHERE id = $package_id AND status = 'active'");
if (mysqli_num_rows($package_check) == 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid package selected']);
    exit;
}

$package = mysqli_fetch_assoc($package_check);
$total_amount = $package['price']; // Use package price from database

// Generate transaction ID
$transaction_id = 'TXN' . date('YmdHis') . rand(1000, 9999);

// Insert booking
$sql = "INSERT INTO bookings (
    package_id, customer_name, customer_email, customer_phone,
    event_date, event_time, location, guests_count, special_requests,
    total_amount, payment_method, transaction_id,
    payment_status, booking_status
) VALUES (
    $package_id, '$customer_name', '$customer_email', '$customer_phone',
    '$event_date', '$event_time', '$location', $guests_count, '$special_requests',
    $total_amount, '$payment_method', '$transaction_id',
    'pending', 'pending'
)";

if (mysqli_query($conn, $sql)) {
    $booking_id = mysqli_insert_id($conn);
    
    echo json_encode([
        'success' => true,
        'message' => 'Booking created successfully',
        'booking_id' => $booking_id,
        'transaction_id' => $transaction_id
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . mysqli_error($conn)
    ]);
}

mysqli_close($conn);
?>
