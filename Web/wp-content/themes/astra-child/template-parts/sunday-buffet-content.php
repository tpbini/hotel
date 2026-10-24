<?php
/**
 * The Cochin - Sunday Brunch Buffet Content Layout
 *
 * Built to match The Cochin inner pages structure (Banquets, Menu, About Us)
 * using strictly the content provided:
 * - Intro Headline & Timing Badge
 * - A Different Menu Every Sunday section
 * - Family-Friendly Prices (Adults, Children under 5, Children under 10)
 * - Book Your Sunday Table reservations & contact block
 * - Weekly changing menu & dietary requirement notice
 */

if (!defined('ABSPATH')) {
    exit;
}

$booking_url = function_exists('the_cochin_get_booking_url') ? the_cochin_get_booking_url() : home_url('/book-table/');
?>

<div class="cochin-buffet-layout">

    <!-- =========================================================================
         SECTION 1: INTRO & MAIN HEADLINE
         ========================================================================= -->
    <section class="buffet-intro-section">
        <div class="buffet-container">
            <div class="buffet-intro-card">
                <span class="buffet-intro-badge">Every Sunday | 12 Noon – 5 PM | £19.95 per person</span>
                <h2 class="buffet-main-headline">Sunday Brunch Buffet – A New Culinary Experience Every Week!</h2>
                <h3 class="buffet-sub-headline">Make Sundays special at The Cochin Restaurant in Hemel Hempstead Old Town!</h3>
                <p class="buffet-intro-text">Enjoy a delicious selection of authentic Kerala specialities and popular Indian favourites, thoughtfully prepared by our chefs.</p>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 2: A DIFFERENT MENU EVERY SUNDAY
         ========================================================================= -->
    <section class="buffet-variety-section">
        <div class="buffet-container">
            <div class="buffet-section-heading-wrap">
                <h3 class="buffet-section-title">A Different Menu Every Sunday!</h3>
            </div>

            <div class="buffet-variety-card">
                <p class="buffet-variety-p">We love bringing variety to your Sunday dining experience. Our chefs create a changing buffet menu each week, giving you something new and exciting to discover every Sunday.</p>

                <div class="buffet-variety-highlight">
                    <p>From flavourful starters and aromatic curries to freshly prepared Indian breads, fragrant rice dishes and traditional desserts, there's always something delicious waiting for you.</p>
                </div>

                <p class="buffet-variety-p">Whether you're joining us for a family Sunday lunch or catching up with friends, enjoy great food, warm hospitality and a relaxed dining experience.</p>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 3: FAMILY-FRIENDLY PRICES
         ========================================================================= -->
    <section class="buffet-pricing-section">
        <div class="buffet-container">
            <div class="buffet-section-heading-wrap">
                <h3 class="buffet-section-title">Family-Friendly Prices</h3>
            </div>

            <div class="buffet-pricing-grid">
                
                <!-- Tier 1: Adults -->
                <div class="buffet-price-card featured">
                    <div class="buffet-price-badge">ADULTS</div>
                    <h4 class="buffet-price-title">Adults</h4>
                    <div class="buffet-price-amount">
                        <span class="buffet-currency">£</span>
                        <span class="buffet-num">19.95</span>
                        <span class="buffet-per">per person</span>
                    </div>
                </div>

                <!-- Tier 2: Children under 5 -->
                <div class="buffet-price-card">
                    <div class="buffet-price-badge free">UNDER 5</div>
                    <h4 class="buffet-price-title">Children under 5</h4>
                    <div class="buffet-price-amount">
                        <span class="buffet-num free-text">Dine FREE</span>
                    </div>
                </div>

                <!-- Tier 3: Children under 10 -->
                <div class="buffet-price-card">
                    <div class="buffet-price-badge discounted">UNDER 10</div>
                    <h4 class="buffet-price-title">Children under 10</h4>
                    <div class="buffet-price-amount">
                        <span class="buffet-desc-text">Special discounted rates available</span>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 4: BOOK YOUR SUNDAY TABLE
         ========================================================================= -->
    <section class="buffet-booking-section">
        <div class="buffet-container">
            <div class="buffet-booking-card">
                <h3 class="buffet-booking-title">Book Your Sunday Table</h3>
                <p class="buffet-booking-lead">Reservations are recommended.</p>

                <div class="buffet-contact-details">
                    <h4 class="buffet-venue-title">The Cochin – Kerala & Indian Restaurant</h4>
                    <p class="buffet-contact-line">
                        <span class="buffet-icon">📍</span>
                        <span>61 High Street, Hemel Hempstead Old Town, HP1 3AF</span>
                    </p>
                    <p class="buffet-contact-line">
                        <span class="buffet-icon">📞</span>
                        <span>Telephone: <a href="tel:01442233777" class="buffet-link">01442 233777</a></span>
                    </p>
                    <p class="buffet-contact-line">
                        <span class="buffet-icon">✉️</span>
                        <span>Email: <a href="mailto:bookings@thecochin.co.uk" class="buffet-link">bookings@thecochin.co.uk</a></span>
                    </p>
                </div>

                <div class="buffet-booking-actions">
                    <a href="<?php echo esc_url($booking_url); ?>" class="buffet-book-button">Book Your Sunday Table</a>
                    <a href="tel:01442233777" class="buffet-call-button">Call 01442 233777</a>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 5: ALLERGIES & DIETARY INFORMATION
         ========================================================================= -->
    <section class="buffet-notice-section">
        <div class="buffet-container">
            <div class="buffet-notice-box">
                <p>Our buffet menu changes weekly and is subject to availability. Please inform our team of any allergies or dietary requirements before dining.</p>
            </div>
        </div>
    </section>

</div>
