<?php
/**
 * The Cochin (Astra Child Theme) Functions
 *
 * Implements custom responsive header based on Docs/html/menu.html,
 * Hero section with Amaranth heading & Poppins typography,
 * About Us section with Architects Daughter font & #F5EFE3 palette,
 * and Interactive Menu Carousel with auto-scrolling cards.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Enqueue Parent and Child Theme Styles, Google Fonts, and Scripts
 */
function the_cochin_enqueue_scripts() {
    // Parent Theme Style
    wp_enqueue_style(
        'astra-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        ASTRA_THEME_VERSION
    );

    // Google Fonts: Amaranth, Architects Daughter, and Poppins
    wp_enqueue_style(
        'the-cochin-google-fonts',
        'https://fonts.googleapis.com/css2?family=Amaranth:ital,wght@0,400;0,700;1,400;1,700&family=Architects+Daughter&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap',
        array(),
        null
    );

    // Child Theme Main Style
    wp_enqueue_style(
        'astra-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('astra-parent-style', 'the-cochin-google-fonts'),
        wp_get_theme()->get('Version') . '.' . filemtime(get_stylesheet_directory() . '/style.css')
    );

    // Header JavaScript (Responsive toggle, sticky scroll, keyboard accessibility)
    wp_enqueue_script(
        'the-cochin-header-script',
        get_stylesheet_directory_uri() . '/assets/js/header.js',
        array(),
        wp_get_theme()->get('Version') . '.' . filemtime(get_stylesheet_directory() . '/assets/js/header.js'),
        true
    );

    // Menu Carousel Script
    wp_enqueue_script(
        'the-cochin-carousel-script',
        get_stylesheet_directory_uri() . '/assets/js/menu-carousel.js',
        array(),
        wp_get_theme()->get('Version') . '.' . filemtime(get_stylesheet_directory() . '/assets/js/menu-carousel.js'),
        true
    );

    // FAQ Accordion Script
    wp_enqueue_script(
        'the-cochin-faq-script',
        get_stylesheet_directory_uri() . '/assets/js/faq-accordion.js',
        array(),
        wp_get_theme()->get('Version') . '.' . (file_exists(get_stylesheet_directory() . '/assets/js/faq-accordion.js') ? filemtime(get_stylesheet_directory() . '/assets/js/faq-accordion.js') : '1.0'),
        true
    );

    // About Us Page Tabs Script
    if (is_page('about') || is_page('about-us') || is_page_template('page-about.php')) {
        wp_enqueue_script(
            'the-cochin-about-tabs-script',
            get_stylesheet_directory_uri() . '/assets/js/about-tabs.js',
            array(),
            wp_get_theme()->get('Version') . '.' . (file_exists(get_stylesheet_directory() . '/assets/js/about-tabs.js') ? filemtime(get_stylesheet_directory() . '/assets/js/about-tabs.js') : '1.0'),
            true
        );
    }

    // Table Booking Page Script
    if (is_page('book-table') || is_page('book-a-table') || is_page_template('page-book-table.php')) {
        wp_enqueue_script(
            'the-cochin-booking-script',
            get_stylesheet_directory_uri() . '/assets/js/book-table.js',
            array('jquery'),
            wp_get_theme()->get('Version') . '.' . (file_exists(get_stylesheet_directory() . '/assets/js/book-table.js') ? filemtime(get_stylesheet_directory() . '/assets/js/book-table.js') : '1.0'),
            true
        );

        wp_localize_script('the-cochin-booking-script', 'cochinBookingData', array(
            'restUrl'         => esc_url_raw(rest_url('rb/v1/')),
            'nonce'           => wp_create_nonce('wp_rest'),
            'restaurantName'  => get_option('rb_restaurant_name', 'The Cochin Indian Restaurant'),
            'restaurantPhone' => get_option('rb_restaurant_phone', '01442 233777'),
            'allergenNotice'  => get_option('rb_allergen_notice', 'If you or any member of your party suffer from a food allergy or dietary intolerance, please speak to a member of our team directly by phone before booking.'),
            'manageUrl'       => home_url('/book-table/?view=manage'),
            'initialToken'    => isset($_GET['rb_token']) ? sanitize_text_field(wp_unslash($_GET['rb_token'])) : (isset($_GET['ref']) ? sanitize_text_field(wp_unslash($_GET['ref'])) : ''),
            'initialView'     => isset($_GET['view']) ? sanitize_text_field(wp_unslash($_GET['view'])) : '',
        ));
    }

    // Contact Page Script
    if (is_page('contact') || is_page('contact-us') || is_page_template('page-contact.php')) {
        wp_enqueue_script(
            'the-cochin-contact-script',
            get_stylesheet_directory_uri() . '/assets/js/contact-form.js',
            array('jquery'),
            wp_get_theme()->get('Version') . '.' . (file_exists(get_stylesheet_directory() . '/assets/js/contact-form.js') ? filemtime(get_stylesheet_directory() . '/assets/js/contact-form.js') : '1.0'),
            true
        );

        wp_localize_script('the-cochin-contact-script', 'cochinContactData', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('the_cochin_contact_nonce'),
        ));
    }
}
add_action('wp_enqueue_scripts', 'the_cochin_enqueue_scripts', 15);

