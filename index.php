<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_logged_in = isset($_SESSION['user_id']);
$user_role = $_SESSION['user_role'] ?? '';
$user_name = $_SESSION['user_name'] ?? 'Guest User';

require_once 'config.php';

if (isset($_SESSION['user_id'])): ?>
    
    <button onclick="document.getElementById('ai-photo-input').click()" class="btn-primary">
        <i class="fas fa-camera"></i> Upload Selfie
    </button>
<?php else: ?>
    
    <button onclick="alert('Please Login or Register to use the AI Hairstyle Recommender!'); window.location.href='login.php';" class="btn-primary">
        <i class="fas fa-lock"></i> Login to Use AI Recommender
    </button>
<?php endif; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salon You - Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <header class="site-header">
        <a href="index.php" class="logo-container">
          <img src="logo/logo.png" alt="Salon You Logo" class="site-logo">
            <span class="logo-text">SALON YOU</span>
        </a>

        <nav class="main-nav">
            <a href="index.php" class="nav-link active">Home</a>
            <a href="#services" class="nav-link">Services</a>
            <a href="#gallery" class="nav-link">Gallery</a>
            <a href="about.php" class="nav-link">About Us</a>
            <a href="#contact" class="nav-link">Contact</a>
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
    <main class="main-container">
        <section class="hero-section">
            <div class="hero-content">
                <span class="welcome-badge">Welcome to Salon You</span>
                <h1 class="hero-title">WHERE EXPERT CARE MEETS <br><span class="gradient-text">LUXURY BEAUTY</span></h1>
                <p class="hero-subtitle">Book your next appointment today and experience the best ever salon service.</p>
                <div class="hero-buttons">
                    <a href="#services" class="btn-primary">View Services</a>
                    <a href="#book" class="btn-secondary">Book Appointment</a>
                </div>
            </div>
        </section>
    </main>
    <secton id="ai-recommender" class="section-container ai-sectoin">
        <div class="section-header">
            <span class="section-badge">AI POWERD</span>
            <h2 class="section-title">Smart<span class="gradient-text">Hairstyle Recommender</span>
            <p class="section-subtile">Upload your selfie to analyze face shape and get a 360 hairstyle preview.</p>

        </div>
        <input type="file" id="ai-photo-input" accept="image/*" onchange="analyzeFaceShape()" hidden>
        <div id="ai-recommender-card" class="ai-feature-card">
        <div class="ai-idle-state">
            <div class="ai-idle-icon">
                <i class="fas fa-user-astronaut"></i>
            </div>
            <div class="ai-idle-text">
                <h4>Interactive Mirror System Idle</h4>
                <p>Please select a clear portrait selfie to apply 360° haircut configurations over your facial structure.</p>
            </div>
            <button onclick="document.getElementById('ai-photo-input').click()" class="btn-primary">
                <i class="fas fa-camera"></i> Upload Selfie</button>
            
        </div>
    </div>
    </secton>
    <!-- 1. HERO SECTION -->
<section class="hero-section" style="background-image: url('uploads/images/salon/hero-bg.jpg');">
    <div class="hero-content">
        <span class="section-badge"><i class="fas fa-crown"></i> WELCOME TO SALON YOU</span>
        <h1 class="hero-title">Redefining Your <span class="gradient-text">Beauty & Style</span></h1>
        <p class="hero-subtitle"> AI technology and the services of top-tier stylists under one roof.</p>
        <div class="hero-btns">
            <a href="#ai-recommender" class="btn-primary"><i class="fas fa-magic"></i> Try AI Mirror</a>
            <a href="#journey" class="btn-secondary"><i class="fas fa-arrow-down"></i> Explore Our Story</a>
        </div>
    </div>
</section>


<section class="section-container services-section">
    <div class="side-by-side-grid">

        
        <div class="quad-image-grid">
            <img src="uploads/images/salon/service-1.jpg" alt="Hair Styling"> 
            <img src="uploads/images/salon/service-2.jpg" alt="Skin Care"> 
            <img src="uploads/images/salon/service-3.jpg" alt="Makeup"> 
            <img src="uploads/images/salon/service-4.jpg" alt="Nail Art"> 
        </div>

        
        <div class="side-by-side-content">
            <span class="section-badge"><i class="fas fa-cut"></i> WHAT WE OFFER</span>
            <h2 class="large-title">Our Premium <span class="gradient-text">Services</span></h2>

            <div class="purpose-block">
                <div class="purpose-header">
                    <i class="fas fa-magic purpose-icon-inline"></i>
                    <h3>Hair Care & Styling</h3>
                </div>
                <p class="large-text">
                    All modern hair styling services including haircuts, coloring, rebonding, and treatments.
                </p>
            </div>

            <div class="purpose-block">
                <div class="purpose-header">
                    <i class="fas fa-spa purpose-icon-inline"></i>
                    <h3>Skin & Beauty Treatments</h3>
                </div>
                <p class="large-text">
                    Premium facials, skin-glowing treatments, and bridal makeups tailored to suit your skin.
                </p>
            </div>
        </div>

    </div>
