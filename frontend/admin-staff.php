<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Login check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Admin role check
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$admin_name = $_SESSION['user_name'];

// 3. Database Connection
require_once __DIR__ . '/../backend/config.php';
global $conn;

$success_msg = '';
$error_msg = '';

// ============================================================
// ADD NEW STAFF (Form submit eka methanata enawa)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_staff'])) {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = $_POST['password'];
    $status   = $_POST['status'];

    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error_msg = "Please fill in all fields.";
    } else {
        // Email dupikeda kiyala check karanawa
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error_msg = "An account with this email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (name, email, phone, password, role, status, is_verified)
                           VALUES (?, ?, ?, ?, 'employee', ?, 1)";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "sssss", $name, $email, $phone, $hashed_password, $status);

            if (mysqli_stmt_execute($insert_stmt)) {
                $success_msg = "Staff member added successfully!";
            } else {
                $error_msg = "Error ekak: " . mysqli_error($conn);
            }
            mysqli_stmt_close($insert_stmt);
        }
        mysqli_stmt_close($check_stmt);
    }
}

// ============================================================
// DELETE STAFF
// ============================================================
if (isset($_GET['delete_id'])) {
    $delete_id = (int) $_GET['delete_id'];
    $delete_sql = "DELETE FROM users WHERE id = ? AND role = 'employee'";
    $delete_stmt = mysqli_prepare($conn, $delete_sql);
    mysqli_stmt_bind_param($delete_stmt, "i", $delete_id);

    if (mysqli_stmt_execute($delete_stmt)) {
        $success_msg = "Staff member deleted successfully.";
    } else {
        $error_msg = "Failed to delete: " . mysqli_error($conn);
    }
    mysqli_stmt_close($delete_stmt);
}

// 4. Staff list eka fetch karanawa
require_once __DIR__ . '/admin-staff-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Staff Management</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
<link rel="stylesheet" href="frontend-css/staff.css">
</head>
<body>

<div class="admin-wrapper">

    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Salon You</h2>
            <p>Admin Panel</p>
        </div>
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
            <span class="admin-name"><?php echo htmlspecialchars($admin_name); ?></span>
        </div>

        <div class="page-header">
            <h2>Staff Management</h2>
        </div>

        <?php if ($success_msg): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>
        <?php if ($error_msg): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>

        <!-- Add Staff Form -->
        <div class="panel">
            <h4>Add New Staff Member</h4>
            <form method="POST" action="admin-staff.php">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status">
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="add_staff" class="btn">
                            <i class="fas fa-plus"></i> Add Staff
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Staff List -->
        <div class="panel">
            <h4>All Staff (<?php echo count($staff_list); ?>)</h4>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($staff_list)): ?>
                            <tr><td colspan="6" style="color: var(--text-muted); text-align:center;">No staff members found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($staff_list as $staff): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($staff['name']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['email']); ?></td>
                                    <td><?php echo htmlspecialchars($staff['phone']); ?></td>
                                    <td><span class="badge <?php echo $staff['status']; ?>"><?php echo ucfirst($staff['status']); ?></span></td>
                                    <td><?php echo date('Y-m-d', strtotime($staff['created_at'])); ?></td>
                                    <td>
                                        <div class="action-icons">
                                            <a href="admin-staff-report.php?staff_id=<?php echo $staff['id']; ?>" title="View Performance Report"><i class="fas fa-chart-bar"></i></a>
                                            <a href="admin-staff-edit.php?id=<?php echo $staff['id']; ?>" title="Edit"><i class="fas fa-edit"></i></a>
                                            <a href="admin-staff.php?delete_id=<?php echo $staff['id']; ?>" class="delete-link" title="Delete"
                                               onclick="return confirm('Are you sure you want to delete this staff member? This cannot be undone.');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
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