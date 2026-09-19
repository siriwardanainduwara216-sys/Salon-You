<?php
// ============================================================
// admin-dashboard-data.php
// All dashboard query logic lives here.
// Meka admin-dashboard.php eken include karanawa.
// $conn variable eka already config.php eken set wela thiyenna one.
// ============================================================

// ---- Today's Bookings ----
$today_count = 0;
$sql = "SELECT COUNT(*) AS total FROM appointments WHERE appointment_date = CURDATE()";
if ($result = mysqli_query($conn, $sql)) {
    $today_count = mysqli_fetch_assoc($result)['total'];
}

// ---- This Week's Bookings ----
$week_count = 0;
$sql = "SELECT COUNT(*) AS total FROM appointments WHERE YEARWEEK(appointment_date, 1) = YEARWEEK(CURDATE(), 1)";
if ($result = mysqli_query($conn, $sql)) {
    $week_count = mysqli_fetch_assoc($result)['total'];
}

// ---- This Month's Bookings ----
$month_count = 0;
$sql = "SELECT COUNT(*) AS total FROM appointments WHERE MONTH(appointment_date) = MONTH(CURDATE()) AND YEAR(appointment_date) = YEAR(CURDATE())";
if ($result = mysqli_query($conn, $sql)) {
    $month_count = mysqli_fetch_assoc($result)['total'];
}

// ---- Total Revenue (completed appointments witharak) ----
$total_revenue = 0;
$sql = "SELECT SUM(s.price) AS total
        FROM appointments a
        JOIN services s ON a.service_id = s.id
        WHERE a.status = 'completed'";
if ($result = mysqli_query($conn, $sql)) {
    $row = mysqli_fetch_assoc($result);
    $total_revenue = $row['total'] ?? 0;
}

// ---- Recent Appointments (last 5) ----
$recent_appointments = [];
$sql = "SELECT u.name AS customer_name, s.service_name, a.status
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN services s ON a.service_id = s.id
        ORDER BY a.created_at DESC
        LIMIT 5";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recent_appointments[] = $row;
    }
}

// ---- Low Stock Items ----
$low_stock_items = [];
$sql = "SELECT item_name, quantity, unit, low_stock_threshold
        FROM inventory
        WHERE quantity <= low_stock_threshold
        ORDER BY quantity ASC
        LIMIT 5";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $low_stock_items[] = $row;
    }
}