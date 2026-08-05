<?php
// Needed configuration or header files
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Our Services - Salon You</title>
    
    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS File -->
    <link rel="stylesheet" href="style.css?v=4.0">
</head>
<body class="luxury-theme">

    <!-- MAIN NAVIGATION BAR -->
    <header class="site-header">
        <a href="index.php" class="logo-container">
            <img src="logo/logo.png" alt="Salon You Logo" class="site-logo">
            <span class="logo-text">SALON YOU</span>
        </a>

        <nav class="main-nav">
            <a href="index.php" class="nav-link">Home</a>
            <a href="services.php" class="nav-link active">Services</a>
            <a href="index.php#gallery" class="nav-link">Gallery</a>
            <a href="about.php" class="nav-link">About Us</a>
            <a href="index.php#contact" class="nav-link">Contact</a>
        </nav>

        <div class="header-actions">
            <a href="register.php" class="btn-register">Register</a>
        </div>
    </header>

    <!-- SERVICES HERO -->
    <section class="services-hero">
        <div class="luxury-container text-center">
            <span class="gold-subtitle">OUR SERVICES</span>
            <h1 class="luxury-heading">EXCLUSIVE HAIR & BEAUTY <br><span class="gold-text">SERVICES</span></h1>
            <p class="about-desc">We use only the finest products, carefully selected for their quality and performance.</p>
        </div>
    </section>

    <!-- STYLIST SELECTION SECTION -->
    <section class="stylist-section">
        <div class="luxury-container text-center">
            <span class="gold-subtitle">STEP 1</span>
            <h2 class="luxury-heading">CHOOSE YOUR <span class="gold-text">STYLIST</span></h2>
            <p class="about-desc">Select your preferred expert before choosing a service.</p>

            <!-- Stylists Cards Grid -->
            <div class="stylist-grid">
                
                <!-- Stylist 1 -->
                <div class="stylist-card active" data-stylist="Sahan Madhawa">
                    <div class="stylist-img-box">
                        <img src="uploads/images/salon/stylist-1.jpg" alt="Sahan Madhawa">
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="stylist-info">
                        <h3>Sahan Madhawa</h3>
                        <p class="stylist-role">Master Barber</p>
                        <div class="stylist-rating">
                            <i class="fas fa-star"></i> 4.9 <span>(120+ reviews)</span>
                        </div>
                    </div>
                </div>

                <!-- Stylist 2 -->
                <div class="stylist-card" data-stylist="Supun Prabath">
                    <div class="stylist-img-box">
                        <img src="uploads/images/salon/stylist-2.jpg" alt="Supun Prabath">
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="stylist-info">
                        <h3>Supun Prabath</h3>
                        <p class="stylist-role">Senior Hair Artist</p>
                        <div class="stylist-rating">
                            <i class="fas fa-star"></i> 4.8 <span>(95+ reviews)</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- INTERACTIVE CATEGORY TABS BAR -->
    <section class="brand-bar">
        <div class="luxury-container brand-grid">
            <button class="category-btn active" data-category="mens-haircuts">
                <i class="fas fa-scissors"></i> MEN'S HAIRCUTS
            </button>
            <button class="category-btn" data-category="beard-cuts">
                <i class="fas fa-user-ninja"></i> BEARD CUTS
            </button>
            <button class="category-btn" data-category="ladies-haircuts">
                <i class="fas fa-female"></i> LADIES HAIRCUTS
            </button>
            <button class="category-btn" data-category="coloring">
                <i class="fas fa-paint-brush"></i> COLORING
            </button>
            <button class="category-btn" data-category="facials">
                <i class="fas fa-spa"></i> FACIALS
            </button>
        </div>
    </section>

    <!-- SERVICES CARDS GRID -->
    <section class="services-list-section">
        <div class="luxury-container">
            <div class="services-grid" id="services-container">
                
                <!-- 1. Men's Haircuts (Default Visible) -->
                <div class="luxury-service-card" data-category="mens-haircuts" data-service="Buzz Cut" data-price="350">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/BuzzCut.jpeg" alt="Buzz Cut">
                        <span class="luxury-price">Rs. 350</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Buzz Cut</h3>
                        <p>Clean and sharp buzz cut with a fresh fade for neat, low-maintenance look.</p>
                    </div>
                </div>

                <div class="luxury-service-card" data-category="mens-haircuts" data-service="Caesar Cut" data-price="350">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/CaesarCut.jpeg" alt="Caesar Cut">
                        <span class="luxury-price">Rs. 350</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Caesar Cut</h3>
                        <p>Modern caesar cut with a textured fringe and clean fade for sharp, stylish look.</p>
                    </div>
                </div>

                <div class="luxury-service-card" data-category="mens-haircuts" data-service="Crew Cut" data-price="350">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/CrewCut.jpeg" alt="Crew cut">
                        <span class="luxury-price">Rs. 350</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Crew Cut</h3>
                        <p>Classic crew cut with a clean fade and textured top for smart, modern look.</p>
                    </div>
                </div>

                <div class="luxury-service-card" data-category="mens-haircuts" data-service="Burst Fade" data-price="400">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/BurstFade.jpeg" alt="Burst Fade">
                        <span class="luxury-price">Rs. 400</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Burst Fade</h3>
                        <p>Modern burst fade with seamless blending and a textured top for bold, stylish look.</p>
                    </div>
                </div>

                <div class="luxury-service-card" data-category="mens-haircuts" data-service="Drop Fade" data-price="350">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/DropFade.jpeg" alt="Drop Fade">
                        <span class="luxury-price">Rs. 350</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Drop Fade</h3>
                        <p>Modern drop fade with smooth blending and a textured top for a clean, stylish look.</p>
                    </div>
                </div>

                <div class="luxury-service-card" data-category="mens-haircuts" data-service="French Crop" data-price="400">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/FrenchCrop.jpeg" alt="French Crop">
                        <span class="luxury-price">Rs. 400</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>French Crop</h3>
                        <p>Modern french crop with a textured fringe and clean fade for a fresh, stylish look.</p>
                    </div>
                </div>

                <div class="luxury-service-card" data-category="mens-haircuts" data-service="Mohawk Cut" data-price="400">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/Mohawk.jpeg" alt="Mohawk cut">
                        <span class="luxury-price">Rs. 400</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Mohawk Cut</h3>
                        <p>Bold mohawk with clean faded sides and a textured top for a modern, edgy look.</p>
                    </div>
                </div>

                <div class="luxury-service-card" data-category="mens-haircuts" data-service="Mullet Cut" data-price="400">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/Mullet.jpeg" alt="Mullet cut">
                        <span class="luxury-price">Rs. 400</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Mullet Cut</h3>
                        <p>Modern mullet with a textured top, clean fade and longer back for bold, stylish look.</p>
                    </div>
                </div>

                <div class="luxury-service-card" data-category="mens-haircuts" data-service="Pompadour Cut" data-price="400">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/Pompadour.jpeg" alt="Pompadour cut">
                        <span class="luxury-price">Rs. 400</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Pompadour Cut</h3>
                        <p>Classic pompadour with a voluminous top and clean fade for a timeless, stylish look.</p>
                    </div>
                </div>

                <div class="luxury-service-card" data-category="mens-haircuts" data-service="Quiff Cut" data-price="400">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/Quiff.jpeg" alt="Quiff cut">
                        <span class="luxury-price">Rs. 400</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Quiff Cut</h3>
                        <p>Modern quiff with a voluminous textured top and clean fade for sharp, stylish look.</p>
                    </div>
                </div>

                <div class="luxury-service-card" data-category="mens-haircuts" data-service="Side Part Cut" data-price="400">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/SidePart.jpeg" alt="Side part cut">
                        <span class="luxury-price">Rs. 400</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Side Part Cut</h3>
                        <p>Classic side part with a clean fade and defined parting for a sharp, professional look.</p>
                    </div>
                </div>

                <div class="luxury-service-card" data-category="mens-haircuts" data-service="Skin Fade" data-price="400">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/SkinFade.jpeg" alt="SkinFade cut">
                        <span class="luxury-price">Rs. 400</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Skin Fade</h3>
                        <p>Clean skin fade with seamless blending and a sharp finish for a fresh, modern look.</p>
                    </div>
                </div>

                <div class="luxury-service-card" data-category="mens-haircuts" data-service="Slick Back" data-price="400">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/SlickBack.jpeg" alt="SlickBack cut">
                        <span class="luxury-price">Rs. 400</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Slick Back</h3>
                        <p>Classic slick back with a smooth finish and clean fade for sleek, sophisticated look.</p>
                    </div>
                </div>

                <div class="luxury-service-card" data-category="mens-haircuts" data-service="Taper Fade" data-price="400">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/TaperFade.jpeg" alt="Taper fade cut">
                        <span class="luxury-price">Rs. 400</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Taper Fade</h3>
                        <p>Modern taper fade with smooth blending and a clean finish for sharp, stylish look.</p>
                    </div>
                </div>

                <div class="luxury-service-card" data-category="mens-haircuts" data-service="Textured Crop" data-price="400">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/TexturedCrop.jpeg" alt="Textured crop cut">
                        <span class="luxury-price">Rs. 400</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Textured Crop</h3>
                        <p>Modern textured crop with a layered top and clean fade for a fresh, stylish look.</p>
                    </div>
                </div>

                <div class="luxury-service-card" data-category="mens-haircuts" data-service="Two Block Cut" data-price="400">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/TwoBlock.jpeg" alt="TwoBlock cut">
                        <span class="luxury-price">Rs. 400</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Two Block Cut</h3>
                        <p>Trendy two block cut with a textured top and neatly tapered sides for a clean, modern look.</p>
                    </div>
                </div>

                <div class="luxury-service-card" data-category="mens-haircuts" data-service="Under Cut" data-price="400">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/UnderCut.jpeg" alt="Under cut">
                        <span class="luxury-price">Rs. 400</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Under Cut</h3>
                        <p>Modern under cut with a textured top and clean sides for a bold, stylish look.</p>
                    </div>
                </div>

                <!-- 2. Beard Cuts -->
