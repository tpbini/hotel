<?php
/**
 * Customer Service Requests (Call Waiter / Request Bill) with Cooldown Protection.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RO_Service {

    public static function create_request( $table_id, $type = 'call_waiter', $notes = '' ) {
        $table = RO_Tables::get_by_id( $table_id );
        if ( ! $table ) {
            return new WP_Error( 'invalid_table', __( 'Invalid table.', 'restaurant-qr-pos' ) );
        }

        // Debounce / Cooldown Check: Prevent repeated requests within 60 seconds
        $recent = RO_DB::get_var(
            "SELECT COUNT(*) FROM " . RO_DB::table( 'service_requests' ) . "
             WHERE table_id = %d AND type = %s AND status = 'pending' AND created_at >= %s",
            $table_id,
            sanitize_text_field( $type ),
            date( 'Y-m-d H:i:s', current_time( 'timestamp' ) - 60 )
        );

        if ( $recent > 0 ) {
            return array(
                'success' => true,
                'cooldown' => true,
                'message' => __( 'Your request was already received! Staff has been notified.', 'restaurant-qr-pos' ),
            );
        }

        $session = RO_Sessions::get_active_session_by_table( $table_id );
        $session_id = $session ? $session['id'] : null;

        $id = RO_DB::insert( 'service_requests', array(
            'table_id'   => intval( $table_id ),
            'session_id' => $session_id,
            'type'       => sanitize_text_field( $type ),
            'status'     => 'pending',
            'notes'      => sanitize_textarea_field( $notes ),
            'created_at' => current_time( 'mysql' ),
        ) );

        return array(
            'success' => true,
            'request_id' => $id,
            'message' => 'call_waiter' === $type ? __( 'A waiter has been called to your table.', 'restaurant-qr-pos' ) : __( 'Your bill request has been sent to the cashier.', 'restaurant-qr-pos' ),
        );
    }

    public static function get_pending_requests() {
        return RO_DB::get_results(
            "SELECT sr.*, t.table_number FROM " . RO_DB::table( 'service_requests' ) . " sr
             JOIN " . RO_DB::table( 'tables' ) . " t ON sr.table_id = t.id
             WHERE sr.status = 'pending'
             ORDER BY sr.id DESC"
        );
    }

    public static function resolve_request( $request_id ) {
        return RO_DB::update( 'service_requests', array(
            'status' => 'resolved',
        ), array( 'id' => intval( $request_id ) ) );
    }
}
