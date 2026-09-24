<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$admin_name = $_SESSION['user_name'];

require_once __DIR__ . '/../backend/config.php';
global $conn;

$success_msg = '';
$error_msg = '';

$upload_dir = __DIR__ . '/../uploads/images/products/';
$upload_db_prefix = 'uploads/images/products/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

function handle_image_upload($file, $upload_dir, $upload_db_prefix, &$error_msg) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error_msg = "Image upload failed. Please try again.";
        return null;
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowed_types)) {
        $error_msg = "Only JPG, PNG, or WEBP images are allowed.";
        return null;
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        $error_msg = "Image must be smaller than 5MB.";
        return null;
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safe_filename = uniqid('product_', true) . '.' . $extension;

    if (move_uploaded_file($file['tmp_name'], $upload_dir . $safe_filename)) {
        return $upload_db_prefix . $safe_filename;
    } else {
        $error_msg = "Could not save the uploaded image.";
        return null;
    }
}

// ============================================================
// ADD NEW PRODUCT
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $product_name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = (float) $_POST['price'];
    $stock_quantity = (int) $_POST['stock_quantity'];

    if (empty($product_name)) {
        $error_msg = "Product name is required.";
    } else {
        $image_url = handle_image_upload($_FILES['image'] ?? null, $upload_dir, $upload_db_prefix, $error_msg);

        if (empty($error_msg)) {
            $sql = "INSERT INTO products (product_name, description, image_url, price, stock_quantity, is_active)
                    VALUES (?, ?, ?, ?, ?, 1)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssdi", $product_name, $description, $image_url, $price, $stock_quantity);
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Product added successfully!";
            } else {
                $error_msg = "Error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// ============================================================
// UPDATE PRODUCT
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product'])) {
    $product_id = (int) $_POST['product_id'];
    $product_name = trim($_POST['product_name']);
    $description = trim($_POST['description']);
    $price = (float) $_POST['price'];
    $stock_quantity = (int) $_POST['stock_quantity'];

    $new_image_url = handle_image_upload($_FILES['image'] ?? null, $upload_dir, $upload_db_prefix, $error_msg);

    if (empty($error_msg)) {
        if ($new_image_url) {
            $sql = "UPDATE products SET product_name=?, description=?, image_url=?, price=?, stock_quantity=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssdii", $product_name, $description, $new_image_url, $price, $stock_quantity, $product_id);
        } else {
            $sql = "UPDATE products SET product_name=?, description=?, price=?, stock_quantity=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssdii", $product_name, $description, $price, $stock_quantity, $product_id);
        }

        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Product updated successfully!";
        } else {
            $error_msg = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// ============================================================
// RESTOCK PRODUCT (add more units to existing stock)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restock_product'])) {
    $product_id = (int) $_POST['product_id'];
    $units_to_add = (int) $_POST['units_to_add'];

    if ($units_to_add > 0) {
        $sql = "UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $units_to_add, $product_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Restocked with $units_to_add more unit(s)!";
        } else {
            $error_msg = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = "Enter a valid number of units to add.";
    }
}

// ============================================================
// TOGGLE ACTIVE / INACTIVE
// ============================================================
if (isset($_GET['toggle_id'])) {
    $toggle_id = (int) $_GET['toggle_id'];
    $sql = "UPDATE products SET is_active = NOT is_active WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $toggle_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    $success_msg = "Product status updated.";
}

// ============================================================
// DELETE PRODUCT
// ============================================================
if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Product deleted successfully.";
    } else {
        $error_msg = "Cannot delete this product - it has existing orders linked to it.";
    }
    mysqli_stmt_close($stmt);
}

