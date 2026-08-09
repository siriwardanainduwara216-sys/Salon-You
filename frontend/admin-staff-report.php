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

$staff_id = (int) ($_GET['staff_id'] ?? 0);
if ($staff_id <= 0) {
    header("Location: admin-staff.php");
    exit();
}

// ---- Date range filter (defaults to the last 30 days if not specified) ----
$date_from = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
$date_to = $_GET['date_to'] ?? date('Y-m-d');

require_once __DIR__ . '/admin-staff-report-data.php';

if (!$staff_info) {
    header("Location: admin-staff.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Staff Report</title>
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
<link rel="stylesheet" href="frontend-css/staff-report.css">
</head>
<body>
<div class="admin-wrapper">
    <div class="sidebar">
        <div class="sidebar-header"><h2>Salon You</h2><p>Admin Panel</p></div>
        <ul class="sidebar-menu">
            <li><a href="admin-dashboard.php"><i class="fas fa-chart-line"></i><span> Dashboard</span></a></li>
            <li class="active"><a href="admin-staff.php"><i class="fas fa-users"></i><span> Staff Management</span></a></li>
            <li><a href="admin-customers.php"><i class="fas fa-user-friends"></i><span> Customer Management</span></a></li>
            <li><a href="admin-appointments.php"><i class="fas fa-calendar-check"></i><span> Appointments</span></a></li>
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

        <div class="page-header">
            <h2>Staff Performance Report</h2>
            <a href="admin-staff.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Staff Management</a>
        </div>

        <!-- Staff Info -->
        <div class="staff-info-card">
            <div>
                <h3><?php echo htmlspecialchars($staff_info['name']); ?></h3>
                <p><?php echo htmlspecialchars($staff_info['email']); ?> | <?php echo htmlspecialchars($staff_info['phone']); ?></p>
            </div>
            <span class="badge <?php echo $staff_info['status']; ?>"><?php echo ucfirst($staff_info['status']); ?></span>
        </div>

        <!-- Date Range Filter -->
        <div class="panel">
            <h4>Filter by Date Range</h4>
            <form method="GET" action="admin-staff-report.php" class="filter-form">
                <input type="hidden" name="staff_id" value="<?php echo $staff_id; ?>">
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

        <!-- Summary Cards -->
        <div class="cards-row">
            <div class="card purple">
                <h4>Total Services Completed</h4>
                <p class="card-number"><?php echo $total_services_count; ?></p>
            </div>
            <div class="card green">
                <h4>Total Revenue Generated</h4>
                <p class="card-number">Rs. <?php echo number_format($total_revenue, 2); ?></p>
            </div>
        </div>

        <!-- Services List -->
        <div class="panel">
            <h4>Completed Services (<?php echo htmlspecialchars($date_from); ?> to <?php echo htmlspecialchars($date_to); ?>)</h4>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Customer Email</th>
                            <th>Service</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($staff_services)): ?>
                            <tr><td colspan="6" style="color: var(--text-muted); text-align:center;">No completed services in this date range.</td></tr>
                        <?php else: ?>
                            <?php foreach ($staff_services as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['customer_email']); ?></td>
                                    <td><?php echo htmlspecialchars($item['service_name']); ?></td>
                                    <td><?php echo $item['appointment_date']; ?></td>
                                    <td><?php echo date('h:i A', strtotime($item['appointment_time'])); ?></td>
                                    <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
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