<?php
/**
 * Template Name: Book a Table Page
 *
 * Description: Custom Reservation page template with dedicated Poppins header banner, intro details, reservation form, and policy notice.
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header(); ?>

<div id="primary" <?php astra_primary_class(); ?>>
    <main id="main" class="site-main">
        <?php
        get_template_part('template-parts/book-table-content');
        ?>
    </main>
</div>

<?php
get_footer();
