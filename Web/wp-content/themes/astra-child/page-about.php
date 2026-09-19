<?php
/**
 * Template Name: About Us Page
 *
 * Description: Custom About Us page template with dedicated header banner and 4-tier editorial layout.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<div id="primary" <?php astra_primary_class(); ?>>
    <main id="main" class="site-main">
        <?php
        // Render 4-tier editorial layout under the header banner
        get_template_part('template-parts/about-content-layout');
        ?>
    </main>
</div>

<?php
get_footer();
