<?php
/**
 * Safe Database Query Wrapper & Event Logger.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RB_DB {

    public static function table( $name ) {
        global $wpdb;
        return $wpdb->prefix . 'rb_' . $name;
    }

    public static function get_row( $query, ...$args ) {
        global $wpdb;
        if ( ! empty( $args ) ) {
            $query = $wpdb->prepare( $query, ...$args );
        }
        return $wpdb->get_row( $query, ARRAY_A );
    }

    public static function get_results( $query, ...$args ) {
        global $wpdb;
        if ( ! empty( $args ) ) {
            $query = $wpdb->prepare( $query, ...$args );
        }
        return $wpdb->get_results( $query, ARRAY_A );
    }

    public static function get_var( $query, ...$args ) {
        global $wpdb;
        if ( ! empty( $args ) ) {
            $query = $wpdb->prepare( $query, ...$args );
        }
        return $wpdb->get_var( $query );
    }

    public static function insert( $table, $data ) {
        global $wpdb;
        $table_name = self::table( $table );
        $result = $wpdb->insert( $table_name, $data );
        if ( false === $result ) {
            return false;
        }
        return $wpdb->insert_id;
    }

    public static function update( $table, $data, $where ) {
        global $wpdb;
        $table_name = self::table( $table );
        return $wpdb->update( $table_name, $data, $where );
    }

    public static function delete( $table, $where ) {
        global $wpdb;
        $table_name = self::table( $table );
        return $wpdb->delete( $table_name, $where );
    }

    /**
     * Audit Event Logger
     */
    public static function log_event( $reservation_id, $event_type, $description, $performed_by = '' ) {
        if ( empty( $performed_by ) ) {
            $user = wp_get_current_user();
            $performed_by = ( $user && $user->exists() ) ? $user->user_login : 'customer';
        }

        return self::insert( 'events', array(
            'reservation_id' => intval( $reservation_id ),
            'event_type'     => sanitize_text_field( $event_type ),
            'description'    => sanitize_textarea_field( $description ),
            'performed_by'   => sanitize_text_field( $performed_by ),
            'created_at'     => self::now(),
        ) );
    }

    public static function now() {
        return RB_i18n::now_uk();
    }
}
