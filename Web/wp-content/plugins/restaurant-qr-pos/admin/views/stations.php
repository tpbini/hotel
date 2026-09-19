<?php
/**
 * Kitchen Preparation Stations View.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$stations = RO_Stations::get_all();
?>
<div class="wrap ro-wrap">
    <div class="ro-header-banner">
        <div>
            <h1>🍳 Kitchen Preparation Stations</h1>
            <p>Define preparation routing lines (e.g. Grill, Bar, Dessert) for dedicated KDS screens and thermal printer routing.</p>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
        <div class="ro-card">
            <h3 class="ro-card-title">Configured Stations</h3>
            <table class="ro-table">
                <thead>
                    <tr>
                        <th>Station Name</th>
                        <th>Code Identifier</th>
                        <th>Routing Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $stations as $st ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $st['name'] ); ?></strong></td>
                            <td><code><?php echo esc_html( $st['code'] ); ?></code></td>
                            <td>
                                <span style="background:#D1FAE5; color:#065F46; font-size:0.75rem; font-weight:800; padding:3px 8px; border-radius:10px;">
                                    <?php echo $st['is_active'] ? 'Active Routing' : 'Disabled'; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="ro-card">
            <h3 class="ro-card-title">Add Kitchen Station</h3>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=ro-stations' ) ); ?>">
                <?php wp_nonce_field( 'ro_station_nonce' ); ?>
                <input type="hidden" name="ro_action" value="create_station">
                <div class="ro-form-group">
                    <label>Station Name</label>
                    <input type="text" name="station_name" class="ro-form-control" placeholder="E.g., Pizza & Oven Section" required>
                </div>
                <div class="ro-form-group">
                    <label>Code Identifier</label>
                    <input type="text" name="station_code" class="ro-form-control" placeholder="E.g., pizza_oven">
                </div>
                <button type="submit" class="ro-btn-submit">+ Create Station</button>
            </form>
        </div>
    </div>
</div>
