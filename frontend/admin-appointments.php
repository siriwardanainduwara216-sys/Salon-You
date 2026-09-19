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


// UPDATE APPOINTMENT STATUS

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $appointment_id = (int) $_POST['appointment_id'];
    $new_status = $_POST['new_status'];

    $allowed_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
    if (!in_array($new_status, $allowed_statuses)) {
        $error_msg = "Invalid status value.";
    } else {
        $sql = "UPDATE appointments SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $new_status, $appointment_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Appointment status updated successfully!";

            // Send a confirmation email to the customer when the appointment is confirmed
            if ($new_status === 'confirmed') {
                $sql = "SELECT u.name AS customer_name, u.email AS customer_email,
                               s.service_name, st.name AS staff_name,
                               a.appointment_date, a.appointment_time
                        FROM appointments a
                        JOIN users u ON a.user_id = u.id
                        JOIN services s ON a.service_id = s.id
                        LEFT JOIN users st ON a.staff_id = st.id
                        WHERE a.id = ?";
                $info_stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($info_stmt, "i", $appointment_id);
                mysqli_stmt_execute($info_stmt);
                $info = mysqli_fetch_assoc(mysqli_stmt_get_result($info_stmt));
                mysqli_stmt_close($info_stmt);

                if ($info) {
                    require_once __DIR__ . '/../backend/mailer.php';
                    $staff_name = $info['staff_name'] ?? 'Our Stylist';
                    send_appointment_confirmation_email(
                        $info['customer_email'],
                        $info['customer_name'],
                        $info['service_name'],
                        $staff_name,
                        $info['appointment_date'],
                        $info['appointment_time']
                    );
                }
            }
        } else {
            $error_msg = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}


// ASSIGN STAFF TO APPOINTMENT

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_staff'])) {
    $appointment_id = (int) $_POST['appointment_id'];
    $staff_id = isset($_POST['staff_id']) && $_POST['staff_id'] !== '' ? (int) $_POST['staff_id'] : null;

    $sql = "UPDATE appointments SET staff_id = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $staff_id, $appointment_id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Staff assigned successfully!";
    } else {
        $error_msg = "Error: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// ---- Active status filter (from URL, defaults to 'all') ----
$status_filter = $_GET['status'] ?? 'all';
$allowed_filters = ['all', 'pending', 'confirmed', 'completed', 'cancelled'];
if (!in_array($status_filter, $allowed_filters)) {
    $status_filter = 'all';
}

require_once __DIR__ . '/admin-appointments-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Appointment Tracking</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
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
            <li class="active"><a href="admin-appointments.php"><i class="fas fa-calendar-check"></i><span> Appointments</span></a></li>
            <li><a href="admin-services.php"><i class="fas fa-cut"></i><span> Services</span></a></li>
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

        <div class="page-header"><h2>Appointment Tracking</h2></div>

        <?php if ($success_msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div><?php endif; ?>
        <?php if ($error_msg): ?><div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div><?php endif; ?>

        <!-- Status Filter Tabs -->
        <div class="filter-tabs">
            <a href="admin-appointments.php?status=all" class="<?php echo $status_filter === 'all' ? 'active' : ''; ?>">
                All <span class="count">(<?php echo $status_counts['all']; ?>)</span>
            </a>
            <a href="admin-appointments.php?status=pending" class="<?php echo $status_filter === 'pending' ? 'active' : ''; ?>">
                Pending <span class="count">(<?php echo $status_counts['pending']; ?>)</span>
            </a>
            <a href="admin-appointments.php?status=confirmed" class="<?php echo $status_filter === 'confirmed' ? 'active' : ''; ?>">
                Confirmed <span class="count">(<?php echo $status_counts['confirmed']; ?>)</span>
            </a>
            <a href="admin-appointments.php?status=completed" class="<?php echo $status_filter === 'completed' ? 'active' : ''; ?>">
                Completed <span class="count">(<?php echo $status_counts['completed']; ?>)</span>
            </a>
            <a href="admin-appointments.php?status=cancelled" class="<?php echo $status_filter === 'cancelled' ? 'active' : ''; ?>">
                Cancelled <span class="count">(<?php echo $status_counts['cancelled']; ?>)</span>
            </a>
        </div>

        <div class="panel">
            <h4>Appointments (<?php echo count($appointments_list); ?>)</h4>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Service</th>
                            <th>Price</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Staff</th>
                            <th>Status</th>
                            <th>Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($appointments_list)): ?>
                            <tr><td colspan="9" style="color: var(--text-muted); text-align:center;">No appointments found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($appointments_list as $appt): ?>
                                <tr id="appt-<?php echo $appt['id']; ?>">
                                    <td><?php echo htmlspecialchars($appt['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appt['customer_phone']); ?></td>
                                    <td><?php echo htmlspecialchars($appt['service_name']); ?></td>
                                    <td>Rs. <?php echo number_format($appt['price'], 2); ?></td>
                                    <td><?php echo $appt['appointment_date']; ?></td>
                                    <td><?php echo date('h:i A', strtotime($appt['appointment_time'])); ?></td>
                                    <td>
                                        <?php echo $appt['staff_name'] ? htmlspecialchars($appt['staff_name']) : '<span style="color: var(--text-muted);">Unassigned</span>'; ?>
                                        <button type="button" onclick="toggleStaffAssign(<?php echo $appt['id']; ?>)" style="background:none; border:none; color:var(--accent-blue); cursor:pointer; font-size:12px; margin-left:4px;">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                    <td><span class="badge <?php echo $appt['status']; ?>"><?php echo ucfirst($appt['status']); ?></span></td>
                                    <td>
                                        <form method="POST" action="admin-appointments.php?status=<?php echo $status_filter; ?>" class="status-form">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                                            <select name="new_status">
                                                <option value="pending" <?php echo $appt['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="confirmed" <?php echo $appt['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                                <option value="completed" <?php echo $appt['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                                <option value="cancelled" <?php echo $appt['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" name="update_status">Save</button>
                                        </form>
                                    </td>
                                </tr>
                                <!-- Staff Assignment Row -->
                                <tr class="staff-assign-row" id="staff-assign-<?php echo $appt['id']; ?>" style="display: none;">
                                    <td colspan="9">
                                        <form method="POST" action="admin-appointments.php?status=<?php echo $status_filter; ?>" style="display:flex; gap:10px; align-items:center;">
                                            <input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">
                                            <select name="staff_id">
                                                <option value="">-- Unassign --</option>
                                                <?php foreach ($all_staff as $staff): ?>
                                                    <option value="<?php echo $staff['id']; ?>" <?php echo $appt['staff_id'] == $staff['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($staff['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" name="assign_staff" style="background: var(--accent-blue); color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">Assign</button>
                                            <button type="button" onclick="toggleStaffAssign(<?php echo $appt['id']; ?>)" style="background: var(--border-color); color: var(--text-main); border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer;">Cancel</button>
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
<script>
function toggleStaffAssign(appointmentId) {
    const row = document.getElementById('staff-assign-' + appointmentId);
    if (row.style.display === 'none') {
        row.style.display = 'table-row';
    } else {
        row.style.display = 'none';
    }
}
</script>
</body>
</html>