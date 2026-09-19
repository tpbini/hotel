<?php
/**
 * Admin Opening Hours & Split Service Periods View.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$days = array(
    1 => 'Monday',
    2 => 'Tuesday',
    3 => 'Wednesday',
    4 => 'Thursday',
    5 => 'Friday',
    6 => 'Saturday',
    7 => 'Sunday',
);

$hours = RB_DB::get_results( "SELECT * FROM " . RB_DB::table( 'opening_hours' ) . " ORDER BY day_of_week ASC, open_time ASC" );
?>
<div class="wrap rb-wrap">
    <div class="rb-header-bar">
        <div>
            <h1>⏰ Opening Hours & Service Periods</h1>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">Manage split lunch and dinner opening schedules for each day of the week.</p>
        </div>
        <div class="rb-header-actions">
            <button class="rb-btn rb-btn-primary" id="rb-btn-add-service">➕ Add Service Period</button>
        </div>
    </div>

    <div class="rb-card">
        <table class="rb-table">
            <thead>
                <tr>
                    <th>Day of Week</th>
                    <th>Service Name</th>
                    <th>Open Time</th>
                    <th>Close Time</th>
                    <th>Slot Interval</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( ! empty( $hours ) ) : ?>
                    <?php foreach ( $hours as $h ) : ?>
                        <tr>
                            <td><strong><?php echo esc_html( isset( $days[ $h['day_of_week'] ] ) ? $days[ $h['day_of_week'] ] : 'Day ' . $h['day_of_week'] ); ?></strong></td>
                            <td><?php echo esc_html( $h['service_name'] ); ?></td>
                            <td><strong><?php echo esc_html( RB_i18n::format_time_uk( $h['open_time'] ) ); ?></strong></td>
                            <td><strong><?php echo esc_html( RB_i18n::format_time_uk( $h['close_time'] ) ); ?></strong></td>
                            <td><?php echo esc_html( $h['slot_interval'] ); ?> minutes</td>
                            <td>
                                <span class="rb-status <?php echo $h['is_active'] ? 'rb-status-seated' : 'rb-status-cancelled'; ?>">
                                    <?php echo $h['is_active'] ? 'Active' : 'Disabled'; ?>
                                </span>
                            </td>
                            <td>
                                <button class="rb-btn rb-btn-danger rb-btn-sm rb-delete-hour" data-id="<?php echo esc_attr( $h['id'] ); ?>">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr><td colspan="7" style="text-align:center; padding:30px;">No service periods defined.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Add Service Period -->
<div class="rb-modal-overlay" id="rb-modal-overlay-hour">
    <div class="rb-modal-box">
        <div class="rb-modal-header">
            <h3>Add Service Period</h3>
            <button class="rb-modal-close" id="hour-close">&times;</button>
        </div>
        <form id="rb-hour-form">
            <div class="rb-modal-body">
                <div class="rb-form-group">
                    <label>Day of Week</label>
                    <select id="hour-day" class="rb-form-control" required>
                        <?php foreach ( $days as $num => $dname ) : ?>
                            <option value="<?php echo esc_attr( $num ); ?>"><?php echo esc_html( $dname ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="rb-form-group">
                    <label>Service Name</label>
                    <input type="text" id="hour-name" class="rb-form-control" placeholder="e.g. Lunch / Dinner / Afternoon Tea" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                    <div class="rb-form-group">
                        <label>Open Time (HH:MM)</label>
                        <input type="time" id="hour-open" class="rb-form-control" value="12:00" required>
                    </div>
                    <div class="rb-form-group">
                        <label>Close Time (HH:MM)</label>
                        <input type="time" id="hour-close-time" class="rb-form-control" value="15:00" required>
                    </div>
                </div>
                <div class="rb-form-group">
                    <label>Booking Slot Interval</label>
                    <select id="hour-interval" class="rb-form-control">
                        <option value="15">Every 15 Minutes</option>
                        <option value="30" selected>Every 30 Minutes (Recommended)</option>
                        <option value="60">Every 60 Minutes</option>
                    </select>
                </div>
            </div>
            <div class="rb-modal-footer">
                <button type="submit" class="rb-btn rb-btn-primary">Save Service Period</button>
            </div>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#rb-btn-add-service').on('click', function() {
        $('#rb-modal-overlay-hour').addClass('active');
    });
    $('#hour-close, #rb-modal-overlay-hour').on('click', function(e) {
        if (e.target === this) $('#rb-modal-overlay-hour').removeClass('active');
    });

    $('#rb-hour-form').on('submit', function(e) {
        e.preventDefault();
        const data = {
            day_of_week: $('#hour-day').val(),
            service_name: $('#hour-name').val(),
            open_time: $('#hour-open').val(),
            close_time: $('#hour-close-time').val(),
            slot_interval: $('#hour-interval').val()
        };

        $.ajax({
            url: rbAdminData.restUrl + 'admin/opening-hours',
            method: 'POST',
            data: JSON.stringify(data),
            headers: { 'X-WP-Nonce': rbAdminData.nonce, 'Content-Type': 'application/json' }
        }).done(function() {
            alert('Service period saved!');
            location.reload();
        });
    });

    $('.rb-delete-hour').on('click', function() {
        if (confirm('Delete this service period?')) {
            const id = $(this).data('id');
            $.ajax({
                url: rbAdminData.restUrl + 'admin/opening-hours/' + id,
                method: 'DELETE',
                headers: { 'X-WP-Nonce': rbAdminData.nonce }
            }).done(function() {
                location.reload();
            });
        }
    });
});
</script>
