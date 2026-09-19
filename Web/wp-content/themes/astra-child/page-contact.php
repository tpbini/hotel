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
        get_template_part('template-parts/contact-content');
        ?>
    </main>
</div>

<?php
get_footer();
