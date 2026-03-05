<?php
session_start();
require_once 'auth_check.php';
require_once '../config/database.php';

$admin_id = $_SESSION['admin_id'];
$admin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM admins WHERE id=$admin_id"));

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action == 'update_profile') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        
        $sql = "UPDATE admins SET name='$name', email='$email' WHERE id=$admin_id";
        if(mysqli_query($conn, $sql)) {
            $success_msg = "Profile updated successfully!";
            $admin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM admins WHERE id=$admin_id"));
        }
        
    } elseif ($action == 'change_password') {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if(password_verify($current_password, $admin['password'])) {
            if($new_password == $confirm_password) {
                $hashed = password_hash($new_password, PASSWORD_DEFAULT);
                mysqli_query($conn, "UPDATE admins SET password='$hashed' WHERE id=$admin_id");
                $success_msg = "Password changed successfully!";
            } else {
                $error_msg = "New passwords do not match!";
            }
        } else {
            $error_msg = "Current password is incorrect!";
        }
    }
}

// Get system stats
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM bookings"))['count'];
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT customer_email) as count FROM bookings"))['count'];
$total_packages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM packages"))['count'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM bookings WHERE payment_status='paid'"))['total'] ?? 0;

// Get database info
$db_size_result = mysqli_query($conn, "SELECT 
    SUM(data_length + index_length) / 1024 / 1024 AS 'size_mb' 
    FROM information_schema.TABLES 
    WHERE table_schema = 'events_management'");
$db_size = mysqli_fetch_assoc($db_size_result)['size_mb'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - DreamEvents Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-content {
            padding: 2rem;
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

        .settings-grid {
            display: grid;
            gap: 2rem;
        }

        .settings-section {
            background: white;
            border-radius: 14px;
            padding: 2rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #ecf0f1;
        }

        .section-header i {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .section-header h2 {
            font-size: 1.35rem;
            color: #2c3e50;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 600;
        }

        .form-group input {
            width: 100%;
            padding: 0.85rem;
            border: 2px solid #ecf0f1;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #ff6b9d;
            box-shadow: 0 0 0 3px rgba(255, 107, 157, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            color: white;
            padding: 0.95rem 2rem;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 157, 0.4);
        }

        .profile-info {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 600;
        }

        .system-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.5rem;
            border-radius: 12px;
            border-left: 4px solid;
        }

        .stat-card:nth-child(1) { border-left-color: #3498db; }
        .stat-card:nth-child(2) { border-left-color: #27ae60; }
        .stat-card:nth-child(3) { border-left-color: #f39c12; }
        .stat-card:nth-child(4) { border-left-color: #ff6b9d; }

        .stat-card h3 {
            font-size: 1.75rem;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }

        .stat-card p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .info-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .info-item i {
            color: #ff6b9d;
            font-size: 1.25rem;
        }

        .info-item strong {
            color: #2c3e50;
            display: block;
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
        }

        .info-item span {
            color: #7f8c8d;
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include 'includes/sidebar.php'; ?>
        
        <div class="main-content">
            <?php include 'includes/topbar.php'; ?>
            
            <div class="dashboard-content">
                <div class="page-header">
                    <h1><i class="fas fa-cog"></i> Settings</h1>
                    <p style="color: #7f8c8d;">Manage your profile and system settings</p>
                </div>

                <div class="settings-grid">
                    <!-- Profile Settings -->
                    <div class="settings-section">
                        <div class="section-header">
                            <i class="fas fa-user"></i>
                            <h2>Profile Settings</h2>
                        </div>

                        <?php if(isset($success_msg)): ?>
                        <div class="alert success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $success_msg; ?>
                        </div>
                        <?php endif; ?>

                        <?php if(isset($error_msg)): ?>
                        <div class="alert error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $error_msg; ?>
                        </div>
                        <?php endif; ?>

                        <div class="profile-info">
                            <div class="profile-avatar">
                                <?php echo isset($admin['name']) ? strtoupper(substr($admin['name'], 0, 1)) : 'A'; ?>
                            </div>
                            <div>
                                <h3 style="font-size: 1.5rem; color: #2c3e50; margin-bottom: 0.5rem;">
                                    <?php echo isset($admin['name']) ? htmlspecialchars($admin['name']) : 'Admin'; ?>
                                </h3>
                                <p style="color: #7f8c8d; margin-bottom: 0.25rem;">
                                    <i class="fas fa-envelope"></i> <?php echo isset($admin['email']) ? htmlspecialchars($admin['email']) : 'N/A'; ?>
                                </p>
                                <p style="color: #7f8c8d;">
                                    <i class="fas fa-calendar"></i> Member since <?php echo isset($admin['created_at']) ? date('M d, Y', strtotime($admin['created_at'])) : 'N/A'; ?>
                                </p>
                            </div>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="action" value="update_profile">
                            
                            <div class="form-group">
                                <label>Full Name</label>
                                <input type="text" name="name" value="<?php echo isset($admin['name']) ? htmlspecialchars($admin['name']) : ''; ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="email" name="email" value="<?php echo isset($admin['email']) ? htmlspecialchars($admin['email']) : ''; ?>" required>
                            </div>

                            <button type="submit" class="btn-submit">
                                <i class="fas fa-save"></i> Update Profile
                            </button>
                        </form>
                    </div>

                    <!-- Change Password -->
                    <div class="settings-section">
                        <div class="section-header">
                            <i class="fas fa-lock"></i>
                            <h2>Change Password</h2>
                        </div>

                        <form method="POST">
                            <input type="hidden" name="action" value="change_password">
                            
                            <div class="form-group">
                                <label>Current Password</label>
                                <input type="password" name="current_password" required>
                            </div>

                            <div class="form-group">
                                <label>New Password</label>
                                <input type="password" name="new_password" required minlength="6">
                            </div>

                            <div class="form-group">
                                <label>Confirm New Password</label>
                                <input type="password" name="confirm_password" required minlength="6">
                            </div>

                            <button type="submit" class="btn-submit">
                                <i class="fas fa-key"></i> Change Password
                            </button>
                        </form>
                    </div>

                    <!-- System Statistics -->
                    <div class="settings-section">
                        <div class="section-header">
                            <i class="fas fa-chart-line"></i>
                            <h2>System Statistics</h2>
                        </div>

                        <div class="system-stats">
                            <div class="stat-card">
                                <h3><?php echo $total_bookings; ?></h3>
                                <p>Total Bookings</p>
                            </div>

                            <div class="stat-card">
                                <h3><?php echo $total_customers; ?></h3>
                                <p>Total Customers</p>
                            </div>

                            <div class="stat-card">
                                <h3><?php echo $total_packages; ?></h3>
                                <p>Active Packages</p>
                            </div>

                            <div class="stat-card">
                                <h3>$<?php echo number_format($total_revenue, 2); ?></h3>
                                <p>Total Revenue</p>
                            </div>
                        </div>
                    </div>

                    <!-- System Information -->
                    <div class="settings-section">
                        <div class="section-header">
                            <i class="fas fa-server"></i>
                            <h2>System Information</h2>
                        </div>

                        <div class="info-row">
                            <div class="info-item">
                                <i class="fas fa-code"></i>
                                <div>
                                    <strong>PHP Version</strong>
                                    <span><?php echo phpversion(); ?></span>
                                </div>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-database"></i>
                                <div>
                                    <strong>MySQL Version</strong>
                                    <span><?php echo mysqli_get_server_info($conn); ?></span>
                                </div>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-hdd"></i>
                                <div>
                                    <strong>Database Size</strong>
                                    <span><?php echo number_format($db_size, 2); ?> MB</span>
                                </div>
                            </div>

                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <strong>Server Time</strong>
                                    <span><?php echo date('M d, Y - h:i A'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
