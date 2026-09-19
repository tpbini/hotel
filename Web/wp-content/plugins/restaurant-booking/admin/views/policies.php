<?php
/**
 * Admin Booking Policies & Settings View.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$settings = array(
    'restaurant_name'           => get_option( 'rb_restaurant_name', 'The Crown & Thistle Bistro' ),
    'restaurant_phone'          => get_option( 'rb_restaurant_phone', '020 7946 0912' ),
    'restaurant_email'          => get_option( 'rb_restaurant_email', get_option( 'admin_email' ) ),
    'restaurant_address'        => get_option( 'rb_restaurant_address', '45 High Street, London, EC1A 1BB' ),
    'default_duration'          => intval( get_option( 'rb_default_duration', 120 ) ),
    'turnaround_buffer'         => intval( get_option( 'rb_turnaround_buffer', 15 ) ),
    'booking_interval'          => intval( get_option( 'rb_booking_interval', 30 ) ),
    'min_party_size'            => intval( get_option( 'rb_min_party_size', 1 ) ),
    'max_party_size'            => intval( get_option( 'rb_max_party_size', 12 ) ),
    'auto_confirm'              => intval( get_option( 'rb_auto_confirm', 1 ) ),
    'cancellation_cutoff_hours' => intval( get_option( 'rb_cancellation_cutoff_hours', 2 ) ),
    'max_advance_days'          => intval( get_option( 'rb_max_advance_days', 90 ) ),
    'allergen_notice'           => get_option( 'rb_allergen_notice', 'Please note: If you or any member of your party suffer from a food allergy or dietary intolerance, please speak to a member of our team directly by phone before booking.' ),
    'privacy_policy_text'       => get_option( 'rb_privacy_policy_text', 'We collect only the personal information required to manage your table reservation. Your details are never sold to third parties.' ),
);
?>
<div class="wrap rb-wrap">
    <div class="rb-header-bar">
        <div>
            <h1>⚙️ Booking Policies & Restaurant Settings</h1>
            <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">Configure UK dining durations, turnaround buffers, auto-confirm rules, and allergen disclosures.</p>
        </div>
    </div>

    <form id="rb-settings-form">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
            <!-- Left Column: Restaurant Info & Booking Rules -->
            <div>
                <div class="rb-card">
                    <div class="rb-card-header">
                        <h2>Restaurant Information (UK)</h2>
                    </div>
                    <div class="rb-form-group">
                        <label>Restaurant Name</label>
                        <input type="text" name="restaurant_name" class="rb-form-control" value="<?php echo esc_attr( $settings['restaurant_name'] ); ?>" required>
                    </div>
                    <div class="rb-form-group">
                        <label>UK Contact Telephone Number</label>
                        <input type="text" name="restaurant_phone" class="rb-form-control" value="<?php echo esc_attr( $settings['restaurant_phone'] ); ?>" placeholder="e.g. 020 7946 0912" required>
                    </div>
                    <div class="rb-form-group">
                        <label>Reservations Notification Email</label>
                        <input type="email" name="restaurant_email" class="rb-form-control" value="<?php echo esc_attr( $settings['restaurant_email'] ); ?>" required>
                    </div>
                    <div class="rb-form-group">
                        <label>Restaurant Physical Address</label>
                        <input type="text" name="restaurant_address" class="rb-form-control" value="<?php echo esc_attr( $settings['restaurant_address'] ); ?>" required>
                    </div>
                </div>

                <div class="rb-card">
                    <div class="rb-card-header">
                        <h2>Confirmation Mode</h2>
                    </div>
                    <div class="rb-form-group">
                        <label>Online Booking Confirmation Mode</label>
                        <select name="auto_confirm" class="rb-form-control">
                            <option value="1" <?php selected( $settings['auto_confirm'], 1 ); ?>>Automatic Confirmation (Immediate table hold & email confirmation)</option>
                            <option value="0" <?php selected( $settings['auto_confirm'], 0 ); ?>>Manual Approval (Staff reviews reservation before confirming)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Right Column: Durations, Buffers & Legal -->
            <div>
                <div class="rb-card">
                    <div class="rb-card-header">
                        <h2>Dining Durations & Buffer Times</h2>
                    </div>
                    <div class="rb-form-group">
                        <label>Default Dining Duration (Minutes)</label>
                        <input type="number" name="default_duration" class="rb-form-control" value="<?php echo esc_attr( $settings['default_duration'] ); ?>" min="30" max="300" step="15" required>
                        <span style="font-size: 12px; color: #64748b;">Typically 120 minutes (2 hours) in UK casual and fine dining.</span>
                    </div>
                    <div class="rb-form-group">
                        <label>Turnaround / Cleanup Buffer (Minutes)</label>
                        <input type="number" name="turnaround_buffer" class="rb-form-control" value="<?php echo esc_attr( $settings['turnaround_buffer'] ); ?>" min="0" max="60" step="5" required>
                        <span style="font-size: 12px; color: #64748b;">Buffer added between consecutive bookings on the same table.</span>
                    </div>
                    <div class="rb-form-group">
                        <label>Cancellation Cut-off Window (Hours)</label>
                        <input type="number" name="cancellation_cutoff_hours" class="rb-form-control" value="<?php echo esc_attr( $settings['cancellation_cutoff_hours'] ); ?>" min="0" max="48" required>
                        <span style="font-size: 12px; color: #64748b;">Cut-off time before dining where online cancellation is permitted.</span>
                    </div>
                    <div class="rb-form-group">
                        <label>Max Advance Booking Window (Days)</label>
                        <input type="number" name="max_advance_days" class="rb-form-control" value="<?php echo esc_attr( $settings['max_advance_days'] ); ?>" min="7" max="365" required>
                    </div>
                </div>

                <div class="rb-card">
                    <div class="rb-card-header">
                        <h2>Allergen Notice & GDPR Compliance</h2>
                    </div>
                    <div class="rb-form-group">
                        <label>UK Food Allergy Advisory Notice</label>
                        <textarea name="allergen_notice" class="rb-form-control" rows="3"><?php echo esc_textarea( $settings['allergen_notice'] ); ?></textarea>
                    </div>
                    <div class="rb-form-group">
                        <label>GDPR Privacy & Data Retention Notice</label>
                        <textarea name="privacy_policy_text" class="rb-form-control" rows="2"><?php echo esc_textarea( $settings['privacy_policy_text'] ); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <button type="submit" class="rb-btn rb-btn-primary" style="padding: 12px 28px; font-size: 14px;">💾 Save All Policies & Settings</button>
        </div>
    </form>
</div>
