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

// ============================================================
// ADD NEW ITEM
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $item_name = trim($_POST['item_name']);
    $category = trim($_POST['category']);
    $bottles_added = (int) $_POST['bottles_added'];
    $unit = trim($_POST['unit']);
    $uses_per_unit = (int) $_POST['uses_per_unit'];
    $threshold = (int) $_POST['low_stock_threshold'];
    $price = (float) $_POST['price'];

    if (empty($item_name)) {
        $error_msg = "Item name is required.";
    } elseif ($uses_per_unit < 1) {
        $error_msg = "Uses per unit must be at least 1.";
    } else {
        // Convert bottles into total usable units before saving.
        $total_uses = $bottles_added * $uses_per_unit;

        $sql = "INSERT INTO inventory (item_name, category, quantity, unit, uses_per_unit, low_stock_threshold, price)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssisiid", $item_name, $category, $total_uses, $unit, $uses_per_unit, $threshold, $price);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Item added successfully! ($bottles_added $unit = $total_uses total uses)";
        } else {
            $error_msg = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

// ============================================================
// UPDATE ITEM (item details only - not stock quantity)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_item'])) {
    $item_id = (int) $_POST['item_id'];
    $item_name = trim($_POST['item_name']);
    $category = trim($_POST['category']);
    $unit = trim($_POST['unit']);
    $uses_per_unit = (int) $_POST['uses_per_unit'];
    $threshold = (int) $_POST['low_stock_threshold'];
    $price = (float) $_POST['price'];

    $sql = "UPDATE inventory SET item_name=?, category=?, unit=?, uses_per_unit=?, low_stock_threshold=?, price=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssiidi", $item_name, $category, $unit, $uses_per_unit, $threshold, $price, $item_id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Item updated successfully!";
    } else {
        $error_msg = "Error: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// ============================================================
// RESTOCK ITEM (add more bottles to existing stock)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restock_item'])) {
    $item_id = (int) $_POST['item_id'];
    $bottles_to_add = (int) $_POST['bottles_to_add'];

    // Get this item's uses_per_unit so we can convert bottles into total uses
    $sql = "SELECT uses_per_unit, item_name FROM inventory WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $item_id);
    mysqli_stmt_execute($stmt);
    $item_row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($item_row && $bottles_to_add > 0) {
        $uses_to_add = $bottles_to_add * $item_row['uses_per_unit'];
        $sql = "UPDATE inventory SET quantity = quantity + ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $uses_to_add, $item_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Restocked " . htmlspecialchars($item_row['item_name']) . " with $uses_to_add more uses!";
        } else {
            $error_msg = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error_msg = "Enter a valid number of bottles to add.";
    }
}