<div class="luxury-service-card" data-category="beard-cuts" data-service="Full Beard" data-price="400" style="display: none;">
    <div class="card-img-box">
        <img src="uploads/images/hairstyles/FullBeard.jpeg" alt="Full Beard">
        <span class="luxury-price">Rs. 300</span>
        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
    </div>
    <div class="card-body">
        <h3>Full Beard</h3>
        <p>Classic full beard with clean shaping for a bold, masculine look.</p>
    </div>
</div>

<div class="luxury-service-card" data-category="beard-cuts" data-service="Goatee Beard" data-price="400" style="display: none;">
    <div class="card-img-box">
        <img src="uploads/images/hairstyles/gotee.jpeg" alt="Goatee Beard">
        <span class="luxury-price">Rs. 300</span>
        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
    </div>
    <div class="card-body">
        <h3>Goatee Beard</h3>
        <p>Neatly trimmed goatee style for a sharp and defined chin look.</p>
    </div>
</div>

<div class="luxury-service-card" data-category="beard-cuts" data-service="Stubble Beard " data-price="400" style="display: none;">
    <div class="card-img-box">
        <img src="uploads/images/hairstyles/StumbbleBeard.jpeg" alt="Stubble Beard">
        <span class="luxury-price">Rs. 300</span>
        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
    </div>
    <div class="card-body">
        <h3>Stubble Trim</h3>
        <p>Clean and well-maintained short stubble trim for an effortless look.</p>
    </div>
</div>

<div class="luxury-service-card" data-category="beard-cuts" data-service="Anchor Beard" data-price="400" style="display: none;">
    <div class="card-img-box">
        <img src="uploads/images/hairstyles/AnchorBeard.jpeg" alt="Anchor Beard">
        <span class="luxury-price">Rs. 200</span>
        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
    </div>
    <div class="card-body">
        <h3>Anchor Beard</h3>
        <p>Sharp anchor beard with precise datailing for a sleek, modern style.</p>
    </div>
</div>


