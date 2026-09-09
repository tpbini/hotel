<?php
/**
 * The Cochin - Homepage Delivery & Collection Section
 *
 * Implements the "Book, Collect or order for delivery" section:
 * - Background: #F5EFE3
 * - Image: deliver.png / delivery.png
 * - Font: Poppins (Heading, Description, Buttons), Architects Daughter (Badge)
 * - CTAs: Book a table (#31211B), Order online (#6B1F2A)
 */

if (!defined('ABSPATH')) {
    exit;
}

$defaults = the_cochin_get_delivery_data();
$delivery = array_merge($defaults, is_array($args) ? array_filter($args) : array());
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

                <p class="cochin-delivery-desc"><?php echo esc_html($delivery['desc']); ?></p>

                <div class="cochin-delivery-actions">
                    <a href="<?php echo esc_url($delivery['book_url']); ?>" class="cochin-delivery-btn btn-delivery-book">
                        <?php esc_html_e('BOOK A TABLE', 'astra-child'); ?>
                    </a>
                    <a href="<?php echo esc_url($delivery['order_url']); ?>" class="cochin-delivery-btn btn-delivery-order">
                        <?php esc_html_e('ORDER ONLINE', 'astra-child'); ?>
                    </a>
                </div>
            </div>

            <!-- Right Illustration Media -->
            <div class="cochin-delivery-media">
                <img src="<?php echo esc_url($delivery['image']); ?>" alt="<?php echo esc_attr(strip_tags($delivery['title'])); ?>" class="cochin-delivery-img" loading="lazy">
            </div>
        </div>
    </div>
</section>
