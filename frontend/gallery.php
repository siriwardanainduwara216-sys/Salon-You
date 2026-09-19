<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$active_page = 'gallery';

$is_logged_in = isset($_SESSION['user_id']);
$user_role    = $_SESSION['user_role'] ?? '';
$user_name    = $_SESSION['user_name'] ?? 'Guest User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photo Gallery - Salon You</title>

    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <!-- CSS Files -->
    <link rel="stylesheet" href="frontend-css/style.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="frontend-css/gallery_style.css?v=<?php echo time(); ?>">
</head>
<body>

    <!-- INCLUDE HEADER COMPONENT -->
    <?php include_once __DIR__ . '/header.php'; ?>

    <!-- HERO BANNER SECTION -->
    <section class="gallery-hero-section" style="background-image: url('../uploads/images/salon/about_header.jpeg');">
        <div class="gallery-hero-container">
            <div class="gallery-hero-content">
                <h1 class="gallery-hero-title">Our Gallery</h1>
                <p class="gallery-hero-subtitle">
                    Explore Our Outstanding Portfolio of Hair, Beauty, and Grooming Transformations.
                </p>
            </div>
        </div>
    </section>

    <!-- MAIN GALLERY CONTENT SECTION -->
    <section class="gallery-main-section">
        <div class="gallery-container">

            <!-- MAIN TOP AUTOMATIC SLIDER SECTION -->
            <div class="main-slider-container">
                <div class="gallery-header-text">
                    <h2 class="section-title">FEATURED WORK</h2>
                    <p class="section-subtitle">Take a look at our highlighted salon transformations</p>
                </div>

                <div class="swiper main-auto-slider">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/servicebg2.jpeg" alt="Featured Style 1">
                                <div class="card-overlay">
                                    <h3 class="card-title">Modern Style & Finish</h3>
                                    <span class="card-badge">FEATURED</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/about_header.jpeg" alt="Featured Style 2">
                                <div class="card-overlay">
                                    <h3 class="card-title">Premium Hair Coloring</h3>
                                    <span class="card-badge">POPULAR</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/contactbg.jpeg" alt="Featured Style 3">
                                <div class="card-overlay">
                                    <h3 class="card-title">Precision Cut & Grooming</h3>
                                    <span class="card-badge">NEW</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/servicebg2.jpeg" alt="Featured Style 4">
                                <div class="card-overlay">
                                    <h3 class="card-title">Creative Hair Styling</h3>
                                    <span class="card-badge">TRENDING</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Pagination Dots -->
                    <div class="swiper-pagination main-slider-pagination"></div>
                </div>
            </div>


            <!-- CATEGORY 1: HAIR CUTS -->
            <div class="category-section" id="cat-haircuts">
                <div class="category-header">
                    <h2 class="category-title">
                        <i class="fa-solid fa-scissors"></i> Hair Cuts
                    </h2>
                    <button class="see-more-btn" onclick="toggleCategory('cat-haircuts', haircutsSwiper)">
                        <span>See More</span> <i class="fa-solid fa-chevron-down"></i>
                    </button>
                </div>

                <div class="swiper slider-haircuts">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/servicebg2.jpeg" alt="Modern Fade Cut">
                                <div class="card-overlay">
                                    <h3 class="card-title">Modern Fade Cut</h3>
                                    <span class="card-badge">FEATURED</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/about_header.jpeg" alt="Classic Gents Trim">
                                <div class="card-overlay">
                                    <h3 class="card-title">Classic Gents Trim</h3>
                                    <span class="card-badge">NEW</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/contactbg.jpeg" alt="Textured Crop Cut">
                                <div class="card-overlay">
                                    <h3 class="card-title">Textured Crop Cut</h3>
                                    <span class="card-badge">POPULAR</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/servicebg2.jpeg" alt="Beard & Hair Combo">
                                <div class="card-overlay">
                                    <h3 class="card-title">Beard & Hair Combo</h3>
                                    <span class="card-badge">TRENDING</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/about_header.jpeg" alt="Buzz Cut Styling">
                                <div class="card-overlay">
                                    <h3 class="card-title">Buzz Cut & Lineup</h3>
                                    <span class="card-badge">NEW</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/contactbg.jpeg" alt="Gentlemen Pompadour">
                                <div class="card-overlay">
                                    <h3 class="card-title">Gentlemen Pompadour</h3>
                                    <span class="card-badge">POPULAR</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- CATEGORY 2: HAIR COLOURING -->
            <div class="category-section" id="cat-colouring">
                <div class="category-header">
                    <h2 class="category-title">
                        <i class="fa-solid fa-wand-magic-sparkles"></i> Hair Colouring
                    </h2>
                    <button class="see-more-btn" onclick="toggleCategory('cat-colouring', colouringSwiper)">
                        <span>See More</span> <i class="fa-solid fa-chevron-down"></i>
                    </button>
                </div>

                <div class="swiper slider-colouring">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/about_header.jpeg" alt="Balayage Blonde">
                                <div class="card-overlay">
                                    <h3 class="card-title">Balayage Blonde Highlights</h3>
                                    <span class="card-badge">POPULAR</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/contactbg.jpeg" alt="Ash Brown Color">
                                <div class="card-overlay">
                                    <h3 class="card-title">Ash Brown Transformation</h3>
                                    <span class="card-badge">TRENDING</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/servicebg2.jpeg" alt="Burgundy Red Glow">
                                <div class="card-overlay">
                                    <h3 class="card-title">Burgundy Red Glow</h3>
                                    <span class="card-badge">FEATURED</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/about_header.jpeg" alt="Ombre Color Shift">
                                <div class="card-overlay">
                                    <h3 class="card-title">Ombre Color Shift</h3>
                                    <span class="card-badge">NEW</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/contactbg.jpeg" alt="Platinum Blonde">
                                <div class="card-overlay">
                                    <h3 class="card-title">Platinum Blonde Gloss</h3>
                                    <span class="card-badge">TRENDING</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- CATEGORY 3: WOMEN'S CUT -->
            <div class="category-section" id="cat-womens">
                <div class="category-header">
                    <h2 class="category-title">
                        <i class="fa-solid fa-person-dress"></i> Women's Cut
                    </h2>
                    <button class="see-more-btn" onclick="toggleCategory('cat-womens', womensSwiper)">
                        <span>See More</span> <i class="fa-solid fa-chevron-down"></i>
                    </button>
                </div>

                <div class="swiper slider-womens">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/contactbg.jpeg" alt="Layered Bob Cut">
                                <div class="card-overlay">
                                    <h3 class="card-title">Layered Bob Cut</h3>
                                    <span class="card-badge">FEATURED</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/servicebg2.jpeg" alt="Modern Pixie Cut">
                                <div class="card-overlay">
                                    <h3 class="card-title">Modern Pixie Cut</h3>
                                    <span class="card-badge">NEW</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/about_header.jpeg" alt="Long Feather Cut">
                                <div class="card-overlay">
                                    <h3 class="card-title">Long Feather Cut</h3>
                                    <span class="card-badge">POPULAR</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/contactbg.jpeg" alt="Curtain Bangs">
                                <div class="card-overlay">
                                    <h3 class="card-title">Curtain Bangs Styling</h3>
                                    <span class="card-badge">TRENDING</span>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="../uploads/images/salon/servicebg2.jpeg" alt="Asymmetrical Bob">
                                <div class="card-overlay">
                                    <h3 class="card-title">Asymmetrical Bob Cut</h3>
                                    <span class="card-badge">NEW</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    <!-- INCLUDE FOOTER COMPONENT -->
    <?php include_once __DIR__ . '/footer.php'; ?>

    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- INITIALIZE SLIDERS & SEE MORE TOGGLE -->
    <script>
        // Main Auto Changing Slider
        new Swiper(".main-auto-slider", {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".main-slider-pagination",
                clickable: true,
            },
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 25 },
            },
        });

        // Initialize Category Swipers
        let haircutsSwiper = new Swiper(".slider-haircuts", {
            slidesPerView: 1,
            spaceBetween: 20,
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 25 },
            },
        });

        let colouringSwiper = new Swiper(".slider-colouring", {
            slidesPerView: 1,
            spaceBetween: 20,
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 25 },
            },
        });

        let womensSwiper = new Swiper(".slider-womens", {
            slidesPerView: 1,
            spaceBetween: 20,
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 3, spaceBetween: 25 },
            },
        });

        // SEE MORE TOGGLE FUNCTION
        function toggleCategory(categoryId, swiperInstance) {
            const section = document.getElementById(categoryId);
            const btn = section.querySelector('.see-more-btn');
            const btnText = btn.querySelector('span');
            const btnIcon = btn.querySelector('i');

            if (section.classList.contains('expanded-grid')) {
                // Collapse back to Slider
                section.classList.remove('expanded-grid');
                btnText.textContent = 'See More';
                btnIcon.className = 'fa-solid fa-chevron-down';
                if (swiperInstance) swiperInstance.init();
            } else {
                // Expand to Grid
                section.classList.add('expanded-grid');
                btnText.textContent = 'See Less';
                btnIcon.className = 'fa-solid fa-chevron-up';
                if (swiperInstance) swiperInstance.destroy(false, true);
            }
        }
    </script>
</body>
</html>