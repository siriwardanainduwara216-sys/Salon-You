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
// UPDATE ORDER STATUS
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int) $_POST['order_id'];
    $new_status = $_POST['new_status'];

    $allowed_statuses = ['pending', 'ready', 'completed', 'cancelled'];
    if (!in_array($new_status, $allowed_statuses)) {
        $error_msg = "Invalid status value.";
    } else {
        // Fetch current status first, so we only deduct stock the FIRST time
        // this order becomes "completed" (never twice).
        $sql = "SELECT status FROM product_orders WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        $current = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        $was_already_completed = $current && $current['status'] === 'completed';

        $sql = "UPDATE product_orders SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $new_status, $order_id);

        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Order status updated successfully!";

            // ---- Deduct stock, only on first transition to "completed" ----
            if ($new_status === 'completed' && !$was_already_completed) {
                $sql = "SELECT product_id, quantity FROM product_order_items WHERE order_id = ?";
                $item_stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($item_stmt, "i", $order_id);
                mysqli_stmt_execute($item_stmt);
                $items_result = mysqli_stmt_get_result($item_stmt);

                while ($item = mysqli_fetch_assoc($items_result)) {
                    $dsql = "UPDATE products SET stock_quantity = GREATEST(stock_quantity - ?, 0) WHERE id = ?";
                    $dstmt = mysqli_prepare($conn, $dsql);
                    mysqli_stmt_bind_param($dstmt, "ii", $item['quantity'], $item['product_id']);
                    mysqli_stmt_execute($dstmt);
                    mysqli_stmt_close($dstmt);
                }
                mysqli_stmt_close($item_stmt);
            }
        } else {
            $error_msg = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

require_once __DIR__ . '/../backend/admin-product-orders-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Product Orders</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="frontend-css/appoinment.css">
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
            <li><a href="admin-products.php"><i class="fas fa-pump-soap"></i><span> Products</span></a></li>
            <li class="active"><a href="admin-product-orders.php"><i class="fas fa-shopping-basket"></i><span> Product Orders</span></a></li>
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

        <div class="page-header"><h2>Product Orders</h2></div>

        <!-- Date + Status Filter -->
        <div class="panel">
            <h4>Filter by Date</h4>
            <form method="GET" action="admin-product-orders.php" class="filter-panel" style="display:flex; gap:15px; align-items:end; flex-wrap:wrap;">
                <input type="hidden" name="status" value="<?php echo $status_filter; ?>">
                <div class="form-group">
                    <label>From</label>
                    <input type="date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>">
                </div>
                <div class="form-group">
                    <label>To</label>
                    <input type="date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn"><i class="fas fa-filter"></i> Apply Filter</button>
                </div>
            </form>
            <div class="quick-filters" style="display:flex; gap:8px; margin-top:10px;">
                <?php $status_qs = '&status=' . $status_filter; ?>
                <a href="admin-product-orders.php?from_date=<?php echo date('Y-m-d'); ?>&to_date=<?php echo date('Y-m-d'); ?><?php echo $status_qs; ?>">Today</a>
                <a href="admin-product-orders.php?from_date=<?php echo date('Y-m-d', strtotime('monday this week')); ?>&to_date=<?php echo date('Y-m-d'); ?><?php echo $status_qs; ?>">This Week</a>
                <a href="admin-product-orders.php?from_date=<?php echo date('Y-m-01'); ?>&to_date=<?php echo date('Y-m-d'); ?><?php echo $status_qs; ?>">This Month</a>
                <a href="admin-product-orders.php?from_date=<?php echo date('Y-m-01', strtotime('first day of last month')); ?>&to_date=<?php echo date('Y-m-t', strtotime('last day of last month')); ?><?php echo $status_qs; ?>">Last Month</a>
                <a href="admin-product-orders.php?from_date=<?php echo date('Y-01-01'); ?>&to_date=<?php echo date('Y-m-d'); ?><?php echo $status_qs; ?>">This Year</a>
            </div>
        </div>

        <!-- Revenue Summary (completed orders only, within selected range) -->
        <div class="panel">
            <h4>Sales Summary: <?php echo htmlspecialchars($from_date); ?> to <?php echo htmlspecialchars($to_date); ?></h4>
            <p style="font-size: 1.4rem; font-weight: 700; color: #d4af37;">
                Rs. <?php echo number_format($revenue_total, 2); ?>
                <span style="font-size:0.7rem; color: var(--text-muted); font-weight:400;">(completed orders only)</span>
            </p>
        </div>

        

        <?php if ($success_msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div><?php endif; ?>
        <?php if ($error_msg): ?><div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div><?php endif; ?>

        <!-- Status Filter Tabs -->
        <div class="filter-tabs">
            <a href="admin-product-orders.php?status=all" class="<?php echo $status_filter === 'all' ? 'active' : ''; ?>">
                All <span class="count">(<?php echo $status_counts['all']; ?>)</span>
            </a>
            <a href="admin-product-orders.php?status=pending" class="<?php echo $status_filter === 'pending' ? 'active' : ''; ?>">
                Pending <span class="count">(<?php echo $status_counts['pending']; ?>)</span>
            </a>
            <a href="admin-product-orders.php?status=ready" class="<?php echo $status_filter === 'ready' ? 'active' : ''; ?>">
                Ready <span class="count">(<?php echo $status_counts['ready']; ?>)</span>
            </a>
            <a href="admin-product-orders.php?status=completed" class="<?php echo $status_filter === 'completed' ? 'active' : ''; ?>">
                Completed <span class="count">(<?php echo $status_counts['completed']; ?>)</span>
            </a>
            <a href="admin-product-orders.php?status=cancelled" class="<?php echo $status_filter === 'cancelled' ? 'active' : ''; ?>">
                Cancelled <span class="count">(<?php echo $status_counts['cancelled']; ?>)</span>
            </a>
        </div>

        <div class="panel">
            <h4>Orders (<?php echo count($orders_list); ?>)</h4>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Pickup Date</th>
                            <th>Status</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders_list)): ?>
                            <tr><td colspan="7" class="no-data">No orders found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($orders_list as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_phone']); ?></td>
                                    <td>
                                        <?php foreach ($order['items'] as $item): ?>
                                            <div style="font-size:12px;">
                                                <?php echo htmlspecialchars($item['product_name']); ?> &times; <?php echo $item['quantity']; ?>
                                            </div>
                                        <?php endforeach; ?>
                                    </td>
                                    <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><?php echo $order['pickup_date'] ? date('M j, Y', strtotime($order['pickup_date'])) : '-'; ?></td>
                                    <td><span class="badge <?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span></td>
                                    <td>
                                        <form method="POST" action="admin-product-orders.php?status=<?php echo $status_filter; ?>" class="status-form">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="new_status">
                                                <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="ready" <?php echo $order['status'] === 'ready' ? 'selected' : ''; ?>>Ready</option>
                                                <option value="completed" <?php echo $order['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" name="update_status">Save</button>
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
</body>
</html>