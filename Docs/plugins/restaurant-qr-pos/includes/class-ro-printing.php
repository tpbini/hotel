<?php
/**
 * Persistent Print Queue & Thermal Ticket Formatting.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RO_Printing {

    /**
     * Queue Kitchen Order Tickets (KOT) grouped by Station
     */
    public static function queue_kitchen_print_jobs( $order_id ) {
        $order = RO_Orders::get_order_by_id( $order_id );
        if ( ! $order || empty( $order['items'] ) ) {
            return;
        }

        // Group items by station
        $station_groups = array();
        foreach ( $order['items'] as $item ) {
            $station_id = $item['station_id'] ? $item['station_id'] : 1;
            $station_groups[ $station_id ][] = $item;
        }

        foreach ( $station_groups as $station_id => $items ) {
            $station = RO_Stations::get_by_id( $station_id );
            $station_name = $station ? $station['name'] : 'Kitchen';

            $payload = array(
                'ticket_type'   => 'KOT',
                'order_id'      => $order['id'],
                'order_number'  => $order['order_number'],
                'table_number'  => $order['table']['table_number'] ?? 'N/A',
                'source'        => $order['source'],
                'created_at'    => $order['created_at'],
                'station_id'    => $station_id,
                'station_name'  => $station_name,
                'items'         => array(),
                'order_notes'   => $order['notes'],
            );

            foreach ( $items as $it ) {
                $mods = array();
                if ( ! empty( $it['modifiers'] ) ) {
                    foreach ( $it['modifiers'] as $m ) {
                        $mods[] = $m['modifier_name'];
                    }
                }
                $payload['items'][] = array(
                    'name'      => $it['item_name'],
                    'quantity'  => $it['quantity'],
                    'notes'     => $it['notes'],
                    'modifiers' => $mods,
                );
            }

            RO_DB::insert( 'print_jobs', array(
                'order_id'     => $order['id'],
                'session_id'   => $order['session_id'],
                'station_id'   => $station_id,
                'printer_type' => 'kitchen',
                'status'       => 'QUEUED',
                'payload'      => wp_json_encode( $payload ),
                'retry_count'  => 0,
                'created_at'   => current_time( 'mysql' ),
            ) );
        }
    }

    /**
     * Queue Customer Invoice / Receipt Print Job for Counter POS
     */
    public static function queue_counter_receipt_job( $session_id, $invoice_number, $payment_method ) {
        $session = RO_Sessions::get_by_id( $session_id );
        if ( ! $session ) {
            return;
        }

        $restaurant_name    = get_option( 'ro_restaurant_name', 'Grand Bistro & Grill' );
        $restaurant_phone   = get_option( 'ro_restaurant_phone', '' );
        $restaurant_address = get_option( 'ro_restaurant_address', '' );
        $currency_symbol    = get_option( 'ro_currency_symbol', '$' );

        $payload = array(
            'ticket_type'        => 'INVOICE',
            'restaurant_name'    => $restaurant_name,
            'restaurant_phone'   => $restaurant_phone,
            'restaurant_address' => $restaurant_address,
            'currency_symbol'    => $currency_symbol,
            'invoice_number'     => $invoice_number,
            'session_code'       => $session['session_code'],
            'table_number'       => $session['table']['table_number'] ?? 'N/A',
            'payment_method'     => $payment_method,
            'opened_at'          => $session['opened_at'],
            'closed_at'          => current_time( 'mysql' ),
            'subtotal'           => $session['subtotal'],
            'tax_amount'         => $session['tax_amount'],
            'discount_amount'    => $session['discount_amount'],
            'total_amount'       => $session['total_amount'],
            'orders'             => array(),
        );

        if ( ! empty( $session['orders'] ) ) {
            foreach ( $session['orders'] as $ord ) {
                $order_entry = array(
                    'order_number' => $ord['order_number'],
                    'items'        => array(),
                );
                foreach ( $ord['items'] as $it ) {
                    $order_entry['items'][] = array(
                        'name'        => $it['item_name'],
                        'quantity'    => $it['quantity'],
                        'unit_price'  => $it['unit_price'],
                        'total_price' => $it['total_price'],
                    );
                }
                $payload['orders'][] = $order_entry;
            }
        }

        RO_DB::insert( 'print_jobs', array(
            'session_id'   => $session_id,
            'printer_type' => 'counter',
            'status'       => 'QUEUED',
            'payload'      => wp_json_encode( $payload ),
            'retry_count'  => 0,
            'created_at'   => current_time( 'mysql' ),
        ) );
    }

    /**
     * Get Pending Jobs for the Local Print Bridge
     */
    public static function get_pending_jobs( $printer_type = null ) {
        if ( $printer_type ) {
            $jobs = RO_DB::get_results(
                "SELECT * FROM " . RO_DB::table( 'print_jobs' ) . " WHERE status IN ('QUEUED', 'RETRYING') AND printer_type = %s ORDER BY id ASC LIMIT 20",
                sanitize_text_field( $printer_type )
            );
        } else {
            $jobs = RO_DB::get_results(
                "SELECT * FROM " . RO_DB::table( 'print_jobs' ) . " WHERE status IN ('QUEUED', 'RETRYING') ORDER BY id ASC LIMIT 20"
            );
        }

        if ( ! is_array( $jobs ) ) {
            return array();
        }

        foreach ( $jobs as &$j ) {
            $j['payload'] = json_decode( $j['payload'], true );
        }
        return $jobs;
    }

    /**
     * Bridge Status Acknowledgement
     */
    public static function acknowledge_job( $job_id, $status, $error_message = '' ) {
        $allowed = array( 'PRINTED', 'FAILED', 'RETRYING', 'CLAIMED' );
        if ( ! in_array( $status, $allowed, true ) ) {
            return false;
        }

        $job = RO_DB::get_row( "SELECT * FROM " . RO_DB::table( 'print_jobs' ) . " WHERE id = %d", $job_id );
        if ( ! $job ) {
            return false;
        }

        $retry_count = intval( $job['retry_count'] );
        if ( 'FAILED' === $status ) {
            $retry_count++;
            if ( $retry_count < 3 ) {
                $status = 'RETRYING';
            }
        }

        return RO_DB::update( 'print_jobs', array(
            'status'      => $status,
            'retry_count' => $retry_count,
            'last_error'  => sanitize_text_field( $error_message ),
        ), array( 'id' => intval( $job_id ) ) );
    }

    /**
     * Manual Reprint Trigger
     */
    public static function reprint_job( $job_id ) {
        $job = RO_DB::get_row( "SELECT * FROM " . RO_DB::table( 'print_jobs' ) . " WHERE id = %d", $job_id );
        if ( ! $job ) {
            return false;
        }

        RO_DB::update( 'print_jobs', array(
            'status'     => 'QUEUED',
            'last_error' => null,
        ), array( 'id' => intval( $job_id ) ) );

        RO_DB::log_event( $job['order_id'] ?? 0, $job['session_id'] ?? 0, 'REPRINT_REQUESTED', "Print job #{$job_id} re-queued for reprint." );

        return true;
    }
}
