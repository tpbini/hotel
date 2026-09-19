<?php
/**
 * The Cochin - About Us Page Content Layout
 *
 * Implements the 4-tier editorial culinary layout:
 * - Row 1: "RESTORACIA AWESOME FOOD FOR YOU" & "SINCE 2003" + Dining Image
 * - Row 2: Overlapping Circular Food Imagery & "WE ALWAYS PREPARE THE MEALS FOR YOU"
 * - Row 3: 4-Column Mosaic Food & Dining Photo Grid
 * - Row 4: "Our Specialties" Tabbed Showcase with 2 Round Dishes and Tabs (Starters, Mains, Desserts)
 */

if (!defined('ABSPATH')) {
    exit;
}

// Helpers for ACF fallback retrieval
function the_cochin_about_val($field_key, $default = '') {
    if (function_exists('get_field')) {
        $val = get_field($field_key);
        if ($val !== null && $val !== '') {
            return $val;
        }
    }
    return $default;
}

function the_cochin_render_paragraphs($text) {
    if (empty(trim($text ?? ''))) {
        return '';
    }
    if (strpos($text, '<p>') !== false) {
        return wp_kses_post($text);
    }
    $clean = str_replace(array("\r\n", "\r"), "\n", trim($text));
    $lines = preg_split('/\n+/', $clean);
    $output = '';
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line !== '') {
            $output .= '<p class="about-body-p">' . esc_html($line) . '</p>' . "\n";
        }
    }
    return $output;
}

// Default dummy images
$img_r1_default     = 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1000&q=80';
$img_r2_lg_default  = 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=800&q=80';
$img_r2_sm_default  = 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?auto=format&fit=crop&w=600&q=80';
$img_g1_default     = 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&w=800&q=80';
$img_g2_default     = 'https://images.unsplash.com/photo-1550966871-3ed3cdb5ed0c?auto=format&fit=crop&w=600&q=80';
$img_g3_default     = 'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=600&q=80';
$img_g4_default     = 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?auto=format&fit=crop&w=600&q=80';
$img_g5_default     = 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=600&q=80';
$img_g6_default     = 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&w=800&q=80';
$img_sp1_default    = 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?auto=format&fit=crop&w=600&q=80';
$img_sp2_default    = 'https://images.unsplash.com/photo-1563379091339-03b21ab4a4f8?auto=format&fit=crop&w=600&q=80';

// Row 1 values
$r1_title     = the_cochin_about_val('about_r1_title', "OUR\nSTORY");
$r1_p1        = the_cochin_about_val('about_r1_p1', '');
$r1_since     = the_cochin_about_val('about_r1_since', 'SINCE 2003');
$r1_image     = the_cochin_about_val('about_r1_image', $img_r1_default);

// Row 2 values
$r2_title     = the_cochin_about_val('about_r2_title', "OUR\nFOOD");
$r2_desc      = the_cochin_about_val('about_r2_desc', "Our menu brings together familiar South Indian favourites and dishes that reflect Kerala's distinctive regional character. Expect aromatic spices, coconut, curry leaves, seafood, rice, lentils and thoughtfully balanced flavours across vegetarian and non-vegetarian selections.");
$r2_img_lg    = the_cochin_about_val('about_r2_img_lg', $img_r2_lg_default);
$r2_img_sm    = the_cochin_about_val('about_r2_img_sm', $img_r2_sm_default);

// Row 3 (Gallery) values
$g_img1       = the_cochin_about_val('about_g_img1', $img_g1_default);
$g_img2       = the_cochin_about_val('about_g_img2', $img_g2_default);
$g_img3       = the_cochin_about_val('about_g_img3', $img_g3_default);
$g_img4       = the_cochin_about_val('about_g_img4', $img_g4_default);
$g_img5       = the_cochin_about_val('about_g_img5', $img_g5_default);
$g_img6       = the_cochin_about_val('about_g_img6', $img_g6_default);

// Row 4 (Specialties) values
$sp_badge     = the_cochin_about_val('about_sp_badge', '');
$sp_title     = the_cochin_about_val('about_sp_title', 'OUR HOSPITALITY');
$sp_dish1     = the_cochin_about_val('about_sp_dish1', $img_sp1_default);
$sp_dish2     = the_cochin_about_val('about_sp_dish2', $img_sp2_default);
$sp_content   = the_cochin_about_val('about_sp_content', 'Food is at the centre of family and community life in Kerala. We want every guest at The Cochin to feel comfortable, well looked after and welcome to return.');
?>

