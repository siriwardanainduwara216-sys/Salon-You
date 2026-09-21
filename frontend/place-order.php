<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once __DIR__ . '/../backend/config.php';
global $conn;

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'customer') {
    echo json_encode(['success' => false, 'message' => 'Please login as a customer.']);
    exit();
}

if (empty($_SESSION['cart'])) {
    echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$pickup_date = trim($_POST['pickup_date'] ?? '');

if ($pickup_date === '') {
    echo json_encode(['success' => false, 'message' => 'Please choose a pickup date.']);
    exit();
}

// ---- Build the order & check stock from database ----
$order_items = [];
$total_amount = 0;

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    // SELECT query එකට stock_quantity column එක එකතු කළා
    $sql = "SELECT id, product_name, price, stock_quantity FROM products WHERE id = ? AND is_active = 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($product) {
        // 1. Stock එක සම්පූර්ණයෙන්ම ඉවර නම්
        if ($product['stock_quantity'] <= 0) {
            echo json_encode([
                'success' => false, 
                'message' => 'Sorry, "' . $product['product_name'] . '" is currently out of stock.'
            ]);
            exit();
        }

        // 2. Cart එකේ තියෙන ප්‍රමාණයට වඩා stock එක අඩු නම්
        if ($quantity > $product['stock_quantity']) {
            echo json_encode([
                'success' => false, 
                'message' => 'Only ' . $product['stock_quantity'] . ' unit(s) available for "' . $product['product_name'] . '". Please update your cart.'
            ]);
            exit();
        }

        $order_items[] = [
            'product_id' => $product['id'],
            'quantity'   => $quantity,
            'price'      => $product['price'],
        ];
        $total_amount += $product['price'] * $quantity;
    }
}

if (empty($order_items)) {
    echo json_encode(['success' => false, 'message' => 'No valid products in cart.']);
    exit();
}

// ---- Insert the order ----
$sql = "INSERT INTO product_orders (user_id, total_amount, status, pickup_date) VALUES (?, ?, 'pending', ?)";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ids", $user_id, $total_amount, $pickup_date);

if (mysqli_stmt_execute($stmt)) {
    $order_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    foreach ($order_items as $item) {
        $sql = "INSERT INTO product_order_items (order_id, product_id, quantity, price_at_time) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    // Clear the cart
    $_SESSION['cart'] = [];

    echo json_encode(['success' => true, 'order_id' => $order_id]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error: ' . mysqli_error($conn)]);
}