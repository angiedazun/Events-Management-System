<?php
session_start();
require_once 'auth_check.php';
require_once '../config/database.php';

// Handle package actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        
        if ($action == 'add') {
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            $price = floatval($_POST['price']);
            $features = mysqli_real_escape_string($conn, $_POST['features']);
            $image = mysqli_real_escape_string($conn, $_POST['image']);
            $status = mysqli_real_escape_string($conn, $_POST['status']);
            
            $sql = "INSERT INTO packages (name, description, price, features, image, status) 
                    VALUES ('$name', '$description', $price, '$features', '$image', '$status')";
            mysqli_query($conn, $sql);
            header('Location: packages.php?success=added');
            exit;
        }
        
        if ($action == 'edit') {
            $id = intval($_POST['id']);
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $description = mysqli_real_escape_string($conn, $_POST['description']);
            $price = floatval($_POST['price']);
            $features = mysqli_real_escape_string($conn, $_POST['features']);
            $image = mysqli_real_escape_string($conn, $_POST['image']);
            $status = mysqli_real_escape_string($conn, $_POST['status']);
            
            $sql = "UPDATE packages SET name='$name', description='$description', 
                    price=$price, features='$features', image='$image', status='$status' 
                    WHERE id=$id";
            mysqli_query($conn, $sql);
            header('Location: packages.php?success=updated');
            exit;
        }
        
        if ($action == 'delete') {
            $id = intval($_POST['id']);
            $sql = "DELETE FROM packages WHERE id=$id";
            mysqli_query($conn, $sql);
            header('Location: packages.php?success=deleted');
            exit;
        }
    }
}

// Fetch all packages
$sql = "SELECT * FROM packages ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages - DreamEvents Admin</title>
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

        .content-card {
            background: white;
            border-radius: 14px;
            padding: 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
        }

        .data-table thead {
            background: #f8f9fa;
        }

        .data-table th {
            text-align: left;
            padding: 1rem 1.25rem;
            font-weight: 600;
            color: #7f8c8d;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #ecf0f1;
        }

        .data-table td {
            padding: 1.25rem;
            border-bottom: 1px solid #f8f9fa;
            color: #34495e;
        }

        .data-table tr:hover {
            background: #f8f9fa;
        }

        .data-table img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }

        .status-active {
            padding: 0.35rem 0.85rem;
            background: #d4edda;
            color: #155724;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-inactive {
            padding: 0.35rem 0.85rem;
            background: #f8d7da;
            color: #721c24;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-view {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
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
            min-height: 100px;
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
                        <h1><i class="fas fa-box"></i> Manage Packages</h1>
                        <p style="color: #7f8c8d;">Create and manage event packages</p>
                    </div>
                    <button class="btn-primary" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add New Package
                    </button>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        Package <?php echo htmlspecialchars($_GET['success']); ?> successfully!
                    </div>
                <?php endif; ?>

                <div class="content-card">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($package = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><strong>#<?php echo $package['id']; ?></strong></td>
                                <td>
                                    <img src="<?php echo htmlspecialchars($package['image']); ?>" alt="Package">
                                </td>
                                <td><strong><?php echo htmlspecialchars($package['name']); ?></strong></td>
                                <td><strong style="color: #27ae60;">$<?php echo number_format($package['price'], 2); ?></strong></td>
                                <td>
                                    <span class="status-<?php echo $package['status']; ?>">
                                        <?php echo ucfirst($package['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($package['created_at'])); ?></td>
                                <td class="actions">
                                    <button class="btn-icon btn-view" onclick='viewPackage(<?php echo json_encode($package); ?>)' title="View">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn-icon btn-edit" onclick='editPackage(<?php echo json_encode($package); ?>)' title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-icon btn-delete" onclick="deletePackage(<?php echo $package['id']; ?>)" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="packageModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Package</h2>
                <span class="close" onclick="closeModal()">&times;</span>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="action" id="formAction" value="add">
                    <input type="hidden" name="id" id="packageId">
                    
                    <div class="form-group">
                        <label>Package Name *</label>
                        <input type="text" name="name" id="packageName" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Description *</label>
                        <textarea name="description" id="packageDescription" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Price ($) *</label>
                        <input type="number" name="price" id="packagePrice" step="0.01" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Features (separate with |) *</label>
                        <textarea name="features" id="packageFeatures" placeholder="Feature 1|Feature 2|Feature 3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Image URL *</label>
                        <input type="text" name="image" id="packageImage" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Status *</label>
                        <select name="status" id="packageStatus" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Save Package</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Package Details</h2>
                <span class="close" onclick="closeViewModal()">&times;</span>
            </div>
            <div class="modal-body" id="viewContent"></div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeViewModal()">Close</button>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('modalTitle').textContent = 'Add New Package';
            document.getElementById('formAction').value = 'add';
            document.getElementById('packageForm') && document.getElementById('packageForm').reset();
            document.querySelector('#packageModal form').reset();
            document.getElementById('packageModal').style.display = 'block';
        }

        function editPackage(pkg) {
            document.getElementById('modalTitle').textContent = 'Edit Package';
            document.getElementById('formAction').value = 'edit';
            document.getElementById('packageId').value = pkg.id;
            document.getElementById('packageName').value = pkg.name;
            document.getElementById('packageDescription').value = pkg.description;
            document.getElementById('packagePrice').value = pkg.price;
            document.getElementById('packageImage').value = pkg.image;
            document.getElementById('packageFeatures').value = pkg.features;
            document.getElementById('packageStatus').value = pkg.status;
            document.getElementById('packageModal').style.display = 'block';
        }

        function viewPackage(pkg) {
            const features = pkg.features.split('|').map(f => `<li style="margin: 0.5rem 0;">${f}</li>`).join('');
            document.getElementById('viewContent').innerHTML = `
                <div style="line-height: 1.8;">
                    <p><strong>Name:</strong> ${pkg.name}</p>
                    <p><strong>Price:</strong> <span style="color: #27ae60; font-weight: 700;">$${parseFloat(pkg.price).toFixed(2)}</span></p>
                    <p><strong>Description:</strong><br>${pkg.description}</p>
                    <p><strong>Features:</strong></p>
                    <ul style="padding-left: 1.5rem;">${features}</ul>
                    <p><strong>Status:</strong> <span class="status-${pkg.status}">${pkg.status}</span></p>
                    <p><strong>Image:</strong> ${pkg.image}</p>
                </div>
            `;
            document.getElementById('viewModal').style.display = 'block';
        }

        function deletePackage(id) {
            if (confirm('Are you sure you want to delete this package?')) {
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
            document.getElementById('packageModal').style.display = 'none';
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('packageModal');
            const viewModal = document.getElementById('viewModal');
            if (event.target == modal) modal.style.display = 'none';
            if (event.target == viewModal) viewModal.style.display = 'none';
        }
    </script>
</body>
</html>
