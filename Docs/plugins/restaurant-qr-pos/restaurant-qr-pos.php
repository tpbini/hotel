<?php
/**
 * Plugin Name: Restaurant QR Ordering & POS
 * Plugin URI: https://github.com/restaurant-qr-pos
 * Description: Production-ready Restaurant Dine-in QR Table Ordering, Kitchen Display System (KDS), Counter POS, and Thermal Print Bridge for WordPress.
 * Version: 1.0.0
 * Author: Antigravity IDE
 * Author URI: https://wordpress.org
 * Text Domain: restaurant-qr-pos
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin Constants
define( 'RO_VERSION', '1.0.0' );
define( 'RO_DB_VERSION', '1.0.0' );
define( 'RO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'RO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

// Require Core Files
require_once RO_PLUGIN_DIR . 'includes/class-ro-activator.php';
require_once RO_PLUGIN_DIR . 'includes/class-ro-deactivator.php';
require_once RO_PLUGIN_DIR . 'includes/class-ro-roles.php';
require_once RO_PLUGIN_DIR . 'includes/class-ro-db.php';
require_once RO_PLUGIN_DIR . 'includes/class-ro-stations.php';
require_once RO_PLUGIN_DIR . 'includes/class-ro-menu.php';
require_once RO_PLUGIN_DIR . 'includes/class-ro-tables.php';
require_once RO_PLUGIN_DIR . 'includes/class-ro-sessions.php';
require_once RO_PLUGIN_DIR . 'includes/class-ro-orders.php';
require_once RO_PLUGIN_DIR . 'includes/class-ro-service.php';
require_once RO_PLUGIN_DIR . 'includes/class-ro-payments.php';
require_once RO_PLUGIN_DIR . 'includes/class-ro-printing.php';

// REST API Controllers
require_once RO_PLUGIN_DIR . 'includes/api/class-ro-rest-controller.php';

// Admin Layer
require_once RO_PLUGIN_DIR . 'admin/class-ro-admin.php';

/**
 * Main Plugin Execution Class
 */
final class Restaurant_QR_POS {

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
        add_action( 'init', array( $this, 'register_rewrite_rules' ) );
        add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
        add_action( 'template_redirect', array( $this, 'template_redirect' ) );
        add_action( 'rest_api_init', array( 'RO_REST_Controller', 'register_routes' ) );
        add_action( 'plugins_loaded', array( 'RO_Roles', 'init' ) );
        add_action( 'init', array( $this, 'maybe_auto_initialize' ) );
        RO_Admin::init();
    }

    public function maybe_auto_initialize() {
        global $wpdb;
        $cats = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}ro_categories" );
        if ( empty( $cats ) ) {
            RO_Activator::activate();
        }
    }

    /**
     * Custom Clean URL Rewrite Rules
     * - /order/t/{token} -> Customer Mobile Ordering App
     * - /restaurant-kds/ -> Fullscreen Kitchen Display System
     * - /restaurant-pos/ -> Fullscreen Counter POS Dashboard
     * - /restaurant-waiter/ -> Fullscreen Waiter Mode App
     */
    public function register_rewrite_rules() {
        add_rewrite_rule( '^order/t/([^/]+)/?$', 'index.php?ro_app=customer&ro_token=$matches[1]', 'top' );
        add_rewrite_rule( '^restaurant-kds/?$', 'index.php?ro_app=kds', 'top' );
        add_rewrite_rule( '^restaurant-pos/?$', 'index.php?ro_app=pos', 'top' );
        add_rewrite_rule( '^restaurant-waiter/?$', 'index.php?ro_app=waiter', 'top' );
    }

    public function register_query_vars( $vars ) {
        $vars[] = 'ro_app';
        $vars[] = 'ro_token';
        return $vars;
    }

    /**
     * Intercept and render standalone frontend apps without theme overhead
     */
    public function template_redirect() {
        $app = get_query_var( 'ro_app' );
        if ( empty( $app ) && isset( $_GET['ro_app'] ) ) {
            $app = sanitize_text_field( wp_unslash( $_GET['ro_app'] ) );
        }
        if ( isset( $_GET['ro_token'] ) && empty( get_query_var( 'ro_token' ) ) ) {
            set_query_var( 'ro_token', sanitize_text_field( wp_unslash( $_GET['ro_token'] ) ) );
        }

        if ( ! empty( $app ) ) {
            switch ( $app ) {
                case 'customer':
                    include RO_PLUGIN_DIR . 'templates/customer-app.php';
                    exit;
                case 'kds':
                    include RO_PLUGIN_DIR . 'templates/kds-app.php';
                    exit;
                case 'pos':
                    include RO_PLUGIN_DIR . 'templates/pos-app.php';
                    exit;
                case 'waiter':
                    include RO_PLUGIN_DIR . 'templates/waiter-app.php';
                    exit;
            }
        }
    }
}

// Initialize Plugin
Restaurant_QR_POS::instance();
