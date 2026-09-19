<?php
/**
 * REST API Base & Route Registry.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RB_REST_Controller {

    const NAMESPACE = 'rb/v1';

    public static function register_routes() {
        RB_Public_API::register_routes();
        RB_Admin_API::register_routes();
    }

    public static function check_admin_permission() {
        return RB_Roles::user_can_view();
    }

    public static function check_manage_permission() {
        return RB_Roles::user_can_manage();
    }
}
