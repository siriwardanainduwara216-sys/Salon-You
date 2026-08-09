<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$appointment_id = (int) ($_GET['id'] ?? 0);

require_once __DIR__ . '/../backend/config.php';
global $conn;

if ($appointment_id > 0) {
    // IMPORTANT: only cancel if this appointment actually belongs to the logged-in customer,
    // and only if it's still pending or confirmed (not already completed/cancelled).
    $sql = "UPDATE appointments
            SET status = 'cancelled'
            WHERE id = ? AND user_id = ? AND status IN ('pending', 'confirmed')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $appointment_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

header("Location: my-appointments.php?cancelled=1");
exit();