/**
 * AJAX Handler for Contact Form Submission
 */
function the_cochin_ajax_submit_contact_form() {
    if (!isset($_POST['contact_nonce']) || !wp_verify_nonce($_POST['contact_nonce'], 'the_cochin_contact_nonce')) {
        wp_send_json_error(array('message' => __('Security verification failed. Please refresh the page and try again.', 'astra-child')));
    }

    $fullname = isset($_POST['fullname']) ? sanitize_text_field(wp_unslash($_POST['fullname'])) : '';
    $phone    = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    $email    = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $subject  = isset($_POST['subject']) ? sanitize_text_field(wp_unslash($_POST['subject'])) : 'General Inquiry';
    $message  = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';

    if (empty($fullname) || empty($phone) || empty($email) || empty($message)) {
        wp_send_json_error(array('message' => __('Please complete all required fields.', 'astra-child')));
    }

    if (!is_email($email)) {
        wp_send_json_error(array('message' => __('Please provide a valid email address.', 'astra-child')));
    }

    // Send email notification to restaurant
    $to = get_option('admin_email', 'thecochin@gmail.com');
    $email_subject = sprintf('[The Cochin Inquiry] %s - From %s', $subject, $fullname);
    $headers = array(
        'Content-Type: text/html; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <no-reply@' . (isset($_SERVER['HTTP_HOST']) ? sanitize_text_field($_SERVER['HTTP_HOST']) : 'thecochin.co.uk') . '>',
        'Reply-To: ' . $fullname . ' <' . $email . '>',
    );

    $body = '<h2>New Inquiry Received on The Cochin Website</h2>';
    $body .= '<p><strong>Guest Name:</strong> ' . esc_html($fullname) . '</p>';
    $body .= '<p><strong>Email:</strong> ' . esc_html($email) . '</p>';
    $body .= '<p><strong>Phone:</strong> ' . esc_html($phone) . '</p>';
    $body .= '<p><strong>Category:</strong> ' . esc_html($subject) . '</p>';
    $body .= '<p><strong>Message:</strong></p>';
    $body .= '<blockquote>' . nl2br(esc_html($message)) . '</blockquote>';
    $body .= '<p><em>Sent on ' . date('d/m/Y H:i:s') . '</em></p>';

    @wp_mail($to, $email_subject, $body, $headers);

    wp_send_json_success(array(
        'message' => __('Thank you for contacting The Cochin! We have received your inquiry and our team will get back to you shortly.', 'astra-child'),
    ));
}
add_action('wp_ajax_the_cochin_submit_contact_form', 'the_cochin_ajax_submit_contact_form');
add_action('wp_ajax_nopriv_the_cochin_submit_contact_form', 'the_cochin_ajax_submit_contact_form');

/**
 * Register Primary Menu & Theme Supports
 */
function the_cochin_theme_setup() {
    register_nav_menus(array(
        'primary' => __('Primary Navigation Menu', 'astra-child'),
    ));

    add_theme_support('custom-logo', array(
        'height'      => 120,
        'width'       => 380,
        'flex-width'  => true,
        'flex-height' => true,
    ));
}
add_action('after_setup_theme', 'the_cochin_theme_setup', 20);

/**
 * Force Astra to use full-width / page-builder stretched layout on pages
 */
function the_cochin_force_fullwidth_layout($layout) {
    return 'page-builder';
}
add_filter('astra_get_content_layout', 'the_cochin_force_fullwidth_layout', 99);

function the_cochin_force_no_sidebar($layout) {
    return 'no-sidebar';
}
add_filter('astra_page_layout', 'the_cochin_force_no_sidebar', 99);

/**
 * Remove Astra's default header hooks and replace with custom Cochin header
 */
function the_cochin_override_astra_header() {
    remove_action('astra_header', 'astra_header_markup');

    if (class_exists('Astra_Builder_Header')) {
        $builder = Astra_Builder_Header::get_instance();
        remove_action('astra_header', array($builder, 'header_builder_markup'));
        remove_action('astra_header', array($builder, 'global_astra_header'), 0);
    }

    if (class_exists('Astra_Mobile_Header')) {
        $mobile_header = Astra_Mobile_Header::get_instance();
        remove_action('astra_header', array($mobile_header, 'mobile_header_markup'), 5);
    }

    // Attach custom header
    add_action('astra_header', 'the_cochin_render_custom_header', 10);
}
add_action('wp', 'the_cochin_override_astra_header', 1);

