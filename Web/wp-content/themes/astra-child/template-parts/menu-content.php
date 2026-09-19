<?php
/**
 * The Cochin - Dynamic Menu Page Content Layout
 *
 * Fully integrated with WooCommerce, WordPress Page Editor & ACF:
 * - Dynamic Categories and Products queried live from WooCommerce (Products > Add New / All Products)
 * - Editable Intro, Notice, Offer, and Callout via ACF / Page Settings
 * - Quick Navigation Category Pills with Scrollspy
 * - Live Instant Search & Vegetarian Filter (🌱 Veg Only)
 * - 100% Full-Width Single-Column Dishes with AJAX Add to Cart & Poppins Typography
 */

if (!defined('ABSPATH')) {
    exit;
}

// Support arguments passed from shortcode or template part
$args = isset($args) && is_array($args) ? $args : array();

// ACF / Configurable Values with Fallback Defaults
$menu_badge     = !empty($args['badge']) ? $args['badge'] : (function_exists('get_field') && get_field('menu_page_badge') ? get_field('menu_page_badge') : __('DISCOVER OUR FOOD', 'astra-child'));
$menu_heading   = !empty($args['heading']) ? $args['heading'] : (function_exists('get_field') && get_field('menu_page_heading') ? get_field('menu_page_heading') : __('Explore the Flavours of Kerala & South India', 'astra-child'));
$menu_intro     = !empty($args['intro']) ? $args['intro'] : (function_exists('get_field') && get_field('menu_page_intro') ? get_field('menu_page_intro') : __('Explore the flavours of Kerala and South India. Our menu includes starters, dosas, seafood, meat and poultry dishes, vegetarian and vegan choices, rice dishes, breads, desserts and drinks.', 'astra-child'));

$allergen_show  = isset($args['show_allergen']) ? (bool)$args['show_allergen'] : (function_exists('get_field') ? get_field('menu_allergen_show') !== false : true);
$allergen_title = function_exists('get_field') && get_field('menu_allergen_title') ? get_field('menu_allergen_title') : __('Dietary and Allergen Notice', 'astra-child');
$allergen_desc  = function_exists('get_field') && get_field('menu_allergen_desc') ? get_field('menu_allergen_desc') : __('Please tell a member of our team about any allergy or dietary requirement before ordering. Our dishes are prepared in a kitchen where allergens are handled, and cross-contact may occur. Please ask for our current allergen information.', 'astra-child');

$offer_show     = isset($args['show_offer']) ? (bool)$args['show_offer'] : (function_exists('get_field') ? get_field('menu_offer_show') !== false : true);
$offer_badge    = function_exists('get_field') && get_field('menu_offer_badge') ? get_field('menu_offer_badge') : __('SPECIAL OFFER', 'astra-child');
$offer_text     = function_exists('get_field') && get_field('menu_offer_text') ? get_field('menu_offer_text') : __('<strong>15% Off</strong> on all collection orders over £25 when ordered online.', 'astra-child');
$offer_btn_text = function_exists('get_field') && get_field('menu_offer_btn_text') ? get_field('menu_offer_btn_text') : __('ORDER TAKEAWAY', 'astra-child');
$offer_btn_url  = function_exists('get_field') && get_field('menu_offer_btn_url') ? get_field('menu_offer_btn_url') : home_url('/#order');

$callout_show     = function_exists('get_field') ? get_field('menu_callout_show') !== false : true;
$callout_title    = function_exists('get_field') && get_field('menu_callout_title') ? get_field('menu_callout_title') : __('Ready to Experience Cochin Flavours?', 'astra-child');
$callout_text     = function_exists('get_field') && get_field('menu_callout_text') ? get_field('menu_callout_text') : __('Reserve a table at our historic Old Town restaurant or order online for collection & delivery.', 'astra-child');
$callout_btn1_txt = function_exists('get_field') && get_field('menu_callout_btn1_text') ? get_field('menu_callout_btn1_text') : __('BOOK A TABLE', 'astra-child');
$callout_btn1_url = function_exists('get_field') && get_field('menu_callout_btn1_url') ? get_field('menu_callout_btn1_url') : (function_exists('the_cochin_get_booking_url') ? the_cochin_get_booking_url() : home_url('/book-table/'));
$callout_btn2_txt = function_exists('get_field') && get_field('menu_callout_btn2_text') ? get_field('menu_callout_btn2_text') : __('ORDER ONLINE', 'astra-child');
$callout_btn2_url = function_exists('get_field') && get_field('menu_callout_btn2_url') ? get_field('menu_callout_btn2_url') : home_url('/#order');

