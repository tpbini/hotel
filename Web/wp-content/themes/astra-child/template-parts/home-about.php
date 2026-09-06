<?php
/**
 * The Cochin - Homepage About Us Section
 *
 * Implements the About Us section matching the reference design:
 * - Background color: #F5EFE3
 * - Subtitle font: Architects Daughter with #9D6F2E color and side lines
 * - Main title: Welcome to The Cochin
 * - Left: Restaurant photo & awards badge composition
 * - Right: Brand narrative with subtle line-art food illustrations
 */

if (!defined('ABSPATH')) {
    exit;
}

$about_img = get_theme_mod('the_cochin_about_img');
if (empty($about_img)) {
    if (file_exists(get_stylesheet_directory() . '/assets/images/about-restaurant.png')) {
        $about_img = get_stylesheet_directory_uri() . '/assets/images/about-restaurant.png';
    } elseif (file_exists(get_stylesheet_directory() . '/assets/images/image 3.png')) {
        $about_img = get_stylesheet_directory_uri() . '/assets/images/image 3.png';
    } else {
        $about_img = get_stylesheet_directory_uri() . '/assets/images/cochin1.jpg';
    }
}
?>

<section class="cochin-about-section" id="about">
    <div class="cochin-about-container">
        <!-- Centered Section Header -->
        <div class="cochin-about-header">
            <div class="cochin-about-badge">
                <span class="cochin-badge-line"></span>
                <span class="cochin-badge-text">About us</span>
                <span class="cochin-badge-line"></span>
            </div>
            <h2 class="cochin-about-title">Welcome to The Cochin</h2>
        </div>

        <!-- Section Grid Content -->
        <div class="cochin-about-grid">
            <!-- Left: Restaurant Photo & Award Badges -->
            <div class="cochin-about-media-wrap">
                <div class="cochin-about-img-frame">
                    <img src="<?php echo esc_url($about_img); ?>" alt="The Cochin Restaurant Interior & Awards" class="cochin-about-main-img">
                </div>
            </div>

            <!-- Decorative Vertical Divider -->
            <div class="cochin-about-divider" aria-hidden="true"></div>

            <!-- Right: Story Narrative & Watermark Line Art -->
            <div class="cochin-about-text-wrap">
                <!-- Background Line Art from Docs/Images -->
                <div class="cochin-about-decor decor-top" aria-hidden="true">
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/Group 38.png'); ?>" alt="">
                </div>
                <div class="cochin-about-decor decor-bottom" aria-hidden="true">
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/Group 28.png'); ?>" alt="" class="decor-prawn">
                    <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/Group 26.png'); ?>" alt="" class="decor-meat">
                </div>

                <div class="cochin-about-paragraphs">
                    <p class="cochin-about-p">
                        Located in the heart of Hemel Hempstead&rsquo;s historic Old Town, The Cochin has been serving authentic Kerala and South Indian cuisine since 2003. Our menu is inspired by the food traditions of India&rsquo;s south-west coast, from delicately spiced vegetarian dishes and crisp dosas to rich curries, seafood specialities and comforting favourites.
                    </p>
                    <p class="cochin-about-p">
                        Whether you are joining us for a relaxed meal, celebrating with family and friends, collecting a takeaway or ordering for delivery, our team looks forward to welcoming you.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
