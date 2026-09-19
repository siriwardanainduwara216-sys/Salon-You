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

// The physical folder (on disk) where uploaded service images are stored
$upload_dir = __DIR__ . '/../uploads/images/services/';
// The web-relative path stored in the database (root-relative, no leading slash)
$upload_db_prefix = 'uploads/images/services/';

// Make sure the upload folder exists
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// ---- Helper: handle an image upload, returns the DB path or null ----
function handle_image_upload($file, $upload_dir, $upload_db_prefix, &$error_msg) {
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // no file selected - not an error, just skip
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

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        $error_msg = "Image must be smaller than 5MB.";
        return null;
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safe_filename = uniqid('service_', true) . '.' . $extension;

    if (move_uploaded_file($file['tmp_name'], $upload_dir . $safe_filename)) {
        return $upload_db_prefix . $safe_filename;
    } else {
        $error_msg = "Could not save the uploaded image.";
        return null;
    }
}

// ============================================================
// ADD NEW SERVICE
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $service_name = trim($_POST['service_name']);
    $category = $_POST['category'];
    $price = (float) $_POST['price'];
    $duration_mins = (int) $_POST['duration_mins'];

    if (empty($service_name)) {
        $error_msg = "Service name is required.";
    } else {
        $image_url = handle_image_upload($_FILES['image'] ?? null, $upload_dir, $upload_db_prefix, $error_msg);

        if (empty($error_msg)) {
            $sql = "INSERT INTO services (service_name, category, image_url, price, duration_mins) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssdi", $service_name, $category, $image_url, $price, $duration_mins);
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Service added successfully!";
            } else {
                $error_msg = "Error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// ============================================================
// UPDATE SERVICE
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_service'])) {
    $service_id = (int) $_POST['service_id'];
    $service_name = trim($_POST['service_name']);
    $category = $_POST['category'];
    $price = (float) $_POST['price'];
    $duration_mins = (int) $_POST['duration_mins'];

    // Only touch the image if a new one was uploaded; otherwise keep the existing one
    $new_image_url = handle_image_upload($_FILES['image'] ?? null, $upload_dir, $upload_db_prefix, $error_msg);

    if (empty($error_msg)) {
        if ($new_image_url) {
            $sql = "UPDATE services SET service_name=?, category=?, image_url=?, price=?, duration_mins=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sssdii", $service_name, $category, $new_image_url, $price, $duration_mins, $service_id);
        } else {
            $sql = "UPDATE services SET service_name=?, category=?, price=?, duration_mins=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssdii", $service_name, $category, $price, $duration_mins, $service_id);
        }

        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Service updated successfully!";
        } else {
            $error_msg = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// ============================================================
// DELETE SERVICE
// ============================================================
if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];

    // Remove any inventory-usage links tied to this service first (avoids foreign key errors)
    $sql = "DELETE FROM service_inventory_usage WHERE service_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $sql = "DELETE FROM services WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Service deleted successfully.";
    } else {
        // Likely blocked because appointments already reference this service
        $error_msg = "Cannot delete this service - it has existing appointments linked to it.";
    }
    mysqli_stmt_close($stmt);
}

