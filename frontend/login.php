<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Already logged in -> go to home page
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Salon You</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- style.css first, register.css last so the auth styles win (same stylesheet as the register page) -->
    <link rel="stylesheet" href="frontend-css/style.css">
    <link rel="stylesheet" href="frontend-css/register.css?v=<?php echo time(); ?>">
</head>
<body class="auth-body">

    <div class="auth-container">

        <!-- LEFT: Luxury visual panel (hidden on mobile) -->
        <div class="auth-visual" style="background-image: url('../uploads/images/salon/hero-bg.jpg');">
            <span class="auth-eyebrow">Welcome back</span>

            <div class="auth-visual-body">
                <h2>Your Style Journey <span>Continues Here</span></h2>
                <p>Sign in to manage your appointments, check today's queue and book your next visit. Experience the best ever salon service.</p>

                <ul class="auth-features">
                    <li><i class="fas fa-calendar-check"></i> Manage Your Appointments</li>
                    <li><i class="fas fa-list-ol"></i> Track Today's Queue</li>
                    <li><i class="fas fa-magic"></i> AI Hairstyle Preview</li>
                </ul>
            </div>
        </div>

        <!-- RIGHT: Login form -->
        <div class="auth-card">

            <!-- Header Section -->
            <div class="auth-header">
                <a href="index.php" class="logo">SALON <span class="gradient-text">YOU</span></a>
                <h2>Welcome Back!</h2>
                <p>Please enter your details to sign in.</p>
            </div>

            <!-- Alert Box -->
            <div id="alert-box" class="alert-box" style="display: none;"></div>

            <!-- Login Form -->
            <form id="login-form">
                <!-- Email Field -->
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" required placeholder="name@example.com">
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" id="password" name="password" class="form-control" required placeholder="Enter your password">
                        <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="btn-submit" class="btn btn-primary btn-block">
                    <i class="fas fa-spinner fa-spin" id="btn-spinner" style="display: none;"></i>
                    <span id="btn-text">Login</span>
                </button>
            </form>

            <!-- Footer Section -->
            <div class="auth-footer">
                <p>Don't have an account? <a href="register.php">Register Here</a></p>
            </div>

        </div>
    </div>

    <script src="frontend-java-script/login.js"></script>
</body>
</html>