<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../backend/config.php';
global $conn;

require_once __DIR__ . '/../backend/products-data.php';

$is_logged_in = isset($_SESSION['user_id']);$is_customer = $is_logged_in && ($_SESSION['user_role'] ?? '') === 'customer';

// Active page indicator for header highlight
$active_page = 'products';

// Cart count for badge display
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as$qty) {
        $cart_count +=$qty;
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
    
    <!-- Shared Site Styles -->
    <link rel="stylesheet" href="frontend-css/style.css">
    <link rel="stylesheet" href="frontend-css/services.css">
    <link rel="stylesheet" href="frontend-css/products.css">

    <style>
    /* Outer Section Setup with Extra Inner Padding */
    body.luxury-theme section.services-list-section {
        width: 100% !important;
        max-width: 100% !important;
        background: transparent !important;
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 40px 50px !important; 
        box-sizing: border-box !important;
    }

    /* Container Spacing */
    body.luxury-theme div.luxury-container {
        width: 100% !important;
        max-width: 100% !important;
        background: transparent !important;
        background-color: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        margin: 0 auto !important;
    }

    /* Grid Layout: 5 Products in 1 Line */
    body.luxury-theme div.services-grid {
        display: grid !important;
        grid-template-columns: repeat(5, 1fr) !important;
        gap: 20px !important;
        width: 100% !important;
    }

    /* Card Container with Inner Framing Padding */
    body.luxury-theme div.luxury-service-card {
        width: 100% !important;
        background: #ffffff !important;
        border: 1px solid #e0e0e0 !important;
        border-radius: 16px !important;
        padding: 8px !important;
        margin: 0 !important;
        overflow: hidden !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08) !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        box-sizing: border-box !important;
    }

    /* Image Box with Rounded Corners */
    body.luxury-theme div.luxury-service-card div.card-img-box {
        width: 100% !important;
        height: 220px !important;
        padding: 0 !important;
        margin: 0 !important;
        overflow: hidden !important;
        border-radius: 12px !important;
        background-color: #f5f5f5 !important;
        position: relative !important;
    }

    /* Image Fill */
    body.luxury-theme div.luxury-service-card div.card-img-box img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        display: block !important;
        padding: 0 !important;
        margin: 0 !important;
        border-radius: 12px !important;
    }

    /* Card Content Padding */
    body.luxury-theme div.luxury-service-card div.card-body {
        padding: 12px 6px 6px 6px !important;
    }

    /* Mobile & Tablet Responsive Layout */
    @media (max-width: 1200px) {
        body.luxury-theme div.services-grid {
            grid-template-columns: repeat(3, 1fr) !important;
        }
    }

    @media (max-width: 768px) {
        body.luxury-theme div.services-grid {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        body.luxury-theme section.services-list-section {
            padding: 20px 15px !important;
        }
    }
    </style>
</head>
<body class="luxury-theme">

    <!-- INCLUDE SHARED HEADER -->
    <?php include_once __DIR__ . '/indexheader.php'; ?>

    <!-- FLOATING SHOPPING CART BUTTON -->
    <button type="button" class="cart-btn-fixed" id="openCartBtn">
        <i class="fas fa-shopping-cart"></i> View Cart
        <span class="cart-badge" id="cartBadge" style="<?php echo $cart_count > 0 ? '' : 'display:none;'; ?>"><?php echo $cart_count; ?></span>
    </button>

    <!-- NEW PRODUCTS HERO BANNER SECTION -->
    <section class="products-hero-section">
        <div class="products-hero-overlay">
            <div class="products-hero-content">
                <span class="products-hero-subtitle">OUR PRODUCTS</span>
                <h1 class="products-hero-title">EXCLUSIVE HAIR & BEAUTY PRODUCTS</h1>
                <p class="products-hero-description">We use only the finest products, carefully selected for their quality and performance.</p>
            </div>
        </div>
    </section>

    <!-- PRODUCTS GRID -->
    <section class="services-list-section">
        <div class="luxury-container">
            <?php if (empty($active_products)): ?>
                <p style="text-align:center; color:#9a9aa5; padding:40px 0;">No products available right now. Check back soon!</p>
            <?php else: ?>
                <div class="services-grid" id="products-container">
                    <?php foreach ($active_products as $product):$p_id = $product['id'];$p_name = $product['item_name'] ?? $product['product_name'] ?? 'Product';
                        $p_price =$product['price'] ?? 0;
                       $p_qty = (int)($product['stock_quantity'] ?? 0);$p_category = $product['category'] ?? '';$p_img = !empty($product['image_url']) ? trim($product['image_url'], '/') : '';
                    ?>
                        <div class="luxury-service-card"
                             data-product-id="<?php echo $p_id; ?>"
                             data-product-name="<?php echo htmlspecialchars($p_name); ?>"
                             data-price="<?php echo $p_price; ?>">
                            <div class="card-img-box">
                                <?php if (!empty($p_img)): ?>
                                    <img src="../<?php echo htmlspecialchars($p_img); ?>" 
                                         alt="<?php echo htmlspecialchars($p_name); ?>"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="card-icon-placeholder" style="display:none;">
                                        <i class="fas fa-pump-soap"></i>
                                    </div>
                                <?php else: ?>
                                    <div class="card-icon-placeholder">
                                        <i class="fas fa-pump-soap"></i>
                                    </div>
                                <?php endif; ?>
                                <span class="luxury-price">Rs. <?php echo number_format($p_price, 2); ?></span>
                            </div>
                            <div class="card-body" style="flex-direction: column; align-items: stretch;">
                                <h3><?php echo htmlspecialchars($p_name); ?></h3>
                                <?php if (!empty($p_category)): ?>
                                    <p class="product-desc">Category: <?php echo htmlspecialchars($p_category); ?></p>
                                <?php endif; ?>
                                
                                <?php if ($p_qty > 0): ?>
                                    <button type="button" class="btn-confirm-booking" style="margin-top:10px;" data-add-to-cart>
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                <?php else: ?>
                                    <button type="button" class="btn-confirm-booking" style="margin-top:10px; opacity: 0.5; cursor: not-allowed;" disabled>
                                        <i class="fas fa-ban"></i> Out of Stock
                                    </button>
                                <?php endif; ?>
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

            <div class="payment-options">
                <button type="button" class="btn-cash" id="place-order-cash-btn">
                    <i class="fas fa-money-bill"></i> Cash on Pickup
                </button>
                <button type="button" class="btn-online" id="place-order-online-btn">
                    <i class="fas fa-credit-card"></i> Pay Online
                </button>
            </div>
        </div>
    </div>

    <div class="booking-alert" id="cart-alert"></div>

    <!-- INCLUDE SHARED FOOTER -->
    <?php include_once __DIR__ . '/indexfooter.php'; ?>

    <script src="https://www.payhere.lk/lib/payhere.js"></script>
    <script>
    const isLoggedIn = <?php echo $is_customer ? 'true' : 'false'; ?>;
    const cartOverlay = document.getElementById('cart-overlay');
    const openCartBtn = document.getElementById('openCartBtn');
    const cartCloseBtn = document.getElementById('cart-close-btn');
    const cartBadge = document.getElementById('cartBadge');
    const cartItemsList = document.getElementById('cart-items-list');
    const cartTotalAmount = document.getElementById('cart-total-amount');
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
                <img src="${item.image_url ? '../' + item.image_url.replace(/^\/+/, '') : 'https://via.placeholder.com/50'}" alt="${item.product_name}">
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

    // ---------- PLACE ORDER: CASH ON PICKUP ----------
    document.getElementById('place-order-cash-btn').addEventListener('click', async function () {
        const pickupDate = document.getElementById('pickup-date').value;
        if (!pickupDate) {
            showAlert('Please choose a pickup date.', true);
            return;
        }

        const response = await fetch('place-order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `pickup_date=${pickupDate}&payment_method=cash`,
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

    // ---------- PLACE ORDER: PAY ONLINE (PayHere) ----------
    document.getElementById('place-order-online-btn').addEventListener('click', async function () {
        const pickupDate = document.getElementById('pickup-date').value;
        if (!pickupDate) {
            showAlert('Please choose a pickup date.', true);
            return;
        }

        const response = await fetch('place-order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `pickup_date=${pickupDate}&payment_method=online`,
        });
        const data = await response.json();

        if (!data.success) {
            showAlert(data.message || 'Could not place order.', true);
            return;
        }

        const payResponse = await fetch(`payhere-init.php?order_id=${data.order_id}`);
        const payData = await payResponse.json();

        if (!payData.success) {
            showAlert(payData.message || 'Could not start payment.', true);
            return;
        }

        payhere.onCompleted = function (orderId) {
            fetch('payhere-confirm-testing.php?order_id=' + orderId)
                .then(() => {
                    cartOverlay.classList.remove('show');
                    cartBadge.textContent = '0';
                    cartBadge.style.display = 'none';
                    showAlert('Payment successful! Your order is confirmed.');
                });
        };

        payhere.onDismissed = function () {
            showAlert('Payment was cancelled.', true);
        };

        payhere.onError = function (error) {
            showAlert('Payment error: ' + error, true);
        };

        payhere.startPayment(payData.payment);
    });
    </script>

</body>
</html>