<?php
/**
 * Table & QR Management View.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tables = RO_Tables::get_all();
$currency = get_option( 'ro_currency_symbol', '$' );
?>
<div class="wrap ro-wrap">
    <div class="ro-header-banner">
        <div>
            <h1>🪑 Table & QR Code Management</h1>
            <p>Generate secure random QR tokens, manage capacities, and print customer table stands.</p>
        </div>
        <div style="display:flex; gap:10px;">
            <button onclick="window.print()" class="ro-launch-btn secondary">
                <span>🖨️ Print Table QR Cards</span>
            </button>
            <button onclick="document.getElementById('addTableForm').scrollIntoView({behavior: 'smooth'})" class="ro-launch-btn">
                <span>+ Add New Table</span>
            </button>
        </div>
    </div>

    <style>
        @media print {
            body * { visibility: hidden; }
            .ro-qr-card, .ro-qr-card * { visibility: visible; }
            .ro-header-banner, #addTableForm, .ro-launch-btn, #adminmenumain, #wpadminbar, #wpfooter { display: none !important; }
            .ro-wrap { margin: 0 !important; padding: 0 !important; }
            div[style*="grid-template-columns"] {
                display: grid !important;
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 20px !important;
            }
            .ro-qr-card {
                border: 2px dashed #CBD5E1 !important;
                page-break-inside: avoid !important;
                padding: 24px !important;
                box-shadow: none !important;
            }
        }
    </style>

    <!-- Tables Grid with QR Codes -->
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:20px; margin-bottom:30px;">
        <?php foreach ( $tables as $t ) : ?>
            <div class="ro-card ro-qr-card" style="display:flex; flex-direction:column; align-items:center; text-align:center;">
                <div style="width:100%; display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
                    <span style="font-weight:800; font-size:1.1rem; color:#0F172A;"><?php echo esc_html( $t['table_number'] ); ?></span>
                    <span style="background:<?php echo 'occupied' === $t['status'] ? '#FEF3C7' : '#D1FAE5'; ?>; color:<?php echo 'occupied' === $t['status'] ? '#92400E' : '#065F46'; ?>; font-size:0.75rem; font-weight:800; padding:2px 8px; border-radius:10px;">
                        <?php echo esc_html( ucfirst( $t['status'] ) ); ?>
                    </span>
                </div>

                <div class="ro-qr-badge" style="margin-bottom:14px;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?php echo urlencode( $t['qr_url'] ); ?>" alt="Table QR" style="width:140px; height:140px; display:block;">
                    <div style="font-size:0.75rem; color:#64748B; font-weight:700; margin-top:8px;">Scan to Order</div>
                </div>

                <div style="font-size:0.85rem; color:#64748B; margin-bottom:14px;">
                    <div>Capacity: <strong><?php echo intval( $t['capacity'] ); ?> Seats</strong></div>
                    <div style="font-family:monospace; font-size:0.75rem; margin-top:2px;">Token: <?php echo esc_html( $t['qr_token'] ); ?></div>
                </div>

                <div style="width:100%; display:flex; gap:8px; justify-content:center;">
                    <a href="<?php echo esc_url( $t['qr_url'] ); ?>" target="_blank" class="ro-launch-btn secondary" style="font-size:0.78rem; padding:6px 12px;">
                        📱 Test QR App
                    </a>
                    <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=ro-tables&ro_action=regen_qr&table_id=' . $t['id'] ), 'ro_regen_qr_' . $t['id'] ) ); ?>" class="ro-launch-btn secondary" style="font-size:0.78rem; padding:6px 10px;" onclick="return confirm('Regenerate QR token? Previous printed code will stop working.');">
                        🔄 Refresh
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Add Table Card Form -->
    <div class="ro-card" id="addTableForm">
        <h3 class="ro-card-title">Add / Edit Table</h3>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=ro-tables' ) ); ?>">
            <?php wp_nonce_field( 'ro_save_table_nonce' ); ?>
            <input type="hidden" name="ro_action" value="save_table">

            <div class="ro-form-group">
                <label>Table Number / Identifier *</label>
                <input type="text" name="table_number" class="ro-form-control" placeholder="E.g., Table 09, Rooftop Table 3, VIP Booth B" required>
            </div>

            <div class="ro-form-group">
                <label>Seating Capacity</label>
                <input type="number" name="capacity" class="ro-form-control" value="4" min="1" max="50">
            </div>

            <div class="ro-form-group">
                <label>Internal Notes / Location</label>
                <textarea name="notes" class="ro-form-control" placeholder="Ground floor near bar, window view..."></textarea>
            </div>

            <button type="submit" class="ro-btn-submit">Save Table</button>
        </form>
    </div>
</div>
