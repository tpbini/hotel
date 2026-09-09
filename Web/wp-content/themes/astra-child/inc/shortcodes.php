<?php
/**
 * The Cochin - Shortcodes & Gutenberg Integrations
 *
 * Exposes all homepage and theme sections as editable Gutenberg shortcodes & block patterns
 * so users can edit the homepage directly in WordPress Admin (Pages > Home).
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * 1. Hero Section Shortcode: [the_cochin_hero]
 */
function the_cochin_hero_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title'      => '',
        'desc'       => '',
        'btn1_text'  => 'BOOK A TABLE',
        'btn1_url'   => the_cochin_get_booking_url(),
        'btn2_text'  => 'ORDER ONLINE',
        'btn2_url'   => home_url('/#order'),
        'btn3_text'  => 'VIEW MENU',
        'btn3_url'   => home_url('/#menu'),
        'bg_image'   => '',
    ), $atts, 'the_cochin_hero');

    ob_start();
    get_template_part('template-parts/home-hero', null, $atts);
    return ob_get_clean();
}
add_shortcode('the_cochin_hero', 'the_cochin_hero_shortcode');

/**
 * 2. About Us Section Shortcode: [the_cochin_about]
 */
function the_cochin_about_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'badge' => 'About us',
        'title' => 'Welcome to The Cochin',
        'image' => '',
    ), $atts, 'the_cochin_about');

    if (!empty($content)) {
        $atts['content'] = do_shortcode($content);
    }

    ob_start();
    get_template_part('template-parts/home-about', null, $atts);
    return ob_get_clean();
}
add_shortcode('the_cochin_about', 'the_cochin_about_shortcode');

/**
 * 3. Menu Carousel Shortcode: [the_cochin_menu_carousel] or [the_cochin_carousel]
 */
function the_cochin_carousel_shortcode($atts) {
    ob_start();
    get_template_part('template-parts/home-menu-carousel');
    return ob_get_clean();
}
add_shortcode('the_cochin_menu_carousel', 'the_cochin_carousel_shortcode');
add_shortcode('the_cochin_carousel', 'the_cochin_carousel_shortcode');

/**
 * 4. Banquet Meals Shortcode: [the_cochin_banquets] or [the_cochin_banquet_meals]
 */
function the_cochin_banquets_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title'         => 'Banquet Meals',
        'days'          => 'EVERY TUESDAY • THURSDAY • WEDNESDAY',
        'time'          => '6PM – 11 PM',
        'desc'          => '1 Starter + 1 Main Dish + Side Dish + Rice & Bread',
        'veg_price'     => '£ 15.95',
        'nonveg_price'  => '£ 17.95',
        'seafood_price' => '£ 19.95',
        'book_url'      => the_cochin_get_booking_url(),
        'order_url'     => home_url('/menu/'),
        'dish_image'    => '',
        'bg_image'      => '',
    ), $atts, 'the_cochin_banquets');

    ob_start();
    get_template_part('template-parts/home-banquet-meals', null, $atts);
    return ob_get_clean();
}
add_shortcode('the_cochin_banquets', 'the_cochin_banquets_shortcode');
add_shortcode('the_cochin_banquet_meals', 'the_cochin_banquets_shortcode');

/**
 * 5. Delivery & Collection Shortcode: [the_cochin_delivery]
 */
function the_cochin_delivery_shortcode($atts) {
    $atts = shortcode_atts(array(
        'badge'     => 'Our Menu',
        'title'     => 'Book, Collect or order for<br>delivery',
        'desc'      => 'Dining with us? Reserve your table online. Prefer to enjoy The Cochin at home? Order for collection or local delivery through our secure online ordering service.',
        'image'     => '',
        'book_url'  => the_cochin_get_booking_url(),
        'order_url' => home_url('/menu/'),
    ), $atts, 'the_cochin_delivery');

    ob_start();
    get_template_part('template-parts/home-delivery', null, $atts);
    return ob_get_clean();
}
add_shortcode('the_cochin_delivery', 'the_cochin_delivery_shortcode');

/**
 * 6. Dietary Choices Shortcode: [the_cochin_dietary]
 */
function the_cochin_dietary_shortcode($atts) {
    $atts = shortcode_atts(array(
        'badge'     => 'Food choice',
        'title'     => 'Vegetarian and vegan choices',
        'desc'      => 'Kerala cuisine offers a wonderful variety of naturally vegetarian and plant-based dishes. Look for the dietary symbols on our menu or speak to our team for guidance.',
        'bg_image'  => '',
        'book_url'  => the_cochin_get_booking_url(),
        'order_url' => home_url('/menu/'),
    ), $atts, 'the_cochin_dietary');

    ob_start();
    get_template_part('template-parts/home-dietary', null, $atts);
    return ob_get_clean();
}
add_shortcode('the_cochin_dietary', 'the_cochin_dietary_shortcode');

/**
 * 7. FAQ Accordion Shortcode: [the_cochin_faq]
 */
function the_cochin_faq_shortcode($atts) {
    $atts = shortcode_atts(array(
        'badge' => 'FAQ',
        'title' => 'Frequently Asked Questions',
    ), $atts, 'the_cochin_faq');

    ob_start();
    get_template_part('template-parts/home-faq', null, $atts);
    return ob_get_clean();
}
add_shortcode('the_cochin_faq', 'the_cochin_faq_shortcode');

/**
 * Register Gutenberg Block Pattern Category
 */
function the_cochin_register_block_patterns() {
    if (function_exists('register_block_pattern_category')) {
        register_block_pattern_category(
            'the-cochin',
            array('label' => __('The Cochin Sections', 'astra-child'))
        );
    }
}
add_action('init', 'the_cochin_register_block_patterns');