<div class="cochin-about-layout">

    <!-- =========================================================================
         BLOCK 1: Story Intro & "SINCE 2003" with Dining Photo
         ========================================================================= -->
    <section class="about-block-story">
        <div class="about-container">
            <div class="about-story-grid">
                <!-- Left: Headline, paragraphs -->
                <div class="about-story-text-col">
                    <h2 class="about-editorial-title">
                        <?php echo nl2br(esc_html($r1_title)); ?>
                    </h2>
                    <div class="about-story-paragraphs">
                        <?php echo the_cochin_render_paragraphs($r1_p1); ?>
                    </div>
                </div>

                <!-- Right: "SINCE 2003" Header line + Image -->
                <div class="about-story-media-col">
                    <div class="about-since-header">
                        <span class="about-since-text"><?php echo esc_html($r1_since); ?></span>
                        <span class="about-since-line" aria-hidden="true"></span>
                    </div>
                    <div class="about-rect-photo-wrap">
                        <img src="<?php echo esc_url($r1_image); ?>" alt="<?php echo esc_attr($r1_title); ?>" class="about-rect-photo" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         BLOCK 2: Overlapping Circular Images & "WE ALWAYS PREPARE THE MEALS FOR YOU"
         ========================================================================= -->
    <section class="about-block-overlap">
        <div class="about-container">
            <div class="about-overlap-grid">
                <!-- Left: Overlapping Circles -->
                <div class="about-overlap-media-col">
                    <div class="about-circles-composition">
                        <div class="about-circle-large-wrap">
                            <img src="<?php echo esc_url($r2_img_lg); ?>" alt="Signature Plated Meal" class="about-circle-img about-circle-lg" loading="lazy">
                        </div>
                        <div class="about-circle-small-wrap">
                            <img src="<?php echo esc_url($r2_img_sm); ?>" alt="Gourmet Dish" class="about-circle-img about-circle-sm" loading="lazy">
                        </div>
                    </div>
                </div>

                <!-- Right: Headline, description -->
                <div class="about-overlap-text-col">
                    <h2 class="about-editorial-title">
                        <?php echo nl2br(esc_html($r2_title)); ?>
                    </h2>
                    <div class="about-story-paragraphs">
                        <?php echo the_cochin_render_paragraphs($r2_desc); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         BLOCK 3: 4-Column Food & Dining Gallery Mosaic
         ========================================================================= -->
    <section class="about-block-gallery" aria-label="Restaurant Gallery">
        <div class="about-gallery-grid">
            <!-- Column 1: Tall Banquet Photo -->
            <div class="about-gallery-col col-tall col-1">
                <div class="about-gallery-item">
                    <img src="<?php echo esc_url($g_img1); ?>" alt="Dining with Wine and Hospitality" class="about-gallery-img" loading="lazy">
                </div>
            </div>

            <!-- Column 2: 2 Stacked Images -->
            <div class="about-gallery-col col-stacked col-2">
                <div class="about-gallery-item item-top">
                    <img src="<?php echo esc_url($g_img2); ?>" alt="Restaurant Ambiance & Architecture" class="about-gallery-img" loading="lazy">
                </div>
                <div class="about-gallery-item item-bottom">
                    <img src="<?php echo esc_url($g_img3); ?>" alt="Gourmet Soup and Starters" class="about-gallery-img" loading="lazy">
                </div>
            </div>

            <!-- Column 3: 2 Stacked Images -->
            <div class="about-gallery-col col-stacked col-3">
                <div class="about-gallery-item item-top">
                    <img src="<?php echo esc_url($g_img4); ?>" alt="Artisanal Dessert" class="about-gallery-img" loading="lazy">
                </div>
                <div class="about-gallery-item item-bottom">
                    <img src="<?php echo esc_url($g_img5); ?>" alt="Freshly Prepared Culinary Dishes" class="about-gallery-img" loading="lazy">
                </div>
            </div>

            <!-- Column 4: Tall Seafood Dish Photo -->
            <div class="about-gallery-col col-tall col-4">
                <div class="about-gallery-item">
                    <img src="<?php echo esc_url($g_img6); ?>" alt="Seafood Speciality with Mussels & Prawns" class="about-gallery-img" loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         BLOCK 4: "Our Specialties" Showcase (Direct Title & Content)
         ========================================================================= -->
    <section class="about-block-specialties" id="specialties">
        <div class="about-container">
            <!-- Centered Header -->
            <div class="about-specialties-header">
                <?php if (!empty($sp_badge)) : ?>
                    <span class="about-specialties-badge"><?php echo esc_html($sp_badge); ?></span>
                <?php endif; ?>
                <h2 class="about-specialties-title"><?php echo esc_html($sp_title); ?></h2>
            </div>

            <!-- Grid: 2 Floating Round Dishes (Left) + Content (Right) -->
            <div class="about-specialties-grid">
                <!-- Left: 2 Round Dish Photos Side by Side -->
                <div class="about-specialties-dishes-col">
                    <div class="about-specialties-dishes-wrap">
                        <div class="about-spec-dish-card">
                            <img src="<?php echo esc_url($sp_dish1); ?>" alt="Specialty Dish 1" class="about-spec-dish-img" loading="lazy">
                        </div>
                        <div class="about-spec-dish-card">
                            <img src="<?php echo esc_url($sp_dish2); ?>" alt="Specialty Dish 2" class="about-spec-dish-img" loading="lazy">
                        </div>
                    </div>
                </div>

                <!-- Right: Content -->
                <div class="about-specialties-content-col">
                    <div class="about-specialties-text">
                        <?php if (strpos($sp_content, '<p>') !== false) : ?>
                            <?php echo wp_kses_post($sp_content); ?>
                        <?php else : ?>
                            <p class="about-body-p"><?php echo nl2br(esc_html($sp_content)); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
