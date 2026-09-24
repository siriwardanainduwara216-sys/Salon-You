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

$user_id = (int) $_SESSION['user_id'];
$staff_id = (int) ($_POST['staff_id'] ?? 0);
$appointment_date = trim($_POST['appointment_date'] ?? '');
$appointment_time = trim($_POST['appointment_time'] ?? '');

// ---- Selected services: service_ids[] (multi) or service_id (old single form) ----
$raw_ids = $_POST['service_ids'] ?? [];
if (!is_array($raw_ids)) {
    $raw_ids = [$raw_ids];
}
if (empty($raw_ids) && isset($_POST['service_id'])) {
    $raw_ids = [$_POST['service_id']];
}

$service_ids = [];
foreach ($raw_ids as $rid) {
    $id = (int) $rid;
    if ($id > 0 && !in_array($id, $service_ids, true)) {
        $service_ids[] = $id;
    }
}

$MAX_SERVICES = 10;
$error = '';

if ($staff_id <= 0 || empty($service_ids) || $appointment_date === '' || $appointment_time === '') {
    $error = 'Missing booking details. Please try again.';
} elseif (count($service_ids) > $MAX_SERVICES) {
    $error = 'You can book up to ' . $MAX_SERVICES . ' services at once.';
}

// ---- Validate date and time format ----
if (!$error) {
    $date_obj = DateTime::createFromFormat('Y-m-d', $appointment_date);
    if (!$date_obj || $date_obj->format('Y-m-d') !== $appointment_date) {
        $error = 'Invalid date. Please try again.';
    } elseif ($appointment_date < date('Y-m-d')) {
        $error = 'You cannot book an appointment in the past.';
    } elseif (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $appointment_time)) {
        $error = 'Invalid time. Please try again.';
    }
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

// ---- Verify every service exists (price + duration always come from the database) ----
$services = [];
if (!$error) {
    $sql = "SELECT service_name, price, duration_mins FROM services WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $current_id = 0;
    mysqli_stmt_bind_param($stmt, "i", $current_id);

    foreach ($service_ids as $sid) {
        $current_id = $sid;
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$row) {
            $error = 'One of the selected services could not be found. Please go back and select again.';
            break;
        }

        $duration = (int) $row['duration_mins'];
        if ($duration <= 0) {
            $duration = 30; // fallback if a service has no duration set
        }

        $services[] = [
            'id' => $sid,
            'name' => $row['service_name'],
            'price' => (float) $row['price'],
            'duration' => $duration,
        ];
    }
    mysqli_stmt_close($stmt);
}

// ---- Build the schedule: services run back-to-back with the same stylist ----
$slots = [];
if (!$error) {
    $cursor = strtotime($appointment_date . ' ' . $appointment_time);

    foreach ($services as $svc) {
        $start = $cursor;
        $end = $start + ($svc['duration'] * 60);

        $slots[] = [
            'service' => $svc,
            'start_ts' => $start,
            'end_ts' => $end,
            'start_time' => date('H:i', $start),
        ];
        $cursor = $end;
    }
}

// ---- Verify each appointment starts within business hours (9 AM - 5 PM) ----
if (!$error) {
    foreach ($slots as $slot) {
        if ($slot['start_time'] < '09:00' || $slot['start_time'] > '17:00') {
            $error = 'Appointments are only available between 9:00 AM and 5:00 PM. '
                   . '"' . $slot['service']['name'] . '" would start at ' . date('g:i A', $slot['start_ts'])
                   . '. Please choose an earlier start time or fewer services.';
            break;
        }
    }
}

// ---- Check the salon isn't closed on this date (Poya days, holidays) ----
if (!$error) {
    $sql = "SELECT reason FROM salon_closed_dates WHERE closed_date = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $appointment_date);
    mysqli_stmt_execute($stmt);
    $closed_row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    if ($closed_row) {
        $error = 'The salon is closed on this date (' . $closed_row['reason'] . '). Please choose another date.';
    }
}

// ---- Check the stylist is free for every time range (overlap check) ----
if (!$error) {
    $sql = "SELECT a.appointment_time, s.duration_mins
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            WHERE a.staff_id = ? AND a.appointment_date = ?
            AND a.status IN ('pending', 'confirmed')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "is", $staff_id, $appointment_date);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $existing = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $ex_start = strtotime($appointment_date . ' ' . $row['appointment_time']);
        $ex_duration = (int) $row['duration_mins'];
        if ($ex_duration <= 0) {
            $ex_duration = 30;
        }
        $existing[] = ['start' => $ex_start, 'end' => $ex_start + ($ex_duration * 60)];
    }
    mysqli_stmt_close($stmt);

    foreach ($slots as $slot) {
        foreach ($existing as $ex) {
            if ($slot['start_ts'] < $ex['end'] && $slot['end_ts'] > $ex['start']) {
                $error = 'This time is already booked for the selected stylist: "'
                       . $slot['service']['name'] . '" (' . date('g:i A', $slot['start_ts'])
                       . ' - ' . date('g:i A', $slot['end_ts'])
                       . ') clashes with an existing booking. Please choose a different start time or stylist.';
                break 2;
            }
        }
    }
}

// ---- Insert all appointments together (all or nothing) ----
if (!$error) {
    try {
        mysqli_begin_transaction($conn);

        $sql = "INSERT INTO appointments (user_id, staff_id, service_id, appointment_date, appointment_time, status)
                VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = mysqli_prepare($conn, $sql);

        $b_service_id = 0;
        $b_time = '';
        mysqli_stmt_bind_param($stmt, "iiiss", $user_id, $staff_id, $b_service_id, $appointment_date, $b_time);

        foreach ($slots as $slot) {
            $b_service_id = $slot['service']['id'];
            $b_time = $slot['start_time'];

            if (!mysqli_stmt_execute($stmt)) {
                throw new Exception(mysqli_stmt_error($stmt));
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_commit($conn);

        // ---- Data for the success page ----
        $names = [];
        $total = 0;
        $items = [];
        foreach ($slots as $slot) {
            $names[] = $slot['service']['name'];
            $total += $slot['service']['price'];
            $items[] = [
                'service' => $slot['service']['name'],
                'price' => $slot['service']['price'],
                'time' => $slot['start_time'],
                'end_time' => date('H:i', $slot['end_ts']),
            ];
        }

        $_SESSION['last_booking'] = [
            'stylist' => $staff_name,
            'service' => implode(', ', $names),   // old key: all service names joined
            'price' => $total,                    // old key: total price
            'date' => $appointment_date,
            'time' => $slots[0]['start_time'],    // old key: start time of the first service
            'items' => $items,                    // new: per-service schedule
        ];

        header("Location: booking-success.php");
        exit();
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        error_log('Booking failed: ' . $e->getMessage());
        $error = 'Something went wrong while saving your booking. Please try again.';
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