<?php
/**
 * The Cochin - Custom Site Footer
 *
 * Implements the 4-column footer matching the brand guidelines:
 * - Brand description & social follow buttons
 * - Quick Links
 * - Opening hours
 * - Visits & Contact details
 * - Copyright & Legal links bar
 * - Background: #6B1F2A, Font: Poppins
 */

if (!defined('ABSPATH')) {
    exit;
}

$footer_logo = get_stylesheet_directory_uri() . '/assets/images/the-cochin-logo-white.png';
$booking_url = the_cochin_get_booking_url();
$menu_url    = the_cochin_get_menu_page_url();
?>

<footer class="site-footer cochin-site-footer" id="colophon" role="contentinfo">
    <div class="cochin-footer-container">
        <div class="cochin-footer-grid">
            <!-- Column 1: Brand, About & Social -->
            <div class="cochin-footer-col cochin-footer-brand-col">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="cochin-footer-logo-link" aria-label="The Cochin — Home">
                    <img src="<?php echo esc_url($footer_logo); ?>" alt="The Cochin" class="cochin-footer-logo" loading="lazy">
                </a>
                <p class="cochin-footer-about">
                    <?php esc_html_e('The Cochin has been bringing the authentic flavours and warm hospitality of Kerala to Hemel Hempstead\'s Old Town since 2003.', 'astra-child'); ?>
                </p>

                <div class="cochin-footer-social">
                    <h4 class="cochin-footer-social-title"><?php esc_html_e('Follow Us', 'astra-child'); ?></h4>
                    <div class="cochin-social-links">
                        <a href="https://facebook.com" class="cochin-social-btn" target="_blank" rel="noopener noreferrer" aria-label="Follow us on Facebook">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="https://instagram.com" class="cochin-social-btn" target="_blank" rel="noopener noreferrer" aria-label="Follow us on Instagram">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                                <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                                <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Column 2: Quick Links -->
            <div class="cochin-footer-col cochin-footer-links-col">
                <h3 class="cochin-footer-heading"><?php esc_html_e('Quick Links', 'astra-child'); ?></h3>
                <ul class="cochin-footer-nav">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'astra-child'); ?></a></li>
                    <li><a href="<?php echo esc_url($menu_url); ?>"><?php esc_html_e('Menu', 'astra-child'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/#about')); ?>"><?php esc_html_e('About', 'astra-child'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/#banquets')); ?>"><?php esc_html_e('Banquet Meals', 'astra-child'); ?></a></li>
                    <li><a href="<?php echo esc_url($booking_url); ?>"><?php esc_html_e('Book a table', 'astra-child'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/#order')); ?>"><?php esc_html_e('Order online', 'astra-child'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/#contact')); ?>"><?php esc_html_e('Contact', 'astra-child'); ?></a></li>
                </ul>
            </div>

            <!-- Column 3: Opening Hours -->
            <div class="cochin-footer-col cochin-footer-hours-col">
                <h3 class="cochin-footer-heading"><?php esc_html_e('Opening hours', 'astra-child'); ?></h3>
                <div class="cochin-footer-hours-block">
                    <strong class="cochin-hours-day"><?php esc_html_e('Tuesday – Saturday', 'astra-child'); ?></strong>
                    <span class="cochin-hours-time">12 PM – 3 PM</span>
                    <span class="cochin-hours-time">6 PM – 11 PM</span>
                </div>
                <div class="cochin-footer-hours-block">
                    <strong class="cochin-hours-day"><?php esc_html_e('Sunday', 'astra-child'); ?></strong>
                    <span class="cochin-hours-time">12 PM – 3 PM</span>
                    <span class="cochin-hours-time">6 PM – 10:30 PM</span>
                </div>
                <div class="cochin-footer-hours-block">
                    <strong class="cochin-hours-day"><?php esc_html_e('Monday: Closed', 'astra-child'); ?></strong>
                </div>
            </div>

            <!-- Column 4: Visits & Contact -->
            <div class="cochin-footer-col cochin-footer-contact-col">
                <h3 class="cochin-footer-heading"><?php esc_html_e('Visits & Contact', 'astra-child'); ?></h3>
                <ul class="cochin-footer-contact-list">
                    <li>
                        <span class="cochin-contact-icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                        </span>
                        <span class="cochin-contact-text"><?php esc_html_e('61 High Street, Hemel Hempstead, HP1 3AF', 'astra-child'); ?></span>
                    </li>
                    <li>
                        <span class="cochin-contact-icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                            </svg>
                        </span>
                        <a href="tel:01442233777" class="cochin-contact-link">01442 233777</a>
                    </li>
                    <li>
                        <span class="cochin-contact-icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                <polyline points="22,6 12,13 2,6"></polyline>
                            </svg>
                        </span>
                        <a href="mailto:thecochin@gmail.com" class="cochin-contact-link">thecochin@gmail.com</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bottom Copyright & Legal Links Bar -->
    <div class="cochin-footer-bottom">
        <div class="cochin-footer-bottom-container">
            <div class="cochin-footer-copy">
                &copy; <?php echo date('Y'); ?> <?php esc_html_e('The Cochin Indian Restaurant. All rights reserved', 'astra-child'); ?>
            </div>
            <div class="cochin-footer-legal">
                <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>"><?php esc_html_e('Privacy Policy', 'astra-child'); ?></a>
                <span class="cochin-legal-sep" aria-hidden="true">|</span>
                <a href="<?php echo esc_url(home_url('/cookie-policy/')); ?>"><?php esc_html_e('Cookie Policy', 'astra-child'); ?></a>
                <span class="cochin-legal-sep" aria-hidden="true">|</span>
                <a href="<?php echo esc_url(home_url('/terms-conditions/')); ?>"><?php esc_html_e('Terms & Conditions', 'astra-child'); ?></a>
                <span class="cochin-legal-sep" aria-hidden="true">|</span>
                <a href="<?php echo esc_url(home_url('/allergen-information/')); ?>"><?php esc_html_e('Allergen Information', 'astra-child'); ?></a>
            </div>
        </div>
    </div>
</footer>
