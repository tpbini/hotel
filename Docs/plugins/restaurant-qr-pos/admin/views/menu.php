<?php
/**
 * Menu & Categories Admin View.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$categories = RO_Menu::get_categories( false );
$items = RO_Menu::get_items();
$currency = get_option( 'ro_currency_symbol', '$' );
$stations = RO_Stations::get_active();
?>
<div class="wrap ro-wrap">
    <div class="ro-header-banner">
        <div>
            <h1>🍕 Digital Menu & Categories</h1>
            <p>Manage categories, menu dishes, prices, preparation stations, portion variations, and add-ons.</p>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 2fr; gap:24px;">
        <!-- Left: Categories -->
        <div>
            <div class="ro-card">
                <h3 class="ro-card-title">Menu Categories</h3>
                <table class="ro-table" style="margin-bottom:18px;">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Order</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $categories as $cat ) : ?>
                            <tr>
                                <td><strong><?php echo esc_html( $cat['name'] ); ?></strong></td>
                                <td><?php echo intval( $cat['sort_order'] ); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=ro-menu' ) ); ?>">
                    <?php wp_nonce_field( 'ro_category_nonce' ); ?>
                    <input type="hidden" name="ro_action" value="create_category">
                    <div class="ro-form-group">
                        <label>New Category Name</label>
                        <input type="text" name="cat_name" class="ro-form-control" placeholder="E.g., Pasta & Risotto" required>
                    </div>
                    <div class="ro-form-group">
                        <label>Sort Order</label>
                        <input type="number" name="cat_order" class="ro-form-control" value="0">
                    </div>
                    <button type="submit" class="ro-btn-submit">+ Add Category</button>
                </form>
            </div>
        </div>

        <!-- Right: Menu Dishes -->
        <div>
            <div class="ro-card">
                <div class="ro-card-title">
                    <span>Menu Dishes (<?php echo count( $items ); ?>)</span>
                </div>
                <table class="ro-table">
                    <thead>
                        <tr>
                            <th>Dish Name</th>
                            <th>Category</th>
                            <th>Station</th>
                            <th>Base Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $items as $it ) : ?>
                            <tr>
                                <td>
                                    <strong><?php echo esc_html( $it['name'] ); ?></strong>
                                    <?php if ( ! empty( $it['variations'] ) ) : ?>
                                        <div style="font-size:0.75rem; color:#64748B;">
                                            Variations: <?php echo esc_html( implode( ', ', array_column( $it['variations'], 'name' ) ) ); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $cat_obj = array_filter( $categories, function( $c ) use ( $it ) { return $c['id'] == $it['category_id']; } );
                                    echo esc_html( ! empty( $cat_obj ) ? reset( $cat_obj )['name'] : '—' );
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $st_obj = array_filter( $stations, function( $s ) use ( $it ) { return $s['id'] == $it['station_id']; } );
                                    echo esc_html( ! empty( $st_obj ) ? reset( $st_obj )['name'] : 'Main Kitchen' );
                                    ?>
                                </td>
                                <td><strong><?php echo esc_html( $currency . number_format( $it['base_price'], 2 ) ); ?></strong></td>
                                <td>
                                    <span style="background:<?php echo 'available' === $it['status'] ? '#D1FAE5' : '#FEE2E2'; ?>; color:<?php echo 'available' === $it['status'] ? '#065F46' : '#991B1B'; ?>; font-size:0.75rem; font-weight:800; padding:3px 8px; border-radius:10px;">
                                        <?php echo esc_html( ucfirst( str_replace( '_', ' ', $it['status'] ) ) ); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
