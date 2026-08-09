<?php
ob_start(); // JSON Break වීම වැළැක්වීමට Output Buffering ආරම්භ කිරීම

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Include configuration file
require_once __DIR__ . '/../config.php';

// Database Connection checking
global $conn;
if (!$conn && isset($GLOBALS['conn'])) {
    $conn = $GLOBALS['conn'];
}

$response = ['status' => 'error', 'message' => 'Invalid request method.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // 1. Required Inputs Validation
    if (empty($email) || empty($password)) {
        ob_end_clean();
        echo json_encode([
            'status'  => 'error',
            'message' => 'Please enter both email address and password.'
        ]);
        exit;
    }

    // 2. Find User in Database
    $sql  = "SELECT id, name, email, password, role, is_verified FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {

            // 3. Password Verification Check
            if (password_verify($password, $user['password'])) {

                // 4. OTP Verification Check (Verify වී නැත්නම් OTP Page එකට Redirect කරයි)
                if ((int)$user['is_verified'] !== 1) {
                    $_SESSION['temp_user_id']    = $user['id'];
                    $_SESSION['temp_user_email'] = $user['email'];
                    $_SESSION['temp_user_role']  = $user['role'];

                    mysqli_stmt_close($stmt);
                    ob_end_clean();
                    echo json_encode([
                        'status'   => 'warning',
                        'message'  => 'Your account is not verified yet. Redirecting to OTP verification...',
                        'redirect' => 'otp-verify.php'
                    ]);
                    exit;
                }

                // 5. Successful Login - Set Active Session Variables
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                // 6. Role-Based Redirection Page Destination
                $redirect_page = ($user['role'] === 'admin') ? 'admin-dashboard.php' : 'index.php';

                mysqli_stmt_close($stmt);
                ob_end_clean();
                echo json_encode([
                    'status'   => 'success',
                    'message'  => 'Login successful! Redirecting...',
                    'redirect' => $redirect_page
                ]);
                exit;

            } else {
                // Invalid Password
                mysqli_stmt_close($stmt);
                ob_end_clean();
                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Invalid email or password.'
                ]);
                exit;
            }

        } else {
            // User Email Not Found
            mysqli_stmt_close($stmt);
            ob_end_clean();
            echo json_encode([
                'status'  => 'error',
                'message' => 'Invalid email or password.'
            ]);
            exit;
        }

    } else {
        ob_end_clean();
        echo json_encode([
            'status'  => 'error',
            'message' => 'Database Query Error: ' . mysqli_error($conn)
        ]);
        exit;
    }

} else {
    ob_end_clean();
    echo json_encode($response);
    exit;
}
?>