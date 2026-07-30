<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
$user_name = $_SESSION['user_name'] ?? 'Guest User';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Salon You</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="about_style.css">
</head>
<body>
    <header class="site-header">
        <a href="index.php" class="logo-container">
          <img src="logo/logo.png" alt="Salon You Logo" class="site-logo">
            <span class="logo-text">SALON YOU</span>
        </a>

        <nav class="main-nav">
            <a href="index.php" class="nav-link ">Home</a>
            <a href="services" class="nav-link">Services</a>
            <a href="gallery" class="nav-link">Gallery</a>
            <a href="about.php" class="nav-link active">About Us</a>
            <a href="contact" class="nav-link">Contact</a>
        </nav>
      

        <div class="header-actions">
            <?php if (!$is_logged_in): ?>
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-register">Register</a>
            <?php else: ?>
                <div class="user-profile">
                <span class="user-name"><?php echo htmlspecialchars($user_name); ?></span>
                <a href="logout.php" class="btn-logout" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <section class="page-hero">
        <div class="section-container text-center">
            <span class="section-badge"><i class="fas fa-heart"></i>OUR STORY & PASSION</span>
            <h1 class="large-title">About <span class="gradient-text">Salon You</span></h1>
            <p class="hero-description">Where elegance meets innovation. Learn more about our journey, vision, and the passion behind our beauty transformation services.</p>
        </div>
    </section>
    // 4. OUR JOURNEY SECTION (WITH VIDEO) //
<section id="journey" class="section-container journey-section">
    <div class="journey-grid">
        <div class="journey-image-box">
            <video autoplay loop muted playsinline>
                <source src="uploads/images/salon/journey-video.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
           
        </div>

        <div class="journey-content">
            <span class="section-badge">OUR STORY</span>
            <h2 class="section-title">The Story Behind <span class="gradient-text">Salon You</span></h2>
            <p class="journey-desc">Our journey that began with a humble start has now grown into a complete beauty studio equipped with
                 the latest AI technology and the highest quality tools.</p>
                

            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-info">
                        <h4>2021 — The Genesis</h4>
                        <p>Salon You was first established with the aim of providing a quality service.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-info">
                        <h4>2023 — Modernization</h4>
                        <p>Introducing modern equipment and international hair & skin treatments.</p>
                    </div>
                </div>
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="timeline-info">
                        <h4>2026 — Smart AI Integration</h4>
                        <p>Introducing Sri Lanka’s first AI Hairstyle 360° Visualizer experience to customers.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
   <!-- VISION & MISSION SECTION WITH IMAGE -->
    <section class="section-container vision-mission-section">
        <div class="about-grid-layout">
            
            <!-- LEFT SIDE: Horizontal Vision & Mission Cards -->
            <div class="vision-mission-cards">
                <div class="about-card">
                    <div class="card-icon"><i class="fas fa-eye"></i></div>
                    <h3>Our Vision</h3>
                    <p>To be the leading luxury beauty salon in Sri Lanka, recognized for blending modern AI-driven hair visualization with world-class personal styling and sustainable beauty care.</p>
                </div>

                <div class="about-card">
                    <div class="card-icon"><i class="fas fa-bullseye"></i></div>
                    <h3>Our Mission</h3>
                    <p>To empower individuals by revealing their ultimate beauty confidence through tailored styling, highly qualified specialists, premium skin & hair products, and an unforgettable salon experience.</p>
                </div>
            </div>

            <!-- RIGHT SIDE: Salon Feature Image Box -->
            <div class="about-image-box">
                <img src="uploads/images/salon/vision.jpg" alt="Salon You Interior" class="about-img">
               
            </div>

        </div>
    </section>
     <section class="section-container video-showcase-section">
        <div class="section-header text-center">
            <span class="section-badge"><i class="fas fa-play-circle"></i> SALON EXPERIENCE</span>
            <h2 class="large-title">Take a Look <span class="gradient-text">Inside</span></h2>
        </div>
        <div class="about-image-container">
            <img src="uploads/images/salon/about.jpg">
                
            </img>
        </div>
    </section>

     <footer class="footer-container">
        <div class="section-container text-center">
            <p>&copy; 2026 Salon You. All Rights Reserved.</p>
        </div>
     </footer>
    
     </script>
     
</body>
</html>
