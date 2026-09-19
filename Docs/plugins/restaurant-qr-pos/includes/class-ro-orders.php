<?php
/**
 * Authoritative Order Creation, Calculation, Lifecycle & Routing.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RO_Orders {

    /**
     * Create a new order with server-side authoritative price calculation & idempotency.
     */
    public static function create_order( $table_id, $items_payload, $notes = '', $source = 'QR', $idempotency_key = null ) {
        // 1. Idempotency Check
        if ( ! empty( $idempotency_key ) ) {
            $existing = RO_DB::get_row( "SELECT * FROM " . RO_DB::table( 'orders' ) . " WHERE idempotency_key = %s", sanitize_text_field( $idempotency_key ) );
            if ( $existing ) {
                return self::get_order_by_id( $existing['id'] );
            }
        }

        // 2. Validate Table
        $table = RO_Tables::get_by_id( $table_id );
        if ( ! $table || 'disabled' === $table['status'] ) {
            return new WP_Error( 'invalid_table', __( 'Invalid or disabled table.', 'restaurant-qr-pos' ) );
        }

        // 3. Get or Create Active Session
        $session = RO_Sessions::get_or_create_session( $table_id );
        if ( ! $session ) {
            return new WP_Error( 'session_error', __( 'Could not establish table session.', 'restaurant-qr-pos' ) );
        }

        if ( empty( $items_payload ) || ! is_array( $items_payload ) ) {
            return new WP_Error( 'empty_cart', __( 'Cannot place an empty order.', 'restaurant-qr-pos' ) );
        }

        // 4. Calculate Authoritative Totals & Verify Items
        $calculated_items = array();
        $subtotal = 0.00;
        $total_tax = 0.00;
        $default_tax_rate = (float) get_option( 'ro_tax_rate', 5.0 );

        foreach ( $items_payload as $item_data ) {
            $item_id = intval( $item_data['menu_item_id'] ?? $item_data['id'] ?? 0 );
            $quantity = max( 1, intval( $item_data['quantity'] ?? 1 ) );

            $menu_item = RO_Menu::get_item_by_id( $item_id );
            if ( ! $menu_item ) {
                return new WP_Error( 'item_not_found', sprintf( __( 'Menu item #%d not found.', 'restaurant-qr-pos' ), $item_id ) );
            }

            if ( 'available' !== $menu_item['status'] ) {
                return new WP_Error( 'item_unavailable', sprintf( __( 'Item "%s" is currently sold out or unavailable.', 'restaurant-qr-pos' ), $menu_item['name'] ) );
            }

            // Determine Base Unit Price (Variation or Base Price)
            $unit_price = $menu_item['base_price'];
            $selected_variation_name = '';

            if ( ! empty( $item_data['variation_id'] ) ) {
                $var_id = intval( $item_data['variation_id'] );
                $variations = RO_Menu::get_item_variations( $item_id );
                foreach ( $variations as $v ) {
                    if ( intval( $v['id'] ) === $var_id ) {
                        $unit_price = (float) $v['price'];
                        $selected_variation_name = $v['name'];
                        break;
                    }
                }
            }

            // Add-ons / Modifiers Calculation
            $applied_modifiers = array();
            $addons_total = 0.00;
            if ( ! empty( $item_data['addons'] ) && is_array( $item_data['addons'] ) ) {
                $available_addons = RO_Menu::get_item_addons( $item_id );
                $addon_map = array();
                foreach ( $available_addons as $ad ) {
                    $addon_map[ $ad['id'] ] = $ad;
                }

                foreach ( $item_data['addons'] as $addon_ref ) {
                    $addon_id = is_array( $addon_ref ) ? intval( $addon_ref['id'] ) : intval( $addon_ref );
                    if ( isset( $addon_map[ $addon_id ] ) ) {
                        $ad_obj = $addon_map[ $addon_id ];
                        $ad_price = (float) $ad_obj['price'];
                        $addons_total += $ad_price;
                        $applied_modifiers[] = array(
                            'name'  => $ad_obj['name'],
                            'price' => $ad_price,
                        );
                    }
                }
            }

            $item_line_unit_price = $unit_price + $addons_total;
            $item_line_total = $item_line_unit_price * $quantity;
            $subtotal += $item_line_total;

            // Tax for this item
            $item_tax_rate = ! empty( $menu_item['tax_rate'] ) ? (float) $menu_item['tax_rate'] : $default_tax_rate;
            $item_tax = ( $item_line_total * $item_tax_rate ) / 100;
            $total_tax += $item_tax;

            $display_name = $menu_item['name'];
            if ( ! empty( $selected_variation_name ) ) {
                $display_name .= ' (' . $selected_variation_name . ')';
            }

            $calculated_items[] = array(
                'menu_item_id' => $item_id,
                'item_name'    => $display_name,
                'quantity'     => $quantity,
                'unit_price'   => $item_line_unit_price,
                'total_price'  => $item_line_total,
                'station_id'   => ! empty( $menu_item['station_id'] ) ? intval( $menu_item['station_id'] ) : 1,
                'status'       => 'NEW',
                'notes'        => sanitize_text_field( $item_data['notes'] ?? '' ),
                'modifiers'    => $applied_modifiers,
            );
        }

        $grand_total = $subtotal + $total_tax;

        // 5. Generate Order Number (#1001, #1002...)
        $last_order_id = (int) RO_DB::get_var( "SELECT MAX(id) FROM " . RO_DB::table( 'orders' ) );
        $order_number = '#' . ( 1000 + $last_order_id + 1 );

        // 6. Insert Order Record
        $order_id = RO_DB::insert( 'orders', array(
            'session_id'      => intval( $session['id'] ),
            'table_id'        => intval( $table_id ),
            'order_number'    => $order_number,
            'idempotency_key' => ! empty( $idempotency_key ) ? sanitize_text_field( $idempotency_key ) : null,
            'source'          => sanitize_text_field( $source ),
            'status'          => 'NEW',
            'subtotal'        => $subtotal,
            'tax_amount'      => $total_tax,
            'discount_amount' => 0.00,
            'total_amount'    => $grand_total,
            'notes'           => sanitize_textarea_field( $notes ),
            'created_at'      => current_time( 'mysql' ),
        ) );

        if ( ! $order_id ) {
            return new WP_Error( 'db_error', __( 'Failed to save order to database.', 'restaurant-qr-pos' ) );
        }

        // 7. Insert Order Items and Modifiers
        foreach ( $calculated_items as $c_item ) {
            $order_item_id = RO_DB::insert( 'order_items', array(
                'order_id'     => $order_id,
                'menu_item_id' => $c_item['menu_item_id'],
                'item_name'    => $c_item['item_name'],
                'quantity'     => $c_item['quantity'],
                'unit_price'   => $c_item['unit_price'],
                'total_price'  => $c_item['total_price'],
                'station_id'   => $c_item['station_id'],
                'status'       => 'NEW',
                'notes'        => $c_item['notes'],
            ) );

            if ( $order_item_id && ! empty( $c_item['modifiers'] ) ) {
                foreach ( $c_item['modifiers'] as $mod ) {
                    RO_DB::insert( 'order_item_modifiers', array(
                        'order_item_id' => $order_item_id,
                        'modifier_name' => $mod['name'],
                        'price'         => $mod['price'],
                    ) );
                }
            }
        }

        // 8. Recalculate Table Session Total
        RO_Sessions::recalculate_session( $session['id'] );

        // 9. Queue Kitchen Print Jobs for each Station
        RO_Printing::queue_kitchen_print_jobs( $order_id );

        // 10. Audit Event
        RO_DB::log_event( $order_id, $session['id'], 'ORDER_CREATED', "Order {$order_number} created via {$source} for Table {$table['table_number']} with total {$grand_total}" );

        return self::get_order_by_id( $order_id );
    }

    public static function get_order_by_id( $order_id ) {
        $order = RO_DB::get_row( "SELECT * FROM " . RO_DB::table( 'orders' ) . " WHERE id = %d", $order_id );
        if ( ! $order ) {
            return null;
        }

        $order['subtotal']        = (float) $order['subtotal'];
        $order['tax_amount']      = (float) $order['tax_amount'];
        $order['discount_amount'] = (float) $order['discount_amount'];
        $order['total_amount']    = (float) $order['total_amount'];

        $order['items'] = self::get_order_items( $order['id'] );
        $order['table'] = RO_DB::get_row( "SELECT * FROM " . RO_DB::table( 'tables' ) . " WHERE id = %d", $order['table_id'] );

        return $order;
    }

    public static function get_order_items( $order_id ) {
        $items = RO_DB::get_results( "SELECT oi.*, st.name as station_name FROM " . RO_DB::table( 'order_items' ) . " oi LEFT JOIN " . RO_DB::table( 'stations' ) . " st ON oi.station_id = st.id WHERE oi.order_id = %d", $order_id );

        foreach ( $items as &$item ) {
            $item['unit_price']  = (float) $item['unit_price'];
            $item['total_price'] = (float) $item['total_price'];
            $item['modifiers']   = RO_DB::get_results( "SELECT * FROM " . RO_DB::table( 'order_item_modifiers' ) . " WHERE order_item_id = %d", $item['id'] );
        }

        return $items;
    }

    public static function get_orders_by_session( $session_id ) {
        $orders = RO_DB::get_results( "SELECT * FROM " . RO_DB::table( 'orders' ) . " WHERE session_id = %d ORDER BY id ASC", $session_id );
        foreach ( $orders as &$order ) {
            $order['subtotal']        = (float) $order['subtotal'];
            $order['tax_amount']      = (float) $order['tax_amount'];
            $order['discount_amount'] = (float) $order['discount_amount'];
            $order['total_amount']    = (float) $order['total_amount'];
            $order['items']           = self::get_order_items( $order['id'] );
        }
        return $orders;
    }

    public static function set_order_status( $order_id, $status, $reason = '' ) {
        $valid_statuses = array( 'NEW', 'ACCEPTED', 'PREPARING', 'READY', 'SERVED', 'PAID', 'CANCELLED' );
        if ( ! in_array( $status, $valid_statuses, true ) ) {
            return false;
        }

        $order = self::get_order_by_id( $order_id );
        if ( ! $order ) {
            return false;
        }

        $update_data = array( 'status' => $status );
        if ( 'CANCELLED' === $status && ! empty( $reason ) ) {
            $update_data['cancellation_reason'] = sanitize_textarea_field( $reason );
        }

        RO_DB::update( 'orders', $update_data, array( 'id' => intval( $order_id ) ) );

        // Update item statuses to match
        if ( in_array( $status, array( 'PREPARING', 'READY', 'SERVED' ), true ) ) {
            RO_DB::update( 'order_items', array( 'status' => $status ), array( 'order_id' => intval( $order_id ) ) );
        }

        // Recalculate session if cancelled
        if ( 'CANCELLED' === $status ) {
            RO_Sessions::recalculate_session( $order['session_id'] );
        }

        RO_DB::log_event( $order_id, $order['session_id'], 'STATUS_CHANGE', "Order {$order['order_number']} status changed to {$status}. " . $reason );

        return true;
    }

    public static function set_item_status( $order_item_id, $status ) {
        $res = RO_DB::update( 'order_items', array( 'status' => sanitize_text_field( $status ) ), array( 'id' => intval( $order_item_id ) ) );
        return (bool) $res;
    }

    /**
     * Get pending orders for Kitchen Display System (KDS)
     */
    public static function get_kds_orders( $station_id = null ) {
        $sql = "SELECT o.*, t.table_number, s.session_code FROM " . RO_DB::table( 'orders' ) . " o
                JOIN " . RO_DB::table( 'tables' ) . " t ON o.table_id = t.id
                JOIN " . RO_DB::table( 'table_sessions' ) . " s ON o.session_id = s.id
                WHERE o.status IN ('NEW', 'ACCEPTED', 'PREPARING', 'READY')
                ORDER BY o.id ASC";

        $orders = RO_DB::get_results( $sql );

        $filtered_orders = array();
        foreach ( $orders as $o ) {
            $items = self::get_order_items( $o['id'] );

            if ( $station_id ) {
                $items = array_values( array_filter( $items, function( $it ) use ( $station_id ) {
                    return intval( $it['station_id'] ) === intval( $station_id );
                } ) );
            }

            if ( ! empty( $items ) ) {
                $o['items'] = $items;
                $o['elapsed_minutes'] = round( ( current_time( 'timestamp' ) - strtotime( $o['created_at'] ) ) / 60 );
                $filtered_orders[] = $o;
            }
        }

        return $filtered_orders;
    }
}
