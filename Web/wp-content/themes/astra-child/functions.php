<?php
/**
 * The Cochin (Astra Child Theme) Functions
 *
 * Implements custom responsive header based on Docs/html/menu.html,
 * Poppins typography, and #6B1F2A brand palette for header and footer.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Enqueue Parent and Child Theme Styles, Poppins Font, and Header Scripts
 */
function the_cochin_enqueue_scripts() {
    // Parent Theme Style
    wp_enqueue_style(
        'astra-parent-style',
        get_template_directory_uri() . '/style.css',
        array(),
        ASTRA_THEME_VERSION
    );

    // Google Fonts: Poppins (Main Website Font)
    wp_enqueue_style(
        'the-cochin-google-fonts',
        'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap',
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
    // Remove Astra default header actions
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

    // Attach our custom menu header
    add_action('astra_header', 'the_cochin_render_custom_header', 10);
}
add_action('wp', 'the_cochin_override_astra_header', 1);

/**
 * Retrieve the booking URL (defaults to #booking or a dedicated page)
 */
function the_cochin_get_booking_url() {
    $booking_page = get_page_by_path('booking');
    if (!$booking_page) {
        $booking_page = get_page_by_path('reservations');
    }
    if (!$booking_page) {
        $booking_page = get_page_by_path('reservation');
    }

    if ($booking_page) {
        return get_permalink($booking_page);
    }

    return home_url('/#booking');
}

/**
 * Custom Nav Walker for outputting clean .nav-link classes and ARIA attributes
 */
class The_Cochin_Nav_Walker extends Walker_Nav_Menu {
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        // Skip CTA if user also has a link explicitly named 'Book a table' in WP menu
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
 * Render Navigation Links (Dynamic WP Menu with clean fallback)
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
 * Fallback Navigation Menu matching Docs/html/menu.html
 */
function the_cochin_fallback_nav_menu() {
    $home_url = home_url('/');
    $current_uri = $_SERVER['REQUEST_URI'] ?? '/';
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
    // Determine logo URL
    $custom_logo_id = get_theme_mod('custom_logo');
    $logo_url = '';

    if ($custom_logo_id) {
        $logo_data = wp_get_attachment_image_src($custom_logo_id, 'full');
        if (!empty($logo_data[0])) {
            $logo_url = $logo_data[0];
        }
    }

    if (empty($logo_url)) {
        // Child theme bundled logo fallback
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
