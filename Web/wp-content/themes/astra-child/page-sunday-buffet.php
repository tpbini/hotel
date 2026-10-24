<?php
/**
 * Template Name: Sunday Brunch Buffet Page
 *
 * Description: Dedicated page template for The Cochin's Sunday Brunch Buffet with weekly menu showcase, pricing tiers, and direct table booking.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<div id="primary" <?php astra_primary_class(); ?>>
    <main id="main" class="site-main">
        <?php
        while (have_posts()) :
            the_post();

            $content = get_the_content();

            // If user added shortcode [the_cochin_sunday_buffet], render the post content
            if (has_shortcode($content, 'the_cochin_sunday_buffet')) {
                the_content();
            } else {
                // If there's custom content/blocks in the editor, output it seamlessly above the editorial template
                if (!empty(trim($content))) {
                    echo '<div class="cochin-buffet-custom-page-content entry-content"><div class="buffet-container" style="padding-top: 24px; padding-bottom: 24px;">';
                    the_content();
                    echo '</div></div>';
                }

                // Render the complete Sunday Brunch Buffet editorial layout
                get_template_part('template-parts/sunday-buffet-content');
            }
        endwhile;
        ?>
    </main>
</div>

<?php
get_footer();
