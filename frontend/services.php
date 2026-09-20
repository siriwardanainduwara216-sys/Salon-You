<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../backend/config.php';
global $conn;

require_once __DIR__ . '/../backend/services-data.php';

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
    
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="frontend-css/services.css">
    <link rel="stylesheet" href="frontend-css/style.css">
    <link rel="stylesheet" href="frontend-css/services-modal.css">

    <style>
    /* AI PREVIEW BUTTON - sits next to the haircut name, not on the image */
    .card-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .btn-ai-card-preview {
        background: linear-gradient(135deg, #d4af37, #996515);
        color: #0f172a;
        border: none;
        border-radius: 20px;
        padding: 6px 12px;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(212, 175, 55, 0.4);
        transition: all 0.2s ease;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .btn-ai-card-preview:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(212, 175, 55, 0.6);
    }

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
        max-width: 460px;
        border-radius: 12px;
        position: relative;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.7);
        max-height: 85vh;
        overflow-y: auto;
    }

    #outputImage {
        max-width: 100%; 
        max-height: 320px;
        object-fit: contain;
        border-radius: 10px; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.5); 
        border: 1px solid #d4af37;
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
    </style>
</head>
<body class="luxury-theme">

    <!-- NAVIGATION HEADER -->
    <header class="site-header">
        <a href="index.php" class="logo-container">
            <img src="../logo/logo.png" alt="Salon You Logo" class="site-logo">
            <span class="logo-text">SALON YOU</span>
        </a>

        <nav class="main-nav">
            <a href="index.php" class="nav-link">Home</a>
            <a href="services.php" class="nav-link active">Services</a>
             <a href="products.php" class="nav-link active">products</a>
            <a href="index.php#gallery" class="nav-link">Gallery</a>
            <a href="about.php" class="nav-link">About Us</a>
            <a href="index.php#contact" class="nav-link">Contact</a>
        </nav>

        <div class="header-actions">
            <?php if ($is_logged_in): ?>
                <a href="../backend/logout.php" class="btn-register">Logout</a>
            <?php else: ?>
                <a href="../register.php" class="btn-register">Register</a>
            <?php endif; ?>
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

            <div id="resultContainer" style="display:none; margin-top: 20px; text-align: center;">
                <h3 style="color: #fff; font-size: 1.1rem; margin-bottom: 10px;">Your Aftermath Preview:</h3>
                <img id="outputImage" src="" alt="AI Hairstyle Preview">
                <div style="margin-top: 15px;">
                    <button type="button" id="proceedToBookBtn" style="background: #22c55e; color: #fff; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        Book This Style
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- STYLIST SELECTION -->
    <section class="stylist-section">
        <div class="luxury-container text-center">
            <span class="gold-subtitle">OUR TEAM</span>
            <h2 class="luxury-heading">MEET OUR <span class="gold-text">STYLISTS</span></h2>
            <p class="about-desc">Browse our expert team below, then click on any service you'd like to book.</p>

            <div class="stylist-grid" id="stylist-grid">
                <?php if (empty($staff_list)): ?>
                    <p style="color:#888;">No stylists available right now.</p>
                <?php else: ?>
                    <?php foreach ($staff_list as $index => $staff): ?>
                        <div class="stylist-card" data-staff-id="<?php echo $staff['id']; ?>" data-staff-name="<?php echo htmlspecialchars($staff['name']); ?>">
                            <div class="stylist-img-box">
                                <img src="../uploads/images/salon/stylist-<?php echo ($index % 2) + 1; ?>.jpg" alt="<?php echo htmlspecialchars($staff['name']); ?>">
                                <span class="badge-selected"><i class="fas fa-check-circle"></i> Selected</span>
                            </div>
                            <div class="stylist-info">
                                <h3><?php echo htmlspecialchars($staff['name']); ?></h3>
                                <p class="stylist-role">Hair Stylist</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- CATEGORY SELECTION TABS -->
    <section class="brand-bar">
        <div class="luxury-container text-center" style="padding-top: 20px;">
            <span class="gold-subtitle">PICK A SERVICE</span>
            <p class="about-desc">Tap any service below to choose your stylist, date and time. Tap the gold "Preview" button on a card to try it with AI first.</p>
        </div>
        <div class="luxury-container brand-grid" id="category-tabs">
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
                <?php foreach ($services_by_category as $category => $service_list): ?>
                    <?php if ($category === 'other') continue; ?>
                    <?php foreach ($service_list as $service): ?>
                        <div class="luxury-service-card"
                             data-category="<?php echo htmlspecialchars($category); ?>"
                             data-service-id="<?php echo $service['id']; ?>"
                             data-service="<?php echo htmlspecialchars($service['service_name']); ?>"
                             data-price="<?php echo $service['price']; ?>"
                             style="<?php echo $category === 'mens-haircuts' ? '' : 'display:none;'; ?>">
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
                            <?php $label = $h <= 12 ? $h . ' AM' : ($h - 12) . ' PM'; if ($h == 12) $label = '12 PM'; ?>
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

    <!-- CLIENT INTERACTION SCRIPTS -->
    <script type="module">
