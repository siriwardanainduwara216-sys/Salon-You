<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once __DIR__ . '/../backend/config.php';
require_once __DIR__ . '/../backend/payhere-config.php';
global $conn;

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'customer') {
    echo json_encode(['success' => false, 'message' => 'Please login as a customer.']);
    exit();
}

$order_id = (int) ($_GET['order_id'] ?? 0);
$user_id = $_SESSION['user_id'];

// Fetch the order (only if it belongs to this customer)
$sql = "SELECT po.id, po.total_amount, u.name, u.email, u.phone
        FROM product_orders po
        JOIN users u ON po.user_id = u.id
        WHERE po.id = ? AND po.user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found.']);
    exit();
}

$amount = number_format((float) $order['total_amount'], 2, '.', '');
$currency = 'LKR';

// PayHere hash formula: MD5(merchant_id + order_id + amount + currency + MD5(merchant_secret))
$hashed_secret = strtoupper(md5(PAYHERE_MERCHANT_SECRET));
$hash = strtoupper(md5(
    PAYHERE_MERCHANT_ID . $order_id . $amount . $currency . $hashed_secret
));

// Split customer name into first/last for PayHere's fields
$name_parts = explode(' ', trim($order['name']), 2);
$first_name = $name_parts[0];
$last_name = $name_parts[1] ?? '';

echo json_encode([
    'success' => true,
    'payment' => [
        'sandbox' => PAYHERE_SANDBOX,
        'merchant_id' => PAYHERE_MERCHANT_ID,
        'return_url' => null,
        'cancel_url' => null,
        'notify_url' => null, // not used in testing mode (client-side callback only)
        'order_id' => (string) $order_id,
        'items' => 'Salon You - Product Order #' . $order_id,
        'amount' => $amount,
        'currency' => $currency,
        'hash' => $hash,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $order['email'],
        'phone' => $order['phone'],
        'address' => 'N/A',
        'city' => 'Colombo',
        'country' => 'Sri Lanka',
    ],
]);