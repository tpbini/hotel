<?php
/**
 * UK Localization, Timezone & Formatting Utilities.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RB_i18n {

    const TIMEZONE = 'Europe/London';

    public static function get_timezone() {
        return new DateTimeZone( self::TIMEZONE );
    }

    /**
     * Get current DateTime in Europe/London timezone.
     */
    public static function get_now() {
        return new DateTime( 'now', self::get_timezone() );
    }

    public static function now_uk() {
        return self::get_now()->format( 'Y-m-d H:i:s' );
    }

    public static function today_uk() {
        return self::get_now()->format( 'Y-m-d' );
    }

    /**
     * Format standard MySQL date (Y-m-d) into UK readable format.
     */
    public static function format_date_uk( $date_str, $format = 'd/m/Y' ) {
        if ( empty( $date_str ) ) {
            return '';
        }
        try {
            $dt = new DateTime( $date_str, self::get_timezone() );
            return $dt->format( $format );
        } catch ( Exception $e ) {
            return $date_str;
        }
    }

    /**
     * Format standard MySQL time (H:i:s) into 12-hour or 24-hour UK time.
     */
    public static function format_time_uk( $time_str, $include_period = true ) {
        if ( empty( $time_str ) ) {
            return '';
        }
        try {
            $dt = new DateTime( '2000-01-01 ' . $time_str, self::get_timezone() );
            return $dt->format( $include_period ? 'g:i A' : 'H:i' );
        } catch ( Exception $e ) {
            return substr( $time_str, 0, 5 );
        }
    }

    /**
     * Format date & time into full UK human friendly string (e.g., "Friday, 28 August 2026 at 7:30 PM")
     */
    public static function format_datetime_uk( $date_str, $time_str ) {
        if ( empty( $date_str ) ) {
            return '';
        }
        try {
            $dt = new DateTime( $date_str . ' ' . $time_str, self::get_timezone() );
            return $dt->format( 'l, j F Y' ) . ' at ' . $dt->format( 'g:i A' );
        } catch ( Exception $e ) {
            return $date_str . ' ' . $time_str;
        }
    }

    /**
     * Normalize UK Phone Number
     * Accepts: '07123 456789', '+44 7123 456789', '07123456789', '020 7946 0912', etc.
     */
    public static function normalize_phone_uk( $phone ) {
        $clean = preg_replace( '/[^0-9+]/', '', trim( $phone ) );
        
        // Convert +447... to 07...
        if ( strpos( $clean, '+44' ) === 0 ) {
            $clean = '0' . substr( $clean, 3 );
        } elseif ( strpos( $clean, '44' ) === 0 && strlen( $clean ) === 12 ) {
            $clean = '0' . substr( $clean, 2 );
        }

        // Format standard 11-digit UK mobile (07xxx xxxxxx)
        if ( strlen( $clean ) === 11 && strpos( $clean, '07' ) === 0 ) {
            return substr( $clean, 0, 5 ) . ' ' . substr( $clean, 5 );
        }

        // Format standard 11-digit UK landline (020 xxxx xxxx / 01xxx xxxxxx)
        if ( strlen( $clean ) === 11 && strpos( $clean, '0' ) === 0 ) {
            if ( strpos( $clean, '02' ) === 0 ) {
                return substr( $clean, 0, 3 ) . ' ' . substr( $clean, 3, 4 ) . ' ' . substr( $clean, 7 );
            }
            return substr( $clean, 0, 4 ) . ' ' . substr( $clean, 4, 3 ) . ' ' . substr( $clean, 7 );
        }

        return ! empty( $clean ) ? $clean : $phone;
    }

    /**
     * Validate UK Phone Number
     */
    public static function is_valid_uk_phone( $phone ) {
        $clean = preg_replace( '/[^0-9]/', '', $phone );
        // UK numbers are typically 10 or 11 digits (or 12-13 if including +44)
        return ( strlen( $clean ) >= 10 && strlen( $clean ) <= 13 );
    }

    /**
     * Format currency in GBP (£)
     */
    public static function format_currency_gbp( $amount ) {
        return '£' . number_format( (float) $amount, 2, '.', ',' );
    }
}
