<?php
/**
 * Plugin Name: UK Restaurant Table Booking
 * Plugin URI: https://github.com/restaurant-table-booking
 * Description: Production-ready UK-focused restaurant table reservation plugin with high-precision availability engine, split service hours, UK date/time/currency localization, secure guest management, and seamless integration with Restaurant QR Ordering & POS.
 * Version: 1.0.0
 * Author: Antigravity IDE
 * Author URI: https://wordpress.org
 * Text Domain: restaurant-booking
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin Constants
define( 'RB_VERSION', '1.0.0' );
define( 'RB_DB_VERSION', '1.0.0' );
define( 'RB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'RB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Core Domain & Helper Includes
require_once RB_PLUGIN_DIR . 'includes/class-rb-activator.php';
require_once RB_PLUGIN_DIR . 'includes/class-rb-deactivator.php';
require_once RB_PLUGIN_DIR . 'includes/class-rb-roles.php';
require_once RB_PLUGIN_DIR . 'includes/class-rb-db.php';
require_once RB_PLUGIN_DIR . 'includes/class-rb-i18n.php';
require_once RB_PLUGIN_DIR . 'includes/availability/class-rb-availability.php';
require_once RB_PLUGIN_DIR . 'includes/reservations/class-rb-reservations.php';
require_once RB_PLUGIN_DIR . 'includes/integrations/class-rb-qr-adapter.php';
require_once RB_PLUGIN_DIR . 'includes/notifications/class-rb-notifications.php';

// REST API Controllers
require_once RB_PLUGIN_DIR . 'includes/api/class-rb-rest-controller.php';
require_once RB_PLUGIN_DIR . 'includes/api/class-rb-public-api.php';
require_once RB_PLUGIN_DIR . 'includes/api/class-rb-admin-api.php';

// Admin Layer
require_once RB_PLUGIN_DIR . 'admin/class-rb-admin.php';

/**
 * Main Plugin Execution Class
 */
final class Restaurant_Booking {

    private static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        register_activation_hook( __FILE__, array( 'RB_Activator', 'activate' ) );
        register_deactivation_hook( __FILE__, array( 'RB_Deactivator', 'deactivate' ) );

        add_action( 'plugins_loaded', array( 'RB_Roles', 'init' ) );
        add_action( 'init', array( $this, 'register_rewrite_rules' ) );
        add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
        add_action( 'template_redirect', array( $this, 'template_redirect' ), 1 );
        add_action( 'rest_api_init', array( 'RB_REST_Controller', 'register_routes' ) );

        // Shortcodes
        add_shortcode( 'restaurant_booking', array( $this, 'render_booking_shortcode' ) );
        add_shortcode( 'manage_booking', array( $this, 'render_manage_shortcode' ) );

        // Auto-check DB migration & flush rules on load if needed
        add_action( 'init', array( $this, 'maybe_auto_initialize' ) );

        // Admin
        RB_Admin::init();

