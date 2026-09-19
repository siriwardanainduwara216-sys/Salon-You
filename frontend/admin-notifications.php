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

require_once __DIR__ . '/admin-notifications-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Notifications</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
<link rel="stylesheet" href="frontend-css/notification.css">
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
            <li><a href="admin-reports.php"><i class="fas fa-file-invoice-dollar"></i><span> Reports</span></a></li>
            <li class="active"><a href="admin-notifications.php"><i class="fas fa-bell"></i><span> Notifications</span>
                <?php if ($total_notifications > 0): ?><span class="nav-badge"><?php echo $total_notifications; ?></span><?php endif; ?>
            </a></li>
            <li><a href="../backend/logout.php"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="topbar">
            <h3>Welcome, <?php echo htmlspecialchars($admin_name); ?></h3>
        </div>

        <div class="page-header"><h2>Notifications</h2></div>

        <!-- Low Stock Alerts -->
        <div class="panel">
            <div class="panel-header">
                <h4><i class="fas fa-box" style="color: var(--danger-red);"></i> Low Stock Alerts</h4>
                <span class="count-badge"><?php echo count($low_stock_items); ?></span>
            </div>
            <?php if (empty($low_stock_items)): ?>
                <p class="empty-msg">All inventory items are well stocked.</p>
            <?php else: ?>
                <?php foreach ($low_stock_items as $item): ?>
                    <div class="notif-item urgent">
                        <div class="notif-text">
                            <strong><?php echo htmlspecialchars($item['item_name']); ?></strong> is running low
                            <div class="sub"><?php echo $item['quantity']; ?> <?php echo htmlspecialchars($item['unit']); ?> left (threshold: <?php echo $item['low_stock_threshold']; ?>)</div>
                        </div>
                        <div class="notif-action"><a href="admin-inventory.php">Restock →</a></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pending Appointments -->
        <div class="panel">
            <div class="panel-header">
                <h4><i class="fas fa-calendar-check" style="color: var(--accent-amber);"></i> Pending Appointments</h4>
                <span class="count-badge"><?php echo count($pending_appointments); ?></span>
            </div>
            <?php if (empty($pending_appointments)): ?>
                <p class="empty-msg">No appointments awaiting confirmation.</p>
            <?php else: ?>
                <?php foreach ($pending_appointments as $appt): ?>
                    <div class="notif-item">
                        <div class="notif-text">
                            <strong><?php echo htmlspecialchars($appt['customer_name']); ?></strong> booked <?php echo htmlspecialchars($appt['service_name']); ?>
                            <div class="sub"><?php echo $appt['appointment_date']; ?> at <?php echo date('h:i A', strtotime($appt['appointment_time'])); ?></div>
                        </div>
                        <div class="notif-action"><a href="admin-appointments.php?status=pending">Confirm →</a></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pending Payments -->
        <div class="panel">
            <div class="panel-header">
                <h4><i class="fas fa-money-bill" style="color: var(--accent-amber);"></i> Pending Payments</h4>
                <span class="count-badge"><?php echo count($pending_payments); ?></span>
            </div>
            <?php if (empty($pending_payments)): ?>
                <p class="empty-msg">No pending payments.</p>
            <?php else: ?>
                <?php foreach ($pending_payments as $pay): ?>
                    <div class="notif-item">
                        <div class="notif-text">
                            <strong><?php echo htmlspecialchars($pay['customer_name']); ?></strong> - Rs. <?php echo number_format($pay['amount'], 2); ?> pending for <?php echo htmlspecialchars($pay['service_name']); ?>
                            <div class="sub">Recorded on <?php echo date('Y-m-d', strtotime($pay['payment_date'])); ?></div>
                        </div>
                        <div class="notif-action"><a href="admin-billing.php">View →</a></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Today's Schedule -->
        <div class="panel">
            <div class="panel-header">
                <h4><i class="fas fa-clock" style="color: var(--accent-blue);"></i> Today's Schedule</h4>
                <span class="count-badge"><?php echo count($today_appointments); ?></span>
            </div>
            <?php if (empty($today_appointments)): ?>
                <p class="empty-msg">No appointments scheduled for today.</p>
            <?php else: ?>
                <?php foreach ($today_appointments as $appt): ?>
                    <div class="notif-item info">
                        <div class="notif-text">
                            <strong><?php echo date('h:i A', strtotime($appt['appointment_time'])); ?></strong> - <?php echo htmlspecialchars($appt['customer_name']); ?> (<?php echo htmlspecialchars($appt['service_name']); ?>)
                        </div>
                        <div class="notif-action"><span class="badge <?php echo $appt['status']; ?>"><?php echo ucfirst($appt['status']); ?></span></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>
</body>
</html>