<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/backend/config.php';
global $conn;

$staff_id = (int) ($_GET['staff_id'] ?? 0);
$date = $_GET['date'] ?? '';

if ($staff_id <= 0 || $date === '') {
    echo json_encode(['busy_slots' => []]);
    exit();
}

// Fetch all pending/confirmed appointments for this stylist on this date,
// along with each one's service duration, so we can compute real time ranges.
$sql = "SELECT a.appointment_time, s.duration_mins, s.service_name
        FROM appointments a
        JOIN services s ON a.service_id = s.id
        WHERE a.staff_id = ? AND a.appointment_date = ?
        AND a.status IN ('pending', 'confirmed')
        ORDER BY a.appointment_time ASC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "is", $staff_id, $date);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$busy_slots = [];
while ($row = mysqli_fetch_assoc($result)) {
    $start = strtotime($date . ' ' . $row['appointment_time']);
    $end = $start + ((int) $row['duration_mins'] * 60);
    $busy_slots[] = [
        'start' => date('H:i', $start),
        'end' => date('H:i', $end),
        'start_label' => date('g:i A', $start),
        'end_label' => date('g:i A', $end),
    ];
}
mysqli_stmt_close($stmt);

echo json_encode(['busy_slots' => $busy_slots]);