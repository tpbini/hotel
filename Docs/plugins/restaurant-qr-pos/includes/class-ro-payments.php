<?php
/**
 * Billing, Payments & Table Settlement.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RO_Payments {

    public static function process_payment( $session_id, $payment_method = 'Cash', $amount = null, $reference = '', $notes = '' ) {
        $session = RO_Sessions::get_by_id( $session_id );
        if ( ! $session ) {
            return new WP_Error( 'invalid_session', __( 'Session not found.', 'restaurant-qr-pos' ) );
        }

        $payable_amount = ( null !== $amount ) ? floatval( $amount ) : (float) $session['total_amount'];

        // Generate invoice number INV-YEAR-RANDOM
        $invoice_number = 'INV-' . date( 'Y' ) . '-' . wp_rand( 100000, 999999 );

        $payment_id = RO_DB::insert( 'payments', array(
            'session_id'     => intval( $session_id ),
            'invoice_number' => $invoice_number,
            'payment_method' => sanitize_text_field( $payment_method ),
            'amount'         => $payable_amount,
            'reference'      => sanitize_text_field( $reference ),
            'notes'          => sanitize_textarea_field( $notes ),
            'created_at'     => current_time( 'mysql' ),
        ) );

        if ( ! $payment_id ) {
            return new WP_Error( 'db_error', __( 'Could not record payment.', 'restaurant-qr-pos' ) );
        }

        // Queue Bill / Receipt Print Job
        RO_Printing::queue_counter_receipt_job( $session_id, $invoice_number, $payment_method );

        // Close Session and Release Table
        RO_Sessions::close_session( $session_id );

        // Log payment audit event
        RO_DB::log_event( 0, $session_id, 'PAYMENT_RECORDED', "Payment of {$payable_amount} recorded via {$payment_method} (Invoice: {$invoice_number})" );

        return array(
            'payment_id'     => $payment_id,
            'invoice_number' => $invoice_number,
            'amount'         => $payable_amount,
            'status'         => 'settled',
        );
    }

    public static function apply_discount( $session_id, $discount_amount ) {
        $discount = max( 0, floatval( $discount_amount ) );
        RO_DB::update( 'table_sessions', array(
            'discount_amount' => $discount,
        ), array( 'id' => intval( $session_id ) ) );

        RO_Sessions::recalculate_session( $session_id );
        RO_DB::log_event( 0, $session_id, 'DISCOUNT_APPLIED', "Discount of {$discount} applied to Session #{$session_id}" );

        return RO_Sessions::get_by_id( $session_id );
    }

    public static function get_payments_by_session( $session_id ) {
        return RO_DB::get_results( "SELECT * FROM " . RO_DB::table( 'payments' ) . " WHERE session_id = %d ORDER BY id DESC", $session_id );
    }
}
