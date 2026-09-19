<?php
/**
 * Admin Reports & Analytics View.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$table = RB_DB::table( 'reservations' );

// Summary aggregates
$total_bookings = intval( RB_DB::get_var( "SELECT COUNT(*) FROM $table" ) );
$total_covers   = intval( RB_DB::get_var( "SELECT COALESCE(SUM(party_size), 0) FROM $table WHERE status NOT IN ('cancelled')" ) );
$avg_party_size = ( $total_bookings > 0 ) ? round( RB_DB::get_var( "SELECT AVG(party_size) FROM $table WHERE status NOT IN ('cancelled')" ), 1 ) : 0;
$total_cancelled= intval( RB_DB::get_var( "SELECT COUNT(*) FROM $table WHERE status = 'cancelled'" ) );
$total_noshow   = intval( RB_DB::get_var( "SELECT COUNT(*) FROM $table WHERE status = 'no_show'" ) );

$web_count   = intval( RB_DB::get_var( "SELECT COUNT(*) FROM $table WHERE source = 'web'" ) );
$walkin_count= intval( RB_DB::get_var( "SELECT COUNT(*) FROM $table WHERE source = 'walk_in'" ) );
$staff_count = intval( RB_DB::get_var( "SELECT COUNT(*) FROM $table WHERE source = 'staff'" ) );

// Top 5 most booked times
$popular_times = RB_DB::get_results( "SELECT start_time, COUNT(*) as count FROM $table GROUP BY start_time ORDER BY count DESC LIMIT 5" );
?>
<div class="wrap rb-wrap">
    <div class="rb-header-bar">
        <div>
            <h1>📊 Reservation Analytics & Service Reports</h1>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">Performance metrics, party sizes, cancellation rates, and peak service analytics.</p>
        </div>
    </div>

    <div class="rb-stats-grid">
        <div class="rb-stat-card">
            <div class="rb-stat-title">Total Lifetime Covers</div>
            <div class="rb-stat-value"><?php echo esc_html( number_format( $total_covers ) ); ?></div>
            <div class="rb-stat-sub">Dining guests accommodated</div>
            <div class="rb-stat-badge rb-badge-blue">👥</div>
        </div>
        <div class="rb-stat-card">
            <div class="rb-stat-title">Average Party Size</div>
            <div class="rb-stat-value"><?php echo esc_html( $avg_party_size ); ?></div>
            <div class="rb-stat-sub">Covers per table reservation</div>
            <div class="rb-stat-badge rb-badge-purple">🪑</div>
        </div>
        <div class="rb-stat-card">
            <div class="rb-stat-title">Total Bookings</div>
            <div class="rb-stat-value"><?php echo esc_html( number_format( $total_bookings ) ); ?></div>
            <div class="rb-stat-sub">All reservation records</div>
            <div class="rb-stat-badge rb-badge-green">📈</div>
        </div>
        <div class="rb-stat-card">
            <div class="rb-stat-title">No-Shows</div>
            <div class="rb-stat-value" style="color: #5b21b6;"><?php echo esc_html( $total_noshow ); ?></div>
            <div class="rb-stat-sub">Recorded guest no-shows</div>
            <div class="rb-stat-badge rb-badge-orange">⚠️</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <!-- Booking Channels -->
        <div class="rb-card">
            <div class="rb-card-header">
                <h2>Booking Sources Breakdown</h2>
            </div>
            <table class="rb-table">
                <thead>
                    <tr>
                        <th>Channel / Source</th>
                        <th>Total Bookings</th>
                        <th>Share (%)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>🌐 Online Website Widget</strong></td>
                        <td><?php echo esc_html( $web_count ); ?></td>
                        <td><?php echo ( $total_bookings > 0 ) ? round( ( $web_count / $total_bookings ) * 100, 1 ) : 0; ?>%</td>
                    </tr>
                    <tr>
                        <td><strong>🚶 Fast Walk-Ins</strong></td>
                        <td><?php echo esc_html( $walkin_count ); ?></td>
                        <td><?php echo ( $total_bookings > 0 ) ? round( ( $walkin_count / $total_bookings ) * 100, 1 ) : 0; ?>%</td>
                    </tr>
                    <tr>
                        <td><strong>📞 Telephone / Staff Booking</strong></td>
                        <td><?php echo esc_html( $staff_count ); ?></td>
                        <td><?php echo ( $total_bookings > 0 ) ? round( ( $staff_count / $total_bookings ) * 100, 1 ) : 0; ?>%</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Popular Dining Times -->
        <div class="rb-card">
            <div class="rb-card-header">
                <h2>Most Popular Booking Times</h2>
            </div>
            <table class="rb-table">
                <thead>
                    <tr>
                        <th>Service Time</th>
                        <th>Bookings Count</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( ! empty( $popular_times ) ) : ?>
                        <?php foreach ( $popular_times as $pt ) : ?>
                            <tr>
                                <td><strong><?php echo esc_html( RB_i18n::format_time_uk( $pt['start_time'] ) ); ?></strong></td>
                                <td><span class="rb-status rb-status-confirmed"><?php echo esc_html( $pt['count'] ); ?> Bookings</span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="2" style="text-align:center; padding: 20px;">No reservation data yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
