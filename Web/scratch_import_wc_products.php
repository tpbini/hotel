<?php
/**
 * Script to create WooCommerce products and categories for The Cochin menu items.
 */
require_once 'wp-load.php';

if (!class_exists('WooCommerce')) {
    echo "WooCommerce is not active.\n";
    exit;
}

$json_file = 'C:/Users/USER/.gemini/antigravity-ide/brain/d46c7272-5c00-4781-a167-0014d3ef2e11/scratch/parsed_menu.json';
if (!file_exists($json_file)) {
    echo "JSON file not found.\n";
    exit;
}

$categories_data = json_decode(file_get_contents($json_file), true);

$dummy_images = array(
    'TRAY' => 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?auto=format&fit=crop&w=800&q=80',
    'STARTERS' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=800&q=80',
    'DOSA' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=800&q=80',
    'FISHERMAN\'S FAVOURITE' => 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&w=800&q=80',
    'MEAT & POULTRY' => 'https://images.unsplash.com/photo-1545247181-516773cae754?auto=format&fit=crop&w=800&q=80',
    'VEGETARIAN DISHES' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=800&q=80',
    'SIDE ORDERS' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=800&q=80',
    'RICE' => 'https://images.unsplash.com/photo-1516714435131-44d6b64dc6a2?auto=format&fit=crop&w=800&q=80',
    'BREAD' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=800&q=80',
);

$products_created = 0;
$product_map = array();

foreach ($categories_data as $cat) {
    $cat_name = $cat['title'];
    
    // Ensure Category term exists
    $term = get_term_by('name', $cat_name, 'product_cat');
    if (!$term) {
        $term_res = wp_insert_term($cat_name, 'product_cat', array(
            'description' => $cat['intro'] ?? '',
        ));
        $term_id = is_array($term_res) ? $term_res['term_id'] : 0;
    } else {
        $term_id = $term->term_id;
    }

    foreach ($cat['items'] as $item) {
        $title = $item['title'];
        $raw_price = str_replace(array('£', '$', ' '), '', $item['price']);
        $price = is_numeric($raw_price) ? $raw_price : '0';
        $desc = $item['desc'] ?? '';
        $tags = $item['tags'] ?? array();

        // Check if product exists
        $existing = get_page_by_title($title, OBJECT, 'product');
        if ($existing) {
            $product_id = $existing->ID;
            $product = wc_get_product($product_id);
            $product->set_regular_price($price);
            $product->set_price($price);
            $product->set_short_description($desc);
            $product->save();
        } else {
            $product = new WC_Product_Simple();
            $product->set_name($title);
            $product->set_status('publish');
            $product->set_catalog_visibility('visible');
            $product->set_description($desc);
            $product->set_short_description($desc);
            $product->set_regular_price($price);
            $product->set_price($price);
            if ($term_id) {
                $product->set_category_ids(array($term_id));
            }
            $product_id = $product->save();
            $products_created++;
        }

        // Add product tags (e.g. Veg, Vegan, Gluten-Free)
        if (!empty($tags)) {
            wp_set_object_terms($product_id, $tags, 'product_tag');
        }

        // Save dietary metadata
        update_post_meta($product_id, '_the_cochin_dietary', implode(',', $tags));

        $product_map[$title] = array(
            'id'    => $product_id,
            'url'   => get_permalink($product_id),
            'price' => $price,
            'tags'  => $tags
        );
    }
}

file_put_contents('C:/Users/USER/.gemini/antigravity-ide/brain/d46c7272-5c00-4781-a167-0014d3ef2e11/scratch/product_map.json', json_encode($product_map, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "WooCommerce Products setup completed!\n";
echo "Created/Updated: " . count($product_map) . " products.\n";
