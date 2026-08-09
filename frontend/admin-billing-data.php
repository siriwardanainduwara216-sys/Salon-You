<?php
// ============================================================
// admin-billing-data.php
// All payment/billing related queries live here.
// $conn must be set before including this file.
// ============================================================

// ---- All payments with related appointment/customer/service info ----
$payments_list = [];
$sql = "SELECT p.id, p.amount, p.payment_method, p.payment_status, p.payment_date,
               a.appointment_date, u.id AS customer_id, u.name AS customer_name, s.service_name
        FROM payments p
        JOIN appointments a ON p.appointment_id = a.id
        JOIN users u ON a.user_id = u.id
        JOIN services s ON a.service_id = s.id
        ORDER BY p.payment_date DESC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $payments_list[] = $row;
    }
}

// ---- Completed appointments that don't have a payment recorded yet ----
$unpaid_appointments = [];
$sql = "SELECT a.id, a.appointment_date, u.name AS customer_name, s.service_name, s.price
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN services s ON a.service_id = s.id
        WHERE a.status = 'completed'
        AND a.id NOT IN (SELECT appointment_id FROM payments)
        ORDER BY a.appointment_date DESC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $unpaid_appointments[] = $row;
    }
}

// All customers with at least one payment (for invoice generation search) 
$customers_with_payments = [];
$sql = "SELECT DISTINCT u.id, u.name, u.email
        FROM users u
        JOIN appointments a ON u.id = a.user_id
        JOIN payments p ON p.appointment_id = a.id
        ORDER BY u.name ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $customers_with_payments[] = $row;
    }
}

// Totals for summary cards
$total_collected = 0;
$total_pending = 0;
$sql = "SELECT payment_status, SUM(amount) AS total FROM payments GROUP BY payment_status";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($row['payment_status'] === 'paid') {
            $total_collected = $row['total'];
        } elseif ($row['payment_status'] === 'pending') {
            $total_pending = $row['total'];
        }
    }
}