</section>



<section class="section-container products-section">
    <div class="section-header text-center">
        <span class="section-badge"><i class="fas fa-pump-soap"></i> EXCLUSIVE PRODUCTS</span>
        <h2 class="large-title">Our Quality <span class="gradient-text">Beauty Products</span></h2>
    </div>

    <div class="products-grid">
        <div class="product-card">
            <div class="product-img-box">
                <img src="uploads/images/salon/service-4.jpg" alt="Product 1">
            </div>
            <h3>kontless Box Braid</h3>
            <p class="large-text">High Taper Fade with Box Braids Top Knot is a modern and stylish men’s hairstyle.
                 The sides and back are cut into a high taper fade, while the hair on top is divided into neat square sections and braided into box braids. The braids are then tied into a top knot (man bun), creating a clean and fashionable look.</p>
        </div>

        <div class="product-card">
            <div class="product-img-box">
                <img src="uploads/images/salon/product-2.jpg" alt="Product 2"> 
            </div>
            <h3>Hair colouring</h3>
            <p class="large-text">This hairstyle features a bold black and deep red color combination, creating a modern and eye-catching look.
                 The black base adds depth, while the vibrant red highlights bring contrast and personality. This color style is popular among people who want a unique, confident, and fashionable appearance.</p>
        </div>
    </div>
</section>



<section class="section-container team-section">
    <div class="section-header text-center">
        <span class="section-badge"><i class="fas fa-users"></i> EXPERT STYLISTS</span>
        <h2 class="large-title">Meet Our <span class="gradient-text">Professional Team</span></h2>
    </div>

    <div class="team-grid">
        <div class="team-card">
            <div class="team-img-box">
                <img src="uploads/images/salon/service-1.jpg" alt="Team Member 1" onerror="this.src='https://via.placeholder.com/300x400?text=Senior+Stylist'">
            </div>
            <h3>Senior Hair Designer</h3>
            <p class="large-text">Leading hairstylists with over 8 years of experience.</p>
        </div>

        <div class="team-card">
            <div class="team-img-box">
                <img src="uploads/images/salon/service-1.jpg" alt="Team Member 2" onerror="this.src='https://via.placeholder.com/300x400?text=Beauty+Expert'">
            </div>
            <h3>Skin & Bridal Specialist</h3>
            <p class="large-text">International-level experts in beauty and bridal styling.</p>
        </div>
    </div>
</section>
<section class="section-container comment-section">
    <div class="section-text-center">
        <span class="section-badge"><i class="fas fa-star"></i> CLIENT FEEDBACK</span>
        <h2 clas="large-title">What Our <span class="gradient-text">Client Say</span>
    </div>
    <div class="comment-slider-wrapper">
        <div class="comment-track" id="commenttrack">

        <div class="comment-card">
            <div class="review-stars">★★★★★</div>
            <p class="review-text">"The AI Hairstyle visualizer helped me pick the best look before cutting my hair! Exceptional service and friendly staff."</p>
            <div class="client-info">
                <h4>Ranjna kaushal</h4>
                <sapn> Regular client</sapn>
        </div>
        </div>
        <div class="comment-card">
            <div class="review-stars">★★★★★</div>
            <p class="review-text">Best salon in town for skin glowing treatments. Very professional team and comfortable environment!"</p>
            <h4>Induwara Nimnada</h4>
            <span>Verified Customer</span>
        </div>

        <div class="comment-card">
                <div class="review-stars">★★★★★</div>
                <p class="review-text">"Very friendly staff and luxury experience at affordable prices. My go-to place for hair coloring."</p>
                <div class="client-info">
                    <h4>sasanka bandara</h4>
                    <span>Regular Client</span>
                </div>
            </div>
           
            <div class="comment-card">
                <div class="review-stars">★★★★★</div>
                <p class="review-text">"Hair coloring came out exactly how I wanted. They use top-quality products that didn't damage my hair at all."</p>
                <div class="client-info">
                    <h4>dasun theekshana</h4>
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



     <script src="script.js"></script>
     <script src="comment.js"></script>
</body>
</html>