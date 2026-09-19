<?php
/**
 * Admin Tables & Combinations View.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tables = RB_QR_Adapter::get_canonical_tables();
$combos = RB_DB::get_results( "SELECT * FROM " . RB_DB::table( 'table_combinations' ) . " ORDER BY id ASC" );
$qr_active = RB_QR_Adapter::is_qr_plugin_active();
?>
<div class="wrap rb-wrap">
    <div class="rb-header-bar">
        <div>
            <h1>🏛️ Tables & Table Combinations</h1>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">
                Physical table inventory and combinable seating definitions for larger dining parties.
            </p>
        </div>
        <div class="rb-header-actions">
            <?php if ( ! $qr_active ) : ?>
                <button class="rb-btn rb-btn-primary" id="rb-btn-add-table">➕ Add Physical Table</button>
            <?php endif; ?>
        </div>
    </div>

    <?php if ( $qr_active ) : ?>
        <div class="rb-alert rb-alert-info">
            <strong>ℹ️ Canonical Inventory:</strong> Physical tables are centrally managed via the <em>Restaurant QR Ordering & POS</em> plugin to ensure consistent QR codes and seating across the restaurant.
        </div>
    <?php endif; ?>

    <!-- Physical Tables Grid -->
    <div class="rb-card">
        <div class="rb-card-header">
            <h2>Physical Table Inventory (<?php echo count( $tables ); ?> Tables)</h2>
        </div>
        <table class="rb-table">
            <thead>
                <tr>
                    <th>Table Number</th>
                    <th>Max Capacity</th>
                    <th>Min Covers</th>
                    <th>Current Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $tables as $t ) : ?>
                    <tr>
                        <td><strong style="font-size: 15px; color: #0284c7;">Table <?php echo esc_html( $t['table_number'] ); ?></strong></td>
                        <td><span style="font-weight: 700; font-size: 14px;"><?php echo esc_html( $t['capacity'] ); ?></span> Persons</td>
                        <td><?php echo esc_html( $t['min_capacity'] ); ?> Persons</td>
                        <td>
                            <span class="rb-status <?php echo ( $t['status'] === 'occupied' ) ? 'rb-status-seated' : 'rb-status-confirmed'; ?>">
                                <?php echo esc_html( ucfirst( $t['status'] ) ); ?>
                            </span>
                        </td>
                        <td><?php echo esc_html( $t['notes'] ? $t['notes'] : '-' ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Combinable Tables -->
    <div class="rb-card">
        <div class="rb-card-header">
            <h2>Configured Table Combinations</h2>
        </div>
        <p style="font-size: 13px; color: #64748b; margin-top: 0;">
            When no single table can accommodate a large party (e.g. 8+ guests), the availability engine automatically evaluates these configured combinations.
        </p>
        <table class="rb-table">
            <thead>
                <tr>
                    <th>Combination Name</th>
                    <th>Primary Table</th>
                    <th>Combined With</th>
                    <th>Total Combined Capacity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty( $combos ) ) : ?>
                    <?php foreach ( $combos as $c ) : 
                        $t1 = RB_QR_Adapter::get_table_by_id( $c['primary_table_id'] );
                        $t2 = RB_QR_Adapter::get_table_by_id( $c['combined_table_id'] );
                    ?>
                        <tr>
                            <td><strong><?php echo esc_html( $c['name'] ); ?></strong></td>
                            <td>Table <?php echo esc_html( $t1 ? $t1['table_number'] : $c['primary_table_id'] ); ?></td>
                            <td>Table <?php echo esc_html( $t2 ? $t2['table_number'] : $c['combined_table_id'] ); ?></td>
                            <td><strong><?php echo esc_html( $c['combined_capacity'] ); ?> Covers</strong></td>
                            <td>
                                <span class="rb-status <?php echo $c['is_active'] ? 'rb-status-seated' : 'rb-status-cancelled'; ?>">
                                    <?php echo $c['is_active'] ? 'Active' : 'Inactive'; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="5" style="text-align:center; padding: 20px;">No table combinations configured.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
