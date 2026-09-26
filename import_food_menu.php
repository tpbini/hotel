<?php
/**
 * CLI Script to Remove all current WooCommerce products and import all items
 * structured identically to the PDF "The_Cochin_Full_Food_Menu_With_Tea_Shop_Snacks.pdf"
 */

if (!defined('ABSPATH')) {
    require_once dirname(__FILE__) . '/Web/wp-load.php';
}

echo "=== Removing All Existing Products ===\n";
$existing_products = get_posts(array(
    'post_type'      => 'product',
    'post_status'    => 'any',
    'posts_per_page' => -1,
    'fields'         => 'ids',
));

foreach ($existing_products as $product_id) {
    wp_delete_post($product_id, true);
    echo "Deleted product ID: $product_id\n";
}

echo "\n=== Ensuring Product Categories Exist ===\n";
$categories_def = array(
    'tea-shop-snacks'        => 'Kerala Tea Shop Snacks',
    'cochin-thali'           => 'Cochin Thali',
    'vegetarian-starters'    => 'Vegetarian Starters',
    'non-vegetarian-starters'=> 'Non-Vegetarian Starters',
    'dosa'                   => 'Dosa',
    'fishermans-favourite'   => 'Fisherman\'s Favourite',
    'meat-poultry-chicken'   => 'Meat & Poultry - Chicken',
    'meat-poultry-lamb'      => 'Meat & Poultry - Lamb',
    'biryani'                => 'Biryani',
    'vegetarian-dishes'      => 'Vegetarian Dishes',
    'side-orders'            => 'Side Orders',
    'rice'                   => 'Rice',
    'bread'                  => 'Bread',
    'childrens-menu'         => 'Children\'s Menu',
);

$cat_ids = array();
foreach ($categories_def as $slug => $name) {
    $term = get_term_by('slug', $slug, 'product_cat');
    if (!$term) {
        $created = wp_insert_term($name, 'product_cat', array('slug' => $slug));
        if (!is_wp_error($created)) {
            $cat_ids[$slug] = $created['term_id'];
            echo "Created category: $name ($slug)\n";
        }
    } else {
        $cat_ids[$slug] = $term->term_id;
        echo "Found category: $name ($slug) -> ID {$term->term_id}\n";
    }
}

// Find existing image attachment IDs
function find_attachment_by_name($name) {
    global $wpdb;
    $id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_type = 'attachment' AND (post_name LIKE %s OR guid LIKE %s) LIMIT 1", "%$name%", "%$name%"));
    return $id ? (int)$id : 0;
}

$img_map = array(
    'prawn'  => find_attachment_by_name('chemmen') ?: find_attachment_by_name('prawn'),
    'fish'   => find_attachment_by_name('fish') ?: find_attachment_by_name('chemmen'),
    'soup'   => find_attachment_by_name('soup'),
    'dosa'   => find_attachment_by_name('Dosa') ?: find_attachment_by_name('dosa'),
    'chick'  => find_attachment_by_name('chick') ?: find_attachment_by_name('food1'),
    'rice'   => find_attachment_by_name('jyt') ?: find_attachment_by_name('food2'),
    'bread'  => find_attachment_by_name('appam') ?: find_attachment_by_name('food3'),
    'veg'    => find_attachment_by_name('appam') ?: find_attachment_by_name('food6'),
    'vada'   => find_attachment_by_name('uzhunnu') ?: find_attachment_by_name('food3'),
);

