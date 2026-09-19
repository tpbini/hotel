<?php
/**
 * User Roles & Capabilities Management.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RB_Roles {

    public static function init() {
        self::add_caps_to_role( 'administrator' );
        self::add_caps_to_role( 'editor' );
        self::add_caps_to_role( 'shop_manager' );
        self::add_caps_to_role( 'ro_manager' );
        self::add_caps_to_role( 'ro_staff' );
    }

    public static function add_caps_to_role( $role_name ) {
        $role = get_role( $role_name );
        if ( $role ) {
            $role->add_cap( 'manage_restaurant_bookings' );
            $role->add_cap( 'view_restaurant_bookings' );
        }
    }

    public static function user_can_manage() {
        return current_user_can( 'manage_restaurant_bookings' ) || current_user_can( 'manage_options' );
    }

    public static function user_can_view() {
        return current_user_can( 'view_restaurant_bookings' ) || current_user_can( 'manage_restaurant_bookings' ) || current_user_can( 'manage_options' );
    }
}
