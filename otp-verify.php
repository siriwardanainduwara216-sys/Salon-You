<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Temp user details නැත්නම් කෙළින්ම Register Page එකට Redirect කිරීම
if (!isset($_SESSION['temp_user_id'])) {
    header("Location: register.php");
    exit();
}

$user_phone = $_SESSION['temp_user_phone'] ?? 'your registered number';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Salon You</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="register.css">
</head>
<body class="auth-body">

    <div class="auth-container">
        <div class="auth-card">
            
            <div class="auth-header">
                <a href="index.php" class="logo">SALON <span class="gradient-text">YOU</span></a>
                <h2>Enter OTP Code</h2>
                <p>We've sent a 6-digit verification code to <br><strong><?php echo htmlspecialchars($user_phone); ?></strong></p>
            </div>

            <div id="alert-box" class="alert-box" style="display: none;"></div>

            <!-- OTP Verification Form -->
            <form id="otp-form" autocomplete="off">
                <div class="form-group">
                    <label for="otp_code"><i class="fas fa-shield-alt"></i> 6-Digit OTP</label>
                    <input type="text" name="otp_code" id="otp_code" class="form-control" placeholder="123456" maxlength="6" style="text-align: center; letter-spacing: 5px; font-size: 1.2rem;" required>
                </div>

                <button type="submit" id="btn-submit" class="btn btn-primary btn-block">
                    <span id="btn-text">Verify Account</span>
                    <i class="fas fa-spinner fa-spin" id="btn-spinner" style="display: none;"></i>
                </button>
            </form>

            <div class="auth-footer">
                <p>Didn't receive the code? <a href="register.php">Try Registering Again</a></p>
            </div>

        </div>
    </div>

    <script src="auth.js"></script>
</body>
</html>