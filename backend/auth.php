<?php
session_start();
require_once 'config.php'; 
header('Content-Type: application/json');
$action = isset($_GET['action']) ? $_GET['action'] : '';

//login test
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'login') {
    
    $role_simulation = isset($_POST['role']) ? $_POST['role'] : '';
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

    // Standard login
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                echo json_encode([
                    'success' => true, 
                    'role' => $user['role'], 
                    'name' => $user['name']
                ]);
                exit;
            }
        }
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error during authentication']);
    }
    exit;
}

// -----------------------------
// REGISTER HANDLING
// -----------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'register') {
    
    $name = isset($_POST['name']) ? $_POST['name'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $role = isset($_POST['role']) ? $_POST['role'] : 'customer';

    // Validate required fields
    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit;
    }

    // Check if email already exists
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

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user
    $insertQuery = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
    $insertStmt = mysqli_prepare($conn, $insertQuery);

    if ($insertStmt) {
        mysqli_stmt_bind_param($insertStmt, "ssss", $name, $email, $hashedPassword, $role);
        
        if (mysqli_stmt_execute($insertStmt)) {
            echo json_encode(['success' => true, 'message' => 'Account created successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to register account']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Database connection processing failure']);
    }
    exit;
}
?>
