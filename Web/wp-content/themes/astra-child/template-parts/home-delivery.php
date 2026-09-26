<?php
/**
 * The Cochin - Homepage Delivery & Collection Section
 *
 * Implements the "Book, Collect or order for delivery" section:
 * - Partner integrations: Uber Eats & Just Eat
 * - Direct phone collection discount (15% off)
 * - Modal trigger for online orders
 */

if (!defined('ABSPATH')) {
    exit;
}

$defaults = the_cochin_get_delivery_data();
$acf_data = array();
if (function_exists('get_field')) {
    $map = array(
        'badge'     => 'delivery_badge',
        'title'     => 'delivery_title',
        'desc'      => 'delivery_desc',
        'image'     => 'delivery_image',
        'book_url'  => 'delivery_book_url',
        'order_url' => 'delivery_order_url',
    );
    foreach ($map as $k => $fn) {
        $val = get_field($fn);
        if (!empty($val)) {
            $acf_data[$k] = $val;
        }
    }
}
$delivery = array_merge($defaults, $acf_data, is_array($args) ? array_filter($args) : array());

$ubereats_url = function_exists('the_cochin_get_ubereats_url') ? the_cochin_get_ubereats_url() : 'https://www.ubereats.com/gb';
$justeat_url  = function_exists('the_cochin_get_justeat_url') ? the_cochin_get_justeat_url() : 'https://www.just-eat.co.uk/';
$restaurant_phone = get_option('rb_restaurant_phone', '01442 233777');
$clean_phone = preg_replace('/[^0-9+]/', '', $restaurant_phone);
$img_base = get_stylesheet_directory_uri() . '/assets/images/';
?>

<section class="cochin-delivery-section" id="delivery">
    <div class="cochin-delivery-container">
        <div class="cochin-delivery-grid">
            <!-- Left Text Content -->
            <div class="cochin-delivery-content">
                <div class="cochin-delivery-badge">
                    <span class="cochin-badge-text"><?php echo esc_html($delivery['badge']); ?></span>
                    <span class="cochin-badge-line" aria-hidden="true"></span>
                </div>

                <h2 class="cochin-delivery-title"><?php echo wp_kses_post($delivery['title']); ?></h2>

                <p class="cochin-delivery-desc">
                    <?php esc_html_e('Craving authentic South Indian flavours at home? Order for speedy doorstep delivery via our official partners Uber Eats and Just Eat, or order directly for takeaway collection.', 'astra-child'); ?>
                </p>

                <!-- Delivery Partner Badges / CTAs -->
                <div class="cochin-delivery-partners-strip">
                    <span class="cochin-partners-label"><?php esc_html_e('Available on:', 'astra-child'); ?></span>
                    <div class="cochin-partners-logos">
                        <a href="<?php echo esc_url($ubereats_url); ?>" target="_blank" rel="noopener noreferrer" class="cochin-partner-pill pill-uber" title="<?php esc_attr_e('Order on Uber Eats', 'astra-child'); ?>">
                            <img src="<?php echo esc_url($img_base . 'uber-eats-badge.svg'); ?>" alt="Uber Eats" width="110" height="26">
                        </a>
                        <a href="<?php echo esc_url($justeat_url); ?>" target="_blank" rel="noopener noreferrer" class="cochin-partner-pill pill-justeat" title="<?php esc_attr_e('Order on Just Eat', 'astra-child'); ?>">
                            <img src="<?php echo esc_url($img_base . 'just-eat-badge.svg'); ?>" alt="Just Eat" width="110" height="26">
                        </a>
                    </div>
                </div>

                <div class="cochin-delivery-actions">
                    <a href="<?php echo esc_url($delivery['book_url']); ?>" class="cochin-delivery-btn btn-delivery-book">
                        <?php esc_html_e('BOOK A TABLE', 'astra-child'); ?>
                    </a>
                    <button type="button" class="cochin-delivery-btn btn-delivery-order" data-open-order-modal="true">
                        <?php esc_html_e('ORDER ONLINE', 'astra-child'); ?>
                    </button>
                </div>
            </div>

            <!-- Right Illustration Media -->
            <div class="cochin-delivery-media">
                <img src="<?php echo esc_url($delivery['image']); ?>" alt="<?php echo esc_attr(strip_tags($delivery['title'])); ?>" class="cochin-delivery-img" loading="lazy">
            </div>
        </div>
    </div>
</section>

