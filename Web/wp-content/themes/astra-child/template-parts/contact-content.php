<?php
/**
 * The Cochin - Contact Page Content Layout (Dynamic ACF Integrated)
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

$page_id = get_the_ID();

// Section 1: Hero & Intro
$badge    = (function_exists('get_field') ? get_field('contact_intro_badge', $page_id) : '') ?: 'VISIT & GET IN TOUCH';
$headline = (function_exists('get_field') ? get_field('contact_main_headline', $page_id) : '') ?: 'Contact The Cochin';
$intro    = (function_exists('get_field') ? get_field('contact_intro_text', $page_id) : '') ?: 'We have been proudly welcoming guests to Hemel Hempstead’s historic Old Town since 2003. Whether you are reserving a table, organising a special banquet celebration, or inquiring about our authentic Kerala menu, we look forward to hearing from you.';

// Section 2: 4-Card Highlight Grid
// Card 1: Location
$card1_title    = (function_exists('get_field') ? get_field('contact_card1_title', $page_id) : '') ?: 'Our Location';
$card1_address  = (function_exists('get_field') ? get_field('contact_card1_address', $page_id) : '') ?: "The Cochin\n61 High Street, Old Town\nHemel Hempstead, Herts\nHP1 3AF";
$card1_btn_text = (function_exists('get_field') ? get_field('contact_card1_btn_text', $page_id) : '') ?: 'Get Directions →';
$card1_map_url  = (function_exists('get_field') ? get_field('contact_card1_map_url', $page_id) : '') ?: ('https://maps.google.com/?q=' . urlencode('61 High Street, Hemel Hempstead, Hertfordshire HP1 3AF'));

// Card 2: Phone & Bookings
$card2_title    = (function_exists('get_field') ? get_field('contact_card2_title', $page_id) : '') ?: 'Telephone & Bookings';
$card2_phone    = (function_exists('get_field') ? get_field('contact_card2_phone', $page_id) : '') ?: (get_option('rb_restaurant_phone') ?: '01442 233777');
$card2_desc     = (function_exists('get_field') ? get_field('contact_card2_desc', $page_id) : '') ?: 'For immediate bookings, same-day tables, or urgent takeaway queries:';
$card2_btn_text = (function_exists('get_field') ? get_field('contact_card2_btn_text', $page_id) : '') ?: 'Book Online Now →';
$card2_btn_url  = (function_exists('get_field') ? get_field('contact_card2_btn_url', $page_id) : '') ?: the_cochin_get_booking_url();

// Card 3: Email & Private Events
$card3_title    = (function_exists('get_field') ? get_field('contact_card3_title', $page_id) : '') ?: 'Email & Private Events';
$card3_email    = (function_exists('get_field') ? get_field('contact_card3_email', $page_id) : '') ?: 'thecochin@gmail.com';
$card3_desc     = (function_exists('get_field') ? get_field('contact_card3_desc', $page_id) : '') ?: 'For banquet bookings, catering consultations, and corporate event queries:';
$card3_subtext  = (function_exists('get_field') ? get_field('contact_card3_subtext', $page_id) : '') ?: 'We reply within 24 hours';

// Card 4: Hours
$card4_title    = (function_exists('get_field') ? get_field('contact_card4_title', $page_id) : '') ?: 'Opening Hours';
$card4_tue_sat  = (function_exists('get_field') ? get_field('contact_card4_tue_sat', $page_id) : '') ?: '12 PM – 3 PM • 6 PM – 11 PM';
$card4_sun      = (function_exists('get_field') ? get_field('contact_card4_sun', $page_id) : '') ?: '12 PM – 3 PM • 6 PM – 10:30 PM';
$card4_mon      = (function_exists('get_field') ? get_field('contact_card4_mon', $page_id) : '') ?: 'Closed (Available for Private Hire)';

// Section 3: Form & Map
$form_badge     = (function_exists('get_field') ? get_field('contact_form_badge', $page_id) : '') ?: 'ONLINE INQUIRY';
$form_title     = (function_exists('get_field') ? get_field('contact_form_title', $page_id) : '') ?: 'Send Us a Message';
$form_subtitle  = (function_exists('get_field') ? get_field('contact_form_subtitle', $page_id) : '') ?: 'Fill out the form below and our team will get back to you promptly.';

$map_iframe_src   = (function_exists('get_field') ? get_field('contact_map_iframe_src', $page_id) : '') ?: 'https://maps.google.com/maps?q=61+High+Street,+Hemel+Hempstead+HP1+3AF&t=&z=16&ie=UTF8&iwloc=&output=embed';
$map_place_name   = (function_exists('get_field') ? get_field('contact_map_place_name', $page_id) : '') ?: 'The Cochin Indian Restaurant';
$map_place_addr   = (function_exists('get_field') ? get_field('contact_map_place_address', $page_id) : '') ?: '📍 61 High Street, Hemel Hempstead HP1 3AF';
$map_tags_raw     = (function_exists('get_field') ? get_field('contact_map_tags', $page_id) : '') ?: 'Old Town Conservation Area | Kerala Cuisine | Licensed Bar';
$map_call_btn     = (function_exists('get_field') ? get_field('contact_map_call_btn', $page_id) : '') ?: '📞 Call 01442 233777';
$map_call_phone   = (function_exists('get_field') ? get_field('contact_map_call_phone', $page_id) : '') ?: '01442233777';

// Section 4: Travel & Parking Guide
$guide_badge    = (function_exists('get_field') ? get_field('contact_guide_badge', $page_id) : '') ?: 'PLAN YOUR VISIT';
$guide_title    = (function_exists('get_field') ? get_field('contact_guide_title', $page_id) : '') ?: 'Getting Here & Parking Guide';
$guide_subtitle = (function_exists('get_field') ? get_field('contact_guide_subtitle', $page_id) : '') ?: 'Conveniently situated on the historic High Street of Hemel Hempstead Old Town with straightforward parking and public transport connections.';

$guide1_icon    = (function_exists('get_field') ? get_field('contact_guide1_icon', $page_id) : '') ?: '🚗';
$guide1_title   = (function_exists('get_field') ? get_field('contact_guide1_title', $page_id) : '') ?: 'By Car & Nearby Parking';
$guide1_items   = (function_exists('get_field') ? get_field('contact_guide1_items', $page_id) : '') ?: "On-Street Parking: Available directly on High Street (free evening parking; daytime time limits apply).\nHigh Street Old Town Car Park: Located just 2 minutes' walk behind the High Street shops (HP1 3AF).\nGadebridge Park Car Park: Ample public parking within 3-4 minutes' stroll through the historic churchyard.";

$guide2_icon    = (function_exists('get_field') ? get_field('contact_guide2_icon', $page_id) : '') ?: '🚆';
$guide2_title   = (function_exists('get_field') ? get_field('contact_guide2_title', $page_id) : '') ?: 'Train & Public Transport';
$guide2_items   = (function_exists('get_field') ? get_field('contact_guide2_items', $page_id) : '') ?: "Hemel Hempstead Train Station: Direct 25-30 minute London Euston connection via West Coast Mainline.\nBus & Taxi Connections: 5 minutes by local taxi or bus routes (1, 2, 4) from the station directly to Old Town.\nHistoric Walking Stroll: 20 minutes scenic walk from the Marlowes shopping centre via Gadebridge Park.";

$guide3_icon    = (function_exists('get_field') ? get_field('contact_guide3_icon', $page_id) : '') ?: '♿';
$guide3_title   = (function_exists('get_field') ? get_field('contact_guide3_title', $page_id) : '') ?: 'Accessibility & Family Dining';
$guide3_items   = (function_exists('get_field') ? get_field('contact_guide3_items', $page_id) : '') ?: "Step-Free Entry: Ground-floor dining room with wide aisles and accessible table seating.\nFamily Friendly: Highchairs, baby changing facilities, and custom mild Kerala dishes for children.\nDietary & Allergen Support: Full allergen matrices and separate preparation protocols for gluten-free, vegan, and nut allergies.";

// Section 5: Bottom CTA
$cta_badge      = (function_exists('get_field') ? get_field('contact_cta_badge', $page_id) : '') ?: 'EXPERIENCE KERALA CUISINE';
$cta_headline   = (function_exists('get_field') ? get_field('contact_cta_headline', $page_id) : '') ?: 'Join Us for Dinner or Plan a Private Celebration';
$cta_desc       = (function_exists('get_field') ? get_field('contact_cta_desc', $page_id) : '') ?: 'Reserve your table online in seconds with live slot confirmation, or discover our regional seafood curries, dosas, and vegetarian delicacies.';
$cta_btn1_text  = (function_exists('get_field') ? get_field('contact_cta_btn1_text', $page_id) : '') ?: '📅 Book a Table Online';
$cta_btn1_url   = (function_exists('get_field') ? get_field('contact_cta_btn1_url', $page_id) : '') ?: the_cochin_get_booking_url();
$cta_btn2_text  = (function_exists('get_field') ? get_field('contact_cta_btn2_text', $page_id) : '') ?: '🍛 Explore Restaurant Menu';
$cta_btn2_url   = (function_exists('get_field') ? get_field('contact_cta_btn2_url', $page_id) : '') ?: the_cochin_get_menu_page_url();
$cta_btn3_text  = (function_exists('get_field') ? get_field('contact_cta_btn3_text', $page_id) : '') ?: '🥂 Banquet Hall & Events';
$cta_btn3_url   = (function_exists('get_field') ? get_field('contact_cta_btn3_url', $page_id) : '') ?: home_url('/banquets/');

$contact_nonce  = wp_create_nonce('the_cochin_contact_nonce');

if (!function_exists('the_cochin_render_guide_list_items')) {
    function the_cochin_render_guide_list_items($raw_items) {
        if (empty($raw_items)) return;
        $lines = preg_split('/\r\n|\r|\n/', trim($raw_items));
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (strpos($line, ':') !== false && strpos($line, '<') === false) {
                $parts = explode(':', $line, 2);
                echo '<li><strong>' . esc_html(trim($parts[0])) . ':</strong> ' . esc_html(trim($parts[1])) . '</li>';
            } else {
                echo '<li>' . wp_kses_post($line) . '</li>';
            }
        }
    }
}
?>

<div class="cochin-contact-page-layout">

    <!-- =========================================================================
         SECTION 1: HERO & INTRO HEADER
         ========================================================================= -->
    <section class="cochin-contact-hero-section">
        <div class="cochin-contact-container">
            <div class="cochin-contact-hero-card">
                <?php if (!empty($badge)) : ?>
                    <span class="cochin-contact-badge"><?php echo esc_html($badge); ?></span>
                <?php endif; ?>
                <h1 class="cochin-contact-headline"><?php echo esc_html($headline); ?></h1>
                <?php if (!empty($intro)) : ?>
                    <p class="cochin-contact-subtitle">
                        <?php echo nl2br(esc_html($intro)); ?>
                    </p>
                <?php endif; ?>
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
                    <h3 class="cochin-info-title"><?php echo esc_html($card1_title); ?></h3>
                    <p class="cochin-info-text">
                        <?php echo nl2br(esc_html($card1_address)); ?>
                    </p>
                    <?php if (!empty($card1_btn_text) && !empty($card1_map_url)) : ?>
                        <a href="<?php echo esc_url($card1_map_url); ?>" target="_blank" rel="noopener noreferrer" class="cochin-info-action-link">
                            <?php echo esc_html($card1_btn_text); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Card 2: Telephone & Bookings -->
                <div class="cochin-info-card">
                    <div class="cochin-info-icon-box" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </div>
                    <h3 class="cochin-info-title"><?php echo esc_html($card2_title); ?></h3>
                    <?php if (!empty($card2_desc)) : ?>
                        <p class="cochin-info-text">
                            <?php echo esc_html($card2_desc); ?>
                        </p>
                    <?php endif; ?>
                    <div class="cochin-info-highlight-val">
                        <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $card2_phone)); ?>"><?php echo esc_html($card2_phone); ?></a>
                    </div>
                    <?php if (!empty($card2_btn_text) && !empty($card2_btn_url)) : ?>
                        <a href="<?php echo esc_url($card2_btn_url); ?>" class="cochin-info-action-link">
                            <?php echo esc_html($card2_btn_text); ?>
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Card 3: Email & Inquiries -->
                <div class="cochin-info-card">
                    <div class="cochin-info-icon-box" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                            <polyline points="22,6 12,13 2,6"></polyline>
                        </svg>
                    </div>
                    <h3 class="cochin-info-title"><?php echo esc_html($card3_title); ?></h3>
                    <?php if (!empty($card3_desc)) : ?>
                        <p class="cochin-info-text">
                            <?php echo esc_html($card3_desc); ?>
                        </p>
                    <?php endif; ?>
                    <div class="cochin-info-highlight-val">
                        <a href="mailto:<?php echo esc_attr($card3_email); ?>"><?php echo esc_html($card3_email); ?></a>
                    </div>
                    <?php if (!empty($card3_subtext)) : ?>
                        <span class="cochin-info-subtext"><?php echo esc_html($card3_subtext); ?></span>
                    <?php endif; ?>
                </div>

                <!-- Card 4: Operating & Service Hours -->
                <div class="cochin-info-card">
                    <div class="cochin-info-icon-box" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <polyline points="12 6 12 12 16 14"></polyline>
                        </svg>
                    </div>
                    <h3 class="cochin-info-title"><?php echo esc_html($card4_title); ?></h3>
                    <div class="cochin-hours-compact-list">
                        <?php if (!empty($card4_tue_sat)) : ?>
                            <div class="hours-compact-item">
                                <span class="day-lbl"><?php esc_html_e('Tue – Sat:', 'astra-child'); ?></span>
                                <span class="time-lbl"><?php echo esc_html($card4_tue_sat); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($card4_sun)) : ?>
                            <div class="hours-compact-item">
                                <span class="day-lbl"><?php esc_html_e('Sunday:', 'astra-child'); ?></span>
                                <span class="time-lbl"><?php echo esc_html($card4_sun); ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($card4_mon)) : ?>
                            <div class="hours-compact-item closed-day">
                                <span class="day-lbl"><?php esc_html_e('Monday:', 'astra-child'); ?></span>
                                <span class="time-lbl"><?php echo esc_html($card4_mon); ?></span>
                            </div>
                        <?php endif; ?>
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
                            <?php if (!empty($form_badge)) : ?>
                                <span class="cochin-card-badge"><?php echo esc_html($form_badge); ?></span>
                            <?php endif; ?>
                            <h2 class="cochin-card-title"><?php echo esc_html($form_title); ?></h2>
                            <?php if (!empty($form_subtitle)) : ?>
                                <p class="cochin-card-subtitle">
                                    <?php echo esc_html($form_subtitle); ?>
                                </p>
                            <?php endif; ?>
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
                                src="<?php echo esc_url($map_iframe_src); ?>" 
                                class="cochin-google-map-iframe"
                                loading="lazy" 
                                allowfullscreen="" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>

                        <!-- Location Detail Overlay Card -->
                        <div class="cochin-map-details-card">
                            <div class="cochin-map-details-content">
                                <h3 class="cochin-map-place-name"><?php echo esc_html($map_place_name); ?></h3>
                                <p class="cochin-map-place-address">
                                    <?php echo esc_html($map_place_addr); ?>
                                </p>
                                <?php if (!empty($map_tags_raw)) : 
                                    $tags = array_map('trim', preg_split('/[|,]/', $map_tags_raw));
                                    if (!empty($tags)) :
                                ?>
                                    <div class="cochin-map-place-tags">
                                        <?php foreach ($tags as $tag) : if (!empty($tag)) : ?>
                                            <span class="place-tag"><?php echo esc_html($tag); ?></span>
                                        <?php endif; endforeach; ?>
                                    </div>
                                <?php endif; endif; ?>
                            </div>

                            <div class="cochin-map-action-btns">
                                <?php if (!empty($card1_map_url)) : ?>
                                    <a href="<?php echo esc_url($card1_map_url); ?>" target="_blank" rel="noopener noreferrer" class="cochin-btn-map-nav">
                                        🧭 <?php esc_html_e('Open in Google Maps', 'astra-child'); ?>
                                    </a>
                                <?php endif; ?>
                                <?php if (!empty($map_call_btn) && !empty($map_call_phone)) : ?>
                                    <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $map_call_phone)); ?>" class="cochin-btn-map-call">
                                        <?php echo esc_html($map_call_btn); ?>
                                    </a>
                                <?php endif; ?>
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
                <?php if (!empty($guide_badge)) : ?>
                    <span class="cochin-contact-badge"><?php echo esc_html($guide_badge); ?></span>
                <?php endif; ?>
                <h2 class="cochin-guide-title"><?php echo esc_html($guide_title); ?></h2>
                <?php if (!empty($guide_subtitle)) : ?>
                    <p class="cochin-guide-subtitle">
                        <?php echo esc_html($guide_subtitle); ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="cochin-travel-cards-grid">
                
                <!-- Travel Card 1: By Car & Parking -->
                <div class="cochin-travel-card">
                    <div class="travel-icon-box"><?php echo esc_html($guide1_icon); ?></div>
                    <h3 class="travel-card-title"><?php echo esc_html($guide1_title); ?></h3>
                    <ul class="travel-points-list">
                        <?php the_cochin_render_guide_list_items($guide1_items); ?>
                    </ul>
                </div>

                <!-- Travel Card 2: Public Transport -->
                <div class="cochin-travel-card">
                    <div class="travel-icon-box"><?php echo esc_html($guide2_icon); ?></div>
                    <h3 class="travel-card-title"><?php echo esc_html($guide2_title); ?></h3>
                    <ul class="travel-points-list">
                        <?php the_cochin_render_guide_list_items($guide2_items); ?>
                    </ul>
                </div>

                <!-- Travel Card 3: Accessibility & Amenities -->
                <div class="cochin-travel-card">
                    <div class="travel-icon-box"><?php echo esc_html($guide3_icon); ?></div>
                    <h3 class="travel-card-title"><?php echo esc_html($guide3_title); ?></h3>
                    <ul class="travel-points-list">
                        <?php the_cochin_render_guide_list_items($guide3_items); ?>
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
                    <?php if (!empty($cta_badge)) : ?>
                        <span class="cochin-cta-badge"><?php echo esc_html($cta_badge); ?></span>
                    <?php endif; ?>
                    <h2 class="cochin-cta-headline"><?php echo esc_html($cta_headline); ?></h2>
                    <?php if (!empty($cta_desc)) : ?>
                        <p class="cochin-cta-desc">
                            <?php echo esc_html($cta_desc); ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="cochin-cta-buttons-group">
                    <?php if (!empty($cta_btn1_text) && !empty($cta_btn1_url)) : ?>
                        <a href="<?php echo esc_url($cta_btn1_url); ?>" class="cochin-btn-cta-primary">
                            <span><?php echo esc_html($cta_btn1_text); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($cta_btn2_text) && !empty($cta_btn2_url)) : ?>
                        <a href="<?php echo esc_url($cta_btn2_url); ?>" class="cochin-btn-cta-secondary">
                            <span><?php echo esc_html($cta_btn2_text); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($cta_btn3_text) && !empty($cta_btn3_url)) : ?>
                        <a href="<?php echo esc_url($cta_btn3_url); ?>" class="cochin-btn-cta-tertiary">
                            <span><?php echo esc_html($cta_btn3_text); ?></span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

</div>