/**
 * Render the Custom Cochin Footer
 */
function the_cochin_render_custom_footer() {
    get_template_part('template-parts/footer-custom');
}

/**
 * Remove Astra's default footer hooks and replace with custom Cochin footer on all pages
 */
function the_cochin_override_astra_footer() {
    remove_action('astra_footer', 'astra_footer_markup');

    if (class_exists('Astra_Builder_Footer')) {
        $builder = Astra_Builder_Footer::get_instance();
        remove_action('astra_footer', array($builder, 'footer_markup'));
    }

    // Attach custom footer
    add_action('astra_footer', 'the_cochin_render_custom_footer', 10);
}
add_action('wp', 'the_cochin_override_astra_footer', 1);

/**
 * Retrieve the booking URL
 */
function the_cochin_get_booking_url() {
    $booking_page = get_page_by_path('book-table');
    if (!$booking_page) {
        $booking_page = get_page_by_path('book-a-table');
    }
    if (!$booking_page) {
        $booking_page = get_page_by_path('booking');
    }
    if (!$booking_page) {
        $booking_page = get_page_by_path('reservations');
    }

    if ($booking_page) {
        return get_permalink($booking_page);
    }

    return home_url('/book-table/');
}

/**
 * Retrieve the Menu Page URL
 */
function the_cochin_get_menu_page_url() {
    $menu_page = get_page_by_path('menu');
    if ($menu_page) {
        return get_permalink($menu_page);
    }
    return home_url('/menu/');
}

/**
 * Menu Carousel Items Data (Configurable & Filterable)
 */
function the_cochin_get_menu_carousel_items() {
    $img_base = get_stylesheet_directory_uri() . '/assets/images/';
    $menu_url = the_cochin_get_menu_page_url();

    $items = array(
        array(
            'title' => 'STARTERS',
            'count' => '18 Items Available',
            'image' => $img_base . 'uzhunnu-vada.png',
            'url'   => $menu_url . '#starters',
        ),
        array(
            'title' => 'DOSA & SOUTH INDIAN CLASSICS',
            'count' => '03 Items Available',
            'image' => $img_base . 'dosa.png',
            'url'   => $menu_url . '#dosa-south-indian-classics',
        ),
        array(
            'title' => 'SEAFOOD',
            'count' => '07 Items Available',
            'image' => $img_base . 'chemmen.png',
            'url'   => $menu_url . '#seafood',
        ),
        array(
            'title' => 'MEAT & POULTRY',
            'count' => '19 Items Available',
            'image' => $img_base . 'chick.png',
            'url'   => $menu_url . '#meat-poultry',
        ),
        array(
            'title' => 'VEGETARIAN & VEGAN',
            'count' => '12 Items Available',
            'image' => $img_base . 'appam.png',
            'url'   => $menu_url . '#vegetarian-vegan',
        ),
        array(
            'title' => 'RICE & BIRYANI',
            'count' => '08 Items Available',
            'image' => $img_base . 'jyt.png',
            'url'   => $menu_url . '#rice-biryani',
        ),
        array(
            'title' => 'BREADS & SIDES',
            'count' => '10 Items Available',
            'image' => $img_base . 'Dosa-Recipe-Step-By-Step-Instructions-scaled.jpg.webp',
            'url'   => $menu_url . '#breads-sides',
        ),
        array(
            'title' => 'DESSERTS',
            'count' => '06 Items Available',
            'image' => $img_base . 'manasa.png',
            'url'   => $menu_url . '#desserts',
        ),
        array(
            'title' => 'DRINKS',
            'count' => '14 Items Available',
            'image' => $img_base . 'delivery.png',
            'url'   => $menu_url . '#drinks',
        ),
    );

    return apply_filters('the_cochin_menu_carousel_items', $items);
}

/**
 * Custom Nav Walker for clean .nav-link classes and ARIA attributes
 */
class The_Cochin_Nav_Walker extends Walker_Nav_Menu {
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        if (strcasecmp(trim($item->title), 'Book a table') === 0 || strcasecmp(trim($item->title), 'Book Table') === 0) {
            return;
        }

        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $is_current = in_array('current-menu-item', $classes) || in_array('current_page_item', $classes);

        $output .= '<li class="' . esc_attr(implode(' ', $classes)) . '">';

        $attributes  = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_url($item->url) . '"' : '';
        $attributes .= ' class="nav-link"';

        if ($is_current) {
            $attributes .= ' aria-current="page"';
        }

        $title = apply_filters('the_title', $item->title, $item->ID);

