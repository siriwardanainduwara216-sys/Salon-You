<!-- ==========================================
     MODERN DARK FOOTER (HTML + INLINE CSS)
     ========================================== -->
<style>
/* FOOTER BASE STYLING */
.main-footer {
    width: 100%;
    background-color: #000000;
    color: #ffffff;
    padding: 70px 8% 30px 8%;
    box-sizing: border-box;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.footer-container {
    max-width: 1250px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1.2fr 1fr 1.5fr 1fr;
    gap: 40px;
    align-items: start;
}

/* Brand Logo - Gold Accent Colour */
.footer-logo h2 {
    font-size: 2.2rem;
    font-weight: 800;
    letter-spacing: 2px;
    color: #e68f05 !important;
    margin-top: 0;
    margin-bottom: 25px;
}

.social-icons {
    display: flex;
    gap: 15px;
}

.social-btn {
    width: 38px;
    height: 38px;
    background-color: #111111;
    border: 1px solid #333333;
    border-radius: 8px;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.3s ease;
}

.social-btn:hover {
    background-color: #e68f05;
    border-color: #e68f05;
    color: #000000;
}

/* Section Headings - Gold Accent Colour */
.footer-heading {
    color: #e68f05 !important;
    font-size: 1.05rem;
    font-weight: 700;
    margin-top: 0;
    margin-bottom: 25px;
    letter-spacing: 1px;
}

/* Navigation Links */
.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-links li {
    margin-bottom: 14px;
}

.footer-links a {
    color: #ffffff;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    letter-spacing: 0.5px;
    transition: color 0.3s ease;
}

.footer-links a:hover {
    color: #e68f05;
}

/* Contact Info Items */
.contact-item {
    display: flex;
    gap: 15px;
    margin-bottom: 22px;
    align-items: flex-start;
}

.contact-icon {
    color: #e68f05;
    font-size: 1.2rem;
    margin-top: 3px;
}

.contact-text strong {
    display: block;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    color: #ffffff;
    margin-bottom: 3px;
}

.contact-text p {
    margin: 0;
    color: #d4d4d4;
    font-size: 0.85rem;
    line-height: 1.4;
}

/* Phone Block */
.phone-block {
    display: flex;
    align-items: center;
    gap: 15px;
}

.phone-icon {
    color: #e68f05;
    font-size: 1.8rem;
}

.phone-text span {
    display: block;
    font-size: 0.8rem;
    color: #d4d4d4;
    letter-spacing: 1px;
}

.phone-text h4 {
    margin: 3px 0 0 0;
    font-size: 1.1rem;
    font-weight: 800;
    color: #ffffff;
}

/* Bottom Bar */
.footer-bottom {
    max-width: 1250px;
    margin: 40px auto 0 auto;
    padding-top: 20px;
    border-top: 1px solid #111111;
    text-align: right;
}

.footer-bottom p {
    margin: 0;
    color: #d4d4d4;
    font-size: 0.85rem;
}

/* Responsive Styles */
@media (max-width: 992px) {
    .footer-container {
        grid-template-columns: 1fr 1fr;
        gap: 30px;
    }
}

@media (max-width: 600px) {
    .footer-container {
        grid-template-columns: 1fr;
    }
    .footer-bottom {
        text-align: center;
    }
}
</style>

<footer class="main-footer">
    <div class="footer-container">
        
        <!-- BRAND LOGO & SOCIALS -->
        <div class="footer-logo">
            <h2>SALON YOU</h2>
            <div class="social-icons">
                <a href="https://www.facebook.com/share/19YocfRUR9/?mibextid=wwXIfr" class="social-btn" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
                <a href="https://www.tiktok.com/@salon.you8?_r=1&_t=ZS-99wq11v8ZbL" class="social-btn" target="_blank"><i class="fab fa-tiktok"></i></a>
            </div>
        </div>

        <!-- QUICK LINKS (SERVICES LINKED HERE) -->
        <div>
            <h3 class="footer-heading">QUICK LINKS</h3>
            <ul class="footer-links">
                <li><a href="index.php">HOME</a></li>
                <li><a href="services.php">SERVICES</a></li>
                <li><a href="about.php">ABOUT</a></li>
                <li><a href="gallery.php">GALLERY</a></li>
                <li><a href="contact.php">CONTACT</a></li>
            </ul>
        </div>

        <!-- CONTACT US DETAILS -->
        <div>
            <h3 class="footer-heading">CONTACT US</h3>
            
            <div class="contact-item">
                <i class="far fa-clock contact-icon"></i>
                <div class="contact-text">
                    <strong>OPENING TIMES</strong>
                    <p>Everyday: 9:00am - 10:00pm</p>
                </div>
            </div>

            <div class="contact-item">
                <i class="fas fa-home contact-icon"></i>
                <div class="contact-text">
                    <strong>OUR LOCATION</strong>
                    <p>Maththegama/Ku/Bopitiya.</p>
                </div>
            </div>
        </div>

        <!-- PHONE NUMBER -->
        <div>
            <div class="phone-block">
                <i class="fas fa-phone-alt phone-icon"></i>
                <div class="phone-text">
                    <span>OUR PHONE</span>
                    <h4>+94 77 903 4502</h4>
                    <h4>+94 74 148 5124</h4>
                </div>
            </div>
        </div>

    </div>

    <!-- FOOTER BOTTOM COPYRIGHT -->
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> All Rights Reserved @ Salon You | Designed & Developed by Ants</p>
    </div>
</footer>