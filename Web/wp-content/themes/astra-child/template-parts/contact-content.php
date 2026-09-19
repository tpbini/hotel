<?php
/**
 * The Cochin - Contact Page Content Layout
 *
 * Implements:
 * - Hero Header Section with warm luxury background
 * - 4-Card Contact & Operating Hours Highlight Grid
 * - Interactive 2-Column Section: AJAX Message Form & Embedded Google Map
 * - Directions, Parking & Accessibility Travel Guide
 * - Direct Online Reservation & Banqueting CTAs
 */

if (!defined('ABSPATH')) {
    exit;
}

$restaurant_name    = get_option('rb_restaurant_name', 'The Cochin Indian Restaurant');
$restaurant_phone   = get_option('rb_restaurant_phone', '01442 233777');
$restaurant_email   = 'thecochin@gmail.com';
$restaurant_address = '61 High Street, Hemel Hempstead, Hertfordshire HP1 3AF';
$booking_url        = the_cochin_get_booking_url();
$menu_url           = the_cochin_get_menu_page_url();
$banquet_url        = home_url('/banquets/');
$google_maps_link   = 'https://maps.google.com/?q=' . urlencode($restaurant_address);
$contact_nonce      = wp_create_nonce('the_cochin_contact_nonce');
$ajax_url           = admin_url('admin-ajax.php');
?>