        $item_output  = $args->before ?? '';
        $item_output .= '<a' . $attributes . '>';
        $item_output .= ($args->link_before ?? '') . $title . ($args->link_after ?? '');
        $item_output .= '</a>';
        $item_output .= $args->after ?? '';

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        if (strcasecmp(trim($item->title), 'Book a table') === 0 || strcasecmp(trim($item->title), 'Book Table') === 0) {
            return;
        }
        $output .= "</li>\n";
    }
}

/**
 * Render Navigation Links
 */
function the_cochin_render_nav_menu() {
    if (has_nav_menu('primary')) {
        wp_nav_menu(array(
            'theme_location' => 'primary',
            'container'      => false,
            'menu_class'     => 'nav-list',
            'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
            'walker'         => new The_Cochin_Nav_Walker(),
            'fallback_cb'    => 'the_cochin_fallback_nav_menu',
        ));
    } else {
        the_cochin_fallback_nav_menu();
    }
}

/**
 * Fallback Navigation Menu
 */
function the_cochin_fallback_nav_menu() {
    $home_url = home_url('/');
    $is_home = is_front_page() || is_home();
    ?>
    <ul class="nav-list">
        <li><a class="nav-link" href="<?php echo esc_url($home_url); ?>" <?php echo $is_home ? 'aria-current="page"' : ''; ?>>Home</a></li>
        <li><a class="nav-link" href="<?php echo esc_url($home_url . '#menu'); ?>">Menu</a></li>
        <li><a class="nav-link" href="<?php echo esc_url($home_url . '#about'); ?>">About</a></li>
        <li><a class="nav-link" href="<?php echo esc_url($home_url . '#banquets'); ?>">Banquets</a></li>
        <li><a class="nav-link" href="<?php echo esc_url($home_url . '#contact'); ?>">Contact</a></li>
    </ul>
    <?php
}

/**
 * Render the Custom Cochin Header from Docs/html/menu.html
 */
function the_cochin_render_custom_header() {
    $custom_logo_id = get_theme_mod('custom_logo');
    $logo_url = '';

    if ($custom_logo_id) {
        $logo_data = wp_get_attachment_image_src($custom_logo_id, 'full');
        if (!empty($logo_data[0])) {
            $logo_url = $logo_data[0];
        }
    }

    if (empty($logo_url)) {
        if (file_exists(get_stylesheet_directory() . '/assets/images/the-cochin-logo.png')) {
            $logo_url = get_stylesheet_directory_uri() . '/assets/images/the-cochin-logo.png';
        } elseif (file_exists(get_stylesheet_directory() . '/assets/images/the-cochin-logo-white.png')) {
            $logo_url = get_stylesheet_directory_uri() . '/assets/images/the-cochin-logo-white.png';
        }
    }

    $site_name = get_bloginfo('name', 'display');
    if (empty($site_name)) {
        $site_name = 'The Cochin';
    }
    ?>
    <header class="site-header" id="siteHeader">
        <div class="header-inner">
            <a class="brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr($site_name); ?> — Home">
                <?php if (!empty($logo_url)) : ?>
                    <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($site_name); ?>">
                <?php else : ?>
                    <span class="brand-text"><?php echo esc_html($site_name); ?></span>
                <?php endif; ?>
            </a>

            <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="primaryNavigation" aria-label="Open menu">
                <span></span>
            </button>

            <nav class="nav-wrap" id="primaryNavigation" aria-label="Primary navigation">
                <?php the_cochin_render_nav_menu(); ?>
                <a class="book-button" href="<?php echo esc_url(the_cochin_get_booking_url()); ?>">Book a table</a>
                <?php the_cochin_render_header_cart_button(); ?>
            </nav>
        </div>
    </header>
    <?php
}

/**
 * Render WooCommerce Header Cart Button
 */
function the_cochin_render_header_cart_button() {
    if (!class_exists('WooCommerce')) {
        return;
    }
    $cart_count = (WC()->cart) ? WC()->cart->get_cart_contents_count() : 0;
    $cart_total = (WC()->cart) ? WC()->cart->get_cart_subtotal() : '£0.00';
    $cart_url   = wc_get_cart_url();
    ?>
    <a class="cochin-header-cart-btn" href="<?php echo esc_url($cart_url); ?>" title="<?php esc_attr_e('View your shopping cart', 'astra-child'); ?>">
        <span class="cochin-cart-icon-wrap" aria-hidden="true">
            <svg class="cochin-cart-svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            <span class="cochin-cart-badge"><?php echo esc_html($cart_count); ?></span>
        </span>
        <span class="cochin-cart-subtotal"><?php echo wp_kses_post($cart_total); ?></span>
    </a>
    <?php
}

/**
 * Ensure header cart button updates via WooCommerce AJAX fragments
 */
function the_cochin_header_cart_fragments($fragments) {
    ob_start();
    the_cochin_render_header_cart_button();
    $fragments['a.cochin-header-cart-btn'] = ob_get_clean();
    return $fragments;
}
add_filter('woocommerce_add_to_cart_fragments', 'the_cochin_header_cart_fragments');

