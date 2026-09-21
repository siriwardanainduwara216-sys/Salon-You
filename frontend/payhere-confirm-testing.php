<?php
// ⚠️ TESTING ONLY — client-side confirmation with NO server-side payment verification.
// Before going live, replace this with a proper PayHere notify_url (server-to-server,
// hash-verified) handler, since this endpoint currently trusts the browser completely.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once __DIR__ . '/../backend/config.php';
global $conn;

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'customer') {
    echo json_encode(['success' => false]);
    exit();
}

$order_id = (int) ($_GET['order_id'] ?? 0);
$user_id = $_SESSION['user_id'];

// Only touch this customer's own order, and only if it's still pending
$sql = "SELECT status FROM product_orders WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$current = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if ($current && $current['status'] === 'pending') {
    $sql = "UPDATE product_orders SET status = 'completed' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Deduct stock (same logic as admin-product-orders.php)
    $sql = "SELECT product_id, quantity FROM product_order_items WHERE order_id = ?";
    $item_stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($item_stmt, "i", $order_id);
    mysqli_stmt_execute($item_stmt);
    $items_result = mysqli_stmt_get_result($item_stmt);

    while ($item = mysqli_fetch_assoc($items_result)) {
        $dsql = "UPDATE products SET stock_quantity = GREATEST(stock_quantity - ?, 0) WHERE id = ?";
        $dstmt = mysqli_prepare($conn, $dsql);
        mysqli_stmt_bind_param($dstmt, "ii", $item['quantity'], $item['product_id']);
        mysqli_stmt_execute($dstmt);
        mysqli_stmt_close($dstmt);
    }
    mysqli_stmt_close($item_stmt);
}

echo json_encode(['success' => true]);