<div class="cochin-contact-page-layout">

    <!-- =========================================================================
         SECTION 1: HERO & INTRO HEADER
         ========================================================================= -->
    <section class="cochin-contact-hero-section">
        <div class="cochin-contact-container">
            <div class="cochin-contact-hero-card">
                <span class="cochin-contact-badge"><?php esc_html_e('VISIT & GET IN TOUCH', 'astra-child'); ?></span>
                <h1 class="cochin-contact-headline"><?php esc_html_e('Contact The Cochin', 'astra-child'); ?></h1>
                <p class="cochin-contact-subtitle">
                    <?php esc_html_e('We have been proudly welcoming guests to Hemel Hempstead’s historic Old Town since 2003. Whether you are reserving a table, organising a special banquet celebration, or inquiring about our authentic Kerala menu, we look forward to hearing from you.', 'astra-child'); ?>
                </p>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 2: 4-CARD HIGHLIGHT GRID (ADDRESS, PHONE, EMAIL, HOURS)
         ========================================================================= -->
    <section class="cochin-contact-info-grid-section">
        <div class="cochin-contact-container">
            <div class="cochin-info-cards-grid">

                <!-- Card 1: Address & Location -->
                <div class="cochin-info-card">
                    <div class="cochin-info-icon-box" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                            <circle cx="12" cy="10" r="3"></circle>
                        </svg>
                    </div>
                    <h3 class="cochin-info-title"><?php esc_html_e('Our Location', 'astra-child'); ?></h3>
                    <p class="cochin-info-text">
                        <strong>The Cochin</strong><br>
                        61 High Street, Old Town<br>
                        Hemel Hempstead, Herts<br>
                        <strong>HP1 3AF</strong>
                    </p>
                    <a href="<?php echo esc_url($google_maps_link); ?>" target="_blank" rel="noopener noreferrer" class="cochin-info-action-link">
                        <?php esc_html_e('Get Directions →', 'astra-child'); ?>
                    </a>
                </div>

                <!-- Card 2: Telephone & Bookings -->
                <div class="cochin-info-card">
                    <div class="cochin-info-icon-box" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </div>
                    <h3 class="cochin-info-title"><?php esc_html_e('Telephone & Bookings', 'astra-child'); ?></h3>
                    <p class="cochin-info-text">
                        For immediate bookings, same-day tables, or urgent takeaway queries:
                    </p>
                    <div class="cochin-info-highlight-val">
                        <a href="tel:01442233777"><?php echo esc_html($restaurant_phone); ?></a>
                    </div>
                    <a href="<?php echo esc_url($booking_url); ?>" class="cochin-info-action-link">
                        <?php esc_html_e('Book Online Now →', 'astra-child'); ?>
                    </a>
                </div>

                <!-- Card 3: Email & Inquiries -->
                <div class="cochin-info-card">
                    <div class="cochin-info-icon-box" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                    <h3 class="cochin-info-title"><?php esc_html_e('Email & Private Events', 'astra-child'); ?></h3>
                    <p class="cochin-info-text">
                        For banquet bookings, catering consultations, and corporate event queries:
                    </p>
                    <div class="cochin-info-highlight-val">
                        <a href="mailto:<?php echo esc_attr($restaurant_email); ?>"><?php echo esc_html($restaurant_email); ?></a>
                    </div>
                    <span class="cochin-info-subtext"><?php esc_html_e('We reply within 24 hours', 'astra-child'); ?></span>
                </div>

                <!-- Card 4: Operating & Service Hours -->
                <div class="cochin-info-card">
                    <div class="cochin-info-icon-box" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h3 class="cochin-info-title"><?php esc_html_e('Opening Hours', 'astra-child'); ?></h3>
                    <div class="cochin-hours-compact-list">
                        <div class="hours-compact-item">
                            <span class="day-lbl"><?php esc_html_e('Tue – Sat:', 'astra-child'); ?></span>
                            <span class="time-lbl">12 PM – 3 PM &bull; 6 PM – 11 PM</span>
                        </div>
                        <div class="hours-compact-item">
                            <span class="day-lbl"><?php esc_html_e('Sunday:', 'astra-child'); ?></span>
                            <span class="time-lbl">12 PM – 3 PM &bull; 6 PM – 10:30 PM</span>
                        </div>
                        <div class="hours-compact-item closed-day">
                            <span class="day-lbl"><?php esc_html_e('Monday:', 'astra-child'); ?></span>
                            <span class="time-lbl"><?php esc_html_e('Closed (Available for Private Hire)', 'astra-child'); ?></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 3: INTERACTIVE 2-COLUMN SECTION (FORM & GOOGLE MAPS EMBED)
         ========================================================================= -->
    <section class="cochin-contact-main-section">
        <div class="cochin-contact-container">
            <div class="cochin-contact-layout-grid">

                <!-- Column A: Contact / Inquiry Form -->
                <div class="cochin-contact-form-column">
                    <div class="cochin-contact-form-card">
                        
                        <div class="cochin-card-header">
                            <span class="cochin-card-badge"><?php esc_html_e('ONLINE INQUIRY', 'astra-child'); ?></span>
                            <h2 class="cochin-card-title"><?php esc_html_e('Send Us a Message', 'astra-child'); ?></h2>
                            <p class="cochin-card-subtitle">
                                <?php esc_html_e('Fill out the form below and our team will get back to you promptly.', 'astra-child'); ?>
                            </p>
                        </div>

                        <form id="theCochinContactForm" class="cochin-contact-form" method="post">
                            <input type="hidden" name="action" value="the_cochin_submit_contact_form">
                            <input type="hidden" name="contact_nonce" value="<?php echo esc_attr($contact_nonce); ?>">

                            <!-- Row 1: Full Name & Telephone -->
                            <div class="cochin-form-row two-col">
                                <div class="cochin-form-group">
                                    <label for="contact_fullname"><?php esc_html_e('Full Name', 'astra-child'); ?> <span class="req">*</span></label>
                                    <input type="text" id="contact_fullname" name="fullname" class="cochin-form-input" placeholder="e.g. Sarah Jenkins" required autocomplete="name">
                                </div>

                                <div class="cochin-form-group">
                                    <label for="contact_phone"><?php esc_html_e('Telephone Number', 'astra-child'); ?> <span class="req">*</span></label>
                                    <input type="tel" id="contact_phone" name="phone" class="cochin-form-input" placeholder="e.g. 07123 456789" required autocomplete="tel">
                                </div>
                            </div>

                            <!-- Row 2: Email Address & Inquiry Type -->
                            <div class="cochin-form-row two-col">
                                <div class="cochin-form-group">
                                    <label for="contact_email"><?php esc_html_e('Email Address', 'astra-child'); ?> <span class="req">*</span></label>
                                    <input type="email" id="contact_email" name="email" class="cochin-form-input" placeholder="e.g. sarah@example.co.uk" required autocomplete="email">
                                </div>

                                <div class="cochin-form-group">
                                    <label for="contact_subject"><?php esc_html_e('Inquiry Category', 'astra-child'); ?> <span class="req">*</span></label>
                                    <select id="contact_subject" name="subject" class="cochin-form-input" required>
                                        <option value="General Inquiry" selected><?php esc_html_e('General Inquiry', 'astra-child'); ?></option>
                                        <option value="Table Reservation Query"><?php esc_html_e('Table Reservation Query', 'astra-child'); ?></option>
                                        <option value="Private Banquets & Parties"><?php esc_html_e('Private Banquets & Parties (up to 80 guests)', 'astra-child'); ?></option>
                                        <option value="Dietary & Allergen Advice"><?php esc_html_e('Dietary & Allergen Guidance', 'astra-child'); ?></option>
                                        <option value="Takeaway & Delivery"><?php esc_html_e('Takeaway & Online Ordering', 'astra-child'); ?></option>
                                        <option value="Corporate Catering"><?php esc_html_e('Corporate Dining & Catering', 'astra-child'); ?></option>
                                        <option value="Customer Feedback"><?php esc_html_e('Guest Feedback & Compliments', 'astra-child'); ?></option>
                                    </select>
                                </div>
                            </div>

                            <!-- Row 3: Message Content -->
                            <div class="cochin-form-group">
                                <label for="contact_message"><?php esc_html_e('Your Message or Inquiry Details', 'astra-child'); ?> <span class="req">*</span></label>
                                <textarea id="contact_message" name="message" class="cochin-form-textarea" rows="5" placeholder="<?php esc_attr_e('Please share your question, event dates, party size, dietary requirements, or any details...', 'astra-child'); ?>" required></textarea>
                            </div>

                            <!-- Feedback Message Container -->
                            <div id="contactFormFeedback" class="cochin-contact-feedback" style="display:none;" role="alert"></div>

                            <!-- Submit Button -->
                            <div class="cochin-form-actions">
                                <button type="submit" id="contactSubmitBtn" class="cochin-contact-submit-btn">
                                    <span><?php esc_html_e('Send Message', 'astra-child'); ?></span>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Column B: Interactive Google Maps Embed & Location Summary -->
                <div class="cochin-contact-map-column">
                    <div class="cochin-contact-map-card">
                        
                        <!-- Map Frame -->
                        <div class="cochin-map-frame-wrapper">
                            <iframe 
                                title="The Cochin Location Map"
                                src="https://maps.google.com/maps?q=61+High+Street,+Hemel+Hempstead+HP1+3AF&t=&z=16&ie=UTF8&iwloc=&output=embed" 
                                class="cochin-google-map-iframe"
                                loading="lazy" 
                                allowfullscreen="" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>

                        <!-- Location Detail Overlay Card -->
                        <div class="cochin-map-details-card">
                            <div class="cochin-map-details-content">
                                <h3 class="cochin-map-place-name"><?php esc_html_e('The Cochin Indian Restaurant', 'astra-child'); ?></h3>
                                <p class="cochin-map-place-address">
                                    📍 <?php echo esc_html($restaurant_address); ?>
                                </p>
                                <div class="cochin-map-place-tags">
                                    <span class="place-tag">Old Town Conservation Area</span>
                                    <span class="place-tag">Kerala Cuisine</span>
                                    <span class="place-tag">Licensed Bar</span>
                                </div>
                            </div>

                            <div class="cochin-map-action-btns">
                                <a href="<?php echo esc_url($google_maps_link); ?>" target="_blank" rel="noopener noreferrer" class="cochin-btn-map-nav">
                                    🧭 <?php esc_html_e('Open in Google Maps', 'astra-child'); ?>
                                </a>
                                <a href="tel:01442233777" class="cochin-btn-map-call">
                                    📞 <?php esc_html_e('Call 01442 233777', 'astra-child'); ?>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 4: TRAVEL, PARKING & ACCESSIBILITY GUIDE
         ========================================================================= -->
    <section class="cochin-travel-guide-section">
        <div class="cochin-contact-container">
            <div class="cochin-section-header-center">
                <span class="cochin-contact-badge"><?php esc_html_e('PLAN YOUR VISIT', 'astra-child'); ?></span>
                <h2 class="cochin-guide-title"><?php esc_html_e('Getting Here & Parking Guide', 'astra-child'); ?></h2>
                <p class="cochin-guide-subtitle">
                    <?php esc_html_e('Conveniently situated on the historic High Street of Hemel Hempstead Old Town with straightforward parking and public transport connections.', 'astra-child'); ?>
                </p>
            </div>

            <div class="cochin-travel-cards-grid">
                
                <!-- Travel Card 1: By Car & Parking -->
                <div class="cochin-travel-card">
                    <div class="travel-icon-box">🚗</div>
                    <h3 class="travel-card-title"><?php esc_html_e('By Car & Nearby Parking', 'astra-child'); ?></h3>
                    <ul class="travel-points-list">
                        <li>
                            <strong>On-Street Parking:</strong> Available directly on High Street (free evening parking; daytime time limits apply).
                        </li>
                        <li>
                            <strong>High Street Old Town Car Park:</strong> Located just 2 minutes' walk behind the High Street shops (HP1 3AF).
                        </li>
                        <li>
                            <strong>Gadebridge Park Car Park:</strong> Ample public parking within 3-4 minutes' stroll through the historic churchyard.
                        </li>
                    </ul>
                </div>

                <!-- Travel Card 2: Public Transport -->
                <div class="cochin-travel-card">
                    <div class="travel-icon-box">🚆</div>
                    <h3 class="travel-card-title"><?php esc_html_e('Train & Public Transport', 'astra-child'); ?></h3>
                    <ul class="travel-points-list">
                        <li>
                            <strong>Hemel Hempstead Train Station:</strong> Direct 25-30 minute London Euston connection via West Coast Mainline.
                        </li>
                        <li>
                            <strong>Bus & Taxi Connections:</strong> 5 minutes by local taxi or bus routes (1, 2, 4) from the station directly to Old Town.
                        </li>
                        <li>
                            <strong>Historic Walking Stroll:</strong> 20 minutes scenic walk from the Marlowes shopping centre via Gadebridge Park.
                        </li>
                    </ul>
                </div>

                <!-- Travel Card 3: Accessibility & Amenities -->
                <div class="cochin-travel-card">
                    <div class="travel-icon-box">♿</div>
                    <h3 class="travel-card-title"><?php esc_html_e('Accessibility & Family Dining', 'astra-child'); ?></h3>
                    <ul class="travel-points-list">
                        <li>
                            <strong>Step-Free Entry:</strong> Ground-floor dining room with wide aisles and accessible table seating.
                        </li>
                        <li>
                            <strong>Family Friendly:</strong> Highchairs, baby changing facilities, and custom mild Kerala dishes for children.
                        </li>
                        <li>
                            <strong>Dietary & Allergen Support:</strong> Full allergen matrices and separate preparation protocols for gluten-free, vegan, and nut allergies.
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 5: BOTTOM QUICK-ACTION CTA BANNER
         ========================================================================= -->
    <section class="cochin-contact-cta-section">
        <div class="cochin-contact-container">
            <div class="cochin-contact-cta-card">
                <div class="cochin-cta-text-wrap">
                    <span class="cochin-cta-badge"><?php esc_html_e('EXPERIENCE KERALA CUISINE', 'astra-child'); ?></span>
                    <h2 class="cochin-cta-headline"><?php esc_html_e('Join Us for Dinner or Plan a Private Celebration', 'astra-child'); ?></h2>
                    <p class="cochin-cta-desc">
                        <?php esc_html_e('Reserve your table online in seconds with live slot confirmation, or discover our regional seafood curries, dosas, and vegetarian delicacies.', 'astra-child'); ?>
                    </p>
                </div>
                <div class="cochin-cta-buttons-group">
                    <a href="<?php echo esc_url($booking_url); ?>" class="cochin-btn-cta-primary">
                        <span>📅 <?php esc_html_e('Book a Table Online', 'astra-child'); ?></span>
                    </a>
                    <a href="<?php echo esc_url($menu_url); ?>" class="cochin-btn-cta-secondary">
                        <span>🍛 <?php esc_html_e('Explore Restaurant Menu', 'astra-child'); ?></span>
                    </a>
                    <a href="<?php echo esc_url($banquet_url); ?>" class="cochin-btn-cta-tertiary">
                        <span>🥂 <?php esc_html_e('Banquet Hall & Events', 'astra-child'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>