// ============================================================
// DELETE ITEM
// ============================================================
if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];
    $sql = "DELETE FROM inventory WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Item deleted successfully.";
    } else {
        $error_msg = "Failed to delete: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

require_once __DIR__ . '/admin-inventory-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Inventory Management</title>
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
<link rel="stylesheet" href="frontend-css/inventory.css">
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
            <li class="active"><a href="admin-inventory.php"><i class="fas fa-box"></i><span> Inventory</span></a></li>
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

        <div class="page-header">
            <h2>Inventory Management</h2>
            <a href="admin-service-inventory.php" class="btn" style="float: right;"><i class="fas fa-link"></i> Manage Service Usage Links</a>
        </div>

        <?php if ($success_msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div><?php endif; ?>
        <?php if ($error_msg): ?><div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div><?php endif; ?>

        <!-- Add Item Form -->
        <div class="panel">
            <h4>Add New Stock Item</h4>
            <p style="color: var(--text-muted); font-size: 12px; margin-bottom: 15px;">
                Enter how many bottles/packs you received and how many customer uses each one gives.
                The system will automatically calculate the total uses in stock.
            </p>
            <form method="POST" action="admin-inventory.php">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" name="item_name" required>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
                        <input type="text" name="category" placeholder="e.g. Hair Care">
                    </div>
                    <div class="form-group">
                        <label>Bottles Added</label>
                        <input type="number" name="bottles_added" value="1" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Unit</label>
                        <input type="text" name="unit" placeholder="e.g. bottles" value="pcs">
                    </div>
                    <div class="form-group">
                        <label>Uses per Unit</label>
                        <input type="number" name="uses_per_unit" value="1" min="1" required>
                    </div>
                    <div class="form-group">
                        <label>Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" value="5" min="0">
                    </div>
                    <div class="form-group">
                        <label>Price (Rs.)</label>
                        <input type="number" name="price" step="0.01" value="0.00" min="0">
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="add_item" class="btn"><i class="fas fa-plus"></i> Add Item</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Inventory List -->
        <div class="panel">
            <h4>All Stock Items (<?php echo count($inventory_list); ?>)</h4>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Category</th>
                            <th>Total Uses Left</th>
                            <th>Uses/Unit</th>
                            <th>Threshold</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($inventory_list)): ?>
                            <tr><td colspan="8" style="color: var(--text-muted); text-align:center;">No stock items found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($inventory_list as $item): ?>
                                <?php
                                $is_low = $item['quantity'] <= $item['low_stock_threshold'];
                                $bottles_remaining = $item['uses_per_unit'] > 0 ? floor($item['quantity'] / $item['uses_per_unit']) : 0;
                                ?>
                                <tr id="row-<?php echo $item['id']; ?>">
                                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['category']); ?></td>
                                    <td>
                                        <?php echo $item['quantity']; ?> uses
                                        <div style="font-size: 11px; color: var(--text-muted);">
                                            ≈ <?php echo $bottles_remaining; ?> <?php echo htmlspecialchars($item['unit']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo $item['uses_per_unit']; ?></td>
                                    <td><?php echo $item['low_stock_threshold']; ?></td>
                                    <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                                    <td><span class="badge <?php echo $is_low ? 'low' : 'ok'; ?>"><?php echo $is_low ? 'Low Stock' : 'OK'; ?></span></td>
                                    <td>
                                        <div class="action-icons">
                                            <button type="button" onclick="toggleEdit(<?php echo $item['id']; ?>)" title="Edit Details"><i class="fas fa-edit"></i></button>
                                            <button type="button" onclick="toggleRestock(<?php echo $item['id']; ?>)" title="Restock"><i class="fas fa-plus-circle"></i></button>
                                            <a href="admin-inventory.php?delete_id=<?php echo $item['id']; ?>" class="delete-link" title="Delete"
                                               onclick="return confirm('Are you sure you want to delete this item?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Restock row -->
                                <tr class="edit-row" id="restock-<?php echo $item['id']; ?>">
                                    <td colspan="8">
                                        <form method="POST" action="admin-inventory.php" style="display:flex; gap:10px; align-items:end;">
                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                            <div class="form-group">
                                                <label>Bottles to Add (each gives <?php echo $item['uses_per_unit']; ?> uses)</label>
                                                <input type="number" name="bottles_to_add" value="1" min="1" required style="width:150px;">
                                            </div>
                                            <button type="submit" name="restock_item" class="btn"><i class="fas fa-plus"></i> Add Stock</button>
                                        </form>
                                    </td>
                                </tr>
                                <tr class="edit-row" id="edit-<?php echo $item['id']; ?>">
                                    <td colspan="8">
                                        <form method="POST" action="admin-inventory.php">
                                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                                            <div class="edit-form-grid">
                                                <input type="text" name="item_name" value="<?php echo htmlspecialchars($item['item_name']); ?>" required>
                                                <input type="text" name="category" value="<?php echo htmlspecialchars($item['category']); ?>">
                                                <input type="text" name="unit" value="<?php echo htmlspecialchars($item['unit']); ?>">
                                                <input type="number" name="uses_per_unit" value="<?php echo $item['uses_per_unit']; ?>" min="1" required>
                                                <input type="number" name="low_stock_threshold" value="<?php echo $item['low_stock_threshold']; ?>" min="0">
                                                <input type="number" name="price" step="0.01" value="<?php echo $item['price']; ?>" min="0">
                                                <button type="submit" name="update_item"><i class="fas fa-save"></i> Save</button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function toggleEdit(id) {
    const row = document.getElementById('edit-' + id);
    row.classList.toggle('show');
}
function toggleRestock(id) {
    const row = document.getElementById('restock-' + id);
    row.classList.toggle('show');
}
</script>

</body>
</html>