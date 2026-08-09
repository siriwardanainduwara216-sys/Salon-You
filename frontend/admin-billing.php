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

$success_msg = '';
$error_msg = '';

//Helper: fetch payment + customer + service details and email a receipt
function send_receipt_for_payment($conn, $payment_id) {
    $sql = "SELECT p.amount, p.payment_method, p.payment_date,
                   u.name AS customer_name, u.email AS customer_email,
                   s.service_name
            FROM payments p
            JOIN appointments a ON p.appointment_id = a.id
            JOIN users u ON a.user_id = u.id
            JOIN services s ON a.service_id = s.id
            WHERE p.id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $payment_id);
    mysqli_stmt_execute($stmt);
    $info = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($info) {
        require_once __DIR__ . '/../backend/mailer.php';
        send_payment_receipt_email(
            $info['customer_email'],
            $info['customer_name'],
            $info['service_name'],
            $info['amount'],
            $info['payment_method'],
            $info['payment_date']
        );
    }
}


// RECORD NEW PAYMENT

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payment'])) {
    $appointment_id = (int) $_POST['appointment_id'];
    $amount = (float) $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $payment_status = $_POST['payment_status'];

    if ($appointment_id <= 0) {
        $error_msg = "Please select an appointment.";
    } else {
        $sql = "INSERT INTO payments (appointment_id, amount, payment_method, payment_status) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "idss", $appointment_id, $amount, $payment_method, $payment_status);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Payment recorded successfully!";

            // If it was recorded as already paid, email the receipt right away
            if ($payment_status === 'paid') {
                send_receipt_for_payment($conn, mysqli_insert_id($conn));
            }
        } else {
            $error_msg = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}


// UPDATE PAYMENT STATUS

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payment_status'])) {
    $payment_id = (int) $_POST['payment_id'];
    $new_status = $_POST['new_status'];

    $allowed = ['pending', 'paid', 'refunded'];
    if (in_array($new_status, $allowed)) {
        $sql = "UPDATE payments SET payment_status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $new_status, $payment_id);
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Payment status updated!";

            // If it was just marked as paid, email the receipt
            if ($new_status === 'paid') {
                send_receipt_for_payment($conn, $payment_id);
            }
        } else {
            $error_msg = "Error: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    }
}

