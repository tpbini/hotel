<?php
/**
 * The Cochin - Homepage Menu Carousel Section
 *
 * Implements the auto-scrolling interactive card carousel:
 * - Background gradient: #ffffff to #f5efe3 (hover: #dfa97c to #f5efe3)
 * - Categories: Starters, Dosa & South Indian Classics, Seafood, Meat & Poultry,
 *   Vegetarian & Vegan, Rice & Biryani, Breads & Sides, Desserts, Drinks
 * - Auto-scroll right to left with pause on hover and touch swipe support
 * - Direct deep-links to menu page sections
 */

if (!defined('ABSPATH')) {
    exit;
}

$menu_items = the_cochin_get_menu_carousel_items();
$menu_url   = the_cochin_get_menu_page_url();
?>

<section class="cochin-menu-carousel-section" id="menu">
    <div class="cochin-carousel-container">
        <!-- Section Header -->
        <div class="cochin-carousel-header">
            <div class="cochin-carousel-badge">
                <span class="cochin-badge-text">Our Menu</span>
                <span class="cochin-badge-line"></span>
            </div>
            <h2 class="cochin-carousel-title">Explore our menu</h2>
        </div>

        <!-- Carousel Outer Wrapper with Navigation Arrows -->
        <div class="cochin-carousel-wrap">
            <button class="cochin-carousel-arrow arrow-prev" type="button" aria-label="Previous menu items">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
            </button>

            <!-- Scrollable Track -->
            <div class="cochin-carousel-track" id="menuCarouselTrack" tabindex="0" role="region" aria-label="Menu categories carousel">
                <?php foreach ($menu_items as $index => $item) : ?>
                    <a href="<?php echo esc_url($item['url']); ?>" class="cochin-menu-card" data-index="<?php echo esc_attr($index); ?>">
                        <div class="cochin-card-header">
                            <h3 class="cochin-card-title"><?php echo esc_html($item['title']); ?></h3>
                            <?php if (!empty($item['count'])) : ?>
                                <span class="cochin-card-count"><?php echo esc_html($item['count']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="cochin-card-media">
                            <img src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['title']); ?>" class="cochin-card-img" loading="lazy">
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <button class="cochin-carousel-arrow arrow-next" type="button" aria-label="Next menu items">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 18 15 12 9 6"></polyline>
                </svg>
            </button>
        </div>

        <!-- Centered View Full Menu Button -->
        <div class="cochin-carousel-footer">
            <a href="<?php echo esc_url($menu_url); ?>" class="cochin-view-full-menu-btn">
                <?php esc_html_e('VIEW FULL MENU', 'astra-child'); ?>
            </a>
        </div>
    </div>
</section>
