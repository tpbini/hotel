<?php
/**
 * Restaurant QR POS General Settings View.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$restaurant_name    = get_option( 'ro_restaurant_name', 'Grand Bistro & Grill' );
$currency_symbol    = get_option( 'ro_currency_symbol', '$' );
$tax_rate           = get_option( 'ro_tax_rate', '5.0' );
$restaurant_phone   = get_option( 'ro_restaurant_phone', '' );
$restaurant_address = get_option( 'ro_restaurant_address', '' );
$bridge_token       = get_option( 'ro_print_bridge_token', '' );
?>
<div class="wrap ro-wrap">
    <div class="ro-header-banner">
        <div>
            <h1>⚙️ Restaurant Operations Settings</h1>
            <p>Configure restaurant details, currency, global tax rates, and local thermal printer bridge credentials.</p>
        </div>
    </div>

    <div class="ro-card" style="max-width:800px;">
        <h3 class="ro-card-title">General Configuration</h3>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=ro-settings' ) ); ?>">
            <?php wp_nonce_field( 'ro_settings_nonce' ); ?>
            <input type="hidden" name="ro_action" value="save_settings">

            <div class="ro-form-group">
                <label>Restaurant Name</label>
                <input type="text" name="restaurant_name" class="ro-form-control" value="<?php echo esc_attr( $restaurant_name ); ?>" required>
            </div>

            <div class="ro-form-group">
                <label>Currency Symbol</label>
                <input type="text" name="currency_symbol" class="ro-form-control" value="<?php echo esc_attr( $currency_symbol ); ?>" style="max-width:120px;" required>
            </div>

            <div class="ro-form-group">
                <label>Default Tax Rate (%)</label>
                <input type="number" step="0.1" name="tax_rate" class="ro-form-control" value="<?php echo esc_attr( $tax_rate ); ?>" style="max-width:120px;" required>
            </div>

            <div class="ro-form-group">
                <label>Contact Phone Number</label>
                <input type="text" name="restaurant_phone" class="ro-form-control" value="<?php echo esc_attr( $restaurant_phone ); ?>">
            </div>

            <div class="ro-form-group">
                <label>Physical Address (Printed on Invoices)</label>
                <textarea name="restaurant_address" class="ro-form-control" rows="3"><?php echo esc_textarea( $restaurant_address ); ?></textarea>
            </div>

            <hr style="margin:24px 0; border:0; border-top:1px solid #E2E8F0;">

            <h3 class="ro-card-title">🖨️ Thermal Print Routing & Bridge Setup</h3>
            <p style="font-size:0.85rem; color:#64748B; margin-bottom:14px;">Configure local network thermal receipt printers (ESC/POS). The background Print Bridge running on the counter PC routes KOTs and invoices to these IP addresses.</p>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                <div class="ro-form-group">
                    <label>Kitchen KOT Printer IP</label>
                    <input type="text" name="kitchen_printer_ip" class="ro-form-control" value="<?php echo esc_attr( get_option( 'ro_kitchen_printer_ip', '192.168.1.20' ) ); ?>" placeholder="e.g. 192.168.1.20">
                </div>

                <div class="ro-form-group">
                    <label>Kitchen Printer Port</label>
                    <input type="number" name="kitchen_printer_port" class="ro-form-control" value="<?php echo esc_attr( get_option( 'ro_kitchen_printer_port', '9100' ) ); ?>" placeholder="9100">
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                <div class="ro-form-group">
                    <label>Counter Invoice Printer IP</label>
                    <input type="text" name="counter_printer_ip" class="ro-form-control" value="<?php echo esc_attr( get_option( 'ro_counter_printer_ip', '192.168.1.21' ) ); ?>" placeholder="e.g. 192.168.1.21">
                </div>

                <div class="ro-form-group">
                    <label>Counter Printer Port</label>
                    <input type="number" name="counter_printer_port" class="ro-form-control" value="<?php echo esc_attr( get_option( 'ro_counter_printer_port', '9100' ) ); ?>" placeholder="9100">
                </div>
            </div>

            <div class="ro-form-group" style="margin-top:8px;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="autoprint_kot" value="1" <?php checked( get_option( 'ro_autoprint_kot', '1' ), '1' ); ?> style="accent-color:var(--primary);">
                    <span>Auto-queue Kitchen Order Tickets (KOT) on every new order</span>
                </label>
            </div>

            <div class="ro-form-group">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="autoprint_receipt" value="1" <?php checked( get_option( 'ro_autoprint_receipt', '1' ), '1' ); ?> style="accent-color:var(--primary);">
                    <span>Auto-queue Customer Invoice receipt on bill payment</span>
                </label>
            </div>

            <div class="ro-form-group" style="margin-top:12px;">
                <label>Bridge Security Token (x-bridge-token)</label>
                <input type="text" name="print_bridge_token" class="ro-form-control" value="<?php echo esc_attr( $bridge_token ); ?>" style="font-family:monospace;" required>
            </div>

            <div style="display:flex; align-items:center; gap:14px; margin-top:20px;">
                <button type="submit" class="ro-btn-submit">Save Restaurant Settings</button>
                <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=ro-settings&ro_action=send_test_print' ), 'ro_test_print_nonce' ) ); ?>" class="ro-btn-secondary" style="display:inline-flex; align-items:center; gap:6px; padding:10px 16px; border-radius:8px; text-decoration:none; background:#F1F5F9; color:#334155; font-weight:700; font-size:0.9rem; border:1px solid #CBD5E1;">
                    <span>🖨️ Send Test Print Job</span>
                </a>
            </div>
        </form>
    </div>
</div>