        // QR Integration hooks
        RB_QR_Adapter::init();
    }

    public function maybe_auto_initialize() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'rb_reservations';
        $table_exists = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_name ) );
        if ( empty( $table_exists ) ) {
            RB_Activator::activate();
        }

        // Auto-create 'Book a Table' WordPress page if not exists
        $page_check = get_page_by_path( 'book-table' );
        if ( ! $page_check ) {
            wp_insert_post( array(
                'post_title'     => 'Book a Table',
                'post_name'      => 'book-table',
                'post_content'   => '[restaurant_booking theme="light"]',
                'post_status'    => 'publish',
                'post_type'      => 'page',
                'comment_status' => 'closed',
            ) );
        }
    }

    /**
     * Rewrite rules for standalone booking application and token management
     * - /book-table/ -> Standalone full-width reservation app
     * - /booking/manage/{token} -> Guest booking management & cancellation portal
     */
    public function register_rewrite_rules() {
        add_rewrite_rule( '^booking/standalone/?$', 'index.php?rb_app=booking', 'top' );
        add_rewrite_rule( '^booking/manage/([^/]+)/?$', 'index.php?rb_app=manage&rb_token=$matches[1]', 'top' );
    }

    public function register_query_vars( $vars ) {
        $vars[] = 'rb_app';
        $vars[] = 'rb_token';
        return $vars;
    }

    /**
     * Intercept and render standalone apps or manager screens
     */
    public function template_redirect() {
        $app = get_query_var( 'rb_app' );
        if ( empty( $app ) && isset( $_GET['rb_app'] ) ) {
            $app = sanitize_text_field( wp_unslash( $_GET['rb_app'] ) );
        }

        // Direct URI fallback detection for development servers & plain permalinks
        $req_uri = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';
        $path = parse_url( $req_uri, PHP_URL_PATH );

        if ( empty( $app ) && ! empty( $path ) ) {
            if ( preg_match( '#/booking/manage/([^/]+)/?#', $path, $m ) ) {
                $app = 'manage';
                set_query_var( 'rb_token', sanitize_text_field( $m[1] ) );
            } elseif ( isset( $_GET['standalone'] ) && preg_match( '#/book-table/?$#', $path ) ) {
                $app = 'booking';
            }
        }

        if ( isset( $_GET['rb_token'] ) && empty( get_query_var( 'rb_token' ) ) ) {
            set_query_var( 'rb_token', sanitize_text_field( wp_unslash( $_GET['rb_token'] ) ) );
        }

        if ( ! empty( $app ) ) {
            switch ( $app ) {
                case 'booking':
                    include RB_PLUGIN_DIR . 'templates/booking-app.php';
                    exit;
                case 'manage':
                    include RB_PLUGIN_DIR . 'templates/manage-booking.php';
                    exit;
            }
        }
    }

    /**
     * Shortcode: [restaurant_booking]
     */
    public function render_booking_shortcode( $atts = array() ) {
        $atts = shortcode_atts( array(
            'theme' => 'light',
            'title' => 'Reserve a Table',
        ), $atts, 'restaurant_booking' );

        wp_enqueue_style( 'rb-booking-style', RB_PLUGIN_URL . 'assets/css/booking.css', array(), RB_VERSION );
        wp_enqueue_script( 'rb-booking-script', RB_PLUGIN_URL . 'assets/js/booking.js', array( 'jquery' ), RB_VERSION, true );

        wp_localize_script( 'rb-booking-script', 'rbData', array(
            'restUrl'        => esc_url_raw( rest_url( 'rb/v1/' ) ),
            'nonce'          => wp_create_nonce( 'wp_rest' ),
            'currencySymbol' => '£',
            'dateFormat'     => 'd/m/Y',
            'allergensNotice'=> get_option( 'rb_allergen_notice', 'If you or any member of your party have a severe food allergy or specific dietary requirement, please inform us directly by telephone.' ),
            'phone'          => get_option( 'rb_restaurant_phone', '020 7946 0912' ),
            'manageUrl'      => home_url( '/booking/manage/' ),
        ) );

        ob_start();
        ?>
        <div class="rb-booking-embed" id="rb-booking-root" data-theme="<?php echo esc_attr( $atts['theme'] ); ?>">
            <div class="rb-spinner-wrap"><div class="rb-spinner"></div><p><?php esc_html_e( 'Loading reservation system...', 'restaurant-booking' ); ?></p></div>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Shortcode: [manage_booking]
     */
    public function render_manage_shortcode( $atts = array() ) {
        $token = isset( $_GET['token'] ) ? sanitize_text_field( wp_unslash( $_GET['token'] ) ) : ( get_query_var( 'rb_token' ) ? sanitize_text_field( get_query_var( 'rb_token' ) ) : '' );

        wp_enqueue_style( 'rb-booking-style', RB_PLUGIN_URL . 'assets/css/booking.css', array(), RB_VERSION );
        wp_enqueue_script( 'rb-booking-script', RB_PLUGIN_URL . 'assets/js/booking.js', array( 'jquery' ), RB_VERSION, true );

        wp_localize_script( 'rb-booking-script', 'rbData', array(
            'restUrl'        => esc_url_raw( rest_url( 'rb/v1/' ) ),
            'nonce'          => wp_create_nonce( 'wp_rest' ),
            'currencySymbol' => '£',
            'dateFormat'     => 'd/m/Y',
            'token'          => $token,
            'manageUrl'      => home_url( '/booking/manage/' ),
        ) );

        ob_start();
        ?>
        <div class="rb-booking-embed" id="rb-manage-root" data-token="<?php echo esc_attr( $token ); ?>">
            <div class="rb-spinner-wrap"><div class="rb-spinner"></div><p><?php esc_html_e( 'Loading reservation...', 'restaurant-booking' ); ?></p></div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// Instantiate Plugin
Restaurant_Booking::instance();