$menu_items = array(
    // =========================================================================
    // 1. KERALA TEA SHOP SNACKS (£6.95)
    // =========================================================================
    array(
        'title'       => 'Kerala Tea Shop Snacks',
        'price'       => '6.95',
        'desc'        => "A light, crispy and spiced assortment inspired by the traditional tea shops of Kerala, served with homemade pickles and chutneys.\n\n• Pappadoms: Kerala-style light and crispy pappadoms.\n• Murukku: A crunchy snack made with rice flour, coconut and black sesame seeds.\n• Pappadavada: Pappadoms dipped in a spiced rice-flour batter with cumin and sesame seeds, then fried.\n• Banana Chips: Thinly sliced plantain, deep-fried until crisp.\n• Accompaniments: Mango pickle, lemon pickle and chef's homemade pickles.",
        'short_desc'  => 'A light, crispy and spiced assortment (Pappadoms, Murukku, Pappadavada, Banana Chips & Pickles) inspired by Kerala tea shops.',
        'category'    => 'tea-shop-snacks',
        'tags'        => array('Veg', 'Vegan', 'Traditional', 'Popular'),
        'image_key'   => 'veg',
    ),

    // =========================================================================
    // 2. COCHIN THALI
    // =========================================================================
    array(
        'title'       => 'Non-Veg Thali',
        'price'       => '21.95',
        'desc'        => 'A selection of South Indian dishes served in small bowls, accompanied by rice and bread and finished with dessert.',
        'short_desc'  => 'A selection of South Indian non-veg dishes served in small bowls, accompanied by rice, bread and dessert.',
        'category'    => 'cochin-thali',
        'tags'        => array('Non-Veg', 'Speciality', 'Thali'),
        'image_key'   => 'chick',
    ),
    array(
        'title'       => 'Veg Thali',
        'price'       => '18.95',
        'desc'        => 'A selection of South Indian dishes served in small bowls, accompanied by rice and bread and finished with dessert.',
        'short_desc'  => 'A selection of South Indian vegetarian dishes served in small bowls, accompanied by rice, bread and dessert.',
        'category'    => 'cochin-thali',
        'tags'        => array('Veg', 'Speciality', 'Thali'),
        'image_key'   => 'veg',
    ),
    array(
        'title'       => 'Seafood Thali',
        'price'       => '24.99',
        'desc'        => 'A selection of South Indian dishes served in small bowls, accompanied by rice and bread and finished with dessert.',
        'short_desc'  => 'A selection of South Indian seafood dishes served in small bowls, accompanied by rice, bread and dessert.',
        'category'    => 'cochin-thali',
        'tags'        => array('Seafood', 'Speciality', 'Thali'),
        'image_key'   => 'fish',
    ),
    array(
        'title'       => 'Vegan Gluten-Free Thali',
        'price'       => '18.95',
        'desc'        => 'A selection of South Indian dishes served in small bowls, accompanied by rice and bread and finished with dessert.',
        'short_desc'  => 'A selection of South Indian vegan and gluten-free dishes served in small bowls, accompanied by rice, bread and dessert.',
        'category'    => 'cochin-thali',
        'tags'        => array('Veg', 'Vegan', 'Gluten Free', 'Speciality', 'Thali'),
        'image_key'   => 'veg',
    ),

    // =========================================================================
    // 3. VEGETARIAN STARTERS
    // =========================================================================
    array(
        'title'       => 'Lentil Soup',
        'price'       => '4.95',
        'desc'        => 'Thick lentil soup with shallots, ginger, garlic and curry leaves.',
        'short_desc'  => 'Thick lentil soup with shallots, ginger, garlic and curry leaves.',
        'category'    => 'vegetarian-starters',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'soup',
    ),
    array(
        'title'       => 'Chilli Paneer (Starter)',
        'price'       => '6.95',
        'desc'        => 'Cottage cheese cooked with chilli, garlic, peppers and tomato sauce.',
        'short_desc'  => 'Cottage cheese cooked with chilli, garlic, peppers and tomato sauce.',
        'category'    => 'vegetarian-starters',
        'tags'        => array('Veg', 'Spicy'),
        'image_key'   => 'veg',
    ),
    array(
        'title'       => 'Onion Bhaji',
        'price'       => '4.95',
        'desc'        => 'Crispy onion fritters in a fragrantly spiced gram-flour batter.',
        'short_desc'  => 'Crispy onion fritters in a fragrantly spiced gram-flour batter.',
        'category'    => 'vegetarian-starters',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'veg',
    ),
    array(
        'title'       => 'Vegetable Samosa',
        'price'       => '4.15',
        'desc'        => 'Pastry filled with spiced potatoes, onions, peas and mixed vegetables.',
        'short_desc'  => 'Pastry filled with spiced potatoes, onions, peas and mixed vegetables.',
        'category'    => 'vegetarian-starters',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'veg',
    ),
    array(
        'title'       => 'Uzhunnu Vada',
        'price'       => '5.25',
        'desc'        => 'South Indian lentil doughnuts with ginger and curry leaves.',
        'short_desc'  => 'South Indian lentil doughnuts with ginger and curry leaves.',
        'category'    => 'vegetarian-starters',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'vada',
    ),
    array(
        'title'       => 'Potato Bonda',
        'price'       => '5.25',
        'desc'        => 'Spiced potato in chickpea batter, deep-fried and served with coconut chutney.',
        'short_desc'  => 'Spiced potato in chickpea batter, deep-fried and served with coconut chutney.',
        'category'    => 'vegetarian-starters',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'veg',
    ),

    // =========================================================================
    // 4. NON-VEGETARIAN STARTERS
    // =========================================================================
    array(
        'title'       => 'Calamari Rings',
        'price'       => '6.95',
        'desc'        => 'Squid rings marinated with turmeric, lemon and spices, coated in chickpea batter and fried.',
        'short_desc'  => 'Squid rings marinated with turmeric, lemon and spices, coated in chickpea batter and fried.',
        'category'    => 'non-vegetarian-starters',
        'tags'        => array('Seafood'),
        'image_key'   => 'fish',
    ),
    array(
        'title'       => 'Alleppey Prawn Fry',
        'price'       => '7.95',
        'desc'        => 'King prawns marinated in Kerala spices, coated and fried in the traditional style.',
        'short_desc'  => 'King prawns marinated in Kerala spices, coated and fried in the traditional style.',
        'category'    => 'non-vegetarian-starters',
        'tags'        => array('Seafood', 'Chef Special'),
        'image_key'   => 'prawn',
    ),
    array(
        'title'       => 'Chicken Ularthiyathu',
        'price'       => '5.95',
        'desc'        => 'Chicken stir-fried with pepper, curry leaves and coconut slivers.',
        'short_desc'  => 'Chicken stir-fried with pepper, curry leaves and coconut slivers.',
        'category'    => 'non-vegetarian-starters',
        'tags'        => array('Non-Veg'),
        'image_key'   => 'chick',
    ),
    array(
        'title'       => 'Samosa (Chicken / Lamb)',
        'price'       => '4.95',
        'desc'        => 'Crisp pastry filled with savoury spiced chicken or lamb.',
        'short_desc'  => 'Crisp pastry filled with savoury spiced chicken or lamb.',
        'category'    => 'non-vegetarian-starters',
        'tags'        => array('Non-Veg'),
        'image_key'   => 'chick',
    ),
    array(
        'title'       => 'Lamb Dry Roast',
        'price'       => '6.95',
        'desc'        => 'Lamb cooked with ginger, garlic and curry leaves, finished with ground pepper.',
        'short_desc'  => 'Lamb cooked with ginger, garlic and curry leaves, finished with ground pepper.',
        'category'    => 'non-vegetarian-starters',
        'tags'        => array('Non-Veg'),
        'image_key'   => 'chick',
    ),

    // =========================================================================
    // 5. DOSA
    // =========================================================================
    array(
        'title'       => 'Masala Dosa',
        'price'       => '9.95',
        'desc'        => 'Crispy rice and lentil dosa stuffed with potato masala, served with sambar and chutneys.',
        'short_desc'  => 'Crispy rice and lentil dosa stuffed with potato masala, served with sambar and chutneys.',
        'category'    => 'dosa',
        'tags'        => array('Veg', 'Popular'),
        'image_key'   => 'dosa',
    ),
    array(
        'title'       => 'Nair Masala Dosa',
        'price'       => '9.95',
        'desc'        => 'Rice and lentil pancake spread with spicy coconut chutney and filled with potato masala.',
        'short_desc'  => 'Rice and lentil pancake spread with spicy coconut chutney and filled with potato masala.',
        'category'    => 'dosa',
        'tags'        => array('Veg', 'Spicy'),
        'image_key'   => 'dosa',
    ),
    array(
        'title'       => 'Plain Dosa',
        'price'       => '8.50',
        'desc'        => 'Crispy rice and lentil dosa served with sambar and chutneys.',
        'short_desc'  => 'Crispy rice and lentil dosa served with sambar and chutneys.',
        'category'    => 'dosa',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'dosa',
    ),

    // =========================================================================
    // 6. FISHERMAN'S FAVOURITE
    // =========================================================================
    array(
        'title'       => 'Cochin King Fish Curry',
        'price'       => '12.95',
        'desc'        => 'King fish cooked in a coastal Kerala coconut-milk curry.',
        'short_desc'  => 'King fish cooked in a coastal Kerala coconut-milk curry.',
        'category'    => 'fishermans-favourite',
        'tags'        => array('Seafood', 'Chef Special'),
        'image_key'   => 'fish',
    ),
    array(
        'title'       => 'Fish in Banana Leaf',
        'price'       => '14.95',
        'desc'        => 'Boneless fish marinated and grilled, wrapped in banana leaf with a lightly spiced sauce.',
        'short_desc'  => 'Boneless fish marinated and grilled, wrapped in banana leaf with a lightly spiced sauce.',
        'category'    => 'fishermans-favourite',
        'tags'        => array('Seafood', 'Chef Special'),
        'image_key'   => 'fish',
    ),
    array(
        'title'       => 'Tiger Prawn Masala',
        'price'       => '14.95',
        'desc'        => 'Tiger prawns cooked in a coconut-milk sauce with ginger and garlic.',
        'short_desc'  => 'Tiger prawns cooked in a coconut-milk sauce with ginger and garlic.',
        'category'    => 'fishermans-favourite',
        'tags'        => array('Seafood'),
        'image_key'   => 'prawn',
    ),
    array(
        'title'       => 'King Prawn Curry',
        'price'       => '14.95',
        'desc'        => 'Tiger prawns cooked in The Cochin\'s special Kerala-style coastal curry.',
        'short_desc'  => 'Tiger prawns cooked in The Cochin\'s special Kerala-style coastal curry.',
        'category'    => 'fishermans-favourite',
        'tags'        => array('Seafood', 'Popular'),
        'image_key'   => 'prawn',
    ),
    array(
        'title'       => 'Mixed Seafood Curry',
        'price'       => '14.95',
        'desc'        => 'King fish, squid, prawns and mussels cooked in an authentic Kerala spiced sauce.',
        'short_desc'  => 'King fish, squid, prawns and mussels cooked in an authentic Kerala spiced sauce.',
        'category'    => 'fishermans-favourite',
        'tags'        => array('Seafood'),
        'image_key'   => 'fish',
    ),

    // =========================================================================
    // 7. MEAT & POULTRY - CHICKEN
    // =========================================================================
    array(
        'title'       => 'Nadan Chicken Curry',
        'price'       => '10.95',
        'desc'        => 'Traditional Kerala chicken curry made with dry-roasted spices.',
        'short_desc'  => 'Traditional Kerala chicken curry made with dry-roasted spices.',
        'category'    => 'meat-poultry-chicken',
        'tags'        => array('Non-Veg', 'Popular'),
        'image_key'   => 'chick',
    ),
    array(
        'title'       => 'Chicken Korma',
        'price'       => '10.95',
        'desc'        => 'Chicken cooked in a rich onion, ginger, garlic and cashew sauce.',
        'short_desc'  => 'Chicken cooked in a rich onion, ginger, garlic and cashew sauce.',
        'category'    => 'meat-poultry-chicken',
        'tags'        => array('Non-Veg', 'Mild'),
        'image_key'   => 'chick',
    ),
    array(
        'title'       => 'Thattukada Chicken',
        'price'       => '10.95',
        'desc'        => 'Kerala street-style chicken stir-fried with pepper, curry leaves and coconut slivers.',
        'short_desc'  => 'Kerala street-style chicken stir-fried with pepper, curry leaves and coconut slivers.',
        'category'    => 'meat-poultry-chicken',
        'tags'        => array('Non-Veg', 'Spicy'),
        'image_key'   => 'chick',
    ),

    // =========================================================================
    // 8. MEAT & POULTRY - LAMB
    // =========================================================================
    array(
        'title'       => 'Cochin Lamb Curry',
        'price'       => '10.95',
        'desc'        => 'Lamb cooked in a tomato and spice sauce, finished with coconut milk.',
        'short_desc'  => 'Lamb cooked in a tomato and spice sauce, finished with coconut milk.',
        'category'    => 'meat-poultry-lamb',
        'tags'        => array('Non-Veg'),
        'image_key'   => 'chick',
    ),
    array(
        'title'       => 'Lamb & Spinach Curry',
        'price'       => '10.95',
        'desc'        => 'Lamb cooked with fresh spinach, onion, coriander, garlic and ginger.',
        'short_desc'  => 'Lamb cooked with fresh spinach, onion, coriander, garlic and ginger.',
        'category'    => 'meat-poultry-lamb',
        'tags'        => array('Non-Veg'),
        'image_key'   => 'chick',
    ),
    array(
        'title'       => 'Lamb Ularthiyathu',
        'price'       => '11.95',
        'desc'        => 'Kerala-style lamb cooked with spices and stir-fried with curry leaves.',
        'short_desc'  => 'Kerala-style lamb cooked with spices and stir-fried with curry leaves.',
        'category'    => 'meat-poultry-lamb',
        'tags'        => array('Non-Veg', 'Chef Special'),
        'image_key'   => 'chick',
    ),

    // =========================================================================
    // 9. BIRYANI
    // =========================================================================
    array(
        'title'       => 'Chicken Biryani',
        'price'       => '12.95',
        'desc'        => 'Chicken cooked with aromatic Kerala spices and fragrant rice, finished with cashew nuts and raisins.',
        'short_desc'  => 'Chicken cooked with aromatic Kerala spices and fragrant rice, finished with cashew nuts and raisins.',
        'category'    => 'biryani',
        'tags'        => array('Non-Veg', 'Popular'),
        'image_key'   => 'rice',
    ),

    // =========================================================================
    // 10. VEGETARIAN DISHES
    // =========================================================================
    array(
        'title'       => 'Aubergine Curry',
        'price'       => '7.95',
        'desc'        => 'Aubergine cooked with roasted coriander, onion and garlic, finished with coconut milk and cashew paste.',
        'short_desc'  => 'Aubergine cooked with roasted coriander, onion and garlic, finished with coconut milk and cashew paste.',
        'category'    => 'vegetarian-dishes',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'veg',
    ),
    array(
        'title'       => 'Dal & Spinach Curry',
        'price'       => '7.95',
        'desc'        => 'Lentil and spinach curry.',
        'short_desc'  => 'Nutritious lentil and spinach curry cooked with traditional spices.',
        'category'    => 'vegetarian-dishes',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'veg',
    ),
    array(
        'title'       => 'Okra Masala',
        'price'       => '7.95',
        'desc'        => 'Okra cooked with carrots, roasted coconut, mustard seeds and curry leaves.',
        'short_desc'  => 'Okra cooked with carrots, roasted coconut, mustard seeds and curry leaves.',
        'category'    => 'vegetarian-dishes',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'veg',
    ),
    array(
        'title'       => 'Palak Paneer',
        'price'       => '9.95',
        'desc'        => 'Fresh spinach cooked with cottage cheese and spices.',
        'short_desc'  => 'Fresh spinach cooked with cottage cheese and spices.',
        'category'    => 'vegetarian-dishes',
        'tags'        => array('Veg'),
        'image_key'   => 'veg',
    ),
    array(
        'title'       => 'Mushroom Masala',
        'price'       => '7.95',
        'desc'        => 'Mushrooms and green peas cooked with fresh coconut and spices.',
        'short_desc'  => 'Mushrooms and green peas cooked with fresh coconut and spices.',
        'category'    => 'vegetarian-dishes',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'veg',
    ),
    array(
        'title'       => 'Chilli Paneer (Main)',
        'price'       => '10.95',
        'desc'        => 'Cottage cheese cooked with chilli, garlic, mixed peppers and tomato sauce.',
        'short_desc'  => 'Cottage cheese cooked with chilli, garlic, mixed peppers and tomato sauce.',
        'category'    => 'vegetarian-dishes',
        'tags'        => array('Veg', 'Spicy'),
        'image_key'   => 'veg',
    ),
    array(
        'title'       => 'Mutter Paneer',
        'price'       => '9.95',
        'desc'        => 'Green peas and cottage cheese cooked with South Indian spices.',
        'short_desc'  => 'Green peas and cottage cheese cooked with South Indian spices.',
        'category'    => 'vegetarian-dishes',
        'tags'        => array('Veg'),
        'image_key'   => 'veg',
    ),
    array(
        'title'       => 'Koottu Parippu Curry (Mixed Lentil)',
        'price'       => '7.95',
        'desc'        => 'Mixed toor, masoor and mung lentils tempered with curry leaves, garlic, red onion and mustard seeds.',
        'short_desc'  => 'Mixed toor, masoor and mung lentils tempered with curry leaves, garlic, red onion and mustard seeds.',
        'category'    => 'vegetarian-dishes',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'veg',
    ),

    // =========================================================================
    // 11. SIDE ORDERS
    // =========================================================================
    array(
        'title'       => 'Beans Coconut Thoran',
        'price'       => '5.25',
        'desc'        => 'Fine chopped green beans stir-fried with grated coconut, mustard seeds and curry leaves.',
        'short_desc'  => 'Fine chopped green beans stir-fried with grated coconut, mustard seeds and curry leaves.',
        'category'    => 'side-orders',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'bread',
    ),
    array(
        'title'       => 'Spicy Potato',
        'price'       => '5.25',
        'desc'        => 'Potatoes tossed in aromatic South Indian spices and curry leaves.',
        'short_desc'  => 'Potatoes tossed in aromatic South Indian spices and curry leaves.',
        'category'    => 'side-orders',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'bread',
    ),
    array(
        'title'       => 'Cabbage Thoran',
        'price'       => '5.25',
        'desc'        => 'Shredded cabbage sautéed with grated coconut, turmeric and mustard seeds.',
        'short_desc'  => 'Shredded cabbage sautéed with grated coconut, turmeric and mustard seeds.',
        'category'    => 'side-orders',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'bread',
    ),

    // =========================================================================
    // 12. RICE
    // =========================================================================
    array(
        'title'       => 'Plain Rice',
        'price'       => '2.75',
        'desc'        => 'Steamed fluffy white rice.',
        'short_desc'  => 'Steamed fluffy white rice.',
        'category'    => 'rice',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'rice',
    ),
    array(
        'title'       => 'Coconut Rice',
        'price'       => '3.25',
        'desc'        => 'Fragrant basmati rice infused with coconut milk and seasoned with curry leaves.',
        'short_desc'  => 'Fragrant basmati rice infused with coconut milk and seasoned with curry leaves.',
        'category'    => 'rice',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'rice',
    ),
    array(
        'title'       => 'Lemon Rice',
        'price'       => '3.25',
        'desc'        => 'Tangy basmati rice tempered with mustard seeds, curry leaves and fresh lemon juice.',
        'short_desc'  => 'Tangy basmati rice tempered with mustard seeds, curry leaves and fresh lemon juice.',
        'category'    => 'rice',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'rice',
    ),
    array(
        'title'       => 'Pilau Rice',
        'price'       => '3.25',
        'desc'        => 'Aromatic basmati rice cooked with whole spices and saffron.',
        'short_desc'  => 'Aromatic basmati rice cooked with whole spices and saffron.',
        'category'    => 'rice',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'rice',
    ),

    // =========================================================================
    // 13. BREAD
    // =========================================================================
    array(
        'title'       => 'Paratha',
        'price'       => '2.45',
        'desc'        => 'Traditional multi-layered flaky Kerala flatbread.',
        'short_desc'  => 'Traditional multi-layered flaky Kerala flatbread.',
        'category'    => 'bread',
        'tags'        => array('Veg'),
        'image_key'   => 'bread',
    ),
    array(
        'title'       => 'Poori',
        'price'       => '2.95',
        'desc'        => 'Puffed golden wholewheat bread, deep fried.',
        'short_desc'  => 'Puffed golden wholewheat bread, deep fried.',
        'category'    => 'bread',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'bread',
    ),
    array(
        'title'       => 'Kallappam',
        'price'       => '2.95',
        'desc'        => 'Soft fermented rice pancake with a fluffy centre.',
        'short_desc'  => 'Soft fermented rice pancake with a fluffy centre.',
        'category'    => 'bread',
        'tags'        => array('Veg', 'Vegan', 'Gluten Free'),
        'image_key'   => 'bread',
    ),
    array(
        'title'       => 'Chapatti',
        'price'       => '2.95',
        'desc'        => 'Traditional unleavened South Indian wholewheat flatbread.',
        'short_desc'  => 'Traditional unleavened South Indian wholewheat flatbread.',
        'category'    => 'bread',
        'tags'        => array('Veg', 'Vegan'),
        'image_key'   => 'bread',
    ),

    // =========================================================================
    // 14. CHILDREN'S MENU
    // =========================================================================
    array(
        'title'       => 'Chicken Nuggets with Chips',
        'price'       => '5.95',
        'desc'        => 'Crispy chicken nuggets served with golden chips.',
        'short_desc'  => 'Crispy chicken nuggets served with golden chips.',
        'category'    => 'childrens-menu',
        'tags'        => array('Non-Veg', 'Kids'),
        'image_key'   => 'chick',
    ),
    array(
        'title'       => 'Calamari Fry with Chips',
        'price'       => '7.95',
        'desc'        => 'Mildly spiced calamari strips served with golden chips.',
        'short_desc'  => 'Mildly spiced calamari strips served with golden chips.',
        'category'    => 'childrens-menu',
        'tags'        => array('Seafood', 'Kids'),
        'image_key'   => 'fish',
    ),
    array(
        'title'       => 'Onion Rings with Chips',
        'price'       => '4.50',
        'desc'        => 'Crispy onion rings served with golden chips.',
        'short_desc'  => 'Crispy onion rings served with golden chips.',
        'category'    => 'childrens-menu',
        'tags'        => array('Veg', 'Vegan', 'Kids'),
        'image_key'   => 'veg',
    ),
);

