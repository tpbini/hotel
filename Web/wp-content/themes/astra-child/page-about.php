<?php
/**
 * Template Name: About Us Page
 *
 * Description: Custom About Us page template with dedicated #F5EFE3 header banner.
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
            get_template_part('template-parts/content', 'page');
        endwhile;
        ?>
    </main>
</div>

<?php
get_footer();
