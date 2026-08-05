<?php
// ==========================================
// Salon You - Database Configuration
// ==========================================
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict',
        'cookie_secure' => false // Change to true when HTTPS is enabled
    ]);
}
// ==========================================
// Database Credentials
// ==========================================
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'salon_you');

// ==========================================
// Database Connection
// ==========================================
$conn = mysqli_connect(
    DB_HOST,
    DB_USER,
    DB_PASS,
    DB_NAME
);
if (!$conn) {
    die(
        "Database Connection Failed: " .
        mysqli_connect_error()
    );
}
mysqli_set_charset($conn, "utf8mb4");

// ==========================================
// Input Sanitization
// ==========================================
function sanitize_input($data)
{
    global $conn;
    return mysqli_real_escape_string(
        $conn,
        trim($data)
    );
}

// ==========================================
// Authentication Helpers
// ==========================================
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}
function currentUserId()
{
    return $_SESSION['user_id'] ?? null;
}
function currentUserRole()
{
    return $_SESSION['user_role'] ?? null;
}
function currentUserName()
{
    return $_SESSION['user_name'] ?? 'Guest';
}

// ==========================================
// Role Protection
// ==========================================
function requireLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: index.php");
        exit;
    }
}
function requireRole($role)
{
    requireLogin();
    if ($_SESSION['user_role'] !== $role) {
        header("Location: index.php");
        exit;
    }
}

// ==========================================
// JSON Response Helper
// ==========================================
function jsonResponse($success, $message = '', $extra = [])
{
    header('Content-Type: application/json');
    echo json_encode(
        array_merge(
            [
                'success' => $success,
                'message' => $message
            ],
            $extra
        )
    );
    exit;
}
?>