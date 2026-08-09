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

require_once __DIR__ . '/admin-staff-performance-data.php';

// Calculate max values for bar chart scaling
$max_appointments = 0;
$max_revenue = 0;
foreach ($staff_performance as $s) {
    if ($s['total_appointments'] > $max_appointments) $max_appointments = $s['total_appointments'];
    if ($s['total_revenue'] > $max_revenue) $max_revenue = $s['total_revenue'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Staff Performance</title>
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
<link rel="stylesheet" href="frontend-css/staff-perfomance.css">
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

        <div class="page-header"><h2>Staff Performance</h2></div>

        <!-- Summary Cards -->
        <div class="cards-row">
            <div class="card purple">
                <h4>Total Staff</h4>
                <p class="card-number"><?php echo $total_staff; ?></p>
            </div>
            <div class="card green">
                <h4>Staff Generated Revenue</h4>
                <p class="card-number">Rs. <?php echo number_format($staff_revenue_total, 2); ?></p>
            </div>
        </div>

        <!-- Staff Performance List -->
        <div class="panel">
            <h4>Staff Members Performance</h4>
            <?php if (empty($staff_performance)): ?>
                <p style="color: var(--text-muted); font-size: 14px;">No staff members found.</p>
            <?php else: ?>
                <?php foreach ($staff_performance as $staff): ?>
                    <div class="staff-card">
                        <div class="staff-card-header">
                            <div>
                                <div class="staff-name"><?php echo htmlspecialchars($staff['name']); ?></div>
                                <div class="staff-contact"><?php echo htmlspecialchars($staff['email']); ?> | <?php echo htmlspecialchars($staff['phone']); ?></div>
                            </div>
                            <?php if ($staff['avg_rating']): ?>
                                <div class="rating-badge">
                                    <i class="fas fa-star"></i> <?php echo number_format($staff['avg_rating'], 1); ?>/5
                                </div>
                            <?php else: ?>
                                <div class="rating-badge" style="background: var(--text-muted); color: var(--bg-dark);">No ratings</div>
                            <?php endif; ?>
                        </div>

                        <div class="staff-metrics">
                            <div class="metric-item">
                                <div class="metric-value"><?php echo $staff['total_appointments'] ?? 0; ?></div>
                                <div class="metric-label">Total Appointments</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value"><?php echo $staff['completed_appointments'] ?? 0; ?></div>
                                <div class="metric-label">Completed</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value">Rs. <?php echo number_format($staff['total_revenue'] ?? 0, 0); ?></div>
                                <div class="metric-label">Revenue</div>
                            </div>
                        </div>

                        <?php if ($staff['total_appointments'] > 0): ?>
                            <div class="bar-row">
                                <div class="bar-label">
                                    <span>Completion Rate</span>
                                    <span><?php echo round(($staff['completed_appointments'] / $staff['total_appointments']) * 100); ?>%</span>
                                </div>
                                <div class="bar-track">
                                    <div class="bar-fill" style="width: <?php echo ($staff['completed_appointments'] / $staff['total_appointments']) * 100; ?>%;"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>