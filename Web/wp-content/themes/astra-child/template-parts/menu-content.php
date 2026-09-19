<?php
/**
 * The Cochin - Menu Page Content Layout
 *
 * Implements:
 * - Introduction Banner
 * - Dietary & Allergen Notice
 * - Sticky Quick Navigation for 9 Modified Categories:
 *   1. Starters
 *   2. Dosa & South Indian Classics
 *   3. Seafood
 *   4. Meat & Poultry
 *   5. Vegetarian & Vegan
 *   6. Rice & Biryani
 *   7. Breads & Sides
 *   8. Desserts
 *   9. Drinks
 * - Interactive Search and Dietary Filter (All / Veg Only)
 * - 100% Full-Width Dishes List with Add to Cart and Poppins Typography
 */

if (!defined('ABSPATH')) {
    exit;
}

// 9 Refined Menu Categories
$categories = array(
    array(
        'id'        => 'starters',
        'title'     => 'STARTERS',
        'subhead'   => 'Crispy Bites, Street Appetizers & Tea Shop Snacks',
        'intro'     => 'A vibrant medley of traditional South Indian street food delicacies, crunchy fritters, and coastal appetizers seasoned with fresh curry leaves, mustard seeds, and homemade spices.',
        'items'     => array(
            array(
                'title' => 'Keralan Tea Shop Snacks',
                'price' => '£6.95',
                'tags'  => array('Veg', 'Vegan'),
                'desc'  => 'Authentic snack tray with light & crispy Kerala pappadoms, homemade Murukku, Pappadavada, and crispy Plantain Banana Chips. Accompanied by homemade mango and lemon pickles.',
            ),
            array(
                'title' => 'Lentil Soup Starter',
                'price' => '£4.95',
                'tags'  => array('Veg', 'Vegan', 'Gluten-Free'),
                'desc'  => 'Thick wholesome soup prepared with yellow lentils, shallots, crushed ginger, garlic & tempered curry leaves.',
            ),
            array(
                'title' => 'Aubergine Fry Starter',
                'price' => '£4.95',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Finely sliced aubergines delicately coated in chef\'s special spiced batter and crisp-fried.',
            ),
            array(
                'title' => 'Uzhunu Vada',
                'price' => '£5.25',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Golden savoury lentil doughnuts crafted with traditional South Indian spices, fresh ginger, and curry leaves. Served with sambar.',
            ),
            array(
                'title' => 'Chilly Paneer Starter',
                'price' => '£6.95',
                'tags'  => array('Veg'),
                'desc'  => 'Fresh cottage cheese tossed in a rich, savoury glaze of chopped garlic, crunchy green peppers and tangy-sweet chilli sauce.',
            ),
            array(
                'title' => 'Meen Fry (Fish Fry)',
                'price' => '£6.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'Thin fillets of tilapia fish marinated in freshly pounded ginger-garlic, turmeric, crushed chilli, lemon juice and aromatic curry leaves, then griddled to perfection.',
            ),
            array(
                'title' => 'Calamari Rings Starter',
                'price' => '£6.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'Tender squid rings marinated with turmeric, lemon juice, and coastal spices, coated in light chickpea batter and golden fried.',
            ),
            array(
                'title' => 'Onion Bhaji',
                'price' => '£4.95',
                'tags'  => array('Veg', 'Vegan', 'Gluten-Free'),
                'desc'  => 'Classic crispy onion fritters prepared with sliced onions and fragrant gram flour batter, fried to golden perfection.',
            ),
            array(
                'title' => 'Alleppey Prawn Fry',
                'price' => '£7.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'Alleppey backwaters speciality: King prawns marinated in vibrant spices, coated in spiced corn flour and egg batter, and pan-fried with roasted spices.',
            ),
            array(
                'title' => 'Vegetable Samosa',
                'price' => '£4.15',
                'tags'  => array('Veg'),
                'desc'  => 'Golden handcrafted pastry parcels filled with delicately spiced potatoes, sweet green peas, and garden vegetables.',
            ),
            array(
                'title' => 'Potato Bonda',
                'price' => '£5.25',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Mashed potatoes spiced with ginger, curry leaves, fresh coriander and tempered with black mustard seeds, fried in chickpea batter and served with coconut chutney.',
            ),
            array(
                'title' => 'Chicken Samosa',
                'price' => '£4.95',
                'tags'  => array(),
                'desc'  => 'Crisp golden pastry filled with a savoury mince of seasoned chicken, fine vegetables, and aromatic spices.',
            ),
            array(
                'title' => 'Lamb Samosa',
                'price' => '£4.95',
                'tags'  => array(),
                'desc'  => 'Flaky golden pastry parcels filled with rich spiced minced lamb, onions, herbs, and potatoes.',
            ),
            array(
                'title' => 'Lamb Dry Roast',
                'price' => '£6.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'Tender cubes of succulent lamb slow-cooked with turmeric, sautéed with ginger, garlic, curry leaves and finished with freshly crushed black pepper.',
            ),
            array(
                'title' => 'Prawn Poori',
                'price' => '£5.75',
                'tags'  => array(),
                'desc'  => 'Succulent prawns simmered in a tangy, aromatic masala sauce served over warm, fluffy fried poori bread.',
            ),
            array(
                'title' => 'Mushroom Fry',
                'price' => '£4.95',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Fresh whole button mushrooms dipped in chef\'s seasoned batter and crispy fried with fragrant herbs.',
            ),
            array(
                'title' => 'Chicken Ularthiyathu',
                'price' => '£5.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'Tender chicken pieces simmered in turmeric water, then stir-fried with black pepper powder, curry leaves and toasted coconut slivers.',
            ),
        ),
    ),
    array(
        'id'        => 'dosa-classics',
        'title'     => 'DOSA & SOUTH INDIAN CLASSICS',
        'subhead'   => 'Traditional Lentil & Rice Crepes',
        'intro'     => 'Dosa is a renowned South Indian culinary art: a thin, golden crepe crafted from a naturally fermented batter of rice and black lentils, cooked crisp on a flat griddle. Served piping hot with aromatic lentil sambar and fresh coconut chutneys.',
        'items'     => array(
            array(
                'title' => 'Masala Dosa',
                'price' => '£9.95',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'The legendary golden crispy crepe made of lentil & rice batter, filled with spiced mashed potato masala and served with traditional sambar and homemade chutneys.',
            ),
            array(
                'title' => 'Plain Dosa',
                'price' => '£8.50',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'A large, wafer-thin golden crispy crepe prepared from naturally fermented rice and lentil batter. Served with sambar and coconut chutneys.',
            ),
            array(
                'title' => 'Nair Masala Dosa',
                'price' => '£9.95',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Classic rice & lentil pancake generously spread with our spicy red coconut chutney and stuffed with seasoned potato masala.',
            ),
            array(
                'title' => 'Mini Vegetarian Dosa',
                'price' => '£4.95',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Crisp mini lentil and rice crepe filled with savoury spiced potatoes, mustard seeds, and curry leaves.',
            ),
            array(
                'title' => 'Mini Chicken Dosa',
                'price' => '£5.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'Crispy mini rice and lentil pancake filled with spiced chicken pieces, potatoes, and Southern herbs.',
            ),
            array(
                'title' => 'Mini Lamb Dosa',
                'price' => '£5.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'Delicate lentil and rice pancake stuffed with tender lamb cubes, spiced potatoes and fragrant tempered curry leaves.',
            ),
            array(
                'title' => 'Ghee Roast Dosa',
                'price' => '£9.50',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Extra-crisp golden dosa roasted with clarified pure butter (desi ghee), releasing an irresistible aroma. Served with sambar and chutneys.',
            ),
            array(
                'title' => 'Onion Dosa',
                'price' => '£9.25',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Crispy fermented rice pancake studded with finely chopped red onions, green chillies, and fresh coriander leaves.',
            ),
        ),
    ),
    array(
        'id'        => 'seafood',
        'title'     => 'SEAFOOD',
        'subhead'   => 'Coastal Fish & Backwater Prawns',
        'intro'     => 'Kerala\'s 600km Arabian Sea coastline inspired these celebrated fish and seafood specialties. Cooked in traditional clay pots with creamy coconut milk, kudampuli (Malabar kokum), raw mango, and fresh curry leaves.',
        'items'     => array(
            array(
                'title' => 'Cochin King Fish Curry',
                'price' => '£12.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'A signature coastal preparation: Steaks of King Fish marinated in chilli powder, turmeric, and lemon juice, then simmered in a luscious coconut milk gravy.',
            ),
            array(
                'title' => 'Fish in Banana Leaf (Meen Pollichathu)',
                'price' => '£14.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'Boneless tilapia fillet marinated in shallots, ginger, garlic and crushed pepper, wrapped delicately in fresh banana leaf and pan-grilled with special spiced masala sauce.',
            ),
            array(
                'title' => 'Tiger Prawn Masala',
                'price' => '£14.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'Plump tiger prawns cooked in a mouth-watering rich sauce of coconut milk, ginger, garlic, garden green peas, and ground South Indian spices.',
            ),
            array(
                'title' => 'Mix Seafood Curry',
                'price' => '£14.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'An ocean feast of King Fish, tender squid rings, juicy prawns and mussels gently simmered in our authentic Kerala spice sauce.',
            ),
            array(
                'title' => 'Cochin King Prawn Curry',
                'price' => '£14.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'Chef\'s speciality: Succulent king prawns cooked in a traditional seaside sauce popular along the historic docks of Fort Cochin.',
            ),
            array(
                'title' => 'Prawn Jalfrezi',
                'price' => '£14.95',
                'tags'  => array(),
                'desc'  => 'Vibrant stir-fried prawns tossed with crunchy bell peppers, onions, fresh green chillies and bold roasted Indian spices.',
            ),
            array(
                'title' => 'Prawn Madras',
                'price' => '£14.95',
                'tags'  => array(),
                'desc'  => 'Fiery Southern style curry featuring tender prawns slow-cooked in a rich, deeply spiced tomato and chilli gravy.',
            ),
        ),
    ),
    array(
        'id'        => 'meat-poultry',
        'title'     => 'MEAT & POULTRY',
        'subhead'   => 'Traditional Keralan Curries & Roasts',
        'intro'     => 'Authentic meat recipes passed down through generations in Central and Southern Kerala. Flavoured with whole roasted spices, shallots, garlic, black pepper, and silky coconut gravies.',
        'items'     => array(
            array(
                'title' => 'Cochin Lamb Curry',
                'price' => '£10.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'Tender lamb simmered in a tomato base with black pepper, ground coriander, garlic, and fresh curry leaves, enriched with silky coconut milk.',
            ),
            array(
                'title' => 'Lamb and Spinach Curry (Saag Gosht)',
                'price' => '£10.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'Succulent lamb cooked with fresh English spinach leaves, onion, coriander, ginger and finished with delicate rich cashew paste.',
            ),
            array(
                'title' => 'Lamb Ularthiyathu',
                'price' => '£11.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'Celebrated Kerala toddy shop speciality: Lamb slow-cooked in turmeric and stir-fried with shallots, crushed ginger, curry leaves, and crunchy coconut slivers.',
            ),
            array(
                'title' => 'Nadan Chicken Curry (Varutharacha)',
                'price' => '£10.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'Classic rural Kerala curry made with dry-roasted whole coriander, fennel, dried chillies, and grated coconut paste.',
            ),
            array(
                'title' => 'Thattukada Chicken',
                'price' => '£10.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'Street-style spiced chicken tossed with black pepper powder, green chillies, curry leaves, and toasted coconut.',
            ),
            array(
                'title' => 'Beef Roast',
                'price' => '£12.99',
                'tags'  => array(),
                'desc'  => 'Succulent beef chunks braised in a roasted masala of crushed ginger, garlic, fennel, cardamom, star anise and caramelised onions. Perfect pairing with Kerala Paratha.',
            ),
            array(
                'title' => 'Garlic Chicken Curry',
                'price' => '£10.95',
                'tags'  => array(),
                'desc'  => 'Deliciously aromatic fresh garlic chicken simmered in an onion-tomato gravy scented with royal garam masala.',
            ),
            array(
                'title' => 'Garlic Lamb Curry',
                'price' => '£10.95',
                'tags'  => array(),
                'desc'  => 'Tender lamb chunks cooked with loads of fresh roasted garlic, onion masala, and warm Southern spices.',
            ),
            array(
                'title' => 'Naranga Chicken Curry (Lemon Chicken)',
                'price' => '£10.95',
                'tags'  => array(),
                'desc'  => 'Tender chicken cooked with fresh lemon slices, curry leaves, ginger, garlic, Kashmiri chilli powder and a hint of white vinegar for a vibrant tangy bite.',
            ),
            array(
                'title' => 'Chicken Korma',
                'price' => '£10.95',
                'tags'  => array(),
                'desc'  => 'Mild, velvety chicken curry cooked in an aromatic onion, ginger and cashew nut sauce with delicate cardamom notes.',
            ),
            array(
                'title' => 'Lamb Korma',
                'price' => '£10.95',
                'tags'  => array(),
                'desc'  => 'Tender cubes of lamb slowly cooked in a rich, creamy sauce of onions, spices, and ground cashews.',
            ),
            array(
                'title' => 'Lemony Lamb',
                'price' => '£10.95',
                'tags'  => array(),
                'desc'  => 'Tender diced lamb simmered with citrus lemon slices, fresh curry leaves, and Kashmiri chilli in a mildly tangy reduction.',
            ),
            array(
                'title' => 'Chicken Jalfrezi',
                'price' => '£10.95',
                'tags'  => array(),
                'desc'  => 'Spicy stir-fried chicken tossed with crunchy bell peppers, onions, tomatoes and fresh aromatic spices.',
            ),
            array(
                'title' => 'Chicken Madras',
                'price' => '£10.95',
                'tags'  => array(),
                'desc'  => 'Fiery South Indian curry with chicken simmered in a bold, rich red chilli and tomato gravy.',
            ),
            array(
                'title' => 'Lamb Jalfrezi',
                'price' => '£10.95',
                'tags'  => array(),
                'desc'  => 'Tender lamb chunks sautéed with sweet peppers, onions, green chillies and freshly ground spices.',
            ),
            array(
                'title' => 'Lamb Madras',
                'price' => '£10.95',
                'tags'  => array(),
                'desc'  => 'Classic fiery Southern curry with succulent lamb slow-simmered in rich spicy tomato and mustard sauce.',
            ),
            array(
                'title' => 'Lamb Rogan Josh',
                'price' => '£10.95',
                'tags'  => array(),
                'desc'  => 'Aromatic curry of tender lamb slow-braised with Kashmiri chillies, ripe tomatoes, and warm warming spices.',
            ),
        ),
    ),
    array(
        'id'        => 'vegetarian-vegan',
        'title'     => 'VEGETARIAN & VEGAN',
        'subhead'   => 'Lentils, Fresh Paneer & Plant-Based Choices',
        'intro'     => 'Kerala\'s vegetarian tradition is renowned for wholesome, vibrant flavours. Featuring freshly roasted spices, freshly grated coconut, hearty lentils, spinach, and artisanal Indian cottage cheese.',
        'items'     => array(
            array(
                'title' => 'Aubergine Curry (Brinjal Masala)',
                'price' => '£7.95',
                'tags'  => array('Veg', 'Vegan', 'Gluten-Free'),
                'desc'  => 'Tender aubergine quarters simmered in a roasted coriander seed and onion gravy, finished with creamy coconut milk and smooth cashew nut paste.',
            ),
            array(
                'title' => 'Dal and Spinach Curry',
                'price' => '£7.95',
                'tags'  => array('Veg', 'Vegan', 'Gluten-Free'),
                'desc'  => 'Nutritious yellow lentils and fresh spinach leaves slow-simmered and tempered with cumin, garlic and curry leaves.',
            ),
            array(
                'title' => 'Okra Masala (Bhindi Masala)',
                'price' => '£7.95',
                'tags'  => array('Veg', 'Vegan', 'Gluten-Free'),
                'desc'  => 'Tender okra sautéed with crunchy carrots, toasted cashew nuts, dry-roasted coconut, mustard seeds and curry leaves.',
            ),
            array(
                'title' => 'Veg Korma',
                'price' => '£7.95',
                'tags'  => array('Veg', 'Vegan'),
                'desc'  => 'Medley of fresh carrots, potatoes, green peas and French beans cooked in a mildly spiced onion, coconut, and cashew sauce.',
            ),
            array(
                'title' => 'Palak Paneer',
                'price' => '£9.95',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Cubes of soft cottage cheese simmered in a pureed fresh spinach gravy, infused with garlic, cumin, and mild spices.',
            ),
            array(
                'title' => 'Mushroom Masala',
                'price' => '£7.95',
                'tags'  => array('Veg', 'Vegan', 'Gluten-Free'),
                'desc'  => 'Sliced button mushrooms and sweet green peas cooked in a fragrant spiced gravy made from freshly grated coconut and ground spices.',
            ),
            array(
                'title' => 'Chilly Paneer (Mains)',
                'price' => '£10.95',
                'tags'  => array('Veg'),
                'desc'  => 'Generous portion of cottage cheese cubes tossed in sweet chilli sauce, chopped garlic, and crunchy bell peppers.',
            ),
            array(
                'title' => 'Mutter Paneer',
                'price' => '£9.95',
                'tags'  => array('Veg'),
                'desc'  => 'Fresh cottage cheese and tender green peas cooked in our signature South Indian tomato-cashew curry.',
            ),
            array(
                'title' => 'Koottu Parippu Curry',
                'price' => '£7.95',
                'tags'  => array('Veg', 'Vegan', 'Gluten-Free'),
                'desc'  => 'Traditional feast-style mixed lentil curry prepared with Toor and Masoor dal, curry leaves, garlic, and tempered with red onions and black mustard seeds.',
            ),
        ),
    ),
    array(
        'id'        => 'rice-biryani',
        'title'     => 'RICE & BIRYANI',
        'subhead'   => 'Fragrant Basmati & Malabar Dum Biryani',
        'intro'     => 'A staple of South Indian dining, our rices and biryanis are prepared with long-grain aged basmati, infused with saffron, citrus, pure ghee, and whole Malabar pot spices.',
        'items'     => array(
            array(
                'title' => 'Chicken Biriyani',
                'price' => '£12.95',
                'tags'  => array('Gluten-Free'),
                'desc'  => 'Aromatic Keralan dum biryani layered with spiced chicken, fragrant long-grain basmati, saffron, toasted cashew nuts, and golden sultanas.',
            ),
            array(
                'title' => 'Lamb Biriyani',
                'price' => '£12.95',
                'tags'  => array(),
                'desc'  => 'Tender marinated lamb cubes cooked in Kerala pot spices, layered with fragrant basmati, fried onions, cashews, and sultanas.',
            ),
            array(
                'title' => 'Prawn Biriyani',
                'price' => '£15.95',
                'tags'  => array(),
                'desc'  => 'Juicy king prawns cooked in spiced masala and layered with saffron-infused basmati rice, garnished with toasted nuts.',
            ),
            array(
                'title' => 'Vegetable Biriyani',
                'price' => '£10.95',
                'tags'  => array('Veg', 'Vegan', 'Gluten-Free'),
                'desc'  => 'Garden-fresh vegetables slow-cooked with basmati rice, mint, coriander, toasted cashews and authentic Malabar spices.',
            ),
            array(
                'title' => 'Plain Basmati Rice',
                'price' => '£2.75',
                'tags'  => array('Veg', 'Vegan', 'Gluten-Free'),
                'desc'  => 'Fluffy, long-grain steamed premium white basmati rice.',
            ),
            array(
                'title' => 'Coconut Rice',
                'price' => '£3.25',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Aromatic basmati tossed with fresh grated coconut, urid dal, curry leaves and mustard seeds. Exceptional with curries.',
            ),
            array(
                'title' => 'Lemon Rice',
                'price' => '£3.25',
                'tags'  => array('Veg', 'Vegan', 'Gluten-Free'),
                'desc'  => 'Tangy, uplifting rice tossed with fresh lemon juice, turmeric, curry leaves, and crackled mustard seeds.',
            ),
            array(
                'title' => 'Pulav Rice',
                'price' => '£3.25',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Fragrant basmati cooked with saffron, whole cardamom, cloves, and cinnamon.',
            ),
            array(
                'title' => 'Tamarind Rice',
                'price' => '£3.25',
                'tags'  => array('Veg', 'Vegan', 'Gluten-Free'),
                'desc'  => 'Basmati rice tossed in tangy tamarind pulp, roasted peanuts, dry red chillies, and South Indian seasonings.',
            ),
        ),
    ),
    array(
        'id'        => 'breads-sides',
        'title'     => 'BREADS & SIDES',
        'subhead'   => 'Flaky Parathas, Appams & Thorans',
        'intro'     => 'Handcrafted breads prepared fresh upon ordering, paired with traditional dry-tempered vegetable Thorans tossed with fresh grated coconut and green chillies.',
        'items'     => array(
            array(
                'title' => 'Kerala Paratha (1 Pc)',
                'price' => '£2.45',
                'tags'  => array('Veg'),
                'desc'  => 'Famous multi-layered, flaky, buttery flatbread tossed and griddled to golden crisp perfection.',
            ),
            array(
                'title' => 'Kallappam (2 Pcs)',
                'price' => '£2.95',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Soft, fermented rice and coconut pancakes flavoured with ground shallots and cumin seeds.',
            ),
            array(
                'title' => 'Poori (2 Pcs)',
                'price' => '£2.95',
                'tags'  => array('Veg'),
                'desc'  => 'Deep-fried golden, puffed, and fluffy bread made of whole wheat flour.',
            ),
            array(
                'title' => 'Chapatti (2 Pcs)',
                'price' => '£2.95',
                'tags'  => array('Veg', 'Vegan'),
                'desc'  => 'Traditional healthy unleavened flatbreads made from whole wheat flour and dry-roasted on the tava.',
            ),
            array(
                'title' => 'Beans Coconut Thoran',
                'price' => '£5.25',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Crisp green beans finely chopped and sautéed with shallots, fresh grated coconut, green chillies, and mustard seed tempering.',
            ),
            array(
                'title' => 'Spicy Potato',
                'price' => '£5.25',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Tender boiled potato cubes tossed in our zesty South Indian masala with crunchy bell peppers and fresh herbs.',
            ),
            array(
                'title' => 'Cabbage Thoran',
                'price' => '£5.25',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Finely shredded crisp cabbage sautéed with shallots, green chillies, curry leaves, and freshly grated coconut.',
            ),
        ),
    ),
    array(
        'id'        => 'desserts',
        'title'     => 'DESSERTS',
        'subhead'   => 'Traditional Sweets & Ice Creams',
        'intro'     => 'Indulgent South Indian desserts and chilled delicacies crafted with slow-simmered milk, aromatic cardamom, jaggery, and premium Alphonso mangoes.',
        'items'     => array(
            array(
                'title' => 'Payasam (Keralan Dessert Pudding)',
                'price' => '£4.50',
                'tags'  => array('Veg'),
                'desc'  => 'Traditional sweet dessert made of roasted vermicelli slow-simmered in sweetened milk, infused with green cardamom, golden raisins, and roasted cashew nuts.',
            ),
            array(
                'title' => 'Gulab Jamun with Ice Cream',
                'price' => '£4.95',
                'tags'  => array('Veg'),
                'desc'  => 'Warm, melt-in-mouth milk dumplings soaked in scented rose-cardamom sugar syrup, served alongside a scoop of rich vanilla ice cream.',
            ),
            array(
                'title' => 'Mango Kulfi',
                'price' => '£4.25',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Traditional dense Indian ice cream prepared from slow-reduced milk infused with sweet Alphonso mango pulp and garnished with crushed pistachios.',
            ),
            array(
                'title' => 'Coconut Kulfi',
                'price' => '£4.25',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Artisanal frozen dessert made with rich dairy cream, freshly grated roasted coconut, cardamom, and toasted almond slivers.',
            ),
            array(
                'title' => 'Selection of Ice Creams',
                'price' => '£3.95',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Two generous scoops of your choice: Madagascan Vanilla, Belgian Chocolate, or Strawberries & Cream.',
            ),
        ),
    ),
    array(
        'id'        => 'drinks',
        'title'     => 'DRINKS',
        'subhead'   => 'Keralan Lassi, Fresh Juices & Hot Beverages',
        'intro'     => 'Quench your thirst with traditional chilled yogurt lassis, authentic South Indian filter coffee, aromatic spiced masala chai, and refreshing beverages.',
        'items'     => array(
            array(
                'title' => 'Mango Lassi',
                'price' => '£3.95',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Our famous chilled blended yogurt smoothie prepared with premium Alphonso mango pulp, a dash of cardamom, and crushed ice.',
            ),
            array(
                'title' => 'Sweet Lassi',
                'price' => '£3.50',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Classic refreshing churned sweet yogurt drink, lightly flavoured with rose water and cardamom.',
            ),
            array(
                'title' => 'Salted Lassi',
                'price' => '£3.50',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Cooling digestive yogurt drink delicately seasoned with roasted cumin powder, fresh ginger, and rock salt.',
            ),
            array(
                'title' => 'South Indian Filter Coffee',
                'price' => '£2.95',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Authentic stainless steel drip-filtered dark roast coffee blended with chicory, frothed with hot creamy milk.',
            ),
            array(
                'title' => 'Masala Chai',
                'price' => '£2.75',
                'tags'  => array('Veg', 'Gluten-Free'),
                'desc'  => 'Spiced milk tea slow-brewed with fresh ginger, cardamom pods, cinnamon, cloves, and whole black tea leaves.',
            ),
            array(
                'title' => 'Fresh Lime Soda (Sweet / Salted)',
                'price' => '£3.25',
                'tags'  => array('Veg', 'Vegan', 'Gluten-Free'),
                'desc'  => 'Freshly hand-squeezed lime juice topped with chilled sparkling club soda, served sweet, salted, or mixed.',
            ),
            array(
                'title' => 'Soft Drinks',
                'price' => '£2.75',
                'tags'  => array('Veg', 'Vegan', 'Gluten-Free'),
                'desc'  => 'Coca Cola, Diet Coke, Sprite, or Fanta (330ml chilled bottle/can).',
            ),
            array(
                'title' => 'Mineral Water (750ml)',
                'price' => '£2.50',
                'tags'  => array('Veg', 'Vegan', 'Gluten-Free'),
                'desc'  => 'Still or Sparkling premium bottled mineral water.',
            ),
        ),
    ),
);
?>

