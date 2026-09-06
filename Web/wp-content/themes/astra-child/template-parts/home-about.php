<?php
/**
 * The Cochin - Homepage About Us Section
 *
 * Implements the About Us section with the requested high-res composite image
 * (framed restaurant photo, Certificate of Excellence, Travelers' Choice badge, and gold line),
 * Architects Daughter font for "About us", #9D6F2E side lines, and #F5EFE3 background.
 */

if (!defined('ABSPATH')) {
    exit;
}

$composite_img_url = get_stylesheet_directory_uri() . '/assets/images/about-awards-composite.png';
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
            <!-- Left: Attached Image Section (no_bg.png / about-awards-composite.png) -->
            <div class="cochin-about-media-wrap">
                <img src="<?php echo esc_url($composite_img_url); ?>" alt="The Cochin Restaurant Interior, Certificate of Excellence, and Travelers' Choice Award" class="cochin-about-composite-img">
            </div>

            <!-- Right: Story Narrative & Background Line-Art -->
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
