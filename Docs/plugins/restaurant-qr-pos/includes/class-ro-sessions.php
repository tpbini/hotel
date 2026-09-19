<?php
/**
 * Table Session Lifecycle & Aggregation Logic.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RO_Sessions {

    public static function get_active_session_by_table( $table_id ) {
        $session = RO_DB::get_row( "SELECT * FROM " . RO_DB::table( 'table_sessions' ) . " WHERE table_id = %d AND status = 'active' ORDER BY id DESC LIMIT 1", $table_id );
        if ( $session ) {
            $session['subtotal']        = (float) $session['subtotal'];
            $session['tax_amount']      = (float) $session['tax_amount'];
            $session['discount_amount'] = (float) $session['discount_amount'];
            $session['total_amount']    = (float) $session['total_amount'];
        }
        return $session;
    }

    public static function get_by_id( $id ) {
        $session = RO_DB::get_row( "SELECT * FROM " . RO_DB::table( 'table_sessions' ) . " WHERE id = %d", $id );
        if ( $session ) {
            $session['subtotal']        = (float) $session['subtotal'];
            $session['tax_amount']      = (float) $session['tax_amount'];
            $session['discount_amount'] = (float) $session['discount_amount'];
            $session['total_amount']    = (float) $session['total_amount'];
            $session['orders']          = RO_Orders::get_orders_by_session( $session['id'] );
            $session['table']           = RO_DB::get_row( "SELECT * FROM " . RO_DB::table( 'tables' ) . " WHERE id = %d", $session['table_id'] );
        }
        return $session;
    }

    public static function get_by_code( $code ) {
        $session = RO_DB::get_row( "SELECT * FROM " . RO_DB::table( 'table_sessions' ) . " WHERE session_code = %s", sanitize_text_field( $code ) );
        if ( $session ) {
            $session['orders'] = RO_Orders::get_orders_by_session( $session['id'] );
            $session['table']  = RO_DB::get_row( "SELECT * FROM " . RO_DB::table( 'tables' ) . " WHERE id = %d", $session['table_id'] );
        }
        return $session;
    }

    /**
     * Get or create active session for a given table.
     */
    public static function get_or_create_session( $table_id ) {
        $session = self::get_active_session_by_table( $table_id );
        if ( $session ) {
            return $session;
        }

        // Generate unique session code TS-XXXXX
        $code = 'TS-' . wp_rand( 10000, 99999 );
        $session_id = RO_DB::insert( 'table_sessions', array(
            'table_id'        => intval( $table_id ),
            'session_code'    => $code,
            'status'          => 'active',
            'subtotal'        => 0.00,
            'tax_amount'      => 0.00,
            'discount_amount' => 0.00,
            'total_amount'    => 0.00,
            'opened_at'       => current_time( 'mysql' ),
        ) );

        // Mark table as occupied
        RO_Tables::set_status( $table_id, 'occupied' );

        return self::get_by_id( $session_id );
    }

    /**
     * Recalculate session totals from all non-cancelled orders.
     */
    public static function recalculate_session( $session_id ) {
        $orders = RO_DB::get_results( "SELECT * FROM " . RO_DB::table( 'orders' ) . " WHERE session_id = %d AND status != 'CANCELLED'", $session_id );

        $subtotal = 0.00;
        $tax = 0.00;
        $discount = 0.00;
        $total = 0.00;

        foreach ( $orders as $o ) {
            $subtotal += (float) $o['subtotal'];
            $tax      += (float) $o['tax_amount'];
            $discount += (float) $o['discount_amount'];
            $total    += (float) $o['total_amount'];
        }

        // Apply session-level discount if any
        $session = RO_DB::get_row( "SELECT * FROM " . RO_DB::table( 'table_sessions' ) . " WHERE id = %d", $session_id );
        if ( $session && (float) $session['discount_amount'] > 0 ) {
            $discount = (float) $session['discount_amount'];
            $total = max( 0, ( $subtotal + $tax ) - $discount );
        }

        RO_DB::update( 'table_sessions', array(
            'subtotal'        => $subtotal,
            'tax_amount'      => $tax,
            'discount_amount' => $discount,
            'total_amount'    => $total,
        ), array( 'id' => intval( $session_id ) ) );

        return array(
            'subtotal'        => $subtotal,
            'tax_amount'      => $tax,
            'discount_amount' => $discount,
            'total_amount'    => $total,
        );
    }

    /**
     * Close Table Session
     */
    public static function close_session( $session_id ) {
        $session = self::get_by_id( $session_id );
        if ( ! $session ) {
            return false;
        }

        // Update session status to closed
        RO_DB::update( 'table_sessions', array(
            'status'    => 'closed',
            'closed_at' => current_time( 'mysql' ),
        ), array( 'id' => intval( $session_id ) ) );

        // Update all active orders in session to PAID/CLOSED
        $orders = RO_DB::get_results( "SELECT id FROM " . RO_DB::table( 'orders' ) . " WHERE session_id = %d AND status NOT IN ('CANCELLED', 'PAID')", $session_id );
        foreach ( $orders as $o ) {
            RO_Orders::set_order_status( $o['id'], 'PAID', 'Session closed after payment' );
        }

        // Release Table back to available
        RO_Tables::set_status( $session['table_id'], 'available' );

        // Log audit event
        RO_DB::log_event( 0, $session_id, 'SESSION_CLOSED', 'Table ' . $session['table_id'] . ' session closed and released.' );

        return true;
    }
}
