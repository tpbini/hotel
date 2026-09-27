<?php
/**
 * CLI Script to Import Desserts from "The_Cochin_A5_Dessert_Menu_With_Prices.docx"
 */

if (!defined('ABSPATH')) {
    require_once dirname(__FILE__) . '/Web/wp-load.php';
}

echo "=== Ensuring Desserts Category Exists ===\n";
$cat_slug = 'desserts';
$cat_name = 'Desserts';

$term = get_term_by('slug', $cat_slug, 'product_cat');
if (!$term) {
    $created = wp_insert_term($cat_name, 'product_cat', array('slug' => $cat_slug));
    $cat_id = $created['term_id'];
    echo "Created category: $cat_name ($cat_slug) -> ID $cat_id\n";
} else {
    $cat_id = $term->term_id;
    echo "Found category: $cat_name ($cat_slug) -> ID $cat_id\n";
}

// Remove any existing products in the desserts category to avoid duplicates
$existing_dessert_ids = get_posts(array(
    'post_type'      => 'product',
    'post_status'    => 'any',
    'posts_per_page' => -1,
    'tax_query'      => array(
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => 'desserts',
        ),
    ),
    'fields'         => 'ids',
));

foreach ($existing_dessert_ids as $did) {
    wp_delete_post($did, true);
    echo "Deleted old dessert product ID: $did\n";
}

// Find image attachment for desserts
global $wpdb;
$dessert_img_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND (post_name LIKE '%product%' OR guid LIKE '%product%') LIMIT 1");
if (!$dessert_img_id) {
    $dessert_img_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND (post_name LIKE '%manasa%' OR guid LIKE '%manasa%') LIMIT 1");
}

$desserts = array(
    array(
        'title'      => 'Rasmalai Duo',
        'price'      => '7.95',
        'desc'       => 'Traditional rasmalai paired with rasmalai ice cream, finished with saffron and pistachio.',
        'short_desc' => 'Traditional rasmalai paired with rasmalai ice cream, finished with saffron and pistachio.',
        'tags'       => array('Veg', 'Dessert', 'Chef Special'),
    ),
    array(
        'title'      => 'Kala Jamun & Palada',
        'price'      => '7.95',
        'desc'       => 'Warm kala jamun served with traditional Kerala palada payasam, finished with pistachio.',
        'short_desc' => 'Warm kala jamun served with traditional Kerala palada payasam, finished with pistachio.',
        'tags'       => array('Veg', 'Dessert', 'Popular'),
    ),
    array(
        'title'      => 'Gulab Jamun & Ice Cream',
        'price'      => '6.95',
        'desc'       => 'Warm gulab jamun served with vanilla ice cream and pistachio.',
        'short_desc' => 'Warm gulab jamun served with vanilla ice cream and pistachio.',
        'tags'       => array('Veg', 'Dessert'),
    ),
    array(
        'title'      => 'Pazhampori & Ice Cream',
        'price'      => '6.95',
        'desc'       => 'Traditional Kerala banana fritters served warm with vanilla ice cream.',
        'short_desc' => 'Traditional Kerala banana fritters served warm with vanilla ice cream.',
        'tags'       => array('Veg', 'Dessert', 'Traditional'),
    ),
    array(
        'title'      => 'Royal Falooda',
        'price'      => '9.95',
        'desc'       => 'Rose milk layered with falooda vermicelli, basil seeds and jelly, topped with three mini scoops of ice cream, mini gulab jamun, pistachios and almonds.',
        'short_desc' => 'Rose milk layered with falooda vermicelli, basil seeds, jelly, 3 mini ice cream scoops, mini gulab jamun, and roasted nuts.',
        'tags'       => array('Veg', 'Dessert', 'Speciality'),
    ),
    array(
        'title'      => 'Pista Kulfi',
        'price'      => '5.95',
        'desc'       => 'Creamy pistachio kulfi finished with crushed pistachios and rose petals.',
        'short_desc' => 'Creamy pistachio kulfi finished with crushed pistachios and rose petals.',
        'tags'       => array('Veg', 'Dessert', 'Kulfi'),
    ),
    array(
        'title'      => 'Malai Kulfi',
        'price'      => '5.95',
        'desc'       => 'Traditional creamy malai kulfi finished with almonds, pistachios and saffron.',
        'short_desc' => 'Traditional creamy malai kulfi finished with almonds, pistachios and saffron.',
        'tags'       => array('Veg', 'Dessert', 'Kulfi'),
    ),
    array(
        'title'      => 'Mango Kulfi',
        'price'      => '5.95',
        'desc'       => 'Creamy mango kulfi finished with mango coulis and crushed pistachios.',
        'short_desc' => 'Creamy mango kulfi finished with mango coulis and crushed pistachios.',
        'tags'       => array('Veg', 'Dessert', 'Kulfi'),
    ),
    array(
        'title'      => 'Guava Chilli Twist',
        'price'      => '6.95',
        'desc'       => 'Creamy guava ice cream finished with a touch of chilli and chaat masala.',
        'short_desc' => 'Creamy guava ice cream finished with a touch of chilli and chaat masala.',
        'tags'       => array('Veg', 'Dessert', 'Speciality'),
    ),
);

echo "\n=== Creating Dessert Products ===\n";
$created = 0;
foreach ($desserts as $d) {
    $pid = wp_insert_post(array(
        'post_title'   => $d['title'],
        'post_content' => $d['desc'],
        'post_excerpt' => $d['short_desc'],
        'post_status'  => 'publish',
        'post_type'    => 'product',
    ));

    if (is_wp_error($pid) || !$pid) {
        echo "Error creating: {$d['title']}\n";
        continue;
    }

    wp_set_object_terms($pid, 'simple', 'product_type');
    wp_set_object_terms($pid, (int)$cat_id, 'product_cat');

    if (!empty($d['tags'])) {
        wp_set_object_terms($pid, $d['tags'], 'product_tag');
        update_post_meta($pid, '_the_cochin_dietary', implode(', ', $d['tags']));
    }

    $price = (string)$d['price'];
    update_post_meta($pid, '_price', $price);
    update_post_meta($pid, '_regular_price', $price);
    update_post_meta($pid, '_visibility', 'visible');
    update_post_meta($pid, '_stock_status', 'instock');
    update_post_meta($pid, '_manage_stock', 'no');
    update_post_meta($pid, 'total_sales', '0');

    if ($dessert_img_id) {
        set_post_thumbnail($pid, (int)$dessert_img_id);
    }

    $created++;
    echo "[OK] #{$pid} - {$d['title']} (£{$price})\n";
}

// Flush cache
wp_cache_flush();
echo "\nSuccessfully imported {$created} desserts from The_Cochin_A5_Dessert_Menu_With_Prices.docx!\n";
