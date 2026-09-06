<?php
/**
 * Redirect root nav-menus.php request to WordPress admin nav-menus.php
 */
require_once __DIR__ . '/wp-load.php';
wp_safe_redirect( admin_url( 'nav-menus.php' ) );
exit;
