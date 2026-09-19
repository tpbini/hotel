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
}
add_action('acf/init', 'the_cochin_register_acf_fields');
