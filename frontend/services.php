<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../backend/config.php';
global $conn;

require_once __DIR__ . '/../backend/services-data.php';

$active_page = 'services';

$is_logged_in = isset($_SESSION['user_id']);
$is_customer = $is_logged_in && ($_SESSION['user_role'] ?? '') === 'customer';
$staff_list = $staff_list ?? [];
$services_by_category = $services_by_category ?? [];
$closed_dates = $closed_dates ?? [];
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

    <!-- CSS Files -->
    <link rel="stylesheet" href="frontend-css/style.css">
    <link rel="stylesheet" href="frontend-css/services.css?v=2.0">
    <link rel="stylesheet" href="frontend-css/services-modal.css">
<style>

/* ==========================================
   1. FULL-WIDTH PREMIUM ASH SECTIONS & CONTAINERS
   ========================================== */
.brand-bar,
.services-list-section,
.stylist-section {
    width: 100vw !important;
    position: relative !important;
    left: 50% !important;
    right: 50% !important;
    margin-left: -50vw !important;
    margin-right: -50vw !important;
    background: #f4f6f8 !important;
    background-color: #f4f6f8 !important;
    padding: 40px 0 !important;
    border-radius: 0 !important;
    border: none !important;
    box-shadow: none !important;
}

.brand-bar .luxury-container,
.services-list-section .luxury-container,
.stylist-section .luxury-container,
.stylist-section .stylist-container {
    max-width: 1400px !important;
    width: 92% !important;
    margin: 0 auto !important;
    background: transparent !important;
    box-shadow: none !important;
    border: none !important;
    padding: 0 !important;
}

/* ==========================================
   2. SECTION HEADINGS & INSTRUCTIONS (CENTER & BOLD)
   ========================================== */
.brand-bar,
.brand-bar .luxury-container,
.services-filter-section {
    text-align: center !important;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100% !important;
}

.brand-bar h2,
.brand-bar h3,
.brand-bar .section-title,
.stylist-section h2,
.stylist-section h3.stylist-title-main {
    font-size: 2.1rem !important;
    font-weight: 800 !important;
    color: #0f172a !important;
    text-align: center !important;
    margin-bottom: 8px !important;
    letter-spacing: 0.5px;
}

.brand-bar p,
.brand-bar .section-desc,
.stylist-section p {
    font-size: 1.1rem !important;
    font-weight: 500 !important;
    color: #475569 !important;
    text-align: center !important;
    max-width: 850px !important;
    margin: 0 auto 15px auto !important;
    line-height: 1.5 !important;
}

/* ==========================================
   3. CATEGORY BUTTONS - CENTERED INSIDE THE BOX
   ========================================== */
#category-tabs,
.category-buttons,
.services-category-tabs {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 12px !important;
    width: 100% !important;
    max-width: 1400px !important;
    margin: 15px auto 30px auto !important;
    padding: 5px 15px 12px 15px !important;
    overflow-x: auto !important;
    scrollbar-width: thin;
}

/* Custom scrollbar styling */
#category-tabs::-webkit-scrollbar,
.category-buttons::-webkit-scrollbar,
.services-category-tabs::-webkit-scrollbar {
    height: 5px;
}

#category-tabs::-webkit-scrollbar-thumb,
.category-buttons::-webkit-scrollbar-thumb,
.services-category-tabs::-webkit-scrollbar-thumb {
    background: rgba(212, 175, 55, 0.4);
    border-radius: 10px;
}

#category-tabs .category-btn,
.category-btn,
.services-category-tabs button {
    background: linear-gradient(135deg, #1e293b, #0f172a) !important;
    color: #f8fafc !important;
    border: 1px solid rgba(212, 175, 55, 0.4) !important;
    border-radius: 30px !important;
    padding: 10px 22px !important;
    font-size: 0.9rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.5px !important;
    cursor: pointer !important;
    white-space: nowrap !important;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;
    flex-shrink: 0 !important;
    transition: all 0.3s ease !important;
}

#category-tabs .category-btn:hover,
.category-btn:hover,
.services-category-tabs button:hover,
#category-tabs .category-btn.active,
.category-btn.active,
.services-category-tabs button.active {
    background: linear-gradient(135deg, #d4af37, #aa7c11) !important;
    color: #0f172a !important;
    border-color: #d4af37 !important;
    transform: translateY(-3px) !important;
    box-shadow: 0 6px 20px rgba(212, 175, 55, 0.35) !important;
}

#category-tabs .category-btn i,
.category-btn i,
.services-category-tabs button i {
    color: #d4af37 !important;
    margin-right: 8px !important;
    transition: color 0.3s ease;
}

#category-tabs .category-btn:hover i,
#category-tabs .category-btn.active i,
.category-btn:hover i,
.services-category-tabs button:hover i,
.category-btn.active i,
.services-category-tabs button.active i {
    color: #0f172a !important;
}

/* ==========================================
   4. SERVICE CARDS - FORCED TALLER HEIGHT
   ========================================== */
.services-grid {
    max-width: 1400px !important;
    display: grid !important;
    grid-template-columns: repeat(3, 1fr) !important;
    gap: 25px !important;
    margin-top: 10px !important;
    width: 100% !important;
}

.service-card,
.services-grid .card,
.luxury-service-card {
    background: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 16px !important;
    padding: 24px !important;
    min-height: 660px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05) !important;
    transition: all 0.3s ease;
}

/* FIX: category filter uses this class to hide cards.
   Two classes = higher specificity than the rule above, so this wins. */
.luxury-service-card.is-hidden,
.service-card.is-hidden {
    display: none !important;
}

.service-card:hover,
.luxury-service-card:hover {
    transform: translateY(-5px);
    border-color: #d4af37 !important;
    box-shadow: 0 12px 28px rgba(212, 175, 55, 0.22);
}

.service-card .img-box,
.service-card .card-img-top,
.services-grid .img-box,
.card-img-box {
    width: 100% !important;
    height: 450px !important;
    background-color: #ffffff !important;
    border-radius: 12px !important;
    overflow: hidden !important;
    position: relative !important;
    flex-shrink: 0 !important;
}

.service-card .img-box img,
.services-grid .img-box img,
.card-img-box img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    object-position: center 20% !important;
}

