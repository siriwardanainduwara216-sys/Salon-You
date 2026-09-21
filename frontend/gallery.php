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
    <?php include_once __DIR__ . '/indexheader.php'; ?>

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
                    <img src="/salon-you-main/uploads/images/salon/gallery/gallery1.jpeg" alt="Baby Cut 1">
                   
                </div>
            </div>

            <div class="swiper-slide">
                <div class="gallery-card">
                    <img src="/salon-you-main/uploads/images/salon/gallery/womens_colour3.jpeg" alt="Baby Cut 2">
                    
                </div>
            </div>

            <div class="swiper-slide">
                <div class="gallery-card">
                    <img src="/salon-you-main/uploads/images/salon/gallery/colour8.jpeg" alt="Baby Cut 3">
                    
                </div>
            </div>

            <div class="swiper-slide">
                <div class="gallery-card">
                    <img src="/salon-you-main/uploads/images/salon/gallery/baby6.jpeg" alt="Baby Cut 4">
                    
                </div>
            </div>

             <div class="swiper-slide">
                <div class="gallery-card">
                    <img src="/salon-you-main/uploads/images/salon/gallery/cut3.jpeg" alt="Baby Cut 4">
                   
                </div>
            </div>

             <div class="swiper-slide">
                <div class="gallery-card">
                    <img src="/salon-you-main/uploads/images/salon/gallery/womens_colour2.jpeg" alt="Baby Cut 4">
                   
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
                                <img src="/salon-you-main/uploads/images/salon/gallery/gallery1.jpeg" alt="Modern Fade Cut">
                                
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut1.jpeg" alt="Classic Gents Trim">
                                
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut2.jpeg" alt="Textured Crop Cut">
                               
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut3.jpeg" alt="Beard & Hair Combo">
                               
                            </div>
                        </div>

                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/gallery2.jpeg" alt="Beard & Hair Combo">
                               
                            </div>
                        </div>
                        
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut4.jpeg" alt="Buzz Cut Styling">
                                
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut5.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut6.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut7.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut9.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut10.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut11.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut12.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut13.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut15.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut16.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut19.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut20.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut21.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut22.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut23.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut24.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut25.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut26.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut27.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                         <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut28.jpeg" alt="Gentlemen Pompadour">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- CATEGORY 2: Men's HAIR COLOURING -->
            <div class="category-section" id="cat-colouring">
                <div class="category-header">
                    <h2 class="category-title">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>Men's Hair Colouring
                    </h2>
                    <button class="see-more-btn" onclick="toggleCategory('cat-colouring', colouringSwiper)">
                        <span>See More</span> <i class="fa-solid fa-chevron-down"></i>
                    </button>
                </div>

                <div class="swiper slider-colouring">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/colour2.jpeg" alt="Balayage Blonde">
                               
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/colour4.jpeg" alt="Ash Brown Color">
                                
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/colour5.jpeg" alt="Burgundy Red Glow">
                               
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/colour6.jpeg" alt="Ombre Color Shift">
                                
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/colour7.jpeg" alt="Platinum Blonde">
                                
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/colour8.jpeg" alt="Platinum Blonde">
                                
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/colour9.jpeg" alt="Platinum Blonde">
                                
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut8.jpeg" alt="Platinum Blonde">
                                
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/cut26.jpeg" alt="Platinum Blonde">
                                
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
                                <img src="/salon-you-main/uploads/images/salon/gallery/womens6.jpeg" alt="Layered Bob Cut">
                               
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/womens5.jpeg" alt="Modern Pixie Cut">
                               
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/womens4.jpeg" alt="Long Feather Cut">
                               
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/womens3.jpeg" alt="Curtain Bangs">
                               
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="gallery-card">
                                <img src="/salon-you-main/uploads/images/salon/gallery/womens2.jpeg" alt="Asymmetrical Bob">
                               
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div>

            <!-- CATEGORY 3: WOMEN'S COLOR -->
<div class="category-section" id="cat-womens-colour">
    <div class="category-header">
        <h2 class="category-title">
            <i class="fa-solid fa-person-dress"></i> Women's Coloring
        </h2>
        <button class="see-more-btn" onclick="toggleCategory('cat-womens-colour', womensColourSwiper)">
            <span>See More</span> <i class="fa-solid fa-chevron-down"></i>
        </button>
    </div>

    <div class="swiper slider-womens-colour">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <div class="gallery-card">
                    <img src="/salon-you-main/uploads/images/salon/gallery/womens_colour1.jpeg" alt="Women's Colour 1">
                </div>
            </div>
            <div class="swiper-slide">
                <div class="gallery-card">
                    <img src="/salon-you-main/uploads/images/salon/gallery/womens_colour2.jpeg" alt="Women's Colour 2">
                </div>
            </div>
            <div class="swiper-slide">
                <div class="gallery-card">
                    <img src="/salon-you-main/uploads/images/salon/gallery/womens_colour3.jpeg" alt="Women's Colour 3">
                </div>
            </div>
            <div class="swiper-slide">
                <div class="gallery-card">
                    <img src="/salon-you-main/uploads/images/salon/gallery/womens_colour4.jpeg" alt="Women's Colour 4">
                </div>
            </div>
            <div class="swiper-slide">
                <div class="gallery-card">
                    <img src="/salon-you-main/uploads/images/salon/gallery/womens_colour5.jpeg" alt="Women's Colour 5">
                </div>
            </div>
            <div class="swiper-slide">
                <div class="gallery-card">
                    <img src="/salon-you-main/uploads/images/salon/gallery/womens_colour6.jpeg" alt="Women's Colour 6">
                </div>
            </div>
        </div>
    </div>
</div>
            

        </div>
    </section>

    <!-- INCLUDE FOOTER COMPONENT -->
    <?php include_once __DIR__ . '/indexfooter.php'; ?>

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
                1024: { slidesPerView: 4, spaceBetween: 20 },
            },
        });

        // Initialize Category Swipers
        let haircutsSwiper = new Swiper(".slider-haircuts", {
            slidesPerView: 1,
            spaceBetween: 20,
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 4, spaceBetween: 20 },
            },
        });

        let colouringSwiper = new Swiper(".slider-colouring", {
            slidesPerView: 1,
            spaceBetween: 20,
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 4, spaceBetween: 20 },
            },
        });

        let womensSwiper = new Swiper(".slider-womens", {
            slidesPerView: 1,
            spaceBetween: 20,
            breakpoints: {
                640: { slidesPerView: 2, spaceBetween: 20 },
                1024: { slidesPerView: 4, spaceBetween: 20 },
            },
        });

        let womensColourSwiper = new Swiper(".slider-womens-colour", {
    slidesPerView: 1,
    spaceBetween: 20,
    breakpoints: {
        640: { slidesPerView: 2, spaceBetween: 20 },
        1024: { slidesPerView: 4, spaceBetween: 20 },
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