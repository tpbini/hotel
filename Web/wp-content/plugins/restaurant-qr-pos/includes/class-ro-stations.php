<?php
/**
 * Kitchen Preparation Stations Management.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RO_Stations {

    public static function get_all() {
        return RO_DB::get_results( "SELECT * FROM " . RO_DB::table( 'stations' ) . " ORDER BY id ASC" );
    }

    public static function get_active() {
        return RO_DB::get_results( "SELECT * FROM " . RO_DB::table( 'stations' ) . " WHERE is_active = 1 ORDER BY id ASC" );
    }

    public static function get_by_id( $id ) {
        return RO_DB::get_row( "SELECT * FROM " . RO_DB::table( 'stations' ) . " WHERE id = %d", $id );
    }

    public static function create( $name, $code ) {
        $slug = sanitize_title( ! empty( $code ) ? $code : $name );
        return RO_DB::insert( 'stations', array(
            'name'      => sanitize_text_field( $name ),
            'code'      => $slug,
            'is_active' => 1,
        ) );
    }

    public static function update( $id, $name, $code, $is_active = 1 ) {
        $slug = sanitize_title( ! empty( $code ) ? $code : $name );
        return RO_DB::update( 'stations', array(
            'name'      => sanitize_text_field( $name ),
            'code'      => $slug,
            'is_active' => $is_active ? 1 : 0,
        ), array( 'id' => intval( $id ) ) );
    }

    public static function delete( $id ) {
        return RO_DB::delete( 'stations', array( 'id' => intval( $id ) ) );
    }
}
