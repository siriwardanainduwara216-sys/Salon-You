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

require_once __DIR__ . '/admin-reports-data.php';

// Find the highest monthly revenue value, used to scale the bar chart widths
$max_month_revenue = 0;
foreach ($monthly_revenue as $m) {
    if ($m['total'] > $max_month_revenue) {
        $max_month_revenue = $m['total'];
    }
}

$max_service_bookings = 0;
foreach ($top_services as $s) {
    if ($s['total_bookings'] > $max_service_bookings) {
        $max_service_bookings = $s['total_bookings'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Reports</title>
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
<link rel="stylesheet" href="fr<?php
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

require_once __DIR__ . '/admin-reports-data.php';

// Find the highest monthly revenue value, used to scale the bar chart widths
$max_month_revenue = 0;
foreach ($monthly_revenue as $m) {
    if ($m['total'] > $max_month_revenue) {
        $max_month_revenue = $m['total'];
    }
}

$max_service_bookings = 0;
foreach ($top_services as $s) {
    if ($s['total_bookings'] > $max_service_bookings) {
        $max_service_bookings = $s['total_bookings'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Reports</title>
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
<link rel="stylesheet" href="frontend-css/reports.css">
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
            <h2>Reports</h2>
            <div style="display:flex; gap:10px;">
                <a href="admin-detailed-report.php" class="btn"><i class="fas fa-list-alt"></i> Detailed Service Report</a>
                <a href="admin-staff-performance.php" class="btn"><i class="fas fa-users-cog"></i> Staff Performance</a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="cards-row">
            <div class="card green">
                <h4>Total Revenue (All Time)</h4>
                <p class="card-number">Rs. <?php echo number_format($grand_total_revenue, 2); ?></p>
            </div>
            <div class="card blue">
                <h4>Total Customers</h4>
                <p class="card-number"><?php echo $total_customers; ?></p>
            </div>
        </div>

        <div class="section-row">
            <!-- Monthly Revenue Trend -->
            <div class="panel">
                <h4>Revenue Trend (Last 6 Months)</h4>
                <?php if (empty($monthly_revenue)): ?>
                    <p style="color: var(--text-muted); font-size: 14px;">No completed appointments yet.</p>
                <?php else: ?>
                    <?php foreach ($monthly_revenue as $m): ?>
                        <?php $pct = $max_month_revenue > 0 ? ($m['total'] / $max_month_revenue) * 100 : 0; ?>
                        <div class="bar-row">
                            <div class="bar-label">
                                <span><?php echo date('M Y', strtotime($m['month_label'] . '-01')); ?></span>
                                <span class="value">Rs. <?php echo number_format($m['total'], 2); ?></span>
                            </div>
                            <div class="bar-track">
                                <div class="bar-fill" style="width: <?php echo $pct; ?>%;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Appointment Status Breakdown -->
            <div class="panel">
                <h4>Appointment Status Breakdown (<?php echo $total_appointments_all; ?> total)</h4>
                <div class="status-grid">
                    <div class="status-pill pending">
                        <div class="num"><?php echo $status_breakdown['pending']; ?></div>
                        <div class="label">Pending</div>
                    </div>
                    <div class="status-pill confirmed">
                        <div class="num"><?php echo $status_breakdown['confirmed']; ?></div>
                        <div class="label">Confirmed</div>
                    </div>
                    <div class="status-pill completed">
                        <div class="num"><?php echo $status_breakdown['completed']; ?></div>
                        <div class="label">Completed</div>
                    </div>
                    <div class="status-pill cancelled">
                        <div class="num"><?php echo $status_breakdown['cancelled']; ?></div>
                        <div class="label">Cancelled</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Services -->
        <div class="panel">
            <h4>Top 5 Services (by Completed Bookings)</h4>
            <?php if (empty($top_services)): ?>
                <p style="color: var(--text-muted); font-size: 14px;">No completed appointments yet.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>Service</th><th>Bookings</th><th>Revenue</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_services as $s): ?>
                                <?php $pct = $max_service_bookings > 0 ? ($s['total_bookings'] / $max_service_bookings) * 100 : 0; ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($s['service_name']); ?></td>
                                    <td><?php echo $s['total_bookings']; ?></td>
                                    <td>Rs. <?php echo number_format($s['total_revenue'], 2); ?></td>
                                    <td style="width: 150px;">
                                        <div class="bar-track">
                                            <div class="bar-fill amber" style="width: <?php echo $pct; ?>%;"></div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Customer Growth -->
        <div class="panel">
            <h4>New Customer Signups (Last 6 Months)</h4>
            <?php if (empty($customer_growth)): ?>
                <p style="color: var(--text-muted); font-size: 14px;">No new customers in this period.</p>
            <?php else: ?>
                <?php
                $max_growth = 0;
                foreach ($customer_growth as $g) { if ($g['total'] > $max_growth) $max_growth = $g['total']; }
                ?>
                <?php foreach ($customer_growth as $g): ?>
                    <?php $pct = $max_growth > 0 ? ($g['total'] / $max_growth) * 100 : 0; ?>
                    <div class="bar-row">
                        <div class="bar-label">
                            <span><?php echo date('M Y', strtotime($g['month_label'] . '-01')); ?></span>
                            <span class="value"><?php echo $g['total']; ?> new customers</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill blue" style="width: <?php echo $pct; ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>
</body>
</html>">
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
            <h2>Reports</h2>
            <div style="display:flex; gap:10px;">
                <a href="admin-detailed-report.php" class="btn"><i class="fas fa-list-alt"></i> Detailed Service Report</a>
                <a href="admin-staff-performance.php" class="btn"><i class="fas fa-users-cog"></i> Staff Performance</a>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="cards-row">
            <div class="card green">
                <h4>Total Revenue (All Time)</h4>
                <p class="card-number">Rs. <?php echo number_format($grand_total_revenue, 2); ?></p>
            </div>
            <div class="card blue">
                <h4>Total Customers</h4>
                <p class="card-number"><?php echo $total_customers; ?></p>
            </div>
        </div>

        <div class="section-row">
            <!-- Monthly Revenue Trend -->
            <div class="panel">
                <h4>Revenue Trend (Last 6 Months)</h4>
                <?php if (empty($monthly_revenue)): ?>
                    <p style="color: var(--text-muted); font-size: 14px;">No completed appointments yet.</p>
                <?php else: ?>
                    <?php foreach ($monthly_revenue as $m): ?>
                        <?php $pct = $max_month_revenue > 0 ? ($m['total'] / $max_month_revenue) * 100 : 0; ?>
                        <div class="bar-row">
                            <div class="bar-label">
                                <span><?php echo date('M Y', strtotime($m['month_label'] . '-01')); ?></span>
                                <span class="value">Rs. <?php echo number_format($m['total'], 2); ?></span>
                            </div>
                            <div class="bar-track">
                                <div class="bar-fill" style="width: <?php echo $pct; ?>%;"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Appointment Status Breakdown -->
            <div class="panel">
                <h4>Appointment Status Breakdown (<?php echo $total_appointments_all; ?> total)</h4>
                <div class="status-grid">
                    <div class="status-pill pending">
                        <div class="num"><?php echo $status_breakdown['pending']; ?></div>
                        <div class="label">Pending</div>
                    </div>
                    <div class="status-pill confirmed">
                        <div class="num"><?php echo $status_breakdown['confirmed']; ?></div>
                        <div class="label">Confirmed</div>
                    </div>
                    <div class="status-pill completed">
                        <div class="num"><?php echo $status_breakdown['completed']; ?></div>
                        <div class="label">Completed</div>
                    </div>
                    <div class="status-pill cancelled">
                        <div class="num"><?php echo $status_breakdown['cancelled']; ?></div>
                        <div class="label">Cancelled</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Services -->
        <div class="panel">
            <h4>Top 5 Services (by Completed Bookings)</h4>
            <?php if (empty($top_services)): ?>
                <p style="color: var(--text-muted); font-size: 14px;">No completed appointments yet.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr><th>Service</th><th>Bookings</th><th>Revenue</th><th></th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_services as $s): ?>
                                <?php $pct = $max_service_bookings > 0 ? ($s['total_bookings'] / $max_service_bookings) * 100 : 0; ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($s['service_name']); ?></td>
                                    <td><?php echo $s['total_bookings']; ?></td>
                                    <td>Rs. <?php echo number_format($s['total_revenue'], 2); ?></td>
                                    <td style="width: 150px;">
                                        <div class="bar-track">
                                            <div class="bar-fill amber" style="width: <?php echo $pct; ?>%;"></div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

        <!-- Customer Growth -->
        <div class="panel">
            <h4>New Customer Signups (Last 6 Months)</h4>
            <?php if (empty($customer_growth)): ?>
                <p style="color: var(--text-muted); font-size: 14px;">No new customers in this period.</p>
            <?php else: ?>
                <?php
                $max_growth = 0;
                foreach ($customer_growth as $g) { if ($g['total'] > $max_growth) $max_growth = $g['total']; }
                ?>
                <?php foreach ($customer_growth as $g): ?>
                    <?php $pct = $max_growth > 0 ? ($g['total'] / $max_growth) * 100 : 0; ?>
                    <div class="bar-row">
                        <div class="bar-label">
                            <span><?php echo date('M Y', strtotime($g['month_label'] . '-01')); ?></span>
                            <span class="value"><?php echo $g['total']; ?> new customers</span>
                        </div>
                        <div class="bar-track">
                            <div class="bar-fill blue" style="width: <?php echo $pct; ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>
</body>
</html>