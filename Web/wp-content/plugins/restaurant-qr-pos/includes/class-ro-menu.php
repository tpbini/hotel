<?php
/**
 * Menu, Categories, Variations & Add-ons Domain Logic.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class RO_Menu {

    // --- CATEGORIES ---

    public static function get_categories( $only_active = true ) {
        $where = $only_active ? 'WHERE is_active = 1' : '';
        return RO_DB::get_results( "SELECT * FROM " . RO_DB::table( 'categories' ) . " $where ORDER BY sort_order ASC, name ASC" );
    }

    public static function create_category( $name, $sort_order = 0 ) {
        $slug = sanitize_title( $name );
        return RO_DB::insert( 'categories', array(
            'name'       => sanitize_text_field( $name ),
            'slug'       => $slug,
            'sort_order' => intval( $sort_order ),
            'is_active'  => 1,
        ) );
    }

    public static function update_category( $id, $name, $sort_order = 0, $is_active = 1 ) {
        return RO_DB::update( 'categories', array(
            'name'       => sanitize_text_field( $name ),
            'slug'       => sanitize_title( $name ),
            'sort_order' => intval( $sort_order ),
            'is_active'  => $is_active ? 1 : 0,
        ), array( 'id' => intval( $id ) ) );
    }

    public static function delete_category( $id ) {
        return RO_DB::delete( 'categories', array( 'id' => intval( $id ) ) );
    }

    // --- MENU ITEMS ---

    public static function get_items( $category_id = null, $only_available = false ) {
        $conditions = array();
        $params = array();

        if ( $category_id ) {
            $conditions[] = "category_id = %d";
            $params[] = intval( $category_id );
        }

        if ( $only_available ) {
            $conditions[] = "status != 'hidden'";
        }

        $where = ! empty( $conditions ) ? 'WHERE ' . implode( ' AND ', $conditions ) : '';
        $sql = "SELECT * FROM " . RO_DB::table( 'menu_items' ) . " $where ORDER BY sort_order ASC, name ASC";

        $items = RO_DB::get_results( $sql, ...$params );

        // Attach variations and add-ons
        foreach ( $items as &$item ) {
            $item['variations'] = self::get_item_variations( $item['id'] );
            $item['addons']     = self::get_item_addons( $item['id'] );
            $item['base_price'] = (float) $item['base_price'];
            $item['tax_rate']   = (float) $item['tax_rate'];
        }

        return $items;
    }

    public static function get_item_by_id( $id ) {
        $item = RO_DB::get_row( "SELECT * FROM " . RO_DB::table( 'menu_items' ) . " WHERE id = %d", $id );
        if ( ! $item ) {
            return null;
        }

        $item['variations'] = self::get_item_variations( $item['id'] );
        $item['addons']     = self::get_item_addons( $item['id'] );
        $item['base_price'] = (float) $item['base_price'];
        $item['tax_rate']   = (float) $item['tax_rate'];
        return $item;
    }

    public static function get_item_variations( $menu_item_id ) {
        $vars = RO_DB::get_results( "SELECT * FROM " . RO_DB::table( 'item_variations' ) . " WHERE menu_item_id = %d ORDER BY price ASC", $menu_item_id );
        foreach ( $vars as &$v ) {
            $v['price'] = (float) $v['price'];
        }
        return $vars;
    }

    public static function get_item_addons( $menu_item_id ) {
        $addons = RO_DB::get_results( "SELECT * FROM " . RO_DB::table( 'addons' ) . " WHERE menu_item_id = %d ORDER BY price ASC", $menu_item_id );
        foreach ( $addons as &$a ) {
            $a['price'] = (float) $a['price'];
        }
        return $addons;
    }

    public static function save_item( $data, $id = null ) {
        $item_data = array(
            'category_id' => intval( $data['category_id'] ),
            'station_id'  => ! empty( $data['station_id'] ) ? intval( $data['station_id'] ) : 1,
            'name'        => sanitize_text_field( $data['name'] ),
            'description' => sanitize_textarea_field( $data['description'] ?? '' ),
            'image_url'   => esc_url_raw( $data['image_url'] ?? '' ),
            'base_price'  => floatval( $data['base_price'] ?? 0 ),
            'tax_rate'    => floatval( $data['tax_rate'] ?? 5 ),
            'food_type'   => sanitize_text_field( $data['food_type'] ?? 'veg' ),
            'status'      => sanitize_text_field( $data['status'] ?? 'available' ),
            'is_popular'  => ! empty( $data['is_popular'] ) ? 1 : 0,
            'sort_order'  => intval( $data['sort_order'] ?? 0 ),
            'prep_time'   => intval( $data['prep_time'] ?? 15 ),
        );

        if ( $id ) {
            RO_DB::update( 'menu_items', $item_data, array( 'id' => intval( $id ) ) );
            $item_id = intval( $id );
        } else {
            $item_id = RO_DB::insert( 'menu_items', $item_data );
        }

        // Save Variations
        if ( isset( $data['variations'] ) && is_array( $data['variations'] ) ) {
            RO_DB::delete( 'item_variations', array( 'menu_item_id' => $item_id ) );
            foreach ( $data['variations'] as $v ) {
                if ( ! empty( $v['name'] ) ) {
                    RO_DB::insert( 'item_variations', array(
                        'menu_item_id' => $item_id,
                        'name'         => sanitize_text_field( $v['name'] ),
                        'price'        => floatval( $v['price'] ),
                    ) );
                }
            }
        }

        // Save Addons
        if ( isset( $data['addons'] ) && is_array( $data['addons'] ) ) {
            RO_DB::delete( 'addons', array( 'menu_item_id' => $item_id ) );
            foreach ( $data['addons'] as $a ) {
                if ( ! empty( $a['name'] ) ) {
                    RO_DB::insert( 'addons', array(
                        'menu_item_id' => $item_id,
                        'name'         => sanitize_text_field( $a['name'] ),
                        'price'        => floatval( $a['price'] ?? 0 ),
                        'type'         => sanitize_text_field( $a['type'] ?? 'extra' ),
                    ) );
                }
            }
        }

        return $item_id;
    }

    public static function set_item_status( $id, $status ) {
        return RO_DB::update( 'menu_items', array(
            'status' => sanitize_text_field( $status ),
        ), array( 'id' => intval( $id ) ) );
    }

    public static function delete_item( $id ) {
        RO_DB::delete( 'item_variations', array( 'menu_item_id' => intval( $id ) ) );
        RO_DB::delete( 'addons', array( 'menu_item_id' => intval( $id ) ) );
        return RO_DB::delete( 'menu_items', array( 'id' => intval( $id ) ) );
    }

    /**
     * Get Complete Hierarchical Menu Payload for Client Applications
     */
    public static function get_full_menu_payload() {
        $categories = self::get_categories( true );
        if ( empty( $categories ) ) {
            RO_Activator::activate();
            $categories = self::get_categories( true );
        }
        $all_items = self::get_items( null, true );

        $categorized = array();
        foreach ( $categories as $cat ) {
            $cat['items'] = array();
            $categorized[ $cat['id'] ] = $cat;
        }

        foreach ( $all_items as $item ) {
            if ( isset( $categorized[ $item['category_id'] ] ) ) {
                $categorized[ $item['category_id'] ]['items'][] = $item;
            }
        }

        return array_values( $categorized );
    }
}