<div class="luxury-service-card" data-category="beard-cuts" data-service="Balbo Beard" data-price="400" style="display: none;">
    <div class="card-img-box">
        <img src="uploads/images/hairstyles/BalboBeard.jpeg" alt="Balbo Beard">
        <span class="luxury-price">Rs. 300</span>
        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
    </div>
    <div class="card-body">
        <h3>Balbo Beard</h3>
        <p>Stylish balbo beard with clean line and well-defined shape for a bold look.</p>
    </div>
</div>

<div class="luxury-service-card" data-category="beard-cuts" data-service="Duck Tail Beard " data-price="400" style="display: none;">
    <div class="card-img-box">
        <img src="uploads/images/hairstyles/DuckTailBeard.jpeg" alt="Duck Tail Beard">
        <span class="luxury-price">Rs. 300</span>
        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
    </div>
    <div class="card-body">
        <h3>Duck Tail Beard</h3>
        <p>full duck tail beard with a tapered finish for a refined,masculine appearence.</p>
    </div>
</div>

<div class="luxury-service-card" data-category="beard-cuts" data-service="Short Box Beard" data-price="400" style="display: none;">
    <div class="card-img-box">
        <img src="uploads/images/hairstyles/shortBox.jpeg" alt="Short Box Beard">
        <span class="luxury-price">Rs. 300</span>
        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
    </div>
    <div class="card-body">
        <h3>Short Box Beard </h3>
        <p>Neatly trimmed short boxed beard for a sharp, modern appearence.</p>
    </div>
</div>

<div class="luxury-service-card" data-category="beard-cuts" data-service="Van Dyke Beard" data-price="400" style="display: none;">
    <div class="card-img-box">
        <img src="uploads/images/hairstyles/VandykeBeard.jpeg" alt="Van Dyke Beard">
        <span class="luxury-price">Rs. 200</span>
        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
    </div>
    <div class="card-body">
        <h3>Van Dyke Beard</h3>
        <p>Modern Van dyke beard with a defined mustache and pointed beard for a sophisticated appearance.</p>
    </div>
</div>

<div class="luxury-service-card" data-category="beard-cuts" data-service="Full Shaved" data-price="400" style="display: none;">
    <div class="card-img-box">
        <img src="uploads/images/hairstyles/FullShaved.jpeg" alt=" Full Shaved">
        <span class="luxury-price">Rs. 200</span>
        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
    </div>
    <div class="card-body">
        <h3>Full Shaved</h3>
        <p>Clean full Shave with a smooth finish for a fresh, sharp, and polished look.</p>
    </div>
</div>

                <!-- 3. Ladies Haircuts -->
                <div class="luxury-service-card" data-category="ladies-haircuts" data-service="Blunt Cut" data-price="2500" style="display: none;">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/Blunt.jpeg" alt="Blunt Cut">
                        <span class="luxury-price">Rs. 2,500</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Blunt Cut </h3>
                        <p>Classic blunt cut with a sleek finish for a clean, elegent and timeless lokk.</p>
                    </div>
                </div>

                 <div class="luxury-service-card" data-category="ladies-haircuts" data-service="Bob Cut" data-price="2500" style="display: none;">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/BobCut.jpeg" alt="Bob Cut">
                        <span class="luxury-price">Rs. 2,500</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Bob Cut </h3>
                        <p>Classic bob cut with a sleek finish for a chic, timless look.</p>
                    </div>
                </div>

                 <div class="luxury-service-card" data-category="ladies-haircuts" data-service="Butterfly Cut" data-price="2500" style="display: none;">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/ButterflyCut.jpeg" alt="Butterfly Cut">
                        <span class="luxury-price">Rs. 2,500</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Butterfly Cut </h3>
                        <p>Trendy butterfly cut with soft layers for a voluminous, modern look.</p>
                    </div>
                </div>

                 <div class="luxury-service-card" data-category="ladies-haircuts" data-service="Curtain Bangs Cut" data-price="2500" style="display: none;">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/CurtainBangs.jpeg" alt="Curtain Bangs Cut">
                        <span class="luxury-price">Rs. 2,500</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Curtain Bangs Cut </h3>
                        <p>Soft curtain bangs with face-framing layers for a fresh, elegent appeatance.</p>
                    </div>
                </div>

                 <div class="luxury-service-card" data-category="ladies-haircuts" data-service="Feather Cut" data-price="2500" style="display: none;">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/FeatherCut.jpeg" alt="Feather Cut Cut">
                        <span class="luxury-price">Rs. 2,500</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Feather Cut </h3>
                        <p>Soft feather cut with layered stuling for a light, voluminous and elegent look.</p>
                    </div>
                </div>

                 <div class="luxury-service-card" data-category="ladies-haircuts" data-service="Layer Cut" data-price="2500" style="display: none;">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/LayerCut.jpeg" alt="Layer Cut">
                        <span class="luxury-price">Rs. 2,500</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Layer Cut </h3>
                        <p>Soft layer cut with natural movement and volume for a stylish, elegant apperence.</p>
                    </div>
                </div>

                 <div class="luxury-service-card" data-category="ladies-haircuts" data-service="Lob Cut" data-price="2500" style="display: none;">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/LobCut.jpeg" alt="Lob Cut">
                        <span class="luxury-price">Rs. 2,500</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Lob Cut </h3>
                        <p>Modern lob cut with soft layers and a sleek finish for a chic,elegent look.</p>
                    </div>
                </div>

                 <div class="luxury-service-card" data-category="ladies-haircuts" data-service="Pixie Cut" data-price="2500" style="display: none;">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/PixieCut.jpeg" alt="Pixie Cut">
                        <span class="luxury-price">Rs. 2,500</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Pixie Cut </h3>
                        <p>Chic pixie cut with a clean finish for a confident, stylish look.</p>
                    </div>
                </div>

                 <div class="luxury-service-card" data-category="ladies-haircuts" data-service="Shag Cut" data-price="2500" style="display: none;">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/ShagCut.jpeg" alt="Shag Cut">
                        <span class="luxury-price">Rs. 2,500</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Shag Cut </h3>
                        <p>Trendy shag cut with textured layers and soft volume for a bold, modern look.</p>
                    </div>
                </div>

                 <div class="luxury-service-card" data-category="ladies-haircuts" data-service="U Cut" data-price="2500" style="display: none;">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/UCut.jpeg" alt="U Cut">
                        <span class="luxury-price">Rs. 2,500</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>U Cut </h3>
                        <p>Classic u cut with smooth layers for a soft, graceful finish.</p>
                    </div>
                </div>

                 <div class="luxury-service-card" data-category="ladies-haircuts" data-service="V Cut" data-price="2500" style="display: none;">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/VCut.jpeg" alt="V Cut">
                        <span class="luxury-price">Rs. 2,500</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>V Cut </h3>
                        <p>Elegent V cut with long flowing layers for a sleek, Stylish look.</p>
                    </div>
                </div>

                 <div class="luxury-service-card" data-category="ladies-haircuts" data-service="Wolf Cut" data-price="2500" style="display: none;">
                    <div class="card-img-box">
                        <img src="uploads/images/hairstyles/WolfCut.jpeg" alt="Wolf Cut">
                        <span class="luxury-price">Rs. 2,500</span>
                        <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                    </div>
                    <div class="card-body">
                        <h3>Wolf Cut </h3>
                        <p>Modern wolf cut with textured layers for a bold, trendy style.</p>
                    </div>
                </div>

