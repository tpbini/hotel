<?php
/**
 * The Cochin - Front Page Template (ACF Powered)
 *
 * Renders all homepage sections using ACF custom fields.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div id="primary" <?php astra_primary_class(); ?>>
    <main id="main" class="site-main">
        <?php
        get_template_part('template-parts/home-hero');
        get_template_part('template-parts/home-about');
        get_template_part('template-parts/home-menu-carousel');
        get_template_part('template-parts/home-banquet-meals');
        get_template_part('template-parts/home-delivery');
        get_template_part('template-parts/home-dietary');
        get_template_part('template-parts/home-faq');
        ?>
    </main>
</div>

<?php
get_footer();
