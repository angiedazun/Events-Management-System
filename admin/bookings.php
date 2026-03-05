<?php
session_start();
require_once 'auth_check.php';
require_once '../config/database.php';

// Get all bookings with package information
$sql = "SELECT b.*, p.name as package_name 
        FROM bookings b 
        LEFT JOIN packages p ON b.package_id = p.id 
        ORDER BY b.created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - DreamEvents Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-content {
            padding: 2rem;
            background: #f5f7fa;
            min-height: 100vh;
        }

        .page-header {
            margin-bottom: 2rem;
            background: white;
            padding: 2rem;
            border-radius: 16px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .page-header h1 {
            font-size: 2rem;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .page-header h1 i {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .page-header p {
            color: #7f8c8d;
            font-size: 1rem;
            margin: 0;
        }

        .content-card {
            background: white;
            border-radius: 16px;
            padding: 0;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .card-header {
            padding: 1.75rem 2rem;
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header h2 {
            font-size: 1.35rem;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0;
        }

        .card-header i {
            color: white;
        }

        .table-container {
            padding: 0;
            width: 100%;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .data-table thead {
            background: linear-gradient(135deg, #f8f9fa 0%, #ecf0f1 100%);
        }

        .data-table th {
            text-align: left;
            padding: 1rem 0.75rem;
            font-weight: 700;
            color: #2c3e50;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 3px solid #ff6b9d;
        }

        .data-table td {
            padding: 1rem 0.75rem;
            border-bottom: 1px solid #ecf0f1;
            color: #34495e;
            font-size: 0.85rem;
            vertical-align: middle;
            word-wrap: break-word;
        }

        .data-table th:nth-child(1) { width: 12%; } /* Customer */
        .data-table th:nth-child(2) { width: 15%; } /* Email */
        .data-table th:nth-child(3) { width: 10%; } /* Phone */
        .data-table th:nth-child(4) { width: 12%; } /* Package */
        .data-table th:nth-child(5) { width: 10%; } /* Event Date */
        .data-table th:nth-child(6) { width: 8%; } /* Amount */
        .data-table th:nth-child(7) { width: 10%; } /* Payment */
        .data-table th:nth-child(8) { width: 10%; } /* Status */
        .data-table th:nth-child(9) { width: 13%; } /* Actions */

        .customer-cell {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .customer-name {
            font-weight: 600;
            color: #2c3e50;
        }

        .booking-id {
            font-size: 0.8rem;
            color: #95a5a6;
        }

        .data-table tbody tr {
            transition: all 0.3s ease;
        }

        .data-table tbody tr:hover {
            background: linear-gradient(135deg, #fff5f8 0%, #ffe8f0 100%);
            transform: scale(1.01);
            box-shadow: 0 4px 12px rgba(255, 107, 157, 0.15);
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: capitalize;
            display: inline-block;
            letter-spacing: 0.2px;
            white-space: nowrap;
        }

        .status-badge.status-pending {
            background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%);
            color: #856404;
            box-shadow: 0 2px 8px rgba(253, 203, 110, 0.3);
        }

        .status-badge.status-confirmed {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(9, 132, 227, 0.3);
        }

        .status-badge.status-completed {
            background: linear-gradient(135deg, #55efc4 0%, #00b894 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(0, 184, 148, 0.3);
        }

        .status-badge.status-cancelled {
            background: linear-gradient(135deg, #ff7675 0%, #d63031 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(214, 48, 49, 0.3);
        }

        .status-badge.status-paid {
            background: linear-gradient(135deg, #55efc4 0%, #00b894 100%);
            color: white;
            box-shadow: 0 2px 8px rgba(0, 184, 148, 0.3);
        }

        .action-buttons {
            display: flex;
            gap: 0.35rem;
            justify-content: center;
        }

        .btn-action {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            color: white;
            font-size: 0.85rem;
        }

        .btn-view {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            box-shadow: 0 3px 10px rgba(52, 152, 219, 0.3);
        }

        .btn-edit {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            box-shadow: 0 3px 10px rgba(243, 156, 18, 0.3);
        }

        .btn-delete {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            box-shadow: 0 3px 10px rgba(231, 76, 60, 0.3);
        }

        .btn-action:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 6px 20px rgba(0,0,0,0.25);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #95a5a6;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            opacity: 0.3;
            color: #ff6b9d;
        }

        .empty-state p {
            font-size: 1.1rem;
            color: #7f8c8d;
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
                <div class="page-header">
                    <h1><i class="fas fa-calendar-check"></i> Manage Bookings</h1>
                    <p>View and manage all event bookings</p>
                </div>

                <!-- Bookings Table -->
                <div class="content-card">
                    <div class="card-header">
                        <h2><i class="fas fa-list"></i> All Bookings</h2>
                    </div>
                    <div class="table-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Customer</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Package</th>
                                    <th>Event Date</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php while ($booking = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td>
                                                <div class="customer-cell">
                                                    <span class="customer-name"><?php echo htmlspecialchars($booking['customer_name']); ?></span>
                                                    <span class="booking-id">ID: #<?php echo $booking['id']; ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo htmlspecialchars($booking['customer_email']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['customer_phone']); ?></td>
                                            <td><?php echo htmlspecialchars($booking['package_name']); ?></td>
                                            <td><?php echo date('M d, Y', strtotime($booking['event_date'])); ?></td>
                                            <td>$<?php echo number_format($booking['total_amount'], 2); ?></td>
                                            <td>
                                                <span class="status-badge status-<?php echo $booking['payment_status']; ?>">
                                                    <?php echo ucfirst($booking['payment_status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="status-badge status-<?php echo $booking['booking_status']; ?>">
                                                    <?php echo ucfirst($booking['booking_status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="action-buttons">
                                                    <button class="btn-action btn-view" title="View Details" onclick="viewBooking(<?php echo $booking['id']; ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn-action btn-edit" title="Edit Booking" onclick="editBooking(<?php echo $booking['id']; ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn-action btn-delete" title="Delete Booking" onclick="deleteBooking(<?php echo $booking['id']; ?>)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9">
                                            <div class="empty-state">
                                                <i class="fas fa-calendar-times"></i>
                                                <p>No bookings found</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/admin-dashboard.js"></script>
    <script>
        function viewBooking(id) {
            alert('View booking #' + id + ' - Feature coming soon');
        }
        
        function editBooking(id) {
            alert('Edit booking #' + id + ' - Feature coming soon');
        }
        
        function deleteBooking(id) {
            if (confirm('Are you sure you want to delete this booking?')) {
                alert('Delete booking #' + id + ' - Feature coming soon');
            }
        }
    </script>
</body>
</html>
<?php mysqli_close($conn); ?>
