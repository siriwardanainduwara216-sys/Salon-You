<?php
session_start();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="frontend-css/style.css">
    <link rel="stylesheet" href="frontend-css/register.css">
</head>
<body class="auth-body">
 <div class="auth-container">
        <div class="auth-card">
            
            <!-- Header Section -->
            <div class="auth-header">
                <a href="index.php" class="logo">Salon You</a>
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
                <button type="submit" id="btn-submit" class="btn-block">
                    <span id="btn-spinner" style="display: none;">⏳ </span>
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