/* ==========================================
   5. STYLIST CARDS & IMAGES
   ========================================== */
.stylist-grid {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)) !important;
    gap: 35px !important;
    margin-top: 30px !important;
    justify-content: center !important;
}

.stylist-card {
    background: #ffffff !important;
    border-radius: 16px !important;
    padding: 16px !important;
    border: 1px solid #cbd5e1 !important;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
}

.stylist-card:hover {
    transform: translateY(-6px);
    border-color: #d4af37 !important;
    box-shadow: 0 12px 28px rgba(212, 175, 55, 0.25);
}

/* FIX: name was white on a white card */
.stylist-card .stylist-info h3 {
    color: #0f172a !important;
    font-size: 1.1rem;
}

.stylist-card .stylist-info .stylist-role {
    color: #aa7c11 !important;
    font-size: 0.85rem;
}

.stylist-card .stylist-img-box {
    width: 100% !important;
    height: 420px !important;
    position: relative !important;
    overflow: hidden !important;
    border-radius: 12px !important;
    background-color: #ffffff !important;
}

.stylist-card .stylist-img-box img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    object-position: center 20% !important;
    display: block !important;
}

/* ==========================================
   6. CARD BODY & AI PREVIEW BUTTON
   ========================================== */
.card-body {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    gap: 10px !important;
    padding: 15px 0 5px 0 !important;
    width: 100% !important;
}

.btn-ai-card-preview {
    background: linear-gradient(135deg, #d4af37, #996515) !important;
    color: #0f172a !important;
    border: none !important;
    border-radius: 20px !important;
    padding: 7px 14px !important;
    font-size: 0.75rem !important;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(212, 175, 55, 0.4);
    transition: all 0.2s ease;
    white-space: nowrap;
    flex-shrink: 0;
}

.btn-ai-card-preview i {
    color: #0f172a !important;
}

.btn-ai-card-preview:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(212, 175, 55, 0.6);
}

/* ==========================================
   7. AI MODAL & PREVIEW BOXES
   ========================================== */
.ai-modal {
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(5px);
}

.ai-modal-content {
    background-color: #0f172a;
    border: 1px solid rgba(212, 175, 55, 0.3);
    color: #ffffff;
    margin: 4% auto;
    padding: 25px;
    width: 90%;
    max-width: 520px;
    border-radius: 12px;
    position: relative;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7);
    max-height: 85vh;
    overflow-y: auto;
}

#aiVisualizerModal #submitBtn {
    background: linear-gradient(135deg, #d4af37, #aa7c11) !important;
    color: #000 !important;
    border: none !important;
    border-radius: 6px !important;
}

#aiVisualizerModal #submitBtn i {
    color: #000 !important;
}

#aiVisualizerModal #proceedToBookBtn {
    background: #22c55e !important;
    color: #fff !important;
    border: none !important;
    border-radius: 6px !important;
}

.ai-preview-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-top: 10px;
}

.preview-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: #1e293b;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid rgba(212, 175, 55, 0.3);
}

.preview-label {
    font-size: 0.8rem;
    font-weight: bold;
    color: #d4af37;
    margin-bottom: 8px;
    text-transform: uppercase;
}

.preview-box img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #334155;
}

.close-ai-modal {
    position: absolute;
    right: 18px;
    top: 12px;
    font-size: 26px;
    cursor: pointer;
    color: #94a3b8;
}

.close-ai-modal:hover {
    color: #fff;
}

