<?php
/**
 * Sync All WooCommerce Menu Products to Restaurant QR POS Tables
 */

require_once __DIR__ . '/Web/wp-load.php';

if (!defined('ABSPATH')) {
    die('Could not load WordPress.');
}

global $wpdb;

echo "========================================================\n";
echo "Starting Sync: WooCommerce Menu -> Restaurant QR POS\n";
echo "========================================================\n\n";

// 1. Define Category Structure with Station and Sort Order
$categories_def = array(
    'tea-shop-snacks' => array(
        'name'       => 'Kerala Tea Shop Snacks',
        'sort_order' => 1,
        'station_id' => 1, // Main Kitchen
    ),
    'cochin-thali' => array(
        'name'       => 'Cochin Thali',
        'sort_order' => 2,
        'station_id' => 1,
    ),
    'vegetarian-starters' => array(
        'name'       => 'Vegetarian Starters',
        'sort_order' => 3,
        'station_id' => 1,
    ),
    'non-vegetarian-starters' => array(
        'name'       => 'Non-Vegetarian Starters',
        'sort_order' => 4,
        'station_id' => 1,
    ),
    'dosa' => array(
        'name'       => 'Dosa & South Indian Classics',
        'sort_order' => 5,
        'station_id' => 1,
    ),
    'fishermans-favourite' => array(
        'name'       => "Fisherman's Favourite (Seafood)",
        'sort_order' => 6,
        'station_id' => 1,
    ),
    'meat-poultry-chicken' => array(
        'name'       => 'Meat & Poultry - Chicken',
        'sort_order' => 7,
        'station_id' => 1,
    ),
    'meat-poultry-lamb' => array(
        'name'       => 'Meat & Poultry - Lamb',
        'sort_order' => 8,
        'station_id' => 1,
    ),
    'biryani' => array(
        'name'       => 'Malabar Dum Biryani',
        'sort_order' => 9,
        'station_id' => 1,
    ),
    'vegetarian-dishes' => array(
        'name'       => 'Vegetarian Dishes (Curries)',
        'sort_order' => 10,
        'station_id' => 1,
    ),
    'side-orders' => array(
        'name'       => 'Side Orders',
        'sort_order' => 11,
        'station_id' => 1,
    ),
    'rice' => array(
        'name'       => 'Rice Dishes',
        'sort_order' => 12,
        'station_id' => 1,
    ),
    'bread' => array(
        'name'       => 'Breads & Appam',
        'sort_order' => 13,
        'station_id' => 1,
    ),
    'childrens-menu' => array(
        'name'       => "Children's Menu",
        'sort_order' => 14,
        'station_id' => 1,
    ),
    'desserts' => array(
        'name'       => 'Handcrafted Desserts',
        'sort_order' => 15,
        'station_id' => 4, // Dessert Station
    ),
);

// 2. Clear old QR POS menu items, variations, addons, and categories
echo "1. Resetting QR POS menu tables...\n";
$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}ro_addons");
$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}ro_item_variations");
$wpdb->query("TRUNCATE TABLE {$wpdb->prefix}ro_order_item_modifiers");
$wpdb->query("DELETE FROM {$wpdb->prefix}ro_menu_items");
$wpdb->query("ALTER TABLE {$wpdb->prefix}ro_menu_items AUTO_INCREMENT = 1");
$wpdb->query("DELETE FROM {$wpdb->prefix}ro_categories");
$wpdb->query("ALTER TABLE {$wpdb->prefix}ro_categories AUTO_INCREMENT = 1");

// 3. Create Categories in wp_ro_categories
echo "\n2. Creating Categories in QR POS...\n";
$cat_id_map = array();

foreach ($categories_def as $slug => $cdata) {
    $inserted = $wpdb->insert(
        "{$wpdb->prefix}ro_categories",
        array(
            'name'       => $cdata['name'],
            'slug'       => $slug,
            'sort_order' => $cdata['sort_order'],
            'is_active'  => 1,
        ),
        array('%s', '%s', '%d', '%d')
    );

    $cat_id = $wpdb->insert_id;
    $cat_id_map[$slug] = $cat_id;
    echo "   [+] Category #{$cat_id}: {$cdata['name']} (slug: {$slug})\n";
}

