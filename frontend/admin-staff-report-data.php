<?php
// ============================================================
// admin-staff-report-data.php
// Per-staff performance report with date range filter.
// $conn, $staff_id, $date_from, $date_to must be set before including this file.
// ============================================================

// ---- Staff member details ----
$staff_info = null;
$sql = "SELECT id, name, email, phone, status FROM users WHERE id = ? AND role = 'employee'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $staff_id);
mysqli_stmt_execute($stmt);
$staff_info = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

// ---- Completed services handled by this staff member, within the date range ----
$staff_services = [];
$sql = "SELECT a.id, a.appointment_date, a.appointment_time,
               u.name AS customer_name, u.email AS customer_email,
               s.service_name, s.price
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN services s ON a.service_id = s.id
        WHERE a.staff_id = ?
        AND a.status = 'completed'
        AND a.appointment_date BETWEEN ? AND ?
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iss", $staff_id, $date_from, $date_to);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $staff_services[] = $row;
}
mysqli_stmt_close($stmt);

// ---- Totals for the selected date range ----
$total_services_count = count($staff_services);
$total_revenue = 0;
foreach ($staff_services as $item) {
    $total_revenue += $item['price'];
}