// Fetch Dynamic Categories and Items from WooCommerce / Database
$categories = function_exists('the_cochin_get_menu_categories') ? the_cochin_get_menu_categories() : array();

// If filtered by specific category slug via shortcode
if (!empty($args['category']) && isset($categories[$args['category']])) {
    $categories = array($args['category'] => $categories[$args['category']]);
}
?>

<div class="cochin-menu-layout">

    <!-- =========================================================================
         TOP SECTION: Introduction Banner & Dietary / Allergen Notice Card
         ========================================================================= -->
    <div class="menu-intro-allergen-section">
        <div class="menu-container">
            
            <!-- Introduction Box -->
            <div class="menu-intro-card">
                <?php if (!empty($menu_badge)) : ?>
                    <div class="menu-intro-badge"><?php echo esc_html($menu_badge); ?></div>
                <?php endif; ?>
                <h1 class="menu-intro-heading"><?php echo esc_html($menu_heading); ?></h1>
                <?php if (!empty($menu_intro)) : ?>
                    <p class="menu-intro-text"><?php echo wp_kses_post($menu_intro); ?></p>
                <?php endif; ?>
            </div>

            <!-- Dietary & Allergen Notice Card -->
            <?php if ($allergen_show) : ?>
                <div class="menu-allergen-card" role="note" aria-label="<?php esc_attr_e('Dietary and allergen notice', 'astra-child'); ?>">
                    <div class="menu-allergen-icon-wrap" aria-hidden="true">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <div class="menu-allergen-content">
                        <h2 class="menu-allergen-title"><?php echo esc_html($allergen_title); ?></h2>
                        <p class="menu-allergen-desc"><?php echo esc_html($allergen_desc); ?></p>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- =========================================================================
         STICKY BAR: 9 Quick Navigation Category Pills & Search / Filter Controls
         ========================================================================= -->
    <div class="menu-sticky-nav-bar">
        <div class="menu-container">
            <div class="menu-nav-scroller">
                <nav class="menu-category-pills" aria-label="<?php esc_attr_e('Menu Categories', 'astra-child'); ?>">
                    <?php 
                    $first_pill = true;
                    foreach ($categories as $cat) : 
                    ?>
                        <a href="#<?php echo esc_attr($cat['id']); ?>" class="menu-pill <?php echo $first_pill ? 'active' : ''; ?>">
                            <?php echo esc_html($cat['title']); ?>
                        </a>
                    <?php 
                        $first_pill = false;
                    endforeach; 
                    ?>
                </nav>
            </div>

            <div class="menu-controls-bar">
                <!-- Search without remove button -->
                <div class="menu-search-wrap">
                    <input type="text" id="menuSearchInput" class="menu-search-input" placeholder="<?php esc_attr_e('Search dishes...', 'astra-child'); ?>" autocomplete="off" aria-label="<?php esc_attr_e('Search menu dishes', 'astra-child'); ?>">
                    <svg class="menu-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                </div>

                <!-- Veg Filter Toggle -->
                <label class="menu-veg-filter-label" for="vegFilterCheckbox">
                    <input type="checkbox" id="vegFilterCheckbox" class="menu-veg-checkbox">
                    <span class="menu-veg-slider"></span>
                    <span class="menu-veg-text">🌱 <?php esc_html_e('Veg Only', 'astra-child'); ?></span>
                </label>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         SPECIAL OFFER BANNER
         ========================================================================= -->
    <?php if ($offer_show) : ?>
        <div class="menu-container">
            <div class="menu-offer-banner">
                <div class="menu-offer-badge"><?php echo esc_html($offer_badge); ?></div>
                <div class="menu-offer-text">
                    <?php echo wp_kses_post($offer_text); ?>
                </div>
                <a href="<?php echo esc_url($offer_btn_url); ?>" class="menu-offer-btn"><?php echo esc_html($offer_btn_text); ?></a>
            </div>
        </div>
    <?php endif; ?>

    <!-- =========================================================================
         DYNAMIC MENU CATEGORIES & DISHES (LIVE FROM WOOCOMMERCE & DATABASE)
         ========================================================================= -->
    <div class="menu-categories-wrapper">
        <?php foreach ($categories as $cat) : 
            if (empty($cat['items'])) {
                continue;
            }
        ?>
            <section class="menu-category-section" id="<?php echo esc_attr($cat['id']); ?>" data-category="<?php echo esc_attr($cat['id']); ?>">
                <div class="menu-container">
                    
                    <!-- CATEGORY HEADER (100% Full Width) -->
                    <div class="menu-section-header-fullwidth">
                        <h2 class="menu-editorial-title"><?php echo esc_html($cat['title']); ?></h2>
                        <?php if (!empty($cat['subhead'])) : ?>
                            <span class="menu-category-subhead"><?php echo esc_html($cat['subhead']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($cat['intro'])) : ?>
                            <p class="menu-category-intro"><?php echo esc_html($cat['intro']); ?></p>
                        <?php endif; ?>
                        <div class="menu-header-divider-line" aria-hidden="true"></div>
                    </div>

                    <!-- 100% FULL-WIDTH SINGLE COLUMN FOOD ITEMS LIST -->
                    <div class="menu-dishes-container-fullwidth">
                        <div class="menu-dishes-list-singlecol">
                            <?php foreach ($cat['items'] as $item) : 
                                $is_veg = !empty($item['tags']) && (in_array('Veg', $item['tags'], true) || in_array('Vegan', $item['tags'], true));
                                $product_id = !empty($item['product_id']) ? (int)$item['product_id'] : 0;
                                $product_url = !empty($item['url']) ? $item['url'] : ($product_id ? get_permalink($product_id) : '#');
                            ?>
                                <div class="menu-dish-item" data-veg="<?php echo $is_veg ? 'true' : 'false'; ?>">
                                    <div class="menu-dish-row-layout">
                                        <!-- Left / Main: Dish Name, Tags, Description -->
                                        <div class="menu-dish-main-info">
                                            <div class="menu-dish-title-wrap">
                                                <?php if ($product_id && $product_url !== '#') : ?>
                                                    <a href="<?php echo esc_url($product_url); ?>" class="menu-dish-title-link" title="<?php echo esc_attr(sprintf(__('View details for %s', 'astra-child'), $item['title'])); ?>">
                                                        <h3 class="menu-dish-name"><?php echo esc_html($item['title']); ?></h3>
                                                    </a>
                                                <?php else : ?>
                                                    <h3 class="menu-dish-name"><?php echo esc_html($item['title']); ?></h3>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($item['tags'])) : ?>
                                                    <div class="menu-dish-tags">
                                                        <?php foreach ($item['tags'] as $tag) : 
                                                            $badge_class = 'badge-tag-' . strtolower(sanitize_html_class($tag));
                                                        ?>
                                                            <span class="menu-badge <?php echo esc_attr($badge_class); ?>">
                                                                <?php echo esc_html($tag); ?>
                                                            </span>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <?php if (!empty($item['desc'])) : ?>
                                                <p class="menu-dish-desc"><?php echo esc_html($item['desc']); ?></p>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Right: Price & Add to Cart Button -->
                                        <div class="menu-dish-action-side">
                                            <span class="menu-dish-price"><?php echo esc_html($item['price']); ?></span>
                                            <?php if ($product_id) : ?>
                                                <button type="button" class="cochin-add-cart-btn" data-product-id="<?php echo esc_attr($product_id); ?>" aria-label="<?php echo esc_attr(sprintf(__('Add %s to cart', 'astra-child'), $item['title'])); ?>">
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                                                    <span><?php esc_html_e('Add to cart', 'astra-child'); ?></span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                </div>
            </section>
        <?php endforeach; ?>
    </div>

    <!-- =========================================================================
         BOTTOM BOOKING & ORDER CALLOUT
         ========================================================================= -->
    <?php if ($callout_show) : ?>
        <section class="menu-bottom-callout">
            <div class="menu-container">
                <div class="menu-callout-box">
                    <h3 class="menu-callout-title"><?php echo esc_html($callout_title); ?></h3>
                    <p class="menu-callout-text"><?php echo esc_html($callout_text); ?></p>
                    <div class="menu-callout-actions">
                        <?php if (!empty($callout_btn1_txt)) : ?>
                            <a href="<?php echo esc_url($callout_btn1_url); ?>" class="menu-btn-primary"><?php echo esc_html($callout_btn1_txt); ?></a>
                        <?php endif; ?>
                        <?php if (!empty($callout_btn2_txt)) : ?>
                            <a href="<?php echo esc_url($callout_btn2_url); ?>" class="menu-btn-secondary"><?php echo esc_html($callout_btn2_txt); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endif; ?>

