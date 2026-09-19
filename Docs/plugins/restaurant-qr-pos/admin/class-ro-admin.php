<?php
/**
 * WordPress Admin Management & Settings Layer.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RO_Admin {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
        add_action( 'admin_init', array( __CLASS__, 'handle_admin_actions' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
    }

    public static function add_admin_menu() {
        $cap = 'manage_options';

        add_menu_page(
            __( 'Restaurant POS', 'restaurant-qr-pos' ),
            __( 'Restaurant POS', 'restaurant-qr-pos' ),
            $cap,
            'restaurant-qr-pos',
            array( __CLASS__, 'render_dashboard_page' ),
            'dashicons-food',
            30
        );

        add_submenu_page(
            'restaurant-qr-pos',
            __( 'Dashboard', 'restaurant-qr-pos' ),
            __( 'Dashboard', 'restaurant-qr-pos' ),
            $cap,
            'restaurant-qr-pos',
            array( __CLASS__, 'render_dashboard_page' )
        );

        add_submenu_page(
            'restaurant-qr-pos',
            __( 'Tables & QR', 'restaurant-qr-pos' ),
            __( 'Tables & QR', 'restaurant-qr-pos' ),
            $cap,
            'ro-tables',
            array( __CLASS__, 'render_tables_page' )
        );

        add_submenu_page(
            'restaurant-qr-pos',
            __( 'Menu & Categories', 'restaurant-qr-pos' ),
            __( 'Menu & Categories', 'restaurant-qr-pos' ),
            $cap,
            'ro-menu',
            array( __CLASS__, 'render_menu_page' )
        );

        add_submenu_page(
            'restaurant-qr-pos',
            __( 'Kitchen Stations', 'restaurant-qr-pos' ),
            __( 'Kitchen Stations', 'restaurant-qr-pos' ),
            $cap,
            'ro-stations',
            array( __CLASS__, 'render_stations_page' )
        );

        add_submenu_page(
            'restaurant-qr-pos',
            __( 'Reports & Sales', 'restaurant-qr-pos' ),
            __( 'Reports & Sales', 'restaurant-qr-pos' ),
            $cap,
            'ro-reports',
            array( __CLASS__, 'render_reports_page' )
        );

        add_submenu_page(
            'restaurant-qr-pos',
            __( 'Settings', 'restaurant-qr-pos' ),
            __( 'Settings', 'restaurant-qr-pos' ),
            $cap,
            'ro-settings',
            array( __CLASS__, 'render_settings_page' )
        );
    }

    public static function enqueue_assets( $hook ) {
        if ( false === strpos( $hook, 'ro-' ) && false === strpos( $hook, 'restaurant-qr-pos' ) ) {
            return;
        }

        wp_enqueue_style( 'ro-admin-css', RO_PLUGIN_URL . 'admin/assets/css/admin.css', array(), RO_VERSION );
        wp_enqueue_script( 'ro-admin-js', RO_PLUGIN_URL . 'admin/assets/js/admin.js', array( 'jquery' ), RO_VERSION, true );
    }

    public static function render_dashboard_page() {
        include RO_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    public static function render_tables_page() {
        include RO_PLUGIN_DIR . 'admin/views/tables.php';
    }

    public static function render_menu_page() {
        include RO_PLUGIN_DIR . 'admin/views/menu.php';
    }

    public static function render_stations_page() {
        include RO_PLUGIN_DIR . 'admin/views/stations.php';
    }

    public static function render_reports_page() {
        include RO_PLUGIN_DIR . 'admin/views/reports.php';
    }

    public static function render_settings_page() {
        include RO_PLUGIN_DIR . 'admin/views/settings.php';
    }

    public static function handle_admin_actions() {
        if ( ! current_user_can( 'manage_options' ) && ! current_user_can( 'ro_manage_restaurant' ) ) {
            return;
        }

        // Handle Table Add/Edit
        if ( isset( $_POST['ro_action'] ) && 'save_table' === $_POST['ro_action'] ) {
            check_admin_referer( 'ro_save_table_nonce' );
            $id = ! empty( $_POST['table_id'] ) ? intval( $_POST['table_id'] ) : null;
            $number = sanitize_text_field( $_POST['table_number'] );
            $capacity = intval( $_POST['capacity'] ?? 4 );
            $notes = sanitize_textarea_field( $_POST['notes'] ?? '' );

            if ( $id ) {
                $status = sanitize_text_field( $_POST['status'] ?? 'available' );
                RO_Tables::update_table( $id, $number, $capacity, $status, $notes );
            } else {
                RO_Tables::create_table( $number, $capacity, $notes );
            }

            wp_safe_redirect( admin_url( 'admin.php?page=ro-tables&updated=1' ) );
            exit;
        }

        // Handle QR Regenerate
        if ( isset( $_GET['ro_action'] ) && 'regen_qr' === $_GET['ro_action'] && isset( $_GET['table_id'] ) ) {
            check_admin_referer( 'ro_regen_qr_' . intval( $_GET['table_id'] ) );
            RO_Tables::regenerate_qr( intval( $_GET['table_id'] ) );
            wp_safe_redirect( admin_url( 'admin.php?page=ro-tables&qr_regenerated=1' ) );
            exit;
        }

        // Handle Category Create
        if ( isset( $_POST['ro_action'] ) && 'create_category' === $_POST['ro_action'] ) {
            check_admin_referer( 'ro_category_nonce' );
            $name = sanitize_text_field( $_POST['cat_name'] );
            $order = intval( $_POST['cat_order'] ?? 0 );
            RO_Menu::create_category( $name, $order );
            wp_safe_redirect( admin_url( 'admin.php?page=ro-menu&cat_created=1' ) );
            exit;
        }

        // Handle Station Create
        if ( isset( $_POST['ro_action'] ) && 'create_station' === $_POST['ro_action'] ) {
            check_admin_referer( 'ro_station_nonce' );
            $name = sanitize_text_field( $_POST['station_name'] );
            $code = sanitize_title( $_POST['station_code'] );
            RO_Stations::create( $name, $code );
            wp_safe_redirect( admin_url( 'admin.php?page=ro-stations&station_created=1' ) );
            exit;
        }

        // Handle Settings Save
        if ( isset( $_POST['ro_action'] ) && 'save_settings' === $_POST['ro_action'] ) {
            check_admin_referer( 'ro_settings_nonce' );
            update_option( 'ro_restaurant_name', sanitize_text_field( $_POST['restaurant_name'] ?? '' ) );
            update_option( 'ro_currency_symbol', sanitize_text_field( $_POST['currency_symbol'] ?? '$' ) );
            update_option( 'ro_tax_rate', floatval( $_POST['tax_rate'] ?? 5.0 ) );
            update_option( 'ro_restaurant_phone', sanitize_text_field( $_POST['restaurant_phone'] ?? '' ) );
            update_option( 'ro_restaurant_address', sanitize_textarea_field( $_POST['restaurant_address'] ?? '' ) );
            update_option( 'ro_kitchen_printer_ip', sanitize_text_field( $_POST['kitchen_printer_ip'] ?? '192.168.1.20' ) );
            update_option( 'ro_kitchen_printer_port', intval( $_POST['kitchen_printer_port'] ?? 9100 ) );
            update_option( 'ro_counter_printer_ip', sanitize_text_field( $_POST['counter_printer_ip'] ?? '192.168.1.21' ) );
            update_option( 'ro_counter_printer_port', intval( $_POST['counter_printer_port'] ?? 9100 ) );
            update_option( 'ro_autoprint_kot', isset( $_POST['autoprint_kot'] ) ? '1' : '0' );
            update_option( 'ro_autoprint_receipt', isset( $_POST['autoprint_receipt'] ) ? '1' : '0' );

            if ( ! empty( $_POST['print_bridge_token'] ) ) {
                update_option( 'ro_print_bridge_token', sanitize_text_field( $_POST['print_bridge_token'] ) );
            }

            wp_safe_redirect( admin_url( 'admin.php?page=ro-settings&saved=1' ) );
            exit;
        }

        // Handle Send Test Print Job
        if ( isset( $_GET['ro_action'] ) && 'send_test_print' === $_GET['ro_action'] ) {
            check_admin_referer( 'ro_test_print_nonce' );

            $test_kot = array(
                'ticket_type'   => 'KOT',
                'order_id'      => 9999,
                'order_number'  => '#TEST-99',
                'table_number'  => 'Table 01',
                'source'        => 'TEST',
                'created_at'    => current_time( 'mysql' ),
                'station_id'    => 1,
                'station_name'  => 'Main Kitchen',
                'printer_ip'    => get_option( 'ro_kitchen_printer_ip', '192.168.1.20' ),
                'printer_port'  => get_option( 'ro_kitchen_printer_port', 9100 ),
                'items'         => array(
                    array( 'name' => 'Royal Dum Biriyani', 'quantity' => 2, 'notes' => 'Extra Raita', 'modifiers' => array( 'Extra Boiled Egg' ) ),
                    array( 'name' => 'Crispy Truffle Fries', 'quantity' => 1, 'notes' => '', 'modifiers' => array( 'Extra Melted Cheddar' ) ),
                ),
                'order_notes'   => 'TEST PRINT - ESC/POS Printer Communication OK',
            );

            RO_DB::insert( 'print_jobs', array(
                'station_id'   => 1,
                'printer_type' => 'kitchen',
                'status'       => 'QUEUED',
                'payload'      => wp_json_encode( $test_kot ),
                'retry_count'  => 0,
                'created_at'   => current_time( 'mysql' ),
            ) );

            wp_safe_redirect( admin_url( 'admin.php?page=ro-settings&test_sent=1' ) );
            exit;
        }
    }
}