.ai-selected-style-label {
    background: rgba(212, 175, 55, 0.1);
    border: 1px solid rgba(212, 175, 55, 0.3);
    color: #d4af37;
    padding: 10px 14px;
    border-radius: 8px;
    font-weight: 600;
    margin-bottom: 15px;
    font-size: 0.95rem;
}

/* ==========================================
   8. RESPONSIVE BREAKPOINTS
   ========================================== */
@media (max-width: 992px) {
    .services-grid {
        grid-template-columns: repeat(2, 1fr) !important;
    }
}

@media (max-width: 600px) {
    .services-grid {
        grid-template-columns: 1fr !important;
    }
    #category-tabs,
    .category-buttons,
    .services-category-tabs {
        justify-content: flex-start !important;
        overflow-x: auto !important;
        flex-wrap: nowrap !important;
        padding-bottom: 10px !important;
    }
}

@media (max-width: 480px) {
    .ai-preview-grid {
        grid-template-columns: 1fr;
    }
}

/* ==========================================
   9. PREMIUM DESIGN UPGRADE
   ========================================== */

/* --- Section background: soft luxury gradient --- */
.services-list-section {
    background: linear-gradient(180deg, #f4f6f8 0%, #e9edf2 100%) !important;
    padding: 55px 0 70px 0 !important;
}

.services-grid {
    gap: 34px !important;
}

/* --- Card shell --- */
.luxury-service-card {
    position: relative !important;
    overflow: hidden !important;
    padding: 18px 18px 22px 18px !important;
    border: 1px solid rgba(212, 175, 55, 0.28) !important;
    border-radius: 20px !important;
    background: linear-gradient(180deg, #ffffff 0%, #fbfaf6 100%) !important;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08) !important;
    cursor: pointer;
    animation: cardFadeUp 0.55s ease both;
    transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease !important;
}

/* gold accent bar on top of every card */
.luxury-service-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #aa7c11, #d4af37, #f3d675, #d4af37, #aa7c11);
    z-index: 3;
}

/* "Book" hint line at the bottom of the card */
.luxury-service-card::after {
    content: "BOOK THIS SERVICE  \2192";
    display: block;
    margin-top: 14px;
    padding-top: 14px;
    border-top: 1px dashed rgba(212, 175, 55, 0.55);
    text-align: center;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 2.5px;
    color: #aa7c11;
    transition: color 0.3s ease, letter-spacing 0.3s ease;
}

.luxury-service-card:hover {
    transform: translateY(-9px) !important;
    border-color: #d4af37 !important;
    box-shadow: 0 22px 45px rgba(212, 175, 55, 0.28), 0 6px 14px rgba(15, 23, 42, 0.08) !important;
}

.luxury-service-card:hover::after {
    color: #0f172a;
    letter-spacing: 3.5px;
}

/* selected state */
.luxury-service-card.selected {
    border-color: #d4af37 !important;
    box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.35), 0 22px 45px rgba(212, 175, 55, 0.3) !important;
}

/* --- Image area --- */
.luxury-service-card .card-img-box {
    border-radius: 14px !important;
    background: linear-gradient(135deg, #1e293b, #0f172a) !important;
    border: none !important;
    padding: 0 !important;
    box-sizing: border-box !important;
}

.luxury-service-card .card-img-box img {
    width: 100% !important;
    height: 100% !important;
    object-fit: cover !important;
    object-position: center 20% !important;
    display: block !important;
    border-radius: 0 !important;
}

.luxury-service-card .card-icon-placeholder {
    border-radius: 0;
}

.luxury-service-card .card-img-box img {
    transition: transform 0.7s ease !important;
}

.luxury-service-card:hover .card-img-box img {
    transform: scale(1.07);
}

/* (dark overlay removed so the full image stays clear) */

/* price = gold pill on the image */
.luxury-service-card .luxury-price {
    position: absolute !important;
    right: 12px !important;
    bottom: 12px !important;
    z-index: 2 !important;
    background: linear-gradient(135deg, #d4af37, #aa7c11) !important;
    color: #0f172a !important;
    font-weight: 800 !important;
    font-size: 0.95rem !important;
    letter-spacing: 0.5px;
    padding: 7px 16px !important;
    border-radius: 30px !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35) !important;
}

/* "Selected" badge (visibility still controlled by your existing CSS) */
.luxury-service-card .badge-selected {
    position: absolute !important;
    top: 12px !important;
    left: 12px !important;
    z-index: 4 !important;
    background: #22c55e !important;
    color: #ffffff !important;
    font-size: 0.75rem !important;
    font-weight: 700 !important;
    padding: 5px 12px !important;
    border-radius: 20px !important;
}

/* placeholder when a service has no image */
.luxury-service-card .card-icon-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: radial-gradient(circle at 50% 40%, #1e293b, #0b1220);
}

.luxury-service-card .card-icon-placeholder i {
    font-size: 4.2rem;
    color: #d4af37;
    filter: drop-shadow(0 4px 12px rgba(212, 175, 55, 0.45));
}

/* --- Card text --- */
.luxury-service-card .card-body {
    padding: 20px 4px 0 4px !important;
}

.luxury-service-card .card-body h3 {
    flex: 1;
    margin: 0;
    font-family: 'Cinzel', serif;
    font-size: 1.15rem;
    font-weight: 700;
    letter-spacing: 0.8px;
    line-height: 1.4;
    color: #0f172a;
    text-align: left;
}

/* --- Smooth entrance when a category is opened (staggered) --- */
@keyframes cardFadeUp {
    from { opacity: 0; transform: translateY(26px); }
    to   { opacity: 1; transform: translateY(0); }
}

.luxury-service-card:nth-child(3n+2) { animation-delay: 0.08s; }
.luxury-service-card:nth-child(3n+3) { animation-delay: 0.16s; }

/* --- Category buttons: a little more presence --- */
#category-tabs .category-btn {
    padding: 12px 26px !important;
    font-size: 0.95rem !important;
}