// 4. Fetch all WooCommerce products grouped by category
echo "\n3. Migrating Products from WooCommerce to QR POS...\n";

$total_migrated = 0;

foreach ($categories_def as $slug => $cdata) {
    $ro_cat_id = $cat_id_map[$slug];
    $station_id = $cdata['station_id'];

    $products = get_posts(array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $slug,
            ),
        ),
        'orderby'        => 'menu_order ID',
        'order'          => 'ASC',
    ));

    echo "\n--- Category: {$cdata['name']} (" . count($products) . " items) ---\n";

    $item_sort = 1;
    foreach ($products as $post) {
        $product_id = $post->ID;
        $title = $post->post_title;
        $desc = !empty($post->post_excerpt) ? $post->post_excerpt : $post->post_content;
        $desc = strip_tags($desc);

        // Price
        $price = get_post_meta($product_id, '_price', true);
        if ($price === '' || $price === false) {
            $price = get_post_meta($product_id, '_regular_price', true) ?: 0.00;
        }
        $price = (float)$price;

        // Image
        $thumb_id = get_post_thumbnail_id($product_id);
        $image_url = '';
        if ($thumb_id) {
            $image_url = wp_get_attachment_image_url($thumb_id, 'medium_large') ?: '';
        }

        // Tags & Dietary Determination
        $tags = wp_get_post_terms($product_id, 'product_tag', array('fields' => 'names'));
        if (is_wp_error($tags)) {
            $tags = array();
        }
        $tag_str = strtolower(implode(' ', $tags) . ' ' . $title);

        $food_type = 'veg';
        $is_popular = 0;

        if (stripos($tag_str, 'vegan') !== false) {
            $food_type = 'vegan';
        } elseif (
            in_array($slug, array('non-vegetarian-starters', 'fishermans-favourite', 'meat-poultry-chicken', 'meat-poultry-lamb', 'biryani')) ||
            stripos($tag_str, 'non-veg') !== false ||
            stripos($tag_str, 'chicken') !== false ||
            stripos($tag_str, 'lamb') !== false ||
            stripos($tag_str, 'fish') !== false ||
            stripos($tag_str, 'prawn') !== false ||
            stripos($tag_str, 'calamari') !== false ||
            stripos($tag_str, 'beef') !== false ||
            stripos($tag_str, 'seafood') !== false
        ) {
            $food_type = 'non-veg';
        }

        if (
            stripos($tag_str, 'popular') !== false ||
            stripos($tag_str, 'special') !== false ||
            stripos($tag_str, 'chef') !== false
        ) {
            $is_popular = 1;
        }

        // Insert into ro_menu_items
        $wpdb->insert(
            "{$wpdb->prefix}ro_menu_items",
            array(
                'category_id' => $ro_cat_id,
                'station_id'  => $station_id,
                'name'        => $title,
                'description' => $desc,
                'image_url'   => $image_url,
                'base_price'  => $price,
                'tax_rate'    => 0.00, // UK Restaurant VAT included in base price
                'food_type'   => $food_type,
                'status'      => 'available',
                'is_popular'  => $is_popular,
                'sort_order'  => $item_sort++,
                'prep_time'   => ($slug === 'dosa' || $slug === 'biryani') ? 20 : 15,
            ),
            array('%d', '%d', '%s', '%s', '%s', '%f', '%f', '%s', '%s', '%d', '%d', '%d')
        );

        $menu_item_id = $wpdb->insert_id;
        $total_migrated++;

        echo "   -> Synced [RO #{$menu_item_id}] {$title} | £" . number_format($price, 2) . " | Type: {$food_type}\n";
    }
}

echo "\n========================================================\n";
echo "Sync Completed Successfully!\n";
echo "Total Menu Items in QR POS: {$total_migrated}\n";
echo "========================================================\n";
