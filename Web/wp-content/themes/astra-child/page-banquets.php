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
        get_template_part('template-parts/banquet-content');
        ?>
    </main>
</div>

<?php
get_footer();
