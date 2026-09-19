<?php
/**
 * Admin REST API Endpoints for Operations, Seating, Walk-Ins & Settings.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RB_Admin_API {

    public static function register_routes() {
        $ns = RB_REST_Controller::NAMESPACE;

        // 1. List / Filter Reservations
        register_rest_route( $ns, '/admin/reservations', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_reservations' ),
            'permission_callback' => array( 'RB_REST_Controller', 'check_admin_permission' ),
        ) );

        // 2. Reservation Detail
        register_rest_route( $ns, '/admin/reservations/(?P<id>\d+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_reservation_detail' ),
            'permission_callback' => array( 'RB_REST_Controller', 'check_admin_permission' ),
        ) );

        // 3. Update Status
        register_rest_route( $ns, '/admin/reservations/(?P<id>\d+)/status', array(
            'methods'             => WP_REST_Server::EDITABLE,
            'callback'            => array( __CLASS__, 'update_reservation_status' ),
            'permission_callback' => array( 'RB_REST_Controller', 'check_admin_permission' ),
        ) );

        // 4. Seat Guest (triggers QR POS session)
        register_rest_route( $ns, '/admin/reservations/(?P<id>\d+)/seat', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'seat_guest' ),
            'permission_callback' => array( 'RB_REST_Controller', 'check_admin_permission' ),
        ) );

        // 5. Assign / Reassign Table
        register_rest_route( $ns, '/admin/reservations/(?P<id>\d+)/assign-table', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'assign_table' ),
            'permission_callback' => array( 'RB_REST_Controller', 'check_admin_permission' ),
        ) );

        // 6. Fast Walk-In Creator
        register_rest_route( $ns, '/admin/walk-ins', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'create_walk_in' ),
            'permission_callback' => array( 'RB_REST_Controller', 'check_admin_permission' ),
        ) );

        // 7. Calendar View Timeline Data
        register_rest_route( $ns, '/admin/calendar', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_calendar_data' ),
            'permission_callback' => array( 'RB_REST_Controller', 'check_admin_permission' ),
        ) );

        // 8. Stats & Dashboard Metrics
        register_rest_route( $ns, '/admin/stats', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_stats' ),
            'permission_callback' => array( 'RB_REST_Controller', 'check_admin_permission' ),
        ) );

        // 9. Settings
        register_rest_route( $ns, '/admin/settings', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( __CLASS__, 'get_settings' ),
                'permission_callback' => array( 'RB_REST_Controller', 'check_admin_permission' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( __CLASS__, 'update_settings' ),
                'permission_callback' => array( 'RB_REST_Controller', 'check_manage_permission' ),
            ),
        ) );

        // 10. Opening Hours CRUD
        register_rest_route( $ns, '/admin/opening-hours', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( __CLASS__, 'get_opening_hours' ),
                'permission_callback' => array( 'RB_REST_Controller', 'check_admin_permission' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( __CLASS__, 'save_opening_hour' ),
                'permission_callback' => array( 'RB_REST_Controller', 'check_manage_permission' ),
            ),
        ) );
        register_rest_route( $ns, '/admin/opening-hours/(?P<id>\d+)', array(
            'methods'             => WP_REST_Server::DELETABLE,
            'callback'            => array( __CLASS__, 'delete_opening_hour' ),
            'permission_callback' => array( 'RB_REST_Controller', 'check_manage_permission' ),
        ) );

        // 11. Special Dates CRUD
        register_rest_route( $ns, '/admin/special-dates', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( __CLASS__, 'get_special_dates' ),
                'permission_callback' => array( 'RB_REST_Controller', 'check_admin_permission' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( __CLASS__, 'save_special_date' ),
                'permission_callback' => array( 'RB_REST_Controller', 'check_manage_permission' ),
            ),
        ) );
        register_rest_route( $ns, '/admin/special-dates/(?P<id>\d+)', array(
            'methods'             => WP_REST_Server::DELETABLE,
            'callback'            => array( __CLASS__, 'delete_special_date' ),
            'permission_callback' => array( 'RB_REST_Controller', 'check_manage_permission' ),
        ) );

        // 12. Tables & Combinations
        register_rest_route( $ns, '/admin/tables', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( __CLASS__, 'get_tables' ),
                'permission_callback' => array( 'RB_REST_Controller', 'check_admin_permission' ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( __CLASS__, 'save_table' ),
                'permission_callback' => array( 'RB_REST_Controller', 'check_manage_permission' ),
            ),
        ) );

        // 13. Waitlist Management
        register_rest_route( $ns, '/admin/waitlist', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_waitlist' ),
            'permission_callback' => array( 'RB_REST_Controller', 'check_admin_permission' ),
        ) );
        register_rest_route( $ns, '/admin/waitlist/(?P<id>\d+)/status', array(
            'methods'             => WP_REST_Server::EDITABLE,
            'callback'            => array( __CLASS__, 'update_waitlist_status' ),
            'permission_callback' => array( 'RB_REST_Controller', 'check_admin_permission' ),
        ) );
    }

    public static function get_reservations( WP_REST_Request $request ) {
        global $wpdb;
        $table = RB_DB::table( 'reservations' );

        $date   = sanitize_text_field( $request->get_param( 'date' ) );
        $status = sanitize_text_field( $request->get_param( 'status' ) );
        $search = sanitize_text_field( $request->get_param( 'search' ) );
        $limit  = max( 1, min( 100, intval( $request->get_param( 'limit' ) ? $request->get_param( 'limit' ) : 50 ) ) );
        $offset = max( 0, intval( $request->get_param( 'offset' ) ? $request->get_param( 'offset' ) : 0 ) );

        $where = array( '1=1' );
        $args  = array();

        if ( ! empty( $date ) ) {
            $where[] = 'booking_date = %s';
            $args[]  = $date;
        }

        if ( ! empty( $status ) && $status !== 'all' ) {
            $where[] = 'status = %s';
            $args[]  = $status;
        }

        if ( ! empty( $search ) ) {
            $like = '%' . $wpdb->esc_like( $search ) . '%';
            $where[] = '(customer_name LIKE %s OR phone LIKE %s OR email LIKE %s OR booking_reference LIKE %s)';
            $args[] = $like;
            $args[] = $like;
            $args[] = $like;
            $args[] = $like;
        }

        $where_sql = implode( ' AND ', $where );

        $count_query = "SELECT COUNT(*) FROM $table WHERE $where_sql";
        $total = empty( $args ) ? $wpdb->get_var( $count_query ) : $wpdb->get_var( $wpdb->prepare( $count_query, ...$args ) );

        $query = "SELECT * FROM $table WHERE $where_sql ORDER BY booking_date DESC, start_time DESC LIMIT %d OFFSET %d";
        $query_args = array_merge( $args, array( $limit, $offset ) );
        $rows = RB_DB::get_results( $query, ...$query_args );

        $formatted = array();
        foreach ( $rows as $row ) {
            $res = RB_Reservations::get_by_id( $row['id'] );
            if ( $res ) {
                $formatted[] = $res;
            }
        }

        return new WP_REST_Response( array(
            'success'      => true,
            'total'        => intval( $total ),
            'limit'        => $limit,
            'offset'       => $offset,
            'reservations' => $formatted,
        ), 200 );
    }

    public static function get_reservation_detail( WP_REST_Request $request ) {
        $id = intval( $request->get_param( 'id' ) );
        $res = RB_Reservations::get_by_id( $id );
        if ( ! $res ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Reservation not found' ), 404 );
        }

        // Get audit events
        $events = RB_DB::get_results(
            "SELECT * FROM " . RB_DB::table( 'events' ) . " WHERE reservation_id = %d ORDER BY id DESC",
            $id
        );

        return new WP_REST_Response( array(
            'success'     => true,
            'reservation' => $res,
            'events'      => $events,
            'table'       => ! empty( $res['table_id'] ) ? RB_QR_Adapter::get_table_by_id( $res['table_id'] ) : null,
        ), 200 );
    }

    public static function update_reservation_status( WP_REST_Request $request ) {
        $id = intval( $request->get_param( 'id' ) );
        $params = $request->get_json_params();
        $status = sanitize_text_field( isset( $params['status'] ) ? $params['status'] : '' );
        $notes  = sanitize_text_field( isset( $params['notes'] ) ? $params['notes'] : '' );

        $valid_statuses = array( 'pending', 'confirmed', 'seated', 'completed', 'cancelled', 'no_show' );
        if ( ! in_array( $status, $valid_statuses, true ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Invalid status value.' ), 400 );
        }

        $updated = RB_Reservations::update_status( $id, $status, $notes );
        if ( ! $updated ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Could not update status.' ), 500 );
        }

        return new WP_REST_Response( array(
            'success'     => true,
            'message'     => sprintf( 'Reservation status updated to %s.', strtoupper( $status ) ),
            'reservation' => $updated,
        ), 200 );
    }

    public static function seat_guest( WP_REST_Request $request ) {
        $id = intval( $request->get_param( 'id' ) );
        $res = RB_Reservations::get_by_id( $id );
        if ( ! $res ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Reservation not found.' ), 404 );
        }

        if ( empty( $res['table_id'] ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'No table assigned to this reservation. Please assign a table first.' ), 400 );
        }

        $seated = RB_Reservations::update_status( $id, 'seated', 'Guest seated via Admin Control Center.' );

        return new WP_REST_Response( array(
            'success'     => true,
            'message'     => sprintf( 'Guest %s has been seated at Table %s.', $res['customer_name'], $res['table_number_display'] ),
            'reservation' => $seated,
        ), 200 );
    }

    public static function assign_table( WP_REST_Request $request ) {
        $id = intval( $request->get_param( 'id' ) );
        $params = $request->get_json_params();
        $table_id = intval( isset( $params['table_id'] ) ? $params['table_id'] : 0 );

        if ( empty( $table_id ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Please select a valid table.' ), 400 );
        }

        $table = RB_QR_Adapter::get_table_by_id( $table_id );
        if ( ! $table ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Table not found.' ), 404 );
        }

        RB_DB::update( 'reservations', array(
            'table_id'             => $table_id,
            'table_number_display' => $table['table_number'],
            'updated_at'           => RB_i18n::now_uk(),
        ), array( 'id' => $id ) );

        RB_DB::log_event( $id, 'TABLE_REASSIGNED', "Table changed to {$table['table_number']}.", 'staff' );

        return new WP_REST_Response( array(
            'success'     => true,
            'message'     => "Table assigned to {$table['table_number']}.",
            'reservation' => RB_Reservations::get_by_id( $id ),
        ), 200 );
    }

    public static function create_walk_in( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        if ( empty( $params ) ) {
            $params = $request->get_params();
        }

        $party_size    = max( 1, intval( isset( $params['party_size'] ) ? $params['party_size'] : 2 ) );
        $table_id      = intval( isset( $params['table_id'] ) ? $params['table_id'] : 0 );
        $customer_name = sanitize_text_field( isset( $params['customer_name'] ) && ! empty( $params['customer_name'] ) ? $params['customer_name'] : 'Walk-In Guest' );
        $notes         = sanitize_textarea_field( isset( $params['notes'] ) ? $params['notes'] : '' );

        if ( empty( $table_id ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => 'Please select an available table for the walk-in.' ), 400 );
        }

        $walk_in = RB_Reservations::create_walk_in( $party_size, $table_id, $customer_name, $notes );
        if ( is_wp_error( $walk_in ) ) {
            return new WP_REST_Response( array( 'success' => false, 'message' => $walk_in->get_error_message() ), 400 );
        }

        return new WP_REST_Response( array(
            'success'     => true,
            'message'     => 'Walk-in seated successfully!',
            'reservation' => $walk_in,
        ), 201 );
    }

    public static function get_calendar_data( WP_REST_Request $request ) {
        $date = sanitize_text_field( $request->get_param( 'date' ) ? $request->get_param( 'date' ) : RB_i18n::today_uk() );

        $tables = RB_QR_Adapter::get_canonical_tables();
        $reservations = RB_DB::get_results(
            "SELECT * FROM " . RB_DB::table( 'reservations' ) . " WHERE booking_date = %s AND status NOT IN ('cancelled') ORDER BY start_time ASC",
            $date
        );

        $formatted_res = array();
        foreach ( $reservations as $r ) {
            $formatted_res[] = RB_Reservations::get_by_id( $r['id'] );
        }

        // Fetch opening hours for this day
        $day_of_week = intval( date( 'N', strtotime( $date ) ) );
        $services = RB_DB::get_results(
            "SELECT * FROM " . RB_DB::table( 'opening_hours' ) . " WHERE day_of_week = %d AND is_active = 1 ORDER BY open_time ASC",
            $day_of_week
        );

        return new WP_REST_Response( array(
            'success'        => true,
            'date'           => $date,
            'date_formatted' => RB_i18n::format_date_uk( $date, 'l, j F Y' ),
            'tables'         => $tables,
            'services'       => $services,
            'reservations'   => $formatted_res,
        ), 200 );
    }

    public static function get_stats( WP_REST_Request $request ) {
        $today = RB_i18n::today_uk();
        $table = RB_DB::table( 'reservations' );

        // Today's total bookings & covers
        $today_bookings = intval( RB_DB::get_var( "SELECT COUNT(*) FROM $table WHERE booking_date = %s", $today ) );
        $today_covers   = intval( RB_DB::get_var( "SELECT COALESCE(SUM(party_size), 0) FROM $table WHERE booking_date = %s AND status NOT IN ('cancelled')", $today ) );
        $today_seated   = intval( RB_DB::get_var( "SELECT COUNT(*) FROM $table WHERE booking_date = %s AND status = 'seated'", $today ) );
        $today_pending  = intval( RB_DB::get_var( "SELECT COUNT(*) FROM $table WHERE booking_date = %s AND status = 'pending'", $today ) );
        $today_confirmed= intval( RB_DB::get_var( "SELECT COUNT(*) FROM $table WHERE booking_date = %s AND status = 'confirmed'", $today ) );

        // All-time totals & cancellation rate
        $total_all = intval( RB_DB::get_var( "SELECT COUNT(*) FROM $table" ) );
        $total_cancelled = intval( RB_DB::get_var( "SELECT COUNT(*) FROM $table WHERE status = 'cancelled'" ) );
        $total_noshow = intval( RB_DB::get_var( "SELECT COUNT(*) FROM $table WHERE status = 'no_show'" ) );

        $cancel_rate = ( $total_all > 0 ) ? round( ( $total_cancelled / $total_all ) * 100, 1 ) : 0;
        $noshow_rate = ( $total_all > 0 ) ? round( ( $total_noshow / $total_all ) * 100, 1 ) : 0;

        // Active physical tables
        $tables = RB_QR_Adapter::get_canonical_tables();
        $occupied_count = 0;
        foreach ( $tables as $t ) {
            if ( $t['status'] === 'occupied' ) {
                $occupied_count++;
            }
        }

        return new WP_REST_Response( array(
            'success' => true,
            'today'   => array(
                'date'       => $today,
                'formatted'  => RB_i18n::format_date_uk( $today, 'l, j F Y' ),
                'bookings'   => $today_bookings,
                'covers'     => $today_covers,
                'seated'     => $today_seated,
                'pending'    => $today_pending,
                'confirmed'  => $today_confirmed,
            ),
            'tables' => array(
                'total'    => count( $tables ),
                'occupied' => $occupied_count,
                'free'     => count( $tables ) - $occupied_count,
            ),
            'rates' => array(
                'cancellation_rate' => $cancel_rate,
                'noshow_rate'       => $noshow_rate,
                'total_bookings'    => $total_all,
            ),
            'qr_plugin_active' => RB_QR_Adapter::is_qr_plugin_active(),
        ), 200 );
    }

    public static function get_settings() {
        return new WP_REST_Response( array(
            'success'  => true,
            'settings' => array(
                'restaurant_name'            => get_option( 'rb_restaurant_name', 'The Crown & Thistle Bistro' ),
                'restaurant_phone'           => get_option( 'rb_restaurant_phone', '020 7946 0912' ),
                'restaurant_email'           => get_option( 'rb_restaurant_email', get_option( 'admin_email' ) ),
                'restaurant_address'         => get_option( 'rb_restaurant_address', '45 High Street, London, EC1A 1BB' ),
                'default_duration'           => intval( get_option( 'rb_default_duration', 120 ) ),
                'turnaround_buffer'          => intval( get_option( 'rb_turnaround_buffer', 15 ) ),
                'booking_interval'           => intval( get_option( 'rb_booking_interval', 30 ) ),
                'min_party_size'             => intval( get_option( 'rb_min_party_size', 1 ) ),
                'max_party_size'             => intval( get_option( 'rb_max_party_size', 12 ) ),
                'auto_confirm'               => intval( get_option( 'rb_auto_confirm', 1 ) ),
                'cancellation_cutoff_hours'  => intval( get_option( 'rb_cancellation_cutoff_hours', 2 ) ),
                'max_advance_days'           => intval( get_option( 'rb_max_advance_days', 90 ) ),
                'allergen_notice'            => get_option( 'rb_allergen_notice', '' ),
                'privacy_policy_text'        => get_option( 'rb_privacy_policy_text', '' ),
            ),
        ), 200 );
    }

    public static function update_settings( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        if ( empty( $params ) ) {
            $params = $request->get_params();
        }

        $fields = array(
            'restaurant_name'           => 'sanitize_text_field',
            'restaurant_phone'          => 'sanitize_text_field',
            'restaurant_email'          => 'sanitize_email',
            'restaurant_address'        => 'sanitize_text_field',
            'default_duration'          => 'intval',
            'turnaround_buffer'         => 'intval',
            'booking_interval'          => 'intval',
            'min_party_size'            => 'intval',
            'max_party_size'            => 'intval',
            'auto_confirm'              => 'intval',
            'cancellation_cutoff_hours' => 'intval',
            'max_advance_days'          => 'intval',
            'allergen_notice'           => 'sanitize_textarea_field',
            'privacy_policy_text'       => 'sanitize_textarea_field',
        );

        foreach ( $fields as $key => $sanitizer ) {
            if ( isset( $params[ $key ] ) ) {
                $val = call_user_func( $sanitizer, $params[ $key ] );
                update_option( 'rb_' . $key, $val );
            }
        }

        return new WP_REST_Response( array(
            'success' => true,
            'message' => __( 'Settings updated successfully.', 'restaurant-booking' ),
        ), 200 );
    }

    public static function get_opening_hours() {
        $hours = RB_DB::get_results( "SELECT * FROM " . RB_DB::table( 'opening_hours' ) . " ORDER BY day_of_week ASC, open_time ASC" );
        return new WP_REST_Response( array( 'success' => true, 'hours' => $hours ), 200 );
    }

    public static function save_opening_hour( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        $id     = isset( $params['id'] ) ? intval( $params['id'] ) : 0;
        $day    = intval( $params['day_of_week'] );
        $name   = sanitize_text_field( $params['service_name'] );
        $open   = sanitize_text_field( $params['open_time'] );
        $close  = sanitize_text_field( $params['close_time'] );
        $int    = intval( isset( $params['slot_interval'] ) ? $params['slot_interval'] : 30 );
        $active = isset( $params['is_active'] ) ? intval( $params['is_active'] ) : 1;

        $data = array(
            'day_of_week'   => $day,
            'service_name'  => $name,
            'open_time'     => substr( $open, 0, 5 ) . ':00',
            'close_time'    => substr( $close, 0, 5 ) . ':00',
            'slot_interval' => $int,
            'is_active'     => $active,
        );

        if ( $id > 0 ) {
            RB_DB::update( 'opening_hours', $data, array( 'id' => $id ) );
        } else {
            RB_DB::insert( 'opening_hours', $data );
        }

        return new WP_REST_Response( array( 'success' => true, 'message' => 'Opening hours saved.' ), 200 );
    }

    public static function delete_opening_hour( WP_REST_Request $request ) {
        $id = intval( $request->get_param( 'id' ) );
        RB_DB::delete( 'opening_hours', array( 'id' => $id ) );
        return new WP_REST_Response( array( 'success' => true, 'message' => 'Deleted.' ), 200 );
    }

    public static function get_special_dates() {
        $dates = RB_DB::get_results( "SELECT * FROM " . RB_DB::table( 'special_dates' ) . " ORDER BY special_date ASC" );
        return new WP_REST_Response( array( 'success' => true, 'dates' => $dates ), 200 );
    }

    public static function save_special_date( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        $id     = isset( $params['id'] ) ? intval( $params['id'] ) : 0;
        $date   = sanitize_text_field( $params['special_date'] );
        $name   = sanitize_text_field( $params['name'] );
        $type   = sanitize_text_field( $params['action_type'] );
        $open   = ! empty( $params['open_time'] ) ? sanitize_text_field( $params['open_time'] ) : null;
        $close  = ! empty( $params['close_time'] ) ? sanitize_text_field( $params['close_time'] ) : null;
        $notes  = sanitize_textarea_field( isset( $params['notes'] ) ? $params['notes'] : '' );

        $data = array(
            'special_date' => $date,
            'name'         => $name,
            'action_type'  => $type,
            'open_time'    => $open,
            'close_time'   => $close,
            'notes'        => $notes,
        );

        if ( $id > 0 ) {
            RB_DB::update( 'special_dates', $data, array( 'id' => $id ) );
        } else {
            RB_DB::insert( 'special_dates', $data );
        }

        return new WP_REST_Response( array( 'success' => true, 'message' => 'Special date saved.' ), 200 );
    }

    public static function delete_special_date( WP_REST_Request $request ) {
        $id = intval( $request->get_param( 'id' ) );
        RB_DB::delete( 'special_dates', array( 'id' => $id ) );
        return new WP_REST_Response( array( 'success' => true, 'message' => 'Deleted.' ), 200 );
    }

    public static function get_tables() {
        $tables = RB_QR_Adapter::get_canonical_tables();
        $combos = RB_DB::get_results( "SELECT * FROM " . RB_DB::table( 'table_combinations' ) . " ORDER BY id ASC" );
        return new WP_REST_Response( array(
            'success'      => true,
            'tables'       => $tables,
            'combinations' => $combos,
            'source'       => RB_QR_Adapter::is_qr_plugin_active() ? 'qr_pos' : 'standalone',
        ), 200 );
    }

    public static function save_table( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        $num    = sanitize_text_field( $params['table_number'] );
        $cap    = intval( $params['capacity'] );
        $notes  = sanitize_textarea_field( isset( $params['notes'] ) ? $params['notes'] : '' );

        if ( RB_QR_Adapter::is_qr_plugin_active() ) {
            RO_Tables::create_table( $num, $cap, $notes );
        } else {
            RB_DB::insert( 'tables', array(
                'table_number' => $num,
                'capacity'     => $cap,
                'min_capacity' => max( 1, $cap - 2 ),
                'status'       => 'available',
                'notes'        => $notes,
            ) );
        }

        return new WP_REST_Response( array( 'success' => true, 'message' => 'Table created.' ), 201 );
    }

    public static function get_waitlist() {
        $rows = RB_DB::get_results( "SELECT * FROM " . RB_DB::table( 'waitlist' ) . " ORDER BY requested_date ASC, id ASC" );
        return new WP_REST_Response( array( 'success' => true, 'waitlist' => $rows ), 200 );
    }

    public static function update_waitlist_status( WP_REST_Request $request ) {
        $id = intval( $request->get_param( 'id' ) );
        $params = $request->get_json_params();
        $status = sanitize_text_field( $params['status'] );

        RB_DB::update( 'waitlist', array( 'status' => $status ), array( 'id' => $id ) );
        return new WP_REST_Response( array( 'success' => true, 'message' => 'Waitlist status updated.' ), 200 );
    }
}