/**
 * Banquet Meals Section Data (Configurable & Filterable)
 */
function the_cochin_get_banquet_data() {
    $img_base = get_stylesheet_directory_uri() . '/assets/images/';
    $data = array(
        'title'        => get_theme_mod('the_cochin_banquet_title', 'Banquet Meals'),
        'days'         => get_theme_mod('the_cochin_banquet_days', 'EVERY TUESDAY • THURSDAY • WEDNESDAY'),
        'time'         => get_theme_mod('the_cochin_banquet_time', '6PM – 11 PM'),
        'desc'         => get_theme_mod('the_cochin_banquet_desc', '1 Starter + 1 Main Dish + Side Dish + Rice & Bread'),
        'veg_price'    => get_theme_mod('the_cochin_banquet_veg_price', '£ 15.95'),
        'nonveg_price' => get_theme_mod('the_cochin_banquet_nonveg_price', '£ 17.95'),
        'seafood_price'=> get_theme_mod('the_cochin_banquet_seafood_price', '£ 19.95'),
        'bg_image'     => $img_base . 'mask-group.jpg',
        'dish_image'   => $img_base . 'manasa.png',
        'book_url'     => the_cochin_get_booking_url(),
        'order_url'    => the_cochin_get_menu_page_url(),
    );
    return apply_filters('the_cochin_banquet_data', $data);
}

/**
 * Delivery & Collection Section Data (Configurable & Filterable)
 */
function the_cochin_get_delivery_data() {
    $img_base = get_stylesheet_directory_uri() . '/assets/images/';
    $data = array(
        'badge'     => get_theme_mod('the_cochin_delivery_badge', 'Our Menu'),
        'title'     => get_theme_mod('the_cochin_delivery_title', 'Book, Collect or order for<br>delivery'),
        'desc'      => get_theme_mod('the_cochin_delivery_desc', 'Dining with us? Reserve your table online. Prefer to enjoy The Cochin at home? Order for collection or local delivery through our secure online ordering service.'),
        'image'     => $img_base . 'deliver.png',
        'book_url'  => the_cochin_get_booking_url(),
        'order_url' => the_cochin_get_menu_page_url(),
    );
    return apply_filters('the_cochin_delivery_data', $data);
}

/**
 * Vegetarian & Vegan Choices Section Data (Configurable & Filterable)
 */
function the_cochin_get_dietary_data() {
    $img_base = get_stylesheet_directory_uri() . '/assets/images/';
    $data = array(
        'badge'     => get_theme_mod('the_cochin_dietary_badge', 'Food choice'),
        'title'     => get_theme_mod('the_cochin_dietary_title', 'Vegetarian and vegan choices'),
        'desc'      => get_theme_mod('the_cochin_dietary_desc', 'Kerala cuisine offers a wonderful variety of naturally vegetarian and plant-based dishes. Look for the dietary symbols on our menu or speak to our team for guidance.'),
        'bg_image'  => $img_base . 'bg.jpg',
        'book_url'  => the_cochin_get_booking_url(),
        'order_url' => the_cochin_get_menu_page_url(),
    );
    return apply_filters('the_cochin_dietary_data', $data);
}

/**
 * FAQ Section Data (Configurable & Filterable)
 */
function the_cochin_get_faq_data() {
    $img_base = get_stylesheet_directory_uri() . '/assets/images/';
    $data = array(
        'badge'     => get_theme_mod('the_cochin_faq_badge', 'FAQ'),
        'title'     => get_theme_mod('the_cochin_faq_title', 'Frequently Asked Questions'),
        'bottom_bg' => $img_base . 'bottombg.jpg',
        'items'     => array(
            array(
                'question' => 'Do you offer vegetarian and vegan dishes?',
                'answer'   => 'Yes. Our menu includes a range of vegetarian and vegan choices. Please check the current menu or speak to our team for guidance.',
            ),
            array(
                'question' => 'Can you accommodate food allergies?',
                'answer'   => 'Please tell us about any allergy before ordering. We handle multiple allergens in our kitchen, so cross-contact may occur. Our team can provide the current allergen information and help you make an informed choice.',
            ),
            array(
                'question' => 'Do you offer takeaway and delivery?',
                'answer'   => 'Yes. You can order online for collection or local delivery. Availability, delivery areas and estimated times are shown during checkout.',
            ),
            array(
                'question' => 'Do I need to book banquet meals in advance?',
                'answer'   => '[CONFIRM BOOKING POLICY AND NOTICE PERIOD BEFORE PUBLISHING]',
            ),
            array(
                'question' => 'Can you cater for groups or private events?',
                'answer'   => 'Yes, subject to availability. Contact us with the date, guest numbers and requirements, and our team will discuss the options with you.',
            ),
            array(
                'question' => 'Is parking available?',
                'answer'   => '[ADD ACCURATE LOCAL PARKING OR PUBLIC-TRANSPORT INFORMATION]',
            ),
        ),
    );
    return apply_filters('the_cochin_faq_data', $data);
}

