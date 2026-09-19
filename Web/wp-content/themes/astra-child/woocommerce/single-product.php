<?php
/**
 * The Cochin - Custom WooCommerce Single Product Page
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<div id="primary" <?php astra_primary_class(); ?>>
    <main id="main" class="site-main cochin-wc-single-main">
        <?php
        while (have_posts()) :
            the_post();
            wc_get_template_part('content', 'single-product');
        endwhile;
        ?>
    </main>
</div>

<?php
get_footer();
