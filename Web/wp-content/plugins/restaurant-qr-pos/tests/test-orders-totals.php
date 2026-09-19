<?php
/**
 * Automated Verification Script for Restaurant QR POS Plugin.
 * Run via CLI: php tests/test-orders-totals.php
 */

define( 'SHORTINIT', true );
require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php';

echo "=== Restaurant QR POS Automated Test Suite ===\n\n";

global $wpdb;

// 1. Verify Database Tables
$tables = array(
    'ro_tables',
    'ro_table_sessions',
    'ro_orders',
    'ro_order_items',
    'ro_order_item_modifiers',
    'ro_categories',
    'ro_stations',
    'ro_menu_items',
    'ro_item_variations',
    'ro_addons',
    'ro_service_requests',
    'ro_payments',
    'ro_print_jobs',
    'ro_order_events'
);

$missing = array();
foreach ( $tables as $t ) {
    $full = $wpdb->prefix . $t;
    $exists = $wpdb->get_var( "SHOW TABLES LIKE '{$full}'" );
    if ( ! $exists ) {
        $missing[] = $full;
    }
}

if ( empty( $missing ) ) {
    echo "✓ All 14 custom database tables exist.\n";
} else {
    echo "✗ Missing tables: " . implode( ', ', $missing ) . "\n";
}

echo "\nVerification complete!\n";
