<?php


//Low stock inventory items 
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
//Low stock products (retail catalog)
$low_stock_products = [];
$sql = "SELECT id, product_name, stock_quantity
        FROM products
        WHERE is_active = 1 AND stock_quantity <= 5
        ORDER BY stock_quantity ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $low_stock_products[] = $row;
    }
}

//  Pending appointments
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

// Pending product orders (not yet completed)
$pending_product_orders = [];
$sql = "SELECT po.id, po.total_amount, po.created_at,
               u.name AS customer_name
        FROM product_orders po
        JOIN users u ON po.user_id = u.id
        WHERE po.status IN ('pending', 'ready')
        ORDER BY po.created_at ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pending_product_orders[] = $row;
    }
}

//Today's confirmed/pending appointments 
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

// Pending payments
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

//Total notification count for services
$total_notifications = count($low_stock_items) + count($pending_appointments) + count($pending_payments);

//Total notification count for products
$total_notifications = count($low_stock_items) + count($low_stock_products) + count($pending_appointments) + count($pending_product_orders) + count($pending_payments);