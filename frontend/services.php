<?php
session_start();
require_once __DIR__ . '/../backend/config.php';
global $conn;
require_once __DIR__ . '/services-data.php';

$is_logged_in = isset($_SESSION['user_id']);
$is_customer = $is_logged_in && $_SESSION['user_role'] === 'customer';
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
    <link rel="stylesheet" href="frontend-css/services.css">
    <link rel="stylesheet" href="frontend-css/style.css">
</head>
<body class="luxury-theme">

    <!-- MAIN NAVIGATION BAR -->
    <header class="site-header">
        <a href="../index.php" class="logo-container">
            <img src="../logo/logo.png" alt="Salon You Logo" class="site-logo">
            <span class="logo-text">SALON YOU</span>
        </a>

        <nav class="main-nav">
            <a href="../index.php" class="nav-link">Home</a>
            <a href="services.php" class="nav-link active">Services</a>
            <a href="../index.php#gallery" class="nav-link">Gallery</a>
            <a href="about.php" class="nav-link">About Us</a>
            <a href="../index.php#contact" class="nav-link">Contact</a>
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

    <!-- STYLIST SELECTION SECTION -->
    <section class="stylist-section">
        <div class="luxury-container text-center">
            <span class="gold-subtitle">OUR TEAM</span>
            <h2 class="luxury-heading">MEET OUR <span class="gold-text">STYLISTS</span></h2>
            <p class="about-desc">Browse our expert team below, then click on any service you'd like to book.</p>

            <!-- Stylists Cards Grid -->
            <div class="stylist-grid">
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

    <!-- INTERACTIVE CATEGORY TABS BAR -->
    <section class="brand-bar">
        <div class="luxury-container text-center" style="padding-top: 20px;">
            <span class="gold-subtitle">PICK A SERVICE</span>
            <p class="about-desc">Tap any service below to choose your stylist, date and time.</p>
        </div>
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
                <?php foreach ($services_by_category as $category => $service_list): ?>
                    <?php if ($category === 'other') continue; // legacy generic services not shown in tabs ?>
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
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- BOOKING MODAL (pops up when a service card is clicked) -->
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

            <div class="modal-field">
                <label>Time</label>
                <input type="time" id="modal-time" required>
            </div>

            <button class="btn-confirm-booking" id="confirm-booking-btn">
                <i class="fas fa-calendar-check"></i> Confirm Booking
            </button>
        </div>
    </div>

    <div class="booking-alert" id="booking-alert"></div>

    <script>
    const isLoggedIn = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
    const isCustomer = <?php echo $is_customer ? 'true' : 'false'; ?>;

    let selectedServiceId = null;
    let preselectedStaffId = ''; // set when a stylist card up top is clicked

    const modalOverlay = document.getElementById('modal-overlay');
    const modalServiceName = document.getElementById('modal-service-name');
    const modalServicePrice = document.getElementById('modal-service-price');
    const modalStylistSelect = document.getElementById('modal-stylist-select');
    const modalDate = document.getElementById('modal-date');
    const modalTime = document.getElementById('modal-time');

    // ---- Stylist selection (top browsing cards) ----
    document.querySelectorAll('.stylist-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.stylist-card').forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            preselectedStaffId = this.getAttribute('data-staff-id');
        });
    });

    // ---- Category tab filtering ----
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const category = this.getAttribute('data-category');
            document.querySelectorAll('.luxury-service-card').forEach(card => {
                card.style.display = (card.getAttribute('data-category') === category) ? '' : 'none';
            });
        });
    });

    // ---- Clicking a service card opens the booking popup ----
    document.querySelectorAll('.luxury-service-card').forEach(card => {
        card.addEventListener('click', function() {
            selectedServiceId = this.getAttribute('data-service-id');
            const serviceName = this.getAttribute('data-service');
            const price = this.getAttribute('data-price');

            modalServiceName.textContent = serviceName;
            modalServicePrice.textContent = 'Rs. ' + price;
            modalStylistSelect.value = preselectedStaffId; // pre-fill from the top selection, if any
            modalDate.value = '';
            modalTime.value = '';

            modalOverlay.classList.add('show');
        });
    });

    // ---- Close the popup ----
    document.getElementById('modal-close-btn').addEventListener('click', closeModal);
    modalOverlay.addEventListener('click', function(e) {
        if (e.target === modalOverlay) closeModal(); // click outside the box closes it
    });
    function closeModal() {
        modalOverlay.classList.remove('show');
    }

    function showAlert(message, isError = false) {
        const alertBox = document.getElementById('booking-alert');
        alertBox.textContent = message;
        alertBox.className = 'booking-alert' + (isError ? ' error' : '');
        alertBox.style.display = 'block';
        setTimeout(() => { alertBox.style.display = 'none'; }, 4000);
    }

    // ---- Confirm booking ----
    document.getElementById('confirm-booking-btn').addEventListener('click', function() {
        const staffId = modalStylistSelect.value;
        const date = modalDate.value;
        const time = modalTime.value;

        if (!staffId) {
            showAlert('Please choose a stylist.', true);
            return;
        }
        if (!date || !time) {
            showAlert('Please select a date and time.', true);
            return;
        }

        if (!isLoggedIn) {
            showAlert('Please login to book an appointment. Redirecting...', true);
            setTimeout(() => { window.location.href = 'login.php'; }, 1800);
            return;
        }

        if (!isCustomer) {
            showAlert('Only customer accounts can book appointments.', true);
            return;
        }

        // Submit the booking via a normal form POST to book-appointment.php
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../book-appointment.php';

        const fields = {
            staff_id: staffId,
            service_id: selectedServiceId,
            appointment_date: date,
            appointment_time: time
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
    </script>

</body>
</html>