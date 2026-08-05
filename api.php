<?php
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthenticated access request.']);
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

switch ($action) {
    case 'get_services':
        $query = "SELECT * FROM services";
        $res = mysqli_query($conn, $query);
        $services = mysqli_fetch_all($res, MYSQLI_ASSOC);
        echo json_encode($services);
        break;

    case 'book_appointment':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') exit;

        $service_id = intval($_POST['service_id'] ?? 0);
        $stylist = sanitize_input($_POST['stylist_name'] ?? '');
        $date = sanitize_input($_POST['appointment_date'] ?? '');
        $time = sanitize_input($_POST['appointment_time'] ?? '');

        if ($service_id === 0 || empty($stylist) || empty($date) || empty($time)) {
            echo json_encode(['success' => false, 'message' => 'Missing operational parameters.']);
            exit;
        }

        $query = "INSERT INTO appointments (user_id, service_id, stylist_name, appointment_date, appointment_time) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iisss", $user_id, $service_id, $stylist, $date, $time);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'message' => 'Appointment locked successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database save failure.']);
        }
        break;

    case 'customer_dashboard':
        if ($user_role !== 'customer') exit;

        $query = "SELECT a.*, s.title, s.price FROM appointments a JOIN services s ON a.service_id = s.id WHERE a.user_id = ? ORDER BY a.appointment_date ASC LIMIT 1";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $appointment = mysqli_stmt_get_result($stmt);

        echo json_encode([
            'loyalty_points' => 250,
            'next_appointment' => mysqli_fetch_assoc($appointment)
        ]);
        break;

    case 'staff_dashboard':
        // Updated 'staff' to 'employee' to match database role enum
        if ($user_role !== 'employee' && $user_role !== 'admin') exit;

        $query = "SELECT a.*, u.name as client_name, s.title FROM appointments a JOIN users u ON a.user_id = u.id JOIN services s ON a.service_id = s.id WHERE a.appointment_date = CURDATE()";
        $res = mysqli_query($conn, $query);
        $today_jobs = mysqli_fetch_all($res, MYSQLI_ASSOC);

        echo json_encode([
            'total_load' => count($today_jobs),
            'schedule' => $today_jobs
        ]);
        break;

    case 'admin_dashboard':
        if ($user_role !== 'admin') exit;

        $rev_res = mysqli_query($conn, "SELECT SUM(s.price) as revenue FROM appointments a JOIN services s ON a.service_id = s.id WHERE a.appointment_date = CURDATE()");
        $rev_data = mysqli_fetch_assoc($rev_res);

        $count_res = mysqli_query($conn, "SELECT COUNT(id) as total FROM appointments WHERE appointment_date = CURDATE()");
        $count_data = mysqli_fetch_assoc($count_res);

        echo json_encode([
            'revenue' => $rev_data['revenue'] ?? 0.00,
            'bookings_count' => $count_data['total'] ?? 0
        ]);
        break;

    default:
        echo json_encode(['error' => 'Endpoint mapping undefined.']);
        break;
}
