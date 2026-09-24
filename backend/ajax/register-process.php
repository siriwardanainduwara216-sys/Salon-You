<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';

global $conn;
if (!$conn && isset($GLOBALS['conn'])) {
    $conn = $GLOBALS['conn'];
}

$response = ['status' => 'error', 'message' => 'Invalid request method.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    
    $role = 'customer';

    $full_name = trim($_POST['full_name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $phone     = trim($_POST['phone'] ?? '');
    $password  = $_POST['password'] ?? '';

    // Required Fields 
    if (empty($full_name) || empty($email) || empty($phone) || empty($password)) {
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'Please fill in all required fields.']);
        exit;
    }

    //  Email format 
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
        exit;
    }

    
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
    } else {
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'Database query preparation failed.']);
        exit;
    }

    // 4. OTP and Password Hash
    $otp_code        = sprintf("%06d", mt_rand(1, 999999));
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 5. User Database Save 
    $insert_query = "INSERT INTO users (name, email, phone, password, role, otp_code, is_verified) VALUES (?, ?, ?, ?, ?, ?, 1)";
    $insert_stmt  = mysqli_prepare($conn, $insert_query);

    if (!$insert_stmt) {
        ob_end_clean();
        echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . mysqli_error($conn)]);
        exit;
    }

    mysqli_stmt_bind_param($insert_stmt, "ssssss", $full_name, $email, $phone, $hashed_password, $role, $otp_code);

    if (mysqli_stmt_execute($insert_stmt)) {
        $new_user_id = mysqli_insert_id($conn);

        // 6. PHPMailer  OTP Mail
        require_once __DIR__ . '/../mailer.php';
        $mail_result = send_otp_email($email, $full_name, $otp_code);

        if ($mail_result['status'] === true) {
            $_SESSION['temp_user_id']    = $new_user_id;
            $_SESSION['temp_user_email'] = $email;
            $_SESSION['temp_user_role']  = $role;

            $response = [
                'status'    => 'success',
                'message'   => 'Registration successful! Verification code sent to your email.',
                'debug_otp' => $otp_code, 
                'redirect'  => 'otp-verify.php'
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

ob_end_clean();
echo json_encode($response);
exit;
?>