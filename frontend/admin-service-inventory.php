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
// ADD NEW USAGE MAPPING
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_usage'])) {
    $service_id = (int) $_POST['service_id'];
    $inventory_id = (int) $_POST['inventory_id'];
    $uses_consumed = (int) $_POST['uses_consumed'];

    if ($uses_consumed < 1) {
        $error_msg = "Uses consumed must be at least 1.";
    } else {
        // Check if this service+item pair already exists
        $check_sql = "SELECT id FROM service_inventory_usage WHERE service_id = ? AND inventory_id = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "ii", $service_id, $inventory_id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error_msg = "This service is already linked to this inventory item. Delete the existing mapping first to change it.";
        } else {
            $sql = "INSERT INTO service_inventory_usage (service_id, inventory_id, uses_consumed) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iii", $service_id, $inventory_id, $uses_consumed);
            if (mysqli_stmt_execute($stmt)) {
                $success_msg = "Usage mapping added successfully!";
            } else {
                $error_msg = "Error: " . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($check_stmt);
    }
}

// ============================================================
// DELETE USAGE MAPPING
// ============================================================
if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];
    $sql = "DELETE FROM service_inventory_usage WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Usage mapping deleted successfully.";
    } else {
        $error_msg = "Failed to delete: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

require_once __DIR__ . '/admin-service-inventory-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Service Inventory Usage</title>
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
<link rel="stylesheet" href="frontend-css/service-inventory.css">
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
            <h2>Service Inventory Usage</h2>
            <a href="admin-inventory.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Inventory</a>
        </div>

        <?php if ($success_msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div><?php endif; ?>
        <?php if ($error_msg): ?><div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div><?php endif; ?>

        <!-- Add Mapping Form -->
        <div class="panel">
            <h4>Link a Service to an Inventory Item</h4>
            <p class="hint">Define how much of a stock item is used up each time a service is completed. Stock will be reduced automatically when the appointment status becomes "Completed".</p>
            <form method="POST" action="admin-service-inventory.php">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Service</label>
                        <select name="service_id" required>
                            <option value="">-- Select Service --</option>
                            <?php foreach ($all_services as $s): ?>
                                <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['service_name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Inventory Item</label>
                        <select name="inventory_id" required>
                            <option value="">-- Select Item --</option>
                            <?php foreach ($all_inventory as $i): ?>
                                <option value="<?php echo $i['id']; ?>">
                                    <?php echo htmlspecialchars($i['item_name']) . ' (' . $i['quantity'] . ' uses left)'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Uses Consumed per Service</label>
                        <input type="number" name="uses_consumed" value="1" min="1" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="add_usage" class="btn"><i class="fas fa-link"></i> Add Mapping</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Existing Mappings -->
        <div class="panel">
            <h4>Current Mappings (<?php echo count($usage_list); ?>)</h4>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Service</th>
                            <th>Inventory Item</th>
                            <th>Uses per Completion</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($usage_list)): ?>
                            <tr><td colspan="4" style="color: var(--text-muted); text-align:center;">No mappings defined yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($usage_list as $u): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($u['service_name']); ?></td>
                                    <td><?php echo htmlspecialchars($u['item_name']); ?></td>
                                    <td><?php echo $u['uses_consumed']; ?> use(s)</td>
                                    <td class="action-icons">
                                        <a href="admin-service-inventory.php?delete_id=<?php echo $u['id']; ?>" class="delete-link" title="Delete"
                                           onclick="return confirm('Remove this usage mapping?');">
                                            <i class="fas fa-trash"></i>
                                        </a>
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
</body>
</html>