<?php
/**
 * Reservation Domain Service, State Machine & Concurrency Control.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RB_Reservations {

    /**
     * Create a new reservation with server-side atomic availability verification.
     */
    public static function create_reservation( $data ) {
        // 1. Sanitize & Normalize Inputs
        $customer_name   = sanitize_text_field( isset( $data['customer_name'] ) ? $data['customer_name'] : '' );
        $phone_raw       = sanitize_text_field( isset( $data['phone'] ) ? $data['phone'] : '' );
        $email           = sanitize_email( isset( $data['email'] ) ? $data['email'] : '' );
        $party_size      = max( 1, intval( isset( $data['party_size'] ) ? $data['party_size'] : 2 ) );
        $booking_date    = sanitize_text_field( isset( $data['booking_date'] ) ? $data['booking_date'] : '' );
        $start_time_raw  = sanitize_text_field( isset( $data['start_time'] ) ? $data['start_time'] : '' );
        $special_request = sanitize_textarea_field( isset( $data['special_requests'] ) ? $data['special_requests'] : '' );
        $dietary_notes   = sanitize_textarea_field( isset( $data['dietary_notes'] ) ? $data['dietary_notes'] : '' );
        $marketing       = ! empty( $data['marketing_consent'] ) ? 1 : 0;
        $source          = sanitize_text_field( isset( $data['source'] ) ? $data['source'] : 'web' );

        // 2. Validate mandatory fields
        if ( empty( $customer_name ) || empty( $phone_raw ) || empty( $email ) || empty( $booking_date ) || empty( $start_time_raw ) ) {
            return new WP_Error( 'missing_fields', __( 'Please fill in all required contact and reservation details.', 'restaurant-booking' ) );
        }

        if ( ! is_email( $email ) ) {
            return new WP_Error( 'invalid_email', __( 'Please provide a valid email address.', 'restaurant-booking' ) );
        }

        if ( ! RB_i18n::is_valid_uk_phone( $phone_raw ) ) {
            return new WP_Error( 'invalid_phone', __( 'Please provide a valid contact telephone number.', 'restaurant-booking' ) );
        }

        $phone = RB_i18n::normalize_phone_uk( $phone_raw );
        $start_time = substr( $start_time_raw, 0, 5 ) . ':00';

        // 3. Compute duration and end time
        $duration_minutes = intval( get_option( 'rb_default_duration', 120 ) );
        $turnaround_buffer = intval( get_option( 'rb_turnaround_buffer', 15 ) );
        $end_time = date( 'H:i:s', strtotime( $booking_date . ' ' . $start_time ) + ( $duration_minutes * 60 ) );

        // 4. Server-Side Availability & Table Allocation Recheck (Double-Booking Race Condition Prevention)
        $tables = RB_QR_Adapter::get_canonical_tables();
        $existing_bookings = RB_DB::get_results(
            "SELECT * FROM " . RB_DB::table( 'reservations' ) . " WHERE booking_date = %s AND status IN ('pending', 'confirmed', 'seated')",
            $booking_date
        );

        $allocated = RB_Availability::find_best_table_for_slot(
            $tables,
            $existing_bookings,
            $booking_date,
            $start_time,
            $party_size,
            $duration_minutes,
            $turnaround_buffer
        );

        if ( ! $allocated ) {
            $check = RB_Availability::check_slot_availability( $booking_date, $start_time, $party_size );
            return new WP_Error( 'slot_unavailable', __( 'Sorry, this time slot has just been booked. Please choose an alternative time.', 'restaurant-booking' ), array(
                'nearby_slots' => $check['nearby_slots'],
            ) );
        }

        // 5. Generate Reference & Passwordless Management Token
        $booking_reference = self::generate_reference();
        $secure_token      = wp_generate_password( 32, false, false );
        $token_hash        = hash( 'sha256', $secure_token );

        // 6. Determine Initial Status based on auto-confirm option
        $auto_confirm = intval( get_option( 'rb_auto_confirm', 1 ) );
        $status = ( $auto_confirm === 1 || $source === 'staff' || $source === 'walk_in' ) ? 'confirmed' : 'pending';
        $confirmation_mode = ( $auto_confirm === 1 ) ? 'auto' : 'manual';

        $now = RB_i18n::now_uk();

        $table_id = $allocated['table_id'];
        $table_number_display = $allocated['table_number'];

        // 7. Insert Reservation Record
        $reservation_id = RB_DB::insert( 'reservations', array(
            'booking_reference'    => $booking_reference,
            'secure_token'         => $secure_token,
            'token_hash'           => $token_hash,
            'customer_name'        => $customer_name,
            'phone'                => $phone,
            'email'                => $email,
            'party_size'           => $party_size,
            'booking_date'         => $booking_date,
            'start_time'           => $start_time,
            'end_time'             => $end_time,
            'status'               => $status,
            'confirmation_mode'    => $confirmation_mode,
            'special_requests'     => $special_request,
            'dietary_notes'        => $dietary_notes,
            'marketing_consent'    => $marketing,
            'source'               => $source,
            'table_id'             => $table_id,
            'table_number_display' => $table_number_display,
            'created_at'           => $now,
            'updated_at'           => $now,
        ) );

        if ( ! $reservation_id ) {
            return new WP_Error( 'db_error', __( 'Could not save reservation. Please try again.', 'restaurant-booking' ) );
        }

        // 8. Insert Table Allocation records
        if ( isset( $allocated['table_ids'] ) && is_array( $allocated['table_ids'] ) ) {
            foreach ( $allocated['table_ids'] as $tid ) {
                RB_DB::insert( 'reservation_tables', array(
                    'reservation_id' => $reservation_id,
                    'table_id'       => intval( $tid ),
                    'created_at'     => $now,
                ) );
            }
        } else {
            RB_DB::insert( 'reservation_tables', array(
                'reservation_id' => $reservation_id,
                'table_id'       => intval( $table_id ),
                'created_at'     => $now,
            ) );
        }

        // 9. Audit Event
        RB_DB::log_event(
            $reservation_id,
            'BOOKING_CREATED',
            sprintf( 'Reservation %s created (%s guests for %s %s). Status: %s. Table: %s', $booking_reference, $party_size, $booking_date, $start_time, $status, $table_number_display ),
            $source
        );

        // 10. Fire Hooks & Notifications
        if ( $status === 'confirmed' ) {
            do_action( 'restaurant_booking_confirmed', $reservation_id );
        } else {
            do_action( 'restaurant_booking_created', $reservation_id );
        }

        RB_Notifications::send_booking_confirmation( $reservation_id );
        RB_Notifications::send_admin_new_booking_alert( $reservation_id );

        return self::get_by_id( $reservation_id );
    }

    /**
     * Get reservation by ID.
     */
    public static function get_by_id( $id ) {
        $res = RB_DB::get_row( "SELECT * FROM " . RB_DB::table( 'reservations' ) . " WHERE id = %d", $id );
        if ( $res ) {
            $res = self::format_reservation_record( $res );
        }
        return $res;
    }

    /**
     * Get reservation by booking reference.
     */
    public static function get_by_reference( $reference ) {
        $res = RB_DB::get_row( "SELECT * FROM " . RB_DB::table( 'reservations' ) . " WHERE booking_reference = %s", sanitize_text_field( $reference ) );
        if ( $res ) {
            $res = self::format_reservation_record( $res );
        }
        return $res;
    }

    /**
     * Get reservation by secure management token.
     */
    public static function get_by_token( $token ) {
        $clean_token = sanitize_text_field( $token );
        $token_hash  = hash( 'sha256', $clean_token );

        $res = RB_DB::get_row( "SELECT * FROM " . RB_DB::table( 'reservations' ) . " WHERE secure_token = %s OR token_hash = %s", $clean_token, $token_hash );
        if ( $res ) {
            $res = self::format_reservation_record( $res );
        }
        return $res;
    }

    /**
     * Update reservation status with lifecycle events.
     */
    public static function update_status( $id, $new_status, $notes = '' ) {
        $res = self::get_by_id( $id );
        if ( ! $res ) {
            return false;
        }

        $old_status = $res['status'];
        $now = RB_i18n::now_uk();

        $update_data = array(
            'status'     => sanitize_text_field( $new_status ),
            'updated_at' => $now,
        );

        // Seating lifecycle
        if ( $new_status === 'seated' && $old_status !== 'seated' ) {
            $table_id = intval( $res['table_id'] );
            $session_id = RB_QR_Adapter::seat_guest_and_create_session( $id, $table_id );
            if ( $session_id ) {
                $update_data['linked_session_id'] = $session_id;
            }
        }

        // Completion or release lifecycle
        if ( in_array( $new_status, array( 'completed', 'cancelled', 'no_show' ), true ) ) {
            if ( $old_status === 'seated' ) {
                RB_QR_Adapter::release_table( $res['table_id'] );
            }
        }

        RB_DB::update( 'reservations', $update_data, array( 'id' => intval( $id ) ) );

        RB_DB::log_event(
            $id,
            'STATUS_CHANGE',
            sprintf( 'Status updated from %s to %s. %s', strtoupper( $old_status ), strtoupper( $new_status ), $notes )
        );

        // Fire Action Hooks
        switch ( $new_status ) {
            case 'confirmed':
                do_action( 'restaurant_booking_confirmed', $id );
                break;
            case 'seated':
                $session_id = isset( $update_data['linked_session_id'] ) ? $update_data['linked_session_id'] : $res['linked_session_id'];
                do_action( 'restaurant_booking_seated', $id, $res['table_id'], $session_id );
                break;
            case 'cancelled':
                do_action( 'restaurant_booking_cancelled', $id );
                RB_Notifications::send_cancellation_confirmation( $id );
                break;
            case 'completed':
                do_action( 'restaurant_booking_completed', $id, $res['linked_session_id'] );
                break;
            case 'no_show':
                do_action( 'restaurant_booking_noshow', $id );
                break;
        }

        return self::get_by_id( $id );
    }

    /**
     * Guest Self-Service Cancellation (with cut-off rule validation)
     */
    public static function cancel_by_guest( $token, $reason = '' ) {
        $res = self::get_by_token( $token );
        if ( ! $res ) {
            return new WP_Error( 'not_found', __( 'Reservation not found.', 'restaurant-booking' ) );
        }

        if ( in_array( $res['status'], array( 'cancelled', 'completed', 'no_show' ), true ) ) {
            return new WP_Error( 'already_closed', sprintf( __( 'This reservation is already %s.', 'restaurant-booking' ), $res['status'] ) );
        }

        // Validate Cut-off Window (e.g. 2 hours before reservation start)
        $cutoff_hours = intval( get_option( 'rb_cancellation_cutoff_hours', 2 ) );
        $booking_ts   = strtotime( $res['booking_date'] . ' ' . $res['start_time'] );
        $cutoff_ts    = $booking_ts - ( $cutoff_hours * 3600 );
        $now_ts       = RB_i18n::get_now()->getTimestamp();

        if ( $now_ts > $cutoff_ts ) {
            $phone = get_option( 'rb_restaurant_phone', '020 7946 0912' );
            return new WP_Error(
                'cutoff_passed',
                sprintf( __( 'Online cancellation is only permitted up to %d hours before dining. Please contact the restaurant directly on %s.', 'restaurant-booking' ), $cutoff_hours, $phone )
            );
        }

        $note = ! empty( $reason ) ? 'Customer Reason: ' . sanitize_text_field( $reason ) : 'Cancelled online by guest.';
        return self::update_status( $res['id'], 'cancelled', $note );
    }

    /**
     * Create Fast Walk-In from Admin (10-second flow)
     */
    public static function create_walk_in( $party_size, $table_id, $customer_name = 'Walk-In Guest', $notes = '' ) {
        $party_size = max( 1, intval( $party_size ) );
        $table_id   = intval( $table_id );
        $today      = RB_i18n::today_uk();
        $now_time   = RB_i18n::get_now()->format( 'H:i:s' );
        $duration   = intval( get_option( 'rb_default_duration', 120 ) );
        $end_time   = date( 'H:i:s', strtotime( $today . ' ' . $now_time ) + ( $duration * 60 ) );

        $table = RB_QR_Adapter::get_table_by_id( $table_id );
        $table_name = $table ? $table['table_number'] : 'T' . $table_id;

        $reference = self::generate_reference( 'WI-' );
        $token     = wp_generate_password( 32, false, false );
        $now       = RB_i18n::now_uk();

        $reservation_id = RB_DB::insert( 'reservations', array(
            'booking_reference'    => $reference,
            'secure_token'         => $token,
            'token_hash'           => hash( 'sha256', $token ),
            'customer_name'        => sanitize_text_field( $customer_name ),
            'phone'                => 'N/A Walk-In',
            'email'                => 'walkin@restaurant.local',
            'party_size'           => $party_size,
            'booking_date'         => $today,
            'start_time'           => $now_time,
            'end_time'             => $end_time,
            'status'               => 'seated',
            'confirmation_mode'    => 'auto',
            'special_requests'     => sanitize_textarea_field( $notes ),
            'source'               => 'walk_in',
            'table_id'             => $table_id,
            'table_number_display' => $table_name,
            'created_at'           => $now,
            'updated_at'           => $now,
        ) );

        // Seat table & create QR session
        $session_id = RB_QR_Adapter::seat_guest_and_create_session( $reservation_id, $table_id );
        if ( $session_id ) {
            RB_DB::update( 'reservations', array( 'linked_session_id' => $session_id ), array( 'id' => $reservation_id ) );
        }

        RB_DB::log_event( $reservation_id, 'WALK_IN_CREATED', "Walk-in guest seated at Table {$table_name}.", 'staff' );

        do_action( 'restaurant_booking_seated', $reservation_id, $table_id, $session_id );

        return self::get_by_id( $reservation_id );
    }

    /**
     * Generate unique UK booking reference (e.g., CR-48192)
     */
    public static function generate_reference( $prefix = 'CR-' ) {
        return $prefix . wp_rand( 10000, 99999 );
    }

    /**
     * Format raw DB record with helper metadata & UK display strings.
     */
    private static function format_reservation_record( $res ) {
        $res['id']           = intval( $res['id'] );
        $res['party_size']   = intval( $res['party_size'] );
        $res['table_id']     = ! empty( $res['table_id'] ) ? intval( $res['table_id'] ) : null;
        $res['date_formatted'] = RB_i18n::format_date_uk( $res['booking_date'], 'l, j F Y' );
        $res['date_short']     = RB_i18n::format_date_uk( $res['booking_date'], 'd/m/Y' );
        $res['time_formatted'] = RB_i18n::format_time_uk( $res['start_time'] );
        $res['end_formatted']  = RB_i18n::format_time_uk( $res['end_time'] );
        $res['manage_url']     = home_url( '/booking/manage/' . $res['secure_token'] );
        $res['status_label']   = strtoupper( $res['status'] );
        return $res;
    }
}
