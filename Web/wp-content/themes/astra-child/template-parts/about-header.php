<?php
/**
 * Template Part: About Us / Inner Page Header Banner
 *
 * Background Color: #F5EFE3
 * Background Illustrations: Group 26.png (left), Group 28.png (right)
 * Typography: Poppins
 */

if (!defined('ABSPATH')) {
    exit;
}

$img_base = get_stylesheet_directory_uri() . '/assets/images/';
$left_art  = $img_base . 'Group 26.png';
$right_art = $img_base . 'Group 28.png';

$title = isset($args['title']) ? $args['title'] : 'About Us';
$breadcrumb = isset($args['breadcrumb']) ? $args['breadcrumb'] : 'About us';
?>
<section class="page-header-banner page-header-banner--about" aria-label="<?php echo esc_attr($title); ?> Header">
    <div class="page-header-banner__bg-art page-header-banner__bg-art--left" aria-hidden="true">
        <img src="<?php echo esc_url($left_art); ?>" alt="" loading="eager">
    </div>

    <div class="page-header-banner__container">
        <h1 class="page-header-banner__title"><?php echo esc_html($title); ?></h1>
        <nav class="page-header-banner__breadcrumbs" aria-label="Breadcrumb">
            <a href="<?php echo esc_url(home_url('/')); ?>" class="breadcrumb-link">Home</a>
            <span class="breadcrumb-separator">/</span>
            <span class="breadcrumb-current"><?php echo esc_html($breadcrumb); ?></span>
        </nav>
    </div>

    <div class="page-header-banner__bg-art page-header-banner__bg-art--right" aria-hidden="true">
        <img src="<?php echo esc_url($right_art); ?>" alt="" loading="eager">
    </div>
</section>
