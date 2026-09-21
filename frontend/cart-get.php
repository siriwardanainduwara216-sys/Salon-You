<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once __DIR__ . '/../backend/config.php';
global $conn;

$items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $sql = "SELECT id, product_name, image_url, price FROM products WHERE id = ? AND is_active = 1";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        mysqli_stmt_execute($stmt);
        $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if ($product) {
            $product['quantity'] = $quantity;
            $items[] = $product;
            $total += $product['price'] * $quantity;
        }
    }
}

echo json_encode(['items' => $items, 'total' => $total]);