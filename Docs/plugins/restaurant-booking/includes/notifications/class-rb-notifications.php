<?php
/**
 * UK HTML Email Notifications, iCal Generator & Alerts.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RB_Notifications {

    /**
     * Send Customer Booking Confirmation Email
     */
    public static function send_booking_confirmation( $reservation_id ) {
        $res = RB_Reservations::get_by_id( $reservation_id );
        if ( ! $res || empty( $res['email'] ) ) {
            return false;
        }

        $restaurant_name = get_option( 'rb_restaurant_name', 'The Crown & Thistle' );
        $restaurant_phone = get_option( 'rb_restaurant_phone', '020 7946 0912' );
        $restaurant_addr = get_option( 'rb_restaurant_address', 'London, UK' );
        $allergen_notice = get_option( 'rb_allergen_notice', 'If you or any guest have a severe food allergy, please inform us directly by phone.' );

        $subject = sprintf( '[%s] Table Reservation Confirmed - Ref: %s', $restaurant_name, $res['booking_reference'] );

        $google_cal_url = self::generate_google_cal_link( $res, $restaurant_name, $restaurant_addr );

        $html = self::get_email_header( $restaurant_name );
        $html .= '<div style="padding: 24px; background: #ffffff; color: #1e293b; font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif;">';
        $html .= '<h2 style="color: #0f172a; margin-top: 0; font-size: 22px;">Reservation Confirmed</h2>';
        $html .= '<p>Dear <strong>' . esc_html( $res['customer_name'] ) . '</strong>,</p>';
        $html .= '<p>Thank you for booking with <strong>' . esc_html( $restaurant_name ) . '</strong>. We look forward to welcoming you.</p>';
        
        $html .= '<div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; margin: 20px 0;">';
        $html .= '<table style="width: 100%; border-collapse: collapse; font-size: 15px;">';
        $html .= '<tr><td style="padding: 6px 0; color: #64748b; width: 140px;">Booking Ref:</td><td style="padding: 6px 0; font-weight: bold; color: #0284c7; font-size: 16px;">' . esc_html( $res['booking_reference'] ) . '</td></tr>';
        $html .= '<tr><td style="padding: 6px 0; color: #64748b;">Date:</td><td style="padding: 6px 0; font-weight: 600;">' . esc_html( $res['date_formatted'] ) . '</td></tr>';
        $html .= '<tr><td style="padding: 6px 0; color: #64748b;">Time:</td><td style="padding: 6px 0; font-weight: 600;">' . esc_html( $res['time_formatted'] ) . '</td></tr>';
        $html .= '<tr><td style="padding: 6px 0; color: #64748b;">Party Size:</td><td style="padding: 6px 0; font-weight: 600;">' . esc_html( $res['party_size'] ) . ' Guests</td></tr>';
        if ( ! empty( $res['table_number_display'] ) ) {
            $html .= '<tr><td style="padding: 6px 0; color: #64748b;">Table Allocation:</td><td style="padding: 6px 0;">' . esc_html( $res['table_number_display'] ) . '</td></tr>';
        }
        if ( ! empty( $res['special_requests'] ) ) {
            $html .= '<tr><td style="padding: 6px 0; color: #64748b;">Special Requests:</td><td style="padding: 6px 0;">' . esc_html( $res['special_requests'] ) . '</td></tr>';
        }
        if ( ! empty( $res['dietary_notes'] ) ) {
            $html .= '<tr><td style="padding: 6px 0; color: #64748b;">Dietary / Allergies:</td><td style="padding: 6px 0; color: #b91c1c; font-weight: 500;">' . esc_html( $res['dietary_notes'] ) . '</td></tr>';
        }
        $html .= '</table>';
        $html .= '</div>';

        // Dietary & Allergy UK Advisory
        $html .= '<div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 12px 16px; margin: 20px 0; border-radius: 4px; font-size: 13px; color: #991b1b;">';
        $html .= '<strong>Allergy Notice:</strong> ' . esc_html( $allergen_notice );
        $html .= '</div>';

        // Action Buttons
        $html .= '<div style="margin: 28px 0; text-align: center;">';
        $html .= '<a href="' . esc_url( $google_cal_url ) . '" target="_blank" style="background: #0f172a; color: #ffffff; text-decoration: none; padding: 12px 20px; border-radius: 6px; font-weight: 600; display: inline-block; margin-right: 10px;">📅 Add to Google Calendar</a>';
        $html .= '<a href="' . esc_url( $res['manage_url'] ) . '" target="_blank" style="background: #f1f5f9; color: #334155; text-decoration: none; padding: 12px 20px; border-radius: 6px; font-weight: 600; display: inline-block; border: 1px solid #cbd5e1;">Manage / Cancel Booking</a>';
        $html .= '</div>';

        $html .= '<p style="font-size: 13px; color: #64748b;">Need to speak with us? Telephone: <a href="tel:' . esc_attr( $restaurant_phone ) . '" style="color: #0284c7;">' . esc_html( $restaurant_phone ) . '</a> | Address: ' . esc_html( $restaurant_addr ) . '</p>';
        $html .= '</div>';
        $html .= self::get_email_footer( $restaurant_name );

        $sent = wp_mail( $res['email'], $subject, $html, array( 'Content-Type: text/html; charset=UTF-8' ) );

        RB_DB::insert( 'notifications', array(
            'reservation_id' => $reservation_id,
            'type'           => 'confirmation',
            'recipient'      => $res['email'],
            'subject'        => $subject,
            'status'         => $sent ? 'sent' : 'failed',
            'sent_at'        => RB_i18n::now_uk(),
        ) );

        return $sent;
    }

    /**
     * Send Customer Cancellation Email
     */
    public static function send_cancellation_confirmation( $reservation_id ) {
        $res = RB_Reservations::get_by_id( $reservation_id );
        if ( ! $res || empty( $res['email'] ) ) {
            return false;
        }

        $restaurant_name = get_option( 'rb_restaurant_name', 'The Crown & Thistle' );
        $subject = sprintf( '[%s] Reservation Cancelled - Ref: %s', $restaurant_name, $res['booking_reference'] );

        $html = self::get_email_header( $restaurant_name );
        $html .= '<div style="padding: 24px; background: #ffffff; color: #1e293b;">';
        $html .= '<h2 style="color: #991b1b; margin-top: 0;">Reservation Cancelled</h2>';
        $html .= '<p>Dear <strong>' . esc_html( $res['customer_name'] ) . '</strong>,</p>';
        $html .= '<p>Your table reservation (Reference: <strong>' . esc_html( $res['booking_reference'] ) . '</strong>) for <strong>' . esc_html( $res['date_formatted'] ) . ' at ' . esc_html( $res['time_formatted'] ) . '</strong> has been cancelled as requested.</p>';
        $html .= '<p>We hope to welcome you another time soon!</p>';
        $html .= '</div>';
        $html .= self::get_email_footer( $restaurant_name );

        return wp_mail( $res['email'], $subject, $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
    }

    /**
     * Send Admin Alert for New Reservation
     */
    public static function send_admin_new_booking_alert( $reservation_id ) {
        $res = RB_Reservations::get_by_id( $reservation_id );
        if ( ! $res ) {
            return false;
        }

        $admin_email = get_option( 'rb_restaurant_email', get_option( 'admin_email' ) );
        if ( empty( $admin_email ) ) {
            return false;
        }

        $subject = sprintf( 'New Booking: %s (%d Guests) on %s at %s', $res['customer_name'], $res['party_size'], $res['date_short'], $res['time_formatted'] );

        $admin_url = admin_url( 'admin.php?page=restaurant-booking-reservations' );

        $html = '<p>A new table reservation has been received:</p>';
        $html .= '<ul>';
        $html .= '<li><strong>Reference:</strong> ' . esc_html( $res['booking_reference'] ) . '</li>';
        $html .= '<li><strong>Customer:</strong> ' . esc_html( $res['customer_name'] ) . ' (' . esc_html( $res['phone'] ) . ')</li>';
        $html .= '<li><strong>Date & Time:</strong> ' . esc_html( $res['date_formatted'] ) . ' @ ' . esc_html( $res['time_formatted'] ) . '</li>';
        $html .= '<li><strong>Party Size:</strong> ' . esc_html( $res['party_size'] ) . ' guests</li>';
        $html .= '<li><strong>Table:</strong> ' . esc_html( $res['table_number_display'] ) . '</li>';
        $html .= '<li><strong>Dietary/Allergy:</strong> ' . esc_html( $res['dietary_notes'] ? $res['dietary_notes'] : 'None' ) . '</li>';
        $html .= '<li><strong>Special Requests:</strong> ' . esc_html( $res['special_requests'] ? $res['special_requests'] : 'None' ) . '</li>';
        $html .= '</ul>';
        $html .= '<p><a href="' . esc_url( $admin_url ) . '">View in Restaurant Booking Admin &rarr;</a></p>';

        return wp_mail( $admin_email, $subject, $html, array( 'Content-Type: text/html; charset=UTF-8' ) );
    }

    private static function generate_google_cal_link( $res, $name, $addr ) {
        $start_dt = new DateTime( $res['booking_date'] . ' ' . $res['start_time'], RB_i18n::get_timezone() );
        $end_dt   = new DateTime( $res['booking_date'] . ' ' . $res['end_time'], RB_i18n::get_timezone() );

        // Convert to UTC ISO format for Google Calendar
        $start_dt->setTimezone( new DateTimeZone( 'UTC' ) );
        $end_dt->setTimezone( new DateTimeZone( 'UTC' ) );

        $dates = $start_dt->format( 'Ymd\THis\Z' ) . '/' . $end_dt->format( 'Ymd\THis\Z' );
        $title = urlencode( 'Table Reservation at ' . $name );
        $details = urlencode( "Booking Ref: {$res['booking_reference']}\nParty Size: {$res['party_size']} Guests\nManage: {$res['manage_url']}" );
        $location = urlencode( $addr );

        return "https://calendar.google.com/calendar/render?action=TEMPLATE&text={$title}&dates={$dates}&details={$details}&location={$location}";
    }

    private static function get_email_header( $restaurant_name ) {
        return '<!DOCTYPE html><html><head><meta charset="utf-8"></head><body style="margin: 0; padding: 20px; background: #f1f5f9; font-family: sans-serif;"><div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);"><div style="background: #0f172a; padding: 24px; text-align: center; color: #ffffff;"><h1 style="margin: 0; font-size: 24px; font-weight: 700; letter-spacing: 0.5px;">' . esc_html( $restaurant_name ) . '</h1><p style="margin: 4px 0 0 0; color: #94a3b8; font-size: 14px;">Table Reservation Service</p></div>';
    }

    private static function get_email_footer( $restaurant_name ) {
        return '<div style="background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 24px; text-align: center; font-size: 12px; color: #94a3b8;"><p style="margin: 0;">&copy; ' . date( 'Y' ) . ' ' . esc_html( $restaurant_name ) . '. All rights reserved.</p></div></div></body></html>';
    }
}
