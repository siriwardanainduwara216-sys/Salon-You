<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
$user_role    = $_SESSION['user_role'] ?? '';
$user_name    = $_SESSION['user_name'] ?? 'Guest User';

if (!isset($active_page)) {
    $active_page = '';
}
?>

<!-- HEADER SPECIFIC STYLING FIX (BLACK THEME) -->
<style>
    header.site-header {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 80px !important;
        display: grid !important;
        grid-template-columns: 220px 1fr 220px !important; /* Left Logo | Middle Links | Right Buttons */
        align-items: center !important;
        padding: 0 5% !important;
        background: #000000 !important; /* Pure Black Background */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5) !important;
        z-index: 999999 !important;
        box-sizing: border-box !important;
        border-bottom: 1px solid #1a1a1a !important;
    }

    header.site-header .logo-container {
        justify-self: start !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
        text-decoration: none !important;
    }

    header.site-header .logo-text {
        color: #e68f05 !important; /* Gold Accent Logo Text */
        font-weight: 800 !important;
        font-size: 1.3rem !important;
        letter-spacing: 1px !important;
    }

    header.site-header .main-nav {
        justify-self: center !important;
        display: flex !important;
        align-items: center !important;
        gap: 32px !important;
    }

    header.site-header .main-nav .nav-link {
        color: #ffffff !important; /* Pure White Links */
        text-decoration: none !important;
        font-weight: 600 !important;
        font-size: 0.95rem !important;
        transition: color 0.3s ease !important;
        position: relative !important;
        padding-bottom: 4px !important;
    }

    header.site-header .main-nav .nav-link:hover,
    header.site-header .main-nav .nav-link.active {
        color: #e68f05 !important; /* Active / Hover Link Color */
    }

    /* Active Nav Link Underline Indicator */
    header.site-header .main-nav .nav-link.active::after,
    header.site-header .main-nav .nav-link:hover::after {
        content: '' !important;
        position: absolute !important;
        bottom: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 2px !important;
        background-color: #e68f05 !important;
        border-radius: 2px !important;
    }

    header.site-header .header-actions {
        justify-self: end !important;
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
    }

    /* LOGIN BUTTON STYLING (GOLD/ORANGE) */
    header.site-header .btn-login {
        background: #e68f05 !important;
        color: #ffffff !important;
        padding: 8px 18px !important;
        border-radius: 6px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        transition: all 0.3s ease !important;
        border: 1px solid #e68f05 !important;
    }

    header.site-header .btn-login:hover {
        background: #c87a02 !important;
        border-color: #c87a02 !important;
    }

    /* REGISTER BUTTON STYLING */
    header.site-header .btn-register {
        background: transparent !important;
        color: #ffffff !important;
        padding: 8px 18px !important;
        border-radius: 6px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        border: 1px solid #ffffff !important;
        transition: all 0.3s ease !important;
    }

    header.site-header .btn-register:hover {
        background: #ffffff !important;
        color: #000000 !important;
    }
</style>

<!-- MAIN NAVIGATION BAR -->
<header class="site-header">
    <!-- LEFT: Logo -->
    <a href="index.php" class="logo-container">
        <img src="logo/logo.png" alt="Salon You Logo" class="site-logo" onerror="this.src='../logo/logo.png'" style="height: 42px; width: auto;">
        <span class="logo-text">SALON YOU</span>
    </a>

   <!-- CENTER: Nav Links -->
<nav class="main-nav">
    <a href="index.php" class="nav-link <?php echo ($active_page == 'home') ? 'active' : ''; ?>">Home</a>
    <a href="services.php" class="nav-link <?php echo ($active_page == 'services') ? 'active' : ''; ?>">Services</a>
    <a href="gallery.php" class="nav-link <?php echo ($active_page == 'gallery') ? 'active' : ''; ?>">Gallery</a>
    <a href="about.php" class="nav-link <?php echo ($active_page == 'about') ? 'active' : ''; ?>">About Us</a>
    <a href="contact.php" class="nav-link <?php echo ($active_page == 'contact') ? 'active' : ''; ?>">Contact</a>
</nav>

    <!-- RIGHT: Action Buttons / Profile -->
    <div class="header-actions">
        <?php if (!$is_logged_in): ?>
            <a href="login.php" class="btn-login">Login</a>
            <a href="register.php" class="btn-register">Register</a>
        <?php else: ?>
            <div class="user-profile-badge">
                <span class="uname" style="color: #ffffff; margin-right: 10px;"><?php echo htmlspecialchars($user_name); ?></span>
                <a href="../backend/logout.php" class="btn-register" title="Logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        <?php endif; ?>
    </div>
</header>