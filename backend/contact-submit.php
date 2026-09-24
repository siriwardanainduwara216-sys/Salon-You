<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';
global $conn;

$redirect = '../frontend/contact.php#send-message';

function flash_and_go($type, $text, $redirect)
{
    $_SESSION['contact_flash'] = ['type' => $type, 'text' => $text];
    header('Location: ' . $redirect);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../frontend/contact.php');
    exit();
}

// CSRF check
if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
    flash_and_go('error', 'Session expired. Please try again.', $redirect);
}

// Simple spam protection: 30 seconds between messages
if (isset($_SESSION['last_contact_msg']) && (time() - $_SESSION['last_contact_msg']) < 30) {
    flash_and_go('error', 'Please wait a few seconds before sending another message.', $redirect);
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $phone === '' || $subject === '' || $message === '') {
    flash_and_go('error', 'Please fill in all required fields.', $redirect);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    flash_and_go('error', 'Please enter a valid email address.', $redirect);
}
if (mb_strlen($name) > 100 || mb_strlen($phone) > 30 || mb_strlen($subject) > 150 || mb_strlen($message) > 2000) {
    flash_and_go('error', 'One of the fields is too long.', $redirect);
}

$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;

try {
    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO contact_messages (user_id, name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "isssss", $user_id, $name, $email, $phone, $subject, $message);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
} catch (Throwable $e) {
    flash_and_go('error', 'Sorry, we could not send your message right now. Please try again later.', $redirect);
}

$_SESSION['last_contact_msg'] = time();

if ($user_id) {
    flash_and_go('success', 'Your message has been sent! Our reply will appear under "My Messages" below.', $redirect);
}
flash_and_go('success', 'Your message has been sent! Our team will contact you soon. (Log in next time to see replies on this page.)', $redirect);