<?php
/**
 * The Cochin - Homepage About Us Section
 *
 * Implements the About Us section with about.jpg from Docs/Images,
 * Architects Daughter font for "About us" (#9D6F2E), and #F5EFE3 background.
 */

if (!defined('ABSPATH')) {
    exit;
}

$about_img_url = !empty($args['image']) ? $args['image'] : (
    file_exists(get_stylesheet_directory() . '/assets/images/about.jpg')
        ? get_stylesheet_directory_uri() . '/assets/images/about.jpg'
        : get_stylesheet_directory_uri() . '/assets/images/about.png'
);

$about_badge = !empty($args['badge']) ? $args['badge'] : 'About us';
$about_title = !empty($args['title']) ? $args['title'] : 'Welcome to The Cochin';
$about_content = !empty($args['content']) ? $args['content'] : '';
?>

<section class="cochin-about-section" id="about">
    <div class="cochin-about-container">
        <!-- Centered Section Header -->
        <div class="cochin-about-header">
            <div class="cochin-about-badge">
                <span class="cochin-badge-line"></span>
                <span class="cochin-badge-text"><?php echo esc_html($about_badge); ?></span>
                <span class="cochin-badge-line"></span>
            </div>
            <h2 class="cochin-about-title"><?php echo esc_html($about_title); ?></h2>
        </div>

        <!-- Section Grid Content (Responsive 2-Column Desktop / 1-Column Mobile) -->
        <div class="cochin-about-grid">
            <!-- Left: about.jpg Image Area -->
            <div class="cochin-about-media-wrap">
                <img src="<?php echo esc_url($about_img_url); ?>" alt="<?php echo esc_attr($about_title); ?> - Hemel Hempstead" class="cochin-about-composite-img">
            </div>

            <!-- Right: Story Narrative & Watermark Line Art -->
            <div class="cochin-about-text-wrap">
                <!-- Background Line-Art Food Illustrations -->
                <div class="cochin-about-decor decor-top" aria-hidden="true">
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/Group 38.png'); ?>" alt="">
                </div>
                <div class="cochin-about-decor decor-bottom" aria-hidden="true">
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/Group 28.png'); ?>" alt="" class="decor-prawn">
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/Group 26.png'); ?>" alt="" class="decor-meat">
                </div>

                <div class="cochin-about-paragraphs">
                    <?php if (!empty($about_content)) : ?>
                        <?php echo wpautop(wp_kses_post($about_content)); ?>
                    <?php else : ?>
                        <p class="cochin-about-p">
                            Located in the heart of Hemel Hempstead&rsquo;s historic Old Town, The Cochin has been serving authentic Kerala and South Indian cuisine since 2003. Our menu is inspired by the food traditions of India&rsquo;s south-west coast, from delicately spiced vegetarian dishes and crisp dosas to rich curries, seafood specialities and comforting favourites.
                        </p>
                        <p class="cochin-about-p">
                            Whether you are joining us for a relaxed meal, celebrating with family and friends, collecting a takeaway or ordering for delivery, our team looks forward to welcoming you.
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