require_once __DIR__ . '/admin-services-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Manage Services</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
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
            <li class="active"><a href="admin-services.php"><i class="fas fa-cut"></i><span> Services</span></a></li>
            <li><a href="admin-inventory.php"><i class="fas fa-box"></i><span> Inventory</span></a></li>
            <li><a href="admin-billing.php"><i class="fas fa-money-bill"></i><span> Billing & Payments</span></a></li>
            <li><a href="admin-reports.php"><i class="fas fa-file-invoice-dollar"></i><span> Reports</span></a></li>
            <li><a href="admin-notifications.php"><i class="fas fa-bell"></i><span> Notifications</span></a></li>
            <li><a href="../backend/logout.php"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="topbar">
            <h3>Welcome, <?php echo htmlspecialchars($admin_name); ?></h3>
        </div>

        <div class="page-header"><h2>Manage Services</h2></div>

        <?php if ($success_msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div><?php endif; ?>
        <?php if ($error_msg): ?><div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div><?php endif; ?>

        <!-- Add New Service -->
        <div class="panel">
            <h4>Add New Service</h4>
            <form method="POST" action="admin-services.php" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Service Name</label>
                        <input type="text" name="service_name" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category" required>
                            <?php foreach ($category_labels as $key => $label): ?>
                                <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Price (Rs.)</label>
                        <input type="number" name="price" step="0.01" min="0" required>
                    </div>
                    <div class="form-group">
                        <label>Duration (minutes)</label>
                        <input type="number" name="duration_mins" min="1" value="30" required>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label>Service Image (JPG, PNG, or WEBP - max 5MB)</label>
                        <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="add_service" class="btn"><i class="fas fa-plus"></i> Add Service</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Services List -->
        <div class="panel">
            <h4>All Services (<?php echo count($all_services_list); ?>)</h4>

            <?php if (empty($all_services_list)): ?>
                <p style="color: var(--text-muted); font-size: 14px;">No services found.</p>
            <?php else: ?>
                <?php foreach ($services_grouped as $cat_key => $services_in_cat): ?>
                    <?php if (empty($services_in_cat)) continue; // skip categories with no services ?>
                    <div class="category-group">
                        <div class="category-group-header">
                            <h4><?php echo $category_labels[$cat_key] ?? $cat_key; ?></h4>
                            <span class="cat-count"><?php echo count($services_in_cat); ?> services</span>
                        </div>
                        <div class="services-grid">
                            <?php foreach ($services_in_cat as $service): ?>
                                <div class="service-card">
                                    <?php if ($service['image_url']): ?>
                                        <img class="service-thumb" src="../<?php echo htmlspecialchars($service['image_url']); ?>" alt="<?php echo htmlspecialchars($service['service_name']); ?>">
                                    <?php else: ?>
                                        <div class="service-thumb-placeholder"><i class="fas fa-cut"></i></div>
                                    <?php endif; ?>
                                    <div class="service-card-body">
                                        <h5><?php echo htmlspecialchars($service['service_name']); ?></h5>
                                        <div class="price-row"><strong>Rs. <?php echo number_format($service['price'], 2); ?></strong> &middot; <?php echo $service['duration_mins']; ?> min</div>
                                        <div class="card-actions">
                                            <button type="button" class="edit-btn" onclick="toggleEdit(<?php echo $service['id']; ?>)"><i class="fas fa-edit"></i> Edit</button>
                                            <a href="admin-services.php?delete_id=<?php echo $service['id']; ?>" class="delete-btn"
                                               onclick="return confirm('Delete this service? This cannot be undone.');"><i class="fas fa-trash"></i> Delete</a>
                                        </div>

                                        <!-- Edit Panel (hidden by default) -->
                                        <div class="edit-panel" id="edit-<?php echo $service['id']; ?>">
                                            <form method="POST" action="admin-services.php" enctype="multipart/form-data">
                                                <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                                <div class="form-group">
                                                    <label>Name</label>
                                                    <input type="text" name="service_name" value="<?php echo htmlspecialchars($service['service_name']); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Category</label>
                                                    <select name="category" required>
                                                        <?php foreach ($category_labels as $key => $label): ?>
                                                            <option value="<?php echo $key; ?>" <?php echo $service['category'] === $key ? 'selected' : ''; ?>><?php echo $label; ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                    <label>Price (Rs.)</label>
                                                    <input type="number" name="price" step="0.01" min="0" value="<?php echo $service['price']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Duration (min)</label>
                                                    <input type="number" name="duration_mins" min="1" value="<?php echo $service['duration_mins']; ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Replace Image (optional)</label>
                                                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
                                                </div>
                                                <button type="submit" name="update_service" class="btn" style="width:100%; justify-content:center;"><i class="fas fa-save"></i> Save Changes</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>

<script>
function toggleEdit(id) {
    document.getElementById('edit-' + id).classList.toggle('show');
}
</script>
</body>
</html>