require_once __DIR__ . '/admin-billing-data.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Salon You - Billing & Payments</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- External CSS Link -->
<link rel="stylesheet" href="frontend-css/billing.css">
</head>
<body>
<div class="admin-wrapper">
    <div class="sidebar">
        <div class="sidebar-header"><h2>Salon You</h2><p>Admin Panel</p></div>
        <ul class="sidebar-menu">
            <li><a href="admin-dashboard.php"><i class="fas fa-chart-line"></i><span> Dashboard</span></a></li>
            <li><a href="admin-staff.php"><i class="fas fa-users"></i><span> Staff Management</span></a></li>
            <li><a href="admin-customers.php"><i class="fas fa-user-friends"></i><span> Customer Management</span></a></li>
            <li><a href="admin-appointments.php"><i class="fas fa-calendar-check"></i><span> Appointments</span></a></li>
            <li><a href="admin-services.php"><i class="fas fa-cut"></i><span> Services</span></a></li>
            <li><a href="admin-inventory.php"><i class="fas fa-box"></i><span> Inventory</span></a></li>
            <li class="active"><a href="admin-billing.php"><i class="fas fa-money-bill"></i><span> Billing & Payments</span></a></li>
            <li><a href="admin-reports.php"><i class="fas fa-file-invoice-dollar"></i><span> Reports</span></a></li>
            <li><a href="admin-notifications.php"><i class="fas fa-bell"></i><span> Notifications</span></a></li>
            <li><a href="../backend/logout.php"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="topbar">
            <h3>Welcome, <?php echo htmlspecialchars($admin_name); ?></h3>
        </div>

        <div class="page-header"><h2>Billing & Payment Management</h2></div>

        <?php if ($success_msg): ?><div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div><?php endif; ?>
        <?php if ($error_msg): ?><div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div><?php endif; ?>

        <!-- Summary Cards -->
        <div class="cards-row">
            <div class="card green">
                <h4>Total Collected</h4>
                <p class="card-number">Rs. <?php echo number_format($total_collected, 2); ?></p>
            </div>
            <div class="card amber">
                <h4>Pending Payments</h4>
                <p class="card-number">Rs. <?php echo number_format($total_pending, 2); ?></p>
            </div>
        </div>

        <!-- Record New Payment -->
        <div class="panel">
            <h4>Record a New Payment</h4>
            <p class="hint">Only completed appointments without an existing payment record are shown below.</p>
            <form method="POST" action="admin-billing.php">
                <div class="form-grid">
                    <div class="form-group" style="grid-column: 1 / 3;">
                        <label>Appointment</label>
                        <select name="appointment_id" required>
                            <option value="">-- Select Appointment --</option>
                            <?php foreach ($unpaid_appointments as $a): ?>
                                <option value="<?php echo $a['id']; ?>" data-price="<?php echo $a['price']; ?>">
                                    <?php echo htmlspecialchars($a['customer_name']) . ' - ' . htmlspecialchars($a['service_name']) . ' (' . $a['appointment_date'] . ') - Rs. ' . number_format($a['price'], 2); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amount (Rs.)</label>
                        <input type="number" name="amount" step="0.01" min="0" required id="amount-input">
                    </div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="online">Online</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Payment Status</label>
                        <select name="payment_status">
                            <option value="paid">Paid</option>
                            <option value="pending">Pending</option>
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="add_payment" class="btn"><i class="fas fa-plus"></i> Record Payment</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Generate Customer Bill/Invoice -->
        <div class="panel">
            <h4>Generate Customer Bill</h4>
            <p class="hint">Search a customer by name or email to generate a printable invoice of all their recorded payments.</p>
            <form method="GET" action="admin-invoice.php" id="invoice-form" style="display:flex; gap:12px; align-items:end; flex-wrap:wrap;">
                <div class="form-group" style="flex:1; min-width:260px; position:relative;">
                    <label>Customer</label>
                    <input type="text" id="customer-search" placeholder="Type name or email..." autocomplete="off" required>
                    <input type="hidden" name="customer_id" id="customer-id-input">
                    <div id="customer-results" class="search-results"></div>
                </div>
                <button type="submit" class="btn"><i class="fas fa-file-invoice"></i> Generate Invoice</button>
            </form>
        </div>

        <script>
        // Customer list passed from PHP for client-side search (name + email)
        const customerList = <?php echo json_encode($customers_with_payments); ?>;

        const searchInput = document.getElementById('customer-search');
        const idInput = document.getElementById('customer-id-input');
        const resultsBox = document.getElementById('customer-results');
        const invoiceForm = document.getElementById('invoice-form');

        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            idInput.value = ''; // reset selection whenever the user types again
            resultsBox.innerHTML = '';

            if (query.length === 0) {
                resultsBox.style.display = 'none';
                return;
            }

            const matches = customerList.filter(c =>
                c.name.toLowerCase().includes(query) || c.email.toLowerCase().includes(query)
            );

            if (matches.length === 0) {
                resultsBox.innerHTML = '<div class="search-result-item no-match">No matching customer found</div>';
                resultsBox.style.display = 'block';
                return;
            }

            matches.forEach(c => {
                const item = document.createElement('div');
                item.className = 'search-result-item';
                item.innerHTML = '<strong>' + c.name + '</strong><br><span>' + c.email + '</span>';
                item.addEventListener('click', function() {
                    searchInput.value = c.name + ' (' + c.email + ')';
                    idInput.value = c.id;
                    resultsBox.innerHTML = '';
                    resultsBox.style.display = 'none';
                });
                resultsBox.appendChild(item);
            });

            resultsBox.style.display = 'block';
        });

        // Hide the results dropdown when clicking elsewhere on the page
        document.addEventListener('click', function(e) {
            if (!e.target.closest('#customer-search') && !e.target.closest('#customer-results')) {
                resultsBox.style.display = 'none';
            }
        });

        // Prevent submitting the form without actually picking a customer from the list
        invoiceForm.addEventListener('submit', function(e) {
            if (!idInput.value) {
                e.preventDefault();
                alert('Please select a customer from the search results.');
            }
        });
        </script>

        <!-- Payments List -->
        <div class="panel">
            <h4>Payment History (<?php echo count($payments_list); ?>)</h4>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Service</th>
                            <th>Appointment Date</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Recorded On</th>
                            <th>Update</th>
                            <th>Invoice</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($payments_list)): ?>
                            <tr><td colspan="9" style="color: var(--text-muted); text-align:center;">No payments recorded yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($payments_list as $p): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p['customer_name']); ?></td>
                                    <td><?php echo htmlspecialchars($p['service_name']); ?></td>
                                    <td><?php echo $p['appointment_date']; ?></td>
                                    <td>Rs. <?php echo number_format($p['amount'], 2); ?></td>
                                    <td><?php echo ucfirst($p['payment_method']); ?></td>
                                    <td><span class="badge <?php echo $p['payment_status']; ?>"><?php echo ucfirst($p['payment_status']); ?></span></td>
                                    <td><?php echo date('Y-m-d', strtotime($p['payment_date'])); ?></td>
                                    <td>
                                        <form method="POST" action="admin-billing.php" class="status-form">
                                            <input type="hidden" name="payment_id" value="<?php echo $p['id']; ?>">
                                            <select name="new_status">
                                                <option value="pending" <?php echo $p['payment_status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                                <option value="paid" <?php echo $p['payment_status'] === 'paid' ? 'selected' : ''; ?>>Paid</option>
                                                <option value="refunded" <?php echo $p['payment_status'] === 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                                            </select>
                                            <button type="submit" name="update_payment_status">Save</button>
                                        </form>
                                    </td>
                                    <td>
                                        <a href="admin-invoice.php?customer_id=<?php echo $p['customer_id']; ?>" title="View Invoice" style="color: var(--accent-blue); font-size:16px;">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-fill the amount field with the service price when an appointment is selected
document.querySelector('select[name="appointment_id"]').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const price = selected.getAttribute('data-price');
    if (price) {
        document.getElementById('amount-input').value = price;
    }
});
</script>

</body>
</html>