<!-- 4. Coloring (With Bellosé & Dreamron Brand Toggle Buttons) -->
<div class="luxury-service-card color-chart-container" data-category="coloring" style="display: none; grid-column: 1 / -1;">
    
    <!-- BRAND TOGGLE BUTTONS -->
    <div class="brand-toggle-wrapper text-center">
        <button type="button" class="brand-toggle-btn active" data-chart="bellose">
            <i class="fas fa-palette"></i> BELLOSE COLOR CHART
        </button>
        <button type="button" class="brand-toggle-btn" data-chart="dreamron">
            <i class="fas fa-palette"></i> DREAMRON COLOR CHART
        </button>
    </div>

    <!--  BELLOSE CHART BOX  -->
<div id="bellose-chart-box" class="color-chart-box">
    <div class="chart-header text-center">
        <h3 class="gold-text">BELLOSE PROFESSIONAL COLOR CHART</h3>
        <p>SELECT YOUR COLORS.</p>
        <div class="chart-img-wrapper">
            <img src="uploads/images/hairstyles/bellosechart.jpeg" alt="bellose"> class="dreamron-chart-img">
        </div>
    </div>

    <div class="color-palette-scroll">
        
        <!-- 1. NATURAL SHADES -->
        <div class="color-group">
            <h4 class="color-cat-title"><i class="fas fa-circle"></i> Natural Shades</h4>
            <div class="color-options-grid">
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Bellosé 1.0 - Black (Natural)" data-price="3000">
                    <span class="color-dot" style="background-color: #0b0b0b;"></span>
                    <span class="color-name"><strong>1.0</strong> Black</span>
                    <span class="color-price">Rs. 3,000</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Bellosé 2.0 - Brown Black" data-price="3000">
                    <span class="color-dot" style="background-color: #1a1412;"></span>
                    <span class="color-name"><strong>2.0</strong> Brown Black</span>
                    <span class="color-price">Rs. 3,000</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Bellosé 3.0 - Dark Brown" data-price="3200">
                    <span class="color-dot" style="background-color: #2a1a14;"></span>
                    <span class="color-name"><strong>3.0</strong> Dark Brown</span>
                    <span class="color-price">Rs. 3,200</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Bellosé 4.0 - Medium Brown" data-price="3200">
                    <span class="color-dot" style="background-color: #422a1d;"></span>
                    <span class="color-name"><strong>4.0</strong> Medium Brown</span>
                    <span class="color-price">Rs. 3,200</span>
                </label>
            </div>
        </div>

        <!-- 2. AMMONIA FREE -->
        <div class="color-group">
            <h4 class="color-cat-title"><i class="fas fa-leaf"></i> Ammonia Free Shades</h4>
            <div class="color-options-grid">
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Bellosé 2.0 - Black (Ammonia Free)" data-price="3500">
                    <span class="color-dot" style="background-color: #0f0f0f; border: 1px solid #d4af37;"></span>
                    <span class="color-name"><strong>2.0</strong> Black</span>
                    <span class="color-price">Rs. 3,500</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Bellosé 3.0 - Dark Brown (Ammonia Free)" data-price="3500">
                    <span class="color-dot" style="background-color: #291c16; border: 1px solid #d4af37;"></span>
                    <span class="color-name"><strong>3.0</strong> Dark Brown</span>
                    <span class="color-price">Rs. 3,500</span>
                </label>
            </div>
        </div>

        <!-- 3. GOLD (0.3) -->
        <div class="color-group">
            <h4 class="color-cat-title"><i class="fas fa-star"></i> Gold Shades (0.3)</h4>
            <div class="color-options-grid">
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Bellosé 7.3 - Medium Golden Blonde" data-price="3800">
                    <span class="color-dot" style="background-color: #b5853b;"></span>
                    <span class="color-name"><strong>7.3</strong> Medium Golden Blonde</span>
                    <span class="color-price">Rs. 3,800</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Bellosé 8.3 - Light Golden Blonde" data-price="4000">
                    <span class="color-dot" style="background-color: #cfa04e;"></span>
                    <span class="color-name"><strong>8.3</strong> Light Golden Blonde</span>
                    <span class="color-price">Rs. 4,000</span>
                </label>
            </div>
        </div>

        <!-- 4. TEMPORARY COLORS -->
        <div class="color-group">
            <h4 class="color-cat-title"><i class="fas fa-paint-brush"></i> Temporary Colors</h4>
            <div class="color-options-grid">
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Bellosé Temporary Green" data-price="2500">
                    <span class="color-dot" style="background-color: #1b7a42;"></span>
                    <span class="color-name">Green</span>
                    <span class="color-price">Rs. 2,500</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Bellosé Temporary Red" data-price="2500">
                    <span class="color-dot" style="background-color: #a31c1c;"></span>
                    <span class="color-name">Red</span>
                    <span class="color-price">Rs. 2,500</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Bellosé Temporary Blue" data-price="2500">
                    <span class="color-dot" style="background-color: #1d3483;"></span>
                    <span class="color-name">Blue</span>
                    <span class="color-price">Rs. 2,500</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Bellosé Temporary Violet" data-price="2500">
                    <span class="color-dot" style="background-color: #582878;"></span>
                    <span class="color-name">Violet</span>
                    <span class="color-price">Rs. 2,500</span>
                </label>
            </div>
        </div>

        <!-- 5. BURGUNDY & REDS -->
        <div class="color-group">
            <h4 class="color-cat-title"><i class="fas fa-fire"></i> Burgundy & Red Shades</h4>
            <div class="color-options-grid">
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Bellosé 5.56 - Dark Chestnut Burgundy" data-price="3800">
                    <span class="color-dot" style="background-color: #3d121c;"></span>
                    <span class="color-name"><strong>5.56</strong> Dark Chestnut Burgundy</span>
                    <span class="color-price">Rs. 3,800</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Bellosé 6.56 - Dark Burgundy" data-price="3800">
                    <span class="color-dot" style="background-color: #521321;"></span>
                    <span class="color-name"><strong>6.56</strong> Dark Burgundy</span>
                    <span class="color-price">Rs. 3,800</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Bellosé 7.5 - Medium Red" data-price="4000">
                    <span class="color-dot" style="background-color: #8f2424;"></span>
                    <span class="color-name"><strong>7.5</strong> Medium Red</span>
                    <span class="color-price">Rs. 4,000</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Bellosé 8.5 - Light Red" data-price="4200">
                    <span class="color-dot" style="background-color: #a83232;"></span>
                    <span class="color-name"><strong>8.5</strong> Light Red</span>
                    <span class="color-price">Rs. 4,200</span>
                </label>
            </div>
        </div>

    </div>
