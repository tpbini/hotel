<?php
/**
 * Admin Layer & Menu Management.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RB_Admin {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'register_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
    }

    public static function register_admin_menu() {
        $cap = 'manage_restaurant_bookings';

        // Main Menu
        add_menu_page(
            __( 'Restaurant Booking', 'restaurant-booking' ),
            __( 'Restaurant Booking', 'restaurant-booking' ),
            $cap,
            'restaurant-booking',
            array( __CLASS__, 'render_dashboard_page' ),
            'dashicons-calendar-alt',
            26
        );

        // Submenus
        add_submenu_page(
            'restaurant-booking',
            __( 'Dashboard', 'restaurant-booking' ),
            __( 'Dashboard', 'restaurant-booking' ),
            $cap,
            'restaurant-booking',
            array( __CLASS__, 'render_dashboard_page' )
        );

        add_submenu_page(
            'restaurant-booking',
            __( 'Reservations', 'restaurant-booking' ),
            __( 'Reservations', 'restaurant-booking' ),
            $cap,
            'restaurant-booking-reservations',
            array( __CLASS__, 'render_reservations_page' )
        );

        add_submenu_page(
            'restaurant-booking',
            __( 'Live Calendar', 'restaurant-booking' ),
            __( 'Live Calendar', 'restaurant-booking' ),
            $cap,
            'restaurant-booking-calendar',
            array( __CLASS__, 'render_calendar_page' )
        );

        add_submenu_page(
            'restaurant-booking',
            __( 'Opening Hours', 'restaurant-booking' ),
            __( 'Opening Hours', 'restaurant-booking' ),
            $cap,
            'restaurant-booking-hours',
            array( __CLASS__, 'render_hours_page' )
        );

        add_submenu_page(
            'restaurant-booking',
            __( 'Special Dates', 'restaurant-booking' ),
            __( 'Special Dates', 'restaurant-booking' ),
            $cap,
            'restaurant-booking-special-dates',
            array( __CLASS__, 'render_special_dates_page' )
        );

        add_submenu_page(
            'restaurant-booking',
            __( 'Tables & Combinations', 'restaurant-booking' ),
            __( 'Tables', 'restaurant-booking' ),
            $cap,
            'restaurant-booking-tables',
            array( __CLASS__, 'render_tables_page' )
        );

        add_submenu_page(
            'restaurant-booking',
            __( 'Waitlist', 'restaurant-booking' ),
            __( 'Waitlist', 'restaurant-booking' ),
            $cap,
            'restaurant-booking-waitlist',
            array( __CLASS__, 'render_waitlist_page' )
        );

        add_submenu_page(
            'restaurant-booking',
            __( 'Booking Policies & Settings', 'restaurant-booking' ),
            __( 'Policies & Settings', 'restaurant-booking' ),
            $cap,
            'restaurant-booking-policies',
            array( __CLASS__, 'render_policies_page' )
        );

        add_submenu_page(
            'restaurant-booking',
            __( 'Reports & Analytics', 'restaurant-booking' ),
            __( 'Reports', 'restaurant-booking' ),
            $cap,
            'restaurant-booking-reports',
            array( __CLASS__, 'render_reports_page' )
        );
    }

    public static function enqueue_assets( $hook ) {
        if ( strpos( $hook, 'restaurant-booking' ) === false ) {
            return;
        }

        wp_enqueue_style( 'rb-admin-style', RB_PLUGIN_URL . 'admin/assets/css/admin.css', array(), RB_VERSION );
        wp_enqueue_script( 'rb-admin-script', RB_PLUGIN_URL . 'admin/assets/js/admin.js', array( 'jquery' ), RB_VERSION, true );

        wp_localize_script( 'rb-admin-script', 'rbAdminData', array(
            'restUrl'        => esc_url_raw( rest_url( 'rb/v1/' ) ),
            'nonce'          => wp_create_nonce( 'wp_rest' ),
            'today'          => RB_i18n::today_uk(),
            'currencySymbol' => '£',
            'qrActive'       => RB_QR_Adapter::is_qr_plugin_active(),
            'posUrl'         => home_url( '/restaurant-pos/' ),
        ) );
    }

    // Page Renderers
    public static function render_dashboard_page() {
        include RB_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    public static function render_reservations_page() {
        include RB_PLUGIN_DIR . 'admin/views/reservations.php';
    }

    public static function render_calendar_page() {
        include RB_PLUGIN_DIR . 'admin/views/calendar.php';
    }

    public static function render_hours_page() {
        include RB_PLUGIN_DIR . 'admin/views/opening-hours.php';
    }

    public static function render_special_dates_page() {
        include RB_PLUGIN_DIR . 'admin/views/special-dates.php';
    }

    public static function render_tables_page() {
        include RB_PLUGIN_DIR . 'admin/views/tables.php';
    }

    public static function render_waitlist_page() {
        include RB_PLUGIN_DIR . 'admin/views/waitlist.php';
    }

    public static function render_policies_page() {
        include RB_PLUGIN_DIR . 'admin/views/policies.php';
    }

    public static function render_reports_page() {
        include RB_PLUGIN_DIR . 'admin/views/reports.php';
    }
}
