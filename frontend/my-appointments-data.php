<?php
// ============================================================
// my-appointments-data.php
// Fetches the logged-in customer's own appointments.
// $conn and $user_id must be set before including this file.
// ============================================================

$my_appointments = [];
$sql = "SELECT a.id, a.appointment_date, a.appointment_time, a.status,
               s.service_name, s.price,
               st.name AS staff_name
        FROM appointments a
        JOIN services s ON a.service_id = s.id
        LEFT JOIN users st ON a.staff_id = st.id
        WHERE a.user_id = ?
        ORDER BY a.appointment_date DESC, a.appointment_time DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($result)) {
    $my_appointments[] = $row;
}
mysqli_stmt_close($stmt);