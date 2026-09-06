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
}
add_action('wp_enqueue_scripts', 'the_cochin_enqueue_scripts', 15);

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
 * Retrieve the booking URL
 */
function the_cochin_get_booking_url() {
    $booking_page = get_page_by_path('book-a-table');
    if (!$booking_page) {
        $booking_page = get_page_by_path('booking');
    }
    if (!$booking_page) {
        $booking_page = get_page_by_path('reservations');
    }

    if ($booking_page) {
        return get_permalink($booking_page);
    }

    return home_url('/#booking');
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
            </nav>
        </div>
    </header>
    <?php
}

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
 * Render Homepage Sections (Hero, About Us, Menu Carousel, Banquet Meals, Delivery, Dietary) under the header
 */
function the_cochin_render_home_sections() {
    if (is_front_page() || is_home()) {
        get_template_part('template-parts/home-hero');
        get_template_part('template-parts/home-about');
        get_template_part('template-parts/home-menu-carousel');
        get_template_part('template-parts/home-banquet-meals');
        get_template_part('template-parts/home-delivery');
        get_template_part('template-parts/home-dietary');
    }
}
add_action('astra_header_after', 'the_cochin_render_home_sections', 20);

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
