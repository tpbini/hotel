<?php
/**
 * Decoupled Integration Adapter with Restaurant QR Ordering & POS Plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RB_QR_Adapter {

    public static function init() {
        // Listen to ordering plugin events when sessions close
        add_action( 'restaurant_ordering_session_closed', array( __CLASS__, 'on_ordering_session_closed' ), 10, 2 );
        add_action( 'restaurant_qr_session_closed', array( __CLASS__, 'on_ordering_session_closed' ), 10, 2 );
    }

    /**
     * Check if the companion QR Ordering & POS plugin is active.
     */
    public static function is_qr_plugin_active() {
        return class_exists( 'RO_Tables' ) && class_exists( 'RO_Sessions' );
    }

    /**
     * Get all physical tables from the canonical source.
     */
    public static function get_canonical_tables() {
        if ( self::is_qr_plugin_active() ) {
            $qr_tables = RO_Tables::get_all();
            $tables = array();
            foreach ( $qr_tables as $t ) {
                $tables[] = array(
                    'id'           => intval( $t['id'] ),
                    'table_number' => $t['table_number'],
                    'capacity'     => intval( $t['capacity'] ),
                    'min_capacity' => max( 1, intval( $t['capacity'] ) - 2 ),
                    'status'       => $t['status'],
                    'notes'        => isset( $t['notes'] ) ? $t['notes'] : '',
                    'source'       => 'qr_pos',
                );
            }
            return $tables;
        }

        // Fallback to standalone tables
        $db_tables = RB_DB::get_results( "SELECT * FROM " . RB_DB::table( 'tables' ) . " ORDER BY id ASC" );
        $tables = array();
        foreach ( $db_tables as $t ) {
            $tables[] = array(
                'id'           => intval( $t['id'] ),
                'table_number' => $t['table_number'],
                'capacity'     => intval( $t['capacity'] ),
                'min_capacity' => intval( $t['min_capacity'] ),
                'status'       => $t['status'],
                'notes'        => $t['notes'],
                'source'       => 'standalone',
            );
        }
        return $tables;
    }

    /**
     * Get table by canonical ID.
     */
    public static function get_table_by_id( $table_id ) {
        if ( self::is_qr_plugin_active() ) {
            $t = RO_Tables::get_by_id( $table_id );
            if ( $t ) {
                return array(
                    'id'           => intval( $t['id'] ),
                    'table_number' => $t['table_number'],
                    'capacity'     => intval( $t['capacity'] ),
                    'status'       => $t['status'],
                    'notes'        => isset( $t['notes'] ) ? $t['notes'] : '',
                    'source'       => 'qr_pos',
                );
            }
            return null;
        }

        $t = RB_DB::get_row( "SELECT * FROM " . RB_DB::table( 'tables' ) . " WHERE id = %d", $table_id );
        if ( $t ) {
            return array(
                'id'           => intval( $t['id'] ),
                'table_number' => $t['table_number'],
                'capacity'     => intval( $t['capacity'] ),
                'status'       => $t['status'],
                'notes'        => $t['notes'],
                'source'       => 'standalone',
            );
        }
        return null;
    }

    /**
     * Create live ordering session and occupy table when guest is seated.
     */
    public static function seat_guest_and_create_session( $reservation_id, $table_id ) {
        $session_id = null;

        if ( self::is_qr_plugin_active() ) {
            // Call QR ordering session generator
            $session = RO_Sessions::get_or_create_session( $table_id );
            if ( $session && isset( $session['id'] ) ) {
                $session_id = intval( $session['id'] );
            }
            // Mark table occupied in QR system
            RO_Tables::set_status( $table_id, 'occupied' );
        } else {
            // Standalone table update
            RB_DB::update( 'tables', array( 'status' => 'occupied' ), array( 'id' => intval( $table_id ) ) );
        }

        return $session_id;
    }

    /**
     * Release table back to available.
     */
    public static function release_table( $table_id ) {
        if ( self::is_qr_plugin_active() ) {
            RO_Tables::set_status( $table_id, 'available' );
        } else {
            RB_DB::update( 'tables', array( 'status' => 'available' ), array( 'id' => intval( $table_id ) ) );
        }
    }

    /**
     * Listener: When POS / QR ordering completes a session, auto-complete linked reservation.
     */
    public static function on_ordering_session_closed( $session_id, $table_id = 0 ) {
        if ( empty( $session_id ) ) {
            return;
        }

        $res = RB_DB::get_row( "SELECT * FROM " . RB_DB::table( 'reservations' ) . " WHERE linked_session_id = %d AND status = 'seated' LIMIT 1", $session_id );
        if ( $res ) {
            RB_Reservations::update_status( $res['id'], 'completed', 'Completed automatically via QR POS session settlement.' );
            do_action( 'restaurant_booking_completed', $res['id'], $session_id );
        }
    }
}
