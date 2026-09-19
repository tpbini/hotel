<?php
/**
 * Reports & Sales Analytics View.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;
$currency = get_option( 'ro_currency_symbol', '$' );

// Sales Breakdown
$payments = RO_DB::get_results( "SELECT p.*, s.session_code, t.table_number FROM " . RO_DB::table( 'payments' ) . " p JOIN " . RO_DB::table( 'table_sessions' ) . " s ON p.session_id = s.id JOIN " . RO_DB::table( 'tables' ) . " t ON s.table_id = t.id ORDER BY p.id DESC LIMIT 20" );

// Payment Methods Summary
$method_summary = RO_DB::get_results( "SELECT payment_method, COUNT(*) as count, SUM(amount) as total FROM " . RO_DB::table( 'payments' ) . " GROUP BY payment_method" );

// Top Items
$top_dishes = RO_DB::get_results( "SELECT item_name, SUM(quantity) as qty, SUM(total_price) as rev FROM " . RO_DB::table( 'order_items' ) . " GROUP BY item_name ORDER BY qty DESC LIMIT 8" );
?>
<div class="wrap ro-wrap">
    <div class="ro-header-banner">
        <div>
            <h1>📊 Restaurant Sales & Performance Reports</h1>
            <p>Review completed invoices, payment settlement channels, and top menu favorites.</p>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px; margin-bottom:24px;">
        <div class="ro-card">
            <h3 class="ro-card-title">Recent Settled Invoices</h3>
            <table class="ro-table">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Table</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( ! empty( $payments ) ) : ?>
                        <?php foreach ( $payments as $pay ) : ?>
                            <tr>
                                <td><strong><?php echo esc_html( $pay['invoice_number'] ); ?></strong></td>
                                <td><?php echo esc_html( $pay['table_number'] ); ?></td>
                                <td><span style="background:#E2E8F0; padding:2px 8px; border-radius:10px; font-size:0.78rem; font-weight:700;"><?php echo esc_html( $pay['payment_method'] ); ?></span></td>
                                <td><strong style="color:#059669;"><?php echo esc_html( $currency . number_format( (float) $pay['amount'], 2 ) ); ?></strong></td>
                                <td><?php echo esc_html( date( 'M d, Y h:i A', strtotime( $pay['created_at'] ) ) ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="5" style="text-align:center; color:#94A3B8; padding:30px;">No settlements recorded yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div>
            <div class="ro-card">
                <h3 class="ro-card-title">Payment Channels</h3>
                <table class="ro-table">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th>Count</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $method_summary as $ms ) : ?>
                            <tr>
                                <td><strong><?php echo esc_html( $ms['payment_method'] ); ?></strong></td>
                                <td><?php echo intval( $ms['count'] ); ?></td>
                                <td><strong><?php echo esc_html( $currency . number_format( (float) $ms['total'], 2 ) ); ?></strong></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="ro-card">
                <h3 class="ro-card-title">Top Selling Dishes</h3>
                <table class="ro-table">
                    <thead>
                        <tr>
                            <th>Dish</th>
                            <th>Qty</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $top_dishes as $td ) : ?>
                            <tr>
                                <td><strong><?php echo esc_html( $td['item_name'] ); ?></strong></td>
                                <td><?php echo intval( $td['qty'] ); ?></td>
                                <td><?php echo esc_html( $currency . number_format( (float) $td['rev'], 2 ) ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
