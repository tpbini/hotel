<?php
/**
 * The Cochin - Homepage Vegetarian & Vegan Choices Section
 *
 * Implements the dietary choices promo banner below the Delivery section:
 * - Background: bg.jpg with warm dark overlay
 * - Font: Poppins (Heading, Description, Buttons), Architects Daughter (Badge)
 * - CTAs: Book a table (#C9A24D), Order online (#6B1F2A)
 */

if (!defined('ABSPATH')) {
    exit;
}

$dietary = the_cochin_get_dietary_data();
?>

<section class="cochin-dietary-section" id="dietary" style="background-image: url('<?php echo esc_url($dietary['bg_image']); ?>');">
    <div class="cochin-dietary-overlay"></div>

    <div class="cochin-dietary-container">
        <div class="cochin-dietary-content">
            <div class="cochin-dietary-badge">
                <span class="cochin-badge-text"><?php echo esc_html($dietary['badge']); ?></span>
                <span class="cochin-badge-line" aria-hidden="true"></span>
            </div>

            <h2 class="cochin-dietary-title"><?php echo esc_html($dietary['title']); ?></h2>

            <p class="cochin-dietary-desc"><?php echo esc_html($dietary['desc']); ?></p>

            <div class="cochin-dietary-actions">
                <a href="<?php echo esc_url($dietary['book_url']); ?>" class="cochin-dietary-btn btn-dietary-book">
                    <?php esc_html_e('BOOK A TABLE', 'astra-child'); ?>
                </a>
                <a href="<?php echo esc_url($dietary['order_url']); ?>" class="cochin-dietary-btn btn-dietary-order">
                    <?php esc_html_e('ORDER ONLINE', 'astra-child'); ?>
                </a>
            </div>
        </div>
    </div>
</section>
