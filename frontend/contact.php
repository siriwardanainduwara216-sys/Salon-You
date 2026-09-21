<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$active_page = 'contact';

$is_logged_in = isset($_SESSION['user_id']);
$user_role    = $_SESSION['user_role'] ?? '';
$user_name    = $_SESSION['user_name'] ?? 'Guest User';
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

    <!-- 2-COLUMN CONTACT LAYOUT SECTION -->
    <section class="contact-main-section">
        <div class="contact-container">
            
            <!-- LEFT COLUMN: GET IN TOUCH -->
            <div class="contact-info-col">
                <h2 class="col-title">GET IN TOUCH</h2>
                <p class="col-desc">
                    Start Your Journey to Beautiful Hair Today. Contact Us for Expert Hair Care and Personalized Services.[cite: 3]
                </p>

                <div class="info-list">
                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="info-text">
                            No.6, Pagoda Road, Nugegoda, 10250, Sri Lanka.[cite: 3]
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="far fa-clock"></i></div>
                        <div class="info-text">
                            9.00 AM - 07.00 PM Tuesday - Sunday[cite: 3]
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon"><i class="fas fa-phone-alt"></i></div>
                        <div class="info-text">
                            +94 77 388 5122[cite: 3]
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
            <div class="contact-form-col">
                <h2 class="col-title">SEND A MESSAGE</h2>
                <p class="col-desc">
                    Reach Out to Us for Expert Hair Care and Personalized Services. Your Journey to Beautiful Hair Starts Here.[cite: 3]
                </p>

                <form action="#" method="POST" class="custom-contact-form">
                    <div class="form-row">
                        <input type="text" name="name" placeholder="Your name*" required>
                        <input type="email" name="email" placeholder="Your email*" required>
                    </div>
                    <div class="form-row">
                        <input type="text" name="phone" placeholder="Your contact number*" required>
                        <input type="text" name="subject" placeholder="Subject*" required>
                    </div>
                    <div class="form-group">
                        <textarea name="message" rows="5" placeholder="Your message*" required></textarea>
                    </div>
                    <button type="submit" class="btn-submit-msg">SEND MESSAGE</button>
                </form>
            </div>

        </div>
    </section>
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