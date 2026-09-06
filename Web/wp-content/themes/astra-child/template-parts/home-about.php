<?php
/**
 * The Cochin - Homepage About Us Section
 *
 * Integrated with cochin.jpg (main restaurant photo),
 * cochinhemels.jpg (Certificate of Excellence),
 * and Travelers' Choice badge in responsive layout.
 */

if (!defined('ABSPATH')) {
    exit;
}

$main_img_url = get_stylesheet_directory_uri() . '/assets/images/cochin.jpg';
$cert_img_url = get_stylesheet_directory_uri() . '/assets/images/cochinhemels.jpg';
$ta_img_url   = get_stylesheet_directory_uri() . '/assets/images/travelers-choice-2022.png';
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
            <!-- Left: Composite Photo with Awards -->
            <div class="cochin-about-media-wrap">
                <div class="cochin-about-img-frame">
                    <!-- Main Restaurant Photo (cochin.jpg) -->
                    <img src="<?php echo esc_url($main_img_url); ?>" alt="The Cochin Indian Restaurant Hemel Hempstead" class="cochin-about-main-img">

                    <!-- Overlay Badge 1: Certificate of Excellence (cochinhemels.jpg) -->
                    <div class="cochin-badge-cert-wrap">
                        <img src="<?php echo esc_url($cert_img_url); ?>" alt="Certificate of Excellence by Restaurantji - The Cochin Indian Restaurant" class="cochin-badge-cert-img">
                    </div>

                    <!-- Overlay Badge 2: Travelers' Choice 2022 -->
                    <div class="cochin-badge-ta-wrap">
                        <img src="<?php echo esc_url($ta_img_url); ?>" alt="TripAdvisor Travelers' Choice 2022 - The Cochin" class="cochin-badge-ta-img">
                    </div>
                </div>
            </div>

            <!-- Decorative Vertical Separator Line -->
            <div class="cochin-about-divider" aria-hidden="true"></div>

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
