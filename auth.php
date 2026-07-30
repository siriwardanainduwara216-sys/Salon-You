<?php
session_start();
require_once 'config.php'; // This loads $conn

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

// --- 1. HANDLING SYSTEM LOGIN ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    $role_simulation = $_POST['role'] ?? '';
    
    // Check if a quick simulation button was clicked
    if (!empty($role_simulation) && in_array($role_simulation, ['customer', 'staff', 'admin'])) {
        $_SESSION['user_id'] = 999; 
        $_SESSION['user_name'] = 'Test ' . ucfirst($role_simulation);
        $_SESSION['user_role'] = $role_simulation;

        echo json_encode([
            'success' => true, 
            'role' => $role_simulation, 
            'name' => $_SESSION['user_name']
        ]);
        exit;
    }

    // Standard fallback login form fields
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $password = $_POST['password'] ?? '';

    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query); 
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                echo json_encode(['success' => true, 'role' => $user['role'], 'name' => $user['name']]);
                exit;
            }
        }
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error during authentication']);
    }
    exit;
}

// --- 2. HANDLING ACCOUNT REGISTRATION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'register') {
    $name = isset($_POST['name']) ? mysqli_real_escape_string($conn, $_POST['name']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $password = $_POST['password'] ?? '';
    $role = isset($_POST['role']) ? mysqli_real_escape_string($conn, $_POST['role']) : 'customer'; 

    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All validation fields are required']);
        exit;
    }

    $checkQuery = "SELECT id FROM users WHERE email = ?";
    $checkStmt = mysqli_prepare($conn, $checkQuery);
    if ($checkStmt) {
        mysqli_stmt_bind_param($checkStmt, "s", $email);
        mysqli_stmt_execute($checkStmt);
        mysqli_stmt_store_result($checkStmt);
        
        if (mysqli_stmt_num_rows($checkStmt) > 0) {
            echo json_encode(['success' => false, 'message' => 'Email address is already registered']);
            exit;
        }
    }

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $insertQuery = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $insertStmt = mysqli_prepare($conn, $insertQuery);
    
    if ($insertStmt) {
        mysqli_stmt_bind_param($insertStmt, "ssss", $name, $email, $hashedPassword, $role);
        if (mysqli_stmt_execute($insertStmt)) {
            echo json_encode(['success' => true, 'message' => 'Account created successfully! You can now log in.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to register account credentials']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database connection processing failure']);
    }
    exit;
}
?>