<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and has permission (Admin or Staff)
$is_logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
$user_name = $_SESSION['user_name'] ?? 'Staff Member';

if (!$is_logged_in || ($user_role != 'admin' && $user_role != 'staff')) {
    header("Location: login.php");
    exit();
}

// Include database configuration
require_once 'config.php';

// Safe PDO connection fallback using config variables
try {
    $pdo = new PDO("mysql:host=" . (defined('DB_HOST') ? DB_HOST : 'localhost') . ";dbname=" . (defined('DB_NAME') ? DB_NAME : 'salon_db') . ";charset=utf8mb4", 
                   defined('DB_USER') ? DB_USER : 'root', 
                   defined('DB_PASS') ? DB_PASS : '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$errorMessage = "";
$successMessage = "";

// Handle Status Updates from Admin/Staff Dashboard
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $queueId = $_POST['queue_id'];
    $newStatus = $_POST['new_status']; // 'In-Service', 'Completed', or 'Cancelled'
    
    $updateStmt = $pdo->prepare("UPDATE salon_queue SET status = ? WHERE id = ?");
    $updateStmt->execute([$newStatus, $queueId]);
    header("Location: queue-dashboard.php");
    exit();
}

// Handle Adding a New Customer to the Queue
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_queue'])) {
    $customerName = trim($_POST['customerName'] ?? '');
    $serviceName  = trim($_POST['serviceName'] ?? 'Haircut');

    if (empty($customerName)) {
        $errorMessage = "Please enter the customer's name.";
    } else {
        $today = date('Y-m-d');
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM salon_queue WHERE DATE(created_at) = ?");
        $stmt->execute([$today]);
        $count = $stmt->fetchColumn() + 1;
        $tokenNumber = 'A-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        $insertStmt = $pdo->prepare("INSERT INTO salon_queue (customer_name, service_name, token_number, status) VALUES (?, ?, ?, 'Waiting')");
        if ($insertStmt->execute([$customerName, $serviceName, $tokenNumber])) {
            $successMessage = "Successfully added! Assigned Token: <strong>{$tokenNumber}</strong>";
        } else {
            $errorMessage = "Failed to add to queue.";
        }
    }
}

