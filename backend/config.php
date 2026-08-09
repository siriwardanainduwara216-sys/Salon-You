<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'salon_you';

// Connect to database
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Show error if connection fails
if (!$conn) {
    die("Unable to connect to database: " . mysqli_connect_error());
}

// Set charset for proper character display
mysqli_set_charset($conn, "utf8mb4");


// Sanitize user input (security)
function sanitize_input($data) {
    global $conn;
    $data = trim($data); // Remove extra spaces
    return mysqli_real_escape_string($conn, $data); // Prevent SQL injection
}


// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}


// Redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: index.php");
        exit();
    }
}


// Check user role (Admin/Customer)
function requireRole($role) {
    requireLogin(); // Must be logged in first
    
    if ($_SESSION['user_role'] !== $role) {
        header("Location: index.php");
        exit();
    }
}


// Send JSON response (for AJAX)
function jsonResponse($success, $message = "") {
    header('Content-Type: application/json');
    
    $response = [
        'success' => $success,
        'message' => $message
    ];
    
    echo json_encode($response);
    exit();
}
?>
