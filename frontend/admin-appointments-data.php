<?php

$appointments_list = [];

$sql = "SELECT a.id, a.appointment_date, a.appointment_time, a.status, a.staff_id,
               u.name AS customer_name, u.phone AS customer_phone,
               s.service_name, s.price,
               st.name AS staff_name
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN services s ON a.service_id = s.id
        LEFT JOIN users st ON a.staff_id = st.id";

if ($status_filter !== 'all') {
    $sql .= " WHERE a.status = ?";
}

$sql .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";

$stmt = mysqli_prepare($conn, $sql);
if ($status_filter !== 'all') {
    mysqli_stmt_bind_param($stmt, "s", $status_filter);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $appointments_list[] = $row;
}
mysqli_stmt_close($stmt);

// ---- All staff members (for assignment dropdown) ----
$all_staff = [];
$sql_staff = "SELECT id, name FROM users WHERE role = 'employee' ORDER BY name ASC";
if ($result_staff = mysqli_query($conn, $sql_staff)) {
    while ($row = mysqli_fetch_assoc($result_staff)) {
        $all_staff[] = $row;
    }
}

// ---- Status counts for the filter tabs ----
$status_counts = ['all' => 0, 'pending' => 0, 'confirmed' => 0, 'completed' => 0, 'cancelled' => 0];
$sql = "SELECT status, COUNT(*) AS total FROM appointments GROUP BY status";
if ($result = mysqli_query($conn, $sql)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $status_counts[$row['status']] = (int) $row['total'];
        $status_counts['all'] += (int) $row['total'];
    }
}