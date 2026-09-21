<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Salon You</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- style.css first, register.css last so the register styles win -->
    <link rel="stylesheet" href="frontend-css/style.css">
    <link rel="stylesheet" href="frontend-css/register.css?v=<?php echo time(); ?>">
</head>
<body class="auth-body">

    <div class="auth-container">

        <!-- LEFT: Luxury visual panel (hidden on mobile) -->
        <div class="auth-visual" style="background-image: url('../uploads/images/salon/hero-bg.jpg');">
            <span class="auth-eyebrow">Welcome to Salon You</span>

            <div class="auth-visual-body">
                <h2>Where Expert Care Meets <span>Luxury Beauty</span></h2>
                <p>Create your account to book appointments, choose your favourite stylist and preview your next look before you sit in the chair.</p>

                <ul class="auth-features">
                    <li><i class="fas fa-magic"></i> AI Hairstyle Preview</li>
                    <li><i class="fas fa-users"></i> Expert Stylists</li>
                    <li><i class="fas fa-calendar-check"></i> Easy Online Booking</li>
                </ul>
            </div>
        </div>

        <!-- RIGHT: Registration form -->
        <div class="auth-card">

            <!-- Auth Header -->
            <div class="auth-header">
                <a href="index.php" class="logo">SALON <span class="gradient-text">YOU</span></a>
                <h2>Create an Account</h2>
                <p>Register to book your luxury salon appointment</p>
            </div>

            <!-- Error or Success Alert Box -->
            <div id="alert-box" class="alert-box" style="display: none;"></div>

            <!-- Registration Form -->
            <form id="register-form" autocomplete="off">

                <!-- Full Name Field -->
                <div class="form-group">
                    <label for="full_name"><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Your full name" required>
                </div>

                <!-- Email Field -->
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="name@example.com" required>
                </div>

                <!-- Phone Field -->
                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="tel" name="phone" id="phone" class="form-control" placeholder="07XXXXXXXX" maxlength="10" required>
                </div>

                <!-- Password Field -->
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <div class="password-input-wrapper">
                        <input type="password" name="password" id="password" class="form-control" placeholder="At least 6 characters" required minlength="6">
                        <i class="fas fa-eye toggle-password" id="toggle-password"></i>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="btn-submit" class="btn btn-primary btn-block">
                    <span id="btn-text">Register Now</span>
                    <i class="fas fa-spinner fa-spin" id="btn-spinner" style="display: none;"></i>
                </button>
            </form>

            <!-- Footer Link -->
            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Log In</a></p>
            </div>

        </div>
    </div>

    <!-- JavaScript File -->
    <script src="frontend-java-script/register.js"></script>
</body>
</html>