<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Global User Session Variables
$is_logged_in = isset($_SESSION['user_id']);
$user_role    = $_SESSION['user_role'] ?? '';
$user_name    = $_SESSION['user_name'] ?? 'Guest User';

// Navigation Active State Highlight
$active_page = 'about';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Salon You</title>

    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- EXTERNAL CSS FILES -->
    <link rel="stylesheet" href="frontend-css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="frontend-css/about_style.css?v=<?php echo time(); ?>">
</head>
<body>

    <!-- SHARED HEADER COMPONENT -->
    <?php include 'header.php'; ?>

    <!-- FULL WIDTH HERO BANNER SECTION -->
    <section class="about-hero-banner" style="background-image: url('../uploads/images/salon/about_header.jpeg');">
        <div class="hero-overlay"></div>
        <div class="hero-banner-content">
            <h1 class="hero-banner-title">About <span class="highlight">Salon You</span></h1>
            <p class="hero-banner-subtitle">
                Discover Our Journey of Excellence and Commitment to Transforming Beauty in Sri Lanka.
            </p>
        </div>
    </section>

    

    <!-- OUR JOURNEY SECTION -->
    <section class="our-journey-section">
        <h2 class="journey-main-title">Our Journey</h2>

        <div class="journey-container">
            <!-- Top Row: Two Images Side by Side + Right Text -->
            <div class="journey-row top-row">
                <div class="journey-images-grid">
                    <div class="journey-media">
                        <img src="../uploads/images/salon/about1.1.jpeg" alt="Salon You History 1" onerror="this.src='../uploads/images/salon/salon1.jpeg'">
                    </div>
                    <div class="journey-media">
                        <img src="../uploads/images/salon/about1.2.jpeg" alt="Salon You History 2" onerror="this.src='../uploads/images/salon/salon1.jpeg'">
                    </div>
                </div>
                <div class="journey-text">
                    <p>
                        Salon You was founded with a vision to provide exceptional beauty services and transform client experiences in Sri Lanka. From our humble beginnings, we expanded our services and relocated to a larger, more sophisticated space to offer a wider range of services and enhance our client experience, further establishing our reputation for excellence in the beauty industry.
                    </p>
                </div>
            </div>

            <!-- Bottom Row: Left Text + Right about2.jpeg Image -->
            <div class="journey-row bottom-row">
                <div class="journey-text">
                    <p>
                        Today, we are proud to unveil our newest and most luxurious location equipped with state-of-the-art facilities and modern styling techniques. Designed to provide an unparalleled experience of luxury and comfort, we believe in offering more than just beauty treatments—we create transformative experiences that leave you looking and feeling your best.
                    </p>
                </div>
                <div class="journey-media">
                    <img src="../uploads/images/salon/about2.jpeg" alt="Salon You Interior" onerror="this.src='../uploads/images/salon/about.jpg'">
                </div>
            </div>
        </div>
    </section>

    <!-- FOUNDER / WELCOME MESSAGE SECTION -->
    <section class="founder-section">
        <div class="founder-container">
            <!-- Left Text Content -->
            <div class="founder-content reveal">
                <p class="founder-message">
                    Welcome to Salon You Private Limited, your ultimate destination for beauty and elegance. Since our inception, we have been dedicated to providing exceptional beauty services that enhance your natural charm and boost your confidence. Our journey reflects our commitment to excellence and growth. At Salon You, we believe in the transformative power of beauty. Our team of skilled professionals is passionate about delivering personalized care using the latest techniques and top-quality products. We strive to create an environment where you can relax, rejuvenate, and leave feeling your best.
                </p>
                <h3 class="founder-name">Sahan madhawa</h3>
                <p class="founder-title">Founder, Salon You (Pvt) Ltd</p>
            </div>
<!-- Right Founder Image -->
<div class="founder-image-box reveal">
    <img src="../uploads/images/salon/owner2.jpg" alt="Salon Owner" onerror="this.src='../uploads/images/salon/about.jpg'">
</div>

        </div>
    </section>

    <!-- OUR VISION AND MISSION SECTION -->
    <section class="vision-mission-dark-section">
        <div class="vm-dark-container">
            <!-- Left Group/Team Image -->
            <div class="vm-dark-image-box reveal">
                <img src="../uploads/images/salon/vision.jpeg" alt="Salon You Team" onerror="this.src='../uploads/images/salon/about2.jpeg'">
            </div>

            <!-- Right Text Content -->
            <div class="vm-dark-content reveal">
                <h2 class="vm-dark-title">Our Vision and Mission</h2>
                
                <p class="vm-dark-text">
                    <strong>Our vision</strong> is to set the standard for beauty and wellness in Sri Lanka by combining cutting-edge techniques with a personalized touch. We are dedicated to continually improving our services and facilities to meet the evolving needs of our clients.
                </p>

                <p class="vm-dark-text">
                    <strong>Our mission</strong> is to provide exceptional beauty services that enhance the natural beauty of every client, delivered in a welcoming and relaxing environment where everyone feels valued and pampered.
                </p>
            </div>
        </div>
    </section>
    
    <!-- OUR COMMITMENT SECTION -->
    <section class="commitment-section">
        <div class="commitment-container reveal">
            <h2 class="commitment-title">Our Commitment</h2>
            <p class="commitment-desc">
                At Salon You, your satisfaction is our top priority. We are committed to maintaining the highest standards of quality and hygiene in all our services. We continuously invest in training and development to ensure our team stays at the forefront of industry trends and innovations.
            </p>
            <div class="commitment-image-box">
                <img src="../uploads/images/salon/commit.jpeg" alt="Our Commitment" onerror="this.src='../uploads/images/salon/about.jpg'">
            </div>
        </div>
    </section>
 <!-- FOOTER COMPONENT LINK -->
    <?php include_once __DIR__ . '/footer.php'; ?>
    

</body>
</html>