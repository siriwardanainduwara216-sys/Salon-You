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

$customer_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($customer_id <= 0) {
    header("Location: admin-customers.php");
    exit();
}

// ---- Loyalty Points Update ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_points'])) {
    $new_points = (int) $_POST['loyalty_points'];
    $sql = "UPDATE users SET loyalty_points = ? WHERE id = ? AND role = 'customer'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $new_points, $customer_id);
    if (mysqli_stmt_execute($stmt)) {
        $success_msg = "Loyalty points updated successfully!";
    } else {
        $error_msg = "Error ekak: " . mysqli_error($conn);
    }
    mysqli_stmt_close($stmt);
}

// Customer basic details
$sql = "SELECT id, name, email, phone, status, loyalty_points, created_at FROM users WHERE id = ? AND role = 'customer'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$customer = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$customer) {
    header("Location: admin-customers.php");
    exit();
}

//Booking History 
$booking_history = [];
$sql = "SELECT a.appointment_date, a.appointment_time, a.status, s.service_name, s.price
        FROM appointments a
        JOIN services s ON a.service_id = s.id
        WHERE a.user_id = ?
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $booking_history[] = $row;
}
mysqli_stmt_close($stmt);

// Reviews / Feedback 
$reviews = [];
$sql = "SELECT rating, comment, created_at FROM reviews WHERE user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $reviews[] = $row;
}
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Customer Profile</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="frontend-css/customer-view.css">
</head>
<body>
<div class="admin-wrapper">
    <div class="sidebar">
        <div class="sidebar-header"><h2>Salon You</h2><p>Admin Panel</p></div>
        <ul class="sidebar-menu">
            <li><a href="admin-dashboard.php"><i class="fas fa-chart-line"></i><span> Dashboard</span></a></li>
            <li><a href="admin-staff.php"><i class="fas fa-users"></i><span> Staff Management</span></a></li>
            <li class="active"><a href="admin-customers.php"><i class="fas fa-user-friends"></i><span> Customer Management</span></a></li>
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
            <h2>Customer Profile</h2>
            <a href="admin-customers.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
        </div>

        <?php if ($success_msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div><?php endif; ?>
        <?php if ($error_msg): ?><div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div><?php endif; ?>

        
        <div class="panel">
            <h4>Basic Details</h4>
            <div class="profile-grid">
                <div class="info-box"><label>Name</label><span><?php echo htmlspecialchars($customer['name']); ?></span></div>
                <div class="info-box"><label>Email</label><span><?php echo htmlspecialchars($customer['email']); ?></span></div>
                <div class="info-box"><label>Phone</label><span><?php echo htmlspecialchars($customer['phone']); ?></span></div>
                <div class="info-box"><label>Status</label><span><?php echo ucfirst($customer['status']); ?></span></div>
            </div>

            <form method="POST" action="admin-customer-view.php?id=<?php echo $customer['id']; ?>">
                <div class="points-form">
                    <div>
                        <label>Loyalty Points</label>
                        <input type="number" name="loyalty_points" value="<?php echo $customer['loyalty_points']; ?>" min="0">
                    </div>
                    <button type="submit" name="update_points" class="btn"><i class="fas fa-save"></i> Update Points</button>
                </div>
            </form>
        </div>

        <!--Booking History -->
        <div class="panel">
            <h4>Booking History (<?php echo count($booking_history); ?>)</h4>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr><th>Date</th><th>Time</th><th>Service</th><th>Price</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($booking_history)): ?>
                            <tr><td colspan="5" style="color: var(--text-muted); text-align:center;">No bookings found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($booking_history as $b): ?>
                                <tr>
                                    <td><?php echo $b['appointment_date']; ?></td>
                                    <td><?php echo date('h:i A', strtotime($b['appointment_time'])); ?></td>
                                    <td><?php echo htmlspecialchars($b['service_name']); ?></td>
                                    <td>Rs. <?php echo number_format($b['price'], 2); ?></td>
                                    <td><span class="badge <?php echo $b['status']; ?>"><?php echo ucfirst($b['status']); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reviews -->
        <div class="panel">
            <h4>Feedback & Reviews (<?php echo count($reviews); ?>)</h4>
            <?php if (empty($reviews)): ?>
                <p style="color: var(--text-muted); font-size: 14px;">No reviews found.</p>
            <?php else: ?>
                <?php foreach ($reviews as $r): ?>
                    <div class="review-item">
                        <div class="review-stars">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $r['rating'] ? '★' : '☆';
                            }
                            ?>
                        </div>
                        <div class="review-comment"><?php echo htmlspecialchars($r['comment']); ?></div>
                        <div class="review-date"><?php echo date('Y-m-d', strtotime($r['created_at'])); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</div>
</body>
</html>