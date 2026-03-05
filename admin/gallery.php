<?php
session_start();
require_once 'auth_check.php';
require_once '../config/database.php';

// Handle gallery actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action == 'add') {
            $title = mysqli_real_escape_string($conn, $_POST['title']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            $image = mysqli_real_escape_string($conn, $_POST['image']);
            $category = mysqli_real_escape_string($conn, $_POST['category']);
            $status = mysqli_real_escape_string($conn, $_POST['status']);
            
            $sql = "INSERT INTO gallery (title, description, image, category, status) 
                    VALUES ('$title', '$description', '$image', '$category', '$status')";
            mysqli_query($conn, $sql);
            header('Location: gallery.php?success=added');
            exit;
        }
        
        if ($action == 'edit') {
            $id = intval($_POST['id']);
            $title = mysqli_real_escape_string($conn, $_POST['title']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            $image = mysqli_real_escape_string($conn, $_POST['image']);
            $category = mysqli_real_escape_string($conn, $_POST['category']);
            $status = mysqli_real_escape_string($conn, $_POST['status']);
            
            $sql = "UPDATE gallery SET title='$title', description='$description', 
                    image='$image', category='$category', status='$status' WHERE id=$id";
            mysqli_query($conn, $sql);
            header('Location: gallery.php?success=updated');
            exit;
        }
        
        if ($action == 'delete') {
            $id = intval($_POST['id']);
            $sql = "DELETE FROM gallery WHERE id=$id";
            mysqli_query($conn, $sql);
            header('Location: gallery.php?success=deleted');
            exit;
        }
    }
}

// Fetch all gallery images
$sql = "SELECT * FROM gallery ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Gallery - DreamEvents Admin</title>
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .dashboard-content {
            padding: 2rem;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
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

        .btn-primary {
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            color: white;
            padding: 0.875rem 1.75rem;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(255, 107, 157, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 157, 0.4);
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .gallery-card {
            background: white;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: all 0.3s;
        }

        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }

        .gallery-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .gallery-content {
            padding: 1.25rem;
        }

        .gallery-content h3 {
            font-size: 1.15rem;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .gallery-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            font-size: 0.85rem;
            color: #7f8c8d;
        }

        .gallery-category {
            background: #f8f9fa;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .gallery-status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .gallery-status.active {
            background: #d4edda;
            color: #155724;
        }

        .gallery-status.inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .gallery-actions {
            display: flex;
            gap: 0.5rem;
            padding-top: 1rem;
            border-top: 1px solid #f8f9fa;
        }

        .btn-icon {
            flex: 1;
            padding: 0.65rem;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .btn-edit {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            color: white;
        }

        .btn-icon:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 14px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 1.5rem 2rem;
            border-bottom: 2px solid #f8f9fa;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            color: #2c3e50;
        }

        .close {
            font-size: 2rem;
            font-weight: 300;
            color: #95a5a6;
            cursor: pointer;
            transition: color 0.3s;
        }

        .close:hover {
            color: #e74c3c;
        }

        .modal-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #ecf0f1;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #ff6b9d;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .modal-footer {
            padding: 1.5rem 2rem;
            border-top: 2px solid #f8f9fa;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .btn-secondary {
            padding: 0.75rem 1.5rem;
            background: #ecf0f1;
            color: #2c3e50;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: #bdc3c7;
        }

        .btn-submit {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 157, 0.3);
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
                        <h1><i class="fas fa-images"></i> Manage Gallery</h1>
                        <p style="color: #7f8c8d;">Upload and manage event photos</p>
                    </div>
                    <button class="btn-primary" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add New Image
                    </button>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        Image <?php echo htmlspecialchars($_GET['success']); ?> successfully!
                    </div>
                <?php endif; ?>

                <div class="gallery-grid">
                    <?php while($item = mysqli_fetch_assoc($result)): ?>
                    <div class="gallery-card">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="gallery-image">
                        <div class="gallery-content">
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <div class="gallery-meta">
                                <span class="gallery-category">
                                    <i class="fas fa-tag"></i> <?php echo ucfirst($item['category']); ?>
                                </span>
                                <span class="gallery-status <?php echo $item['status']; ?>">
                                    <?php echo ucfirst($item['status']); ?>
                                </span>
                            </div>
                            <div class="gallery-actions">
                                <button class="btn-icon btn-edit" onclick='editImage(<?php echo json_encode($item); ?>)'>
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn-icon btn-delete" onclick="deleteImage(<?php echo $item['id']; ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="galleryModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Image</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="imageId">
                    
                    <div class="form-group">
                        <label>Title *</label>
                        <input type="text" name="title" id="imageTitle" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" id="imageDescription"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Image URL *</label>
                        <input type="text" name="image" id="imageUrl" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Category *</label>
                        <select name="category" id="imageCategory" required>
                            <option value="wedding">Wedding</option>
                            <option value="proposal">Proposal</option>
                            <option value="anniversary">Anniversary</option>
                            <option value="birthday">Birthday</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" id="imageStatus" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Save Image</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Image';
            document.getElementById('formAction').value = 'add';
            document.querySelector('#galleryModal form').reset();
            document.getElementById('galleryModal').style.display = 'block';
        }

        function editImage(img) {
            document.getElementById('modalTitle').textContent = 'Edit Image';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('imageId').value = img.id;
            document.getElementById('imageTitle').value = img.title;
            document.getElementById('imageDescription').value = img.description;
            document.getElementById('imageUrl').value = img.image;
            document.getElementById('imageCategory').value = img.category;
            document.getElementById('imageStatus').value = img.status;
            document.getElementById('galleryModal').style.display = 'block';
        }

        function deleteImage(id) {
            if (confirm('Are you sure you want to delete this image?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function closeModal() {
            document.getElementById('galleryModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('galleryModal');
            if (event.target == modal) modal.style.display = 'none';
        }
    </script>
</body>
</html>
