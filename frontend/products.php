<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../backend/config.php';
global $conn;

require_once __DIR__ . '/../backend/products-data.php';

$is_logged_in = isset($_SESSION['user_id']);
$is_customer = $is_logged_in && ($_SESSION['user_role'] ?? '') === 'customer';

// Cart count for the header badge
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $qty) {
        $cart_count += $qty;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Products - Salon You</title>

    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="frontend-css/services.css">
    <link rel="stylesheet" href="frontend-css/style.css">

    <style>
    .cart-btn {
        position: relative;
        background: #d4af37;
        color: #1a1a1a;
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        font-size: 14px;
    }
    .cart-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #ef4444;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .product-desc {
        color: #9a9aa5;
        font-size: 12px;
        margin-top: 6px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .cart-item-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid #2c2d35;
    }
    .cart-item-row img {
        width: 50px; height: 50px; object-fit: cover; border-radius: 8px;
    }
    .cart-item-info { flex: 1; }
    .cart-item-info h4 { font-size: 14px; margin-bottom: 2px; }
    .cart-item-info span { font-size: 12px; color: #9a9aa5; }
    .qty-controls { display: flex; align-items: center; gap: 8px; }
    .qty-controls button {
        background: #2c2d35; color: #fff; border: none; width: 26px; height: 26px;
        border-radius: 6px; cursor: pointer; font-size: 14px;
    }
    .cart-total-row {
        display: flex; justify-content: space-between; font-size: 16px;
        font-weight: 700; color: #d4af37; margin-top: 16px; padding-top: 16px;
        border-top: 1px solid #2c2d35;
    }
    .empty-cart-msg { color: #9a9aa5; font-size: 14px; text-align: center; padding: 20px 0; }
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
            <a href="services.php" class="nav-link">Services</a>
            <a href="products.php" class="nav-link active">Shop</a>
            <a href="index.php#gallery" class="nav-link">Gallery</a>
            <a href="about.php" class="nav-link">About Us</a>
        </nav>

        <div class="header-actions" style="display:flex; align-items:center; gap:12px;">
            <button type="button" class="cart-btn" id="openCartBtn">
                <i class="fas fa-shopping-cart"></i> Cart
                <span class="cart-badge" id="cartBadge" style="<?php echo $cart_count > 0 ? '' : 'display:none;'; ?>"><?php echo $cart_count; ?></span>
            </button>
            <?php if ($is_logged_in): ?>
                <a href="../backend/logout.php" class="btn-register">Logout</a>
            <?php else: ?>
                <a href="../register.php" class="btn-register">Register</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- PRODUCTS HERO -->
    <section class="services-hero">
        <div class="luxury-container text-center">
            <span class="gold-subtitle">SHOP OUR PRODUCTS</span>
            <h1 class="luxury-heading">SALON <span class="gold-text">ESSENTIALS</span></h1>
            <p class="about-desc">Take the salon experience home with our curated beauty products.</p>
        </div>
    </section>

    <!-- PRODUCTS GRID -->
    <section class="services-list-section">
        <div class="luxury-container">
            <?php if (empty($active_products)): ?>
                <p style="text-align:center; color:#9a9aa5; padding:40px 0;">No products available right now. Check back soon!</p>
            <?php else: ?>
                <div class="services-grid" id="products-container">
                    <?php foreach ($active_products as $product): ?>
                        <div class="luxury-service-card"
                             data-product-id="<?php echo $product['id']; ?>"
                             data-product-name="<?php echo htmlspecialchars($product['product_name']); ?>"
                             data-price="<?php echo $product['price']; ?>">
                            <div class="card-img-box">
                                <?php if (!empty($product['image_url'])): ?>
                                    <img src="../<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                                <?php else: ?>
                                    <div class="card-icon-placeholder">
                                        <i class="fas fa-pump-soap"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="luxury-price">Rs. <?php echo number_format($product['price'], 2); ?></span>
                            </div>
                            <div class="card-body" style="flex-direction: column; align-items: stretch;">
                                <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                                <?php if (!empty($product['description'])): ?>
                                    <p class="product-desc"><?php echo htmlspecialchars($product['description']); ?></p>
                                <?php endif; ?>
                                <button type="button" class="btn-confirm-booking" style="margin-top:10px;" data-add-to-cart>
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- CART MODAL -->
    <div class="modal-overlay" id="cart-overlay">
        <div class="booking-modal" style="max-width: 480px;">
            <button class="modal-close" id="cart-close-btn">&times;</button>
            <h3><i class="fas fa-shopping-cart"></i> Your Cart</h3>
            <p class="modal-price">Review your items before checkout</p>

            <div id="cart-items-list">
                <p class="empty-cart-msg">Loading cart...</p>
            </div>

            <div class="cart-total-row">
                <span>Total</span>
                <span id="cart-total-amount">Rs. 0.00</span>
            </div>

            <div class="modal-field" style="margin-top:16px;">
                <label>Pickup Date</label>
                <input type="date" id="pickup-date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>

            <button class="btn-confirm-booking" id="place-order-btn">
                <i class="fas fa-check-circle"></i> Place Order (Pay Cash on Pickup)
            </button>
        </div>
    </div>

    <div class="booking-alert" id="cart-alert"></div>

    <script>
    const isLoggedIn = <?php echo $is_customer ? 'true' : 'false'; ?>;
    const cartOverlay = document.getElementById('cart-overlay');
    const openCartBtn = document.getElementById('openCartBtn');
    const cartCloseBtn = document.getElementById('cart-close-btn');
    const cartBadge = document.getElementById('cartBadge');
    const cartItemsList = document.getElementById('cart-items-list');
    const cartTotalAmount = document.getElementById('cart-total-amount');
    const placeOrderBtn = document.getElementById('place-order-btn');
    const cartAlert = document.getElementById('cart-alert');
    const productsContainer = document.getElementById('products-container');

    function showAlert(message, isError = false) {
        cartAlert.textContent = message;
        cartAlert.className = 'booking-alert' + (isError ? ' error' : '');
        cartAlert.style.display = 'block';
        setTimeout(() => { cartAlert.style.display = 'none'; }, 3500);
    }

    function requireLogin() {
        showAlert('Please login as a customer to shop.', true);
        setTimeout(() => { window.location.href = 'login.php'; }, 1200);
    }

    // ---------- ADD TO CART ----------
    if (productsContainer) {
        productsContainer.addEventListener('click', async function (e) {
            const btn = e.target.closest('[data-add-to-cart]');
            if (!btn) return;

            if (!isLoggedIn) { requireLogin(); return; }

            const card = btn.closest('.luxury-service-card');
            const productId = card.dataset.productId;

            const response = await fetch('cart-add.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&quantity=1`,
            });
            const data = await response.json();

            if (data.success) {
                cartBadge.textContent = data.cart_count;
                cartBadge.style.display = data.cart_count > 0 ? 'flex' : 'none';
                showAlert(card.dataset.productName + ' added to cart!');
            } else {
                showAlert(data.message || 'Could not add to cart.', true);
            }
        });
    }

    // ---------- OPEN / LOAD CART ----------
    async function loadCart() {
        const response = await fetch('cart-get.php');
        const data = await response.json();

        if (!data.items || data.items.length === 0) {
            cartItemsList.innerHTML = '<p class="empty-cart-msg">Your cart is empty.</p>';
            cartTotalAmount.textContent = 'Rs. 0.00';
            return;
        }

        cartItemsList.innerHTML = data.items.map(item => `
            <div class="cart-item-row" data-product-id="${item.id}">
                <img src="${item.image_url ? '../' + item.image_url : 'https://via.placeholder.com/50'}" alt="${item.product_name}">
                <div class="cart-item-info">
                    <h4>${item.product_name}</h4>
                    <span>Rs. ${parseFloat(item.price).toFixed(2)} each</span>
                </div>
                <div class="qty-controls">
                    <button type="button" data-decrease>-</button>
                    <span>${item.quantity}</span>
                    <button type="button" data-increase>+</button>
                </div>
            </div>
        `).join('');

        cartTotalAmount.textContent = 'Rs. ' + parseFloat(data.total).toFixed(2);
    }

    if (openCartBtn) {
        openCartBtn.addEventListener('click', () => {
            if (!isLoggedIn) { requireLogin(); return; }
            cartOverlay.classList.add('show');
            loadCart();
        });
    }
    if (cartCloseBtn) {
        cartCloseBtn.addEventListener('click', () => cartOverlay.classList.remove('show'));
    }
    if (cartOverlay) {
        cartOverlay.addEventListener('click', (e) => {
            if (e.target === cartOverlay) cartOverlay.classList.remove('show');
        });
    }

    // ---------- QUANTITY +/- INSIDE CART ----------
    if (cartItemsList) {
        cartItemsList.addEventListener('click', async function (e) {
            const row = e.target.closest('.cart-item-row');
            if (!row) return;
            const productId = row.dataset.productId;
            let action = null;
            if (e.target.closest('[data-increase]')) action = 'increase';
            if (e.target.closest('[data-decrease]')) action = 'decrease';
            if (!action) return;

            const response = await fetch('cart-add.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `product_id=${productId}&quantity=1&action=${action}`,
            });
            const data = await response.json();

            cartBadge.textContent = data.cart_count;
            cartBadge.style.display = data.cart_count > 0 ? 'flex' : 'none';
            loadCart();
        });
    }

    // ---------- PLACE ORDER ----------
    if (placeOrderBtn) {
        placeOrderBtn.addEventListener('click', async function () {
            const pickupDate = document.getElementById('pickup-date').value;
            if (!pickupDate) {
                showAlert('Please choose a pickup date.', true);
                return;
            }

            const response = await fetch('place-order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `pickup_date=${pickupDate}`,
            });
            const data = await response.json();

            if (data.success) {
                cartOverlay.classList.remove('show');
                cartBadge.textContent = '0';
                cartBadge.style.display = 'none';
                showAlert('Order placed! Come collect and pay cash at the salon.');
            } else {
                showAlert(data.message || 'Could not place order.', true);
            }
        });
    }
    </script>

</body>
</html>