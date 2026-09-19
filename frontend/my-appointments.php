<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];

require_once __DIR__ . '/../backend/config.php';
global $conn;

$success_msg = isset($_GET['cancelled']) ? 'Appointment cancelled successfully.' : '';

require_once __DIR__ . '/my-appointments-data.php';

// Statuses that are still allowed to be cancelled by the customer
$cancellable_statuses = ['pending', 'confirmed'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments - Salon You</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="frontend-css/services.css">
    <link rel="stylesheet" href="frontend-css/style.css">
    <link rel="stylesheet" href="frontend-css/my-appointments.css">
</head>
<body class="luxury-theme">

    <header class="site-header">
        <a href="index.php" class="logo-container">
            <img src="../logo/logo.png" alt="Salon You Logo" class="site-logo">
            <span class="logo-text">SALON YOU</span>
        </a>
        <nav class="main-nav">
            <a href="index.php" class="nav-link">Home</a>
            <a href="services.php" class="nav-link">Services</a>
            <a href="index.php#gallery" class="nav-link">Gallery</a>
            <a href="about.php" class="nav-link">About Us</a>
            <a href="index.php#contact" class="nav-link">Contact</a>
        </nav>
        <div class="header-actions">
            <a href="my-appointments.php" class="btn-register" style="margin-right:10px;">My Appointments</a>
            <a href="../backend/logout.php" class="btn-register">Logout</a>
        </div>
    </header>

    <div class="appt-page-wrap">
        <h1>My Appointments</h1>
        <p class="subtitle">Welcome, <?php echo htmlspecialchars($user_name); ?> - here's your booking history.</p>

        <?php if ($success_msg): ?>
            <div class="alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
        <?php endif; ?>

        <?php if (empty($my_appointments)): ?>
            <div class="empty-state">
                <p>You haven't booked any appointments yet.</p>
                <p><a href="services.php">Browse our services →</a></p>
            </div>
        <?php else: ?>
            <?php foreach ($my_appointments as $appt): ?>
                <div class="appt-card">
                    <div class="appt-info">
                        <h3><?php echo htmlspecialchars($appt['service_name']); ?></h3>
                        <p>Stylist: <strong><?php echo $appt['staff_name'] ? htmlspecialchars($appt['staff_name']) : 'Unassigned'; ?></strong></p>
                        <p><?php echo $appt['appointment_date']; ?> at <?php echo date('h:i A', strtotime($appt['appointment_time'])); ?></p>
                        <p>Price: Rs. <?php echo number_format($appt['price'], 2); ?></p>
                    </div>
                    <div class="appt-actions">
                        <span class="appt-status <?php echo $appt['status']; ?>"><?php echo $appt['status']; ?></span>
                        <?php if (in_array($appt['status'], $cancellable_statuses)): ?>
                            <a href="cancel-appointment.php?id=<?php echo $appt['id']; ?>" class="btn-cancel-appt"
                               onclick="return confirm('Are you sure you want to cancel this appointment?');">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</body>
</html>