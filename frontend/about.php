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
    <link rel="stylesheet" href="frontend-css/style.css">
    <link rel="stylesheet" href="frontend-css/about_style.css">
</head>
<body>
    <header class="header">
        <a href="index.php" class="logo">
          <img src="../logo/logo.png" alt="Salon You Logo" class="logo-img">
            <span class="logo-name">SALON YOU</span>
        </a>

       <nav class="nav">
    <a href="index.php" class="link">Home</a>
    <a href="services.php" class="link">Services</a>
    <a href="../index.php#gallery" class="link">Gallery</a>
    <a href="about.php" class="link active">About Us</a>
    <a href="../index.php#contact" class="link">Contact</a>
</nav>
      

        <div class="actions">
            <?php if (!$is_logged_in): ?>
                <a href="login.php" class="login-btn">Login</a>
                <a href="register.php" class="register-btn">Register</a>
            <?php else: ?>
                <div class="profile">
                <span class="uname"><?php echo htmlspecialchars($user_name); ?></span>
                <a href="logout.php" class="logout-btn" title="Logout"><i class="fas fa-sign-out-alt"></i></a>
                </div>
            <?php endif; ?>
        </div>
    </header>
    <section class="page-hero">
        <div class="section center">
            <span class="badge"><i class="fas fa-heart"></i>OUR STORY & PASSION</span>
            <h1 class="big-title">About <span class="highlight">Salon You</span></h1>
            <p class="hero-desc">Where elegance meets innovation. Learn more about our journey, vision, and the passion behind our beauty transformation services.</p>
        </div>
    </section>
    <!--OUR JOURNEY SECTION-->
<section id="journey" class="section journey">
    <div class="journey-grid">
        <div class="journey-img">
            <video autoplay loop muted playsinline>
                <source src="../uploads/images/salon/journey-video.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
           
        </div>

        <div class="journey-content">
            <span class="badge">OUR STORY</span>
            <h2 class="sec-title">The Story Behind <span class="highlight">Salon You</span></h2>
            <p class="journey-desc">Our journey that began with a humble start has now grown into a complete beauty studio equipped with
                 the latest AI technology and the highest quality tools.</p>
                

            <div class="timeline">
                <div class="t-item">
                    <div class="t-dot"></div>
                    <div class="t-info">
                        <h4>2021 — The Genesis</h4>
                        <p>Salon You was first established with the aim of providing a quality service.</p>
                    </div>
                </div>
                <div class="t-item">
                    <div class="t-dot"></div>
                    <div class="t-info">
                        <h4>2023 — Modernization</h4>
                        <p>Introducing modern equipment and international hair & skin treatments.</p>
                    </div>
                </div>
                <div class="t-item">
                    <div class="t-dot"></div>
                    <div class="t-info">
                        <h4>2026 — Smart AI Integration</h4>
                        <p>Introducing Sri Lanka's first AI Hairstyle 360° Visualizer experience to customers.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
   <!-- vision and mission -->
    <section class="section vision-mission-section">
        <div class="grid2">
            
            <!-- cards -->
            <div class="vm-cards">
                <div class="vm-card2">
                    <div class="vm-icon"><i class="fas fa-eye"></i></div>
                    <h3>Our Vision</h3>
                    <p>To be the leading luxury beauty salon in Sri Lanka, recognized for blending modern AI-driven hair visualization with world-class personal styling and sustainable beauty care.</p>
                </div>

                <div class="vm-card2">
                    <div class="vm-icon"><i class="fas fa-bullseye"></i></div>
                    <h3>Our Mission</h3>
                    <p>To empower individuals by revealing their ultimate beauty confidence through tailored styling, highly qualified specialists, premium skin & hair products, and an unforgettable salon experience.</p>
                </div>
            </div>

            <!-- right image -->
            <div class="vm-img-box2">
                <img src="../uploads/images/salon/vision.jpg" alt="Salon You Interior" class="vm-img2">
               
            </div>

        </div>
    </section>
     <section class="section video-showcase">
        <div class="sec-head center">
            <span class="badge"><i class="fas fa-play-circle"></i> SALON EXPERIENCE</span>
            <h2 class="big-title">Take a Look <span class="highlight">Inside</span></h2>
        </div>
        <div class="video-box">
            <img src="uploads/images/salon/about.jpg">
                
            </img>
        </div>
    </section>

     <footer class="footer">
        <div class="section center">
            <p>&copy; 2026 Salon You. All Rights Reserved.</p>
        </div>
     </footer>
    
     </script>
     
</body>
</html>