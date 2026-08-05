<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
$user_name = $_SESSION['user_name'] ?? 'Guest User';

// Include your system's database configuration
require_once 'config.php';

// Fallback or direct PDO initialization if config uses standard variables
try {
    $pdo = new PDO("mysql:host=" . (defined('DB_HOST') ? DB_HOST : $host) . ";dbname=" . (defined('DB_NAME') ? DB_NAME : $dbname) . ";charset=utf8mb4", 
                   defined('DB_USER') ? DB_USER : $username, 
                   defined('DB_PASS') ? DB_PASS : $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Fallback if $conn from config.php is mysqli, we initialize PDO safely
    $pdo = null; 
}

$searchToken = trim($_GET['token'] ?? '');
$customerData = null;
$queuePosition = 0;
$estimatedWait = 0;
$isNextInLine = false;

if (!empty($searchToken) && $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM salon_queue WHERE token_number = ?");
    $stmt->execute([$searchToken]);
    $customerData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($customerData && $customerData['status'] == 'Waiting') {
        $posStmt = $pdo->prepare("SELECT COUNT(*) FROM salon_queue WHERE status = 'Waiting' AND created_at < ?");
        $posStmt->execute([$customerData['created_at']]);
        $queuePosition = $posStmt->fetchColumn() + 1;
        $estimatedWait = $queuePosition * 20;

        if ($queuePosition == 1) {
            $isNextInLine = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Queue Status - Salon You</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .queue-tracker-card {
            background: var(--surface-dark, #1E293B);
            border: 1px solid var(--border-color, #334155);
            border-radius: 1.25rem;
            color: var(--text-main, #F8FAFC);
        }
    </style>
</head>
<body>

    <!-- Header Navigation (Consistent with your about.php / index.php) -->
    <header class="site-header">
        <a href="index.php" class="logo-container">
            <img src="logo/logo.png" alt="Salon You Logo" class="site-logo">
            <span class="logo-text">SALON YOU</span>
        </a>

        <nav class="main-nav">
            <a href="index.php" class="nav-link">Home</a>
            <a href="services" class="nav-link">Services</a>
            <a href="gallery" class="nav-link">Gallery</a>
            <a href="about.php" class="nav-link">About Us</a>
            <a href="contact" class="nav-link">Contact</a>
        </nav>

        <div class="header-actions">
            <?php if (!$is_logged_in): ?>
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-register">Register</a>
            <?php else: ?>
                <div class="user-profile">
                    <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                    <a href="logout.php" class="btn-logout" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- Queue Tracker Main Wrapper -->
    <div class="container py-5" style="max-width: 600px; min-height: 70vh;">
        <div class="text-center mb-4">
            <h1 class="fw-bold fs-2">Live Queue <span class="gradient-text">Tracker</span></h1>
            <p style="color: var(--text-muted, #CBD5E1);" class="small">Enter your assigned token number to check your current live status and wait time.</p>
        </div>

        <div class="queue-tracker-card p-4 shadow-lg">
            <form method="GET" action="" class="mb-4">
                <div class="input-group">
                    <input type="text" name="token" class="form-control bg-dark text-white border-secondary shadow-none py-2" placeholder="e.g. A-001" value="<?php echo htmlspecialchars($searchToken); ?>" required>
                    <button class="btn btn-primary px-4" type="submit"><i class="bi bi-search"></i> Search</button>
                </div>
            </form>

            <?php if (!empty($searchToken)): ?>
                <?php if (!$customerData): ?>
                    <div class="alert alert-warning text-center small mb-0 rounded-3">Token not found. Please check your token number.</div>
                <?php else: ?>
                    <?php if ($customerData['status'] == 'Completed'): ?>
                        <div class="alert alert-secondary text-center rounded-3 mb-4">
                            <h5 class="fw-bold mb-1"><i class="bi bi-check-circle-fill me-1"></i> Session is Over</h5>
                            <p class="mb-0 small">Thank you for visiting Salon You! Your service has been completed.</p>
                        </div>
                    <?php elseif ($customerData['status'] == 'In-Service'): ?>
                        <div class="alert alert-success text-center rounded-3 mb-4">
                            <h5 class="fw-bold mb-1"><i class="bi bi-soundwave me-1"></i> In Session</h5>
                            <p class="mb-0 small">You are currently being served. Enjoy your experience!</p>
                        </div>
                    <?php elseif ($isNextInLine): ?>
                        <div class="alert alert-warning text-center rounded-3 mb-4 border-warning">
                            <h5 class="fw-bold mb-1"><i class="bi bi-bell-fill me-1"></i> You Are Next!</h5>
                            <p class="mb-0 small">Get ready! Please head near the styling area shortly.</p>
                        </div>
                    <?php endif; ?>

                    <div class="p-3 bg-dark rounded-4 text-center border border-secondary">
                        <span class="text-uppercase fw-semibold" style="font-size: 0.75rem; color: var(--text-muted);">Token Number</span>
                        <h1 class="fw-bold gradient-text display-5 mb-2"><?php echo htmlspecialchars($customerData['token_number']); ?></h1>

                        <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($customerData['customer_name']); ?></p>
                        <p class="mb-3"><strong>Service:</strong> <?php echo htmlspecialchars($customerData['service_name']); ?></p>

                        <p class="mb-3"><strong>Current Status:</strong>
                            <span class="badge bg-<?php echo ($customerData['status'] == 'In-Service') ? 'success' : (($customerData['status'] == 'Completed') ? 'secondary' : 'warning text-dark'); ?> px-3 py-2 rounded-pill">
                                <?php echo $customerData['status']; ?>
                            </span>
                        </p>

                        <?php if ($customerData['status'] == 'Waiting'): ?>
                            <hr class="border-secondary">
                            <div class="row text-center g-2">
                                <div class="col-6">
                                    <span class="small" style="color: var(--text-muted);">Queue Position</span>
                                    <h4 class="fw-bold text-white">#<?php echo $queuePosition; ?></h4>
                                </div>
                                <div class="col-6">
                                    <span class="small" style="color: var(--text-muted);">Est. Wait Time</span>
                                    <h4 class="fw-bold" style="color: var(--you-amber, #F59E0B);"><?php echo $estimatedWait; ?> mins</h4>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="text-center mt-3">
            <a href="index.php" class="text-decoration-none small" style="color: var(--text-muted);"><i class="bi bi-arrow-left me-1"></i> Back to Home</a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-container py-4 text-center border-top border-secondary mt-auto">
        <div class="section-container">
            <p class="small mb-0" style="color: var(--text-muted);">&copy; 2026 Salon You. All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>