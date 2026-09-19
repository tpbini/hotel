<?php
/**
 * Restaurant QR POS REST API Controller.
 * Base route: /wp-json/ro/v1/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RO_REST_Controller {

    const NAMESPACE = 'ro/v1';

    public static function register_routes() {
        // 1. Resolve Table & Session by QR Token (Public)
        register_rest_route( self::NAMESPACE, '/tables/(?P<token>[a-zA-Z0-9_-]+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_table_by_token' ),
            'permission_callback' => '__return_true',
        ) );

        // 1b. Get Itemized Table Bill for Customer (Public)
        register_rest_route( self::NAMESPACE, '/tables/(?P<table_id>\d+)/bill', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_table_bill' ),
            'permission_callback' => '__return_true',
        ) );

        // 2. Get Public Active Menu (Public)
        register_rest_route( self::NAMESPACE, '/menu', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_menu' ),
            'permission_callback' => '__return_true',
        ) );

        // 3. Create / Get Table Session (Public / QR Context)
        register_rest_route( self::NAMESPACE, '/sessions', array(
            'methods'             => array( 'GET', 'POST' ),
            'callback'            => array( __CLASS__, 'create_or_get_session' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::NAMESPACE, '/sessions/(?P<id>\d+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_session_details' ),
            'permission_callback' => '__return_true',
        ) );

        // 4. Create Order (Public / QR Context / Waiter)
        register_rest_route( self::NAMESPACE, '/orders', array(
            'methods'             => array( 'GET', 'POST' ),
            'callback'            => array( __CLASS__, 'create_order' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::NAMESPACE, '/orders/(?P<id>\d+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_order_details' ),
            'permission_callback' => '__return_true',
        ) );

        // 5. Update Order Status (Operational Staff / Admin / KDS / POS)
        register_rest_route( self::NAMESPACE, '/orders/(?P<id>\d+)/status', array(
            'methods'             => array( 'GET', 'POST', 'PUT', 'PATCH' ),
            'callback'            => array( __CLASS__, 'update_order_status' ),
            'permission_callback' => '__return_true',
        ) );

        // 6. Service Requests (Call Waiter / Request Bill) (Public)
        register_rest_route( self::NAMESPACE, '/service-requests', array(
            'methods'             => array( 'GET', 'POST' ),
            'callback'            => array( __CLASS__, 'create_service_request' ),
            'permission_callback' => '__return_true',
        ) );

        // 7. Kitchen Display System (KDS) Feed & Updates
        register_rest_route( self::NAMESPACE, '/kds/orders', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_kds_orders' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::NAMESPACE, '/kds/items/(?P<id>\d+)/status', array(
            'methods'             => array( 'GET', 'POST', 'PUT', 'PATCH' ),
            'callback'            => array( __CLASS__, 'update_kds_item_status' ),
            'permission_callback' => '__return_true',
        ) );

        // 8. Counter POS Endpoints
        register_rest_route( self::NAMESPACE, '/pos/tables', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_pos_tables' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::NAMESPACE, '/pos/pay', array(
            'methods'             => array( 'GET', 'POST' ),
            'callback'            => array( __CLASS__, 'process_pos_payment' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::NAMESPACE, '/pos/discount', array(
            'methods'             => array( 'GET', 'POST' ),
            'callback'            => array( __CLASS__, 'apply_pos_discount' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::NAMESPACE, '/pos/close-session', array(
            'methods'             => array( 'GET', 'POST' ),
            'callback'            => array( __CLASS__, 'close_pos_session' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::NAMESPACE, '/pos/service-requests/(?P<id>\d+)/resolve', array(
            'methods'             => array( 'GET', 'POST', 'PUT', 'PATCH' ),
            'callback'            => array( __CLASS__, 'resolve_service_request' ),
            'permission_callback' => '__return_true',
        ) );

        // 9. Print Bridge Queue & Acknowledgement
        register_rest_route( self::NAMESPACE, '/print-jobs', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_print_jobs' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::NAMESPACE, '/print-jobs/test', array(
            'methods'             => array( 'GET', 'POST' ),
            'callback'            => array( __CLASS__, 'trigger_test_print_job' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::NAMESPACE, '/print-jobs/(?P<id>\d+)/ack', array(
            'methods'             => array( 'GET', 'POST' ),
            'callback'            => array( __CLASS__, 'acknowledge_print_job' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( self::NAMESPACE, '/print-jobs/(?P<id>\d+)/reprint', array(
            'methods'             => array( 'GET', 'POST' ),
            'callback'            => array( __CLASS__, 'reprint_job' ),
            'permission_callback' => '__return_true',
        ) );

        // 10. Dashboard & Reports Summary
        register_rest_route( self::NAMESPACE, '/reports/summary', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_reports_summary' ),
            'permission_callback' => '__return_true',
        ) );

        // 11. Explicit Install / Reset Trigger
        register_rest_route( self::NAMESPACE, '/install', array(
            'methods'             => array( 'GET', 'POST' ),
            'callback'            => array( __CLASS__, 'run_plugin_install' ),
            'permission_callback' => '__return_true',
        ) );
    }

    // --- PERMISSION CALLBACKS ---

    public static function check_staff_permission() {
        return current_user_can( 'ro_kitchen_staff' ) || current_user_can( 'ro_counter_pos' ) || current_user_can( 'ro_waiter_mode' ) || current_user_can( 'manage_options' );
    }

    public static function check_kds_permission() {
        return current_user_can( 'ro_kitchen_staff' ) || current_user_can( 'ro_counter_pos' ) || current_user_can( 'manage_options' );
    }

    public static function check_pos_permission() {
        return current_user_can( 'ro_counter_pos' ) || current_user_can( 'manage_options' );
    }

    public static function check_manager_permission() {
        return current_user_can( 'ro_manage_restaurant' ) || current_user_can( 'manage_options' );
    }

    public static function check_bridge_permission( $request ) {
        $token = $request->get_header( 'x-bridge-token' );
        if ( ! $token ) {
            $token = $request->get_param( 'token' );
        }
        $stored_token = get_option( 'ro_print_bridge_token' );
        return ( ! empty( $token ) && hash_equals( (string) $stored_token, (string) $token ) ) || current_user_can( 'manage_options' );
    }

    // --- HANDLER METHODS ---

    public static function get_table_by_token( $request ) {
        $token = sanitize_text_field( $request['token'] );
        $table = RO_Tables::get_by_token( $token );

        if ( ! $table ) {
            return new WP_Error( 'not_found', __( 'Table not found or invalid QR code.', 'restaurant-qr-pos' ), array( 'status' => 404 ) );
        }

        return rest_ensure_response( array(
            'success' => true,
            'table'   => $table,
            'store'   => array(
                'name'     => get_option( 'ro_restaurant_name', 'Grand Bistro & Grill' ),
                'currency' => get_option( 'ro_currency_symbol', '$' ),
                'tax_rate' => (float) get_option( 'ro_tax_rate', 5 ),
            ),
        ) );
    }

    public static function get_table_bill( $request ) {
        $table_id = intval( $request['table_id'] );
        $table = RO_Tables::get_by_id( $table_id );
        if ( ! $table ) {
            return new WP_Error( 'not_found', __( 'Table not found.', 'restaurant-qr-pos' ), array( 'status' => 404 ) );
        }

        $session = RO_Sessions::get_active_session_by_table( $table_id );
        $currency = get_option( 'ro_currency_symbol', '$' );
        $tax_rate = (float) get_option( 'ro_tax_rate', 5 );

        if ( ! $session ) {
            return rest_ensure_response( array(
                'success'   => true,
                'has_order' => false,
                'table'     => $table,
                'items'     => array(),
                'subtotal'  => 0.00,
                'tax'       => 0.00,
                'discount'  => 0.00,
                'total'     => 0.00,
                'currency'  => $currency,
            ) );
        }

        // Recalculate session to make sure it is 100% up-to-date
        RO_Sessions::recalculate_session( $session['id'] );
        $session = RO_Sessions::get_by_id( $session['id'] );

        $orders = RO_Orders::get_orders_by_session( $session['id'] );
        $aggregated_items = array();

        foreach ( $orders as $ord ) {
            if ( 'CANCELLED' === $ord['status'] ) {
                continue;
            }
            foreach ( $ord['items'] as $it ) {
                $var_suffix = '';
                $key = $it['menu_item_id'] . '_' . $it['item_name'];
                if ( ! isset( $aggregated_items[ $key ] ) ) {
                    $aggregated_items[ $key ] = array(
                        'name'        => $it['item_name'],
                        'quantity'    => 0,
                        'unit_price'  => (float) $it['unit_price'],
                        'total_price' => 0.00,
                        'modifiers'   => ! empty( $it['modifiers'] ) ? $it['modifiers'] : array(),
                    );
                }
                $aggregated_items[ $key ]['quantity']    += intval( $it['quantity'] );
                $aggregated_items[ $key ]['total_price'] += (float) $it['total_price'];
            }
        }

        return rest_ensure_response( array(
            'success'   => true,
            'has_order' => count( $aggregated_items ) > 0,
            'table'     => $table,
            'session'   => $session,
            'items'     => array_values( $aggregated_items ),
            'subtotal'  => (float) $session['subtotal'],
            'tax'       => (float) $session['tax_amount'],
            'tax_rate'  => $tax_rate,
            'discount'  => (float) $session['discount_amount'],
            'total'     => (float) $session['total_amount'],
            'currency'  => $currency,
        ) );
    }

    public static function get_menu( $request ) {
        $menu = RO_Menu::get_full_menu_payload();
        return rest_ensure_response( array(
            'success'    => true,
            'categories' => $menu,
        ) );
    }

    public static function create_or_get_session( $request ) {
        $table_id = intval( $request->get_param( 'table_id' ) );
        if ( ! $table_id ) {
            return new WP_Error( 'missing_param', __( 'table_id is required.', 'restaurant-qr-pos' ), array( 'status' => 400 ) );
        }

        $session = RO_Sessions::get_or_create_session( $table_id );
        return rest_ensure_response( array(
            'success' => true,
            'session' => $session,
        ) );
    }

    public static function get_session_details( $request ) {
        $session_id = intval( $request['id'] );
        $session = RO_Sessions::get_by_id( $session_id );
        if ( ! $session ) {
            return new WP_Error( 'not_found', __( 'Session not found.', 'restaurant-qr-pos' ), array( 'status' => 404 ) );
        }

        return rest_ensure_response( array(
            'success' => true,
            'session' => $session,
        ) );
    }

    public static function create_order( $request ) {
        $json = $request->get_json_params() ?: array();
        $raw = file_get_contents( 'php://input' );
        if ( ! empty( $raw ) ) {
            $parsed = json_decode( $raw, true );
            if ( is_array( $parsed ) ) {
                $json = array_merge( $json, $parsed );
            }
        }
        if ( ! empty( $_POST ) ) {
            $json = array_merge( $json, $_POST );
        }

        $table_id        = intval( $request->get_param( 'table_id' ) ?: ( $json['table_id'] ?? 0 ) );
        $items           = $request->get_param( 'items' ) ?: ( $json['items'] ?? array() );
        $notes           = sanitize_textarea_field( $request->get_param( 'notes' ) ?: ( $json['notes'] ?? '' ) );
        $source          = sanitize_text_field( $request->get_param( 'source' ) ?: ( $json['source'] ?? 'QR' ) );
        $idempotency_key = sanitize_text_field( $request->get_param( 'idempotency_key' ) ?: ( $json['idempotency_key'] ?? '' ) );

        if ( ! $table_id || empty( $items ) ) {
            return new WP_Error( 'invalid_data', __( 'table_id and items are required.', 'restaurant-qr-pos' ), array( 'status' => 400 ) );
        }

        $result = RO_Orders::create_order( $table_id, $items, $notes, $source, $idempotency_key );
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        return rest_ensure_response( array(
            'success' => true,
            'order'   => $result,
        ) );
    }

    public static function get_order_details( $request ) {
        $order_id = intval( $request['id'] );
        $order = RO_Orders::get_order_by_id( $order_id );
        if ( ! $order ) {
            return new WP_Error( 'not_found', __( 'Order not found.', 'restaurant-qr-pos' ), array( 'status' => 404 ) );
        }

        return rest_ensure_response( array(
            'success' => true,
            'order'   => $order,
        ) );
    }

    public static function update_order_status( $request ) {
        $order_id = intval( $request['id'] );
        $status   = sanitize_text_field( $request->get_param( 'status' ) );
        $reason   = sanitize_textarea_field( $request->get_param( 'reason' ) ?? '' );

        $updated = RO_Orders::set_order_status( $order_id, $status, $reason );
        if ( ! $updated ) {
            return new WP_Error( 'update_failed', __( 'Could not update order status.', 'restaurant-qr-pos' ), array( 'status' => 400 ) );
        }

        return rest_ensure_response( array(
            'success' => true,
            'order'   => RO_Orders::get_order_by_id( $order_id ),
        ) );
    }

    public static function create_service_request( $request ) {
        $table_id = intval( $request->get_param( 'table_id' ) );
        $type     = sanitize_text_field( $request->get_param( 'type' ) ?? 'call_waiter' );
        $notes    = sanitize_textarea_field( $request->get_param( 'notes' ) ?? '' );

        $res = RO_Service::create_request( $table_id, $type, $notes );
        if ( is_wp_error( $res ) ) {
            return $res;
        }

        return rest_ensure_response( $res );
    }

    public static function get_kds_orders( $request ) {
        $station_id = $request->get_param( 'station_id' );
        $orders = RO_Orders::get_kds_orders( $station_id );
        $stations = RO_Stations::get_active();

        return rest_ensure_response( array(
            'success'  => true,
            'orders'   => $orders,
            'stations' => $stations,
        ) );
    }

    public static function update_kds_item_status( $request ) {
        $item_id = intval( $request['id'] );
        $status  = sanitize_text_field( $request->get_param( 'status' ) );

        RO_Orders::set_item_status( $item_id, $status );
        return rest_ensure_response( array( 'success' => true ) );
    }

    public static function get_pos_tables( $request ) {
        $tables = RO_Tables::get_all();
        $service_requests = RO_Service::get_pending_requests();

        return rest_ensure_response( array(
            'success'          => true,
            'tables'           => $tables,
            'service_requests' => $service_requests,
            'currency'         => get_option( 'ro_currency_symbol', '$' ),
        ) );
    }

    public static function process_pos_payment( $request ) {
        $session_id = intval( $request->get_param( 'session_id' ) );
        $table_id   = intval( $request->get_param( 'table_id' ) );
        $method     = sanitize_text_field( $request->get_param( 'payment_method' ) ?? 'Cash' );
        $amount     = $request->get_param( 'amount' );
        $ref        = sanitize_text_field( $request->get_param( 'reference' ) ?? '' );
        $notes      = sanitize_textarea_field( $request->get_param( 'notes' ) ?? '' );

        // Auto-resolve session from table_id if session_id not provided
        if ( ! $session_id && $table_id ) {
            $session = RO_Sessions::get_active_session_by_table( $table_id );
            if ( $session ) {
                $session_id = intval( $session['id'] );
            }
        }

        if ( ! $session_id ) {
            return new WP_Error( 'invalid_session', __( 'No active session found for this table.', 'restaurant-qr-pos' ), array( 'status' => 404 ) );
        }

        $result = RO_Payments::process_payment( $session_id, $method, $amount, $ref, $notes );
        if ( is_wp_error( $result ) ) {
            return $result;
        }

        return rest_ensure_response( array(
            'success' => true,
            'payment' => $result,
        ) );
    }

    public static function apply_pos_discount( $request ) {
        $session_id = intval( $request->get_param( 'session_id' ) );
        $discount   = floatval( $request->get_param( 'discount' ) );

        $session = RO_Payments::apply_discount( $session_id, $discount );
        return rest_ensure_response( array(
            'success' => true,
            'session' => $session,
        ) );
    }

    public static function close_pos_session( $request ) {
        $session_id = intval( $request->get_param( 'session_id' ) );
        $res = RO_Sessions::close_session( $session_id );
        return rest_ensure_response( array( 'success' => (bool) $res ) );
    }

    public static function resolve_service_request( $request ) {
        $request_id = intval( $request['id'] );
        RO_Service::resolve_request( $request_id );
        return rest_ensure_response( array( 'success' => true ) );
    }

    public static function get_print_jobs( $request ) {
        $type = $request->get_param( 'type' );
        $jobs = RO_Printing::get_pending_jobs( $type );
        return rest_ensure_response( array(
            'success' => true,
            'jobs'    => $jobs,
        ) );
    }

    public static function acknowledge_print_job( $request ) {
        $job_id = intval( $request['id'] );
        $status = sanitize_text_field( $request->get_param( 'status' ) );
        $error  = sanitize_text_field( $request->get_param( 'error' ) ?? '' );

        $res = RO_Printing::acknowledge_job( $job_id, $status, $error );
        return rest_ensure_response( array( 'success' => (bool) $res ) );
    }

    public static function reprint_job( $request ) {
        $job_id = intval( $request['id'] );
        $res = RO_Printing::reprint_job( $job_id );
        return rest_ensure_response( array( 'success' => (bool) $res ) );
    }

    public static function get_reports_summary( $request ) {
        global $wpdb;

        $today = current_time( 'Y-m-d' );

        $today_sales = (float) $wpdb->get_var( $wpdb->prepare(
            "SELECT SUM(amount) FROM " . RO_DB::table( 'payments' ) . " WHERE DATE(created_at) = %s",
            $today
        ) );

        $total_orders_today = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM " . RO_DB::table( 'orders' ) . " WHERE DATE(created_at) = %s",
            $today
        ) );

        $active_tables_count = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM " . RO_DB::table( 'tables' ) . " WHERE status = 'occupied'"
        );

        $pending_kot_count = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM " . RO_DB::table( 'orders' ) . " WHERE status IN ('NEW', 'ACCEPTED', 'PREPARING')"
        );

        // Payment method breakdown
        $payment_methods = $wpdb->get_results(
            "SELECT payment_method, COUNT(*) as count, SUM(amount) as total FROM " . RO_DB::table( 'payments' ) . " GROUP BY payment_method",
            ARRAY_A
        );

        // Top selling items
        $top_items = $wpdb->get_results(
            "SELECT item_name, SUM(quantity) as total_qty, SUM(total_price) as total_revenue FROM " . RO_DB::table( 'order_items' ) . " GROUP BY item_name ORDER BY total_qty DESC LIMIT 5",
            ARRAY_A
        );

        return rest_ensure_response( array(
            'success' => true,
            'summary' => array(
                'today_sales'         => $today_sales,
                'total_orders_today'  => $total_orders_today,
                'active_tables_count' => $active_tables_count,
                'pending_kot_count'   => $pending_kot_count,
                'payment_methods'     => $payment_methods,
                'top_items'           => $top_items,
                'currency'            => get_option( 'ro_currency_symbol', '$' ),
            ),
        ) );
    }

    public static function trigger_test_print_job( $request ) {
        $test_kot = array(
            'ticket_type'   => 'KOT',
            'order_id'      => null,
            'order_number'  => '#TEST-99',
            'table_number'  => 'Table 01',
            'source'        => 'TEST',
            'created_at'    => current_time( 'mysql' ),
            'station_id'    => 1,
            'station_name'  => 'Main Kitchen',
            'items'         => array(
                array( 'name' => 'Royal Dum Biriyani', 'quantity' => 2, 'notes' => 'Extra Raita', 'modifiers' => array( 'Extra Boiled Egg' ) ),
                array( 'name' => 'Crispy Truffle Fries', 'quantity' => 1, 'notes' => '', 'modifiers' => array( 'Extra Melted Cheddar' ) ),
            ),
            'order_notes'   => 'TEST PRINT - ESC/POS Communication OK',
        );

        $job_id = RO_DB::insert( 'print_jobs', array(
            'station_id'   => 1,
            'printer_type' => 'kitchen',
            'status'       => 'QUEUED',
            'payload'      => wp_json_encode( $test_kot ),
            'retry_count'  => 0,
            'created_at'   => current_time( 'mysql' ),
        ) );

        return rest_ensure_response( array(
            'success' => true,
            'job_id'  => $job_id,
            'message' => 'Test KOT print job queued successfully.'
        ) );
    }

    public static function run_plugin_install( $request ) {
        global $wpdb;
        RO_Activator::create_tables();
        RO_Activator::seed_initial_data();
        RO_Activator::register_roles();
        update_option( 'ro_db_version', RO_DB_VERSION );

        $tables_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}ro_tables" );
        $items_count  = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}ro_menu_items" );

        return rest_ensure_response( array(
            'success'      => true,
            'tables_count' => intval( $tables_count ),
            'items_count'  => intval( $items_count ),
            'last_error'   => $wpdb->last_error,
        ) );
    }
}