</div>

   <!-- DREAMRON CHART BOX  -->
<div id="dreamron-chart-box" class="color-chart-box" style="display: none;">
    <div class="chart-header text-center">
        <h3 class="gold-text">DREAMRON PROFESSIONAL COLOR CHART</h3>
        <p>SELECT YOUR COLORS ...</p>
        <div class="chart-img-wrapper">
            <img src="uploads/images/hairstyles/dreamronchart.jpeg" alt="U Cut" class="dreamron-chart-img">
        </div>
    </div>

    <div class="color-palette-scroll">
        
        <!-- 1. NATURAL & BROWNS -->
        <div class="color-group">
            <h4 class="color-cat-title"><i class="fas fa-circle"></i> Natural & Brown Shades</h4>
            <div class="color-options-grid">
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 1.0 - Black" data-price="3000">
                    <span class="color-dot" style="background-color: #0a0a0a;"></span>
                    <span class="color-name"><strong>1.0</strong> Black</span>
                    <span class="color-price">Rs. 3,000</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 2.0 - Brown Black" data-price="3000">
                    <span class="color-dot" style="background-color: #141212;"></span>
                    <span class="color-name"><strong>2.0</strong> Brown Black</span>
                    <span class="color-price">Rs. 3,000</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 3.0 - Dark Brown" data-price="3200">
                    <span class="color-dot" style="background-color: #1f1a18;"></span>
                    <span class="color-name"><strong>3.0</strong> Dark Brown</span>
                    <span class="color-price">Rs. 3,200</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 4.0 - Medium Brown" data-price="3200">
                    <span class="color-dot" style="background-color: #2b1f1a;"></span>
                    <span class="color-name"><strong>4.0</strong> Medium Brown</span>
                    <span class="color-price">Rs. 3,200</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 5.0 - Light Brown" data-price="3200">
                    <span class="color-dot" style="background-color: #3d2c25;"></span>
                    <span class="color-name"><strong>5.0</strong> Light Brown</span>
                    <span class="color-price">Rs. 3,200</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 6.0 - Dark Blond" data-price="3500">
                    <span class="color-dot" style="background-color: #543d31;"></span>
                    <span class="color-name"><strong>6.0</strong> Dark Blond</span>
                    <span class="color-price">Rs. 3,500</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 8.0 - Light Blond" data-price="3800">
                    <span class="color-dot" style="background-color: #b3925c;"></span>
                    <span class="color-name"><strong>8.0</strong> Light Blond</span>
                    <span class="color-price">Rs. 3,800</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 1000 - Natural Blond" data-price="4000">
                    <span class="color-dot" style="background-color: #ebd9b2;"></span>
                    <span class="color-name"><strong>1000</strong> Natural Blond</span>
                    <span class="color-price">Rs. 4,000</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 2000" data-price="4200">
                    <span class="color-dot" style="background-color: #f0e2b8;"></span>
                    <span class="color-name"><strong>2000</strong> Light Blond</span>
                    <span class="color-price">Rs. 4,200</span>
                </label>
            </div>
        </div>

        <!-- 2. ASH & SILVER SHADES -->
        <div class="color-group">
            <h4 class="color-cat-title"><i class="fas fa-wind"></i> Ash & Silver Shades</h4>
            <div class="color-options-grid">
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 4.1 - Medium Ash Brown" data-price="3500">
                    <span class="color-dot" style="background-color: #333130;"></span>
                    <span class="color-name"><strong>4.1</strong> Medium Ash Brown</span>
                    <span class="color-price">Rs. 3,500</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 5.1 - Light Ash Brown" data-price="3500">
                    <span class="color-dot" style="background-color: #5c5a58;"></span>
                    <span class="color-name"><strong>5.1</strong> Light Ash Brown</span>
                    <span class="color-price">Rs. 3,500</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 9.1 - Very Light Ash Blond" data-price="4200">
                    <span class="color-dot" style="background-color: #aba9a4;"></span>
                    <span class="color-name"><strong>9.1</strong> Very Light Ash Blond</span>
                    <span class="color-price">Rs. 4,200</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 10.1 - Lightest Ash Blond" data-price="4500">
                    <span class="color-dot" style="background-color: #a3a19b;"></span>
                    <span class="color-name"><strong>10.1</strong> Lightest Ash Blond</span>
                    <span class="color-price">Rs. 4,500</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron SILVER" data-price="4500">
                    <span class="color-dot" style="background-color: #9e9e9e;"></span>
                    <span class="color-name">Silver</span>
                    <span class="color-price">Rs. 4,500</span>
                </label>
            </div>
        </div>

        <!-- 3. GOLD & PLATINUM SHADES -->
        <div class="color-group">
            <h4 class="color-cat-title"><i class="fas fa-star"></i> Gold & Platinum Shades</h4>
            <div class="color-options-grid">
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 6.3 - Dark Gold Blond" data-price="3800">
                    <span class="color-dot" style="background-color: #7a5229;"></span>
                    <span class="color-name"><strong>6.3</strong> Dark Gold Blond</span>
                    <span class="color-price">Rs. 3,800</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 8.3 - Light Gold Blond" data-price="3800">
                    <span class="color-dot" style="background-color: #634928;"></span>
                    <span class="color-name"><strong>8.3</strong> Light Gold Blond</span>
                    <span class="color-price">Rs. 3,800</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 8.33 - Intense Gold Light Blond" data-price="4000">
                    <span class="color-dot" style="background-color: #5c4922;"></span>
                    <span class="color-name"><strong>8.33</strong> Intense Gold Light Blond</span>
                    <span class="color-price">Rs. 4,000</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 9.33 - Intense Gold Very Light Blond" data-price="4200">
                    <span class="color-dot" style="background-color: #cb9b41;"></span>
                    <span class="color-name"><strong>9.33</strong> Intense Gold Very Light Blond</span>
                    <span class="color-price">Rs. 4,200</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 10.33 - Intense Gold Lightest Blond" data-price="4200">
                    <span class="color-dot" style="background-color: #d9a24a;"></span>
                    <span class="color-name"><strong>10.33</strong> Intense Gold Lightest Blond</span>
                    <span class="color-price">Rs. 4,200</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 11.3 - Platinum Light Gold Blond" data-price="4500">
                    <span class="color-dot" style="background-color: #e8cf9b;"></span>
                    <span class="color-name"><strong>11.3</strong> Platinum Light Gold Blond</span>
                    <span class="color-price">Rs. 4,500</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 11.33 - Intense Gold Platinum Light Blond" data-price="4500">
                    <span class="color-dot" style="background-color: #e8bf78;"></span>
                    <span class="color-name"><strong>11.33</strong> Intense Gold Platinum Light Blond</span>
                    <span class="color-price">Rs. 4,500</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 12.0 - Platinum Super Light Blond" data-price="4500">
                    <span class="color-dot" style="background-color: #f2ebd9;"></span>
                    <span class="color-name"><strong>12.0</strong> Platinum Super Light Blond</span>
                    <span class="color-price">Rs. 4,500</span>
                </label>
            </div>
        </div>

        <!-- 4. COPPER SHADES -->
        <div class="color-group">
            <h4 class="color-cat-title"><i class="fas fa-sun"></i> Copper & Coppery Blond Shades</h4>
            <div class="color-options-grid">
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 6.4 - Dark Copper Blond" data-price="3800">
                    <span class="color-dot" style="background-color: #6b3c26;"></span>
                    <span class="color-name"><strong>6.4</strong> Dark Copper Blond</span>
                    <span class="color-price">Rs. 3,800</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 6.44 - Intense Copper Dark Blond" data-price="3800">
                    <span class="color-dot" style="background-color: #8f3219;"></span>
                    <span class="color-name"><strong>6.44</strong> Intense Copper Dark Blond</span>
                    <span class="color-price">Rs. 3,800</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 7.4 - Medium Copper Blond" data-price="3800">
                    <span class="color-dot" style="background-color: #913d22;"></span>
                    <span class="color-name"><strong>7.4</strong> Medium Copper Blond</span>
                    <span class="color-price">Rs. 3,800</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 7.44 - Intense Copper Medium Blond" data-price="4000">
                    <span class="color-dot" style="background-color: #a6441b;"></span>
                    <span class="color-name"><strong>7.44</strong> Intense Copper Medium Blond</span>
                    <span class="color-price">Rs. 4,000</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 8.34 - Light Gold Coppery Blond" data-price="4000">
                    <span class="color-dot" style="background-color: #b3612a;"></span>
                    <span class="color-name"><strong>8.34</strong> Light Gold Coppery Blond</span>
                    <span class="color-price">Rs. 4,000</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 9.34 - Very Light Gold Coppery Blond" data-price="4200">
                    <span class="color-dot" style="background-color: #d87a38;"></span>
                    <span class="color-name"><strong>9.34</strong> Very Light Gold Coppery Blond</span>
                    <span class="color-price">Rs. 4,200</span>
                </label>
            </div>
        </div>

        <!-- 5. RED & MAHOGANY SHADES -->
        <div class="color-group">
            <h4 class="color-cat-title"><i class="fas fa-fire"></i> Red & Mahogany Shades</h4>
            <div class="color-options-grid">
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 0.66 - Red" data-price="4000">
                    <span class="color-dot" style="background-color: #c81d25;"></span>
                    <span class="color-name"><strong>0.66</strong> Red</span>
                    <span class="color-price">Rs. 4,000</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 4.5 - Medium Mahogany Brown" data-price="3800">
                    <span class="color-dot" style="background-color: #422622;"></span>
                    <span class="color-name"><strong>4.5</strong> Medium Mahogany Brown</span>
                    <span class="color-price">Rs. 3,800</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 5.6 - Light Red Brown" data-price="3800">
                    <span class="color-dot" style="background-color: #54181f;"></span>
                    <span class="color-name"><strong>5.6</strong> Light Red Brown</span>
                    <span class="color-price">Rs. 3,800</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 6.55 - Dark Mahogany Golden Blond" data-price="4000">
                    <span class="color-dot" style="background-color: #4a1d24;"></span>
                    <span class="color-name"><strong>6.55</strong> Dark Mahogany Golden Blond</span>
                    <span class="color-price">Rs. 4,000</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 6.6 - Dark Red Blond" data-price="4000">
                    <span class="color-dot" style="background-color: #731f24;"></span>
                    <span class="color-name"><strong>6.6</strong> Dark Red Blond</span>
                    <span class="color-price">Rs. 4,000</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 6.66 - Intense Red Dark Blond" data-price="4200">
                    <span class="color-dot" style="background-color: #631219;"></span>
                    <span class="color-name"><strong>6.66</strong> Intense Red Dark Blond</span>
                    <span class="color-price">Rs. 4,200</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 7.66 - Intense Red Medium Blond" data-price="4200">
                    <span class="color-dot" style="background-color: #821c21;"></span>
                    <span class="color-name"><strong>7.66</strong> Intense Red Medium Blond</span>
                    <span class="color-price">Rs. 4,200</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 8.55 - Light Mahogany Golden Blond" data-price="4200">
                    <span class="color-dot" style="background-color: #5e2321;"></span>
                    <span class="color-name"><strong>8.55</strong> Light Mahogany Golden Blond</span>
                    <span class="color-price">Rs. 4,200</span>
                </label>
            </div>
        </div>

        <!-- 6. VIOLET & FASHION SHADES -->
        <div class="color-group">
            <h4 class="color-cat-title"><i class="fas fa-palette"></i> Violet & Fashion Shades</h4>
            <div class="color-options-grid">
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 0.11 - Blue" data-price="3800">
                    <span class="color-dot" style="background-color: #12257d;"></span>
                    <span class="color-name"><strong>0.11</strong> Blue</span>
                    <span class="color-price">Rs. 3,800</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 0.22 - Violet" data-price="3800">
                    <span class="color-dot" style="background-color: #511c6d;"></span>
                    <span class="color-name"><strong>0.22</strong> Violet</span>
                    <span class="color-price">Rs. 3,800</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron GREEN" data-price="3800">
                    <span class="color-dot" style="background-color: #1d5232;"></span>
                    <span class="color-name">Green</span>
                    <span class="color-price">Rs. 3,800</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 0.33 - Yellow" data-price="3800">
                    <span class="color-dot" style="background-color: #eab019;"></span>
                    <span class="color-name"><strong>0.33</strong> Yellow</span>
                    <span class="color-price">Rs. 3,800</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 5.56 - Light Violet Reddish Brown" data-price="4000">
                    <span class="color-dot" style="background-color: #47182e;"></span>
                    <span class="color-name"><strong>5.56</strong> Light Violet Reddish Brown</span>
                    <span class="color-price">Rs. 4,000</span>
                </label>
                <label class="color-radio-card">
                    <input type="radio" name="hair_color" value="Dreamron 7.22 - Intense Violet Medium Blond" data-price="4000">
                    <span class="color-dot" style="background-color: #431c3d;"></span>
                    <span class="color-name"><strong>7.22</strong> Intense Violet Medium Blond</span>
                    <span class="color-price">Rs. 4,000</span>
                </label>
            </div>
        </div>

    </div>
