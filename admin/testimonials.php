<?php
session_start();
require_once 'auth_check.php';
require_once '../config/database.php';

// Handle CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    if ($action == 'add') {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $rating = intval($_POST['rating']);
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        $sql = "INSERT INTO testimonials (name, email, rating, message, status) 
                VALUES ('$name', '$email', $rating, '$message', '$status')";
        mysqli_query($conn, $sql);
        
    } elseif ($action == 'edit') {
        $id = intval($_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $rating = intval($_POST['rating']);
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        $status = mysqli_real_escape_string($conn, $_POST['status']);
        
        $sql = "UPDATE testimonials SET 
                name='$name', email='$email', rating=$rating, 
                message='$message', status='$status' 
                WHERE id=$id";
        mysqli_query($conn, $sql);
        
    } elseif ($action == 'delete') {
        $id = intval($_POST['id']);
        mysqli_query($conn, "DELETE FROM testimonials WHERE id=$id");
    }
    
    header('Location: testimonials.php');
    exit;
}

// Get statistics
$total_testimonials = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM testimonials"))['count'];
$approved = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM testimonials WHERE status='approved'"))['count'];
$pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM testimonials WHERE status='pending'"))['count'];
$avg_rating = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(rating) as avg FROM testimonials WHERE status='approved'"))['avg'];

