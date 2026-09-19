<?php
session_start();

if (!isset($_SESSION['last_booking'])) {
    header("Location: frontend/index.php");
    exit();
}

$booking = $_SESSION['last_booking'];
unset($_SESSION['last_booking']); // show it once, then clear it
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Booking Confirmed - Salon You</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
<link rel="stylesheet" href="frontend/frontend-css/booking-success.css">
</head>
<body>
    <div class="success-box">
        <i class="fas fa-check-circle"></i>
        <h2>Booking Confirmed!</h2>
        <p class="subtitle">Your appointment has been submitted successfully.</p>

        <div class="detail-row"><span>Stylist</span><span><?php echo htmlspecialchars($booking['stylist']); ?></span></div>
        <div class="detail-row"><span>Service</span><span><?php echo htmlspecialchars($booking['service']); ?></span></div>
        <div class="detail-row"><span>Price</span><span>Rs. <?php echo number_format($booking['price'], 2); ?></span></div>
        <div class="detail-row"><span>Date</span><span><?php echo htmlspecialchars($booking['date']); ?></span></div>
        <div class="detail-row"><span>Time</span><span><?php echo date('h:i A', strtotime($booking['time'])); ?></span></div>

        <div class="status-badge"><i class="fas fa-clock"></i> Status: Pending Confirmation</div>
        <br>
        <a href="frontend/index.php" class="btn-home"><i class="fas fa-home"></i> Back to Home</a>
    </div>
</body>
</html>