/* --- Stylist cards: same premium touch --- */
.stylist-card {
    border: 1px solid rgba(212, 175, 55, 0.28) !important;
    border-radius: 20px !important;
}

.stylist-card.selected,
.stylist-card.active {
    border-color: #d4af37 !important;
    box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.35), 0 16px 34px rgba(212, 175, 55, 0.25) !important;
}

/* --- Mobile tuning for the taller cards --- */
@media (max-width: 600px) {
    .luxury-service-card {
        min-height: 580px !important;
    }
    .luxury-service-card .card-img-box {
        height: 380px !important;
    }
}

</style>
</head>
<body class="luxury-theme">

    <!-- INCLUDE HEADER COMPONENT -->
    <?php include_once __DIR__ . '/indexheader.php'; ?>

    <!-- SERVICES HERO -->
    <section class="services-hero" style="background: url('../uploads/images/salon/servicebg2.jpeg') no-repeat center 20%/cover; padding: 120px 20px; text-align: center;">
        <div class="luxury-container text-center" style="max-width: 1050px; margin: 0 auto; background: rgba(0, 0, 0, 0.65); padding: 55px 45px; border-radius: 20px; backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.2); box-shadow: 0 15px 35px rgba(0,0,0,0.6);">
            <span class="gold-subtitle" style="color: #FFD700 !important; font-weight: 700; letter-spacing: 3px; font-size: 1.15rem; display: inline-block;">OUR SERVICES</span>
            <h1 class="luxury-heading" style="color: #FFFFFF !important; font-size: 3.3rem; font-weight: 800; margin: 20px 0; line-height: 1.25;">EXCLUSIVE HAIR & BEAUTY <br><span class="gold-text" style="color: #FFD700 !important;">SERVICES</span></h1>
            <p class="about-desc" style="color: #FFFFFF !important; font-size: 1.25rem; font-weight: 400; margin: 20px auto 0 auto; line-height: 1.6; max-width: 750px; text-align: center;">We use only the finest products, carefully selected for their quality and performance.</p>
        </div>
    </section>

    <!-- AI VISUALIZER MODAL -->
    <div id="aiVisualizerModal" class="ai-modal" style="display: none;">
        <div class="ai-modal-content">
            <span class="close-ai-modal">&times;</span>
            <h2 style="font-family: 'Cinzel', serif; color: #d4af37; margin-bottom: 10px;">
                <i class="fas fa-magic"></i> AI Hair Visualizer
            </h2>
            <p style="font-size: 0.9rem; color: #ccc; margin-bottom: 15px;">
                Upload your selfie to preview this style before booking!
            </p>

            <div class="ai-selected-style-label" id="aiSelectedStyleLabel">
                Style: -
            </div>

            <form id="aiHairstyleForm">
                <div class="form-group" style="margin-bottom: 20px; text-align: left;">
                    <label style="display: block; margin-bottom: 5px; color: #ddd; font-weight: 500;">Upload Your Selfie:</label>
                    <input type="file" id="customerPhoto" name="user_photo" accept="image/*" required style="width: 100%; padding: 8px; background: #1e293b; color: #fff; border: 1px solid #334155; border-radius: 6px;">
                </div>

                <input type="hidden" id="stylePreference" value="">
                <input type="hidden" id="aiServiceId" value="">

                <button type="submit" id="submitBtn" style="width: 100%; background: linear-gradient(135deg, #d4af37, #aa7c11); color: #000; padding: 12px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
                    Generate Aftermath Preview
                </button>
            </form>

            <div id="errorMessage" style="display:none; margin-top: 15px; color: #ef4444; font-size: 0.9rem; background: rgba(239, 68, 68, 0.1); padding: 10px; border-radius: 6px; border: 1px solid #ef4444;"></div>

            <div id="loadingSpinner" style="display:none; margin-top: 20px; color: #d4af37; text-align: center;">
                <i class="fas fa-spinner fa-spin fa-2x"></i>
                <p style="margin-top: 10px; font-size: 0.9rem;">AI is processing your image... (takes 5-15s)</p>
            </div>

            <!-- BEFORE / AFTER SIDE-BY-SIDE RESULT CONTAINER -->
            <div id="resultContainer" style="display:none; margin-top: 20px; text-align: center;">
                <h3 style="color: #fff; font-size: 1.1rem; margin-bottom: 15px;">Your Hairstyle Preview:</h3>

                <div class="ai-preview-grid">
                    <!-- Box 1: Original Image -->
                    <div class="preview-box">
                        <span class="preview-label">Original (Before)</span>
                        <img id="inputPreviewImage" src="" alt="Original Selfie">
                    </div>

                    <!-- Box 2: AI Generated Image -->
                    <div class="preview-box">
                        <span class="preview-label">AI Preview (After)</span>
                        <img id="outputImage" src="" alt="AI Hairstyle Preview">
                    </div>
                </div>

                <div style="margin-top: 20px;">
                    <button type="button" id="proceedToBookBtn" style="background: #22c55e; color: #fff; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        Book This Style
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- STYLIST SELECTION SECTION -->
    <section class="stylist-section">
        <div class="luxury-container text-center">
            <span class="gold-subtitle">OUR TEAM</span>
            <h2 class="luxury-heading">MEET OUR <span class="gold-text">STYLISTS</span></h2>
            <p class="about-desc">Browse our expert team below, then click on any service you'd like to book.</p>

            <!-- Stylists Cards Grid -->
            <div class="stylist-grid" id="stylist-grid">
                <?php if (empty($staff_list)): ?>
                    <p style="color:#888;">No stylists available right now.</p>
                <?php else: ?>
                    <?php foreach ($staff_list as $index => $staff): ?>
                        <?php
                            $raw_img = !empty($staff['image']) ? trim($staff['image']) : '';

                            if (!empty($raw_img)) {
                                $clean_img = ltrim($raw_img, '/.');
                                if (strpos($clean_img, 'uploads/') !== false) {
                                    $staff_img_url = '../' . $clean_img;
                                } else {
                                    $staff_img_url = '../uploads/images/salon/' . $clean_img;
                                }
                            } else {
                                $default_name = 'stylist-' . (($index % 2) + 1) . '.jpg';
                                $staff_img_url = '../uploads/images/salon/' . $default_name;
                            }
                        ?>
                        <div class="stylist-card" data-staff-id="<?php echo $staff['id']; ?>" data-staff-name="<?php echo htmlspecialchars($staff['name']); ?>">
                            <div class="stylist-img-box">
                                <img src="<?php echo htmlspecialchars($staff_img_url); ?>"
                                     alt="<?php echo htmlspecialchars($staff['name']); ?>"
                                     onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($staff['name']); ?>&background=d4af37&color=000&size=250';">
                                <span class="badge-selected">Selected</span>
                            </div>
                            <div class="stylist-info" style="margin-top:10px;">
                                <h3><?php echo htmlspecialchars($staff['name']); ?></h3>
                                <p class="stylist-role"><?php echo htmlspecialchars($staff['role'] ?? 'Hair Stylist'); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- INTERACTIVE CATEGORY TABS BAR -->
    <section class="brand-bar">
        <div class="luxury-container text-center" style="padding-top: 20px;">
            <span class="gold-subtitle">PICK A SERVICE</span>
            <p class="about-desc">Tap any service below to choose your stylist, date and time. Tap the gold "Preview" button on a card to try it with AI first.</p>
        </div>
        <div class="luxury-container brand-grid" id="category-tabs">
            <button type="button" class="category-btn active" data-category="mens-haircuts">
                <i class="fas fa-scissors"></i> MEN'S HAIRCUTS
            </button>
            <button type="button" class="category-btn" data-category="beard-cuts">
                <i class="fas fa-user-ninja"></i> BEARD CUTS
            </button>
            <button type="button" class="category-btn" data-category="ladies-haircuts">
                <i class="fas fa-female"></i> LADIES HAIRCUTS
            </button>
            <button type="button" class="category-btn" data-category="coloring">
                <i class="fas fa-paint-brush"></i> COLORING
            </button>
            <button type="button" class="category-btn" data-category="facials">
                <i class="fas fa-spa"></i> FACIALS
            </button>
        </div>
    </section>

    <!-- SERVICES CARDS GRID -->
    <section class="services-list-section">
        <div class="luxury-container">
            <div class="services-grid" id="services-container">
                <?php foreach ($services_by_category as $category => $service_list): ?>
                    <?php if ($category === 'other') continue; ?>
                    <?php foreach ($service_list as $service): ?>
                        <div class="luxury-service-card<?php echo $category === 'mens-haircuts' ? '' : ' is-hidden'; ?>"
                             data-category="<?php echo htmlspecialchars($category); ?>"
                             data-service-id="<?php echo $service['id']; ?>"
                             data-service="<?php echo htmlspecialchars($service['service_name']); ?>"
                             data-price="<?php echo $service['price']; ?>">
                            <div class="card-img-box">
                                <?php if (!empty($service['image_url'])): ?>
                                    <img src="../<?php echo htmlspecialchars($service['image_url']); ?>" alt="<?php echo htmlspecialchars($service['service_name']); ?>">
                                <?php else: ?>
                                    <div class="card-icon-placeholder">
                                        <i class="fas <?php echo $category === 'coloring' ? 'fa-palette' : ($category === 'facials' ? 'fa-spa' : 'fa-cut'); ?>"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="luxury-price">Rs. <?php echo number_format($service['price'], 2); ?></span>
                                <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                            </div>
                            <div class="card-body">
                                <h3><?php echo htmlspecialchars($service['service_name']); ?></h3>
                                <?php if ($category !== 'facials'): ?>
                                    <button type="button" class="btn-ai-card-preview" data-ai-preview-btn>
                                        <i class="fas fa-magic"></i> Preview
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- BOOKING SELECTION MODAL -->
    <div class="modal-overlay" id="modal-overlay">
        <div class="booking-modal">
            <button class="modal-close" id="modal-close-btn">&times;</button>
            <h3 id="modal-service-name">-</h3>
            <p class="modal-price" id="modal-service-price">-</p>

            <div class="modal-field">
                <label>Choose Stylist</label>
                <select id="modal-stylist-select">
                    <option value="">-- Select Stylist --</option>
                    <?php foreach ($staff_list as $staff): ?>
                        <option value="<?php echo $staff['id']; ?>"><?php echo htmlspecialchars($staff['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="modal-field">
                <label>Date</label>
                <input type="date" id="modal-date" required min="<?php echo date('Y-m-d'); ?>">
            </div>

            <div id="busy-times-box" class="busy-times-box" style="display:none;">
                <p><i class="fas fa-info-circle"></i> Already booked on this date:</p>
                <div id="busy-times-list"></div>
            </div>

            <div class="modal-field">
                <label>Time (Business hours: 9:00 AM - 5:00 PM)</label>
                <div style="display:flex; gap:10px;">
                    <select id="modal-hour" required style="flex:1;">
                        <option value="">Hour</option>
                        <?php for ($h = 9; $h <= 16; $h++): ?>
                            <?php
                                $label = $h <= 12 ? $h . ' AM' : ($h - 12) . ' PM';
                                if ($h == 12) $label = '12 PM';
                            ?>
                            <option value="<?php echo str_pad($h, 2, '0', STR_PAD_LEFT); ?>"><?php echo $label; ?></option>
                        <?php endfor; ?>
                    </select>
                    <select id="modal-minute" required style="flex:1;">
                        <option value="">Minute</option>
                        <option value="00">:00</option>
                        <option value="15">:15</option>
                        <option value="30">:30</option>
                        <option value="45">:45</option>
                    </select>
                </div>
            </div>

            <button class="btn-confirm-booking" id="confirm-booking-btn">
                <i class="fas fa-calendar-check"></i> Confirm Booking
            </button>
        </div>
    </div>

    <div class="booking-alert" id="booking-alert"></div>

    <!-- FOOTER COMPONENT LINK -->
    <?php include_once __DIR__ . '/indexfooter.php'; ?>

    <!-- CLIENT INTERACTION SCRIPTS -->
    <script type="module">
import { InferenceClient } from "https://cdn.jsdelivr.net/npm/@huggingface/inference@4/+esm";

const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
const isCustomer = <?php echo $is_customer ? 'true' : 'false'; ?>;

let selectedService = { id: null, name: null, price: null };
let selectedStaffId = null;

const categoryTabs = document.getElementById('category-tabs');
const servicesContainer = document.getElementById('services-container');
const stylistGrid = document.getElementById('stylist-grid');

const modalOverlay = document.getElementById('modal-overlay');
const modalCloseBtn = document.getElementById('modal-close-btn');
const modalServiceName = document.getElementById('modal-service-name');
const modalServicePrice = document.getElementById('modal-service-price');
const modalStylistSelect = document.getElementById('modal-stylist-select');
const modalDate = document.getElementById('modal-date');
const modalHour = document.getElementById('modal-hour');
const modalMinute = document.getElementById('modal-minute');
const confirmBookingBtn = document.getElementById('confirm-booking-btn');
const bookingAlert = document.getElementById('booking-alert');

const aiModal = document.getElementById('aiVisualizerModal');
const closeAiBtn = document.querySelector('.close-ai-modal');
const aiFormHandler = document.getElementById('aiHairstyleForm');
const aiSelectedStyleLabel = document.getElementById('aiSelectedStyleLabel');
const proceedToBookBtn = document.getElementById('proceedToBookBtn');

/* ------------------------------------------
   CATEGORY FILTER
   (uses the .is-hidden class, not inline display)
   ------------------------------------------ */
function applyCategory(category) {
    if (!servicesContainer) return;

    let matches = 0;
    servicesContainer.querySelectorAll('.luxury-service-card').forEach(card => {
        const show = card.dataset.category === category;
        card.classList.toggle('is-hidden', !show);
        if (show) matches++;
    });

    // Debug helper: if nothing matched, the data-category keys from PHP
    // don't match the button's data-category values.
    if (matches === 0) {
        const available = [...new Set(
            [...servicesContainer.querySelectorAll('.luxury-service-card')].map(c => c.dataset.category)
        )];
        console.warn('No services found for category "' + category + '". Categories available in the page:', available);
    }
}

if (categoryTabs) {
    categoryTabs.addEventListener('click', function (e) {
        const btn = e.target.closest('.category-btn');
        if (!btn) return;

        categoryTabs.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        applyCategory(btn.dataset.category);
    });

    // Apply the active tab's filter once on page load
    const initialBtn = categoryTabs.querySelector('.category-btn.active');
    if (initialBtn) applyCategory(initialBtn.dataset.category);
}

/* ------------------------------------------
   STYLIST SELECTION
   ------------------------------------------ */
if (stylistGrid) {
    stylistGrid.addEventListener('click', function (e) {
        const card = e.target.closest('.stylist-card');
        if (!card) return;

        stylistGrid.querySelectorAll('.stylist-card').forEach(c => c.classList.remove('selected', 'active'));
        card.classList.add('selected', 'active');

        selectedStaffId = card.dataset.staffId;
        if (modalStylistSelect) {
            modalStylistSelect.value = selectedStaffId;
        }
    });
}

/* ------------------------------------------
   SERVICE CARD CLICK  (single handler)
   - Preview button  -> opens AI modal only
   - Anywhere else   -> opens booking modal
   ------------------------------------------ */
if (servicesContainer) {
    servicesContainer.addEventListener('click', function (e) {
        const card = e.target.closest('.luxury-service-card');
        if (!card) return;

        // 1) AI Preview button
        const previewBtn = e.target.closest('[data-ai-preview-btn]');
        if (previewBtn) {
            const serviceName = card.dataset.service;
            const serviceId = card.dataset.serviceId;

            document.getElementById('stylePreference').value = serviceName;
            document.getElementById('aiServiceId').value = serviceId;
            aiSelectedStyleLabel.textContent = 'Style: ' + serviceName;

            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('resultContainer').style.display = 'none';
            document.getElementById('customerPhoto').value = '';

            aiModal.style.display = 'block';
            return; // do NOT open the booking modal
        }

        // 2) Normal card click -> booking modal
        servicesContainer.querySelectorAll('.luxury-service-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');

        selectedService = {
            id: card.dataset.serviceId,
            name: card.dataset.service,
            price: card.dataset.price,
        };

        modalServiceName.textContent = selectedService.name;
        modalServicePrice.textContent = 'Rs. ' + parseFloat(selectedService.price).toFixed(2);

        if (selectedStaffId) {
            modalStylistSelect.value = selectedStaffId;
        }

        modalOverlay.style.display = 'flex';
    });
}

/* ------------------------------------------
   CHECK STYLIST AVAILABILITY
   ------------------------------------------ */
let currentBusySlots = [];

async function fetchBusyTimes() {
    const staffId = modalStylistSelect.value;
    const date = modalDate.value;
    const busyBox = document.getElementById('busy-times-box');
    const busyList = document.getElementById('busy-times-list');

    if (!staffId || !date) {
        busyBox.style.display = 'none';
        currentBusySlots = [];
        return;
    }

    try {
        const response = await fetch(`check-availability.php?staff_id=${staffId}&date=${date}`);
        const data = await response.json();
        currentBusySlots = data.busy_slots || [];

        if (currentBusySlots.length > 0) {
            busyList.innerHTML = currentBusySlots
                .map(slot => `<span>${slot.start_label} - ${slot.end_label}</span>`)
                .join(', ');
            busyBox.style.display = 'block';
        } else {
            busyBox.style.display = 'none';
        }
    } catch (error) {
        console.error('Failed to fetch availability:', error);
        busyBox.style.display = 'none';
        currentBusySlots = [];
    }
}

if (modalStylistSelect) {
    modalStylistSelect.addEventListener('change', fetchBusyTimes);
}
if (modalDate) {
    modalDate.addEventListener('change', fetchBusyTimes);
}

function isTimeSlotBusy(time) {
    return currentBusySlots.some(slot => time >= slot.start && time < slot.end);
}

/* ------------------------------------------
   BOOKING MODAL OPEN / CLOSE / CONFIRM
   ------------------------------------------ */
if (modalCloseBtn) {
    modalCloseBtn.addEventListener('click', () => { modalOverlay.style.display = 'none'; });
}
if (modalOverlay) {
    modalOverlay.addEventListener('click', function (e) {
        if (e.target === modalOverlay) modalOverlay.style.display = 'none';
    });
}

if (confirmBookingBtn) {
    confirmBookingBtn.addEventListener('click', function () {
        const staffId = modalStylistSelect.value;
        const date = modalDate.value;
        const hour = modalHour.value;
        const minute = modalMinute.value;

        if (!selectedService.id) {
            showBookingAlert('Please select a service first.');
            return;
        }
        if (!staffId) {
            showBookingAlert('Please choose a stylist.');
            return;
        }
        if (!date) {
            showBookingAlert('Please choose a date.');
            return;
        }
        if (!hour || !minute) {
            showBookingAlert('Please choose a time.');
            return;
        }

        if (!isLoggedIn) {
            showBookingAlert('Please login to book an appointment. Redirecting...');
            setTimeout(() => { window.location.href = 'login.php'; }, 1800);
            return;
        }
        if (!isCustomer) {
            showBookingAlert('Only customer accounts can book appointments.');
            return;
        }

        const time = hour + ':' + minute;

        if (isTimeSlotBusy(time)) {
            showBookingAlert('This time is already booked for the selected stylist. Please choose a different time.');
            return;
        }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../book-appointment.php';

        const fields = {
            staff_id: staffId,
            service_id: selectedService.id,
            appointment_date: date,
            appointment_time: time,
        };

        for (const key in fields) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = fields[key];
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
    });
}

function showBookingAlert(message, isError = true) {
    if (!bookingAlert) return;
    bookingAlert.textContent = message;
    bookingAlert.className = 'booking-alert' + (isError ? ' error' : '');
    bookingAlert.style.display = 'block';
    setTimeout(() => { bookingAlert.style.display = 'none'; }, 4000);
}

/* ------------------------------------------
   AI HAIRSTYLE VISUALIZER
   ------------------------------------------ */
if (closeAiBtn) {
    closeAiBtn.addEventListener('click', () => { aiModal.style.display = 'none'; });
}

if (aiFormHandler) {
    aiFormHandler.addEventListener('submit', async function (e) {
        e.preventDefault();

        const submitBtn = document.getElementById('submitBtn');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const resultContainer = document.getElementById('resultContainer');
        const outputImage = document.getElementById('outputImage');
        const inputPreviewImage = document.getElementById('inputPreviewImage');
        const errorMessage = document.getElementById('errorMessage');

        const styleInput = document.getElementById('stylePreference').value;
        const photoInput = document.getElementById('customerPhoto').files[0];

        if (!photoInput) {
            errorMessage.innerText = 'Please select a selfie photo first.';
            errorMessage.style.display = 'block';
            return;
        }

        // Display Original Photo
        inputPreviewImage.src = URL.createObjectURL(photoInput);

        errorMessage.style.display = 'none';
        resultContainer.style.display = 'none';
        loadingSpinner.style.display = 'block';
        submitBtn.disabled = true;

        // SECURITY: the old token was exposed in the page source. Revoke it on
        // Hugging Face and create a NEW one. Best practice is to move this call
        // to a PHP backend file so the token never reaches the browser.
        const apiToken = "PASTE_YOUR_NEW_HF_TOKEN_HERE";
        const client = new InferenceClient(apiToken);

        try {
            const promptText = `Give this exact same person a ${styleInput} hairstyle. Keep the face, identity, skin tone, expression and background unchanged. Only change the hair.`;

            const imageBlob = await client.imageToImage({
                provider: "fal-ai",
                model: "black-forest-labs/FLUX.2-dev",
                inputs: photoInput,
                parameters: {
                    prompt: promptText,
                },
            });

            const imageUrl = URL.createObjectURL(imageBlob);
            outputImage.src = imageUrl;
            resultContainer.style.display = 'block';

        } catch (error) {
            console.error('AI Fetch Error:', error);
            errorMessage.innerText = 'AI Generation failed: ' + error.message;
            errorMessage.style.display = 'block';
        } finally {
            loadingSpinner.style.display = 'none';
            submitBtn.disabled = false;
        }
    });
}

if (proceedToBookBtn) {
    proceedToBookBtn.addEventListener('click', () => {
        const serviceId = document.getElementById('aiServiceId').value;
        const card = servicesContainer.querySelector(`.luxury-service-card[data-service-id="${serviceId}"]`);

        if (card) {
            selectedService = {
                id: card.dataset.serviceId,
                name: card.dataset.service,
                price: card.dataset.price,
            };
            modalServiceName.textContent = selectedService.name;
            modalServicePrice.textContent = 'Rs. ' + parseFloat(selectedService.price).toFixed(2);

            if (selectedStaffId) {
                modalStylistSelect.value = selectedStaffId;
            }
        }

        aiModal.style.display = 'none';
        modalOverlay.style.display = 'flex';
    });
}
    </script>

</body>
</html>