</div>
                <!-- 5. Facials -->
               <!-- Facial & Treatment Services Container -->
<div class="luxury-service-card" data-category="facial" style="display: none; grid-column: 1 / -1;">
    <div class="chart-header text-center mb-4">
        <h3 class="gold-text">FACIAL & BEAUTY TREATMENTS</h3>
        <p>ඔබට අවශ්‍ය Treatment එක පහතින් Select කරන්න.</p>
    </div>

    <div class="treatments-grid">
        
        <!-- 1. Facial -->
        <label class="treatment-card">
            <input type="radio" name="hair_color" value="Facial Treatment" data-price="4500">
            <div class="treatment-card-content">
                <div class="treatment-icon"><i class="fas fa-spa"></i></div>
                <h4>Facial Treatment</h4>
                <p>Deep cleansing and revitalizing skin facial for a natural healthy glow.</p>
                <span class="treatment-price">Rs. 4,500</span>
            </div>
        </label>

        <!-- 2. Cleanup -->
        <label class="treatment-card">
            <input type="radio" name="hair_color" value="Face Cleanup" data-price="3000">
            <div class="treatment-card-content">
                <div class="treatment-icon"><i class="fas fa-shower"></i></div>
                <h4>Face Cleanup</h4>
                <p>Removes dead skin cells, blackheads, and deep dirt efficiently.</p>
                <span class="treatment-price">Rs. 3,000</span>
            </div>
        </label>

        <!-- 3. Piercing -->
        <label class="treatment-card">
            <input type="radio" name="hair_color" value="Ear & Nose Piercing" data-price="2500">
            <div class="treatment-card-content">
                <div class="treatment-icon"><i class="fas fa-gem"></i></div>
                <h4>Ear & Nose Piercing</h4>
                <p>Safe, pain-free, and hygienic piercing service with high precision.</p>
                <span class="treatment-price">Rs. 2,500</span>
            </div>
        </label>

        <!-- 4. Hair Straightening -->
        <label class="treatment-card">
            <input type="radio" name="hair_color" value="Hair Straightening" data-price="8500">
            <div class="treatment-card-content">
                <div class="treatment-icon"><i class="fas fa-cut"></i></div>
                <h4>Hair Straightening</h4>
                <p>Sleek, smooth, and manageable long-lasting straight hair treatment.</p>
                <span class="treatment-price">Rs. 8,500</span>
            </div>
        </label>

        <!-- 5. Hair Rebonding -->
        <label class="treatment-card">
            <input type="radio" name="hair_color" value="Hair Rebonding" data-price="12000">
            <div class="treatment-card-content">
                <div class="treatment-icon"><i class="fas fa-magic"></i></div>
                <h4>Hair Rebonding</h4>
                <p>Permanent straight hair transformation for silky and shiny hair.</p>
                <span class="treatment-price">Rs. 12,000</span>
            </div>
        </label>

        <!-- 6. Oil Treatment -->
        <label class="treatment-card">
            <input type="radio" name="hair_color" value="Hot Oil Hair Treatment" data-price="3500">
            <div class="treatment-card-content">
                <div class="treatment-icon"><i class="fas fa-droplet"></i></div>
                <h4>Oil Treatment</h4>
                <p>Nourishing hot oil therapy for scalp health and hair strength.</p>
                <span class="treatment-price">Rs. 3,500</span>
            </div>
        </label>

        <!-- 7. Head Massage -->
        <label class="treatment-card">
            <input type="radio" name="treatment_service" value="Relaxing Head Massage" data-price="2500">
            <div class="treatment-card-content">
                <div class="treatment-icon"><i class="fas fa-hands"></i></div>
                <h4>Head Massage</h4>
                <p>Deep relaxation pressure point massage to relieve stress and tension.</p>
                <span class="treatment-price">Rs. 2,500</span>
            </div>
        </label>

    </div>
