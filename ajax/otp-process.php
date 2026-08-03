<?php
require_once '../config.php';

/** @var mysqli $conn */

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Invalid request.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_otp = trim($_POST['otp_code'] ?? '');
    $user_id     = $_SESSION['temp_user_id'] ?? 0;

    if (empty($entered_otp) || $user_id === 0) {
        $response['message'] = 'Please enter the verification code.';
        echo json_encode($response);
        exit;
    }

    // 1. User ගේ Database OTP එක නිවැරදිදැයි පරීක්ෂා කිරීම
    $stmt = $conn->prepare("SELECT id, name, role FROM users WHERE id = ? AND otp_code = ?");
    $stmt->bind_param("is", $user_id, $entered_otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // 2. User Status එක 'active' කර OTP Code එක Clear කිරීම
        $update_stmt = $conn->prepare("UPDATE users SET status = 'active', otp_code = NULL WHERE id = ?");
        $update_stmt->bind_param("i", $user_id);
        
        if ($update_stmt->execute()) {
            // Permanent User Session සකස් කිරීම (ලොග් වී ඇති බවට)
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Temporary Sessions අයින් කිරීම
            unset($_SESSION['temp_user_id']);
            unset($_SESSION['temp_user_phone']);
            unset($_SESSION['temp_user_role']);

            // Role එක අනුව Redirect වන ස්ථානය තීරණය කිරීම
            $redirect_page = 'index.php'; // Default Home Page
            if ($user['role'] === 'employee') {
                $redirect_page = 'employee-dashboard.php';
            } elseif ($user['role'] === 'admin') {
                $redirect_page = 'admin-dashboard.php';
            }

            $response = [
                'status'   => 'success',
                'message'  => 'Account verified successfully! Redirecting...',
                'redirect' => $redirect_page
            ];
        } else {
            $response['message'] = 'Database error: Could not verify account.';
        }
    } else {
        $response['message'] = 'Invalid OTP code. Please check and try again.';
    }
}

echo json_encode($response);