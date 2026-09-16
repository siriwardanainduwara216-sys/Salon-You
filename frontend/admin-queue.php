<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if (!in_array($_SESSION['user_role'], ['admin', 'employee'])) {
    header("Location: index.php");
    exit();
}

$user_name = $_SESSION['user_name'];

require_once __DIR__ . '/../backend/config.php';
global $conn;

$success_msg = '';

// UPDATE QUEUE STATUS

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_queue_status'])) {
    $appointment_id = (int) $_POST['appointment_id'];
    $new_status = $_POST['new_status'];

    if (in_array($new_status, ['waiting', 'in_progress', 'done'])) {
        $sql = "UPDATE appointments SET queue_status = ? WHERE id = ? AND appointment_date = CURDATE()";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $new_status, $appointment_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        $success_msg = "Queue updated.";
    }
}

require_once __DIR__ . '/../backend/admin-queue-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Today's Queue</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
<link rel="stylesheet" href="frontend-css/admin-queue.css">
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
            <li class="active"><a href="admin-queue.php"><i class="fas fa-list-ol"></i><span> Today's Queue</span></a></li>
            <li><a href="admin-services.php"><i class="fas fa-cut"></i><span> Services</span></a></li>
            <li><a href="admin-inventory.php"><i class="fas fa-box"></i><span> Inventory</span></a></li>
            <li><a href="admin-billing.php"><i class="fas fa-money-bill"></i><span> Billing & Payments</span></a></li>
            <li><a href="admin-reports.php"><i class="fas fa-file-invoice-dollar"></i><span> Reports</span></a></li>
            <li><a href="admin-closed-dates.php"><i class="fas fa-calendar-times"></i><span> Closed Dates</span></a></li>
            <li><a href="admin-notifications.php"><i class="fas fa-bell"></i><span> Notifications</span></a></li>
            <li><a href="../backend/logout.php"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="topbar">
            <h3>Welcome, <?php echo htmlspecialchars($user_name); ?></h3>
        </div>

        <div class="page-header"><h2>Today's Queue - <?php echo date('l, F j, Y'); ?></h2></div>

        <?php if ($success_msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div><?php endif; ?>

        <?php if (empty($queue_by_staff)): ?>
            <p class="empty-msg">No appointments scheduled for today.</p>
        <?php else: ?>
            <?php foreach ($queue_by_staff as $staff_id => $group): ?>
                <div class="staff-queue-panel">
                    <h4><i class="fas fa-user" style="color: var(--accent-purple);"></i> <?php echo htmlspecialchars($group['staff_name']); ?>'s Queue</h4>

                    <?php $position = 1; ?>
                    <?php foreach ($group['appointments'] as $appt): ?>
                        <div class="queue-row <?php echo $appt['queue_status']; ?>">
                            <div class="queue-num">#<?php echo $position++; ?></div>
                            <div class="queue-info">
                                <h5><?php echo htmlspecialchars($appt['customer_name']); ?> - <?php echo htmlspecialchars($appt['service_name']); ?></h5>
                                <p><?php echo date('h:i A', strtotime($appt['appointment_time'])); ?> | <?php echo htmlspecialchars($appt['customer_phone']); ?></p>
                            </div>
                            <div class="queue-actions">
                                <span class="status-pill <?php echo $appt['queue_status']; ?>"><?php echo str_replace('_', ' ', $appt['queue_status']); ?></span>
                                <?php if ($appt['queue_status'] === 'waiting'): ?>
                                    <form method="POST" action="admin-queue.php" style="display:inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                                        <input type="hidden" name="new_status" value="in_progress">
                                        <button type="submit" name="update_queue_status" class="btn-start"><i class="fas fa-play"></i> Start</button>
                                    </form>
                                <?php elseif ($appt['queue_status'] === 'in_progress'): ?>
                                    <form method="POST" action="admin-queue.php" style="display:inline;">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                                        <input type="hidden" name="new_status" value="done">
                                        <button type="submit" name="update_queue_status" class="btn-done"><i class="fas fa-check"></i> Done</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>
</body>
</html>