<?php
session_start();
require_once __DIR__ . '/backend/config.php';
global $conn;

// ---- Must be logged in as a customer ----
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'customer') {
    header("Location: frontend/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: frontend/services.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$staff_id = (int) ($_POST['staff_id'] ?? 0);
$service_id = (int) ($_POST['service_id'] ?? 0);
$appointment_date = trim($_POST['appointment_date'] ?? '');
$appointment_time = trim($_POST['appointment_time'] ?? '');

$error = '';

if ($staff_id <= 0 || $service_id <= 0 || $appointment_date === '' || $appointment_time === '') {
    $error = 'Missing booking details. Please try again.';
}

// ---- Verify the staff member exists ----
$staff_name = '';
if (!$error) {
    $sql = "SELECT name FROM users WHERE id = ? AND role = 'employee'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $staff_id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($row) {
        $staff_name = $row['name'];
    } else {
        $error = 'Selected stylist could not be found. Please go back and select again.';
    }
}

// ---- Verify the service exists (price always comes from the database, never the form) ----
$service_name = '';
$service_price = null;
if (!$error) {
    $sql = "SELECT service_name, price FROM services WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $service_id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($row) {
        $service_name = $row['service_name'];
        $service_price = $row['price'];
    } else {
        $error = 'Selected service could not be found. Please go back and select again.';
    }
}

// ---- Insert the appointment ----
if (!$error) {
    $sql = "INSERT INTO appointments (user_id, staff_id, service_id, appointment_date, appointment_time, status)
            VALUES (?, ?, ?, ?, ?, 'pending')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiiss", $user_id, $staff_id, $service_id, $appointment_date, $appointment_time);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['last_booking'] = [
            'stylist' => $staff_name,
            'service' => $service_name,
            'price' => $service_price,
            'date' => $appointment_date,
            'time' => $appointment_time,
        ];
        mysqli_stmt_close($stmt);
        header("Location: booking-success.php");
        exit();
    } else {
        $error = 'Something went wrong while saving your booking: ' . mysqli_error($conn);
        mysqli_stmt_close($stmt);
    }
}

// ---- If we reach here, something failed - show the error ----
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Booking Failed - Salon You</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', sans-serif; }
    body { background:#0f0f12; color:#f1f1f3; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:20px; }
    .error-box { background:#1a1a1f; border:1px solid #2c2d35; border-radius:14px; padding:35px; max-width:460px; width:100%; text-align:center; }
    .error-box i { font-size:40px; color:#ef4444; margin-bottom:15px; }
    .error-box h2 { margin-bottom:10px; }
    .error-box p { color:#9a9aa5; margin-bottom:20px; font-size:14px; }
    .error-box a { display:inline-block; background:#d4af37; color:#1a1a1a; padding:11px 26px; border-radius:8px; text-decoration:none; font-weight:600; }
</style>
</head>
<body>
    <div class="error-box">
        <i class="fas fa-exclamation-circle"></i>
        <h2>Booking Failed</h2>
        <p><?php echo htmlspecialchars($error); ?></p>
        <a href="frontend/services.php">Back to Services</a>
    </div>
</body>
</html>