<div class="cochin-menu-layout">

    <!-- =========================================================================
         TOP SECTION: Introduction Banner & Dietary / Allergen Notice Card
         ========================================================================= -->
    <div class="menu-intro-allergen-section">
        <div class="menu-container">
            
            <!-- Introduction Box -->
            <div class="menu-intro-card">
                <div class="menu-intro-badge">DISCOVER OUR FOOD</div>
                <h1 class="menu-intro-heading">Explore the Flavours of Kerala &amp; South India</h1>
                <p class="menu-intro-text">
                    Explore the flavours of Kerala and South India. Our menu includes starters, dosas, seafood, meat and poultry dishes, vegetarian and vegan choices, rice dishes, breads, desserts and drinks.
                </p>
            </div>

            <!-- Dietary & Allergen Notice Card -->
            <div class="menu-allergen-card" role="note" aria-label="Dietary and allergen notice">
                <div class="menu-allergen-icon-wrap" aria-hidden="true">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <div class="menu-allergen-content">
                    <h2 class="menu-allergen-title">Dietary and Allergen Notice</h2>
                    <p class="menu-allergen-desc">
                        Please tell a member of our team about any allergy or dietary requirement before ordering. Our dishes are prepared in a kitchen where allergens are handled, and cross-contact may occur. Please ask for our current allergen information.
                    </p>
                </div>
            </div>

        </div>
    </div>

    <!-- =========================================================================
         STICKY BAR: 9 Quick Navigation Category Pills & Search / Filter Controls
         ========================================================================= -->
    <div class="menu-sticky-nav-bar">
        <div class="menu-container">
            <div class="menu-nav-scroller">
                <nav class="menu-category-pills" aria-label="Menu Categories">
                    <a href="#starters" class="menu-pill active">Starters</a>
                    <a href="#dosa-classics" class="menu-pill">Dosa &amp; South Indian Classics</a>
                    <a href="#seafood" class="menu-pill">Seafood</a>
                    <a href="#meat-poultry" class="menu-pill">Meat &amp; Poultry</a>
                    <a href="#vegetarian-vegan" class="menu-pill">Vegetarian &amp; Vegan</a>
                    <a href="#rice-biryani" class="menu-pill">Rice &amp; Biryani</a>
                    <a href="#breads-sides" class="menu-pill">Breads &amp; Sides</a>
                    <a href="#desserts" class="menu-pill">Desserts</a>
                    <a href="#drinks" class="menu-pill">Drinks</a>
                </nav>
            </div>

            <div class="menu-controls-bar">
                <!-- Search without remove button -->
                <div class="menu-search-wrap">
                    <input type="text" id="menuSearchInput" class="menu-search-input" placeholder="Search dishes..." autocomplete="off">
                    <svg class="menu-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>

                <!-- Veg Filter Toggle -->
                <label class="menu-veg-filter-label" for="vegFilterCheckbox">
                    <input type="checkbox" id="vegFilterCheckbox" class="menu-veg-checkbox">
                    <span class="menu-veg-slider"></span>
                    <span class="menu-veg-text">🌱 Veg Only</span>
                </label>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SPECIAL OFFER BANNER
         ========================================================================= -->
    <div class="menu-container">
        <div class="menu-offer-banner">
            <div class="menu-offer-badge">SPECIAL OFFER</div>
            <div class="menu-offer-text">
                <strong>15% Off</strong> on all collection orders over £25 when ordered online.
            </div>
            <a href="<?php echo esc_url(home_url('/#order')); ?>" class="menu-offer-btn">ORDER TAKEAWAY</a>
        </div>
    </div>

    <!-- =========================================================================
         MENU CATEGORIES (MATCHING USER REFERENCE + 100% SINGLE COLUMN DISHES)
         ========================================================================= -->
    <div class="menu-categories-wrapper">
        <?php foreach ($categories as $cat) : ?>
            <section class="menu-category-section" id="<?php echo esc_attr($cat['id']); ?>" data-category="<?php echo esc_attr($cat['id']); ?>">
                <div class="menu-container">
                    
                    <!-- CATEGORY HEADER (100% Full Width) -->
                    <div class="menu-section-header-fullwidth">
                        <h2 class="menu-editorial-title"><?php echo esc_html($cat['title']); ?></h2>
                        <?php if (!empty($cat['subhead'])) : ?>
                            <span class="menu-category-subhead"><?php echo esc_html($cat['subhead']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($cat['intro'])) : ?>
                            <p class="menu-category-intro"><?php echo esc_html($cat['intro']); ?></p>
                        <?php endif; ?>
                        <div class="menu-header-divider-line" aria-hidden="true"></div>
                    </div>

                    <!-- 100% FULL-WIDTH SINGLE COLUMN FOOD ITEMS LIST -->
                    <div class="menu-dishes-container-fullwidth">
                        <div class="menu-dishes-list-singlecol">
                            <?php foreach ($cat['items'] as $item) : 
                                $is_veg = in_array('Veg', $item['tags']) || in_array('Vegan', $item['tags']);
                                $product_obj = get_page_by_title($item['title'], OBJECT, 'product');
                                $product_id = $product_obj ? $product_obj->ID : 0;
                                $product_url = $product_id ? get_permalink($product_id) : '#';
                            ?>
                                <div class="menu-dish-item" data-veg="<?php echo $is_veg ? 'true' : 'false'; ?>">
                                    <div class="menu-dish-row-layout">
                                        <!-- Left / Main: Dish Name (Linked to WC details), Tags, Description -->
                                        <div class="menu-dish-main-info">
                                            <div class="menu-dish-title-wrap">
                                                <?php if ($product_id) : ?>
                                                    <a href="<?php echo esc_url($product_url); ?>" class="menu-dish-title-link" title="View details for <?php echo esc_attr($item['title']); ?>">
                                                        <h3 class="menu-dish-name"><?php echo esc_html($item['title']); ?></h3>
                                                    </a>
                                                <?php else : ?>
                                                    <h3 class="menu-dish-name"><?php echo esc_html($item['title']); ?></h3>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($item['tags'])) : ?>
                                                    <div class="menu-dish-tags">
                                                        <?php foreach ($item['tags'] as $tag) : 
                                                            $badge_class = 'badge-tag-' . strtolower(sanitize_html_class($tag));
                                                        ?>
                                                            <span class="menu-badge <?php echo esc_attr($badge_class); ?>">
                                                                <?php echo esc_html($tag); ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <?php if (!empty($item['desc'])) : ?>
                                                <p class="menu-dish-desc"><?php echo esc_html($item['desc']); ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Right: Price & Add to Cart Button -->
                                        <div class="menu-dish-action-side">
                                            <span class="menu-dish-price"><?php echo esc_html($item['price']); ?></span>
                                            <?php if ($product_id) : ?>
                                                <button type="button" class="cochin-add-cart-btn" data-product-id="<?php echo esc_attr($product_id); ?>" aria-label="Add <?php echo esc_attr($item['title']); ?> to cart">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                                    <span>Add to cart</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </section>
        <?php endforeach; ?>
    </div>

    <!-- =========================================================================
         BOTTOM BOOKING & ORDER CALLOUT
         ========================================================================= -->
    <section class="menu-bottom-callout">
        <div class="menu-container">
            <div class="menu-callout-box">
                <h3 class="menu-callout-title">Ready to Experience Cochin Flavours?</h3>
                <p class="menu-callout-text">Reserve a table at our historic Old Town restaurant or order online for collection &amp; delivery.</p>
                <div class="menu-callout-actions">
                    <a href="<?php echo esc_url(the_cochin_get_booking_url()); ?>" class="menu-btn-primary">BOOK A TABLE</a>
                    <a href="<?php echo esc_url(home_url('/#order')); ?>" class="menu-btn-secondary">ORDER ONLINE</a>
                </div>
            </div>
        </div>
    </section>

