<?php
/**
 * Template Name: Menu Page
 *
 * Description: Custom Menu page template with dedicated Poppins header banner and editorial category layout.
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

            // If user has added shortcode [the_cochin_menu] in editor, render the post content directly
            if (has_shortcode($content, 'the_cochin_menu')) {
                the_content();
            } else {
                // If there's custom content/blocks in the editor, output it seamlessly
                if (!empty(trim($content))) {
                    echo '<div class="cochin-menu-custom-page-content entry-content"><div class="menu-container" style="padding-top: 24px; padding-bottom: 24px;">';
                    the_content();
                    echo '</div></div>';
                }

                // Render the Menu Editorial Layout with dynamic WooCommerce categories and items
                get_template_part('template-parts/menu-content');
            }
        endwhile;
        ?>
    </main>
</div>

<?php
get_footer();
