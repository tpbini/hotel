<?php
/**
 * Roles & Capabilities Handler for Restaurant Operations
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RO_Roles {

    public static function init() {
        self::add_capabilities();
        self::register_custom_roles();
    }

    public static function add_capabilities() {
        $admin = get_role( 'administrator' );
        if ( $admin ) {
            $admin->add_cap( 'ro_manage_restaurant' );
            $admin->add_cap( 'ro_counter_pos' );
            $admin->add_cap( 'ro_kitchen_staff' );
            $admin->add_cap( 'ro_waiter_mode' );
            $admin->add_cap( 'ro_view_reports' );
        }
    }

    public static function register_custom_roles() {
        // Cashier Role
        add_role( 'ro_cashier', __( 'Restaurant Cashier', 'restaurant-qr-pos' ), array(
            'read'              => true,
            'ro_counter_pos'    => true,
            'ro_view_reports'   => false,
        ) );

        // Kitchen Staff Role
        add_role( 'ro_kitchen', __( 'Kitchen Staff', 'restaurant-qr-pos' ), array(
            'read'              => true,
            'ro_kitchen_staff'  => true,
        ) );

        // Waiter Role
        add_role( 'ro_waiter', __( 'Restaurant Waiter', 'restaurant-qr-pos' ), array(
            'read'              => true,
            'ro_waiter_mode'    => true,
        ) );

        // Restaurant Manager Role
        add_role( 'ro_manager', __( 'Restaurant Manager', 'restaurant-qr-pos' ), array(
            'read'                  => true,
            'ro_manage_restaurant'  => true,
            'ro_counter_pos'        => true,
            'ro_kitchen_staff'      => true,
            'ro_waiter_mode'        => true,
            'ro_view_reports'       => true,
        ) );
    }
}
