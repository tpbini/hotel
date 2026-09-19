<?php
/**
 * Admin Calendar & Service Timeline View.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$today = RB_i18n::today_uk();
?>
<div class="wrap rb-wrap" id="rb-calendar-view">
    <div class="rb-header-bar">
        <div>
            <h1>📅 Live Operational Calendar & Timeline</h1>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">Visual table occupancy across Lunch and Dinner service periods.</p>
        </div>
        <div class="rb-header-actions">
            <button class="rb-btn rb-btn-secondary" id="rb-cal-prev">&larr; Previous Day</button>
            <input type="date" id="rb-cal-date" class="rb-input" value="<?php echo esc_attr( $today ); ?>" style="font-weight: 700;">
            <button class="rb-btn rb-btn-secondary" id="rb-cal-next">Next Day &rarr;</button>
            <button class="rb-btn rb-btn-success" id="rb-btn-open-walkin">➕ Fast Walk-In</button>
        </div>
    </div>

    <!-- Timeline Grid Container -->
    <div class="rb-timeline-wrap">
        <div id="rb-timeline-grid">
            <p style="padding: 20px; text-align: center;">Loading service schedule...</p>
        </div>
    </div>
</div>
