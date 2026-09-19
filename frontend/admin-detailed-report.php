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

// ---- Date range filter (defaults to the last 30 days if not specified) ----
$date_from = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
$date_to = $_GET['date_to'] ?? date('Y-m-d');

require_once __DIR__ . '/admin-detailed-report-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Detailed Service Report</title>
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
<link rel="stylesheet" href="frontend-css/detailed-report.css">
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
            <li><a href="admin-inventory.php"><i class="fas fa-box"></i><span> Inventory</span></a></li>
            <li><a href="admin-billing.php"><i class="fas fa-money-bill"></i><span> Billing & Payments</span></a></li>
            <li class="active"><a href="admin-reports.php"><i class="fas fa-file-invoice-dollar"></i><span> Reports</span></a></li>
            <li><a href="admin-notifications.php"><i class="fas fa-bell"></i><span> Notifications</span></a></li>
            <li><a href="../backend/logout.php"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="topbar">
            <h3>Welcome, <?php echo htmlspecialchars($admin_name); ?></h3>
        </div>

        <div class="page-header">
            <h2>Detailed Service Report</h2>
            <a href="admin-reports.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Reports</a>
        </div>

        <!-- Date Range Filter -->
        <div class="panel">
            <h4>Filter by Date Range</h4>
            <form method="GET" action="admin-detailed-report.php" class="filter-form">
                <div class="form-group">
                    <label>From</label>
                    <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>" required>
                </div>
                <div class="form-group">
                    <label>To</label>
                    <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>" required>
                </div>
                <button type="submit" class="btn"><i class="fas fa-filter"></i> Apply Filter</button>
            </form>
        </div>

        <!-- Overall Summary Cards -->
        <div class="cards-row">
            <div class="card purple">
                <h4>Total Services (All Staff)</h4>
                <p class="card-number"><?php echo $grand_total_services; ?></p>
            </div>
            <div class="card green">
                <h4>Total Revenue (All Staff)</h4>
                <p class="card-number">Rs. <?php echo number_format($grand_total_revenue, 2); ?></p>
            </div>
        </div>

        <!-- Grouped by Employee -->
        <div class="panel">
            <h4>Services by Employee (<?php echo htmlspecialchars($date_from); ?> to <?php echo htmlspecialchars($date_to); ?>)</h4>

            <?php if (empty($grouped_by_staff)): ?>
                <p style="color: var(--text-muted); font-size: 14px;">No completed services found in this date range.</p>
            <?php else: ?>
                <?php foreach ($grouped_by_staff as $staff_id => $group): ?>
                    <div class="staff-group">
                        <div class="staff-group-header">
                            <h4><i class="fas fa-user"></i> <?php echo htmlspecialchars($group['staff_name']); ?></h4>
                            <div class="subtotal">
                                <?php echo $group['subtotal_count']; ?> services &nbsp;|&nbsp;
                                <strong>Rs. <?php echo number_format($group['subtotal_revenue'], 2); ?></strong>
                            </div>
                        </div>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Customer Email</th>
                                        <th>Service</th>
                                        <th>Price</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($group['services'] as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['customer_email']); ?></td>
                                            <td><?php echo htmlspecialchars($item['service_name']); ?></td>
                                            <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                                            <td><?php echo $item['appointment_date']; ?></td>
                                            <td><?php echo date('h:i A', strtotime($item['appointment_time'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>
</body>
</html>