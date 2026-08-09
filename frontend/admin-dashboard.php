<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Login wela nathnam login page ekata redirect
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Login wela thiyenawa, ithin role eka 'admin' da kiyala check karanawa
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: index.php"); // Redirect non-admins to home page
    exit();
}

// 3. Store session variables in local vars for easy use in frontend
$admin_name = $_SESSION['user_name'];

// 4. Database Connection
require_once __DIR__ . '/../backend/config.php';
global $conn;

// 5. All dashboard data queries live in a separate file
require_once __DIR__ . '/admin-dashboard-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Admin Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
<link rel="stylesheet" href="frontend-css/dashboard.css">
</head>
<body>

<div class="admin-wrapper">

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Salon You</h2>
            <p>Admin Panel</p>
        </div>
        <ul class="sidebar-menu">
            <li class="active"><a href="#"><i class="fas fa-chart-line"></i><span> Dashboard</span></a></li>
            <li><a href="admin-staff.php"><i class="fas fa-users"></i><span> Staff Management</span></a></li>
            <li><a href="admin-customers.php"><i class="fas fa-user-friends"></i><span> Customer Management</span></a></li>
            <li><a href="admin-appointments.php"><i class="fas fa-calendar-check"></i><span> Appointments</span></a></li>
            <li><a href="admin-services.php"><i class="fas fa-cut"></i><span> Services</span></a></li>
            <li><a href="admin-inventory.php"><i class="fas fa-box"></i><span> Inventory</span></a></li>
            <li><a href="admin-billing.php"><i class="fas fa-money-bill"></i><span> Billing & Payments</span></a></li>
            <li><a href="admin-reports.php"><i class="fas fa-file-invoice-dollar"></i><span> Reports</span></a></li>
            <li><a href="admin-notifications.php"><i class="fas fa-bell"></i><span> Notifications</span></a></li>
            <li><a href="../backend/logout.php"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a></li>
        </ul>
    </div>

    <!-- Main content -->
    <div class="main-content">

        <div class="topbar">
            <h3>Welcome, <?php echo htmlspecialchars($admin_name); ?></h3>
            <div class="topbar-right">
                <span class="notif-icon"><i class="fas fa-bell"></i><span class="notif-dot"></span></span>
                <span class="admin-name"><?php echo htmlspecialchars($admin_name); ?></span>
            </div>
        </div>

        <div class="dashboard-content">
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <h2>Dashboard Overview</h2>
                <a href="admin-staff-performance.php" style="display:inline-flex; align-items:center; gap:8px; background: var(--accent-purple); color:white; border:none; padding:10px 18px; border-radius:8px; font-size:14px; text-decoration:none;">
                    <i class="fas fa-users-cog"></i> Staff Performance
                </a>
            </div>

            <div class="cards-row">
                <div class="card purple">
                    <h4>Today's Bookings</h4>
                    <p class="card-number"><?php echo $today_count; ?></p>
                </div>
                <div class="card amber">
                    <h4>This Week</h4>
                    <p class="card-number"><?php echo $week_count; ?></p>
                </div>
                <div class="card blue">
                    <h4>This Month</h4>
                    <p class="card-number"><?php echo $month_count; ?></p>
                </div>
                <div class="card green">
                    <h4>Total Revenue</h4>
                    <p class="card-number">Rs. <?php echo number_format($total_revenue, 2); ?></p>
                </div>
            </div>

            <div class="section-row">
                <div class="panel">
                    <h4>Recent Appointments</h4>
                    <?php if (empty($recent_appointments)): ?>
                        <p style="color: var(--text-muted); font-size: 13px;">No appointments found.</p>
                    <?php else: ?>
                        <?php foreach ($recent_appointments as $appt): ?>
                            <div class="panel-list-item">
                                <span><?php echo htmlspecialchars($appt['customer_name']); ?> - <?php echo htmlspecialchars($appt['service_name']); ?></span>
                                <?php
                                $badge_class = 'ok';
                                if ($appt['status'] === 'pending') $badge_class = 'low';
                                if ($appt['status'] === 'cancelled') $badge_class = 'low';
                                ?>
                                <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($appt['status']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="panel">
                    <h4>Low Stock Alerts</h4>
                    <?php if (empty($low_stock_items)): ?>
                        <p style="color: var(--text-muted); font-size: 13px;">All stock levels are OK.</p>
                    <?php else: ?>
                        <?php foreach ($low_stock_items as $item): ?>
                            <div class="panel-list-item">
                                <span><?php echo htmlspecialchars($item['item_name']); ?></span>
                                <span class="badge low"><?php echo $item['quantity'] . ' ' . htmlspecialchars($item['unit']); ?> left</span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>
</div>

</body>
</html>