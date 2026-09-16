<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'customer') {
    echo json_encode(['success' => false, 'message' => 'Please login as a customer.']);
    exit();
}

$product_id = (int) ($_POST['product_id'] ?? 0);
$action = $_POST['action'] ?? 'add';

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($action === 'decrease') {
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]--;
        if ($_SESSION['cart'][$product_id] <= 0) {
            unset($_SESSION['cart'][$product_id]);
        }
    }
} else {
    // 'add' or 'increase' both just add 1
    $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + 1;
}

$cart_count = array_sum($_SESSION['cart']);

echo json_encode(['success' => true, 'cart_count' => $cart_count]);