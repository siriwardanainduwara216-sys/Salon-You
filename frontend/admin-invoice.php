<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
if ($_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$admin_name = $_SESSION['user_name'];

require_once __DIR__ . '/../backend/config.php';
global $conn;

$customer_id = (int) ($_GET['customer_id'] ?? 0);

if ($customer_id <= 0) {
    header("Location: admin-billing.php");
    exit();
}

// ---- Customer details ----
$sql = "SELECT id, name, email, phone FROM users WHERE id = ? AND role = 'customer'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$customer = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$customer) {
    header("Location: admin-billing.php");
    exit();
}

// ---- All payments for this customer (itemized) ----
$invoice_items = [];
$sql = "SELECT p.id, p.amount, p.payment_method, p.payment_status, p.payment_date,
               a.appointment_date, s.service_name
        FROM payments p
        JOIN appointments a ON p.appointment_id = a.id
        JOIN services s ON a.service_id = s.id
        WHERE a.user_id = ?
        ORDER BY p.payment_date ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $customer_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $invoice_items[] = $row;
}
mysqli_stmt_close($stmt);

// ---- Totals ----
$grand_total = 0;
$paid_total = 0;
$pending_total = 0;
foreach ($invoice_items as $item) {
    $grand_total += $item['amount'];
    if ($item['payment_status'] === 'paid') {
        $paid_total += $item['amount'];
    } elseif ($item['payment_status'] === 'pending') {
        $pending_total += $item['amount'];
    }
}

$invoice_number = 'INV-' . str_pad($customer_id, 4, '0', STR_PAD_LEFT) . '-' . date('Ymd');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Invoice for <?php echo htmlspecialchars($customer['name']); ?></title>
<!-- FontAwesome Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
<link rel="stylesheet" href="frontend-css/invoice.css">
</head>
<body>

    <div class="toolbar">
        <a href="admin-billing.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Billing</a>
        <button onclick="window.print()" class="btn"><i class="fas fa-print"></i> Print / Save as PDF</button>
    </div>

    <div class="invoice-box">
        <div class="invoice-header">
            <div>
                <h1>Salon You</h1>
                <p>Beauty & Wellness Salon</p>
                <p>Sri Lanka</p>
            </div>
            <div class="invoice-meta">
                <p><strong>Invoice #:</strong> <?php echo $invoice_number; ?></p>
                <p><strong>Date:</strong> <?php echo date('Y-m-d'); ?></p>
            </div>
        </div>

        <div class="bill-to">
            <h4>Bill To</h4>
            <p>
                <strong><?php echo htmlspecialchars($customer['name']); ?></strong><br>
                <?php echo htmlspecialchars($customer['email']); ?><br>
                <?php echo htmlspecialchars($customer['phone']); ?>
            </p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Appointment Date</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($invoice_items)): ?>
                    <tr><td colspan="5" style="text-align:center; color:#888;">No payments found for this customer.</td></tr>
                <?php else: ?>
                    <?php foreach ($invoice_items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['service_name']); ?></td>
                            <td><?php echo $item['appointment_date']; ?></td>
                            <td><?php echo ucfirst($item['payment_method']); ?></td>
                            <td><span class="badge <?php echo $item['payment_status']; ?>"><?php echo ucfirst($item['payment_status']); ?></span></td>
                            <td class="text-right">Rs. <?php echo number_format($item['amount'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="totals-box">
            <div class="totals-row">
                <span>Paid</span>
                <span>Rs. <?php echo number_format($paid_total, 2); ?></span>
            </div>
            <div class="totals-row">
                <span>Pending</span>
                <span>Rs. <?php echo number_format($pending_total, 2); ?></span>
            </div>
            <div class="totals-row grand">
                <span>Total</span>
                <span>Rs. <?php echo number_format($grand_total, 2); ?></span>
            </div>
        </div>

        <div class="invoice-footer">
            <p>Thank you for choosing Salon You!</p>
            <p>This is a system-generated invoice.</p>
        </div>
    </div>

</body>
</html>