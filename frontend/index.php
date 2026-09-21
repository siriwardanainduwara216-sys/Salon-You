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
        body {
            padding-top: 80px !important; /* Header overlay prevention */
        }

        .hero-section {
            position: relative !important;
        }

        /* Video darkness layer */
        .hero-section::after {
            content: '' !important;
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            background: rgba(0, 0, 0, 0.6) !important;
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
            background: #f8fafc !important;
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
        <!-- 1. HERO SECTION WITH BACKGROUND VIDEO -->
        <section class="hero-section">
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

        <!-- 2. AI RECOMMENDER SECTION -->
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

        <!-- 3. SECONDARY BANNER -->
        <section class="hero-section" style="background-image: linear-gradient(rgba(15, 23, 42, 0.35), rgba(15, 23, 42, 0.45)), url('../uploads/images/salon/hero-bg.jpg') !important; background-size: cover !important; background-position: center !important; min-height: 80vh;">
            <div class="hero-content text-center">
                <span class="section-badge">WELCOME TO SALON YOU</span>
                <h1 class="hero-title">Redefining Your <span class="gradient-text">Beauty & Style</span></h1>
                <p class="hero-subtitle">AI technology and the services of top-tier stylists under one roof.</p>
                <div class="hero-btns" style="margin-top: 20px;">
                    <a href="#ai-recommender" class="btn-primary">Try AI Mirror</a>
                    <a href="about.php" class="btn-secondary">Explore Our Story</a>
                </div>
            </div>
        </section>

        <!-- 4. SERVICES SHOWCASE SECTION -->
        <section class="section-container services-section">
            <div class="side-by-side-grid">
                <div class="quad-image-grid">
                    <img src="../uploads/images/salon/service-1.jpg" alt="Hair Styling"> 
                    <img src="../uploads/images/salon/service-2.jpg" alt="Skin Care"> 
                    <img src="../uploads/images/salon/service-3.jpg" alt="Makeup"> 
                    <img src="../uploads/images/salon/service-4.jpg" alt="Nail Art"> 
                </div>

                <div class="side-by-side-content">
                    <span class="section-badge">WHAT WE OFFER</span>
                    <h2 class="large-title">Our Premium <span class="gradient-text">Services</span></h2>

                    <div class="purpose-block">
                        <div class="purpose-header">
                            <h3>Hair Care & Styling</h3>
                        </div>
                        <p class="large-text">
                            All modern hair styling services including haircuts, coloring, rebonding, and treatments.
                        </p>
                    </div>

                    <div class="purpose-block">
                        <div class="purpose-header">
                            <h3>Skin & Beauty Treatments</h3>
                        </div>
                        <p class="large-text">
                            Premium facials, skin-glowing treatments, and bridal makeups tailored to suit your skin.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. PRODUCTS SECTION -->
        <section class="section-container products-section">
            <div class="section-header text-center">
                <span class="section-badge">EXCLUSIVE PRODUCTS</span>
                <h2 class="large-title">Our Quality <span class="gradient-text">Beauty Products</span></h2>
            </div>

            <div class="products-grid">
                <div class="product-card">
                    <div class="product-img-box">
                        <img src="../uploads/images/salon/service-4.jpg" alt="Product 1">
                    </div>
                    <h3>Knotless Box Braid</h3>
                    <p class="large-text">High Taper Fade with Box Braids Top Knot is a modern and stylish men’s hairstyle. The sides and back are cut into a high taper fade, while the hair on top is divided into neat square sections and braided into box braids. The braids are then tied into a top knot, creating a clean and fashionable look.</p>
                </div>

                <div class="product-card">
                    <div class="product-img-box">
                        <img src="../uploads/images/salon/product-2.jpg" alt="Product 2"> 
                    </div>
                    <h3>Hair Colouring</h3>
                    <p class="large-text">This hairstyle features a bold black and deep red color combination, creating a modern and eye-catching look. The black base adds depth, while the vibrant red highlights bring contrast and personality. This color style is popular among people who want a unique, confident, and fashionable appearance.</p>
                </div>
            </div>
        </section>

        <!-- 6. EXPERT TEAM SECTION -->
        <section class="section-container team-section">
            <div class="section-header text-center">
                <span class="section-badge"><i class="fas fa-users"></i> EXPERT STYLISTS</span>
                <h2 class="large-title">Meet Our <span class="gradient-text">Professional Team</span></h2>
            </div>

            <div class="team-grid">
                <div class="team-card">
                    <div class="team-img-box">
                        <img src="../uploads/images/salon/service-1.jpg" alt="Team Member 1" onerror="this.src='https://via.placeholder.com/300x400?text=Senior+Stylist'">
                    </div>
                    <h3>Senior Hair Designer</h3>
                    <p class="large-text">Leading hairstylists with over 8 years of experience.</p>
                </div>

                <div class="team-card">
                    <div class="team-img-box">
                        <img src="../uploads/images/salon/service-1.jpg" alt="Team Member 2" onerror="this.src='https://via.placeholder.com/300x400?text=Beauty+Expert'">
                    </div>
                    <h3>Skin & Bridal Specialist</h3>
                    <p class="large-text">International-level experts in beauty and bridal styling.</p>
                </div>
            </div>
        </section>

        <!-- 7. CLIENT FEEDBACK & REVIEWS SECTION -->
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
    <script src="frontend-java-script/script.js"></script>
    <script src="frontend-java-script/comment.js"></script>
</body>
</html>