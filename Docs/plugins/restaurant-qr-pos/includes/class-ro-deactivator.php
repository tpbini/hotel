<?php
/**
 * Fired during plugin deactivation.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RO_Deactivator {

    public static function deactivate() {
        flush_rewrite_rules();
    }
}
