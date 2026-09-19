<?php
/**
 * Public REST API Endpoints for Booking Widget & Guest Management.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RB_Public_API {

    public static function register_routes() {
        $ns = RB_REST_Controller::NAMESPACE;

        // 1. Get Availability
        register_rest_route( $ns, '/availability', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_availability' ),
            'permission_callback' => '__return_true',
            'args'                => array(
                'date'       => array( 'required' => true, 'type' => 'string' ),
                'party_size' => array( 'required' => false, 'type' => 'integer', 'default' => 2 ),
            ),
        ) );

        // 2. Create Reservation
        register_rest_route( $ns, '/reservations', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'create_reservation' ),
            'permission_callback' => '__return_true',
        ) );

        // 3. Get Reservation by Guest Token
        register_rest_route( $ns, '/reservations/(?P<token>[a-zA-Z0-9_-]+)', array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_reservation' ),
            'permission_callback' => '__return_true',
        ) );

        // 4. Guest Self-Service Cancel
        register_rest_route( $ns, '/reservations/(?P<token>[a-zA-Z0-9_-]+)/cancel', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'cancel_reservation' ),
            'permission_callback' => '__return_true',
        ) );

        // 5. Join Waitlist
        register_rest_route( $ns, '/waitlist', array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'join_waitlist' ),
            'permission_callback' => '__return_true',
        ) );
    }

    public static function get_availability( WP_REST_Request $request ) {
        $date = sanitize_text_field( $request->get_param( 'date' ) );
        $party_size = max( 1, intval( $request->get_param( 'party_size' ) ) );

        $result = RB_Availability::get_slots_for_date( $date, $party_size );
        if ( ! $result['success'] ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => $result['message'],
            ), 400 );
        }

        return new WP_REST_Response( $result, 200 );
    }

    public static function create_reservation( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        if ( empty( $params ) ) {
            $params = $request->get_params();
        }

        $res = RB_Reservations::create_reservation( $params );
        if ( is_wp_error( $res ) ) {
            return new WP_REST_Response( array(
                'success'      => false,
                'code'         => $res->get_error_code(),
                'message'      => $res->get_error_message(),
                'nearby_slots' => $res->get_error_data() && isset( $res->get_error_data()['nearby_slots'] ) ? $res->get_error_data()['nearby_slots'] : array(),
            ), 400 );
        }

        return new WP_REST_Response( array(
            'success'     => true,
            'message'     => __( 'Your reservation has been confirmed successfully!', 'restaurant-booking' ),
            'reservation' => $res,
        ), 201 );
    }

    public static function get_reservation( WP_REST_Request $request ) {
        $token = sanitize_text_field( $request->get_param( 'token' ) );
        $res = RB_Reservations::get_by_token( $token );

        if ( ! $res ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => __( 'Reservation not found or token has expired.', 'restaurant-booking' ),
            ), 404 );
        }

        // Return sanitised guest view
        $cutoff_hours = intval( get_option( 'rb_cancellation_cutoff_hours', 2 ) );
        $booking_ts   = strtotime( $res['booking_date'] . ' ' . $res['start_time'] );
        $cutoff_ts    = $booking_ts - ( $cutoff_hours * 3600 );
        $can_cancel   = ( RB_i18n::get_now()->getTimestamp() <= $cutoff_ts ) && in_array( $res['status'], array( 'pending', 'confirmed' ), true );

        return new WP_REST_Response( array(
            'success'     => true,
            'reservation' => $res,
            'can_cancel'  => $can_cancel,
            'cutoff_hours'=> $cutoff_hours,
            'restaurant'  => array(
                'name'    => get_option( 'rb_restaurant_name', 'The Crown & Thistle' ),
                'phone'   => get_option( 'rb_restaurant_phone', '020 7946 0912' ),
                'address' => get_option( 'rb_restaurant_address', 'London, UK' ),
            ),
        ), 200 );
    }

    public static function cancel_reservation( WP_REST_Request $request ) {
        $token = sanitize_text_field( $request->get_param( 'token' ) );
        $params = $request->get_json_params();
        $reason = isset( $params['reason'] ) ? sanitize_text_field( $params['reason'] ) : '';

        $result = RB_Reservations::cancel_by_guest( $token, $reason );
        if ( is_wp_error( $result ) ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => $result->get_error_message(),
            ), 400 );
        }

        return new WP_REST_Response( array(
            'success'     => true,
            'message'     => __( 'Your reservation has been cancelled successfully.', 'restaurant-booking' ),
            'reservation' => $result,
        ), 200 );
    }

    public static function join_waitlist( WP_REST_Request $request ) {
        $params = $request->get_json_params();
        if ( empty( $params ) ) {
            $params = $request->get_params();
        }

        $name       = sanitize_text_field( isset( $params['customer_name'] ) ? $params['customer_name'] : '' );
        $phone_raw  = sanitize_text_field( isset( $params['phone'] ) ? $params['phone'] : '' );
        $email      = sanitize_email( isset( $params['email'] ) ? $params['email'] : '' );
        $party_size = max( 1, intval( isset( $params['party_size'] ) ? $params['party_size'] : 2 ) );
        $date       = sanitize_text_field( isset( $params['requested_date'] ) ? $params['requested_date'] : '' );
        $time       = sanitize_text_field( isset( $params['preferred_time'] ) ? $params['preferred_time'] : '19:00:00' );
        $notes      = sanitize_textarea_field( isset( $params['notes'] ) ? $params['notes'] : '' );

        if ( empty( $name ) || empty( $phone_raw ) || empty( $email ) || empty( $date ) ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => __( 'Please fill in all contact details to join the waitlist.', 'restaurant-booking' ),
            ), 400 );
        }

        $phone = RB_i18n::normalize_phone_uk( $phone_raw );

        $waitlist_id = RB_DB::insert( 'waitlist', array(
            'customer_name'  => $name,
            'phone'          => $phone,
            'email'          => $email,
            'party_size'     => $party_size,
            'requested_date' => $date,
            'preferred_time' => substr( $time, 0, 5 ) . ':00',
            'status'         => 'waiting',
            'notes'          => $notes,
            'created_at'     => RB_i18n::now_uk(),
        ) );

        if ( ! $waitlist_id ) {
            return new WP_REST_Response( array(
                'success' => false,
                'message' => __( 'Could not save waitlist request.', 'restaurant-booking' ),
            ), 500 );
        }

        return new WP_REST_Response( array(
            'success' => true,
            'message' => __( 'You have been added to the waitlist! We will contact you if a table becomes available.', 'restaurant-booking' ),
        ), 201 );
    }
}
