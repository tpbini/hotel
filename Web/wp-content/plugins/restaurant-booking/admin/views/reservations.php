<?php
/**
 * Admin Reservations List View.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$today = RB_i18n::today_uk();
?>
<div class="wrap rb-wrap" id="rb-reservations-view">
    <div class="rb-header-bar">
        <div>
            <h1>📖 Reservations Management</h1>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">View, search, confirm, seat, and manage all guest reservations.</p>
        </div>
        <div class="rb-header-actions">
            <button class="rb-btn rb-btn-success" id="rb-btn-open-walkin">➕ Fast Walk-In</button>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=restaurant-booking-calendar' ) ); ?>" class="rb-btn rb-btn-primary">📅 Live Calendar</a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="rb-filter-bar">
        <div>
            <label style="font-size: 12px; font-weight: 600; display:block; margin-bottom: 4px;">Filter Date:</label>
            <input type="date" id="rb-filter-date" class="rb-input" value="">
        </div>
        <div>
            <label style="font-size: 12px; font-weight: 600; display:block; margin-bottom: 4px;">Status:</label>
            <select id="rb-filter-status" class="rb-select">
                <option value="all">All Statuses</option>
                <option value="confirmed">Confirmed</option>
                <option value="pending">Pending</option>
                <option value="seated">Seated</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
                <option value="no_show">No-Show</option>
            </select>
        </div>
        <div style="flex-grow: 1;">
            <label style="font-size: 12px; font-weight: 600; display:block; margin-bottom: 4px;">Search:</label>
            <input type="text" id="rb-filter-search" class="rb-input" style="width: 100%;" placeholder="Search by customer name, phone, email, or reference...">
        </div>
        <div style="align-self: flex-end;">
            <button class="rb-btn rb-btn-primary" id="rb-btn-filter">Filter</button>
        </div>
    </div>

    <!-- Reservations Table -->
    <div class="rb-card">
        <table class="rb-table">
            <thead>
                <tr>
                    <th>Ref / Source</th>
                    <th>Customer Name & Phone</th>
                    <th>Date & Time</th>
                    <th>Guests</th>
                    <th>Table</th>
                    <th>Status</th>
                    <th>Allergies / Dietary</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="rb-res-tbody">
                <tr><td colspan="8" style="text-align:center; padding: 30px;">Loading reservations...</td></tr>
            </tbody>
        </table>
    </div>
</div>