</div>

<!-- Inline JavaScript for Menu Search, Veg Filter, Add-to-Cart AJAX, and Smooth Category Sticky Pill Tabs -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('menuSearchInput');
    const vegCheckbox = document.getElementById('vegFilterCheckbox');
    const categorySections = document.querySelectorAll('.menu-category-section');
    const navPills = document.querySelectorAll('.menu-pill');

    function filterDishes() {
        const query = (searchInput ? searchInput.value : '').toLowerCase().trim();
        const vegOnly = vegCheckbox ? vegCheckbox.checked : false;

        categorySections.forEach(section => {
            const dishes = section.querySelectorAll('.menu-dish-item');
            let visibleCount = 0;

            dishes.forEach(dish => {
                const title = dish.querySelector('.menu-dish-name')?.textContent.toLowerCase() || '';
                const desc = dish.querySelector('.menu-dish-desc')?.textContent.toLowerCase() || '';
                const isVeg = dish.getAttribute('data-veg') === 'true';

                const matchesQuery = !query || title.includes(query) || desc.includes(query);
                const matchesVeg = !vegOnly || isVeg;

                if (matchesQuery && matchesVeg) {
                    dish.style.display = 'block';
                    visibleCount++;
                } else {
                    dish.style.display = 'none';
                }
            });

            if (visibleCount === 0) {
                section.style.display = 'none';
            } else {
                section.style.display = 'block';
            }
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterDishes);
    }
    if (vegCheckbox) {
        vegCheckbox.addEventListener('change', filterDishes);
    }

    // Scrollspy for active pill navigation
    window.addEventListener('scroll', function() {
        let scrollPos = window.scrollY + 180;
        categorySections.forEach(sec => {
            if (sec.style.display !== 'none') {
                const top = sec.offsetTop;
                const height = sec.offsetHeight;
                const id = sec.getAttribute('id');
                if (scrollPos >= top && scrollPos < top + height) {
                    navPills.forEach(pill => {
                        pill.classList.remove('active');
                        if (pill.getAttribute('href') === '#' + id) {
                            pill.classList.add('active');
                        }
                    });
                }
            }
        });
    }, { passive: true });

    // Smooth scroll on pill click
    navPills.forEach(pill => {
        pill.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetEl = document.querySelector(targetId);
            if (targetEl) {
                const offset = 140;
                const bodyRect = document.body.getBoundingClientRect().top;
                const elementRect = targetEl.getBoundingClientRect().top;
                const elementPosition = elementRect - bodyRect;
                const offsetPosition = elementPosition - offset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                navPills.forEach(p => p.classList.remove('active'));
                this.classList.add('active');
            }
        });
    });

    // AJAX Add-To-Cart Handler
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.cochin-add-cart-btn');
        if (!btn) return;
        e.preventDefault();

        const productId = btn.getAttribute('data-product-id');
        if (!productId) return;

        const originalHtml = btn.innerHTML;
        btn.classList.add('is-loading');
        btn.innerHTML = '<span>Adding...</span>';

        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', 1);

        const cartUrl = <?php echo json_encode(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/')); ?>;

        fetch(cartUrl + '?wc-ajax=add_to_cart', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            btn.classList.remove('is-loading');
            btn.classList.add('is-added');
            btn.innerHTML = '<span>✓ Added!</span>';

            if (data && data.fragments) {
                Object.keys(data.fragments).forEach(key => {
                    const elems = document.querySelectorAll(key);
                    elems.forEach(el => {
                        const temp = document.createElement('div');
                        temp.innerHTML = data.fragments[key];
                        if (temp.firstElementChild) {
                            el.replaceWith(temp.firstElementChild);
                        }
                    });
                });
            }

            setTimeout(() => {
                btn.classList.remove('is-added');
                btn.innerHTML = originalHtml;
            }, 2000);
        })
        .catch(() => {
            window.location.href = '?add-to-cart=' + productId;
        });
    });
});
</script>