require_once __DIR__ . '/../backend/admin-products-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Manage Products</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="frontend-css/admin-services.css">
</head>
<body>
<div class="admin-wrapper">
    <div class="sidebar">
        <div class="sidebar-header"><h2>Salon You</h2><p>Admin Panel</p></div>
        <ul class="sidebar-menu">
            <li><a href="admin-dashboard.php"><i class="fas fa-chart-line"></i><span> Dashboard</span></a></li>
            <li><a href="admin-staff.php"><i class="fas fa-users"></i><span> Staff Management</span></a></li>
            <li><a href="admin-customers.php"><i class="fas fa-user-friends"></i><span> Customer Management</span></a></li>
            <li><a href="admin-appointments.php"><i class="fas fa-calendar-check"></i><span> Appointments</span></a></li>
            <li><a href="admin-queue.php"><i class="fas fa-list-ol"></i><span> Today's Queue</span></a></li>
            <li><a href="admin-services.php"><i class="fas fa-cut"></i><span> Services</span></a></li>
            <li><a href="admin-product-orders.php"><i class="fas fa-shopping-basket"></i><span> Product Orders</span></a></li>
            <li class="active"><a href="admin-products.php"><i class="fas fa-pump-soap"></i><span> Products</span></a></li>
            <li><a href="admin-inventory.php"><i class="fas fa-box"></i><span> Inventory</span></a></li>
            <li><a href="admin-billing.php"><i class="fas fa-money-bill"></i><span> Billing & Payments</span></a></li>
            <li><a href="admin-reports.php"><i class="fas fa-file-invoice-dollar"></i><span> Reports</span></a></li>
            <li><a href="admin-closed-dates.php"><i class="fas fa-calendar-times"></i><span> Closed Dates</span></a></li>
            
            <li><a href="admin-notifications.php"><i class="fas fa-bell"></i><span> Notifications</span></a></li>
            <li><a href="../backend/logout.php"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="topbar">
            <h3>Welcome, <?php echo htmlspecialchars($admin_name); ?></h3>
        </div>

        <div class="page-header"><h2>Manage Products</h2></div>

        <?php if (!empty($low_stock_products)): ?>
        <div class="panel" style="border-left: 3px solid #ef4444;">
            <h4><i class="fas fa-exclamation-triangle" style="color:#ef4444;"></i> Low Stock Products (<?php echo count($low_stock_products); ?>)</h4>
            <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:10px;">
                <?php foreach ($low_stock_products as $item): ?>
                    <span class="badge low">
                        <?php echo htmlspecialchars($item['product_name']); ?> - <?php echo $item['stock_quantity']; ?> left
                    </span>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($success_msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div><?php endif; ?>
        <?php if ($error_msg): ?><div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div><?php endif; ?>

        <!-- Add New Product -->
        <div class="panel">
            <h4>Add New Product</h4>
            <form method="POST" action="admin-products.php" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="product_name" required>
                    </div>
                    <div class="form-group">
                        <label>Price (Rs.)</label>
                        <input type="number" name="price" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Stock Quantity (units)</label>
                        <input type="number" name="stock_quantity" min="0" value="0" required>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Description</label>
                        <textarea name="description" rows="2" style="width:100%;"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Product Image (JPG, PNG, or WEBP - max 5MB)</label>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="add_product" class="btn"><i class="fas fa-plus"></i> Add Product</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Products List -->
        <div class="panel">
            <h4>All Products (<?php echo count($products_list); ?>)</h4>

            <?php if (empty($products_list)): ?>
                <p style="color: var(--text-muted); font-size: 14px;">No products found.</p>
            <?php else: ?>
                <div class="services-grid">
                    <?php foreach ($products_list as $product): ?>
                        <?php $is_low_stock = $product['stock_quantity'] <= 5; ?>
                        <div class="service-card">
                            <?php if ($product['image_url']): ?>
                                <img class="service-thumb" src="../<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                            <?php else: ?>
                                <div class="service-thumb-placeholder"><i class="fas fa-pump-soap"></i></div>
                            <?php endif; ?>
                            <div class="service-card-body">
                                <h5><?php echo htmlspecialchars($product['product_name']); ?></h5>
                                <div class="price-row"><strong>Rs. <?php echo number_format($product['price'], 2); ?></strong></div>
                                <p style="font-size:12px; color: var(--text-muted); margin-top:4px;">
                                    <i class="fas fa-boxes"></i> Stock: <?php echo $product['stock_quantity']; ?> units
                                    <?php if ($is_low_stock): ?><span class="badge low" style="margin-left:6px;">Low</span><?php endif; ?>
                                </p>
                                <span class="badge <?php echo $product['is_active'] ? 'ok' : 'low'; ?>">
                                    <?php echo $product['is_active'] ? 'Active' : 'Hidden'; ?>
                                </span>

                                <div class="card-actions">
                                    <button type="button" class="edit-btn" onclick="toggleEdit(<?php echo $product['id']; ?>)"><i class="fas fa-edit"></i> Edit</button>
                                    <button type="button" class="edit-btn" onclick="toggleRestock(<?php echo $product['id']; ?>)"><i class="fas fa-plus-circle"></i> Restock</button>
                                    <a href="admin-products.php?toggle_id=<?php echo $product['id']; ?>" class="edit-btn">
                                        <i class="fas fa-eye<?php echo $product['is_active'] ? '-slash' : ''; ?>"></i> <?php echo $product['is_active'] ? 'Hide' : 'Show'; ?>
                                    </a>
                                    <a href="admin-products.php?delete_id=<?php echo $product['id']; ?>" class="delete-btn"
                                       onclick="return confirm('Delete this product? This cannot be undone.');"><i class="fas fa-trash"></i> Delete</a>
                                </div>

                                <!-- Restock Panel (hidden by default) -->
                                <div class="edit-panel" id="restock-<?php echo $product['id']; ?>">
                                    <form method="POST" action="admin-products.php" style="display:flex; gap:10px; align-items:end;">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <div class="form-group">
                                            <label>Units to Add (current: <?php echo $product['stock_quantity']; ?>)</label>
                                            <input type="number" name="units_to_add" value="1" min="1" required style="width:120px;">
                                        </div>
                                        <button type="submit" name="restock_product" class="btn"><i class="fas fa-plus"></i> Add Stock</button>
                                    </form>
                                </div>
                                <div class="edit-panel" id="edit-<?php echo $product['id']; ?>">
                                    <form method="POST" action="admin-products.php" enctype="multipart/form-data">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Description</label>
                                            <textarea name="description" rows="2" style="width:100%;"><?php echo htmlspecialchars($product['description']); ?></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label>Price (Rs.)</label>
                                            <input type="number" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Stock Quantity (units)</label>
                                            <input type="number" name="stock_quantity" min="0" value="<?php echo $product['stock_quantity']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label>Replace Image (optional)</label>
                                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
                                        </div>
                                        <button type="submit" name="update_product" class="btn" style="width:100%; justify-content:center;"><i class="fas fa-save"></i> Save Changes</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
function toggleEdit(id) {
    document.getElementById('edit-' + id).classList.toggle('show');
}
function toggleRestock(id) {
    document.getElementById('restock-' + id).classList.toggle('show');
}
</script>
</body>
</html>