// Include Shortcodes & Block Patterns
require_once get_stylesheet_directory() . '/inc/shortcodes.php';

// Include ACF Fields Configuration
require_once get_stylesheet_directory() . '/inc/acf-fields.php';



/**
 * Render About Us / Inner Page Header Banner under the site navigation header
 */
function the_cochin_render_page_header_banner() {
    if (is_page('about') || is_page('about-us') || is_page_template('page-about.php')) {
        get_template_part('template-parts/about-header', null, array(
            'title'      => 'About Us',
            'breadcrumb' => 'About us',
        ));
    } elseif (is_page('menu') || is_page_template('page-menu.php')) {
        get_template_part('template-parts/about-header', null, array(
            'title'      => 'Menu',
            'breadcrumb' => 'Menu',
        ));
    } elseif (is_page('banquets') || is_page('banquet-meals') || is_page_template('page-banquets.php')) {
        get_template_part('template-parts/about-header', null, array(
            'title'      => 'Banquets',
            'breadcrumb' => 'Banquets',
        ));
    } elseif (is_page('book-table') || is_page('book-a-table')) {
        get_template_part('template-parts/about-header', null, array(
            'title'      => 'Book a Table',
            'breadcrumb' => 'Book a Table',
        ));
    } elseif (function_exists('is_product') && is_product()) {
        get_template_part('template-parts/about-header', null, array(
            'title'      => get_the_title(),
            'breadcrumb' => 'Menu / ' . get_the_title(),
        ));
    } elseif (function_exists('is_cart') && is_cart()) {
        get_template_part('template-parts/about-header', null, array(
            'title'      => 'Your Cart',
            'breadcrumb' => 'Cart',
        ));
    } elseif (function_exists('is_checkout') && is_checkout()) {
        get_template_part('template-parts/about-header', null, array(
            'title'      => 'Checkout',
            'breadcrumb' => 'Checkout',
        ));
    }
}
add_action('astra_header_after', 'the_cochin_render_page_header_banner', 15);

/**
 * Shortcode for Header Banner: [the_cochin_page_header title="About Us" breadcrumb="About us"]
 */
function the_cochin_page_header_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title'      => 'About Us',
        'breadcrumb' => 'About us',
    ), $atts, 'the_cochin_page_header');

    ob_start();
    get_template_part('template-parts/about-header', null, array(
        'title'      => $atts['title'],
        'breadcrumb' => $atts['breadcrumb'],
    ));
    return ob_get_clean();
}
add_shortcode('the_cochin_page_header', 'the_cochin_page_header_shortcode');
add_shortcode('about_us_header', 'the_cochin_page_header_shortcode');


/**
 * Add WordPress Customizer Controls
 */