</div>

<!-- Inline JavaScript for Menu Search, Veg Filter, Add-to-Cart AJAX, and Smooth Category Sticky Pill Tabs -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('menuSearchInput');
    const vegCheckbox = document.getElementById('vegFilterCheckbox');
    const categorySections = document.querySelectorAll('.menu-category-section');
    const navPills = document.querySelectorAll('.menu-pill');

    function filterDishes() {
        const query = (searchInput.value || '').toLowerCase().trim();
        const vegOnly = vegCheckbox.checked;

        categorySections.forEach(section => {
            const dishes = section.querySelectorAll('.menu-dish-item');
            let visibleCount = 0;

            dishes.forEach(dish => {
                const title = dish.querySelector('.menu-dish-name')?.textContent.toLowerCase() || '';
                const desc = dish.querySelector('.menu-dish-desc')?.textContent.toLowerCase() || '';
                const isVeg = dish.getAttribute('data-veg') === 'true';

                const matchesQuery = !query || title.includes(query) || desc.includes(query);
                const matchesVeg = !vegOnly || isVeg;

                if (matchesQuery && matchesVeg) {
                    dish.style.display = 'block';
                    visibleCount++;
                } else {
                    dish.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                section.style.display = 'none';
            } else {
                section.style.display = 'block';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterDishes);
    }
    if (vegCheckbox) {
        vegCheckbox.addEventListener('change', filterDishes);
    }

    // Scrollspy for active pill navigation
    window.addEventListener('scroll', function() {
        let scrollPos = window.scrollY + 180;
        categorySections.forEach(sec => {
            if (sec.style.display !== 'none') {
                const top = sec.offsetTop;
                const height = sec.offsetHeight;
                const id = sec.getAttribute('id');
                if (scrollPos >= top && scrollPos < top + height) {
                    navPills.forEach(pill => {
                        pill.classList.remove('active');
                        if (pill.getAttribute('href') === '#' + id) {
                            pill.classList.add('active');
                        }
                    });
                }
            }
        });
    }, { passive: true });

    // Smooth scroll on pill click
    navPills.forEach(pill => {
        pill.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetEl = document.querySelector(targetId);
            if (targetEl) {
                const offset = 140;
                const bodyRect = document.body.getBoundingClientRect().top;
                const elementRect = targetEl.getBoundingClientRect().top;
                const elementPosition = elementRect - bodyRect;
                const offsetPosition = elementPosition - offset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                navPills.forEach(p => p.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });

    // AJAX Add-To-Cart Handler
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.cochin-add-cart-btn');
        if (!btn) return;
        e.preventDefault();

        const productId = btn.getAttribute('data-product-id');
        if (!productId) return;

        const originalHtml = btn.innerHTML;
        btn.classList.add('is-loading');
        btn.innerHTML = '<span>Adding...</span>';

        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', 1);

        fetch('<?php echo esc_url(wc_get_cart_url()); ?>?wc-ajax=add_to_cart', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            btn.classList.remove('is-loading');
            btn.classList.add('is-added');
            btn.innerHTML = '<span>✓ Added!</span>';

            if (data && data.fragments) {
                Object.keys(data.fragments).forEach(key => {
                    const elems = document.querySelectorAll(key);
                    elems.forEach(el => {
                        const temp = document.createElement('div');
                        temp.innerHTML = data.fragments[key];
                        if (temp.firstElementChild) {
                            el.replaceWith(temp.firstElementChild);
                        }
                    });
                });
            }

            setTimeout(() => {
                btn.classList.remove('is-added');
                btn.innerHTML = originalHtml;
            }, 2000);
        })
        .catch(() => {
            window.location.href = '?add-to-cart=' + productId;
        });
    });
});
</script>
