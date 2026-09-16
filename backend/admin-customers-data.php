<?php


$customer_list = [];
$sql = "SELECT u.id, u.name, u.email, u.phone, u.status, u.loyalty_points, u.created_at,
               (SELECT COUNT(*) FROM appointments a WHERE a.user_id = u.id) AS total_bookings
        FROM users u
        WHERE u.role = 'customer'
        ORDER BY u.created_at DESC";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $customer_list[] = $row;
    }
}