// Fetch all testimonials
$sql = "SELECT * FROM testimonials ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Testimonials - DreamEvents Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-content {
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
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

        .btn-add {
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            color: white;
            padding: 0.85rem 1.75rem;
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

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 157, 0.4);
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
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
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
        }

        .stat-box:nth-child(3) .stat-icon {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
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

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .testimonial-card {
            background: white;
            border-radius: 14px;
            padding: 1.75rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s;
            border-top: 4px solid #ff6b9d;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }

        .testimonial-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .customer-info h3 {
            font-size: 1.15rem;
            color: #2c3e50;
            margin-bottom: 0.35rem;
        }

        .customer-email {
            color: #7f8c8d;
            font-size: 0.85rem;
        }

        .rating {
            display: flex;
            gap: 0.25rem;
            margin-bottom: 1rem;
        }

        .rating i {
            color: #f39c12;
            font-size: 1.1rem;
        }

        .rating i.empty {
            color: #ddd;
        }

        .testimonial-message {
            color: #34495e;
            line-height: 1.7;
            margin-bottom: 1.25rem;
            font-style: italic;
        }

        .testimonial-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #ecf0f1;
        }

        .status-badge {
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-badge.approved {
            background: #d4edda;
            color: #155724;
        }

        .status-badge.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-badge.rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-action {
            padding: 0.5rem 0.85rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
            transition: all 0.3s;
            color: white;
        }

        .btn-edit {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }

        .btn-delete {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }

        .btn-action:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            animation: fadeIn 0.3s;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideDown 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideDown {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .modal-header h2 {
            font-size: 1.5rem;
            color: #2c3e50;
        }

        .close-btn {
            background: #ecf0f1;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.25rem;
            color: #7f8c8d;
            transition: all 0.3s;
        }

        .close-btn:hover {
            background: #bdc3c7;
            transform: rotate(90deg);
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

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.85rem;
            border: 2px solid #ecf0f1;
            border-radius: 10px;
            font-size: 0.95rem;
            transition: all 0.3s;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #ff6b9d;
            box-shadow: 0 0 0 3px rgba(255, 107, 157, 0.1);
        }

        .btn-submit {
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            color: white;
            padding: 1rem 2rem;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 157, 0.4);
        }

        .date-text {
            font-size: 0.8rem;
            color: #95a5a6;
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
                    <div>
                        <h1><i class="fas fa-comments"></i> Manage Testimonials</h1>
                        <p style="color: #7f8c8d; margin-top: 0.5rem;">Customer reviews and feedback</p>
                    </div>
                    <button class="btn-add" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add Testimonial
                    </button>
                </div>

                <div class="stats-row">
                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $total_testimonials; ?></h3>
                            <p>Total Reviews</p>
                        </div>
                    </div>

                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $approved; ?></h3>
                            <p>Approved</p>
                        </div>
                    </div>

                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo $pending; ?></h3>
                            <p>Pending</p>
                        </div>
                    </div>

                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-info">
                            <h3><?php echo number_format($avg_rating, 1); ?></h3>
                            <p>Average Rating</p>
                        </div>
                    </div>
                </div>

                <div class="testimonials-grid">
                    <?php if(mysqli_num_rows($result) > 0): ?>
                        <?php while($testimonial = mysqli_fetch_assoc($result)): ?>
                        <div class="testimonial-card">
                            <div class="testimonial-header">
                                <div class="customer-info">
                                    <h3><?php echo isset($testimonial['name']) ? htmlspecialchars($testimonial['name']) : 'N/A'; ?></h3>
                                    <div class="customer-email"><?php echo isset($testimonial['email']) ? htmlspecialchars($testimonial['email']) : 'N/A'; ?></div>
                                </div>
                            </div>

                            <div class="rating">
                                <?php 
                                $rating = isset($testimonial['rating']) ? $testimonial['rating'] : 0;
                                for($i = 1; $i <= 5; $i++) {
                                    if($i <= $rating) {
                                        echo '<i class="fas fa-star"></i>';
                                    } else {
                                        echo '<i class="fas fa-star empty"></i>';
                                    }
                                }
                                ?>
                            </div>

                            <div class="testimonial-message">
                                "<?php echo isset($testimonial['message']) ? nl2br(htmlspecialchars($testimonial['message'])) : 'No message'; ?>"
                            </div>

                        <div class="testimonial-footer">
                            <div>
                                <span class="status-badge <?php echo $testimonial['status']; ?>">
                                    <?php echo ucfirst($testimonial['status']); ?>
                                </span>
                                <div class="date-text" style="margin-top: 0.5rem;">
                                    <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($testimonial['created_at'])); ?>
                                </div>
                            </div>
                            <div class="action-buttons">
                                <button class="btn-action btn-edit" onclick='editTestimonial(<?php echo json_encode($testimonial); ?>)'>
                                    <i class="fas fa-edit"></i>
                                </button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $testimonial['id']; ?>">
                                    <button type="submit" class="btn-action btn-delete" onclick="return confirm('Delete this testimonial?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    <?php else: ?>
                        <div style="grid-column: 1/-1; text-align: center; padding: 3rem; color: #95a5a6;">
                            <i class="fas fa-comments" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                            <p style="font-size: 1.1rem;">No testimonials found</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="testimonialModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add Testimonial</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST" id="testimonialForm">
                <input type="hidden" name="action" id="formAction" value="add">
                <input type="hidden" name="id" id="testimonialId">
                
                <div class="form-group">
                    <label>Customer Name *</label>
                    <input type="text" name="name" id="name" required>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" id="email" required>
                </div>

                <div class="form-group">
                    <label>Rating *</label>
                    <select name="rating" id="rating" required>
                        <option value="5">5 Stars - Excellent</option>
                        <option value="4">4 Stars - Very Good</option>
                        <option value="3">3 Stars - Good</option>
                        <option value="2">2 Stars - Fair</option>
                        <option value="1">1 Star - Poor</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Review Message *</label>
                    <textarea name="message" id="message" required></textarea>
                </div>

                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" id="status" required>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Save Testimonial
                </button>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add Testimonial';
            document.getElementById('formAction').value = 'add';
            document.getElementById('testimonialForm').reset();
            document.getElementById('testimonialModal').classList.add('active');
        }

        function editTestimonial(testimonial) {
            document.getElementById('modalTitle').textContent = 'Edit Testimonial';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('testimonialId').value = testimonial.id;
            document.getElementById('name').value = testimonial.name;
            document.getElementById('email').value = testimonial.email;
            document.getElementById('rating').value = testimonial.rating;
            document.getElementById('message').value = testimonial.message;
            document.getElementById('status').value = testimonial.status;
            document.getElementById('testimonialModal').classList.add('active');
        }

        function closeModal() {
            document.getElementById('testimonialModal').classList.remove('active');
        }

        window.onclick = function(event) {
            const modal = document.getElementById('testimonialModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
