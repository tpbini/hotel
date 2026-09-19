<?php
/**
 * High-Precision UK Restaurant Booking Availability Engine.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RB_Availability {

    /**
     * Get all available booking slots for a specified date, party size, and optional duration.
     */
    public static function get_slots_for_date( $date, $party_size, $duration_minutes = null ) {
        $party_size = max( 1, intval( $party_size ) );
        $date = sanitize_text_field( $date );
        $duration_minutes = ! empty( $duration_minutes ) ? max( 30, intval( $duration_minutes ) ) : intval( get_option( 'rb_default_duration', 120 ) );

        // Validate date format YYYY-MM-DD
        if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ) {
            return array(
                'success' => false,
                'message' => __( 'Invalid date format. Expected YYYY-MM-DD.', 'restaurant-booking' ),
                'slots'   => array(),
            );
        }

        // Check date in past (UK London time)
        $today = RB_i18n::today_uk();
        if ( $date < $today ) {
            return array(
                'success' => false,
                'message' => __( 'Cannot book dates in the past.', 'restaurant-booking' ),
                'slots'   => array(),
            );
        }

        // Check max advance booking days
        $max_days = intval( get_option( 'rb_max_advance_days', 90 ) );
        $max_date = date( 'Y-m-d', strtotime( "+{$max_days} days", strtotime( $today ) ) );
        if ( $date > $max_date ) {
            return array(
                'success' => false,
                'message' => sprintf( __( 'Bookings are only accepted up to %d days in advance.', 'restaurant-booking' ), $max_days ),
                'slots'   => array(),
            );
        }

        // Check special dates / holiday exceptions
        $special = RB_DB::get_row( "SELECT * FROM " . RB_DB::table( 'special_dates' ) . " WHERE special_date = %s", $date );
        if ( $special && $special['action_type'] === 'closed' ) {
            return array(
                'success' => true,
                'is_closed' => true,
                'message' => sprintf( __( 'The restaurant is closed on this date (%s).', 'restaurant-booking' ), $special['name'] ),
                'services' => array(),
                'slots' => array(),
            );
        }

        // Get Service Periods for this Day of Week (1=Monday ... 7=Sunday)
        $day_of_week = intval( date( 'N', strtotime( $date ) ) );
        $services = array();

        if ( $special && $special['action_type'] === 'custom_hours' ) {
            $services[] = array(
                'service_name'  => $special['name'] ? $special['name'] : 'Special Service',
                'open_time'     => $special['open_time'],
                'close_time'    => $special['close_time'],
                'slot_interval' => intval( get_option( 'rb_booking_interval', 30 ) ),
            );
        } else {
            $services = RB_DB::get_results(
                "SELECT * FROM " . RB_DB::table( 'opening_hours' ) . " WHERE day_of_week = %d AND is_active = 1 ORDER BY open_time ASC",
                $day_of_week
            );
        }

        if ( empty( $services ) ) {
            return array(
                'success'   => true,
                'is_closed' => true,
                'message'   => __( 'The restaurant is not open on this day of the week.', 'restaurant-booking' ),
                'services'  => array(),
                'slots'     => array(),
            );
        }

        if ( empty( $duration_minutes ) ) {
            $duration_minutes = intval( get_option( 'rb_default_duration', 120 ) );
        }
        $turnaround_buffer = intval( get_option( 'rb_turnaround_buffer', 15 ) );
        $tables = RB_QR_Adapter::get_canonical_tables();

        // Get all active reservations and walk-ins for this date
        $existing_bookings = RB_DB::get_results(
            "SELECT * FROM " . RB_DB::table( 'reservations' ) . " WHERE booking_date = %s AND status IN ('pending', 'confirmed', 'seated')",
            $date
        );

        $now_dt = RB_i18n::get_now();
        $is_today = ( $date === $today );

        $grouped_slots = array();
        $all_slots = array();

        foreach ( $services as $service ) {
            $open_ts  = strtotime( $date . ' ' . $service['open_time'] );
            $close_ts = strtotime( $date . ' ' . $service['close_time'] );
            $interval = ! empty( $service['slot_interval'] ) ? intval( $service['slot_interval'] ) : 30;
            $interval_sec = $interval * 60;

            // Last allowable booking start time must allow dining before close
            $last_booking_ts = $close_ts - ( 30 * 60 ); // at least 30m before close

            $service_slots = array();

            for ( $slot_ts = $open_ts; $slot_ts <= $last_booking_ts; $slot_ts += $interval_sec ) {
                $time_str = date( 'H:i:s', $slot_ts );
                $display_time = RB_i18n::format_time_uk( $time_str );

                // If today, slot must be at least 30 minutes in future
                if ( $is_today ) {
                    $slot_dt = new DateTime( $date . ' ' . $time_str, RB_i18n::get_timezone() );
                    if ( $slot_dt->getTimestamp() <= ( $now_dt->getTimestamp() + ( 20 * 60 ) ) ) {
                        continue;
                    }
                }

                // Check table availability for this slot
                $allocated_table = self::find_best_table_for_slot(
                    $tables,
                    $existing_bookings,
                    $date,
                    $time_str,
                    $party_size,
                    $duration_minutes,
                    $turnaround_buffer
                );

                $is_available = ! is_null( $allocated_table );

                $slot_data = array(
                    'time'          => $time_str,
                    'display_time'  => $display_time,
                    'service_name'  => $service['service_name'],
                    'is_available'  => $is_available,
                    'party_size'    => $party_size,
                );

                $service_slots[] = $slot_data;
                $all_slots[] = $slot_data;
            }

            if ( ! empty( $service_slots ) ) {
                $grouped_slots[] = array(
                    'service_name' => $service['service_name'],
                    'open_time'    => RB_i18n::format_time_uk( $service['open_time'] ),
                    'close_time'   => RB_i18n::format_time_uk( $service['close_time'] ),
                    'slots'        => $service_slots,
                );
            }
        }

        return array(
            'success'       => true,
            'is_closed'     => false,
            'date'          => $date,
            'date_formatted'=> RB_i18n::format_date_uk( $date, 'l, j F Y' ),
            'party_size'    => $party_size,
            'services'      => $grouped_slots,
            'all_slots'     => $all_slots,
        );
    }

    /**
     * Check a specific slot's availability, and return nearby alternatives if booked out.
     */
    public static function check_slot_availability( $date, $time, $party_size ) {
        $slots_result = self::get_slots_for_date( $date, $party_size );
        if ( ! $slots_result['success'] || ! empty( $slots_result['is_closed'] ) ) {
            return array(
                'available'    => false,
                'message'      => isset( $slots_result['message'] ) ? $slots_result['message'] : __( 'Unavailable', 'restaurant-booking' ),
                'nearby_slots' => array(),
            );
        }

        $clean_time = substr( $time, 0, 5 ) . ':00';
        $is_exact_available = false;
        $nearby_slots = array();

        $requested_ts = strtotime( $date . ' ' . $clean_time );

        foreach ( $slots_result['all_slots'] as $slot ) {
            $slot_ts = strtotime( $date . ' ' . $slot['time'] );

            if ( substr( $slot['time'], 0, 5 ) === substr( $clean_time, 0, 5 ) ) {
                if ( $slot['is_available'] ) {
                    $is_exact_available = true;
                }
            } elseif ( $slot['is_available'] ) {
                // Check if within +/- 90 minutes
                $diff_minutes = abs( $slot_ts - $requested_ts ) / 60;
                if ( $diff_minutes <= 90 ) {
                    $nearby_slots[] = $slot;
                }
            }
        }

        // Sort nearby slots by closest time difference
        usort( $nearby_slots, function( $a, $b ) use ( $date, $requested_ts ) {
            $diffA = abs( strtotime( $date . ' ' . $a['time'] ) - $requested_ts );
            $diffB = abs( strtotime( $date . ' ' . $b['time'] ) - $requested_ts );
            return $diffA - $diffB;
        } );

        return array(
            'available'    => $is_exact_available,
            'time'         => $clean_time,
            'display_time' => RB_i18n::format_time_uk( $clean_time ),
            'nearby_slots' => array_slice( $nearby_slots, 0, 4 ),
        );
    }

    /**
     * Best-Fit Table Allocation Engine
     * Selects the smallest suitable physical table or combination that satisfies party size without overlapping.
     */
    public static function find_best_table_for_slot( $tables, $existing_bookings, $date, $time_str, $party_size, $duration_minutes = 120, $turnaround_buffer = 15 ) {
        $req_start = strtotime( $date . ' ' . $time_str );
        $req_end   = $req_start + ( $duration_minutes * 60 );

        // 1. Single Table Match: Filter candidate tables that can seat the party
        $candidate_tables = array();
        foreach ( $tables as $table ) {
            if ( $table['capacity'] >= $party_size ) {
                $candidate_tables[] = $table;
            }
        }

        // Sort candidates by capacity ascending (Best Fit to prevent wasting larger tables)
        usort( $candidate_tables, function( $a, $b ) {
            return $a['capacity'] - $b['capacity'];
        } );

        foreach ( $candidate_tables as $table ) {
            if ( ! self::is_table_conflicted( $table['id'], $existing_bookings, $req_start, $req_end, $turnaround_buffer ) ) {
                return array(
                    'type'         => 'single',
                    'table_id'     => $table['id'],
                    'table_number' => $table['table_number'],
                    'capacity'     => $table['capacity'],
                );
            }
        }

        // 2. Table Combination Match (if no single table is free)
        $combinations = RB_DB::get_results(
            "SELECT * FROM " . RB_DB::table( 'table_combinations' ) . " WHERE combined_capacity >= %d AND is_active = 1 ORDER BY combined_capacity ASC",
            $party_size
        );

        foreach ( $combinations as $combo ) {
            $t1_free = ! self::is_table_conflicted( $combo['primary_table_id'], $existing_bookings, $req_start, $req_end, $turnaround_buffer );
            $t2_free = ! self::is_table_conflicted( $combo['combined_table_id'], $existing_bookings, $req_start, $req_end, $turnaround_buffer );

            if ( $t1_free && $t2_free ) {
                $t1 = RB_QR_Adapter::get_table_by_id( $combo['primary_table_id'] );
                $t2 = RB_QR_Adapter::get_table_by_id( $combo['combined_table_id'] );
                $num_display = ( $t1 ? $t1['table_number'] : '' ) . ' + ' . ( $t2 ? $t2['table_number'] : '' );

                return array(
                    'type'         => 'combination',
                    'combo_id'     => $combo['id'],
                    'table_id'     => $combo['primary_table_id'],
                    'table_ids'    => array( $combo['primary_table_id'], $combo['combined_table_id'] ),
                    'table_number' => $num_display,
                    'capacity'     => $combo['combined_capacity'],
                );
            }
        }

        return null;
    }

    /**
     * Check if a specific table has an overlapping booking.
     */
    private static function is_table_conflicted( $table_id, $existing_bookings, $req_start, $req_end, $turnaround_buffer ) {
        $buffer_sec = $turnaround_buffer * 60;

        foreach ( $existing_bookings as $booking ) {
            // Check if this booking uses this table directly or via reservation_tables
            $is_on_table = ( intval( $booking['table_id'] ) === intval( $table_id ) );

            if ( $is_on_table ) {
                $b_start = strtotime( $booking['booking_date'] . ' ' . $booking['start_time'] );
                $b_end   = strtotime( $booking['booking_date'] . ' ' . $booking['end_time'] );

                // Account for cleanup turnaround buffer on both sides
                $b_window_end = $b_end + $buffer_sec;
                $req_window_end = $req_end + $buffer_sec;

                // Overlap condition: max(start1, start2) < min(end1, end2)
                $overlap_start = max( $req_start, $b_start );
                $overlap_end   = min( $req_window_end, $b_window_end );

                if ( $overlap_start < $overlap_end ) {
                    return true; // Overlap detected!
                }
            }
        }

        return false;
    }
}
