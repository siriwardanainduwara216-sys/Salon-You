<?php

// admin-queue-data.php
// Fetches today's appointments grouped by stylist, in time order.
// $conn must be set before including this file.

$queue_by_staff = [];

$sql = "SELECT a.id, a.appointment_time, a.queue_status,
               u.name AS customer_name, u.phone AS customer_phone,
               s.service_name,
               st.id AS staff_id, st.name AS staff_name
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN services s ON a.service_id = s.id
        LEFT JOIN users st ON a.staff_id = st.id
        WHERE a.appointment_date = CURDATE()
        AND a.status IN ('pending', 'confirmed')
        ORDER BY a.appointment_time ASC";

if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $key = $row['staff_id'] ?? 0;
        if (!isset($queue_by_staff[$key])) {
            $queue_by_staff[$key] = [
                'staff_name' => $row['staff_name'] ?? 'Unassigned',
                'appointments' => [],
            ];
        }
        $queue_by_staff[$key]['appointments'][] = $row;
    }
}