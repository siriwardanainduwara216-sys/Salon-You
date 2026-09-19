<?php
// ============================================================
// admin-reports-data.php
// All business/financial report queries live here.
// $conn must be set before including this file.
// ============================================================

// ---- Monthly revenue trend (last 6 months, from completed appointments) ----
$monthly_revenue = [];
$sql = "SELECT DATE_FORMAT(a.appointment_date, '%Y-%m') AS month_label,
               SUM(s.price) AS total
        FROM appointments a
        JOIN services s ON a.service_id = s.id
        WHERE a.status = 'completed'
        AND a.appointment_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY month_label
        ORDER BY month_label ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $monthly_revenue[] = $row;
    }
}

// ---- Top 5 services by number of completed bookings ----
$top_services = [];
$sql = "SELECT s.service_name, COUNT(*) AS total_bookings, SUM(s.price) AS total_revenue
        FROM appointments a
        JOIN services s ON a.service_id = s.id
        WHERE a.status = 'completed'
        GROUP BY s.id
        ORDER BY total_bookings DESC
        LIMIT 5";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $top_services[] = $row;
    }
}

// ---- Appointment status breakdown ----
$status_breakdown = ['pending' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0];
$sql = "SELECT status, COUNT(*) AS total FROM appointments GROUP BY status";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $status_breakdown[$row['status']] = (int) $row['total'];
    }
}
$total_appointments_all = array_sum($status_breakdown);

// ---- New customer signups per month (last 6 months) ----
$customer_growth = [];
$sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month_label, COUNT(*) AS total
        FROM users
        WHERE role = 'customer'
        AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
        GROUP BY month_label
        ORDER BY month_label ASC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $customer_growth[] = $row;
    }
}

// ---- Overall totals for summary cards ----
$grand_total_revenue = 0;
$sql = "SELECT SUM(s.price) AS total FROM appointments a JOIN services s ON a.service_id = s.id WHERE a.status = 'completed'";
if ($result = mysqli_query($conn, $sql)) {
    $grand_total_revenue = mysqli_fetch_assoc($result)['total'] ?? 0;
}

$total_customers = 0;
$sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'customer'";
if ($result = mysqli_query($conn, $sql)) {
    $total_customers = mysqli_fetch_assoc($result)['total'];
}