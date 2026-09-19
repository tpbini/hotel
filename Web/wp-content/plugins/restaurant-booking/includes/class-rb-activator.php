<?php
/**
 * Plugin Activation & Schema Installer.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RB_Activator {

    public static function activate() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        // 1. Reservations Table
        $table_reservations = $wpdb->prefix . 'rb_reservations';
        $sql_reservations = "CREATE TABLE $table_reservations (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            booking_reference varchar(32) NOT NULL,
            secure_token varchar(64) NOT NULL,
            token_hash varchar(64) NOT NULL,
            customer_name varchar(150) NOT NULL,
            phone varchar(50) NOT NULL,
            email varchar(150) NOT NULL,
            party_size int(11) NOT NULL DEFAULT 2,
            booking_date date NOT NULL,
            start_time time NOT NULL,
            end_time time NOT NULL,
            status varchar(30) NOT NULL DEFAULT 'confirmed',
            confirmation_mode varchar(20) NOT NULL DEFAULT 'auto',
            special_requests text NULL,
            dietary_notes text NULL,
            marketing_consent tinyint(1) NOT NULL DEFAULT 0,
            source varchar(30) NOT NULL DEFAULT 'web',
            table_id bigint(20) unsigned NULL,
            table_number_display varchar(50) NULL,
            linked_session_id bigint(20) unsigned NULL,
            deposit_required tinyint(1) NOT NULL DEFAULT 0,
            deposit_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            deposit_status varchar(30) NOT NULL DEFAULT 'none',
            created_at datetime NOT NULL,
            updated_at datetime NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY booking_reference (booking_reference),
            KEY secure_token (secure_token),
            KEY token_hash (token_hash),
            KEY booking_date (booking_date),
            KEY status (status),
            KEY table_id (table_id),
            KEY email (email)
        ) $charset_collate;";
        dbDelta( $sql_reservations );

        // 2. Reservation Table Allocations (supports multi-table combinability)
        $table_res_tables = $wpdb->prefix . 'rb_reservation_tables';
        $sql_res_tables = "CREATE TABLE $table_res_tables (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            reservation_id bigint(20) unsigned NOT NULL,
            table_id bigint(20) unsigned NOT NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY reservation_id (reservation_id),
            KEY table_id (table_id)
        ) $charset_collate;";
        dbDelta( $sql_res_tables );

        // 3. Opening Hours & Split Service Periods
        $table_opening = $wpdb->prefix . 'rb_opening_hours';
        $sql_opening = "CREATE TABLE $table_opening (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            day_of_week tinyint(1) NOT NULL COMMENT '1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri, 6=Sat, 7=Sun',
            service_name varchar(50) NOT NULL DEFAULT 'Dinner',
            open_time time NOT NULL,
            close_time time NOT NULL,
            slot_interval int(11) NOT NULL DEFAULT 30,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            PRIMARY KEY  (id),
            KEY day_of_week (day_of_week)
        ) $charset_collate;";
        dbDelta( $sql_opening );

        // 4. Special Dates & Holiday Exceptions
        $table_special = $wpdb->prefix . 'rb_special_dates';
        $sql_special = "CREATE TABLE $table_special (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            special_date date NOT NULL,
            name varchar(100) NOT NULL,
            action_type varchar(30) NOT NULL DEFAULT 'closed' COMMENT 'closed, custom_hours, blocked_time',
            open_time time NULL,
            close_time time NULL,
            notes text NULL,
            PRIMARY KEY  (id),
            KEY special_date (special_date)
        ) $charset_collate;";
        dbDelta( $sql_special );

        // 5. Standalone Physical Tables Fallback (when QR plugin is not active)
        $table_tables = $wpdb->prefix . 'rb_tables';
        $sql_tables = "CREATE TABLE $table_tables (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            table_number varchar(30) NOT NULL,
            capacity int(11) NOT NULL DEFAULT 4,
            min_capacity int(11) NOT NULL DEFAULT 1,
            status varchar(30) NOT NULL DEFAULT 'available',
            notes varchar(255) NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY table_number (table_number)
        ) $charset_collate;";
        dbDelta( $sql_tables );

        // 6. Table Combinations
        $table_combos = $wpdb->prefix . 'rb_table_combinations';
        $sql_combos = "CREATE TABLE $table_combos (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            primary_table_id bigint(20) unsigned NOT NULL,
            combined_table_id bigint(20) unsigned NOT NULL,
            combined_capacity int(11) NOT NULL DEFAULT 8,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            PRIMARY KEY  (id),
            KEY primary_table_id (primary_table_id),
            KEY combined_table_id (combined_table_id)
        ) $charset_collate;";
        dbDelta( $sql_combos );

        // 7. Notifications Log
        $table_notifs = $wpdb->prefix . 'rb_notifications';
        $sql_notifs = "CREATE TABLE $table_notifs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            reservation_id bigint(20) unsigned NOT NULL,
            type varchar(50) NOT NULL,
            recipient varchar(150) NOT NULL,
            subject varchar(255) NOT NULL,
            status varchar(30) NOT NULL DEFAULT 'sent',
            sent_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY reservation_id (reservation_id)
        ) $charset_collate;";
        dbDelta( $sql_notifs );

        // 8. Waitlist
        $table_waitlist = $wpdb->prefix . 'rb_waitlist';
        $sql_waitlist = "CREATE TABLE $table_waitlist (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            customer_name varchar(150) NOT NULL,
            phone varchar(50) NOT NULL,
            email varchar(150) NOT NULL,
            party_size int(11) NOT NULL DEFAULT 2,
            requested_date date NOT NULL,
            preferred_time time NOT NULL,
            status varchar(30) NOT NULL DEFAULT 'waiting' COMMENT 'waiting, offered, booked, expired, cancelled',
            notes text NULL,
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY requested_date (requested_date),
            KEY status (status)
        ) $charset_collate;";
        dbDelta( $sql_waitlist );

        // 9. Events & Audit Log
        $table_events = $wpdb->prefix . 'rb_events';
        $sql_events = "CREATE TABLE $table_events (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            reservation_id bigint(20) unsigned NOT NULL,
            event_type varchar(50) NOT NULL,
            description text NOT NULL,
            performed_by varchar(100) NOT NULL DEFAULT 'system',
            created_at datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY reservation_id (reservation_id),
            KEY event_type (event_type)
        ) $charset_collate;";
        dbDelta( $sql_events );

        // Seed Default Configuration and Opening Hours if not exists
        self::seed_default_data();

        update_option( 'rb_db_version', RB_DB_VERSION );
    }

    private static function seed_default_data() {
        global $wpdb;

        // Default General Options
        add_option( 'rb_restaurant_name', 'The Crown & Thistle Bistro' );
        add_option( 'rb_restaurant_phone', '020 7946 0912' );
        add_option( 'rb_restaurant_email', get_option( 'admin_email' ) );
        add_option( 'rb_restaurant_address', '45 High Street, London, EC1A 1BB' );
        add_option( 'rb_default_duration', 120 ); // 2 hours
        add_option( 'rb_turnaround_buffer', 15 ); // 15 mins
        add_option( 'rb_booking_interval', 30 ); // 30 mins
        add_option( 'rb_min_party_size', 1 );
        add_option( 'rb_max_party_size', 12 );
        add_option( 'rb_auto_confirm', 1 ); // 1 = Auto confirm, 0 = Manual approval
        add_option( 'rb_cancellation_cutoff_hours', 2 );
        add_option( 'rb_max_advance_days', 90 );
        add_option( 'rb_allergen_notice', 'Please note: If you or any member of your party suffer from a food allergy or dietary intolerance, please speak to a member of our team directly by phone before booking.' );
        add_option( 'rb_privacy_policy_text', 'We collect only the personal information required to manage your table reservation. Your details are never sold to third parties.' );

        // Seed Opening Hours (UK standard lunch 12:00-14:30, dinner 17:30-22:30; Sunday special)
        $opening_table = $wpdb->prefix . 'rb_opening_hours';
        $count = $wpdb->get_var( "SELECT COUNT(*) FROM $opening_table" );
        if ( empty( $count ) ) {
            $default_hours = array(
                // Monday (1) to Thursday (4)
                array( 'day' => 1, 'name' => 'Lunch', 'open' => '12:00:00', 'close' => '14:30:00' ),
                array( 'day' => 1, 'name' => 'Dinner', 'open' => '17:30:00', 'close' => '22:00:00' ),
                array( 'day' => 2, 'name' => 'Lunch', 'open' => '12:00:00', 'close' => '14:30:00' ),
                array( 'day' => 2, 'name' => 'Dinner', 'open' => '17:30:00', 'close' => '22:00:00' ),
                array( 'day' => 3, 'name' => 'Lunch', 'open' => '12:00:00', 'close' => '14:30:00' ),
                array( 'day' => 3, 'name' => 'Dinner', 'open' => '17:30:00', 'close' => '22:00:00' ),
                array( 'day' => 4, 'name' => 'Lunch', 'open' => '12:00:00', 'close' => '14:30:00' ),
                array( 'day' => 4, 'name' => 'Dinner', 'open' => '17:30:00', 'close' => '22:30:00' ),
                // Friday (5) & Saturday (6)
                array( 'day' => 5, 'name' => 'Lunch', 'open' => '12:00:00', 'close' => '15:00:00' ),
                array( 'day' => 5, 'name' => 'Dinner', 'open' => '17:00:00', 'close' => '23:00:00' ),
                array( 'day' => 6, 'name' => 'Lunch', 'open' => '12:00:00', 'close' => '15:30:00' ),
                array( 'day' => 6, 'name' => 'Dinner', 'open' => '17:00:00', 'close' => '23:00:00' ),
                // Sunday (7) All-Day Roast / Dinner
                array( 'day' => 7, 'name' => 'Sunday Lunch & Roast', 'open' => '12:00:00', 'close' => '16:30:00' ),
                array( 'day' => 7, 'name' => 'Sunday Dinner', 'open' => '17:30:00', 'close' => '21:30:00' ),
            );

            foreach ( $default_hours as $h ) {
                $wpdb->insert( $opening_table, array(
                    'day_of_week'   => $h['day'],
                    'service_name'  => $h['name'],
                    'open_time'     => $h['open'],
                    'close_time'    => $h['close'],
                    'slot_interval' => 30,
                    'is_active'     => 1,
                ) );
            }
        }

        // Seed Fallback Tables
        $tables_table = $wpdb->prefix . 'rb_tables';
        $t_count = $wpdb->get_var( "SELECT COUNT(*) FROM $tables_table" );
        if ( empty( $t_count ) ) {
            $default_tables = array(
                array( 'table_number' => 'T01', 'capacity' => 2, 'min_capacity' => 1, 'notes' => 'Window 2-Top' ),
                array( 'table_number' => 'T02', 'capacity' => 2, 'min_capacity' => 1, 'notes' => 'Cozy Booth 2-Top' ),
                array( 'table_number' => 'T03', 'capacity' => 4, 'min_capacity' => 2, 'notes' => 'Center Dining 4-Top' ),
                array( 'table_number' => 'T04', 'capacity' => 4, 'min_capacity' => 2, 'notes' => 'Center Dining 4-Top' ),
                array( 'table_number' => 'T05', 'capacity' => 6, 'min_capacity' => 4, 'notes' => 'Spacious Booth 6-Top' ),
                array( 'table_number' => 'T06', 'capacity' => 8, 'min_capacity' => 6, 'notes' => 'Large Dining Table 8-Top' ),
            );
            foreach ( $default_tables as $tb ) {
                $wpdb->insert( $tables_table, $tb );
            }

            // Seed sample combinable tables (T03 + T04 = 8 covers)
            $combo_table = $wpdb->prefix . 'rb_table_combinations';
            $t3_id = $wpdb->get_var( "SELECT id FROM $tables_table WHERE table_number = 'T03'" );
            $t4_id = $wpdb->get_var( "SELECT id FROM $tables_table WHERE table_number = 'T04'" );
            if ( $t3_id && $t4_id ) {
                $wpdb->insert( $combo_table, array(
                    'name'              => 'T03 + T04 Combined',
                    'primary_table_id'  => $t3_id,
                    'combined_table_id' => $t4_id,
                    'combined_capacity' => 8,
                    'is_active'         => 1,
                ) );
            }
        }
    }
}
