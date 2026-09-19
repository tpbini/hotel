<?php
/**
 * Admin Dashboard View.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$today_uk = RB_i18n::today_uk();
$today_formatted = RB_i18n::format_date_uk( $today_uk, 'l, j F Y' );
$qr_active = RB_QR_Adapter::is_qr_plugin_active();
?>
<div class="wrap rb-wrap" id="rb-dashboard-view">
    <!-- Header -->
    <div class="rb-header-bar">
        <div>
            <h1>🍽️ Restaurant Booking Control Center</h1>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">
                Today: <strong><?php echo esc_html( $today_formatted ); ?></strong> | Timezone: <code>Europe/London (GMT/BST)</code>
            </p>
        </div>
        <div class="rb-header-actions">
            <button class="rb-btn rb-btn-success" id="rb-btn-open-walkin">➕ Fast Walk-In</button>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=restaurant-booking-calendar' ) ); ?>" class="rb-btn rb-btn-primary">📅 Live Calendar</a>
            <?php if ( $qr_active ) : ?>
                <a href="<?php echo esc_url( home_url( '/restaurant-pos/' ) ); ?>" target="_blank" class="rb-btn rb-btn-accent">⚡ Open POS Dashboard &rarr;</a>
            <?php endif; ?>
            <button class="rb-btn rb-btn-secondary" id="rb-refresh-stats">🔄 Refresh</button>
        </div>
    </div>

    <?php if ( $qr_active ) : ?>
        <div class="rb-alert rb-alert-info">
            <strong>🔗 Seamless Integration Active:</strong> Linked to <em>Restaurant QR Ordering & POS</em>. Table allocations and live seating are synchronized across both systems.
        </div>
    <?php endif; ?>

    <!-- Real-Time Metrics -->
    <div class="rb-stats-grid">
        <div class="rb-stat-card">
            <div class="rb-stat-title">Today's Covers</div>
            <div class="rb-stat-value" id="stat-today-covers">--</div>
            <div class="rb-stat-sub">Expected dining guests</div>
            <div class="rb-stat-badge rb-badge-blue">👥</div>
        </div>
        <div class="rb-stat-card">
            <div class="rb-stat-title">Today's Reservations</div>
            <div class="rb-stat-value" id="stat-today-bookings">--</div>
            <div class="rb-stat-sub">Total booked parties</div>
            <div class="rb-stat-badge rb-badge-purple">📅</div>
        </div>
        <div class="rb-stat-card">
            <div class="rb-stat-title">Seated / In-Service</div>
            <div class="rb-stat-value" id="stat-today-seated" style="color: #059669;">--</div>
            <div class="rb-stat-sub">Active dining sessions</div>
            <div class="rb-stat-badge rb-badge-green">🪑</div>
        </div>
        <div class="rb-stat-card">
            <div class="rb-stat-title">Pending Approvals</div>
            <div class="rb-stat-value" id="stat-today-pending" style="color: #d97706;">--</div>
            <div class="rb-stat-sub">Awaiting staff review</div>
            <div class="rb-stat-badge rb-badge-orange">⏳</div>
        </div>
        <div class="rb-stat-card">
            <div class="rb-stat-title">Table Occupancy</div>
            <div class="rb-stat-value" id="stat-table-occupancy" style="font-size: 20px;">--</div>
            <div class="rb-stat-sub">Physical table capacity</div>
            <div class="rb-stat-badge rb-badge-blue">🏛️</div>
        </div>
        <div class="rb-stat-card">
            <div class="rb-stat-title">Cancellation Rate</div>
            <div class="rb-stat-value" id="stat-cancellation-rate">--</div>
            <div class="rb-stat-sub">Historical average</div>
            <div class="rb-stat-badge rb-badge-orange">📉</div>
        </div>
    </div>

    <!-- Today's Reservations Datatable -->
    <div class="rb-card">
        <div class="rb-card-header">
            <h2>Today's Service Schedule (<?php echo esc_html( $today_formatted ); ?>)</h2>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=restaurant-booking-reservations' ) ); ?>" class="rb-btn rb-btn-secondary rb-btn-sm">View All Dates &rarr;</a>
        </div>
        <table class="rb-table">
            <thead>
                <tr>
                    <th>Ref / Source</th>
                    <th>Customer Name</th>
                    <th>Time</th>
                    <th>Guests</th>
                    <th>Table</th>
                    <th>Status</th>
                    <th>Allergies / Dietary</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="rb-today-tbody">
                <tr><td colspan="8" style="text-align:center; padding: 30px;">Loading today's schedule...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Fast Walk-In Creator -->
<div class="rb-modal-overlay" id="rb-modal-overlay-walkin">
    <div class="rb-modal-box">
        <div class="rb-modal-header">
            <h3>➕ Seat Fast Walk-In Guest</h3>
            <button class="rb-modal-close" id="rb-walkin-close">&times;</button>
        </div>
        <form id="rb-walkin-form">
            <div class="rb-modal-body">
                <div class="rb-form-group">
                    <label>Party Size (Number of Guests) *</label>
                    <input type="number" id="walkin-party" class="rb-form-control" min="1" max="20" value="2" required>
                </div>
                <div class="rb-form-group">
                    <label>Select Table *</label>
                    <select id="walkin-table" class="rb-form-control" required>
                        <option value="">Loading tables...</option>
                    </select>
                </div>
                <div class="rb-form-group">
                    <label>Guest Name (Optional)</label>
                    <input type="text" id="walkin-name" class="rb-form-control" placeholder="e.g. Walk-In Guest / Smith">
                </div>
                <div class="rb-form-group">
                    <label>Notes / Dietary Restrictions</label>
                    <textarea id="walkin-notes" class="rb-form-control" rows="2" placeholder="e.g. Highchair needed, Gluten free"></textarea>
                </div>
            </div>
            <div class="rb-modal-footer">
                <button type="submit" class="rb-btn rb-btn-success" id="rb-walkin-submit">🪑 Seat Walk-In Guest</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Reservation Details & Audit Trail -->
<div class="rb-modal-overlay" id="rb-modal-overlay-detail">
    <div class="rb-modal-box" style="max-width: 600px;">
        <div class="rb-modal-header">
            <h3>Reservation Details: <span id="detail-ref"></span></h3>
            <button class="rb-modal-close" id="detail-close">&times;</button>
        </div>
        <div class="rb-modal-body">
            <table class="rb-table" style="margin-bottom: 20px;">
                <tr><td style="width: 140px; color:#64748b;">Customer:</td><td><strong id="detail-name"></strong> (<span id="detail-phone"></span>)</td></tr>
                <tr><td style="color:#64748b;">Email:</td><td id="detail-email"></td></tr>
                <tr><td style="color:#64748b;">Date & Time:</td><td id="detail-datetime"></td></tr>
                <tr><td style="color:#64748b;">Party Size:</td><td id="detail-party"></td></tr>
                <tr><td style="color:#64748b;">Allocated Table:</td><td><strong id="detail-table"></strong></td></tr>
                <tr><td style="color:#64748b;">Current Status:</td><td id="detail-status"></td></tr>
                <tr><td style="color:#64748b;">Special Requests:</td><td id="detail-special"></td></tr>
                <tr><td style="color:#64748b;">Dietary / Allergies:</td><td id="detail-dietary" style="color:#b91c1c; font-weight:600;"></td></tr>
            </table>

            <h4 style="margin: 16px 0 8px 0; font-size: 14px;">Audit & Lifecycle History</h4>
            <ul id="detail-events-list" style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; padding:12px 20px; max-height:140px; overflow-y:auto;"></ul>
        </div>
        <div class="rb-modal-footer">
            <a href="#" target="_blank" id="detail-manage-link" class="rb-btn rb-btn-secondary rb-btn-sm">🔗 Open Guest Manage Portal</a>
        </div>
    </div>
</div>
