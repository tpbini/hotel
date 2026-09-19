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
        // Render the Menu Editorial Layout with categories from https://hp1.thecochin.uk/
        get_template_part('template-parts/menu-content');
        ?>
    </main>
</div>

<?php
get_footer();