function the_cochin_customize_register($wp_customize) {
    // Hero Section Panel
    $wp_customize->add_section('the_cochin_hero_section', array(
        'title'    => __('Hero Section (Homepage)', 'astra-child'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('the_cochin_hero_bg', array(
        'default'           => get_stylesheet_directory_uri() . '/assets/images/kerala-cuisine-hero.png',
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'the_cochin_hero_bg', array(
        'label'    => __('Hero Background Image', 'astra-child'),
        'section'  => 'the_cochin_hero_section',
        'settings' => 'the_cochin_hero_bg',
    )));

    $wp_customize->add_setting('the_cochin_hero_title', array(
        'default'           => 'Authentic Kerala Cuisine in Hemel Hempstead\'s Old Town',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('the_cochin_hero_title', array(
        'label'    => __('Hero Title (Amaranth Font)', 'astra-child'),
        'section'  => 'the_cochin_hero_section',
        'type'     => 'text',
    ));

    $wp_customize->add_setting('the_cochin_hero_desc', array(
        'default'           => 'Proudly serving the local community since 2003. Discover the distinctive flavours of Kerala, freshly prepared and served with the warmth of traditional South Indian hospitality.',
        'sanitize_callback' => 'sanitize_textarea_field',
    ));
    $wp_customize->add_control('the_cochin_hero_desc', array(
        'label'    => __('Hero Description (Poppins Font)', 'astra-child'),
        'section'  => 'the_cochin_hero_section',
        'type'     => 'textarea',
    ));

    $wp_customize->add_setting('the_cochin_btn1_text', array(
        'default'           => 'BOOK A TABLE',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('the_cochin_btn1_text', array(
        'label'    => __('Button 1 Text (#6B1F2A)', 'astra-child'),
        'section'  => 'the_cochin_hero_section',
    ));
    $wp_customize->add_setting('the_cochin_btn1_url', array(
        'default'           => the_cochin_get_booking_url(),
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('the_cochin_btn1_url', array(
        'label'    => __('Button 1 URL', 'astra-child'),
        'section'  => 'the_cochin_hero_section',
    ));

    $wp_customize->add_setting('the_cochin_btn2_text', array(
        'default'           => 'ORDER ONLINE',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('the_cochin_btn2_text', array(
        'label'    => __('Button 2 Text (#FF8B26)', 'astra-child'),
        'section'  => 'the_cochin_hero_section',
    ));
    $wp_customize->add_setting('the_cochin_btn2_url', array(
        'default'           => home_url('/#order'),
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('the_cochin_btn2_url', array(
        'label'    => __('Button 2 URL', 'astra-child'),
        'section'  => 'the_cochin_hero_section',
    ));

    $wp_customize->add_setting('the_cochin_btn3_text', array(
        'default'           => 'VIEW MENU',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    $wp_customize->add_control('the_cochin_btn3_text', array(
        'label'    => __('Button 3 Text (#C9A24D)', 'astra-child'),
        'section'  => 'the_cochin_hero_section',
    ));
    $wp_customize->add_setting('the_cochin_btn3_url', array(
        'default'           => home_url('/#menu'),
        'sanitize_callback' => 'esc_url_raw',
    ));
    $wp_customize->add_control('the_cochin_btn3_url', array(
        'label'    => __('Button 3 URL', 'astra-child'),
        'section'  => 'the_cochin_hero_section',
    ));
}
add_action('customize_register', 'the_cochin_customize_register');

/**
 * Handle Banquet & Event Form Submissions via AJAX
 */
function the_cochin_handle_banquet_inquiry() {
    check_ajax_referer('the_cochin_banquet_nonce', 'security');

    $name         = sanitize_text_field($_POST['name'] ?? '');
    $email        = sanitize_email($_POST['email'] ?? '');
    $phone        = sanitize_text_field($_POST['phone'] ?? '');
    $event_date   = sanitize_text_field($_POST['event_date'] ?? '');
    $event_time   = sanitize_text_field($_POST['event_time'] ?? '');
    $guests       = intval($_POST['guests'] ?? 0);
    $feast_type   = sanitize_text_field($_POST['feast_type'] ?? 'Vegetarian Feast');
    $notes        = sanitize_textarea_field($_POST['notes'] ?? '');

    if (empty($name) || empty($email) || empty($phone) || empty($event_date) || empty($guests)) {
        wp_send_json_error(array('message' => 'Please fill in all required fields.'));
    }

    // Prepare Email Content
    $admin_email = get_option('admin_email', 'info@thecochin.uk');
    $subject = "New Banquet Feast Inquiry from {$name} - The Cochin";
    $body = "New Banquet Meal & Group Inquiry Received:\n\n"
          . "Name: {$name}\n"
          . "Email: {$email}\n"
          . "Phone: {$phone}\n"
          . "Date: {$event_date}\n"
          . "Time: {$event_time}\n"
          . "Party Size: {$guests} Guests\n"
          . "Feast Type: {$feast_type}\n"
          . "Special Requests / Dietary Notes:\n" . ($notes ? $notes : "None") . "\n\n"
          . "Submitted on: " . current_time('mysql');

    $headers = array('Content-Type: text/plain; charset=UTF-8', "Reply-To: {$name} <{$email}>");

    // Send email via WordPress wp_mail (handled by FluentSMTP)
    @wp_mail($admin_email, $subject, $body, $headers);

    // Also send polite confirmation to guest
    $guest_subject = "We received your Banquet Inquiry - The Cochin Indian Restaurant";
    $guest_body = "Dear {$name},\n\n"
                . "Thank you for your interest in our Banquet Meals at The Cochin Indian Restaurant.\n\n"
                . "We have received your inquiry for a party of {$guests} guests on {$event_date} ({$event_time}).\n"
                . "Selected Feast: {$feast_type}\n\n"
                . "Our manager will review our table arrangements and get in touch with you shortly to confirm the details.\n\n"
                . "Warm regards,\n"
                . "The Cochin Indian Restaurant\n"
                . "180 Cowley Road, Oxford / Hemel Hempstead\n"
                . "01442 256111";

    @wp_mail($email, $guest_subject, $guest_body, array('Content-Type: text/plain; charset=UTF-8'));

    wp_send_json_success(array(
        'message' => 'Thank you! Your banquet inquiry has been received. Our team will contact you shortly to confirm your booking.'
    ));
}
add_action('wp_ajax_the_cochin_banquet_inquiry', 'the_cochin_handle_banquet_inquiry');
add_action('wp_ajax_nopriv_the_cochin_banquet_inquiry', 'the_cochin_handle_banquet_inquiry');

/**
 * Handle Table Reservation Submissions via AJAX
 */
function the_cochin_handle_table_reservation() {
    check_ajax_referer('the_cochin_reservation_nonce', 'security');

    $full_name            = sanitize_text_field($_POST['full_name'] ?? '');
    $phone                = sanitize_text_field($_POST['telephone'] ?? '');
    $email                = sanitize_email($_POST['email'] ?? '');
    $guests               = max(1, intval($_POST['guests'] ?? 2));
    $booking_date         = sanitize_text_field($_POST['booking_date'] ?? '');
    $booking_time         = sanitize_text_field($_POST['booking_time'] ?? '');
    $occasion             = sanitize_text_field($_POST['occasion'] ?? 'Casual Dining');
    $special_requirements = sanitize_textarea_field($_POST['special_requirements'] ?? '');

    if (empty($full_name) || empty($phone) || empty($email) || empty($booking_date) || empty($booking_time)) {
        wp_send_json_error(array('message' => 'Please fill in all required fields.'));
    }

    if (!is_email($email)) {
        wp_send_json_error(array('message' => 'Please enter a valid email address.'));
    }

    $combined_notes = "Occasion: {$occasion}";
    if (!empty($special_requirements)) {
        $combined_notes .= "\nSpecial Requirements / Accessibility: {$special_requirements}";
    }

    // If Restaurant Booking plugin is loaded, integrate directly
    $reference = 'COCHIN-' . strtoupper(wp_generate_password(6, false, false));
    if (class_exists('RB_Reservations')) {
        $formatted_time = date('H:i:s', strtotime($booking_time));
        $res = RB_Reservations::create_reservation(array(
            'customer_name'    => $full_name,
            'phone'            => $phone,
            'email'            => $email,
            'party_size'       => $guests,
            'booking_date'     => $booking_date,
            'start_time'       => $formatted_time,
            'special_requests' => $combined_notes,
            'source'           => 'web_redesign',
        ));
        if (!is_wp_error($res) && is_array($res) && !empty($res['booking_reference'])) {
            $reference = $res['booking_reference'];
        }
    }

    // Send Admin Notification Email
    $admin_email = get_option('admin_email', 'info@thecochin.uk');
    $subject = "New Table Reservation Request (#{$reference}) - The Cochin";
    $body = "New Table Reservation Request Details:\n\n"
          . "Reference: #{$reference}\n"
          . "Full Name: {$full_name}\n"
          . "Telephone: {$phone}\n"
          . "Email: {$email}\n"
          . "Number of Guests: {$guests}\n"
          . "Date: {$booking_date}\n"
          . "Time: {$booking_time}\n"
          . "Occasion: {$occasion}\n"
          . "Accessibility / Special Requirements:\n" . ($special_requirements ? $special_requirements : "None") . "\n\n"
          . "Submitted on: " . current_time('mysql');

    $headers = array('Content-Type: text/plain; charset=UTF-8', "Reply-To: {$full_name} <{$email}>");
    @wp_mail($admin_email, $subject, $body, $headers);

    // Send Customer Confirmation & Notice Email
    $guest_subject = "Your Table Reservation Request (#{$reference}) - The Cochin";
    $guest_body = "Dear {$full_name},\n\n"
                . "Thank you for reserving a table with The Cochin Indian Restaurant.\n\n"
                . "RESERVATION SUMMARY:\n"
                . "----------------------------------------\n"
                . "Reference: #{$reference}\n"
                . "Date: {$booking_date}\n"
                . "Time: {$booking_time}\n"
                . "Party Size: {$guests} Guest(s)\n"
                . "Occasion: {$occasion}\n"
                . "----------------------------------------\n\n"
                . "IMPORTANT NOTICE:\n"
                . "Your booking is not confirmed until you receive confirmation from The Cochin. For same-day bookings or groups of 6 or more, please call us on 01442 233777.\n\n"
                . "We look forward to welcoming you soon!\n\n"
                . "Warm regards,\n"
                . "The Cochin Indian Restaurant\n"
                . "180 Cowley Road, Oxford / Hemel Hempstead\n"
                . "Telephone: 01442 233777";

    @wp_mail($email, $guest_subject, $guest_body, array('Content-Type: text/plain; charset=UTF-8'));

    wp_send_json_success(array(
        'message' => "Your reservation request (#{$reference}) has been received for {$guests} guest(s) on {$booking_date} at {$booking_time}. Please note your booking is not confirmed until you receive confirmation from The Cochin. For same-day bookings or groups of 6+, please call 01442 233777.",
        'reference' => $reference
    ));
}
add_action('wp_ajax_the_cochin_submit_reservation', 'the_cochin_handle_table_reservation');
add_action('wp_ajax_nopriv_the_cochin_submit_reservation', 'the_cochin_handle_table_reservation');


