<?php
/**
 * Database helper wrapper for Restaurant QR POS custom tables.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RO_DB {

    public static function table( $name ) {
        global $wpdb;
        return $wpdb->prefix . 'ro_' . $name;
    }

    public static function get_results( $query, ...$args ) {
        global $wpdb;
        if ( ! empty( $args ) ) {
            $query = $wpdb->prepare( $query, ...$args );
        }
        $res = $wpdb->get_results( $query, ARRAY_A );
        return is_array( $res ) ? $res : array();
    }

    public static function get_row( $query, ...$args ) {
        global $wpdb;
        if ( ! empty( $args ) ) {
            $query = $wpdb->prepare( $query, ...$args );
        }
        $res = $wpdb->get_row( $query, ARRAY_A );
        return is_array( $res ) ? $res : null;
    }

    public static function get_var( $query, ...$args ) {
        global $wpdb;
        if ( ! empty( $args ) ) {
            $query = $wpdb->prepare( $query, ...$args );
        }
        return $wpdb->get_var( $query );
    }

    public static function insert( $table_name, $data, $format = null ) {
        global $wpdb;
        $table = self::table( $table_name );
        $result = $wpdb->insert( $table, $data, $format );
        if ( false === $result ) {
            return false;
        }
        return $wpdb->insert_id;
    }

    public static function update( $table_name, $data, $where, $format = null, $where_format = null ) {
        global $wpdb;
        $table = self::table( $table_name );
        return $wpdb->update( $table, $data, $where, $format, $where_format );
    }

    public static function delete( $table_name, $where, $where_format = null ) {
        global $wpdb;
        $table = self::table( $table_name );
        return $wpdb->delete( $table, $where, $where_format );
    }

    public static function log_event( $order_id, $session_id, $event_type, $details = '' ) {
        $user_id = get_current_user_id();
        $user = wp_get_current_user();
        $role = ! empty( $user->roles ) ? $user->roles[0] : ( is_user_logged_in() ? 'user' : 'guest_qr' );

        self::insert( 'order_events', array(
            'order_id'   => $order_id,
            'session_id' => $session_id,
            'event_type' => sanitize_text_field( $event_type ),
            'actor_id'   => $user_id ? $user_id : null,
            'actor_role' => $role,
            'details'    => is_array( $details ) ? wp_json_encode( $details ) : sanitize_text_field( $details ),
            'created_at' => current_time( 'mysql' ),
        ) );
    }
}