echo "\n=== Creating New Products ===\n";
$created_count = 0;

foreach ($menu_items as $item) {
    $post_data = array(
        'post_title'   => $item['title'],
        'post_content' => $item['desc'],
        'post_excerpt' => $item['short_desc'],
        'post_status'  => 'publish',
        'post_type'    => 'product',
    );

    $pid = wp_insert_post($post_data);

    if (is_wp_error($pid) || !$pid) {
        echo "Error creating product: {$item['title']}\n";
        continue;
    }

    // Set Product Type
    wp_set_object_terms($pid, 'simple', 'product_type');

    // Set Product Category
    if (!empty($cat_ids[$item['category']])) {
        wp_set_object_terms($pid, (int)$cat_ids[$item['category']], 'product_cat');
    }

    // Set Product Tags
    if (!empty($item['tags'])) {
        wp_set_object_terms($pid, $item['tags'], 'product_tag');
        update_post_meta($pid, '_the_cochin_dietary', implode(', ', $item['tags']));
    }

    // Set WooCommerce Meta
    $price = (string)$item['price'];
    update_post_meta($pid, '_price', $price);
    update_post_meta($pid, '_regular_price', $price);
    update_post_meta($pid, '_visibility', 'visible');
    update_post_meta($pid, '_stock_status', 'instock');
    update_post_meta($pid, '_manage_stock', 'no');
    update_post_meta($pid, 'total_sales', '0');

    // Attach image if available
    if (!empty($item['image_key']) && !empty($img_map[$item['image_key']])) {
        set_post_thumbnail($pid, $img_map[$item['image_key']]);
    }

    $created_count++;
    echo "[OK] #{$pid} - {$item['title']} (£{$price}) -> Category: {$item['category']}\n";
}

// Clear transients
if (function_exists('wc_delete_product_transients')) {
    delete_transient('wc_products_onsale');
    delete_transient('wc_featured_products');
}

echo "\nSuccessfully created {$created_count} products matching exact PDF sections!\n";
