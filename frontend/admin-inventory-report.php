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

// ============================================================
// DATE RANGE FILTER (defaults to the current month)
// ============================================================
$from_date = $_GET['from_date'] ?? date('Y-m-01');
$to_date = $_GET['to_date'] ?? date('Y-m-d');

// Basic validation - fall back to defaults if something odd was passed in
if (!strtotime($from_date)) {
    $from_date = date('Y-m-01');
}
if (!strtotime($to_date)) {
    $to_date = date('Y-m-d');
}

// The "to" date needs to include the whole day, so push it to 23:59:59
$to_date_end = $to_date . ' 23:59:59';
$from_date_start = $from_date . ' 00:00:00';

// FETCH USAGE DATA, GROUPED BY ITEM

$sql = "SELECT
            item_name,
            unit_price,
            SUM(quantity_used) AS total_quantity,
            SUM(quantity_used * unit_price) AS total_cost,
            GROUP_CONCAT(DISTINCT service_name ORDER BY service_name SEPARATOR ', ') AS services_used,
            COUNT(DISTINCT appointment_id) AS times_used
        FROM inventory_usage_log
        WHERE used_at BETWEEN ? AND ?
        GROUP BY item_name, unit_price
        ORDER BY total_cost DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $from_date_start, $to_date_end);
mysqli_stmt_execute($stmt);
$report_rows = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// GRAND TOTALS

$grand_total_qty = 0;
$grand_total_cost = 0;
foreach ($report_rows as $row) {
    $grand_total_qty += $row['total_quantity'];
    $grand_total_cost += $row['total_cost'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Inventory Usage Report</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="frontend-css/inventory.css">
<style>
    .filter-panel {
        display: flex;
        gap: 15px;
        align-items: end;
        flex-wrap: wrap;
    }
    .filter-panel .form-group label {
        display: block;
        margin-bottom: 5px;
        font-size: 13px;
    }
    .quick-filters {
        display: flex;
        gap: 8px;
        margin-top: 10px;
        flex-wrap: wrap;
    }
    .quick-filters a {
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 20px;
        border: 1px solid var(--border-color, #444);
        text-decoration: none;
        color: inherit;
    }
    .quick-filters a:hover {
        opacity: 0.8;
    }
    table tfoot td {
        font-weight: 700;
        border-top: 2px solid var(--border-color, #444);
    }
    .desc-cell {
        font-size: 12px;
        color: var(--text-muted, #888);
        max-width: 320px;
    }
</style>
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
            <li><a href="admin-products.php"><i class="fas fa-pump-soap"></i><span> Product</span></a></li>
            <li><a href="admin-product-orders.php"><i class="fas fa-shopping-basket"></i><span> Product Orders</span></a></li>
            <li class="active"><a href="admin-inventory.php"><i class="fas fa-box"></i><span> Inventory</span></a></li>
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

        <div class="page-header">
            <h2>Inventory Usage Report</h2>
            <a href="admin-inventory.php" class="btn btn-secondary" style="float: right;"><i class="fas fa-arrow-left"></i> Back to Inventory</a>
        </div>

        <!-- Date Filter -->
        <div class="panel">
            <h4>Filter by Date</h4>
            <form method="GET" action="admin-inventory-report.php" class="filter-panel">
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
            <div class="quick-filters">
                <a href="admin-inventory-report.php?from_date=<?php echo date('Y-m-d'); ?>&to_date=<?php echo date('Y-m-d'); ?>">Today</a>
                <a href="admin-inventory-report.php?from_date=<?php echo date('Y-m-d', strtotime('monday this week')); ?>&to_date=<?php echo date('Y-m-d'); ?>">This Week</a>
                <a href="admin-inventory-report.php?from_date=<?php echo date('Y-m-01'); ?>&to_date=<?php echo date('Y-m-d'); ?>">This Month</a>
                <a href="admin-inventory-report.php?from_date=<?php echo date('Y-m-01', strtotime('first day of last month')); ?>&to_date=<?php echo date('Y-m-t', strtotime('last day of last month')); ?>">Last Month</a>
                <a href="admin-inventory-report.php?from_date=<?php echo date('Y-01-01'); ?>&to_date=<?php echo date('Y-m-d'); ?>">This Year</a>
            </div>
        </div>

        <!-- Report Table -->
        <div class="panel">
            <h4>Usage Summary: <?php echo htmlspecialchars($from_date); ?> to <?php echo htmlspecialchars($to_date); ?> (<?php echo count($report_rows); ?> items)</h4>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Description</th>
                            <th>Unit Price</th>
                            <th>Total Quantity Used</th>
                            <th>Total Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($report_rows)): ?>
                            <tr><td colspan="5" style="text-align:center; color: var(--text-muted);">No inventory usage recorded for this period.</td></tr>
                        <?php else: ?>
                            <?php foreach ($report_rows as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['item_name']); ?></td>
                                    <td class="desc-cell">
                                        Used in <?php echo $row['times_used']; ?> completed appointment(s) for:
                                        <?php echo htmlspecialchars($row['services_used']); ?>
                                    </td>
                                    <td>Rs. <?php echo number_format($row['unit_price'], 2); ?></td>
                                    <td><?php echo $row['total_quantity']; ?></td>
                                    <td>Rs. <?php echo number_format($row['total_cost'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($report_rows)): ?>
                    <tfoot>
                        <tr>
                            <td colspan="3">GRAND TOTAL</td>
                            <td><?php echo $grand_total_qty; ?></td>
                            <td>Rs. <?php echo number_format($grand_total_cost, 2); ?></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>