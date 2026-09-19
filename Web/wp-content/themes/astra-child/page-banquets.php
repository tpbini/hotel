<?php
/**
 * Template Name: Banquets Page
 *
 * Description: Custom Banquets page template with dedicated Poppins header banner, Feast specifications, and Inquiry form.
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

            // If user added shortcode [the_cochin_banquets] or [the_cochin_banquet_page], render the post content
            if (has_shortcode($content, 'the_cochin_banquets') || has_shortcode($content, 'the_cochin_banquet_page')) {
                the_content();
            } else {
                // If there's custom content/blocks in the editor, output it seamlessly
                if (!empty(trim($content))) {
                    echo '<div class="cochin-banquet-custom-page-content entry-content"><div class="banquet-container" style="padding-top: 24px; padding-bottom: 24px;">';
                    the_content();
                    echo '</div></div>';
                }

                // Render the Banquets editorial layout
                get_template_part('template-parts/banquet-content');
            }
        endwhile;
        ?>
    </main>
</div>

<?php
get_footer();
