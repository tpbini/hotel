<?php
/**
 * Table & Secure QR Token Management.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RO_Tables {

    public static function get_all() {
        $tables = RO_DB::get_results( "SELECT * FROM " . RO_DB::table( 'tables' ) . " ORDER BY id ASC" );
        foreach ( $tables as &$table ) {
            $table['active_session'] = RO_Sessions::get_active_session_by_table( $table['id'] );
            $table['qr_url']         = self::get_qr_url( $table['qr_token'] );
        }
        return $tables;
    }

    public static function get_by_id( $id ) {
        $table = RO_DB::get_row( "SELECT * FROM " . RO_DB::table( 'tables' ) . " WHERE id = %d", $id );
        if ( $table ) {
            $table['active_session'] = RO_Sessions::get_active_session_by_table( $table['id'] );
            $table['qr_url']         = self::get_qr_url( $table['qr_token'] );
        }
        return $table;
    }

    public static function get_by_token( $token ) {
        $table = RO_DB::get_row( "SELECT * FROM " . RO_DB::table( 'tables' ) . " WHERE qr_token = %s", sanitize_text_field( $token ) );
        if ( $table ) {
            $table['active_session'] = RO_Sessions::get_active_session_by_table( $table['id'] );
            $table['qr_url']         = self::get_qr_url( $table['qr_token'] );
        }
        return $table;
    }

    public static function generate_token( $prefix = 'TBL' ) {
        return strtoupper( sanitize_key( $prefix ) ) . '-' . wp_generate_password( 10, false, false );
    }

    public static function get_qr_url( $token ) {
        return home_url( '/order/t/' . $token );
    }

    public static function create_table( $table_number, $capacity = 4, $notes = '' ) {
        $token = self::generate_token( 'T' . preg_replace( '/[^0-9]/', '', $table_number ) );
        return RO_DB::insert( 'tables', array(
            'table_number' => sanitize_text_field( $table_number ),
            'qr_token'     => $token,
            'capacity'     => intval( $capacity ),
            'status'       => 'available',
            'notes'        => sanitize_textarea_field( $notes ),
        ) );
    }

    public static function update_table( $id, $table_number, $capacity = 4, $status = 'available', $notes = '' ) {
        return RO_DB::update( 'tables', array(
            'table_number' => sanitize_text_field( $table_number ),
            'capacity'     => intval( $capacity ),
            'status'       => sanitize_text_field( $status ),
            'notes'        => sanitize_textarea_field( $notes ),
        ), array( 'id' => intval( $id ) ) );
    }

    public static function regenerate_qr( $id ) {
        $table = self::get_by_id( $id );
        if ( ! $table ) {
            return false;
        }
        $new_token = self::generate_token( 'T' . preg_replace( '/[^0-9]/', '', $table['table_number'] ) );
        RO_DB::update( 'tables', array(
            'qr_token' => $new_token,
        ), array( 'id' => intval( $id ) ) );

        return $new_token;
    }

    public static function set_status( $id, $status ) {
        return RO_DB::update( 'tables', array(
            'status' => sanitize_text_field( $status ),
        ), array( 'id' => intval( $id ) ) );
    }

    public static function delete_table( $id ) {
        return RO_DB::delete( 'tables', array( 'id' => intval( $id ) ) );
    }
}