import { InferenceClient } from "https://cdn.jsdelivr.net/npm/@huggingface/inference@4/+esm";

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

if (categoryTabs) {
    categoryTabs.addEventListener('click', function (e) {
        const btn = e.target.closest('.category-btn');
        if (!btn) return;

        categoryTabs.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const category = btn.dataset.category;
        servicesContainer.querySelectorAll('.luxury-service-card').forEach(card => {
            card.style.display = (card.dataset.category === category) ? '' : 'none';
        });
    });
}

if (stylistGrid) {
    stylistGrid.addEventListener('click', function (e) {
        const card = e.target.closest('.stylist-card');
        if (!card) return;

        stylistGrid.querySelectorAll('.stylist-card').forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');

        selectedStaffId = card.dataset.staffId;
        if (modalStylistSelect) {
            modalStylistSelect.value = selectedStaffId;
        }
    });
}

if (servicesContainer) {
    servicesContainer.addEventListener('click', function (e) {
        const card = e.target.closest('.luxury-service-card');
        if (!card) return;

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

if (servicesContainer) {
    servicesContainer.addEventListener('click', function (e) {
        const previewBtn = e.target.closest('[data-ai-preview-btn]');
        if (!previewBtn) return;

        e.stopPropagation();

        const card = previewBtn.closest('.luxury-service-card');
        const serviceName = card.dataset.service;
        const serviceId = card.dataset.serviceId;

        document.getElementById('stylePreference').value = serviceName;
        document.getElementById('aiServiceId').value = serviceId;
        aiSelectedStyleLabel.textContent = 'Style: ' + serviceName;

        document.getElementById('errorMessage').style.display = 'none';
        document.getElementById('resultContainer').style.display = 'none';
        document.getElementById('customerPhoto').value = '';

        aiModal.style.display = 'block';
    });

}

// ---------- CHECK STYLIST AVAILABILITY FOR SELECTED DATE ----------
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

// Returns true if the given HH:MM time falls inside any busy slot
function isTimeSlotBusy(time) {
    return currentBusySlots.some(slot => time >= slot.start && time < slot.end);
}

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

        const time = hour + ':' + minute;

        // Busy Time Validation Check
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

function showBookingAlert(message) {
    if (!bookingAlert) return;
    bookingAlert.textContent = message;
    bookingAlert.style.display = 'block';
    setTimeout(() => { bookingAlert.style.display = 'none'; }, 4000);
}

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
        const errorMessage = document.getElementById('errorMessage');

        const styleInput = document.getElementById('stylePreference').value;
        const photoInput = document.getElementById('customerPhoto').files[0];

        if (!photoInput) {
            errorMessage.innerText = 'Please select a selfie photo first.';
            errorMessage.style.display = 'block';
            return;
        }

        errorMessage.style.display = 'none';
        resultContainer.style.display = 'none';
        loadingSpinner.style.display = 'block';
        submitBtn.disabled = true;

        const apiToken = "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";
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