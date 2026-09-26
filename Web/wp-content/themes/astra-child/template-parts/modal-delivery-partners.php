<?php
/**
 * The Cochin - Delivery & Takeaway Partner Selection Modal
 *
 * Provides a seamless popup allowing customers to choose between:
 * - Uber Eats (Delivery)
 * - Just Eat (Delivery)
 * - Direct Takeaway Collection (Phone & In-Store pickup with 15% discount)
 */

if (!defined('ABSPATH')) {
    exit;
}

$ubereats_url = function_exists('the_cochin_get_ubereats_url') ? the_cochin_get_ubereats_url() : 'https://www.ubereats.com/gb';
$justeat_url  = function_exists('the_cochin_get_justeat_url') ? the_cochin_get_justeat_url() : 'https://www.just-eat.co.uk/';
$restaurant_phone = get_option('rb_restaurant_phone', '01442 233777');
$clean_phone = preg_replace('/[^0-9+]/', '', $restaurant_phone);
$img_base = get_stylesheet_directory_uri() . '/assets/images/';
?>

<div class="cochin-order-modal-backdrop" id="cochinOrderModal" role="dialog" aria-modal="true" aria-labelledby="cochinOrderModalTitle" style="display:none;">
    <div class="cochin-order-modal-box">
        <!-- Close Button -->
        <button type="button" class="cochin-order-modal-close" id="cochinOrderModalClose" aria-label="<?php esc_attr_e('Close order options', 'astra-child'); ?>">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>

        <div class="cochin-order-modal-header">
            <span class="cochin-order-modal-badge"><?php esc_html_e('ONLINE ORDERING & DELIVERY', 'astra-child'); ?></span>
            <h3 class="cochin-order-modal-title" id="cochinOrderModalTitle"><?php esc_html_e('How would you like to enjoy The Cochin today?', 'astra-child'); ?></h3>
            <p class="cochin-order-modal-subtitle"><?php esc_html_e('Choose your preferred delivery partner for doorstep delivery, or order directly for takeaway collection.', 'astra-child'); ?></p>
        </div>

        <div class="cochin-order-modal-grid">
            <!-- Uber Eats Option -->
            <a href="<?php echo esc_url($ubereats_url); ?>" target="_blank" rel="noopener noreferrer" class="cochin-partner-card card-ubereats">
                <div class="cochin-partner-logo-wrap">
                    <img src="<?php echo esc_url($img_base . 'uber-eats-badge.svg'); ?>" alt="Uber Eats" class="cochin-partner-badge-img" width="140" height="38">
                </div>
                <div class="cochin-partner-info">
                    <h4 class="cochin-partner-name"><?php esc_html_e('Order with Uber Eats', 'astra-child'); ?></h4>
                    <p class="cochin-partner-desc"><?php esc_html_e('Fast, tracked delivery directly to your home or office.', 'astra-child'); ?></p>
                </div>
                <span class="cochin-partner-action-btn btn-uber">
                    <?php esc_html_e('Order on Uber Eats &rarr;', 'astra-child'); ?>
                </span>
            </a>

            <!-- Just Eat Option -->
            <a href="<?php echo esc_url($justeat_url); ?>" target="_blank" rel="noopener noreferrer" class="cochin-partner-card card-justeat">
                <div class="cochin-partner-logo-wrap">
                    <img src="<?php echo esc_url($img_base . 'just-eat-badge.svg'); ?>" alt="Just Eat" class="cochin-partner-badge-img" width="140" height="38">
                </div>
                <div class="cochin-partner-info">
                    <h4 class="cochin-partner-name"><?php esc_html_e('Order with Just Eat', 'astra-child'); ?></h4>
                    <p class="cochin-partner-desc"><?php esc_html_e('Order online through Just Eat for quick and reliable local delivery.', 'astra-child'); ?></p>
                </div>
                <span class="cochin-partner-action-btn btn-justeat">
                    <?php esc_html_e('Order on Just Eat &rarr;', 'astra-child'); ?>
                </span>
            </a>

            <!-- Direct Collection / Takeaway Option -->
            <div class="cochin-partner-card card-collection">
                <div class="cochin-partner-logo-wrap">
                    <span class="cochin-collection-badge">🥡 <?php esc_html_e('15% OFF COLLECTION', 'astra-child'); ?></span>
                </div>
                <div class="cochin-partner-info">
                    <h4 class="cochin-partner-name"><?php esc_html_e('Takeaway Collection', 'astra-child'); ?></h4>
                    <p class="cochin-partner-desc"><?php esc_html_e('Order directly by phone and save 15% on collection orders over £25.', 'astra-child'); ?></p>
                </div>
                <a href="tel:<?php echo esc_attr($clean_phone); ?>" class="cochin-partner-action-btn btn-collection">
                    📞 <?php echo esc_html($restaurant_phone); ?>
                </a>
            </div>
        </div>

        <div class="cochin-order-modal-footer">
            <span class="cochin-order-location-note">📍 61 High Street, Old Town, Hemel Hempstead, HP1 3AF</span>
        </div>
    </div>
</div>
