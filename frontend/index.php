<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
$user_name = $_SESSION['user_name'] ?? 'Guest User';

// Correct Relative Path to Config File
require_once __DIR__ . '/../backend/config.php';

// ---- Fetch today's queue position for this customer 
$today_queue_appt = null;
if ($is_logged_in && $user_role === 'customer') {
    $sql = "SELECT a.id, a.appointment_time, s.service_name, st.name AS staff_name
            FROM appointments a
            JOIN services s ON a.service_id = s.id
            LEFT JOIN users st ON a.staff_id = st.id
            WHERE a.user_id = ?
            AND a.appointment_date = CURDATE()
            AND a.status IN ('pending', 'confirmed')
            ORDER BY a.appointment_time ASC
            LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $today_queue_appt = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    // If they have an appointment today, find their position in that stylist's queue
    if ($today_queue_appt && $today_queue_appt['staff_name']) {
        $sql = "SELECT COUNT(*) AS position FROM appointments
                WHERE staff_id = (SELECT staff_id FROM appointments WHERE id = ?)
                AND appointment_date = CURDATE()
                AND status IN ('pending', 'confirmed')
                AND queue_status IN ('waiting', 'in_progress')
                AND appointment_time <= ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $today_queue_appt['id'], $today_queue_appt['appointment_time']);
        mysqli_stmt_execute($stmt);
        $today_queue_appt['position'] = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['position'] ?? null;
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salon You - Management System</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Stylesheets -->
    <link rel="stylesheet" href="frontend-css/style.css?v=<?php echo time(); ?>">

    <!-- Custom Styles -->
    <style>
        :root {
            --header-h: 80px; /* height of the fixed header (change here if the header is taller/shorter) */
            --banner-dark: 0.22; /* banner darkness: 0 = brightest, 0.6 = old dark look */
        }

        body {
            padding-top: var(--header-h) !important; /* Header overlay prevention */
        }

        .hero-section {
            position: relative !important;
        }

        /* ===== FULL-SCREEN BANNERS ===== */
        /* style.css has a "section.hero-section" rule that turns every hero into a boxed card
           (max-width 1500px, margins, rounded corners, purple border, 580px height).
           The rules below (higher specificity) undo that for the two home-page banners only. */

        /* 1st banner = exactly the visible screen below the fixed header */
        .hero-section.hero-fullscreen {
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 40px 20px !important;
            border: none !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            animation: none !important;
            min-height: calc(100vh - var(--header-h)) !important;
            min-height: calc(100dvh - var(--header-h)) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            overflow: hidden !important;
            box-sizing: border-box !important;
        }

        /* 2nd banner = the whole screen height */
        .hero-section.hero-fullscreen.hero-fullscreen-second {
            min-height: 100vh !important;
            min-height: 100dvh !important;
        }

        /* extra overlay from style.css removed so the banners stay bright */
        .hero-section.hero-fullscreen .hero-overlay {
            background: transparent !important;
        }

        /* Background video always covers the full banner */
        .hero-section.hero-fullscreen .hero-bg-video {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
            z-index: 1 !important;
        }

        /* Video darkness layer */
        .hero-section::after {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background: rgba(0, 0, 0, var(--banner-dark)) !important;
            z-index: 2 !important;
        }

        .hero-content {
            position: relative !important;
            z-index: 10 !important;
        }

        .hero-title, .hero-title * {
            color: #ffffff !important;
            text-shadow: 0 4px 15px rgba(0, 0, 0, 0.9) !important;
        }

        .gradient-text {
            background: linear-gradient(135deg, #f59e0b, #fbbf24) !important;
            -webkit-background-clip: text !important;
            background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
        }

        .hero-subtitle {
            color: #f1f5f9 !important;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.9) !important;
        }

        .welcome-badge {
            color: #fbbf24 !important;
            background: rgba(0, 0, 0, 0.5) !important;
            border: 1px solid rgba(251, 191, 36, 0.6) !important;
        }

        .btn-secondary {
            background: rgba(0, 0, 0, 0.6) !important;
            color: #ffffff !important;
            border: 1px solid rgba(255, 255, 255, 0.5) !important;
        }

        /* ===== AI RECOMMENDER STYLES ===== */
        #ai-recommender {
            padding: 60px 20px !important;
            text-align: center !important;
            background: #ffffff !important;
            width: 100% !important;
            display: block !important;
            border-top: 1px solid #e2e8f0 !important;
        }

        #ai-recommender .section-header {
            text-align: center !important;
            margin-bottom: 30px !important;
        }

        #ai-recommender .section-title {
            color: #0f172a !important;
            font-size: 2.2rem !important;
            font-weight: 700 !important;
        }

        #ai-recommender .section-subtitle {
            color: #64748b !important;
        }

        #ai-recommender-card {
            max-width: 580px !important;
            margin: 0 auto !important;
        }

        #ai-recommender .ai-upload-box {
            border: 2px dashed #d97706 !important;
            background: #ffffff !important;
            border-radius: 20px !important;
            padding: 40px 25px !important;
            cursor: pointer !important;
            text-align: center !important;
            transition: all 0.3s ease !important;
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            justify-content: center !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05) !important;
        }

        #ai-recommender .ai-upload-box:hover {
            border-color: #b45309 !important;
            background: #fffbe2 !important;
            box-shadow: 0 12px 25px rgba(217, 119, 6, 0.15) !important;
            transform: translateY(-3px) !important;
        }

        #ai-recommender .upload-icon-wrapper {
            width: 70px !important;
            height: 70px !important;
            background: rgba(217, 119, 6, 0.1) !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            margin: 0 auto 15px auto !important;
            font-size: 30px !important;
            color: #d97706 !important;
            border: 1px solid rgba(217, 119, 6, 0.25) !important;
        }

        #ai-recommender h3 {
            font-size: 1.2rem !important;
            color: #1e293b !important;
            margin-bottom: 8px !important;
            font-weight: 600 !important;
        }

        #ai-recommender h3 span {
            color: #d97706 !important;
            text-decoration: underline !important;
        }

        #ai-recommender p {
            color: #64748b !important;
            font-size: 0.88rem !important;
            margin-bottom: 20px !important;
        }

        /* ==========================================================
           PREMIUM SALON HOME PAGE THEME
           (matches the gold/cream luxury look already used on the
           services, login and register pages. #ai-recommender is
           left untouched on purpose.)
           ========================================================== */

        html, body {
            background: #faf9f5 !important; /* warm cream, same as the luxury theme elsewhere */
        }

        .main-container {
            background: #faf9f5;
        }

        /* ---------- Section badges (gold pill, used across the page) ---------- */
        .section-badge {
            display: inline-block;
            color: #8c6517 !important;
            background: rgba(179, 135, 40, 0.12) !important;
            border: 1px solid rgba(179, 135, 40, 0.35) !important;
            padding: 6px 18px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .large-title,
        .section-title {
            font-family: 'Cinzel', serif;
            color: #1d1b18;
            letter-spacing: 0.5px;
        }

        .gradient-text {
            background: linear-gradient(135deg, #b38728 0%, #8c6517 100%) !important;
            -webkit-background-clip: text !important;
            background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
        }

        /* ---------- Services Showcase section ---------- */
        .services-section {
            background: #faf9f5 !important;
            padding: 90px 0 !important;
        }

        .quad-image-grid img {
            border: 1px solid #e8e3d9;
        }

        .purpose-block {
            border-left: 3px solid #b38728;
            padding-left: 18px !important;
        }

        .purpose-header h3 {
            font-family: 'Cinzel', serif;
        }

        /* 4-icon feature strip, recolored gold to match the site */
        .feature-icon-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
            margin-top: 2.4rem;
        }

        .feature-icon-card {
            background: #ffffff;
            border: 1px solid #e8e3d9;
            border-radius: 16px;
            padding: 28px 18px;
            text-align: center;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
            transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        }

        .feature-icon-card:hover {
            transform: translateY(-6px);
            border-color: #b38728;
            box-shadow: 0 14px 30px rgba(179, 135, 40, 0.18);
        }

        .feature-icon-card i {
            font-size: 1.9rem;
            color: #b38728;
            margin-bottom: 14px;
            display: inline-block;
        }

        .feature-icon-card h4 {
            font-size: 0.95rem;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #1d1b18;
            margin-bottom: 8px;
        }

        .feature-icon-card p {
            font-size: 0.85rem;
            color: #736d66;
            line-height: 1.5;
            margin: 0;
        }

        @media (max-width: 900px) {
            .feature-icon-strip {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* ---------- Products section ---------- */
        .products-section {
            background: #ffffff !important;
            padding: 90px 0 !important;
        }

        .product-card {
            background: #faf9f5;
            border: 1px solid #e8e3d9;
            border-radius: 16px;
            padding: 16px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 30px rgba(179, 135, 40, 0.15);
        }

        .product-card h3 {
            font-family: 'Cinzel', serif;
            color: #1d1b18;
            margin-top: 10px;
        }

        /* ---------- Team section ---------- */
        .team-section {
            background: #faf9f5 !important;
            padding: 90px 0 !important;
        }

        .team-card {
            background: #ffffff;
            border: 1px solid #e8e3d9;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.04);
        }

        .team-card h3 {
            font-family: 'Cinzel', serif;
            color: #1d1b18;
        }

        /* ---------- Secondary banner (already full-screen from earlier edit) ---------- */
        .hero-fullscreen-second .hero-title {
            font-family: 'Cinzel', serif;
            letter-spacing: 1px;
        }

        /* ---------- Reviews section ---------- */
        .comment-section {
            background: #ffffff !important;
            padding: 90px 0 !important;
        }

        .comment-card {
            background: #faf9f5;
            border: 1px solid #e8e3d9;
        }

        /* ---------- Browse Our Gallery feature (below hero) ---------- */
        .gallery-feature-section {
            display: grid;
            grid-template-columns: 1fr 2fr;
            min-height: 340px;
        }

        .gallery-feature-text {
            background: linear-gradient(160deg, #fdfaf3 0%, #f3e6c4 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px 45px;
        }

        .gallery-feature-text h2 {
            font-family: 'Cinzel', serif;
            font-size: 2.1rem;
            font-weight: 700;
            color: #1d1b18;
            line-height: 1.25;
            margin-bottom: 14px;
        }

        .gallery-feature-text h2 span {
            background: linear-gradient(135deg, #b38728 0%, #8c6517 100%);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gallery-feature-text p {
            color: #736d66;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 24px;
            max-width: 320px;
        }

        .gallery-feature-btn {
            display: inline-block;
            align-self: flex-start;
            background: linear-gradient(135deg, #b38728 0%, #8c6517 100%);
            color: #ffffff !important;
            padding: 13px 30px !important;
            border-radius: 30px !important;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 8px 20px rgba(140, 101, 23, 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .gallery-feature-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px rgba(140, 101, 23, 0.4);
        }

        .gallery-feature-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
        }

        .gallery-preview-slot {
            position: relative;
            overflow: hidden;
        }

        .gallery-preview-slot img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: opacity 0.5s ease;
        }

        @media (max-width: 900px) {
            .gallery-feature-section {
                grid-template-columns: 1fr;
            }
            .gallery-feature-strip {
                grid-template-columns: repeat(4, 1fr);
                min-height: 220px;
            }
        }

        @media (max-width: 560px) {
            .gallery-feature-strip {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* ---------- Salon Story section (replaces the old full-screen secondary banner) ---------- */
        .salon-story-section {
            position: relative;
            background-size: cover !important;
            background-position: center !important;
            background-repeat: no-repeat !important;
            padding: 100px 20px !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .salon-story-content {
            max-width: 720px;
            position: relative;
            z-index: 2;
        }

        .salon-story-content .large-title {
            color: #ffffff !important;
            margin: 14px 0;
        }

        .salon-story-text {
            color: #e2e8f0;
            font-size: 1.05rem;
            line-height: 1.75;
        }

        /* ---------- Minimalist alternating sections (Pinnacle of Beauty style) ---------- */
        .minimal-section {
            background: #ffffff;
            padding: 70px 3% !important;
        }

        .minimal-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            max-width: 1300px;
            margin: 0 auto;
        }

        .minimal-row-reverse .minimal-media {
            order: 2;
        }

        .minimal-text h2 {
            font-family: 'Cinzel', serif;
            font-size: 1.9rem;
            font-weight: 700;
            color: #1d1b18;
            line-height: 1.3;
        }

        .minimal-text h2 span {
            display: block;
        }

        .minimal-text h3 {
            font-family: 'Cinzel', serif;
            font-size: 2rem;
            font-weight: 700;
            color: #1d1b18;
            margin-bottom: 16px;
        }

        .minimal-text p {
            color: #524d46;
            font-size: 1.05rem;
            line-height: 1.8;
        }

        .minimal-btn {
            display: inline-block;
            margin-top: 18px;
            background: #1d1b18;
            color: #ffffff !important;
            padding: 12px 30px !important;
            border-radius: 6px !important;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
            text-decoration: none;
            transition: background 0.3s ease, transform 0.3s ease;
        }

        .minimal-btn:hover {
            background: #b38728;
            transform: translateY(-2px);
        }

        .minimal-media {
            position: relative;
            border-radius: 10px;
            overflow: hidden;
        }

        .minimal-media img {
            width: 100%;
            height: 100%;
            max-height: 460px;
            object-fit: cover;
            display: block;
        }

        /* Our Services: image left, text right */
        .services-row {
            grid-template-columns: 1.3fr 1fr;
        }

        .services-media img {
            max-height: 620px;
        }

        /* Our Products: text left, image right */
        .products-row {
            grid-template-columns: 1fr 1.3fr;
        }

        .products-media img {
            max-height: 620px;
        }

        /* Our Team: image left, text right */
        .team-row {
            grid-template-columns: 1.3fr 1fr;
        }

        .team-media img {
            max-height: 620px;
        }

        @media (max-width: 900px) {
            .services-row,
            .products-row,
            .team-row {
                grid-template-columns: 1fr;
            }
        }

        .minimal-media-caption {
            position: absolute;
            bottom: 22px;
            left: 50%;
            transform: translateX(-50%);
            color: #ffffff;
            font-family: 'Cinzel', serif;
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: 4px;
            text-transform: uppercase;
            text-shadow: 0 3px 12px rgba(0, 0, 0, 0.6);
            text-align: center;
        }

        @media (max-width: 900px) {
            .minimal-row,
            .minimal-row-reverse {
                grid-template-columns: 1fr;
            }
            .minimal-row-reverse .minimal-media {
                order: 0;
            }
        }

        /* ---------- Intro statement + video ---------- */
        .intro-statement-row {
            margin-bottom: 45px;
        }

        .intro-statement-row .minimal-text:first-child h2 {
            font-size: 2.3rem;
        }

        .intro-video-wrapper {
            position: relative;
            max-width: 1300px;
            margin: 0 auto;
            border-radius: 10px;
            overflow: hidden;
            filter: grayscale(1) contrast(1.05);
        }

        .intro-video-wrapper video {
            width: 100%;
            display: block;
            max-height: 480px;
            object-fit: cover;
        }

        .intro-video-play {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            color: #1d1b18;
            font-size: 1.4rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease, background 0.3s ease;
        }

        .intro-video-play:hover {
            transform: translate(-50%, -50%) scale(1.08);
            background: #ffffff;
        }

        /* ---------- Meet the Owners strip ---------- */
        .owners-strip-section {
            background: #faf9f5 !important;
        }

        .owners-strip-title {
            text-align: center;
            font-family: 'Cinzel', serif;
            font-size: 1.6rem;
            color: #1d1b18;
            margin-bottom: 34px;
        }

        .owners-strip {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            max-width: 900px;
            margin: 0 auto;
        }

        .owner-mini-card {
            display: flex;
            align-items: center;
            gap: 16px;
            background: #ffffff;
            border: 1px solid #e8e3d9;
            border-radius: 14px;
            padding: 14px 20px;
            max-width: 360px;
        }

        .owner-mini-card img {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
            flex-shrink: 0;
        }

        .owner-mini-card h4 {
            font-size: 0.95rem;
            color: #1d1b18;
            margin-bottom: 4px;
        }

        .owner-mini-card p {
            font-size: 0.82rem;
            color: #736d66;
            margin: 0;
            line-height: 1.4;
        }

        /* ==========================================================
           FIX: vertically center the text block next to the
           Services / Products / Team images, and keep image height
           predictable so it doesn't tower over the text content.
           ========================================================== */
        .minimal-row {
            align-items: center !important;
        }

        .minimal-row .minimal-text {
            align-self: center !important;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .services-media img,
        .products-media img,
        .team-media img {
            max-height: 640px !important;   /* raised from 480px - tweak up/down to taste */
            aspect-ratio: 4 / 5;             /* keeps a consistent portrait ratio instead of the image's natural height */
            object-fit: cover;
        }

        /* ==========================================================
           FIX: Our Services / Products / Team rows were capped at
           max-width:1300px, leaving big empty gaps on wide screens.
           Stretch them closer to the full screen width instead.
           ========================================================== */
        .minimal-section {
            padding: 70px 40px !important;   /* was 70px 3% - fixed px gives a smaller, consistent side gap */
        }

        .minimal-row {
            max-width: 1800px !important;    /* was 1300px - raise further (e.g. 2000px) or remove entirely for edge-to-edge */
            width: 100%;
        }

        @media (max-width: 900px) {
            .minimal-section {
                padding: 50px 20px !important;
            }
        }

    </style>
</head>
<body>

    <!-- SHARED HEADER INCLUDE WITH ACTIVE PAGE STATE -->
    <?php 
    $active_page = 'home';
    include 'indexheader.php'; 
    ?>

    <!-- TODAY'S QUEUE BANNER (customers with an appointment today) -->
    <?php if ($today_queue_appt): ?>
    <div style="background: linear-gradient(135deg, #7c3aed, #a855f7); padding: 16px 20px; text-align: center; color: #fff; position: relative; z-index: 10;">
        <div style="max-width: 900px; margin: 0 auto; display: flex; align-items: center; justify-content: center; gap: 16px; flex-wrap: wrap;">
            <span style="font-weight: 600;">
                <i class="fas fa-list-ol"></i>
                Today's Queue:
                <?php if ($today_queue_appt['position']): ?>
                    You're <strong>#<?php echo $today_queue_appt['position']; ?></strong> in line
                    for <?php echo htmlspecialchars($today_queue_appt['service_name']); ?>
                    with <?php echo htmlspecialchars($today_queue_appt['staff_name']); ?>
                    at <?php echo date('h:i A', strtotime($today_queue_appt['appointment_time'])); ?>
                <?php else: ?>
                    Your <?php echo htmlspecialchars($today_queue_appt['service_name']); ?> appointment
                    is today at <?php echo date('h:i A', strtotime($today_queue_appt['appointment_time'])); ?>
                <?php endif; ?>
            </span>
            <a href="my-appointments.php" style="background: #fff; color: #7c3aed; padding: 6px 16px; border-radius: 20px; text-decoration: none; font-weight: 600; font-size: 0.85rem; white-space: nowrap;">
                View Full Queue <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <main class="main-container">
        <!-- 1. HERO SECTION WITH BACKGROUND VIDEO (FULL SCREEN) -->
        <section class="hero-section hero-fullscreen">
            <video autoplay loop muted playsinline class="hero-bg-video">
                <source src="../uploads/videos/bg-video.mp4" type="video/mp4">
            </video>

            <div class="hero-overlay"></div>

            <div class="hero-content">
                <span class="welcome-badge">Welcome to Salon You</span>
                <h1 class="hero-title">WHERE EXPERT CARE MEETS <br><span class="gradient-text">LUXURY BEAUTY</span></h1>
                <p class="hero-subtitle">Book your next appointment today and experience the best ever salon service.</p>
                <div class="hero-buttons">
                    <a href="services.php" class="btn-primary">View Services</a>
                    <a href="services.php" class="btn-secondary">Book Appointment</a>
                </div>
            </div>
        </section>

        <!-- 2. AI RECOMMENDER SECTION (moved right under the hero banner) -->
        <section id="ai-recommender" class="section-container">
            <div class="section-header text-center">
                <span class="section-badge"><i class="fas fa-magic"></i> AI POWERED</span>
                <h2 class="section-title">Smart <span class="gradient-text">Hairstyle Recommender</span></h2>
                <p class="section-subtitle">Upload your selfie to analyze face shape and get a 360 hairstyle preview.</p>
            </div>
            
            <input type="file" id="ai-photo-input" accept="image/*" onchange="analyzeFaceShape()" hidden>
            
            <div id="ai-recommender-card">
                <div class="ai-upload-box" onclick="document.getElementById('ai-photo-input').click()">
                    <div class="upload-icon-wrapper">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h3>Drag & Drop your Selfie here or <span>Browse</span></h3>
                    <p>Supports JPG, PNG, WEBP (Clear portrait selfie recommended)</p>

                    <?php if ($is_logged_in): ?>
                        <button type="button" class="btn-primary upload-btn">
                            <i class="fas fa-camera"></i> Upload Photo
                        </button>
                    <?php else: ?>
                        <button type="button" onclick="event.stopPropagation(); alert('Please Login or Register to use the AI Hairstyle Recommender!'); window.location.href='login.php';" class="btn-primary upload-btn">
                            <i class="fas fa-lock"></i> Login to Use AI Recommender
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- 3. BROWSE OUR GALLERY FEATURE -->
        <section class="gallery-feature-section">
            <div class="gallery-feature-text">
                <h2>Browse Our <span>Gallery</span></h2>
                <p>See real transformations from real clients &mdash; haircuts, coloring and more.</p>
                <a href="gallery.php" class="gallery-feature-btn">Click here</a>
            </div>
            <div class="gallery-feature-strip">
                <div class="gallery-preview-slot" data-images='["../uploads/images/salon/gallery/gallery1.jpeg","../uploads/images/salon/gallery/cut5.jpeg","../uploads/images/salon/gallery/colour6.jpeg"]'>
                    <img src="../uploads/images/salon/gallery/gallery1.jpeg" alt="Salon gallery preview 1">
                </div>
                <div class="gallery-preview-slot" data-images='["../uploads/images/salon/gallery/colour2.jpeg","../uploads/images/salon/gallery/womens_colour3.jpeg","../uploads/images/salon/gallery/cut10.jpeg"]'>
                    <img src="../uploads/images/salon/gallery/colour2.jpeg" alt="Salon gallery preview 2">
                </div>
                <div class="gallery-preview-slot" data-images='["../uploads/images/salon/gallery/womens6.jpeg","../uploads/images/salon/gallery/cut16.jpeg","../uploads/images/salon/gallery/colour8.jpeg"]'>
                    <img src="../uploads/images/salon/gallery/womens6.jpeg" alt="Salon gallery preview 3">
                </div>
                <div class="gallery-preview-slot" data-images='["../uploads/images/salon/gallery/baby6.jpeg","../uploads/images/salon/gallery/cut22.jpeg","../uploads/images/salon/gallery/womens_colour5.jpeg"]'>
                    <img src="../uploads/images/salon/gallery/baby6.jpeg" alt="Salon gallery preview 4">
                </div>
            </div>
        </section>

        <!-- 4. INTRO STATEMENT + VIDEO -->
        <section class="minimal-section intro-statement-section">
            <div class="minimal-row intro-statement-row">
                <div class="minimal-text">
                    <h2>The Pinnacle of Beauty <span>and Luxury</span></h2>
                </div>
                <div class="minimal-text">
                    <p>
                        For every beauty need, our stylists at Salon You are ready to uplift your look with a luxury
                        experience, offered for the first time in Sri Lanka. We provide unparalleled hair and beauty
                        services, combining the latest techniques with a personalised touch, so you leave every visit
                        looking and feeling your best.
                    </p>
                    <a href="about.php" class="minimal-btn">View More</a>
                </div>
            </div>

            <div class="intro-video-wrapper">
                <video id="intro-video" muted loop playsinline poster="../uploads/images/salon/hero-bg.jpg">
                    <source src="../uploads/videos/bg-video.mp4" type="video/mp4">
                </video>
                <button type="button" id="intro-video-play" class="intro-video-play" aria-label="Play video">
                    <i class="fas fa-play"></i>
                </button>
            </div>
        </section>

        <!-- 5. OUR SERVICES (image left / text right) -->
        <section class="minimal-section">
            <div class="minimal-row services-row">
                <div class="minimal-media services-media">
                    <img src="../uploads/images/salon/service.jpeg" alt="Our Services">
                </div>
                <div class="minimal-text">
                    <h3>Our Services</h3>
                    <p>
                        From precision haircuts and vibrant colour transformations to keratin treatments, bridal
                        styling and rejuvenating spa therapies, Salon You brings together a full menu of hair and
                        beauty services under one roof. Every treatment is carried out by trained stylists who take
                        the time to understand your hair type, skin tone and lifestyle before recommending what's
                        right for you &mdash; never a one-size-fits-all approach.
                    </p>
                    <p>
                        We invest in the latest techniques, premium international product lines and a calm, welcoming
                        space so that every visit feels less like an appointment and more like a treat. Whether you're
                        popping in for a quick trim or booking a full bridal package, you can expect the same
                        attention to detail and warm, personalised service every single time.
                    </p>
                    <a href="services.php" class="minimal-btn">View More</a>
                </div>
            </div>
        </section>

        <!-- 6. OUR PRODUCTS (text left / image right) -->
        <section class="minimal-section">
            <div class="minimal-row minimal-row-reverse products-row">
                <div class="minimal-text">
                    <h3>Our Products</h3>
                    <p>
                        Discover the finest selection of beauty products at Salon You, carefully curated to ensure you
                        achieve salon-quality results at home. Our shelves feature top-tier, professional-grade
                        brands &mdash; from nourishing shampoos and colour-protecting treatments to styling essentials
                        and skincare &mdash; hand-picked by our stylists for real results, not just packaging.
                    </p>
                    <p>
                        Every product we stock is one we use and trust on our own clients, so you can shop with
                        confidence knowing it suits your hair type, colour treatment or skin concern. Our team is
                        always on hand to help you build a simple, effective at-home routine that keeps your
                        salon results looking fresh between visits.
                    </p>
                    <a href="products.php" class="minimal-btn">View More</a>
                </div>
                <div class="minimal-media products-media">
                    <img src="../uploads/images/salon/productbg.jpeg" alt="Our Products">
                    <span class="minimal-media-caption">Color Blocking Collection</span>
                </div>
            </div>
        </section>

        <!-- 7. OUR TEAM (image left / text right) -->
        <section class="minimal-section">
            <div class="minimal-row team-row">
                <div class="minimal-media team-media">
                    <img src="../uploads/images/salon/team.jpeg" alt="Our Team" onerror="this.src='https://via.placeholder.com/600x500?text=Our+Team'">
                </div>
                <div class="minimal-text">
                    <h3>Our Team</h3>
                    <p>
                        At Salon You, our talented team is at the heart of everything we do. Led by our founders and
                        made up of experienced hairstylists, colour specialists and beauty therapists, every
                        professional here has trained under industry-recognised programmes and continues to build on
                        their craft through ongoing education and hands-on experience.
                    </p>
                    <p>
                        More than technical skill, our team brings genuine care to every appointment &mdash; listening
                        first, explaining your options clearly, and taking the time to get the details right. Whether
                        it's your first visit or your fiftieth, you'll be looked after by someone who treats your hair
                        and skin as if it were their own, ensuring personalised care and outstanding results every time.
                    </p>
                    <a href="about.php" class="minimal-btn">Meet The Team</a>
                </div>
            </div>
        </section>

        <!-- 8. MEET THE OWNERS (compact strip) -->
        <section class="minimal-section owners-strip-section">
            <h3 class="owners-strip-title">Meet The Owners</h3>
            <div class="owners-strip">
                <div class="owner-mini-card">
                    <img src="../uploads/images/salon/owner2.jpg" alt="Owner 1" onerror="this.src='https://via.placeholder.com/200x200?text=Owner+1'">
                    <div>
                        <h4>Senior Hair Designer</h4>
                        <p>Leading hairstylists with over 8 years of experience.</p>
                    </div>
                </div>
                <div class="owner-mini-card">
                    <img src="../uploads/images/salon/owner1.jpg" alt="Owner 2" onerror="this.src='https://via.placeholder.com/200x200?text=Owner+2'">
                    <div>
                        <h4>Skin & Bridal Specialist</h4>
                        <p>International-level experts in beauty and bridal styling.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 9. CLIENT FEEDBACK & REVIEWS SECTION -->
        <section class="section-container comment-section">
            <div class="section-header text-center">
                <span class="section-badge">CLIENT FEEDBACK</span>
                <h2 class="large-title">What Our <span class="gradient-text">Clients Say</span></h2>
                
                <div style="margin-top: 15px;">
                    <?php if ($is_logged_in && $user_role === 'customer'): ?>
                        <a href="leave-review.php" class="btn-primary" style="display: inline-block;">
                            Leave a Review
                        </a>
                    <?php elseif (!$is_logged_in): ?>
                        <button type="button" onclick="alert('Please login or register to leave a review!'); window.location.href='login.php';" class="btn-primary">
                            Login to Leave a Review
                        </button>
                    <?php endif; ?>
                </div>
            </div>

            <div class="comment-slider-wrapper" style="margin-top: 30px;">
                <div class="comment-track" id="commenttrack">
                    <div class="comment-card">
                        <div class="review-stars">★★★★★</div>
                        <p class="review-text">"The AI Hairstyle visualizer helped me pick the best look before cutting my hair! Exceptional service and friendly staff."</p>
                        <div class="client-info">
                            <h4>Ranjna Kaushal</h4>
                            <span>Regular Client</span>
                        </div>
                    </div>

                    <div class="comment-card">
                        <div class="review-stars">★★★★★</div>
                        <p class="review-text">"Best salon in town for skin glowing treatments. Very professional team and comfortable environment!"</p>
                        <div class="client-info">
                            <h4>Induwara Nimnada</h4>
                            <span>Verified Customer</span>
                        </div>
                    </div>

                    <div class="comment-card">
                        <div class="review-stars">★★★★★</div>
                        <p class="review-text">"Very friendly staff and luxury experience at affordable prices. My go-to place for hair coloring."</p>
                        <div class="client-info">
                            <h4>Sasanka Bandara</h4>
                            <span>Regular Client</span>
                        </div>
                    </div>

                    <div class="comment-card">
                        <div class="review-stars">★★★★★</div>
                        <p class="review-text">"Hair coloring came out exactly how I wanted. They use top-quality products that didn't damage my hair at all."</p>
                        <div class="client-info">
                            <h4>Dasun Theekshana</h4>
                            <span>New Client</span>
                        </div>
                    </div>

                    <div class="comment-card">
                        <div class="review-stars">★★★★★</div>
                        <p class="review-text">"Super clean environment and premium quality beauty products. I highly recommend Salon You to everyone!"</p>
                        <div class="client-info">
                            <h4>Nimesha Wickramasinghe</h4>
                            <span>Verified Customer</span>
                        </div>
                    </div>

                    <div class="comment-card">
                        <div class="review-stars">★★★★★</div>
                        <p class="review-text">"Amazing bridal makeup service! Made my special day truly unforgettable. Thank you so much team Salon You!"</p>
                        <div class="client-info">
                            <h4>Thilini Rajapaksha</h4>
                            <span>Bridal Client</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- FOOTER COMPONENT LINK -->
    <?php include_once __DIR__ . '/indexfooter.php'; ?>

    <!-- SCRIPTS -->
    <!-- INTRO VIDEO PLAY/PAUSE TOGGLE -->
    <script>
        (function () {
            const video = document.getElementById('intro-video');
            const playBtn = document.getElementById('intro-video-play');
            if (!video || !playBtn) return;

            playBtn.addEventListener('click', function () {
                if (video.paused) {
                    video.muted = false;
                    video.play();
                    playBtn.innerHTML = '<i class="fas fa-pause"></i>';
                } else {
                    video.pause();
                    playBtn.innerHTML = '<i class="fas fa-play"></i>';
                }
            });

            video.addEventListener('ended', function () {
                playBtn.innerHTML = '<i class="fas fa-play"></i>';
            });
        })();
    </script>

    <!-- GALLERY PREVIEW AUTO-ROTATE -->
    <script>
        document.querySelectorAll('.gallery-preview-slot').forEach(function (slot, i) {
            let images;
            try {
                images = JSON.parse(slot.dataset.images);
            } catch (e) {
                images = [];
            }
            if (!images.length) return;

            let index = 0;
            const imgEl = slot.querySelector('img');

            setInterval(function () {
                index = (index + 1) % images.length;
                imgEl.style.opacity = 0;
                setTimeout(function () {
                    imgEl.src = images[index];
                    imgEl.style.opacity = 1;
                }, 400);
            }, 3200 + i * 650); // each slot rotates on a slightly different beat
        });
    </script>

    <script src="frontend-java-script/script.js"></script>
    <script src="frontend-java-script/comment.js"></script>
</body>
</html>