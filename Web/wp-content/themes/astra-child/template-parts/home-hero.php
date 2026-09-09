<?php
/**
 * The Cochin - Homepage Hero Section
 *
 * Implements the full-width culinary hero banner with Amaranth heading,
 * Poppins description, and custom styled buttons (#6B1F2A, #FF8B26, #C9A24D).
 */

if (!defined('ABSPATH')) {
    exit;
}

// Customizable Hero Settings (with smart defaults & shortcode args)
$hero_bg = !empty($args['bg_image']) ? $args['bg_image'] : get_theme_mod('the_cochin_hero_bg');
if (empty($hero_bg)) {
    $hero_bg = get_stylesheet_directory_uri() . '/assets/images/kerala-cuisine-hero.png';
}

$hero_title = !empty($args['title']) ? $args['title'] : get_theme_mod(
    'the_cochin_hero_title',
    __('Authentic Kerala Cuisine in Hemel Hempstead\'s Old Town', 'astra-child')
);

$hero_desc = !empty($args['desc']) ? $args['desc'] : get_theme_mod(
    'the_cochin_hero_desc',
    __('Proudly serving the local community since 2003. Discover the distinctive flavours of Kerala, freshly prepared and served with the warmth of traditional South Indian hospitality.', 'astra-child')
);

$btn1_text = isset($args['btn1_text']) ? $args['btn1_text'] : get_theme_mod('the_cochin_btn1_text', __('BOOK A TABLE', 'astra-child'));
$btn1_url  = !empty($args['btn1_url']) ? $args['btn1_url'] : get_theme_mod('the_cochin_btn1_url', the_cochin_get_booking_url());

$btn2_text = isset($args['btn2_text']) ? $args['btn2_text'] : get_theme_mod('the_cochin_btn2_text', __('ORDER ONLINE', 'astra-child'));
$btn2_url  = !empty($args['btn2_url']) ? $args['btn2_url'] : get_theme_mod('the_cochin_btn2_url', home_url('/#order'));

$btn3_text = isset($args['btn3_text']) ? $args['btn3_text'] : get_theme_mod('the_cochin_btn3_text', __('VIEW MENU', 'astra-child'));
$btn3_url  = !empty($args['btn3_url']) ? $args['btn3_url'] : get_theme_mod('the_cochin_btn3_url', home_url('/#menu'));
?>

<section class="cochin-hero-section" style="background-image: url('<?php echo esc_url($hero_bg); ?>');">
    <div class="cochin-hero-overlay"></div>
    <div class="cochin-hero-container">
        <div class="cochin-hero-content">
            <h1 class="cochin-hero-title"><?php echo esc_html($hero_title); ?></h1>
            <p class="cochin-hero-description"><?php echo esc_html($hero_desc); ?></p>
            <div class="cochin-hero-actions">
                <?php if (!empty($btn1_text)) : ?>
                    <a href="<?php echo esc_url($btn1_url); ?>" class="cochin-hero-btn btn-brand">
                        <?php echo esc_html($btn1_text); ?>
                    </a>
                <?php endif; ?>

                <?php if (!empty($btn2_text)) : ?>
                    <a href="<?php echo esc_url($btn2_url); ?>" class="cochin-hero-btn btn-orange">
                        <?php echo esc_html($btn2_text); ?>
                    </a>
                <?php endif; ?>

                <?php if (!empty($btn3_text)) : ?>
                    <a href="<?php echo esc_url($btn3_url); ?>" class="cochin-hero-btn btn-gold">
                        <?php echo esc_html($btn3_text); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
