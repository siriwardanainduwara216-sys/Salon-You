<?php
// ============================================================
// ADMIN PRODUCT ORDERS DATA
// Fetches all data needed by admin-product-orders.php
// ============================================================

// Active status filter (from URL, defaults to 'all')
$status_filter = $_GET['status'] ?? 'all';
$allowed_filters = ['all', 'pending', 'ready', 'completed', 'cancelled'];
if (!in_array($status_filter, $allowed_filters)) {
    $status_filter = 'all';
}

// ---- Fetch orders (with customer info and item summary) ----
$where_clause = ($status_filter !== 'all') ? "WHERE po.status = '" . mysqli_real_escape_string($conn, $status_filter) . "'" : "";

$sql = "SELECT po.id, po.total_amount, po.status, po.pickup_date, po.created_at,
               u.name AS customer_name, u.phone AS customer_phone
        FROM product_orders po
        JOIN users u ON po.user_id = u.id
        $where_clause
        ORDER BY po.created_at DESC";
$result = mysqli_query($conn, $sql);
$orders_list = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Fetch this order's line items
    $sql2 = "SELECT poi.quantity, poi.price_at_time, p.product_name
             FROM product_order_items poi
             JOIN products p ON poi.product_id = p.id
             WHERE poi.order_id = ?";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "i", $row['id']);
    mysqli_stmt_execute($stmt2);
    $items_result = mysqli_stmt_get_result($stmt2);
    $row['items'] = [];
    while ($item = mysqli_fetch_assoc($items_result)) {
        $row['items'][] = $item;
    }
    mysqli_stmt_close($stmt2);

    $orders_list[] = $row;
}

// ---- Counts for the filter tabs ----
$status_counts = ['all' => 0, 'pending' => 0, 'ready' => 0, 'completed' => 0, 'cancelled' => 0];
$sql = "SELECT status, COUNT(*) AS total FROM product_orders GROUP BY status";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $status_counts[$row['status']] = $row['total'];
    $status_counts['all'] += $row['total'];
}

// ============================================================
// ADMIN PRODUCT ORDERS DATA
// Fetches all data needed by admin-product-orders.php
// ============================================================

// Active status filter (from URL, defaults to 'all')
$status_filter = $_GET['status'] ?? 'all';
$allowed_filters = ['all', 'pending', 'ready', 'completed', 'cancelled'];
if (!in_array($status_filter, $allowed_filters)) {
    $status_filter = 'all';
}

// Date range filter (defaults to the current month)
$from_date = $_GET['from_date'] ?? date('Y-m-01');
$to_date = $_GET['to_date'] ?? date('Y-m-d');

if (!strtotime($from_date)) {
    $from_date = date('Y-m-01');
}
if (!strtotime($to_date)) {
    $to_date = date('Y-m-d');
}

$from_date_start = $from_date . ' 00:00:00';
$to_date_end = $to_date . ' 23:59:59';

// ---- Build WHERE clause from both filters ----
$where_parts = ["po.created_at BETWEEN ? AND ?"];
$bind_types = "ss";
$bind_values = [$from_date_start, $to_date_end];

if ($status_filter !== 'all') {
    $where_parts[] = "po.status = ?";
    $bind_types .= "s";
    $bind_values[] = $status_filter;
}

$where_clause = "WHERE " . implode(" AND ", $where_parts);

// ---- Fetch orders (with customer info and item summary) ----
$sql = "SELECT po.id, po.total_amount, po.status, po.pickup_date, po.created_at,
               u.name AS customer_name, u.phone AS customer_phone
        FROM product_orders po
        JOIN users u ON po.user_id = u.id
        $where_clause
        ORDER BY po.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $bind_types, ...$bind_values);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$orders_list = [];
$revenue_total = 0;
while ($row = mysqli_fetch_assoc($result)) {
    $sql2 = "SELECT poi.quantity, poi.price_at_time, p.product_name
             FROM product_order_items poi
             JOIN products p ON poi.product_id = p.id
             WHERE poi.order_id = ?";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "i", $row['id']);
    mysqli_stmt_execute($stmt2);
    $items_result = mysqli_stmt_get_result($stmt2);
    $row['items'] = [];
    while ($item = mysqli_fetch_assoc($items_result)) {
        $row['items'][] = $item;
    }
    mysqli_stmt_close($stmt2);

    $orders_list[] = $row;

    // Only count actually completed sales toward revenue
    if ($row['status'] === 'completed') {
        $revenue_total += $row['total_amount'];
    }
}
mysqli_stmt_close($stmt);

// Counts for the filter tabs (within the selected date range)
$status_counts = ['all' => 0, 'pending' => 0, 'ready' => 0, 'completed' => 0, 'cancelled' => 0];
$sql = "SELECT status, COUNT(*) AS total FROM product_orders WHERE created_at BETWEEN ? AND ? GROUP BY status";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $from_date_start, $to_date_end);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $status_counts[$row['status']] = $row['total'];
    $status_counts['all'] += $row['total'];
}
?>