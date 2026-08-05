<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// User ලොග් වී ඇත්නම් Home Page එකට යැවීම
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="register.css">
    
</head>
<body class="auth-body">

    <div class="auth-container">
        <div class="auth-card">
            
            <div class="auth-header">
                <a href="index.php" class="logo">SALON <span class="gradient-text">YOU</span></a>
                <h2>Create an Account</h2>
                <p>Register to book your luxury salon appointment</p>
            </div>

            <!-- Error / Success Messages පෙන්වන Alert Box එක -->
            <div id="alert-box" class="alert-box" style="display: none;"></div>

            <!-- Registration Form -->
            <form id="register-form" autocomplete="off">
                
                <!-- Account Type Selection -->
                <div class="form-group">
                    <label for="role"><i class="fas fa-user-tag"></i> Register As</label>
                    <select name="role" id="role" class="form-control">
                        <option value="customer" selected>Customer</option>
                        <option value="employee">Salon Employee / Stylist</option>
                    </select>
                </div>

                <!-- Full Name -->
                <div class="form-group">
                    <label for="full_name"><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" name="full_name" id="full_name" class="form-control" placeholder="John Doe" required>
                </div>

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="name@example.com" required>
                </div>

                <!-- Phone Number -->
                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="tel" name="phone" id="phone" class="form-control" placeholder="07XXXXXXXX" maxlength="10" required>
                </div>

                <!-- Password -->
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

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Log In</a></p>
            </div>

        </div>
    </div>

    <!-- Password Hide/Show Script -->
    <script>
        document.getElementById('toggle-password').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                this.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                this.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    </script>
    <script src="auth.js"></script>
</body>
</html>