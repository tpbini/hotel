<?php
/**
 * Fired during plugin activation.
 * Creates dedicated custom database tables and seeds initial data.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RO_Activator {

    public static function activate() {
        try {
            self::create_tables();
            self::seed_initial_data();
            self::register_roles();

            if ( function_exists( 'flush_rewrite_rules' ) ) {
                flush_rewrite_rules( false );
            }

            update_option( 'ro_db_version', RO_DB_VERSION );
        } catch ( \Exception $e ) {
            error_log( 'RO_Activator Exception: ' . $e->getMessage() );
        } catch ( \Error $e ) {
            error_log( 'RO_Activator Error: ' . $e->getMessage() );
        }
    }

    public static function create_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        if ( ! function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        // 1. Tables Table
        $sql_tables = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ro_tables (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            table_number varchar(50) NOT NULL,
            qr_token varchar(64) NOT NULL,
            capacity int(11) NOT NULL DEFAULT 4,
            status varchar(20) NOT NULL DEFAULT 'available',
            notes text NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY qr_token (qr_token),
            KEY status (status)
        ) $charset_collate;";
        dbDelta( $sql_tables );

        // 2. Table Sessions Table
        $sql_sessions = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ro_table_sessions (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            table_id bigint(20) unsigned NOT NULL,
            session_code varchar(64) NOT NULL,
            status varchar(20) NOT NULL DEFAULT 'active',
            subtotal decimal(10,2) NOT NULL DEFAULT 0.00,
            tax_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            discount_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            total_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            opened_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            closed_at datetime NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY session_code (session_code),
            KEY table_id (table_id),
            KEY status (status)
        ) $charset_collate;";
        dbDelta( $sql_sessions );

        // 3. Orders Table
        $sql_orders = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ro_orders (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            session_id bigint(20) unsigned NOT NULL,
            table_id bigint(20) unsigned NOT NULL,
            order_number varchar(64) NOT NULL,
            idempotency_key varchar(128) NULL,
            source varchar(20) NOT NULL DEFAULT 'QR',
            status varchar(20) NOT NULL DEFAULT 'NEW',
            cancellation_reason text NULL,
            subtotal decimal(10,2) NOT NULL DEFAULT 0.00,
            tax_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            discount_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            total_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            notes text NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY order_number (order_number),
            KEY session_id (session_id),
            KEY table_id (table_id),
            KEY status (status),
            KEY idempotency_key (idempotency_key)
        ) $charset_collate;";
        dbDelta( $sql_orders );

        // 4. Order Items Table
        $sql_order_items = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ro_order_items (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            order_id bigint(20) unsigned NOT NULL,
            menu_item_id bigint(20) unsigned NOT NULL,
            item_name varchar(255) NOT NULL,
            quantity int(11) NOT NULL DEFAULT 1,
            unit_price decimal(10,2) NOT NULL DEFAULT 0.00,
            total_price decimal(10,2) NOT NULL DEFAULT 0.00,
            station_id bigint(20) unsigned NOT NULL DEFAULT 1,
            status varchar(20) NOT NULL DEFAULT 'NEW',
            notes text NULL,
            PRIMARY KEY  (id),
            KEY order_id (order_id),
            KEY station_id (station_id),
            KEY status (status)
        ) $charset_collate;";
        dbDelta( $sql_order_items );

        // 5. Order Item Modifiers Table
        $sql_item_mods = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ro_order_item_modifiers (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            order_item_id bigint(20) unsigned NOT NULL,
            modifier_name varchar(255) NOT NULL,
            price decimal(10,2) NOT NULL DEFAULT 0.00,
            PRIMARY KEY  (id),
            KEY order_item_id (order_item_id)
        ) $charset_collate;";
        dbDelta( $sql_item_mods );

        // 6. Categories Table
        $sql_categories = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ro_categories (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            slug varchar(100) NOT NULL,
            sort_order int(11) NOT NULL DEFAULT 0,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            PRIMARY KEY  (id),
            UNIQUE KEY slug (slug)
        ) $charset_collate;";
        dbDelta( $sql_categories );

        // 7. Stations Table (Kitchen Preparation Stations)
        $sql_stations = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ro_stations (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            name varchar(100) NOT NULL,
            code varchar(50) NOT NULL,
            is_active tinyint(1) NOT NULL DEFAULT 1,
            PRIMARY KEY  (id),
            UNIQUE KEY code (code)
        ) $charset_collate;";
        dbDelta( $sql_stations );

        // 8. Menu Items Table
        $sql_menu_items = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ro_menu_items (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            category_id bigint(20) unsigned NOT NULL,
            station_id bigint(20) unsigned NOT NULL DEFAULT 1,
            name varchar(255) NOT NULL,
            description text NULL,
            image_url text NULL,
            base_price decimal(10,2) NOT NULL DEFAULT 0.00,
            tax_rate decimal(5,2) NOT NULL DEFAULT 5.00,
            food_type varchar(20) NOT NULL DEFAULT 'veg',
            status varchar(20) NOT NULL DEFAULT 'available',
            is_popular tinyint(1) NOT NULL DEFAULT 0,
            sort_order int(11) NOT NULL DEFAULT 0,
            prep_time int(11) NOT NULL DEFAULT 15,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY category_id (category_id),
            KEY station_id (station_id),
            KEY status (status)
        ) $charset_collate;";
        dbDelta( $sql_menu_items );

        // 9. Item Variations Table (e.g. Half / Full)
        $sql_variations = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ro_item_variations (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            menu_item_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            price decimal(10,2) NOT NULL DEFAULT 0.00,
            PRIMARY KEY  (id),
            KEY menu_item_id (menu_item_id)
        ) $charset_collate;";
        dbDelta( $sql_variations );

        // 10. Item Add-ons / Modifiers Table
        $sql_addons = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ro_addons (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            menu_item_id bigint(20) unsigned NOT NULL,
            name varchar(100) NOT NULL,
            price decimal(10,2) NOT NULL DEFAULT 0.00,
            type varchar(50) NOT NULL DEFAULT 'extra',
            PRIMARY KEY  (id),
            KEY menu_item_id (menu_item_id)
        ) $charset_collate;";
        dbDelta( $sql_addons );

        // 11. Service Requests Table (Call Waiter / Request Bill)
        $sql_service = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ro_service_requests (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            table_id bigint(20) unsigned NOT NULL,
            session_id bigint(20) unsigned NULL,
            type varchar(30) NOT NULL DEFAULT 'call_waiter',
            status varchar(20) NOT NULL DEFAULT 'pending',
            notes text NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY table_id (table_id),
            KEY status (status)
        ) $charset_collate;";
        dbDelta( $sql_service );

        // 12. Payments Table
        $sql_payments = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ro_payments (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            session_id bigint(20) unsigned NOT NULL,
            invoice_number varchar(64) NOT NULL,
            payment_method varchar(30) NOT NULL DEFAULT 'Cash',
            amount decimal(10,2) NOT NULL DEFAULT 0.00,
            reference varchar(128) NULL,
            notes text NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            UNIQUE KEY invoice_number (invoice_number),
            KEY session_id (session_id)
        ) $charset_collate;";
        dbDelta( $sql_payments );

        // 13. Print Jobs Table
        $sql_print_jobs = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ro_print_jobs (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            order_id bigint(20) unsigned NULL,
            session_id bigint(20) unsigned NULL,
            station_id bigint(20) unsigned NULL,
            printer_type varchar(30) NOT NULL DEFAULT 'kitchen',
            status varchar(20) NOT NULL DEFAULT 'QUEUED',
            payload longtext NOT NULL,
            retry_count int(11) NOT NULL DEFAULT 0,
            last_error text NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY status (status),
            KEY order_id (order_id),
            KEY station_id (station_id)
        ) $charset_collate;";
        dbDelta( $sql_print_jobs );

        // 14. Order Events / Audit Table
        $sql_order_events = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}ro_order_events (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            order_id bigint(20) unsigned NOT NULL,
            session_id bigint(20) unsigned NULL,
            event_type varchar(50) NOT NULL,
            actor_id bigint(20) unsigned NULL,
            actor_role varchar(50) NULL,
            details text NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY order_id (order_id)
        ) $charset_collate;";
        dbDelta( $sql_order_events );
    }

    public static function seed_initial_data() {
        global $wpdb;

        // Default Options
        add_option( 'ro_currency_symbol', '$' );
        add_option( 'ro_currency_code', 'USD' );
        add_option( 'ro_tax_rate', '5' );
        add_option( 'ro_service_charge', '0' );
        add_option( 'ro_restaurant_name', 'Grand Bistro & Grill' );
        add_option( 'ro_restaurant_phone', '+1 (555) 019-2834' );
        add_option( 'ro_restaurant_address', '124 Gourmet Boulevard, Food City' );
        add_option( 'ro_print_bridge_token', bin2hex( random_bytes( 16 ) ) );

        // Seed Stations if empty
        $station_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}ro_stations" );
        if ( 0 == $station_count ) {
            $stations = array(
                array( 'name' => 'Main Kitchen', 'code' => 'main_kitchen' ),
                array( 'name' => 'Grill Station', 'code' => 'grill' ),
                array( 'name' => 'Beverage & Bar', 'code' => 'beverage' ),
                array( 'name' => 'Dessert & Bakery', 'code' => 'dessert' ),
            );
            foreach ( $stations as $st ) {
                $wpdb->insert( "{$wpdb->prefix}ro_stations", $st );
            }
        }

        // Seed Categories if empty
        $cat_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}ro_categories" );
        if ( 0 == $cat_count ) {
            $categories = array(
                array( 'name' => 'Starters & Appetizers', 'slug' => 'starters', 'sort_order' => 1 ),
                array( 'name' => 'Chef Specials & Grills', 'slug' => 'grills', 'sort_order' => 2 ),
                array( 'name' => 'Signature Mains & Biriyani', 'slug' => 'mains', 'sort_order' => 3 ),
                array( 'name' => 'Artisan Beverages', 'slug' => 'beverages', 'sort_order' => 4 ),
                array( 'name' => 'Handcrafted Desserts', 'slug' => 'desserts', 'sort_order' => 5 ),
            );
            foreach ( $categories as $cat ) {
                $wpdb->insert( "{$wpdb->prefix}ro_categories", $cat );
            }
        }

        // Seed Menu Items if empty
        $item_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}ro_menu_items" );
        if ( 0 == $item_count ) {
            $menu_items = array(
                array(
                    'category_id' => 1,
                    'station_id'  => 1,
                    'name'        => 'Crispy Truffle Fries',
                    'description' => 'Hand-cut golden fries tossed with black truffle oil, rosemary, and aged parmesan with garlic aioli dip.',
                    'image_url'   => 'https://images.unsplash.com/photo-1573080496219-bb080dd4f877?auto=format&fit=crop&w=600&q=80',
                    'base_price'  => 8.50,
                    'food_type'   => 'veg',
                    'is_popular'  => 1,
                    'prep_time'   => 10,
                ),
                array(
                    'category_id' => 1,
                    'station_id'  => 1,
                    'name'        => 'Smoked Chicken Wings',
                    'description' => 'Hickory smoked wings glazed in spicy habanero honey or smoky barbecue sauce.',
                    'image_url'   => 'https://images.unsplash.com/photo-1567620832903-9fc6debc209f?auto=format&fit=crop&w=600&q=80',
                    'base_price'  => 12.00,
                    'food_type'   => 'non_veg',
                    'is_popular'  => 1,
                    'prep_time'   => 15,
                ),
                array(
                    'category_id' => 2,
                    'station_id'  => 2,
                    'name'        => 'Flame-Grilled Ribeye Steak',
                    'description' => '300g Prime Angus beef ribeye, charred herb butter, roasted asparagus, and pepper sauce.',
                    'image_url'   => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80',
                    'base_price'  => 28.00,
                    'food_type'   => 'non_veg',
                    'is_popular'  => 1,
                    'prep_time'   => 20,
                ),
                array(
                    'category_id' => 2,
                    'station_id'  => 2,
                    'name'        => 'Charred Tandoori Chicken Tikka',
                    'description' => 'Succulent boneless chicken marinated in hung curd, Kashmiri chili, and aromatic spices cooked over live coals.',
                    'image_url'   => 'https://images.unsplash.com/photo-1599488615731-7e5c2823ff28?auto=format&fit=crop&w=600&q=80',
                    'base_price'  => 16.50,
                    'food_type'   => 'non_veg',
                    'is_popular'  => 1,
                    'prep_time'   => 18,
                ),
                array(
                    'category_id' => 3,
                    'station_id'  => 1,
                    'name'        => 'Royal Dum Biriyani',
                    'description' => 'Fragrant long-grain aged basmati rice layered with slow-cooked marinated chicken, saffron, mint, and fried onions.',
                    'image_url'   => 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=600&q=80',
                    'base_price'  => 17.50,
                    'food_type'   => 'non_veg',
                    'is_popular'  => 1,
                    'prep_time'   => 15,
                ),
                array(
                    'category_id' => 3,
                    'station_id'  => 1,
                    'name'        => 'Creamy Paneer Butter Masala',
                    'description' => 'Cottage cheese cubes simmered in a velvety tomato gravy finished with butter, cream, and dried fenugreek.',
                    'image_url'   => 'https://images.unsplash.com/photo-1631452180519-c014fe946bc7?auto=format&fit=crop&w=600&q=80',
                    'base_price'  => 14.00,
                    'food_type'   => 'veg',
                    'is_popular'  => 0,
                    'prep_time'   => 12,
                ),
                array(
                    'category_id' => 4,
                    'station_id'  => 3,
                    'name'        => 'Fresh Mint & Lime Soda',
                    'description' => 'Sparkling water infused with crushed garden mint, freshly squeezed lime, and organic cane syrup.',
                    'image_url'   => 'https://images.unsplash.com/photo-1513558161293-cdaf765ed2fd?auto=format&fit=crop&w=600&q=80',
                    'base_price'  => 4.50,
                    'food_type'   => 'beverage',
                    'is_popular'  => 1,
                    'prep_time'   => 5,
                ),
                array(
                    'category_id' => 4,
                    'station_id'  => 3,
                    'name'        => 'Iced Salted Caramel Cold Brew',
                    'description' => '16-hour slow-steeped Arabica coffee topped with artisanal sea salt caramel cold foam.',
                    'image_url'   => 'https://images.unsplash.com/photo-1517701550927-30cf4ba1dba5?auto=format&fit=crop&w=600&q=80',
                    'base_price'  => 5.50,
                    'food_type'   => 'beverage',
                    'is_popular'  => 0,
                    'prep_time'   => 5,
                ),
                array(
                    'category_id' => 5,
                    'station_id'  => 4,
                    'name'        => 'Molten Dark Chocolate Lava Cake',
                    'description' => 'Warm Belgian dark chocolate cake with a rich flowing center, served with Madagascar vanilla bean gelato.',
                    'image_url'   => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?auto=format&fit=crop&w=600&q=80',
                    'base_price'  => 7.50,
                    'food_type'   => 'veg',
                    'is_popular'  => 1,
                    'prep_time'   => 12,
                ),
            );

            foreach ( $menu_items as $item ) {
                $wpdb->insert( "{$wpdb->prefix}ro_menu_items", $item );
                $item_id = $wpdb->insert_id;

                // Add Variations for Biriyani & Steak
                if ( 'Royal Dum Biriyani' === $item['name'] ) {
                    $wpdb->insert( "{$wpdb->prefix}ro_item_variations", array( 'menu_item_id' => $item_id, 'name' => 'Regular Portion', 'price' => 17.50 ) );
                    $wpdb->insert( "{$wpdb->prefix}ro_item_variations", array( 'menu_item_id' => $item_id, 'name' => 'Family Jumbo Portion', 'price' => 32.00 ) );

                    $wpdb->insert( "{$wpdb->prefix}ro_addons", array( 'menu_item_id' => $item_id, 'name' => 'Extra Raita & Salan', 'price' => 1.50, 'type' => 'extra' ) );
                    $wpdb->insert( "{$wpdb->prefix}ro_addons", array( 'menu_item_id' => $item_id, 'name' => 'Extra Boiled Egg', 'price' => 1.00, 'type' => 'extra' ) );
                    $wpdb->insert( "{$wpdb->prefix}ro_addons", array( 'menu_item_id' => $item_id, 'name' => 'Spice Level: Extra Spicy', 'price' => 0.00, 'type' => 'spice' ) );
                }

                if ( 'Flame-Grilled Ribeye Steak' === $item['name'] ) {
                    $wpdb->insert( "{$wpdb->prefix}ro_item_variations", array( 'menu_item_id' => $item_id, 'name' => '250g Medium Rare', 'price' => 28.00 ) );
                    $wpdb->insert( "{$wpdb->prefix}ro_item_variations", array( 'menu_item_id' => $item_id, 'name' => '400g Large Cut', 'price' => 39.00 ) );

                    $wpdb->insert( "{$wpdb->prefix}ro_addons", array( 'menu_item_id' => $item_id, 'name' => 'Garlic Butter Jumbo Prawns (2pcs)', 'price' => 6.50, 'type' => 'extra' ) );
                    $wpdb->insert( "{$wpdb->prefix}ro_addons", array( 'menu_item_id' => $item_id, 'name' => 'Truffle Mushroom Sauce', 'price' => 2.50, 'type' => 'extra' ) );
                }

                if ( 'Crispy Truffle Fries' === $item['name'] ) {
                    $wpdb->insert( "{$wpdb->prefix}ro_addons", array( 'menu_item_id' => $item_id, 'name' => 'Extra Melted Cheddar', 'price' => 2.00, 'type' => 'extra' ) );
                    $wpdb->insert( "{$wpdb->prefix}ro_addons", array( 'menu_item_id' => $item_id, 'name' => 'Jalapeno Bacon Bits', 'price' => 2.50, 'type' => 'extra' ) );
                }
            }
        }

        // Seed Tables if empty
        $table_count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}ro_tables" );
        if ( 0 == $table_count ) {
            $default_tables = array(
                array( 'number' => 'Table 01', 'capacity' => 2, 'token' => 'T01-' . substr( bin2hex( random_bytes( 6 ) ), 0, 8 ) ),
                array( 'number' => 'Table 02', 'capacity' => 4, 'token' => 'T02-' . substr( bin2hex( random_bytes( 6 ) ), 0, 8 ) ),
                array( 'number' => 'Table 03', 'capacity' => 4, 'token' => 'T03-' . substr( bin2hex( random_bytes( 6 ) ), 0, 8 ) ),
                array( 'number' => 'Table 04', 'capacity' => 6, 'token' => 'T04-' . substr( bin2hex( random_bytes( 6 ) ), 0, 8 ) ),
                array( 'number' => 'Table 05', 'capacity' => 2, 'token' => 'T05-' . substr( bin2hex( random_bytes( 6 ) ), 0, 8 ) ),
                array( 'number' => 'VIP Booth A', 'capacity' => 8, 'token' => 'VIPA-' . substr( bin2hex( random_bytes( 6 ) ), 0, 8 ) ),
                array( 'number' => 'Patio Table 1', 'capacity' => 4, 'token' => 'PAT1-' . substr( bin2hex( random_bytes( 6 ) ), 0, 8 ) ),
                array( 'number' => 'Patio Table 2', 'capacity' => 4, 'token' => 'PAT2-' . substr( bin2hex( random_bytes( 6 ) ), 0, 8 ) ),
            );

            foreach ( $default_tables as $tb ) {
                $wpdb->insert( "{$wpdb->prefix}ro_tables", array(
                    'table_number' => $tb['number'],
                    'qr_token'     => $tb['token'],
                    'capacity'     => $tb['capacity'],
                    'status'       => 'available',
                ) );
            }
        }
    }

    public static function register_roles() {
        RO_Roles::init();
    }
}
