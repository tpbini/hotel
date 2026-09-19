<?php
/**
 * Template Name: Contact Page
 *
 * Description: Custom Contact Page template for The Cochin featuring interactive inquiry form,
 * direct Google Maps embed, operating hours, travel & parking guide, and direct booking links.
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

            // If user added shortcode [the_cochin_contact_page], render the post content
            if (has_shortcode($content, 'the_cochin_contact_page') || has_shortcode($content, 'the_cochin_contact')) {
                the_content();
            } else {
                // If there's custom content/blocks in the editor, output it seamlessly
                if (!empty(trim($content))) {
                    echo '<div class="cochin-contact-custom-page-content entry-content"><div class="cochin-contact-container" style="padding-top: 24px; padding-bottom: 24px;">';
                    the_content();
                    echo '</div></div>';
                }

                // Render the Contact editorial layout
                get_template_part('template-parts/contact-content');
            }
        endwhile;
        ?>
    </main>
</div>

<?php
get_footer();
