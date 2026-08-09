<?php
// ============================================================
// admin-staff-performance-data.php
// Staff performance metrics queries.
// $conn must be set before including this file.
// ============================================================

// ---- All staff with performance metrics ----
$staff_performance = [];
$sql = "SELECT u.id, u.name, u.email, u.phone,
               COUNT(a.id) AS total_appointments,
               SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) AS completed_appointments,
               ROUND(AVG(CASE WHEN r.rating IS NOT NULL THEN r.rating ELSE NULL END), 2) AS avg_rating,
               SUM(CASE WHEN a.status = 'completed' THEN s.price ELSE 0 END) AS total_revenue
        FROM users u
        LEFT JOIN appointments a ON u.id = a.staff_id
        LEFT JOIN services s ON a.service_id = s.id
        LEFT JOIN reviews r ON a.id = r.appointment_id
        WHERE u.role = 'employee'
        GROUP BY u.id
        ORDER BY total_revenue DESC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $staff_performance[] = $row;
    }
}

// ---- Overall staff metrics ----
$total_staff = 0;
$sql = "SELECT COUNT(*) AS total FROM users WHERE role = 'employee'";
if ($result = mysqli_query($conn, $sql)) {
    $total_staff = mysqli_fetch_assoc($result)['total'];
}

$staff_revenue_total = 0;
$sql = "SELECT SUM(s.price) AS total FROM appointments a 
        JOIN services s ON a.service_id = s.id 
        WHERE a.status = 'completed' AND a.staff_id IS NOT NULL";
if ($result = mysqli_query($conn, $sql)) {
    $staff_revenue_total = mysqli_fetch_assoc($result)['total'] ?? 0;
}