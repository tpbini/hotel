<?php
/**
 * Admin Special Dates & Holiday Exceptions View.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$special_dates = RB_DB::get_results( "SELECT * FROM " . RB_DB::table( 'special_dates' ) . " ORDER BY special_date ASC" );
?>
<div class="wrap rb-wrap">
    <div class="rb-header-bar">
        <div>
            <h1>🎄 Special Dates & Holiday Overrides</h1>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">Set full-day closures (e.g. Christmas, Bank Holidays) or altered hours for special dates.</p>
        </div>
        <div class="rb-header-actions">
            <button class="rb-btn rb-btn-primary" id="rb-btn-add-date">➕ Add Special Date</button>
        </div>
    </div>

    <div class="rb-card">
        <table class="rb-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Occasion / Reason</th>
                    <th>Action Type</th>
                    <th>Custom Hours</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty( $special_dates ) ) : ?>
                    <?php foreach ( $special_dates as $sd ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( RB_i18n::format_date_uk( $sd['special_date'], 'l, j F Y' ) ); ?></strong></td>
                            <td><?php echo esc_html( $sd['name'] ); ?></td>
                            <td>
                                <span class="rb-status <?php echo ( $sd['action_type'] === 'closed' ) ? 'rb-status-cancelled' : 'rb-status-pending'; ?>">
                                    <?php echo esc_html( strtoupper( str_replace( '_', ' ', $sd['action_type'] ) ) ); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ( $sd['action_type'] === 'custom_hours' && $sd['open_time'] && $sd['close_time'] ) : ?>
                                    <?php echo esc_html( RB_i18n::format_time_uk( $sd['open_time'] ) . ' - ' . RB_i18n::format_time_uk( $sd['close_time'] ) ); ?>
                                <?php else : ?>
                                    <span style="color:#94a3b8;">All Day Closed</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo esc_html( $sd['notes'] ); ?></td>
                            <td>
                                <button class="rb-btn rb-btn-danger rb-btn-sm rb-delete-special" data-id="<?php echo esc_attr( $sd['id'] ); ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="6" style="text-align:center; padding:30px;">No special date overrides configured.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Add Special Date -->
<div class="rb-modal-overlay" id="rb-modal-overlay-special">
    <div class="rb-modal-box">
        <div class="rb-modal-header">
            <h3>Add Special Date / Holiday</h3>
            <button class="rb-modal-close" id="special-close">&times;</button>
        </div>
        <form id="rb-special-form">
            <div class="rb-modal-body">
                <div class="rb-form-group">
                    <label>Select Date *</label>
                    <input type="date" id="special-date" class="rb-form-control" required>
                </div>
                <div class="rb-form-group">
                    <label>Occasion / Reason Name *</label>
                    <input type="text" id="special-name" class="rb-form-control" placeholder="e.g. Christmas Day, New Year's Eve, Private Event" required>
                </div>
                <div class="rb-form-group">
                    <label>Action</label>
                    <select id="special-action" class="rb-form-control">
                        <option value="closed">Closed All Day (No Bookings)</option>
                        <option value="custom_hours">Custom Opening Hours</option>
                    </select>
                </div>
                <div id="special-custom-hours-wrap" style="display: none; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="rb-form-group">
                        <label>Open Time</label>
                        <input type="time" id="special-open" class="rb-form-control" value="12:00">
                    </div>
                    <div class="rb-form-group">
                        <label>Close Time</label>
                        <input type="time" id="special-close-time" class="rb-form-control" value="18:00">
                    </div>
                </div>
                <div class="rb-form-group">
                    <label>Customer Advisory Note</label>
                    <textarea id="special-notes" class="rb-form-control" rows="2" placeholder="e.g. Set menu only on this date."></textarea>
                </div>
            </div>
            <div class="rb-modal-footer">
                <button type="submit" class="rb-btn rb-btn-primary">Save Special Date</button>
            </div>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#special-action').on('change', function() {
        if ($(this).val() === 'custom_hours') {
            $('#special-custom-hours-wrap').css('display', 'grid');
        } else {
            $('#special-custom-hours-wrap').hide();
        }
    });

    $('#rb-btn-add-date').on('click', function() {
        $('#rb-modal-overlay-special').addClass('active');
    });
    $('#special-close, #rb-modal-overlay-special').on('click', function(e) {
        if (e.target === this) $('#rb-modal-overlay-special').removeClass('active');
    });

    $('#rb-special-form').on('submit', function(e) {
        e.preventDefault();
        const data = {
            special_date: $('#special-date').val(),
            name: $('#special-name').val(),
            action_type: $('#special-action').val(),
            open_time: $('#special-open').val(),
            close_time: $('#special-close-time').val(),
            notes: $('#special-notes').val()
        };

        $.ajax({
            url: rbAdminData.restUrl + 'admin/special-dates',
            method: 'POST',
            data: JSON.stringify(data),
            headers: { 'X-WP-Nonce': rbAdminData.nonce, 'Content-Type': 'application/json' }
        }).done(function() {
            alert('Special date override saved!');
            location.reload();
        });
    });

    $('.rb-delete-special').on('click', function() {
        if (confirm('Delete this special date override?')) {
            const id = $(this).data('id');
            $.ajax({
                url: rbAdminData.restUrl + 'admin/special-dates/' + id,
                method: 'DELETE',
                headers: { 'X-WP-Nonce': rbAdminData.nonce }
            }).done(function() {
                location.reload();
            });
        }
    });
});
</script>
