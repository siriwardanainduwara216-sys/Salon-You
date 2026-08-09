<?php
// ============================================================
// admin-notifications-data.php
// Aggregates alerts the admin needs to act on.
// $conn must be set before including this file.
// ============================================================

// ---- Low stock inventory items ----
$low_stock_items = [];
$sql = "SELECT id, item_name, quantity, unit, low_stock_threshold
        FROM inventory
        WHERE quantity <= low_stock_threshold
        ORDER BY quantity ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $low_stock_items[] = $row;
    }
}

// ---- Pending appointments (awaiting confirmation) ----
$pending_appointments = [];
$sql = "SELECT a.id, a.appointment_date, a.appointment_time,
               u.name AS customer_name, s.service_name
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN services s ON a.service_id = s.id
        WHERE a.status = 'pending'
        ORDER BY a.appointment_date ASC, a.appointment_time ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pending_appointments[] = $row;
    }
}

// ---- Today's confirmed/pending appointments (today's schedule) ----
$today_appointments = [];
$sql = "SELECT a.id, a.appointment_time, a.status,
               u.name AS customer_name, s.service_name
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN services s ON a.service_id = s.id
        WHERE a.appointment_date = CURDATE()
        AND a.status IN ('pending', 'confirmed')
        ORDER BY a.appointment_time ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $today_appointments[] = $row;
    }
}

// ---- Pending payments ----
$pending_payments = [];
$sql = "SELECT p.id, p.amount, p.payment_date,
               u.name AS customer_name, s.service_name
        FROM payments p
        JOIN appointments a ON p.appointment_id = a.id
        JOIN users u ON a.user_id = u.id
        JOIN services s ON a.service_id = s.id
        WHERE p.payment_status = 'pending'
        ORDER BY p.payment_date ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pending_payments[] = $row;
    }
}

// ---- Total notification count (for the sidebar badge) ----
$total_notifications = count($low_stock_items) + count($pending_appointments) + count($pending_payments);