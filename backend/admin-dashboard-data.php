<?php


// Today Bookings 
$today_count = 0;
$sql = "SELECT COUNT(*) AS total FROM appointments WHERE appointment_date = CURDATE()";
if ($result = mysqli_query($conn, $sql)) {
    $today_count = mysqli_fetch_assoc($result)['total'];
}

//This Week Bookings
$week_count = 0;
$sql = "SELECT COUNT(*) AS total FROM appointments WHERE YEARWEEK(appointment_date, 1) = YEARWEEK(CURDATE(), 1)";
if ($result = mysqli_query($conn, $sql)) {
    $week_count = mysqli_fetch_assoc($result)['total'];
}

//This Month Bookings
$month_count = 0;
$sql = "SELECT COUNT(*) AS total FROM appointments WHERE MONTH(appointment_date) = MONTH(CURDATE()) AND YEAR(appointment_date) = YEAR(CURDATE())";
if ($result = mysqli_query($conn, $sql)) {
    $month_count = mysqli_fetch_assoc($result)['total'];
}


//Total Revenue
$total_revenue = 0;
$sql = "SELECT SUM(s.price) AS total
        FROM appointments a
        JOIN services s ON a.service_id = s.id
        WHERE a.status = 'completed'
        AND MONTH(a.appointment_date) = MONTH(CURDATE())
        AND YEAR(a.appointment_date) = YEAR(CURDATE())";
if ($result = mysqli_query($conn, $sql)) {
    $row = mysqli_fetch_assoc($result);
    $total_revenue = $row['total'] ?? 0;
}
//Product Sales Overview
$products_today_count = 0;
$sql = "SELECT COUNT(*) AS total FROM product_orders WHERE DATE(created_at) = CURDATE()";
if ($result = mysqli_query($conn, $sql)) {
    $products_today_count = mysqli_fetch_assoc($result)['total'];
}

$products_week_count = 0;
$sql = "SELECT COUNT(*) AS total FROM product_orders WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)";
if ($result = mysqli_query($conn, $sql)) {
    $products_week_count = mysqli_fetch_assoc($result)['total'];
}

$products_month_count = 0;
$sql = "SELECT COUNT(*) AS total FROM product_orders WHERE MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE())";
if ($result = mysqli_query($conn, $sql)) {
    $products_month_count = mysqli_fetch_assoc($result)['total'];
}

// Product revenue - only counts COMPLETED orders (actual paid sales), this month
$product_revenue = 0;
$sql = "SELECT SUM(total_amount) AS total
        FROM product_orders
        WHERE status = 'completed'
        AND MONTH(created_at) = MONTH(CURDATE())
        AND YEAR(created_at) = YEAR(CURDATE())";
if ($result = mysqli_query($conn, $sql)) {
    $row = mysqli_fetch_assoc($result);
    $product_revenue = $row['total'] ?? 0;
}

// Recent Appointments 
$recent_appointments = [];
$sql = "SELECT u.name AS customer_name, s.service_name, a.status
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN services s ON a.service_id = s.id
        ORDER BY a.created_at DESC
        LIMIT 20";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $recent_appointments[] = $row;
    }
}

//Low Stock Items
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
//Low Stock Products (retail catalog, separate from service inventory)
$low_stock_products = [];
$sql = "SELECT id, product_name, stock_quantity
        FROM products
        WHERE is_active = 1 AND stock_quantity <= 5
        ORDER BY stock_quantity ASC
        LIMIT 5";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $low_stock_products[] = $row;
    }
}
//Total Customers Count
$total_customers = 0;
$sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'customer'";
if ($result = mysqli_query($conn, $sql)) {
    $total_customers = mysqli_fetch_assoc($result)['total'];
}