// Fetch Currently Serving
$servingStmt = $pdo->query("SELECT * FROM salon_queue WHERE status = 'In-Service' ORDER BY created_at ASC");
$nowServing = $servingStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch Waiting Queue
$waitingStmt = $pdo->query("SELECT * FROM salon_queue WHERE status = 'Waiting' ORDER BY created_at ASC");
$upNextQueue = $waitingStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Queue Dashboard - Salon You</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            background-color: var(--bg-main, #0F172A);
            color: var(--text-main, #F8FAFC);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .dashboard-card {
            background: var(--surface-dark, #1E293B);
            border: 1px solid var(--border-color, #334155);
            border-radius: 1.25rem;
        }
        .token-badge {
            font-size: 2.2rem;
            font-weight: 800;
            letter-spacing: 0.05rem;
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="site-header">
        <a href="index.php" class="logo-container">
            <img src="logo/logo.png" alt="Salon You Logo" class="site-logo">
            <span class="logo-text">SALON YOU</span>
        </a>

        <nav class="main-nav">
            <a href="index.php" class="nav-link">Home</a>
            <a href="queue-dashboard.php" class="nav-link active">Queue Dashboard</a>
            <a href="track-queue.php" class="nav-link">Track View</a>
        </nav>

        <div class="header-actions">
            <div class="user-profile">
                <span class="user-name"><?php echo htmlspecialchars($user_name); ?> (<?php echo ucfirst($user_role); ?>)</span>
                <a href="logout.php" class="btn-logout" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
            </div>
        </div>
    </header>

    <div class="container py-5" style="max-width: 1100px;">
        
        <!-- Header Section -->
        <div class="row mb-4 text-center">
            <div class="col-12">
                <h1 class="fw-bold mb-1">Live Salon <span class="gradient-text">Queue Dashboard</span></h1>
                <p style="color: var(--text-muted, #CBD5E1);">Manage walk-ins, track tokens, and update service progress dynamically.</p>
                <a href="track-queue.php" target="_blank" class="btn btn-outline-light btn-sm mt-2"><i class="bi bi-box-arrow-up-right me-1"></i> Open Customer Track View</a>
            </div>
        </div>

        <div class="row g-4">
            
            <!-- Left Side: Add Customer Form -->
            <div class="col-lg-4">
                <div class="dashboard-card shadow-sm p-4">
                    <div>
                        <h4 class="fw-bold mb-3"><i class="bi bi-person-plus me-2 text-warning"></i>New Walk-In</h4>
                        
                        <?php if (!empty($errorMessage)): ?>
                            <div class="alert alert-danger py-2 small rounded-3"><?php echo htmlspecialchars($errorMessage); ?></div>
                        <?php endif; ?>
                        <?php if (!empty($successMessage)): ?>
                            <div class="alert alert-success py-2 small rounded-3"><?php echo $successMessage; ?></div>
                        <?php endif; ?>

                        <form action="queue-dashboard.php" method="POST" autocomplete="off">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Customer Name</label>
                                <input type="text" name="customerName" class="form-control bg-dark text-white border-secondary shadow-none" placeholder="e.g. Sarah Smith" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-muted">Select Service</label>
                                <select name="serviceName" class="form-select bg-dark text-white border-secondary shadow-none">
                                    <option value="Haircut & Styling">Haircut & Styling</option>
                                    <option value="Manicure & Pedicure">Manicure & Pedicure</option>
                                    <option value="Hair Coloring">Hair Coloring</option>
                                    <option value="Facial Treatment">Facial Treatment</option>
                                </select>
                            </div>

                            <button type="submit" name="add_to_queue" class="btn btn-primary w-100 py-2 rounded-3 fw-semibold">Generate Token</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Side: Live Queue Board -->
            <div class="col-lg-8">
                
                <!-- Now Serving Card -->
                <div class="dashboard-card shadow-sm p-4 mb-4">
                    <div>
                        <h4 class="fw-bold mb-3 text-success"><i class="bi bi-soundwave me-2"></i>Now Serving</h4>
                        
                        <?php if (empty($nowServing)): ?>
                            <p class="text-muted small mb-0">No customers currently in service.</p>
                        <?php else: ?>
                            <div class="row g-3">
                                <?php foreach ($nowServing as $serving): ?>
                                    <div class="col-md-6">
                                        <div class="p-3 bg-dark rounded-4 border-start border-4 border-success d-flex flex-column justify-content-between h-100 border-secondary">
                                            <div>
                                                <div class="token-badge text-success mb-1"><?php echo htmlspecialchars($serving['token_number']); ?></div>
                                                <h5 class="fw-bold mb-0 text-white"><?php echo htmlspecialchars($serving['customer_name']); ?></h5>
                                                <span class="text-muted small"><?php echo htmlspecialchars($serving['service_name']); ?></span>
                                            </div>
                                            <div class="mt-3">
                                                <form method="POST" class="d-inline">
                                                    <input type="hidden" name="queue_id" value="<?php echo $serving['id']; ?>">
                                                    <input type="hidden" name="new_status" value="Completed">
                                                    <button type="submit" name="update_status" class="btn btn-sm btn-success w-100 rounded-3">Mark Completed</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Up Next / Waiting Queue -->
                <div class="dashboard-card shadow-sm p-4">
                    <div>
                        <h4 class="fw-bold mb-3 text-white"><i class="bi bi-clock-history me-2 text-warning"></i>Up Next (Waiting)</h4>

                        <?php if (empty($upNextQueue)): ?>
                            <p class="text-muted small mb-0">The queue is currently empty.</p>
                        <?php else: ?>
                            <div class="list-group list-group-flush bg-transparent">
                                <?php foreach ($upNextQueue as $index => $waiting): ?>
                                    <div class="list-group-item bg-transparent border-secondary d-flex justify-content-between align-items-center py-3 px-0 text-white">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-secondary rounded-pill me-3 px-3 py-2">#<?php echo $index + 1; ?></span>
                                            <div>
                                                <h6 class="fw-bold mb-0 text-white"><?php echo htmlspecialchars($waiting['customer_name']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($waiting['service_name']); ?></small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold"><?php echo htmlspecialchars($waiting['token_number']); ?></span>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="queue_id" value="<?php echo $waiting['id']; ?>">
                                                <input type="hidden" name="new_status" value="In-Service">
                                                <button type="submit" name="update_status" class="btn btn-sm btn-light rounded-pill px-3 fw-semibold">Call In</button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>