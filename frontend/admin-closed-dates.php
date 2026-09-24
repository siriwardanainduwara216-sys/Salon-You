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

// ADD CLOSED DATE

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_closed_date'])) {
    $closed_date = trim($_POST['closed_date']);
    $reason = trim($_POST['reason']) ?: 'Poya Day';

    if (empty($closed_date)) {
        $error_msg = "Please select a date.";
    } else {
        $sql = "INSERT INTO salon_closed_dates (closed_date, reason) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $closed_date, $reason);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Closed date added successfully!";
        } else {
            $error_msg = "This date is already marked as closed, or another error occurred.";
        }
        mysqli_stmt_close($stmt);
    }
}

// DELETE CLOSED DATE

if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];
    $sql = "DELETE FROM salon_closed_dates WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $delete_id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Closed date removed.";
    }
    mysqli_stmt_close($stmt);
}

require_once __DIR__ . '/../backend/admin-closed-dates-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Closed Dates</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    :root {
        --bg-dark: #14151a; --card-bg: #1e1f26; --accent-purple: #8b5cf6;
        --accent-amber: #f59e0b; --text-main: #f1f1f3;
        --text-muted: #9a9aa5; --border-color: #2c2d35; --success-green: #22c55e; --danger-red: #ef4444;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', sans-serif; }
    body { background-color: var(--bg-dark); color: var(--text-main); }
    .admin-wrapper { display: flex; min-height: 100vh; }
    .sidebar { width: 250px; background-color: var(--card-bg); border-right: 1px solid var(--border-color); padding: 20px 0; flex-shrink: 0; }
    .sidebar-header { padding: 0 20px 20px 20px; border-bottom: 1px solid var(--border-color); margin-bottom: 15px; }
    .sidebar-header h2 { color: var(--accent-purple); font-size: 20px; }
    .sidebar-header p { color: var(--text-muted); font-size: 12px; margin-top: 4px; }
    .sidebar-menu { list-style: none; }
    .sidebar-menu li a { display: flex; align-items: center; gap: 12px; padding: 12px 20px; color: var(--text-muted); text-decoration: none; font-size: 14px; border-left: 3px solid transparent; }
    .sidebar-menu li a:hover, .sidebar-menu li.active a { background-color: rgba(139, 92, 246, 0.1); color: var(--text-main); border-left: 3px solid var(--accent-purple); }
    .sidebar-menu li a i { width: 18px; text-align: center; }
    .main-content { flex: 1; padding: 25px 30px; overflow-x: hidden; }
    .topbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .page-header { margin-bottom: 20px; }
    .page-header h2 { font-size: 22px; }

    .btn { display: inline-flex; align-items: center; gap: 8px; background: var(--accent-purple); color: white; border: none; padding: 10px 18px; border-radius: 8px; font-size: 14px; cursor: pointer; text-decoration: none; }
    .btn:hover { opacity: 0.9; }

    .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; }
    .alert-success { background: rgba(34,197,94,0.15); color: var(--success-green); border: 1px solid var(--success-green); }
    .alert-error { background: rgba(239,68,68,0.15); color: var(--danger-red); border: 1px solid var(--danger-red); }

    .panel { background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 10px; padding: 20px; margin-bottom: 20px; }
    .panel h4 { font-size: 15px; margin-bottom: 15px; }

    .form-grid { display: grid; grid-template-columns: 1fr 1fr auto; gap: 15px; align-items: end; }
    .form-group { display: flex; flex-direction: column; gap: 6px; }
    .form-group label { font-size: 13px; color: var(--text-muted); }
    .form-group input {
        background: var(--bg-dark); border: 1px solid var(--border-color); color: var(--text-main);
        padding: 10px 12px; border-radius: 6px; font-size: 14px;
    }

    table { width: 100%; border-collapse: collapse; }
    thead th { text-align: left; font-size: 12px; text-transform: uppercase; color: var(--text-muted); padding: 12px; border-bottom: 1px solid var(--border-color); }
    tbody td { padding: 12px; font-size: 14px; border-bottom: 1px solid var(--border-color); }
    .table-wrap { overflow-x: auto; }
    .delete-link { color: var(--danger-red); text-decoration: none; }

    @media (max-width: 900px) {
        .sidebar { width: 70px; }
        .sidebar-header p, .sidebar-menu li a span { display: none; }
        .main-content { padding: 20px 15px; }
        .form-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 600px) {
        .admin-wrapper { flex-direction: column; }
        .sidebar { width: 100%; border-right: none; border-bottom: 1px solid var(--border-color); padding: 10px 0; }
        .sidebar-header { display: none; }
        .sidebar-menu { display: flex; overflow-x: auto; gap: 5px; padding: 0 10px; }
        .sidebar-menu li a { flex-direction: column; gap: 4px; padding: 8px 12px; font-size: 10px; white-space: nowrap; border-left: none; border-bottom: 3px solid transparent; }
        .sidebar-menu li a:hover, .sidebar-menu li.active a { border-left: none; border-bottom: 3px solid var(--accent-purple); }
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
            <li><a href="admin-inventory.php"><i class="fas fa-box"></i><span> Inventory</span></a></li>
            <li><a href="admin-billing.php"><i class="fas fa-money-bill"></i><span> Billing & Payments</span></a></li>
            <li><a href="admin-reports.php"><i class="fas fa-file-invoice-dollar"></i><span> Reports</span></a></li>
            <li class="active"><a href="admin-closed-dates.php"><i class="fas fa-calendar-times"></i><span> Closed Dates</span></a></li>
             
            <li><a href="admin-notifications.php"><i class="fas fa-bell"></i><span> Notifications</span></a></li>
            <li><a href="../backend/logout.php"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="topbar">
            <h3>Welcome, <?php echo htmlspecialchars($admin_name); ?></h3>
        </div>

        <div class="page-header"><h2>Salon Closed Dates</h2></div>
        <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 20px;">
            Mark Poya days, public holidays, or any date the salon will be closed. Customers won't be able to book appointments on these dates.
        </p>

        <?php if ($success_msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div><?php endif; ?>
        <?php if ($error_msg): ?><div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div><?php endif; ?>

        <!-- Add Closed Date -->
        <div class="panel">
            <h4>Add a Closed Date</h4>
            <form method="POST" action="admin-closed-dates.php">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" name="closed_date" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Reason</label>
                        <input type="text" name="reason" placeholder="e.g. Poya Day, New Year" value="Poya Day">
                    </div>
                    <button type="submit" name="add_closed_date" class="btn"><i class="fas fa-plus"></i> Add</button>
                </div>
            </form>
        </div>

        <!-- Closed Dates List -->
        <div class="panel">
            <h4>Upcoming Closed Dates (<?php echo count($closed_dates_list); ?>)</h4>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Date</th><th>Day</th><th>Reason</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($closed_dates_list)): ?>
                            <tr><td colspan="4" style="color: var(--text-muted); text-align:center;">No closed dates scheduled.</td></tr>
                        <?php else: ?>
                            <?php foreach ($closed_dates_list as $cd): ?>
                                <tr>
                                    <td><?php echo $cd['closed_date']; ?></td>
                                    <td><?php echo date('l', strtotime($cd['closed_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($cd['reason']); ?></td>
                                    <td>
                                        <a href="admin-closed-dates.php?delete_id=<?php echo $cd['id']; ?>" class="delete-link"
                                           onclick="return confirm('Remove this closed date?');">
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