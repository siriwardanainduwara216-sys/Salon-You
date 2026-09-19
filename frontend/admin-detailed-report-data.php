<?php
// ============================================================
// admin-detailed-report-data.php
// Detailed service report grouped by staff, filtered by date range.
// $conn, $date_from, $date_to must be set before including this file.
// ============================================================

// ---- All completed services with staff assigned, within the date range ----
$all_services_list = [];
$sql = "SELECT a.id, a.appointment_date, a.appointment_time,
               u.name AS customer_name, u.email AS customer_email,
               s.service_name, s.price,
               st.id AS staff_id, st.name AS staff_name
        FROM appointments a
        JOIN users u ON a.user_id = u.id
        JOIN services s ON a.service_id = s.id
        JOIN users st ON a.staff_id = st.id
        WHERE a.status = 'completed'
        AND a.staff_id IS NOT NULL
        AND a.appointment_date BETWEEN ? AND ?
        ORDER BY st.name ASC, a.appointment_date DESC, a.appointment_time DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ss", $date_from, $date_to);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $all_services_list[] = $row;
}
mysqli_stmt_close($stmt);

// ---- Group the flat list by staff member ----
$grouped_by_staff = [];
foreach ($all_services_list as $item) {
    $sid = $item['staff_id'];
    if (!isset($grouped_by_staff[$sid])) {
        $grouped_by_staff[$sid] = [
            'staff_name' => $item['staff_name'],
            'services' => [],
            'subtotal_count' => 0,
            'subtotal_revenue' => 0,
        ];
    }
    $grouped_by_staff[$sid]['services'][] = $item;
    $grouped_by_staff[$sid]['subtotal_count'] += 1;
    $grouped_by_staff[$sid]['subtotal_revenue'] += $item['price'];
}

// ---- Overall totals across all staff, for the selected date range ----
$grand_total_services = count($all_services_list);
$grand_total_revenue = 0;
foreach ($all_services_list as $item) {
    $grand_total_revenue += $item['price'];
}