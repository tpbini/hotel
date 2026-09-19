<?php
/**
 * The Cochin - ACF Fields Registration
 *
 * Registers the Homepage Custom Fields group for the Front Page using standard Free ACF features.
 */

if (!defined('ABSPATH')) {
    exit;
}

function the_cochin_register_acf_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    $front_id = get_option('page_on_front');
    $location_rules = array();

    if ($front_id) {
        $location_rules[] = array(
            array(
                'param'    => 'page',
                'operator' => '==',
                'value'    => strval($front_id),
            ),
        );
    }

    $location_rules[] = array(
        array(
            'param'    => 'page_type',
            'operator' => '==',
            'value'    => 'front_page',
        ),
    );

    $fields = array();

    // ----------------------------------------------------
    // TAB: Hero Banner
    // ----------------------------------------------------
    $fields[] = array(
        'key'   => 'field_tab_hero',
        'label' => '🌟 Hero Section',
        'name'  => '',
        'type'  => 'tab',
    );
    $fields[] = array(
        'key'           => 'field_hero_bg_image',
        'label'         => 'Hero Background Image',
        'name'          => 'hero_bg_image',
        'type'          => 'image',
        'return_format' => 'url',
        'preview_size'  => 'medium',
        'instructions'  => 'Upload or select the main banner background image.',
    );
    $fields[] = array(
        'key'          => 'field_hero_title',
        'label'        => 'Hero Title',
        'name'         => 'hero_title',
        'type'         => 'text',
        'default_value'=> 'Authentic Kerala Cuisine in Hemel Hempstead\'s Old Town',
    );
    $fields[] = array(
        'key'          => 'field_hero_desc',
        'label'        => 'Hero Description',
        'name'         => 'hero_desc',
        'type'         => 'textarea',
        'rows'         => 3,
        'default_value'=> 'Proudly serving the local community since 2003. Discover the distinctive flavours of Kerala, freshly prepared and served with the warmth of traditional South Indian hospitality.',
    );
    $fields[] = array(
        'key'          => 'field_hero_btn1_text',
        'label'        => 'Button 1 Text (Wine Red)',
        'name'         => 'hero_btn1_text',
        'type'         => 'text',
        'default_value'=> 'BOOK A TABLE',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_hero_btn1_url',
        'label'        => 'Button 1 URL',
        'name'         => 'hero_btn1_url',
        'type'         => 'text',
        'default_value'=> '/book-a-table/',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_hero_btn2_text',
        'label'        => 'Button 2 Text (Orange)',
        'name'         => 'hero_btn2_text',
        'type'         => 'text',
        'default_value'=> 'ORDER ONLINE',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_hero_btn2_url',
        'label'        => 'Button 2 URL',
        'name'         => 'hero_btn2_url',
        'type'         => 'text',
        'default_value'=> '/menu/',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_hero_btn3_text',
        'label'        => 'Button 3 Text (Gold)',
        'name'         => 'hero_btn3_text',
        'type'         => 'text',
        'default_value'=> 'VIEW MENU',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_hero_btn3_url',
        'label'        => 'Button 3 URL',
        'name'         => 'hero_btn3_url',
        'type'         => 'text',
        'default_value'=> '/menu/',
        'wrapper'      => array('width' => '50'),
    );

    // ----------------------------------------------------
    // TAB: About Us
    // ----------------------------------------------------
    $fields[] = array(
        'key'   => 'field_tab_about',
        'label' => '📖 About Us',
        'name'  => '',
        'type'  => 'tab',
    );
    $fields[] = array(
        'key'          => 'field_about_badge',
        'label'        => 'Section Badge / Subtitle',
        'name'         => 'about_badge',
        'type'         => 'text',
        'default_value'=> 'About us',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_about_title',
        'label'        => 'Section Heading',
        'name'         => 'about_title',
        'type'         => 'text',
        'default_value'=> 'Welcome to The Cochin',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'           => 'field_about_image',
        'label'         => 'About Us Photo',
        'name'          => 'about_image',
        'type'          => 'image',
        'return_format' => 'url',
        'preview_size'  => 'medium',
        'instructions'  => 'Upload or select the About Us section image.',
    );
    $fields[] = array(
        'key'          => 'field_about_content',
        'label'        => 'About Us Story Content',
        'name'         => 'about_content',
        'type'         => 'wysiwyg',
        'tabs'         => 'all',
        'toolbar'      => 'basic',
        'media_upload' => 0,
        'default_value'=> '<p>Located in the heart of Hemel Hempstead’s historic Old Town, The Cochin has been serving authentic Kerala and South Indian cuisine since 2003. Our menu is inspired by the food traditions of India’s south-west coast, from delicately spiced vegetarian dishes and crisp dosas to rich curries, seafood specialities and comforting favourites.</p><p>Whether you are joining us for a relaxed meal, celebrating with family and friends, collecting a takeaway or ordering for delivery, our team looks forward to welcoming you.</p>',
    );

    // ----------------------------------------------------
    // TAB: Menu Carousel
    // ----------------------------------------------------
    $fields[] = array(
        'key'   => 'field_tab_carousel',
        'label' => '🍽️ Menu Carousel',
        'name'  => '',
        'type'  => 'tab',
    );
    $fields[] = array(
        'key'          => 'field_carousel_show',
        'label'        => 'Show Menu Carousel',
        'name'         => 'carousel_show',
        'type'         => 'true_false',
        'default_value'=> 1,
        'ui'           => 1,
        'instructions' => 'Enable or disable the auto-scrolling menu carousel on the homepage.',
    );

    // ----------------------------------------------------
    // TAB: Banquet Meals
    // ----------------------------------------------------
    $fields[] = array(
        'key'   => 'field_tab_banquets',
        'label' => '🍛 Banquet Meals',
        'name'  => '',
        'type'  => 'tab',
    );
    $fields[] = array(
        'key'          => 'field_banquet_title',
        'label'        => 'Banquet Title',
        'name'         => 'banquet_title',
        'type'         => 'text',
        'default_value'=> 'Banquet Meals',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_banquet_days',
        'label'        => 'Available Days',
        'name'         => 'banquet_days',
        'type'         => 'text',
        'default_value'=> 'EVERY TUESDAY • THURSDAY • WEDNESDAY',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_banquet_time',
        'label'        => 'Available Time',
        'name'         => 'banquet_time',
        'type'         => 'text',
        'default_value'=> '6PM – 11 PM',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_banquet_desc',
        'label'        => 'Meal Includes Description',
        'name'         => 'banquet_desc',
        'type'         => 'text',
        'default_value'=> '1 Starter + 1 Main Dish + Side Dish + Rice & Bread',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_banquet_veg_price',
        'label'        => 'Vegetarian Price',
        'name'         => 'banquet_veg_price',
        'type'         => 'text',
        'default_value'=> '£ 15.95',
        'wrapper'      => array('width' => '33'),
    );
    $fields[] = array(
        'key'          => 'field_banquet_nonveg_price',
        'label'        => 'Non-Vegetarian Price',
        'name'         => 'banquet_nonveg_price',
        'type'         => 'text',
        'default_value'=> '£ 17.95',
        'wrapper'      => array('width' => '33'),
    );
    $fields[] = array(
        'key'          => 'field_banquet_seafood_price',
        'label'        => 'Seafood Price',
        'name'         => 'banquet_seafood_price',
        'type'         => 'text',
        'default_value'=> '£ 19.95',
        'wrapper'      => array('width' => '34'),
    );
    $fields[] = array(
        'key'           => 'field_banquet_dish_image',
        'label'         => 'Dish Showcase Image',
        'name'          => 'banquet_dish_image',
        'type'          => 'image',
        'return_format' => 'url',
        'preview_size'  => 'medium',
        'wrapper'       => array('width' => '50'),
    );
    $fields[] = array(
        'key'           => 'field_banquet_bg_image',
        'label'         => 'Background Texture Image',
        'name'          => 'banquet_bg_image',
        'type'          => 'image',
        'return_format' => 'url',
        'preview_size'  => 'medium',
        'wrapper'       => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_banquet_book_url',
        'label'        => 'Book a Table Button URL',
        'name'         => 'banquet_book_url',
        'type'         => 'text',
        'default_value'=> '/book-a-table/',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_banquet_order_url',
        'label'        => 'Order Online Button URL',
        'name'         => 'banquet_order_url',
        'type'         => 'text',
        'default_value'=> '/menu/',
        'wrapper'      => array('width' => '50'),
    );

    // ----------------------------------------------------
    // TAB: Delivery & Collection
    // ----------------------------------------------------
    $fields[] = array(
        'key'   => 'field_tab_delivery',
        'label' => '🛵 Delivery & Collection',
        'name'  => '',
        'type'  => 'tab',
    );
    $fields[] = array(
        'key'          => 'field_delivery_badge',
        'label'        => 'Section Badge',
        'name'         => 'delivery_badge',
        'type'         => 'text',
        'default_value'=> 'Our Menu',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_delivery_title',
        'label'        => 'Heading (HTML permitted)',
        'name'         => 'delivery_title',
        'type'         => 'text',
        'default_value'=> 'Book, Collect or order for<br>delivery',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_delivery_desc',
        'label'        => 'Description',
        'name'         => 'delivery_desc',
        'type'         => 'textarea',
        'rows'         => 3,
        'default_value'=> 'Dining with us? Reserve your table online. Prefer to enjoy The Cochin at home? Order for collection or local delivery through our secure online ordering service.',
    );
    $fields[] = array(
        'key'           => 'field_delivery_image',
        'label'         => 'Delivery Illustration / Photo',
        'name'          => 'delivery_image',
        'type'          => 'image',
        'return_format' => 'url',
        'preview_size'  => 'medium',
    );
    $fields[] = array(
        'key'          => 'field_delivery_book_url',
        'label'        => 'Book a Table URL',
        'name'         => 'delivery_book_url',
        'type'         => 'text',
        'default_value'=> '/book-a-table/',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_delivery_order_url',
        'label'        => 'Order Online URL',
        'name'         => 'delivery_order_url',
        'type'         => 'text',
        'default_value'=> '/menu/',
        'wrapper'      => array('width' => '50'),
    );

    // ----------------------------------------------------
    // TAB: Dietary Choices
    // ----------------------------------------------------
    $fields[] = array(
        'key'   => 'field_tab_dietary',
        'label' => '🥗 Dietary Choices',
        'name'  => '',
        'type'  => 'tab',
    );
    $fields[] = array(
        'key'          => 'field_dietary_badge',
        'label'        => 'Badge Text',
        'name'         => 'dietary_badge',
        'type'         => 'text',
        'default_value'=> 'Food choice',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_dietary_title',
        'label'        => 'Section Heading',
        'name'         => 'dietary_title',
        'type'         => 'text',
        'default_value'=> 'Vegetarian and vegan choices',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_dietary_desc',
        'label'        => 'Description',
        'name'         => 'dietary_desc',
        'type'         => 'textarea',
        'rows'         => 3,
        'default_value'=> 'Kerala cuisine offers a wonderful variety of naturally vegetarian and plant-based dishes. Look for the dietary symbols on our menu or speak to our team for guidance.',
    );
    $fields[] = array(
        'key'           => 'field_dietary_bg_image',
        'label'         => 'Background Image',
        'name'          => 'dietary_bg_image',
        'type'          => 'image',
        'return_format' => 'url',
        'preview_size'  => 'medium',
    );
    $fields[] = array(
        'key'          => 'field_dietary_book_url',
        'label'        => 'Book a Table URL',
        'name'         => 'dietary_book_url',
        'type'         => 'text',
        'default_value'=> '/book-a-table/',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_dietary_order_url',
        'label'        => 'Order Online URL',
        'name'         => 'dietary_order_url',
        'type'         => 'text',
        'default_value'=> '/menu/',
        'wrapper'      => array('width' => '50'),
    );

    // ----------------------------------------------------
    // TAB: FAQ Accordion
    // ----------------------------------------------------
    $fields[] = array(
        'key'   => 'field_tab_faq',
        'label' => '❓ FAQ Accordion',
        'name'  => '',
        'type'  => 'tab',
    );
    $fields[] = array(
        'key'          => 'field_faq_badge',
        'label'        => 'FAQ Badge',
        'name'         => 'faq_badge',
        'type'         => 'text',
        'default_value'=> 'FAQ',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'          => 'field_faq_title',
        'label'        => 'FAQ Heading',
        'name'         => 'faq_title',
        'type'         => 'text',
        'default_value'=> 'Frequently Asked Questions',
        'wrapper'      => array('width' => '50'),
    );
    $fields[] = array(
        'key'           => 'field_faq_bottom_art',
        'label'         => 'Kerala Artwork (Bottom of FAQ)',
        'name'          => 'faq_bottom_art',
        'type'          => 'image',
        'return_format' => 'url',
        'preview_size'  => 'medium',
    );

    // 8 Structured FAQ Questions & Answers
    $default_faqs = array(
        1 => array(
            'q' => 'Do you offer vegetarian and vegan dishes?',
            'a' => 'Yes. Our menu includes a range of vegetarian and vegan choices. Please check the current menu or speak to our team for guidance.',
        ),
        2 => array(
            'q' => 'Can you accommodate food allergies?',
            'a' => 'Please tell us about any allergy before ordering. We handle multiple allergens in our kitchen, so cross-contact may occur. Our team can provide the current allergen information and help you make an informed choice.',
        ),
        3 => array(
            'q' => 'Do you offer takeaway and delivery?',
            'a' => 'Yes. You can order online for collection or local delivery. Availability, delivery areas and estimated times are shown during checkout.',
        ),
        4 => array(
            'q' => 'Do I need to book banquet meals in advance?',
            'a' => 'Advance booking is recommended for banquet meals, especially during peak dining times and weekends.',
        ),
        5 => array(
            'q' => 'Can you cater for groups or private events?',
            'a' => 'Yes, subject to availability. Contact us with the date, guest numbers and requirements, and our team will discuss the options with you.',
        ),
        6 => array(
            'q' => 'Is parking available?',
            'a' => 'Street and public car parking options are available in Hemel Hempstead Old Town within short walking distance.',
        ),
        7 => array(
            'q' => '',
            'a' => '',
        ),
        8 => array(
            'q' => '',
            'a' => '',
        ),
    );

    for ($i = 1; $i <= 8; $i++) {
        $fields[] = array(
            'key'          => 'field_faq_' . $i . '_question',
            'label'        => sprintf('Question %d (Leave empty to skip)', $i),
            'name'         => 'faq_' . $i . '_question',
            'type'         => 'text',
            'default_value'=> $default_faqs[$i]['q'] ?? '',
        );
        $fields[] = array(
            'key'          => 'field_faq_' . $i . '_answer',
            'label'        => sprintf('Answer %d', $i),
            'name'         => 'faq_' . $i . '_answer',
            'type'         => 'textarea',
            'rows'         => 3,
            'default_value'=> $default_faqs[$i]['a'] ?? '',
        );
    }

    acf_add_local_field_group(array(
        'key'                   => 'group_the_cochin_homepage',
        'title'                 => '🍽️ The Cochin — Homepage Content & Layout Settings',
        'fields'                => $fields,
        'location'              => $location_rules,
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen'        => array(),
        'active'                => true,
        'description'           => 'Manage all homepage visual banners, images, text, and FAQs easily.',
    ));

    // ----------------------------------------------------
    // ABOUT US PAGE FIELD GROUP
    // ----------------------------------------------------
    $about_page = get_page_by_path('about');
    if (!$about_page) {
        $about_page = get_page_by_path('about-us');
    }

    $about_locations = array(
        array(
            array(
                'param'    => 'page_template',
                'operator' => '==',
                'value'    => 'page-about.php',
            ),
        ),
    );

    if ($about_page) {
        $about_locations[] = array(
            array(
                'param'    => 'page',
                'operator' => '==',
                'value'    => strval($about_page->ID),
            ),
        );
    }

    $about_fields = array(
        // Tab 1: Story Intro & Since 2003
        array(
            'key'   => 'field_tab_about_r1',
            'label' => '🌟 Row 1: Story & Dining Photo',
            'name'  => '',
            'type'  => 'tab',
        ),
        array(
            'key'          => 'field_about_r1_title',
            'label'        => 'Row 1 Heading',
            'name'         => 'about_r1_title',
            'type'         => 'textarea',
            'rows'         => 2,
            'default_value'=> "OUR\nSTORY",
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_about_r1_since',
            'label'        => 'Established Tagline',
            'name'         => 'about_r1_since',
            'type'         => 'text',
            'default_value'=> 'SINCE 2003',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_about_r1_p1',
            'label'        => 'Story Content (Press Enter twice for paragraphs)',
            'name'         => 'about_r1_p1',
            'type'         => 'textarea',
            'rows'         => 8,
            'default_value'=> "The Cochin has been part of Hemel Hempstead's dining community since 2003. From our restaurant in the historic Old Town, we have welcomed generations of guests to enjoy the distinctive flavours and warm hospitality of Kerala.\n\nThe restaurant takes its name from Cochin - now Kochi - a historic port city on Kerala's south-west coast. For centuries, the region's food culture has been shaped by the spice trade, coastal ingredients and the traditions of the communities who call Kerala home.\n\nOur aim is to continue sharing that culinary heritage through carefully prepared food, genuine hospitality and a relaxed dining experience. We welcome long-standing customers, Kerala food lovers and guests discovering the cuisine for the first time.",
        ),
        array(
            'key'           => 'field_about_r1_image',
            'label'         => 'Dining Experience Photo (Right Side)',
            'name'          => 'about_r1_image',
            'type'          => 'image',
            'return_format' => 'url',
            'preview_size'  => 'medium',
        ),

        // Tab 2: Overlapping Circular Food Imagery
        array(
            'key'   => 'field_tab_about_r2',
            'label' => '🍽️ Row 2: Overlapping Circles & Food',
            'name'  => '',
            'type'  => 'tab',
        ),
        array(
            'key'          => 'field_about_r2_title',
            'label'        => 'Row 2 Heading',
            'name'         => 'about_r2_title',
            'type'         => 'textarea',
            'rows'         => 2,
            'default_value'=> "OUR\nFOOD",
        ),
        array(
            'key'          => 'field_about_r2_desc',
            'label'        => 'Row 2 Content (Press Enter twice for paragraphs)',
            'name'         => 'about_r2_desc',
            'type'         => 'textarea',
            'rows'         => 5,
            'default_value'=> "Our menu brings together familiar South Indian favourites and dishes that reflect Kerala's distinctive regional character. Expect aromatic spices, coconut, curry leaves, seafood, rice, lentils and thoughtfully balanced flavours across vegetarian and non-vegetarian selections.",
        ),
        array(
            'key'           => 'field_about_r2_img_lg',
            'label'         => 'Large Circular Food Dish Photo',
            'name'          => 'about_r2_img_lg',
            'type'          => 'image',
            'return_format' => 'url',
            'preview_size'  => 'medium',
            'wrapper'       => array('width' => '50'),
        ),
        array(
            'key'           => 'field_about_r2_img_sm',
            'label'         => 'Small Overlapping Circular Dish Photo',
            'name'          => 'about_r2_img_sm',
            'type'          => 'image',
            'return_format' => 'url',
            'preview_size'  => 'medium',
            'wrapper'       => array('width' => '50'),
        ),

        // Tab 3: Food & Dining Gallery Mosaic
        array(
            'key'   => 'field_tab_about_g',
            'label' => '🖼️ Row 3: Dining Mosaic Gallery',
            'name'  => '',
            'type'  => 'tab',
        ),
        array(
            'key'           => 'field_about_g_img1',
            'label'         => 'Gallery 1: Tall Banquet Photo (Column 1)',
            'name'          => 'about_g_img1',
            'type'          => 'image',
            'return_format' => 'url',
            'preview_size'  => 'medium',
            'wrapper'       => array('width' => '50'),
        ),
        array(
            'key'           => 'field_about_g_img2',
            'label'         => 'Gallery 2: Top Ambiance Photo (Column 2)',
            'name'          => 'about_g_img2',
            'type'          => 'image',
            'return_format' => 'url',
            'preview_size'  => 'medium',
            'wrapper'       => array('width' => '50'),
        ),
        array(
            'key'           => 'field_about_g_img3',
            'label'         => 'Gallery 3: Bottom Soup/Starter (Column 2)',
            'name'          => 'about_g_img3',
            'type'          => 'image',
            'return_format' => 'url',
            'preview_size'  => 'medium',
            'wrapper'       => array('width' => '50'),
        ),
        array(
            'key'           => 'field_about_g_img4',
            'label'         => 'Gallery 4: Top Dessert Photo (Column 3)',
            'name'          => 'about_g_img4',
            'type'          => 'image',
            'return_format' => 'url',
            'preview_size'  => 'medium',
            'wrapper'       => array('width' => '50'),
        ),
        array(
            'key'           => 'field_about_g_img5',
            'label'         => 'Gallery 5: Bottom Dining Photo (Column 3)',
            'name'          => 'about_g_img5',
            'type'          => 'image',
            'return_format' => 'url',
            'preview_size'  => 'medium',
            'wrapper'       => array('width' => '50'),
        ),
        array(
            'key'           => 'field_about_g_img6',
            'label'         => 'Gallery 6: Tall Seafood Dish Photo (Column 4)',
            'name'          => 'about_g_img6',
            'type'          => 'image',
            'return_format' => 'url',
            'preview_size'  => 'medium',
            'wrapper'       => array('width' => '50'),
        ),

        // Tab 4: Our Specialties
        array(
            'key'   => 'field_tab_about_sp',
            'label' => '🥗 Row 4: Our Specialties Tabs',
            'name'  => '',
            'type'  => 'tab',
        ),
        array(
            'key'          => 'field_about_sp_badge',
            'label'        => 'Subtitle Badge',
            'name'         => 'about_sp_badge',
            'type'         => 'text',
            'default_value'=> 'TASTY AND CRUNCHY',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_about_sp_title',
            'label'        => 'Main Title',
            'name'         => 'about_sp_title',
            'type'         => 'text',
            'default_value'=> 'Our Specialties',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'           => 'field_about_sp_dish1',
            'label'         => 'Round Specialty Dish 1',
            'name'          => 'about_sp_dish1',
            'type'          => 'image',
            'return_format' => 'url',
            'preview_size'  => 'medium',
            'wrapper'       => array('width' => '50'),
        ),
        array(
            'key'           => 'field_about_sp_dish2',
            'label'         => 'Round Specialty Dish 2',
            'name'          => 'about_sp_dish2',
            'type'          => 'image',
            'return_format' => 'url',
            'preview_size'  => 'medium',
            'wrapper'       => array('width' => '50'),
        ),
        array(
            'key'          => 'field_about_sp_content',
            'label'        => 'Specialties Content / Description',
            'name'         => 'about_sp_content',
            'type'         => 'textarea',
            'rows'         => 5,
            'default_value'=> 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat quis nostrud exercitation ullamco laboris.',
        ),
    );

    acf_add_local_field_group(array(
        'key'                   => 'group_the_cochin_about_page',
        'title'                 => '📖 The Cochin — About Us Page Editorial Layout Settings',
        'fields'                => $about_fields,
        'location'              => $about_locations,
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'description'           => 'Manage the 4-tier editorial layout images and text for the About Us page.',
    ));

    // -------------------------------------------------------------------------
    // MENU PAGE FIELD GROUP
    // -------------------------------------------------------------------------
    $menu_page_id = 0;
    $menu_page_obj = get_page_by_path('menu');
    if ($menu_page_obj) {
        $menu_page_id = $menu_page_obj->ID;
    }

    $menu_locations = array(
        array(
            array(
                'param'    => 'page_template',
                'operator' => '==',
                'value'    => 'page-menu.php',
            ),
        ),
    );

    if ($menu_page_id) {
        $menu_locations[] = array(
            array(
                'param'    => 'page',
                'operator' => '==',
                'value'    => strval($menu_page_id),
            ),
        );
    }

    $menu_fields = array(
        // Tab 1: Header Banner & Intro
        array(
            'key'   => 'field_tab_menu_intro',
            'label' => '🍽️ Intro & Header Banner',
            'name'  => '',
            'type'  => 'tab',
        ),
        array(
            'key'          => 'field_menu_page_badge',
            'label'        => 'Subtitle Badge',
            'name'         => 'menu_page_badge',
            'type'         => 'text',
            'default_value'=> 'DISCOVER OUR FOOD',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_menu_page_heading',
            'label'        => 'Main Heading',
            'name'         => 'menu_page_heading',
            'type'         => 'text',
            'default_value'=> 'Explore the Flavours of Kerala & South India',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_menu_page_intro',
            'label'        => 'Introduction Description',
            'name'         => 'menu_page_intro',
            'type'         => 'textarea',
            'rows'         => 3,
            'default_value'=> 'Explore the flavours of Kerala and South India. Our menu includes starters, dosas, seafood, meat and poultry dishes, vegetarian and vegan choices, rice dishes, breads, desserts and drinks.',
        ),

        // Tab 2: Dietary & Allergen Notice
        array(
            'key'   => 'field_tab_menu_allergen',
            'label' => '⚠️ Dietary & Allergen Notice',
            'name'  => '',
            'type'  => 'tab',
        ),
        array(
            'key'          => 'field_menu_allergen_show',
            'label'        => 'Show Allergen Notice Card',
            'name'         => 'menu_allergen_show',
            'type'         => 'true_false',
            'default_value'=> 1,
            'ui'           => 1,
            'wrapper'      => array('width' => '30'),
        ),
        array(
            'key'          => 'field_menu_allergen_title',
            'label'        => 'Notice Title',
            'name'         => 'menu_allergen_title',
            'type'         => 'text',
            'default_value'=> 'Dietary and Allergen Notice',
            'wrapper'      => array('width' => '70'),
        ),
        array(
            'key'          => 'field_menu_allergen_desc',
            'label'        => 'Notice Details',
            'name'         => 'menu_allergen_desc',
            'type'         => 'textarea',
            'rows'         => 3,
            'default_value'=> 'Please tell a member of our team about any allergy or dietary requirement before ordering. Our dishes are prepared in a kitchen where allergens are handled, and cross-contact may occur. Please ask for our current allergen information.',
        ),

        // Tab 3: Special Offer Banner
        array(
            'key'   => 'field_tab_menu_offer',
            'label' => '🏷️ Special Offer Banner',
            'name'  => '',
            'type'  => 'tab',
        ),
        array(
            'key'          => 'field_menu_offer_show',
            'label'        => 'Show Special Offer Banner',
            'name'         => 'menu_offer_show',
            'type'         => 'true_false',
            'default_value'=> 1,
            'ui'           => 1,
            'wrapper'      => array('width' => '30'),
        ),
        array(
            'key'          => 'field_menu_offer_badge',
            'label'        => 'Offer Badge',
            'name'         => 'menu_offer_badge',
            'type'         => 'text',
            'default_value'=> 'SPECIAL OFFER',
            'wrapper'      => array('width' => '70'),
        ),
        array(
            'key'          => 'field_menu_offer_text',
            'label'        => 'Offer Description Text',
            'name'         => 'menu_offer_text',
            'type'         => 'textarea',
            'rows'         => 2,
            'default_value'=> '<strong>15% Off</strong> on all collection orders over £25 when ordered online.',
        ),
        array(
            'key'          => 'field_menu_offer_btn_text',
            'label'        => 'Offer Button Text',
            'name'         => 'menu_offer_btn_text',
            'type'         => 'text',
            'default_value'=> 'ORDER TAKEAWAY',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_menu_offer_btn_url',
            'label'        => 'Offer Button URL',
            'name'         => 'menu_offer_btn_url',
            'type'         => 'text',
            'default_value'=> '/#order',
            'wrapper'      => array('width' => '50'),
        ),

        // Tab 4: Bottom Callout Box
        array(
            'key'   => 'field_tab_menu_callout',
            'label' => '📞 Bottom Booking Callout',
            'name'  => '',
            'type'  => 'tab',
        ),
        array(
            'key'          => 'field_menu_callout_show',
            'label'        => 'Show Bottom Callout',
            'name'         => 'menu_callout_show',
            'type'         => 'true_false',
            'default_value'=> 1,
            'ui'           => 1,
            'wrapper'      => array('width' => '30'),
        ),
        array(
            'key'          => 'field_menu_callout_title',
            'label'        => 'Callout Title',
            'name'         => 'menu_callout_title',
            'type'         => 'text',
            'default_value'=> 'Ready to Experience Cochin Flavours?',
            'wrapper'      => array('width' => '70'),
        ),
        array(
            'key'          => 'field_menu_callout_text',
            'label'        => 'Callout Description',
            'name'         => 'menu_callout_text',
            'type'         => 'textarea',
            'rows'         => 2,
            'default_value'=> 'Reserve a table at our historic Old Town restaurant or order online for collection & delivery.',
        ),
        array(
            'key'          => 'field_menu_callout_btn1_text',
            'label'        => 'Primary Button Text',
            'name'         => 'menu_callout_btn1_text',
            'type'         => 'text',
            'default_value'=> 'BOOK A TABLE',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_menu_callout_btn1_url',
            'label'        => 'Primary Button URL',
            'name'         => 'menu_callout_btn1_url',
            'type'         => 'text',
            'default_value'=> '/book-a-table/',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_menu_callout_btn2_text',
            'label'        => 'Secondary Button Text',
            'name'         => 'menu_callout_btn2_text',
            'type'         => 'text',
            'default_value'=> 'ORDER ONLINE',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_menu_callout_btn2_url',
            'label'        => 'Secondary Button URL',
            'name'         => 'menu_callout_btn2_url',
            'type'         => 'text',
            'default_value'=> '/#order',
            'wrapper'      => array('width' => '50'),
        ),
    );

    acf_add_local_field_group(array(
        'key'                   => 'group_the_cochin_menu_page',
        'title'                 => '🍽️ The Cochin — Menu Page Header & Notice Settings',
        'fields'                => $menu_fields,
        'location'              => $menu_locations,
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'description'           => 'Manage the header banner, dietary notice, special offer, and callout for the Menu page.',
    ));

    // -------------------------------------------------------------------------
    // BANQUETS PAGE FIELD GROUP
    // -------------------------------------------------------------------------
    $banquets_page_id = 0;
    $banquets_page_obj = get_page_by_path('banquets');
    if ($banquets_page_obj) {
        $banquets_page_id = $banquets_page_obj->ID;
    }

    $banquet_locations = array(
        array(
            array(
                'param'    => 'page_template',
                'operator' => '==',
                'value'    => 'page-banquets.php',
            ),
        ),
    );

    if ($banquets_page_id) {
        $banquet_locations[] = array(
            array(
                'param'    => 'page',
                'operator' => '==',
                'value'    => strval($banquets_page_id),
            ),
        );
    }

    $banquet_fields = array(
        // Tab 1: Intro & Headline
        array(
            'key'   => 'field_tab_banquet_intro',
            'label' => '🌟 Intro & Headline',
            'name'  => '',
            'type'  => 'tab',
        ),
        array(
            'key'          => 'field_banquet_intro_badge',
            'label'        => 'Subtitle Badge',
            'name'         => 'banquet_intro_badge',
            'type'         => 'text',
            'default_value'=> 'BANQUET MEALS',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_banquet_main_headline',
            'label'        => 'Main Headline',
            'name'         => 'banquet_main_headline',
            'type'         => 'text',
            'default_value'=> 'A Kerala Feast for the Whole Table',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_banquet_intro_text',
            'label'        => 'Introductory Copy',
            'name'         => 'banquet_intro_text',
            'type'         => 'textarea',
            'rows'         => 3,
            'default_value'=> 'Experience a generous selection of dishes designed for sharing. Our banquet meals are ideal for family gatherings, groups and guests who would like to explore a wider range of Kerala flavours.',
        ),

        // Tab 2: Feast Packages
        array(
            'key'   => 'field_tab_banquet_feasts',
            'label' => '🍲 Feast Packages (3 Feasts)',
            'name'  => '',
            'type'  => 'tab',
        ),
        array(
            'key'          => 'field_banquet_feasts_title',
            'label'        => 'Section Heading',
            'name'         => 'banquet_feasts_title',
            'type'         => 'text',
            'default_value'=> 'Curated Banquet Menus',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_banquet_feasts_subtitle',
            'label'        => 'Section Subtitle',
            'name'         => 'banquet_feasts_subtitle',
            'type'         => 'text',
            'default_value'=> 'Generous multi-course sharing feasts crafted fresh with authentic spices.',
            'wrapper'      => array('width' => '50'),
        ),

        // Vegetarian Feast
        array(
            'key'          => 'field_banquet_veg_title',
            'label'        => '1. Vegetarian Feast Title',
            'name'         => 'banquet_veg_title',
            'type'         => 'text',
            'default_value'=> 'Vegetarian Feast',
            'wrapper'      => array('width' => '33'),
        ),
        array(
            'key'          => 'field_banquet_veg_badge',
            'label'        => '1. Vegetarian Badge',
            'name'         => 'banquet_veg_badge',
            'type'         => 'text',
            'default_value'=> 'VEGETARIAN',
            'wrapper'      => array('width' => '33'),
        ),
        array(
            'key'          => 'field_banquet_veg_price',
            'label'        => '1. Vegetarian Price / Person',
            'name'         => 'banquet_veg_price',
            'type'         => 'text',
            'default_value'=> '£ 15.95',
            'wrapper'      => array('width' => '34'),
        ),
        array(
            'key'          => 'field_banquet_veg_desc',
            'label'        => '1. Vegetarian Description',
            'name'         => 'banquet_veg_desc',
            'type'         => 'textarea',
            'rows'         => 2,
            'default_value'=> 'A traditional Keralan plant-based feast bringing together garden-fresh vegetables, rich paneer, tempered lentils, and homemade breads.',
        ),
        array(
            'key'          => 'field_banquet_veg_items',
            'label'        => '1. Vegetarian Dish Inclusions (one item per line)',
            'name'         => 'banquet_veg_items',
            'type'         => 'textarea',
            'rows'         => 6,
            'default_value'=> "Keralan Tea Shop Snacks with homemade chutneys\nCrispy Aubergine & Lentil Soup Starters\nPalak Paneer & Dal Spinach Curry\nFresh Beans Coconut Thoran\nFlaky Kerala Parathas & Steamed Basmati Rice\nTraditional Sweet Payasam Pudding",
            'instructions' => 'Enter each included dish on a new line.',
        ),

        // Non-Vegetarian Feast
        array(
            'key'          => 'field_banquet_nonveg_title',
            'label'        => '2. Non-Vegetarian Feast Title',
            'name'         => 'banquet_nonveg_title',
            'type'         => 'text',
            'default_value'=> 'Non-Vegetarian Feast',
            'wrapper'      => array('width' => '33'),
        ),
        array(
            'key'          => 'field_banquet_nonveg_badge',
            'label'        => '2. Non-Vegetarian Badge',
            'name'         => 'banquet_nonveg_badge',
            'type'         => 'text',
            'default_value'=> 'NON-VEGETARIAN',
            'wrapper'      => array('width' => '33'),
        ),
        array(
            'key'          => 'field_banquet_nonveg_price',
            'label'        => '2. Non-Vegetarian Price / Person',
            'name'         => 'banquet_nonveg_price',
            'type'         => 'text',
            'default_value'=> '£ 17.95',
            'wrapper'      => array('width' => '34'),
        ),
        array(
            'key'          => 'field_banquet_nonveg_popular',
            'label'        => '2. Highlight Badge',
            'name'         => 'banquet_nonveg_popular',
            'type'         => 'text',
            'default_value'=> 'CHEF RECOMMENDED',
        ),
        array(
            'key'          => 'field_banquet_nonveg_desc',
            'label'        => '2. Non-Vegetarian Description',
            'name'         => 'banquet_nonveg_desc',
            'type'         => 'textarea',
            'rows'         => 2,
            'default_value'=> 'Our most celebrated sharing banquet featuring tender chicken roasts, slow-braised lamb curries, and aromatic Malabar biryani.',
        ),
        array(
            'key'          => 'field_banquet_nonveg_items',
            'label'        => '2. Non-Vegetarian Dish Inclusions (one item per line)',
            'name'         => 'banquet_nonveg_items',
            'type'         => 'textarea',
            'rows'         => 6,
            'default_value'=> "Keralan Tea Shop Selection & Chicken Samosa\nAlleppey Spiced Chicken Roast\nTraditional Cochin Lamb Curry\nDal & Spinach or Vegetable Thoran\nButtery Kerala Parathas & Fragrant Basmati Rice\nWarm Gulab Jamun with Vanilla Ice Cream",
            'instructions' => 'Enter each included dish on a new line.',
        ),

        // Seafood Feast
        array(
            'key'          => 'field_banquet_seafood_title',
            'label'        => '3. Seafood Feast Title',
            'name'         => 'banquet_seafood_title',
            'type'         => 'text',
            'default_value'=> 'Seafood Feast',
            'wrapper'      => array('width' => '33'),
        ),
        array(
            'key'          => 'field_banquet_seafood_badge',
            'label'        => '3. Seafood Badge',
            'name'         => 'banquet_seafood_badge',
            'type'         => 'text',
            'default_value'=> 'SEAFOOD SPECIAL',
            'wrapper'      => array('width' => '33'),
        ),
        array(
            'key'          => 'field_banquet_seafood_price',
            'label'        => '3. Seafood Price / Person',
            'name'         => 'banquet_seafood_price',
            'type'         => 'text',
            'default_value'=> '£ 19.95',
            'wrapper'      => array('width' => '34'),
        ),
        array(
            'key'          => 'field_banquet_seafood_desc',
            'label'        => '3. Seafood Description',
            'name'         => 'banquet_seafood_desc',
            'type'         => 'textarea',
            'rows'         => 2,
            'default_value'=> 'An opulent coastal banquet from the Arabian Sea docks of Fort Cochin, celebrating delicate King Fish and succulent tiger prawns.',
        ),
        array(
            'key'          => 'field_banquet_seafood_items',
            'label'        => '3. Seafood Dish Inclusions (one item per line)',
            'name'         => 'banquet_seafood_items',
            'type'         => 'textarea',
            'rows'         => 6,
            'default_value'=> "Calamari Rings & Alleppey Prawn Fry\nCochin King Fish Curry in creamy coconut milk\nTiger Prawn Masala with roasted Southern spices\nCabbage Thoran & Tangy Lemon Rice\nFermented Soft Kallappams (2 Pcs)\nArtisanal Mango or Coconut Kulfi",
            'instructions' => 'Enter each included dish on a new line.',
        ),

        // Tab 3: Information & Guidelines
        array(
            'key'   => 'field_tab_banquet_info',
            'label' => 'ℹ️ Information & Guidelines',
            'name'  => '',
            'type'  => 'tab',
        ),
        array(
            'key'          => 'field_banquet_info_title',
            'label'        => 'Guidelines Title',
            'name'         => 'banquet_info_title',
            'type'         => 'text',
            'default_value'=> 'Banquet Information & Details',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_banquet_info_subtitle',
            'label'        => 'Guidelines Subtitle',
            'name'         => 'banquet_info_subtitle',
            'type'         => 'text',
            'default_value'=> 'Important booking notes and guidelines for groups and party dining.',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_banquet_info_timing',
            'label'        => 'Available Times Note',
            'name'         => 'banquet_info_timing',
            'type'         => 'text',
            'default_value'=> 'Daily for Lunch (12:00 PM – 3:00 PM) and Dinner (5:00 PM – 10:30 PM).',
        ),
        array(
            'key'          => 'field_banquet_info_requirement',
            'label'        => 'Booking Requirement Note',
            'name'         => 'banquet_info_requirement',
            'type'         => 'text',
            'default_value'=> 'Advance booking recommended for groups of 4 or more guests.',
        ),
        array(
            'key'          => 'field_banquet_info_children',
            'label'        => 'Children Pricing Note',
            'name'         => 'banquet_info_children',
            'type'         => 'text',
            'default_value'=> 'Half portions and milder child-friendly choices available for children under 10.',
        ),
        array(
            'key'          => 'field_banquet_info_dietary',
            'label'        => 'Dietary Adjustments Note',
            'name'         => 'banquet_info_dietary',
            'type'         => 'text',
            'default_value'=> 'Vegetarian, Vegan, and Gluten-Free feast adjustments available upon prior request.',
        ),

        // Tab 4: Inquiry Form & Direct Contact
        array(
            'key'   => 'field_tab_banquet_form',
            'label' => '📩 Inquiry Form & Contact',
            'name'  => '',
            'type'  => 'tab',
        ),
        array(
            'key'          => 'field_banquet_form_show',
            'label'        => 'Show Booking Inquiry Form',
            'name'         => 'banquet_form_show',
            'type'         => 'true_false',
            'default_value'=> 1,
            'ui'           => 1,
            'wrapper'      => array('width' => '30'),
        ),
        array(
            'key'          => 'field_banquet_form_title',
            'label'        => 'Form Title',
            'name'         => 'banquet_form_title',
            'type'         => 'text',
            'default_value'=> 'Reserve Your Banquet Gathering',
            'wrapper'      => array('width' => '70'),
        ),
        array(
            'key'          => 'field_banquet_form_desc',
            'label'        => 'Form Description',
            'name'         => 'banquet_form_desc',
            'type'         => 'textarea',
            'rows'         => 2,
            'default_value'=> 'Please provide your gathering details below. Our restaurant manager will contact you promptly to confirm table arrangements and banquet selections.',
        ),
        array(
            'key'          => 'field_banquet_phone',
            'label'        => 'Direct Contact Phone',
            'name'         => 'banquet_phone',
            'type'         => 'text',
            'default_value'=> '01442 256111',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_banquet_email',
            'label'        => 'Direct Contact Email',
            'name'         => 'banquet_email',
            'type'         => 'text',
            'default_value'=> 'info@thecochin.uk',
            'wrapper'      => array('width' => '50'),
        ),
    );

    acf_add_local_field_group(array(
        'key'                   => 'group_the_cochin_banquets_page',
        'title'                 => '🍲 The Cochin — Banquets Page Settings & Menus',
        'fields'                => $banquet_fields,
        'location'              => $banquet_locations,
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'description'           => 'Manage banquet feast menus, pricing, dish inclusions, guidelines, and booking inquiry settings.',
    ));

    // -------------------------------------------------------------------------
    // CONTACT PAGE FIELD GROUP
    // -------------------------------------------------------------------------
    $contact_page_id = 0;
    $contact_page_obj = get_page_by_path('contact');
    if (!$contact_page_obj) {
        $contact_page_obj = get_page_by_path('contact-us');
    }
    if ($contact_page_obj) {
        $contact_page_id = $contact_page_obj->ID;
    }

    $contact_locations = array(
        array(
            array(
                'param'    => 'page_template',
                'operator' => '==',
                'value'    => 'page-contact.php',
            ),
        ),
    );

    if ($contact_page_id) {
        $contact_locations[] = array(
            array(
                'param'    => 'page',
                'operator' => '==',
                'value'    => strval($contact_page_id),
            ),
        );
    }

    $contact_fields = array(
        // Tab 1: Intro & Hero Banner
        array(
            'key'   => 'field_tab_contact_intro',
            'label' => '🌟 Intro & Headline',
            'name'  => '',
            'type'  => 'tab',
        ),
        array(
            'key'          => 'field_contact_intro_badge',
            'label'        => 'Subtitle Badge',
            'name'         => 'contact_intro_badge',
            'type'         => 'text',
            'default_value'=> 'VISIT & GET IN TOUCH',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_main_headline',
            'label'        => 'Main Headline',
            'name'         => 'contact_main_headline',
            'type'         => 'text',
            'default_value'=> 'Contact The Cochin',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_intro_text',
            'label'        => 'Intro Description Text',
            'name'         => 'contact_intro_text',
            'type'         => 'textarea',
            'rows'         => 3,
            'default_value'=> 'We have been proudly welcoming guests to Hemel Hempstead’s historic Old Town since 2003. Whether you are reserving a table, organising a special banquet celebration, or inquiring about our authentic Kerala menu, we look forward to hearing from you.',
        ),

        // Tab 2: 4-Card Contact Info Grid
        array(
            'key'   => 'field_tab_contact_cards',
            'label' => '📍 Contact Info Cards (4 Cards)',
            'name'  => '',
            'type'  => 'tab',
        ),
        // Card 1: Location
        array(
            'key'          => 'field_contact_card1_title',
            'label'        => 'Card 1 Title (Location)',
            'name'         => 'contact_card1_title',
            'type'         => 'text',
            'default_value'=> 'Our Location',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_card1_address',
            'label'        => 'Card 1 Address (multiline)',
            'name'         => 'contact_card1_address',
            'type'         => 'textarea',
            'rows'         => 4,
            'default_value'=> "The Cochin\n61 High Street, Old Town\nHemel Hempstead, Herts\nHP1 3AF",
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_card1_btn_text',
            'label'        => 'Card 1 Button Text',
            'name'         => 'contact_card1_btn_text',
            'type'         => 'text',
            'default_value'=> 'Get Directions →',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_card1_map_url',
            'label'        => 'Card 1 Map / Directions URL',
            'name'         => 'contact_card1_map_url',
            'type'         => 'text',
            'default_value'=> 'https://maps.google.com/?q=61+High+Street,+Hemel+Hempstead,+Hertfordshire+HP1+3AF',
            'wrapper'      => array('width' => '50'),
        ),

        // Card 2: Telephone & Bookings
        array(
            'key'          => 'field_contact_card2_title',
            'label'        => 'Card 2 Title (Phone)',
            'name'         => 'contact_card2_title',
            'type'         => 'text',
            'default_value'=> 'Telephone & Bookings',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_card2_phone',
            'label'        => 'Card 2 Phone Number',
            'name'         => 'contact_card2_phone',
            'type'         => 'text',
            'default_value'=> '01442 233777',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_card2_desc',
            'label'        => 'Card 2 Description',
            'name'         => 'contact_card2_desc',
            'type'         => 'textarea',
            'rows'         => 2,
            'default_value'=> 'For immediate bookings, same-day tables, or urgent takeaway queries:',
        ),
        array(
            'key'          => 'field_contact_card2_btn_text',
            'label'        => 'Card 2 Button Text',
            'name'         => 'contact_card2_btn_text',
            'type'         => 'text',
            'default_value'=> 'Book Online Now →',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_card2_btn_url',
            'label'        => 'Card 2 Button URL',
            'name'         => 'contact_card2_btn_url',
            'type'         => 'text',
            'default_value'=> '/book-table/',
            'wrapper'      => array('width' => '50'),
        ),

        // Card 3: Email & Private Events
        array(
            'key'          => 'field_contact_card3_title',
            'label'        => 'Card 3 Title (Email)',
            'name'         => 'contact_card3_title',
            'type'         => 'text',
            'default_value'=> 'Email & Private Events',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_card3_email',
            'label'        => 'Card 3 Email Address',
            'name'         => 'contact_card3_email',
            'type'         => 'text',
            'default_value'=> 'thecochin@gmail.com',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_card3_desc',
            'label'        => 'Card 3 Description',
            'name'         => 'contact_card3_desc',
            'type'         => 'textarea',
            'rows'         => 2,
            'default_value'=> 'For banquet bookings, catering consultations, and corporate event queries:',
        ),
        array(
            'key'          => 'field_contact_card3_subtext',
            'label'        => 'Card 3 Note / Subtext',
            'name'         => 'contact_card3_subtext',
            'type'         => 'text',
            'default_value'=> 'We reply within 24 hours',
        ),

        // Card 4: Opening Hours
        array(
            'key'          => 'field_contact_card4_title',
            'label'        => 'Card 4 Title (Hours)',
            'name'         => 'contact_card4_title',
            'type'         => 'text',
            'default_value'=> 'Opening Hours',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_card4_tue_sat',
            'label'        => 'Tue – Sat Hours',
            'name'         => 'contact_card4_tue_sat',
            'type'         => 'text',
            'default_value'=> '12 PM – 3 PM • 6 PM – 11 PM',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_card4_sun',
            'label'        => 'Sunday Hours',
            'name'         => 'contact_card4_sun',
            'type'         => 'text',
            'default_value'=> '12 PM – 3 PM • 6 PM – 10:30 PM',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_card4_mon',
            'label'        => 'Monday Hours',
            'name'         => 'contact_card4_mon',
            'type'         => 'text',
            'default_value'=> 'Closed (Available for Private Hire)',
            'wrapper'      => array('width' => '50'),
        ),

        // Tab 3: Inquiry Form & Interactive Map
        array(
            'key'   => 'field_tab_contact_form_map',
            'label' => '📩 Inquiry Form & Map',
            'name'  => '',
            'type'  => 'tab',
        ),
        array(
            'key'          => 'field_contact_form_badge',
            'label'        => 'Form Badge',
            'name'         => 'contact_form_badge',
            'type'         => 'text',
            'default_value'=> 'ONLINE INQUIRY',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_form_title',
            'label'        => 'Form Title',
            'name'         => 'contact_form_title',
            'type'         => 'text',
            'default_value'=> 'Send Us a Message',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_form_subtitle',
            'label'        => 'Form Subtitle',
            'name'         => 'contact_form_subtitle',
            'type'         => 'textarea',
            'rows'         => 2,
            'default_value'=> 'Fill out the form below and our team will get back to you promptly.',
        ),
        array(
            'key'          => 'field_contact_map_iframe_src',
            'label'        => 'Google Maps Embed URL / Iframe src',
            'name'         => 'contact_map_iframe_src',
            'type'         => 'text',
            'default_value'=> 'https://maps.google.com/maps?q=61+High+Street,+Hemel+Hempstead+HP1+3AF&t=&z=16&ie=UTF8&iwloc=&output=embed',
            'instructions' => 'Paste the Google Maps embed iframe URL source.',
        ),
        array(
            'key'          => 'field_contact_map_place_name',
            'label'        => 'Map Card Heading',
            'name'         => 'contact_map_place_name',
            'type'         => 'text',
            'default_value'=> 'The Cochin Indian Restaurant',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_map_place_address',
            'label'        => 'Map Card Address Note',
            'name'         => 'contact_map_place_address',
            'type'         => 'text',
            'default_value'=> '📍 61 High Street, Hemel Hempstead HP1 3AF',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_map_tags',
            'label'        => 'Map Tags (separated by | or commas)',
            'name'         => 'contact_map_tags',
            'type'         => 'text',
            'default_value'=> 'Old Town Conservation Area | Kerala Cuisine | Licensed Bar',
        ),
        array(
            'key'          => 'field_contact_map_call_btn',
            'label'        => 'Map Card Call Button Text',
            'name'         => 'contact_map_call_btn',
            'type'         => 'text',
            'default_value'=> '📞 Call 01442 233777',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_map_call_phone',
            'label'        => 'Map Card Call Telephone Number',
            'name'         => 'contact_map_call_phone',
            'type'         => 'text',
            'default_value'=> '01442233777',
            'wrapper'      => array('width' => '50'),
        ),

        // Tab 4: Getting Here & Travel Guide
        array(
            'key'   => 'field_tab_contact_guide',
            'label' => '🚗 Travel & Parking Guide (3 Cards)',
            'name'  => '',
            'type'  => 'tab',
        ),
        array(
            'key'          => 'field_contact_guide_badge',
            'label'        => 'Section Badge',
            'name'         => 'contact_guide_badge',
            'type'         => 'text',
            'default_value'=> 'PLAN YOUR VISIT',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_guide_title',
            'label'        => 'Section Title',
            'name'         => 'contact_guide_title',
            'type'         => 'text',
            'default_value'=> 'Getting Here & Parking Guide',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_guide_subtitle',
            'label'        => 'Section Subtitle',
            'name'         => 'contact_guide_subtitle',
            'type'         => 'textarea',
            'rows'         => 2,
            'default_value'=> 'Conveniently situated on the historic High Street of Hemel Hempstead Old Town with straightforward parking and public transport connections.',
        ),

        // Guide Card 1
        array(
            'key'          => 'field_contact_guide1_icon',
            'label'        => 'Guide 1 Icon / Emoji',
            'name'         => 'contact_guide1_icon',
            'type'         => 'text',
            'default_value'=> '🚗',
            'wrapper'      => array('width' => '30'),
        ),
        array(
            'key'          => 'field_contact_guide1_title',
            'label'        => 'Guide 1 Title',
            'name'         => 'contact_guide1_title',
            'type'         => 'text',
            'default_value'=> 'By Car & Nearby Parking',
            'wrapper'      => array('width' => '70'),
        ),
        array(
            'key'          => 'field_contact_guide1_items',
            'label'        => 'Guide 1 Details (one item per line, format: Bold Title: description)',
            'name'         => 'contact_guide1_items',
            'type'         => 'textarea',
            'rows'         => 5,
            'default_value'=> "On-Street Parking: Available directly on High Street (free evening parking; daytime time limits apply).\nHigh Street Old Town Car Park: Located just 2 minutes' walk behind the High Street shops (HP1 3AF).\nGadebridge Park Car Park: Ample public parking within 3-4 minutes' stroll through the historic churchyard.",
        ),

        // Guide Card 2
        array(
            'key'          => 'field_contact_guide2_icon',
            'label'        => 'Guide 2 Icon / Emoji',
            'name'         => 'contact_guide2_icon',
            'type'         => 'text',
            'default_value'=> '🚆',
            'wrapper'      => array('width' => '30'),
        ),
        array(
            'key'          => 'field_contact_guide2_title',
            'label'        => 'Guide 2 Title',
            'name'         => 'contact_guide2_title',
            'type'         => 'text',
            'default_value'=> 'Train & Public Transport',
            'wrapper'      => array('width' => '70'),
        ),
        array(
            'key'          => 'field_contact_guide2_items',
            'label'        => 'Guide 2 Details (one item per line)',
            'name'         => 'contact_guide2_items',
            'type'         => 'textarea',
            'rows'         => 5,
            'default_value'=> "Hemel Hempstead Train Station: Direct 25-30 minute London Euston connection via West Coast Mainline.\nBus & Taxi Connections: 5 minutes by local taxi or bus routes (1, 2, 4) from the station directly to Old Town.\nHistoric Walking Stroll: 20 minutes scenic walk from the Marlowes shopping centre via Gadebridge Park.",
        ),

        // Guide Card 3
        array(
            'key'          => 'field_contact_guide3_icon',
            'label'        => 'Guide 3 Icon / Emoji',
            'name'         => 'contact_guide3_icon',
            'type'         => 'text',
            'default_value'=> '♿',
            'wrapper'      => array('width' => '30'),
        ),
        array(
            'key'          => 'field_contact_guide3_title',
            'label'        => 'Guide 3 Title',
            'name'         => 'contact_guide3_title',
            'type'         => 'text',
            'default_value'=> 'Accessibility & Family Dining',
            'wrapper'      => array('width' => '70'),
        ),
        array(
            'key'          => 'field_contact_guide3_items',
            'label'        => 'Guide 3 Details (one item per line)',
            'name'         => 'contact_guide3_items',
            'type'         => 'textarea',
            'rows'         => 5,
            'default_value'=> "Step-Free Entry: Ground-floor dining room with wide aisles and accessible table seating.\nFamily Friendly: Highchairs, baby changing facilities, and custom mild Kerala dishes for children.\nDietary & Allergen Support: Full allergen matrices and separate preparation protocols for gluten-free, vegan, and nut allergies.",
        ),

        // Tab 5: Bottom Call-to-Action Banner
        array(
            'key'   => 'field_tab_contact_cta',
            'label' => '🌟 Bottom CTA Banner',
            'name'  => '',
            'type'  => 'tab',
        ),
        array(
            'key'          => 'field_contact_cta_badge',
            'label'        => 'CTA Badge',
            'name'         => 'contact_cta_badge',
            'type'         => 'text',
            'default_value'=> 'EXPERIENCE KERALA CUISINE',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_cta_headline',
            'label'        => 'CTA Headline',
            'name'         => 'contact_cta_headline',
            'type'         => 'text',
            'default_value'=> 'Join Us for Dinner or Plan a Private Celebration',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_cta_desc',
            'label'        => 'CTA Description',
            'name'         => 'contact_cta_desc',
            'type'         => 'textarea',
            'rows'         => 2,
            'default_value'=> 'Reserve your table online in seconds with live slot confirmation, or discover our regional seafood curries, dosas, and vegetarian delicacies.',
        ),
        array(
            'key'          => 'field_contact_cta_btn1_text',
            'label'        => 'Button 1 Text (Booking)',
            'name'         => 'contact_cta_btn1_text',
            'type'         => 'text',
            'default_value'=> '📅 Book a Table Online',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_cta_btn1_url',
            'label'        => 'Button 1 URL',
            'name'         => 'contact_cta_btn1_url',
            'type'         => 'text',
            'default_value'=> '/book-table/',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_cta_btn2_text',
            'label'        => 'Button 2 Text (Menu)',
            'name'         => 'contact_cta_btn2_text',
            'type'         => 'text',
            'default_value'=> '🍛 Explore Restaurant Menu',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_cta_btn2_url',
            'label'        => 'Button 2 URL',
            'name'         => 'contact_cta_btn2_url',
            'type'         => 'text',
            'default_value'=> '/menu/',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_cta_btn3_text',
            'label'        => 'Button 3 Text (Banquets)',
            'name'         => 'contact_cta_btn3_text',
            'type'         => 'text',
            'default_value'=> '🥂 Banquet Hall & Events',
            'wrapper'      => array('width' => '50'),
        ),
        array(
            'key'          => 'field_contact_cta_btn3_url',
            'label'        => 'Button 3 URL',
            'name'         => 'contact_cta_btn3_url',
            'type'         => 'text',
            'default_value'=> '/banquets/',
            'wrapper'      => array('width' => '50'),
        ),
    );

    acf_add_local_field_group(array(
        'key'                   => 'group_the_cochin_contact_page',
        'title'                 => '📍 The Cochin — Contact Page Settings & Details',
        'fields'                => $contact_fields,
        'location'              => $contact_locations,
        'menu_order'            => 0,
        'position'              => 'normal',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'description'           => 'Manage contact details, opening hours, directions, travel guides, inquiry forms, and CTA buttons.',
    ));
}
add_action('acf/init', 'the_cochin_register_acf_fields');
