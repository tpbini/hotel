<?php
/**
 * Plugin Deactivator.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RB_Deactivator {
    public static function deactivate() {
        flush_rewrite_rules();
    }
}
