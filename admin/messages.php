<?php
session_start();
require_once 'auth_check.php';
require_once '../config/database.php';

// Handle message actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id = intval($_POST['id']);
    
    if ($action == 'read') {
        mysqli_query($conn, "UPDATE contact_messages SET status='read' WHERE id=$id");
    } elseif ($action == 'replied') {
        mysqli_query($conn, "UPDATE contact_messages SET status='replied' WHERE id=$id");
    } elseif ($action == 'delete') {
        mysqli_query($conn, "DELETE FROM contact_messages WHERE id=$id");
    }
    
    header('Location: messages.php');
    exit;
}

// Get statistics
$total_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM contact_messages"))['count'];
$new_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM contact_messages WHERE status='new'"))['count'];
$read_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM contact_messages WHERE status='read'"))['count'];
$replied_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM contact_messages WHERE status='replied'"))['count'];

// Fetch all messages
$sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Messages - DreamEvents Admin</title>
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

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-box {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .stat-box:nth-child(1) .stat-icon {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        }

        .stat-box:nth-child(2) .stat-icon {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }

        .stat-box:nth-child(3) .stat-icon {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        }

        .stat-box:nth-child(4) .stat-icon {
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
        }

        .stat-info h3 {
            font-size: 1.75rem;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }

        .stat-info p {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .messages-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .message-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s;
            border-left: 4px solid #ecf0f1;
        }

        .message-card.new {
            border-left-color: #3498db;
            background: #ebf8ff;
        }

        .message-card.read {
            border-left-color: #f39c12;
        }

        .message-card.replied {
            border-left-color: #27ae60;
        }

        .message-card:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .message-info h3 {
            font-size: 1.15rem;
            color: #2c3e50;
            margin-bottom: 0.25rem;
        }

        .message-meta {
            display: flex;
            gap: 1.5rem;
            font-size: 0.85rem;
            color: #7f8c8d;
            margin-bottom: 0.5rem;
        }

        .message-meta i {
            margin-right: 0.35rem;
        }

        .message-status {
            padding: 0.35rem 0.85rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .message-status.new {
            background: #d1ecf1;
            color: #0c5460;
        }

        .message-status.read {
            background: #fff3cd;
            color: #856404;
        }

        .message-status.replied {
            background: #d4edda;
            color: #155724;
        }

        .message-subject {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }

        .message-body {
            color: #34495e;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .message-actions {
            display: flex;
            gap: 0.75rem;
            padding-top: 1rem;
            border-top: 1px solid #ecf0f1;
        }

        .btn-action {
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-read {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
        }

        .btn-replied {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
                    <h1><i class="fas fa-envelope"></i> Manage Messages</h1>
                    <p style="color: #7f8c8d;">View and respond to customer inquiries</p>
                </div>

                <div class="stats-row">
                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="fas fa-inbox"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $total_messages; ?></h3>
                            <p>Total Messages</p>
                        </div>
                    </div>

                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $new_messages; ?></h3>
                            <p>New Messages</p>
                        </div>
                    </div>

                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $replied_messages; ?></h3>
                            <p>Replied</p>
                        </div>
                    </div>

                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="fas fa-envelope-open"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $read_messages; ?></h3>
                            <p>Read</p>
                        </div>
                    </div>
                </div>

                <div class="messages-list">
                    <?php while($message = mysqli_fetch_assoc($result)): ?>
                    <div class="message-card <?php echo $message['status']; ?>">
                        <div class="message-header">
                            <div class="message-info">
                                <h3><?php echo htmlspecialchars($message['name']); ?></h3>
                                <div class="message-meta">
                                    <span><i class="fas fa-envelope"></i><?php echo htmlspecialchars($message['email']); ?></span>
                                    <span><i class="fas fa-phone"></i><?php echo htmlspecialchars($message['phone']); ?></span>
                                    <span><i class="fas fa-clock"></i><?php echo date('M d, Y - h:i A', strtotime($message['created_at'])); ?></span>
                                </div>
                            </div>
                            <span class="message-status <?php echo $message['status']; ?>">
                                <?php echo ucfirst($message['status']); ?>
                            </span>
                        </div>
                        
                        <div class="message-subject">
                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($message['subject']); ?>
                        </div>
                        
                        <div class="message-body">
                            <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                        </div>
                        
                        <div class="message-actions">
                            <?php if($message['status'] == 'new'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="read">
                                <input type="hidden" name="id" value="<?php echo $message['id']; ?>">
                                <button type="submit" class="btn-action btn-read">
                                    <i class="fas fa-eye"></i> Mark as Read
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <?php if($message['status'] != 'replied'): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="replied">
                                <input type="hidden" name="id" value="<?php echo $message['id']; ?>">
                                <button type="submit" class="btn-action btn-replied">
                                    <i class="fas fa-reply"></i> Mark as Replied
                                </button>
                            </form>
                            <?php endif; ?>
                            
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $message['id']; ?>">
                                <button type="submit" class="btn-action btn-delete" onclick="return confirm('Delete this message?')">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
