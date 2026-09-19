<?php
/**
 * Admin Dashboard View.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;

$today = current_time( 'Y-m-d' );
$today_sales = (float) $wpdb->get_var( $wpdb->prepare(
    "SELECT SUM(amount) FROM " . RO_DB::table( 'payments' ) . " WHERE DATE(created_at) = %s",
    $today
) );

$total_orders = (int) $wpdb->get_var( $wpdb->prepare(
    "SELECT COUNT(*) FROM " . RO_DB::table( 'orders' ) . " WHERE DATE(created_at) = %s",
    $today
) );

$occupied_tables = (int) $wpdb->get_var( "SELECT COUNT(*) FROM " . RO_DB::table( 'tables' ) . " WHERE status = 'occupied'" );
$total_tables = (int) $wpdb->get_var( "SELECT COUNT(*) FROM " . RO_DB::table( 'tables' ) );
$pending_kot = (int) $wpdb->get_var( "SELECT COUNT(*) FROM " . RO_DB::table( 'orders' ) . " WHERE status IN ('NEW', 'ACCEPTED', 'PREPARING')" );

$currency = get_option( 'ro_currency_symbol', '$' );
$restaurant_name = get_option( 'ro_restaurant_name', 'Grand Bistro & Grill' );

$recent_orders = RO_DB::get_results( "SELECT o.*, t.table_number FROM " . RO_DB::table( 'orders' ) . " o JOIN " . RO_DB::table( 'tables' ) . " t ON o.table_id = t.id ORDER BY o.id DESC LIMIT 8" );
?>
<div class="wrap ro-wrap">
    <div class="ro-header-banner">
        <div>
            <h1>🍽️ <?php echo esc_html( $restaurant_name ); ?></h1>
            <p>Production-Grade Restaurant QR Ordering, KDS & Counter POS Operations Platform</p>
        </div>
        <div class="ro-launcher-links">
            <a href="<?php echo esc_url( home_url( '/restaurant-pos/' ) ); ?>" target="_blank" class="ro-launch-btn">
                <span>⚡ Open Counter POS</span>
            </a>
            <a href="<?php echo esc_url( home_url( '/restaurant-kds/' ) ); ?>" target="_blank" class="ro-launch-btn secondary">
                <span>🔥 Open Kitchen KDS</span>
            </a>
            <a href="<?php echo esc_url( home_url( '/restaurant-waiter/' ) ); ?>" target="_blank" class="ro-launch-btn secondary">
                <span>📱 Waiter Mode</span>
            </a>
        </div>
    </div>

    <!-- KPIs -->
    <div class="ro-kpi-grid">
        <div class="ro-kpi-card">
            <div class="ro-kpi-label">Today's Total Sales</div>
            <div class="ro-kpi-value"><?php echo esc_html( $currency . number_format( $today_sales, 2 ) ); ?></div>
        </div>
        <div class="ro-kpi-card">
            <div class="ro-kpi-label">Today's Orders</div>
            <div class="ro-kpi-value"><?php echo esc_html( $total_orders ); ?></div>
        </div>
        <div class="ro-kpi-card">
            <div class="ro-kpi-label">Active Tables</div>
            <div class="ro-kpi-value"><?php echo esc_html( "{$occupied_tables} / {$total_tables}" ); ?></div>
        </div>
        <div class="ro-kpi-card">
            <div class="ro-kpi-label">Pending Kitchen KOT</div>
            <div class="ro-kpi-value" style="color:#FF5A1F;"><?php echo esc_html( $pending_kot ); ?></div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="ro-card">
        <div class="ro-card-title">
            <span>Recent Live Orders</span>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=ro-reports' ) ); ?>" style="font-size:0.85rem; text-decoration:none;">View Full Reports ➔</a>
        </div>
        <table class="ro-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Table</th>
                    <th>Source</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty( $recent_orders ) ) : ?>
                    <?php foreach ( $recent_orders as $ord ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $ord['order_number'] ); ?></strong></td>
                            <td><?php echo esc_html( $ord['table_number'] ); ?></td>
                            <td><span style="background:#E2E8F0; padding:3px 8px; border-radius:12px; font-size:0.75rem; font-weight:700;"><?php echo esc_html( $ord['source'] ); ?></span></td>
                            <td><strong><?php echo esc_html( $currency . number_format( (float) $ord['total_amount'], 2 ) ); ?></strong></td>
                            <td>
                                <span style="background:<?php echo 'NEW' === $ord['status'] ? '#DBEAFE' : ( 'PAID' === $ord['status'] ? '#D1FAE5' : '#FEF3C7' ); ?>; color:<?php echo 'NEW' === $ord['status'] ? '#1E40AF' : ( 'PAID' === $ord['status'] ? '#065F46' : '#92400E' ); ?>; padding:4px 10px; border-radius:14px; font-weight:800; font-size:0.75rem;">
                                    <?php echo esc_html( $ord['status'] ); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html( date( 'h:i A', strtotime( $ord['created_at'] ) ) ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" style="text-align:center; color:#94A3B8; padding:30px;">No live orders yet. Scan any Table QR code or use Waiter Mode to create test orders!</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
