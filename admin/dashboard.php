<?php
session_start();
require_once 'auth_check.php';
require_once '../config/database.php';

// Fetch statistics
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings"))['count'];
$pending_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings WHERE booking_status='pending'"))['count'];
$confirmed_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings WHERE booking_status='confirmed'"))['count'];
$completed_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings WHERE booking_status='completed'"))['count'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM bookings WHERE payment_status='paid'"))['total'] ?? 0;
$pending_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM bookings WHERE payment_status='pending'"))['total'] ?? 0;
$total_packages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM packages WHERE status='active'"))['count'];
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT customer_email) as count FROM bookings"))['count'];
$new_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM contact_messages WHERE status='new'"))['count'];
$total_testimonials = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM testimonials WHERE status='approved'"))['count'];

// Monthly revenue (last 6 months)
$monthly_revenue = mysqli_query($conn, "SELECT DATE_FORMAT(created_at, '%b') as month, SUM(total_amount) as revenue 
                                         FROM bookings 
                                         WHERE payment_status='paid' AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                                         GROUP BY YEAR(created_at), MONTH(created_at) 
                                         ORDER BY created_at");
$months = [];
$revenues = [];
while($row = mysqli_fetch_assoc($monthly_revenue)) {
    $months[] = $row['month'];
    $revenues[] = (float)$row['revenue'];
}

// Weekly bookings (last 7 days)
$weekly_bookings = mysqli_query($conn, "SELECT DATE_FORMAT(created_at, '%a') as day, COUNT(*) as count 
                                         FROM bookings 
                                         WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                                         GROUP BY DATE(created_at) 
                                         ORDER BY created_at");
$days = [];
$booking_counts = [];
while($row = mysqli_fetch_assoc($weekly_bookings)) {
    $days[] = $row['day'];
    $booking_counts[] = (int)$row['count'];
}

// Top packages
$top_packages = mysqli_query($conn, "SELECT p.name, p.price, COUNT(b.id) as booking_count, SUM(b.total_amount) as revenue
                                      FROM packages p 
                                      LEFT JOIN bookings b ON p.id = b.package_id 
                                      GROUP BY p.id 
                                      ORDER BY booking_count DESC 
                                      LIMIT 5");

// Recent bookings
$recent_bookings = mysqli_query($conn, "SELECT b.*, p.name as package_name 
                                         FROM bookings b 
                                         LEFT JOIN packages p ON p.id = b.package_id 
                                         ORDER BY b.created_at DESC 
                                         LIMIT 6");

// Recent messages
$recent_messages = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");

// Growth calculations
$last_month_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings WHERE created_at >= DATE_SUB(NOW(), INTERVAL 2 MONTH) AND created_at < DATE_SUB(NOW(), INTERVAL 1 MONTH)"))['count'];
$this_month_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)"))['count'];
$booking_growth = $last_month_bookings > 0 ? round((($this_month_bookings - $last_month_bookings) / $last_month_bookings) * 100, 1) : 0;

$last_month_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM bookings WHERE payment_status='paid' AND created_at >= DATE_SUB(NOW(), INTERVAL 2 MONTH) AND created_at < DATE_SUB(NOW(), INTERVAL 1 MONTH)"))['total'] ?? 0;
$this_month_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM bookings WHERE payment_status='paid' AND created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)"))['total'] ?? 0;
$revenue_growth = $last_month_revenue > 0 ? round((($this_month_revenue - $last_month_revenue) / $last_month_revenue) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DreamEvents Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Dashboard Specific Styles */
        .dashboard-content {
            padding: 2rem;
        }

        .welcome-banner {
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            border-radius: 16px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            color: white;
            box-shadow: 0 8px 24px rgba(255, 107, 157, 0.3);
            position: relative;
            overflow: hidden;
        }

        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .welcome-content {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .welcome-text h1 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
        }

        .welcome-text p {
            font-size: 1.05rem;
            opacity: 0.95;
        }

        .welcome-stats {
            display: flex;
            gap: 3rem;
        }

        .welcome-stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            display: block;
        }

        .welcome-stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 14px;
            padding: 1.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            opacity: 0.08;
        }

        .stat-card.primary::before { background: #ff6b9d; }
        .stat-card.success::before { background: #27ae60; }
        .stat-card.warning::before { background: #f39c12; }
        .stat-card.info::before { background: #3498db; }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: white;
        }

        .stat-card.primary .stat-icon { background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%); }
        .stat-card.success .stat-icon { background: linear-gradient(135deg, #27ae60 0%, #229954 100%); }
        .stat-card.warning .stat-icon { background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); }
        .stat-card.info .stat-icon { background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); }

        .stat-trend {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .stat-trend.up {
            background: #d4edda;
            color: #155724;
        }

        .stat-body h3 {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #7f8c8d;
        }

        .stat-footer {
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #ecf0f1;
            font-size: 0.85rem;
            color: #95a5a6;
        }

        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .chart-card {
            background: white;
            border-radius: 14px;
            padding: 1.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f8f9fa;
        }

        .chart-header h3 {
            font-size: 1.2rem;
            color: #2c3e50;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-header i {
            color: #ff6b9d;
        }

        .chart-filter select {
            padding: 0.5rem 1rem;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-size: 0.85rem;
        }

        .chart-canvas {
            height: 280px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .content-card {
            background: white;
            border-radius: 14px;
            padding: 1.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f8f9fa;
        }

        .content-header h3 {
            font-size: 1.15rem;
            color: #2c3e50;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .view-all {
            color: #ff6b9d;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .bookings-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bookings-table thead {
            background: #f8f9fa;
        }

        .bookings-table th {
            text-align: left;
            padding: 0.85rem;
            font-weight: 600;
            color: #7f8c8d;
            font-size: 0.8rem;
            text-transform: uppercase;
        }

        .bookings-table td {
            padding: 1rem 0.85rem;
            border-bottom: 1px solid #f8f9fa;
        }

        .customer-name {
            font-weight: 600;
            color: #2c3e50;
        }

        .customer-email {
            font-size: 0.8rem;
            color: #95a5a6;
            display: block;
        }

        .amount {
            font-weight: 700;
            color: #27ae60;
        }

        .status-badge {
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-badge.pending { background: #fff3cd; color: #856404; }
        .status-badge.confirmed { background: #d1ecf1; color: #0c5460; }
        .status-badge.completed { background: #d4edda; color: #155724; }
        .status-badge.cancelled { background: #f8d7da; color: #721c24; }

        .packages-list {
            display: flex;
            flex-direction: column;
            gap: 1.15rem;
        }

        .package-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .package-rank {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            color: white;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .package-name {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.3rem;
        }

        .package-stats {
            display: flex;
            gap: 1.25rem;
            font-size: 0.8rem;
            color: #7f8c8d;
        }

        .package-stat i {
            color: #ff6b9d;
        }

        .messages-list {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .message-item {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
            border-left: 3px solid #ff6b9d;
        }

        .message-item.new {
            background: #ebf8ff;
            border-left-color: #3498db;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.4rem;
        }

        .message-name {
            font-weight: 600;
            color: #2c3e50;
            font-size: 0.9rem;
        }

        .message-time {
            font-size: 0.7rem;
            color: #95a5a6;
        }

        .message-email {
            font-size: 0.8rem;
            color: #7f8c8d;
            margin-bottom: 0.4rem;
        }

        .message-subject {
            font-size: 0.85rem;
            color: #34495e;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 2rem;
        }

        .action-btn {
            padding: 1.15rem;
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            color: white;
            border-radius: 10px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.85rem;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(255, 107, 157, 0.3);
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 16px rgba(255, 107, 157, 0.4);
        }

        .action-btn i {
            font-size: 1.3rem;
        }

        .action-btn span {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .action-btn:nth-child(2) { background: linear-gradient(135deg, #27ae60 0%, #229954 100%); }
        .action-btn:nth-child(3) { background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); }
        .action-btn:nth-child(4) { background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); }

        @media (max-width: 1200px) {
            .content-grid { grid-template-columns: 1fr; }
            .charts-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <?php include 'includes/topbar.php'; ?>

            <!-- Dashboard Content -->
            <div class="dashboard-content">
                <!-- Welcome Banner -->
                <div class="welcome-banner">
                    <div class="welcome-content">
                        <div class="welcome-text">
                            <h1>👋 Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h1>
                            <p>Here's what's happening with your events today</p>
                        </div>
                        <div class="welcome-stats">
                            <div class="welcome-stat">
                                <span class="welcome-stat-value"><?php echo $total_bookings; ?></span>
                                <span class="welcome-stat-label">Total Bookings</span>
                            </div>
                            <div class="welcome-stat">
                                <span class="welcome-stat-value">$<?php echo number_format($total_revenue, 2); ?></span>
                                <span class="welcome-stat-label">Total Revenue</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-trend <?php echo $booking_growth >= 0 ? 'up' : 'down'; ?>">
                                <i class="fas fa-arrow-<?php echo $booking_growth >= 0 ? 'up' : 'down'; ?>"></i>
                                <span><?php echo abs($booking_growth); ?>%</span>
                            </div>
                        </div>
                        <div class="stat-body">
                            <h3><?php echo $total_bookings; ?></h3>
                            <p class="stat-label">Total Bookings</p>
                        </div>
                        <div class="stat-footer">
                            <i class="fas fa-clock"></i> <?php echo $pending_bookings; ?> pending approval
                        </div>
                    </div>

                    <div class="stat-card success">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <div class="stat-trend <?php echo $revenue_growth >= 0 ? 'up' : 'down'; ?>">
                                <i class="fas fa-arrow-<?php echo $revenue_growth >= 0 ? 'up' : 'down'; ?>"></i>
                                <span><?php echo abs($revenue_growth); ?>%</span>
                            </div>
                        </div>
                        <div class="stat-body">
                            <h3>$<?php echo number_format($total_revenue, 2); ?></h3>
                            <p class="stat-label">Total Revenue</p>
                        </div>
                        <div class="stat-footer">
                            <i class="fas fa-hourglass-half"></i> $<?php echo number_format($pending_revenue, 2); ?> pending
                        </div>
                    </div>

                    <div class="stat-card warning">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-trend up">
                                <i class="fas fa-arrow-up"></i>
                                <span>8.5%</span>
                            </div>
                        </div>
                        <div class="stat-body">
                            <h3><?php echo $total_customers; ?></h3>
                            <p class="stat-label">Total Customers</p>
                        </div>
                        <div class="stat-footer">
                            <i class="fas fa-star"></i> <?php echo $total_testimonials; ?> testimonials
                        </div>
                    </div>

                    <div class="stat-card info">
                        <div class="stat-header">
                            <div class="stat-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <?php if($new_messages > 0): ?>
                            <div class="stat-trend up">
                                <i class="fas fa-exclamation"></i>
                                <span>New</span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="stat-body">
                            <h3><?php echo $new_messages; ?></h3>
                            <p class="stat-label">New Messages</p>
                        </div>
                        <div class="stat-footer">
                            <i class="fas fa-box"></i> <?php echo $total_packages; ?> active packages
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="charts-grid">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-line"></i> Revenue Trend</h3>
                            <div class="chart-filter">
                                <select>
                                    <option>Last 6 Months</option>
                                    <option>Last 3 Months</option>
                                    <option>This Year</option>
                                </select>
                            </div>
                        </div>
                        <div class="chart-canvas">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-bar"></i> Weekly Bookings</h3>
                            <div class="chart-filter">
                                <select>
                                    <option>Last 7 Days</option>
                                    <option>Last 14 Days</option>
                                    <option>Last 30 Days</option>
                                </select>
                            </div>
                        </div>
                        <div class="chart-canvas">
                            <canvas id="bookingsChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="content-grid">
                    <!-- Recent Bookings -->
                    <div class="content-card">
                        <div class="content-header">
                            <h3><i class="fas fa-calendar-alt"></i> Recent Bookings</h3>
                            <a href="bookings.php" class="view-all">View All →</a>
                        </div>
                        <table class="bookings-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Package</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($booking = mysqli_fetch_assoc($recent_bookings)): ?>
                                <tr>
                                    <td>
                                        <span class="customer-name"><?php echo htmlspecialchars($booking['customer_name']); ?></span>
                                        <span class="customer-email"><?php echo htmlspecialchars($booking['customer_email']); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($booking['package_name']); ?></td>
                                    <td>
                                        <span class="amount">$<?php echo number_format($booking['total_amount'], 2); ?></span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $booking['booking_status']; ?>">
                                            <?php echo ucfirst($booking['booking_status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Right Column -->
                    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                        <!-- Top Packages -->
                        <div class="content-card">
                            <div class="content-header">
                                <h3><i class="fas fa-trophy"></i> Top Packages</h3>
                                <a href="packages.php" class="view-all">View All →</a>
                            </div>
                            <div class="packages-list">
                                <?php 
                                $rank = 1;
                                while($package = mysqli_fetch_assoc($top_packages)): 
                                ?>
                                <div class="package-item">
                                    <div class="package-rank"><?php echo $rank++; ?></div>
                                    <div class="package-info">
                                        <div class="package-name"><?php echo htmlspecialchars($package['name']); ?></div>
                                        <div class="package-stats">
                                            <span class="package-stat">
                                                <i class="fas fa-shopping-cart"></i>
                                                <?php echo $package['booking_count']; ?> bookings
                                            </span>
                                            <span class="package-stat">
                                                <i class="fas fa-dollar-sign"></i>
                                                $<?php echo number_format($package['revenue'] ?? 0, 2); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>

                        <!-- Recent Messages -->
                        <div class="content-card">
                            <div class="content-header">
                                <h3><i class="fas fa-comments"></i> Recent Messages</h3>
                                <a href="messages.php" class="view-all">View All →</a>
                            </div>
                            <div class="messages-list">
                                <?php while($message = mysqli_fetch_assoc($recent_messages)): ?>
                                <div class="message-item <?php echo $message['status'] == 'new' ? 'new' : ''; ?>">
                                    <div class="message-header">
                                        <span class="message-name"><?php echo htmlspecialchars($message['name']); ?></span>
                                        <span class="message-time"><?php echo date('M d, H:i', strtotime($message['created_at'])); ?></span>
                                    </div>
                                    <div class="message-email"><?php echo htmlspecialchars($message['email']); ?></div>
                                    <div class="message-subject"><?php echo htmlspecialchars($message['subject']); ?></div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions">
                    <a href="packages.php" class="action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <span>Add Package</span>
                    </a>
                    <a href="bookings.php" class="action-btn">
                        <i class="fas fa-calendar-plus"></i>
                        <span>New Booking</span>
                    </a>
                    <a href="gallery.php" class="action-btn">
                        <i class="fas fa-images"></i>
                        <span>Add Gallery</span>
                    </a>
                    <a href="messages.php" class="action-btn">
                        <i class="fas fa-envelope-open"></i>
                        <span>View Messages</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Revenue ($)',
                    data: <?php echo json_encode($revenues); ?>,
                    borderColor: '#ff6b9d',
                    backgroundColor: 'rgba(255, 107, 157, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointBackgroundColor: '#ff6b9d',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#2c3e50',
                        padding: 12,
                        borderColor: '#ff6b9d',
                        borderWidth: 2,
                        callbacks: {
                            label: (context) => '$' + context.parsed.y.toFixed(2)
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f8f9fa' },
                        ticks: {
                            callback: (value) => '$' + value,
                            color: '#95a5a6'
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#95a5a6' }
                    }
                }
            }
        });

        // Bookings Chart
        const bookingsCtx = document.getElementById('bookingsChart').getContext('2d');
        new Chart(bookingsCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($days); ?>,
                datasets: [{
                    label: 'Bookings',
                    data: <?php echo json_encode($booking_counts); ?>,
                    backgroundColor: [
                        'rgba(255, 107, 157, 0.8)',
                        'rgba(196, 69, 105, 0.8)',
                        'rgba(255, 107, 157, 0.8)',
                        'rgba(196, 69, 105, 0.8)',
                        'rgba(255, 107, 157, 0.8)',
                        'rgba(196, 69, 105, 0.8)',
                        'rgba(255, 107, 157, 0.8)'
                    ],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#2c3e50',
                        padding: 12,
                        borderColor: '#ff6b9d',
                        borderWidth: 2
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f8f9fa' },
                        ticks: { stepSize: 1, color: '#95a5a6' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#95a5a6' }
                    }
                }
            }
        });
    </script>
</body>
</html>