</div>
    </section>

    <!-- FLOATING BOOKING SUMMARY BAR -->
    <div class="booking-summary-bar" id="bookingBar">
        <div class="summary-details">
            <div class="summary-item">
                <small>Stylist</small>
                <strong id="selectedStylist">Sahan Madhawa</strong>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-item">
                <small>Service</small>
                <strong id="selectedService">Not Selected</strong>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-item">
                <small>Total</small>
                <strong id="selectedPrice" class="gold-text">Rs. 0</strong>
            </div>
        </div>
        <a href="booking.php" class="btn-book-now" id="bookNowBtn">Book Appointment <i class="fas fa-arrow-right"></i></a>
    </div>

    <!-- COMBINED SCRIPT FOR FILTER & SELECTION LOGIC -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Category Filter Elements
            const categoryBtns = document.querySelectorAll('.category-btn');
            const serviceCards = document.querySelectorAll('.luxury-service-card');

            // Selection Variables
            let selectedStylistName = "Sahan Madhawa"; // Default active stylist
            let selectedServiceName = "";
            let selectedServicePrice = "";

            // Selection Elements
            const stylistCards = document.querySelectorAll('.stylist-card');
            const bookingBar = document.getElementById('bookingBar');
            const txtStylist = document.getElementById('selectedStylist');
            const txtService = document.getElementById('selectedService');
            const txtPrice = document.getElementById('selectedPrice');
            const bookNowBtn = document.getElementById('bookNowBtn');

            // 1. CATEGORY FILTER LOGIC
            categoryBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    categoryBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    const selectedCategory = this.getAttribute('data-category');

                    serviceCards.forEach(card => {
                        if (card.getAttribute('data-category') === selectedCategory) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });

            // 2. STYLIST SELECTION
            stylistCards.forEach(card => {
                card.addEventListener('click', function() {
                    stylistCards.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    
                    selectedStylistName = this.getAttribute('data-stylist');
                    txtStylist.textContent = selectedStylistName;
                    updateBookingLink();
                });
            });

            // 3. SERVICE SELECTION
            serviceCards.forEach(card => {
                card.addEventListener('click', function() {
                    serviceCards.forEach(c => c.classList.remove('selected'));
                    this.classList.add('selected');

                    selectedServiceName = this.getAttribute('data-service');
                    selectedServicePrice = this.getAttribute('data-price');

                    txtService.textContent = selectedServiceName;
                    txtPrice.textContent = "Rs. " + selectedServicePrice;

                    bookingBar.classList.add('active');
                    updateBookingLink();
                });
            });

            // 4. UPDATE BOOKING URL PARAMETERS
            function updateBookingLink() {
                if (selectedServiceName !== "") {
                    const url = `booking.php?stylist=${encodeURIComponent(selectedStylistName)}&service=${encodeURIComponent(selectedServiceName)}&price=${encodeURIComponent(selectedServicePrice)}`;
                    bookNowBtn.setAttribute('href', url);
                }
            }
        });
    </script>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
    const brandBtns = document.querySelectorAll('.brand-toggle-btn');
    const belloseBox = document.getElementById('bellose-chart-box');
    const dreamronBox = document.getElementById('dreamron-chart-box');

    // 1. BRAND TOGGLE BUTTON CLICK LOGIC
    brandBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            brandBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const selectedChart = this.getAttribute('data-chart');

            if (selectedChart === 'bellose') {
                belloseBox.style.display = 'block';
                dreamronBox.style.display = 'none';
            } else if (selectedChart === 'dreamron') {
                belloseBox.style.display = 'none';
                dreamronBox.style.display = 'block';
            }
        });
    });

    // 2. RADIO SELECTION LOGIC FOR ALL BRAND COLORS
    const bookingBar = document.getElementById('bookingBar');
    const txtService = document.getElementById('selectedService');
    const txtPrice = document.getElementById('selectedPrice');
    const bookNowBtn = document.getElementById('bookNowBtn');

    document.addEventListener('change', function(e) {
        if (e.target && e.target.name === 'hair_color') {
            const selectedRadio = e.target;
            
            selectedServiceName = selectedRadio.value;
            selectedServicePrice = selectedRadio.getAttribute('data-price');

            txtService.textContent = selectedServiceName;
            txtPrice.textContent = "Rs. " + selectedServicePrice;

            bookingBar.classList.add('active');

            // Update URL Parameters for booking.php
            const url = `booking.php?stylist=${encodeURIComponent(selectedStylistName)}&service=${encodeURIComponent(selectedServiceName)}&price=${encodeURIComponent(selectedServicePrice)}`;
            bookNowBtn.setAttribute('href', url);
        }
    });
});
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
    
    // Category Button Click Logic (Facial, Hair, Coloring switch කිරීමට)
    const categoryBtns = document.querySelectorAll('[data-category-btn]');
    const categoryCards = document.querySelectorAll('[data-category]');

    categoryBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const selectedCategory = this.getAttribute('data-category-btn');

            categoryCards.forEach(card => {
                if (card.getAttribute('data-category') === selectedCategory) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Booking Bar Price and Service Updater Logic
    const bookingBar = document.getElementById('bookingBar');
    const txtService = document.getElementById('selectedService');
    const txtPrice = document.getElementById('selectedPrice');
    const bookNowBtn = document.getElementById('bookNowBtn');

    document.addEventListener('change', function(e) {
        if (e.target && (e.target.name === 'hair_color' || e.target.name === 'treatment_service')) {
            const selectedRadio = e.target;
            
            const serviceName = selectedRadio.value;
            const servicePrice = selectedRadio.getAttribute('data-price');

            txtService.textContent = serviceName;
            txtPrice.textContent = "Rs. " + servicePrice;

            if (bookingBar) {
                bookingBar.classList.add('active');
            }

            // Booking Page Redirect URL Update
            if (bookNowBtn) {
                const url = `booking.php?service=${encodeURIComponent(serviceName)}&price=${encodeURIComponent(servicePrice)}`;
                bookNowBtn.setAttribute('href', url);
            }
        }
    });
});
    </script>
</body>
</html>