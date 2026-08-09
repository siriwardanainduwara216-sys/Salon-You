<?php
// ============================================================
// test-mail.php
// Standalone test - sends a test OTP email directly, bypassing
// the registration form entirely. Helps isolate whether the
// problem is network/DNS related or something in the form flow.
//
// IMPORTANT: Delete this file once testing is done - it should
// never be left on a live/production server.
// ============================================================

require_once __DIR__ . '/mailer.php';

// Change this to your own email to test
$test_email = 'siriwardanainduwara216@gmail.com';

echo "<h3>Testing email connection...</h3>";
echo "<p>Sending a test email to: " . htmlspecialchars($test_email) . "</p>";

$result = send_otp_email($test_email, 'Test User', '123456');

if ($result['status'] === true) {
    echo "<p style='color:green; font-weight:bold;'>SUCCESS - Email sent!</p>";
} else {
    echo "<p style='color:red; font-weight:bold;'>FAILED</p>";
    echo "<pre>" . htmlspecialchars($result['error']) . "</pre>";
}

// Extra diagnostic: check if PHP can resolve smtp.gmail.com at all
echo "<h3>DNS Resolution Test</h3>";
$ip = gethostbyname('smtp.gmail.com');
if ($ip === 'smtp.gmail.com') {
    echo "<p style='color:red;'>DNS lookup FAILED - your server/PC could not resolve smtp.gmail.com to an IP address.</p>";
} else {
    echo "<p style='color:green;'>DNS lookup OK - smtp.gmail.com resolved to: " . htmlspecialchars($ip) . "</p>";
}

// Extra diagnostic: check if the openssl extension is loaded
echo "<h3>OpenSSL Extension Check</h3>";
if (extension_loaded('openssl')) {
    echo "<p style='color:green;'>openssl extension is loaded.</p>";
} else {
    echo "<p style='color:red;'>openssl extension is NOT loaded. Enable it in php.ini.</p>";
}