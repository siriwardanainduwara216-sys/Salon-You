<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../backend/config.php';
global $conn;

$active_page = 'contact';

$is_logged_in = isset($_SESSION['user_id']);
$user_role    = $_SESSION['user_role'] ?? '';
$user_name    = $_SESSION['user_name'] ?? 'Guest User';

// CSRF token for the contact form
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Flash message (success / error) from backend/contact-submit.php
$flash = $_SESSION['contact_flash'] ?? null;
unset($_SESSION['contact_flash']);

// Logged-in users: load their previous messages + admin replies
$my_messages = [];
if ($is_logged_in) {
    try {
        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, subject, message, status, admin_reply, replied_at, created_at
             FROM contact_messages
             WHERE user_id = ?
             ORDER BY created_at DESC
             LIMIT 20"
        );
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        while ($row = mysqli_fetch_assoc($res)) {
            $my_messages[] = $row;
        }
        mysqli_stmt_close($stmt);
    } catch (Throwable $e) {
        $my_messages = []; // table not created yet
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Salon You</title>

    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- CSS Files -->
    <link rel="stylesheet" href="frontend-css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="frontend-css/about_style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="frontend-css/contact_style.css?v=<?php echo time(); ?>">

    <style>
        .contact-flash {
            max-width: 1200px;
            margin: 25px auto 0;
            padding: 14px 20px;
            border-radius: 10px;
            font-weight: 500;
        }
        .contact-flash.success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .contact-flash.error   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        .my-messages-section { padding: 50px 20px; background: #f8fafc; }
        .my-messages-container { max-width: 900px; margin: 0 auto; }
        .my-messages-container h2 { text-align: center; margin-bottom: 25px; color: #0f172a; }

        .my-msg-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 18px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.04);
        }
        .my-msg-head { display: flex; justify-content: space-between; align-items: center; gap: 10px; flex-wrap: wrap; }
        .my-msg-head h4 { margin: 0; color: #1e293b; }
        .my-msg-date { color: #94a3b8; font-size: 0.8rem; margin: 4px 0 10px; }
        .my-msg-text { color: #475569; line-height: 1.6; }

        .msg-status { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; }
        .msg-status.pending { background: #fef3c7; color: #92400e; }
        .msg-status.replied { background: #dcfce7; color: #166534; }

        .admin-reply-box {
            margin-top: 14px;
            padding: 14px 16px;
            background: #fffbeb;
            border-left: 4px solid #d97706;
            border-radius: 8px;
        }
        .admin-reply-box strong { color: #b45309; display: block; margin-bottom: 6px; }
        .admin-reply-box p { margin: 0; color: #334155; line-height: 1.6; }
        .admin-reply-box small { display: block; margin-top: 8px; color: #94a3b8; }
    </style>
</head>
<body>

    <!-- INCLUDE HEADER COMPONENT -->
    <?php include_once __DIR__ . '/indexheader.php'; ?>

    <!-- HERO BANNER SECTION -->
    <section class="about-hero-banner" style="background-image: url('../uploads/images/salon/about_header.jpeg');">
        <div class="hero-overlay"></div>
        <div class="hero-banner-content">
            <h1 class="hero-banner-title">Contact <span class="highlight">Salon You</span></h1>
            <p class="hero-banner-subtitle">
                We'd Love to Hear From You. Get in Touch with Us for Bookings, Inquiries & Special Services.
            </p>
        </div>
    </section>

    <?php if ($flash): ?>
        <div class="contact-flash <?php echo $flash['type'] === 'success' ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($flash['text']); ?>
        </div>
    <?php endif; ?>

    <!-- 2-COLUMN CONTACT LAYOUT SECTION -->
    <section class="contact-main-section">
        <div class="contact-container">

            <!-- LEFT COLUMN: GET IN TOUCH -->
            <div class="contact-info-col">
                <h2 class="col-title">GET IN TOUCH</h2>
                <p class="col-desc">
                    Start Your Journey to Beautiful Hair Today. Contact Us for Expert Hair Care and Personalized Services.
                </p>

                <div class="info-list">
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="info-text">
                            Maththegama/Ku/Bopitiya, Sri Lanka.
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="far fa-clock"></i></div>
                        <div class="info-text">
                            9.00 AM - 07.00 PM Tuesday - Sunday
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                        <div class="info-text">
                            +94 77 388 5122
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="far fa-envelope"></i></div>
                        <div class="info-text">
                            salonyou@gmail.com
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: SEND A MESSAGE FORM -->
            <div class="contact-form-col" id="send-message">
                <h2 class="col-title">SEND A MESSAGE</h2>
                <p class="col-desc">
                    Reach Out to Us for Expert Hair Care and Personalized Services. Your Journey to Beautiful Hair Starts Here.
                </p>

                <form action="../backend/contact-submit.php" method="POST" class="custom-contact-form">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                    <div class="form-row">
                        <input type="text" name="name" placeholder="Your name*" maxlength="100" required
                               value="<?php echo $is_logged_in ? htmlspecialchars($user_name) : ''; ?>">
                        <input type="email" name="email" placeholder="Your email*" maxlength="150" required>
                    </div>
                    <div class="form-row">
                        <input type="text" name="phone" placeholder="Your contact number*" maxlength="30" required>
                        <input type="text" name="subject" placeholder="Subject*" maxlength="150" required>
                    </div>
                    <div class="form-group">
                        <textarea name="message" rows="5" maxlength="2000" placeholder="Your message*" required></textarea>
                    </div>
                    <button type="submit" class="btn-submit-msg">SEND MESSAGE</button>

                    <?php if (!$is_logged_in): ?>
                        <p style="margin-top: 12px; font-size: 0.85rem; color: #94a3b8;">
                            <i class="fas fa-info-circle"></i>
                            <a href="login.php">Log in</a> before sending to see our reply here on this page.
                        </p>
                    <?php endif; ?>
                </form>
            </div>

        </div>
    </section>

    <!-- MY MESSAGES & REPLIES (logged-in users only) -->
    <?php if ($is_logged_in && !empty($my_messages)): ?>
    <section class="my-messages-section" id="my-messages">
        <div class="my-messages-container">
            <h2><i class="fas fa-envelope-open-text"></i> My Messages</h2>

            <?php foreach ($my_messages as $m): ?>
                <div class="my-msg-card">
                    <div class="my-msg-head">
                        <h4><?php echo htmlspecialchars($m['subject']); ?></h4>
                        <?php if ($m['status'] === 'replied'): ?>
                            <span class="msg-status replied"><i class="fas fa-check"></i> Replied</span>
                        <?php else: ?>
                            <span class="msg-status pending"><i class="far fa-clock"></i> Waiting for reply</span>
                        <?php endif; ?>
                    </div>
                    <div class="my-msg-date"><?php echo date('M d, Y h:i A', strtotime($m['created_at'])); ?></div>
                    <div class="my-msg-text"><?php echo nl2br(htmlspecialchars($m['message'])); ?></div>

                    <?php if ($m['status'] === 'replied' && !empty($m['admin_reply'])): ?>
                        <div class="admin-reply-box">
                            <strong><i class="fas fa-reply"></i> Reply from Salon You</strong>
                            <p><?php echo nl2br(htmlspecialchars($m['admin_reply'])); ?></p>
                            <?php if (!empty($m['replied_at'])): ?>
                                <small><?php echo date('M d, Y h:i A', strtotime($m['replied_at'])); ?></small>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- FULL WIDTH GOOGLE MAP SECTION (GIRIULLA) -->
    <section class="map-section">
        <iframe
            src="https://maps.google.com/maps?q=Giriulla,%20Sri%20Lanka&t=&z=15&ie=UTF8&iwloc=&output=embed"
            width="100%"
            height="450"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </section>

    <!-- FOOTER COMPONENT LINK -->
    <?php include_once __DIR__ . '/indexfooter.php'; ?>

</body>
</html>