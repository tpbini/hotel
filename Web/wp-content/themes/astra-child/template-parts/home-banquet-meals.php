<?php
/**
 * The Cochin - Homepage Banquet Meals Section
 *
 * Implements the Banquet Meals promo card below the carousel:
 * - Background: Mask group.jpg
 * - Dish Media: manasa.png
 * - Font: Poppins
 * - Pricing Card: Vegetarian, Non-Vegetarian, Sea Food
 * - CTAs: Book a table, Order online
 */

if (!defined('ABSPATH')) {
    exit;
}

$defaults = the_cochin_get_banquet_data();
$acf_data = array();
if (function_exists('get_field')) {
    $map = array(
        'title'         => 'banquet_title',
        'days'          => 'banquet_days',
        'time'          => 'banquet_time',
        'desc'          => 'banquet_desc',
        'veg_price'     => 'banquet_veg_price',
        'nonveg_price'  => 'banquet_nonveg_price',
        'seafood_price' => 'banquet_seafood_price',
        'dish_image'    => 'banquet_dish_image',
        'bg_image'      => 'banquet_bg_image',
        'book_url'      => 'banquet_book_url',
        'order_url'     => 'banquet_order_url',
    );
    foreach ($map as $k => $fn) {
        $val = get_field($fn);
        if (!empty($val)) {
            $acf_data[$k] = $val;
        }
    }
}
$banquet = array_merge($defaults, $acf_data, is_array($args) ? array_filter($args) : array());
?>

<section class="cochin-banquet-section" id="banquets" style="background-image: url('<?php echo esc_url($banquet['bg_image']); ?>');">
    <div class="cochin-banquet-backdrop-overlay"></div>

    <div class="cochin-banquet-container">
        <!-- Floating Glassmorphic / Warm Beige Card -->
        <div class="cochin-banquet-card">
            <!-- Feast Dish Image Overlapping on Right -->
            <div class="cochin-banquet-media">
                <img src="<?php echo esc_url($banquet['dish_image']); ?>" alt="<?php echo esc_attr($banquet['title']); ?> Feast" class="cochin-banquet-dish-img" loading="lazy">
            </div>

            <!-- Content Container -->
            <div class="cochin-banquet-content">
                <h2 class="cochin-banquet-title"><?php echo esc_html($banquet['title']); ?></h2>
                <div class="cochin-banquet-days"><?php echo esc_html($banquet['days']); ?></div>
                <div class="cochin-banquet-time"><?php echo esc_html($banquet['time']); ?></div>
                <p class="cochin-banquet-desc"><?php echo esc_html($banquet['desc']); ?></p>

                <!-- White 3-Column Pricing Box -->
                <div class="cochin-banquet-pricing-box">
                    <div class="cochin-pricing-col">
                        <span class="cochin-pricing-label">VEGETARIAN</span>
                        <span class="cochin-pricing-val"><?php echo esc_html($banquet['veg_price']); ?></span>
                        <span class="cochin-pricing-sub">PER HEAD</span>
                    </div>

                    <div class="cochin-pricing-sep" aria-hidden="true"></div>

                    <div class="cochin-pricing-col">
                        <span class="cochin-pricing-label">NON-VEGETARIAN</span>
                        <span class="cochin-pricing-val"><?php echo esc_html($banquet['nonveg_price']); ?></span>
                        <span class="cochin-pricing-sub">PER HEAD</span>
                    </div>

                    <div class="cochin-pricing-sep" aria-hidden="true"></div>

                    <div class="cochin-pricing-col">
                        <span class="cochin-pricing-label">SEA FOOD</span>
                        <span class="cochin-pricing-val"><?php echo esc_html($banquet['seafood_price']); ?></span>
                        <span class="cochin-pricing-sub">PER HEAD</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="cochin-banquet-actions">
                    <a href="<?php echo esc_url($banquet['book_url']); ?>" class="cochin-banquet-btn btn-banquet-book">
                        <?php esc_html_e('BOOK A TABLE', 'astra-child'); ?>
                    </a>
                    <a href="<?php echo esc_url($banquet['order_url']); ?>" class="cochin-banquet-btn btn-banquet-order">
                        <?php esc_html_e('ORDER ONLINE', 'astra-child'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
