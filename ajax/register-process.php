<?php
ob_start();
session_start();
header('Content-Type: application/json');

require_once '../config.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role      = trim($_POST['role'] ?? 'customer');
    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $password  = $_POST['password'] ?? '';

    // Validation
    if (empty($full_name) || empty($email) || empty($phone) || empty($password)) {
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
        exit;
    }

    // Check if email exists
    $check_stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            mysqli_stmt_close($check_stmt);
            ob_end_clean();
            echo json_encode(['status' => 'error', 'message' => 'Email address is already registered.']);
            exit;
        }
        mysqli_stmt_close($check_stmt);
    }

    // OTP & Password Hash
    $otp_code        = sprintf("%06d", mt_rand(1, 999999));
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Save User to Database
    $insert_query = "INSERT INTO users (name, email, phone, password, role, otp_code, is_verified) VALUES (?, ?, ?, ?, ?, ?, 0)";
    $insert_stmt  = mysqli_prepare($conn, $insert_query);

    // SQL Error Handling
    if (!$insert_stmt) {
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . mysqli_error($conn)]);
        exit;
    }

    mysqli_stmt_bind_param($insert_stmt, "ssssss", $full_name, $email, $phone, $hashed_password, $role, $otp_code);

    if (mysqli_stmt_execute($insert_stmt)) {
        $new_user_id = mysqli_insert_id($conn);

        // ✉️ Send Email
        require_once '../mailer.php';
        $mail_result = send_otp_email($email, $full_name, $otp_code);

        if ($mail_result['status'] === true) {
            $_SESSION['temp_user_id']    = $new_user_id;
            $_SESSION['temp_user_email'] = $email;
            $_SESSION['temp_user_role']  = $role;

            $response = [
                'status'   => 'success',
                'message'  => 'Registration successful! Verification code sent to your email.',
                'redirect' => 'otp-verify.php'
            ];
        } else {
            $response = [
                'status'  => 'error',
                'message' => 'Email Failed: ' . $mail_result['error']
            ];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Database Query Execution Failed: ' . mysqli_error($conn)];
    }
    mysqli_stmt_close($insert_stmt);
}

// Clean Output Buffer & Return Clean JSON
ob_end_clean();
echo json_encode($response);
exit;