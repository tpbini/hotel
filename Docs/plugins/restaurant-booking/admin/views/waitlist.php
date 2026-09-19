<?php
/**
 * Admin Waitlist View.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$waitlist = RB_DB::get_results( "SELECT * FROM " . RB_DB::table( 'waitlist' ) . " ORDER BY requested_date ASC, id ASC" );
?>
<div class="wrap rb-wrap">
    <div class="rb-header-bar">
        <div>
            <h1>📋 Customer Waitlist</h1>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">Manage standby requests from customers who requested dates/times that were fully booked.</p>
        </div>
    </div>

    <div class="rb-card">
        <table class="rb-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Phone & Email</th>
                    <th>Requested Date</th>
                    <th>Preferred Time</th>
                    <th>Party Size</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty( $waitlist ) ) : ?>
                    <?php foreach ( $waitlist as $w ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( $w['customer_name'] ); ?></strong></td>
                            <td><?php echo esc_html( $w['phone'] ); ?><br><span style="font-size:12px; color:#64748b;"><?php echo esc_html( $w['email'] ); ?></span></td>
                            <td><strong><?php echo esc_html( RB_i18n::format_date_uk( $w['requested_date'], 'l, j F Y' ) ); ?></strong></td>
                            <td><?php echo esc_html( RB_i18n::format_time_uk( $w['preferred_time'] ) ); ?></td>
                            <td><strong><?php echo esc_html( $w['party_size'] ); ?></strong> Guests</td>
                            <td>
                                <span class="rb-status <?php echo ( $w['status'] === 'waiting' ) ? 'rb-status-pending' : 'rb-status-seated'; ?>">
                                    <?php echo esc_html( strtoupper( $w['status'] ) ); ?>
                                </span>
                            </td>
                            <td><?php echo esc_html( $w['notes'] ? $w['notes'] : '-' ); ?></td>
                            <td>
                                <button class="rb-btn rb-btn-accent rb-btn-sm rb-waitlist-status" data-id="<?php echo esc_attr( $w['id'] ); ?>" data-status="offered">Offer Table</button>
                                <button class="rb-btn rb-btn-secondary rb-btn-sm rb-waitlist-status" data-id="<?php echo esc_attr( $w['id'] ); ?>" data-status="expired">Expire</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="8" style="text-align:center; padding:30px;">The waitlist is currently empty.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('.rb-waitlist-status').on('click', function() {
        const id = $(this).data('id');
        const status = $(this).data('status');
        $.ajax({
            url: rbAdminData.restUrl + 'admin/waitlist/' + id + '/status',
            method: 'PATCH',
            data: JSON.stringify({ status: status }),
            headers: { 'X-WP-Nonce': rbAdminData.nonce, 'Content-Type': 'application/json' }
        }).done(function() {
            location.reload();
        });
    });
});
</script>
