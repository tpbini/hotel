<?php
/**
 * The Cochin - Custom WooCommerce Single Product Content Layout
 */

if (!defined('ABSPATH')) {
    exit;
}

global $product;

if (empty($product) || !is_a($product, 'WC_Product')) {
    $product = wc_get_product(get_the_ID());
}

if (!$product) {
    return;
}

$product_id = $product->get_id();
$title      = $product->get_name();
$price_html = $product->get_price_html();
$short_desc = $product->get_short_description();
$full_desc  = $product->get_description();
$desc       = !empty($short_desc) ? $short_desc : $full_desc;

// Dietary tags
$dietary_raw = get_post_meta($product_id, '_the_cochin_dietary', true);
$dietary_tags = !empty($dietary_raw) ? explode(',', $dietary_raw) : array();
if (empty($dietary_tags)) {
    $terms = get_the_terms($product_id, 'product_tag');
    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $t) {
            $dietary_tags[] = $t->name;
        }
    }
}

// Category
$categories_terms = get_the_terms($product_id, 'product_cat');
$cat_name = '';
$cat_id = 0;
if (!empty($categories_terms) && !is_wp_error($categories_terms)) {
    $first_cat = reset($categories_terms);
    $cat_name = $first_cat->name;
    $cat_id = $first_cat->term_id;
}

// Category image map
$dummy_cat_images = array(
    'TRAY' => 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?auto=format&fit=crop&w=1000&q=80',
    'STARTERS' => 'https://images.unsplash.com/photo-1601050690597-df0568f70950?auto=format&fit=crop&w=1000&q=80',
    'DOSA' => 'https://images.unsplash.com/photo-1668236543090-82eba5ee5976?auto=format&fit=crop&w=1000&q=80',
    'FISHERMAN\'S FAVOURITE' => 'https://images.unsplash.com/photo-1534422298391-e4f8c172dddb?auto=format&fit=crop&w=1000&q=80',
    'MEAT & POULTRY' => 'https://images.unsplash.com/photo-1545247181-516773cae754?auto=format&fit=crop&w=1000&q=80',
    'VEGETARIAN DISHES' => 'https://images.unsplash.com/photo-1540420773420-3366772f4999?auto=format&fit=crop&w=1000&q=80',
    'SIDE ORDERS' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?auto=format&fit=crop&w=1000&q=80',
    'RICE' => 'https://images.unsplash.com/photo-1516714435131-44d6b64dc6a2?auto=format&fit=crop&w=1000&q=80',
    'BREAD' => 'https://images.unsplash.com/photo-1589301760014-d929f3979dbc?auto=format&fit=crop&w=1000&q=80',
);

$image_url = '';
if (has_post_thumbnail($product_id)) {
    $image_url = get_the_post_thumbnail_url($product_id, 'large');
} else {
    $image_url = isset($dummy_cat_images[strtoupper($cat_name)]) ? $dummy_cat_images[strtoupper($cat_name)] : 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?auto=format&fit=crop&w=1000&q=80';
}

// Related products
$related_products = array();
if ($cat_id) {
    $related_products = wc_get_products(array(
        'category' => array($cat_name),
        'exclude'  => array($product_id),
        'limit'    => 4,
    ));
}
?>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class('cochin-product-page-wrapper', $product); ?>>

    <div class="product-container">
        
        <!-- Back link -->
        <div class="product-back-nav">
            <a href="<?php echo esc_url(the_cochin_get_menu_page_url()); ?>" class="product-back-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                <span>Back to Full Menu</span>
            </a>
        </div>

        <!-- Main Product Grid (Left: Image, Right: Details) -->
        <div class="product-showcase-grid">
            
            <!-- Left: Product Image Card -->
            <div class="product-media-col">
                <div class="product-image-card">
                    <img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr($title); ?>" class="product-featured-img">
                    <?php if (!empty($dietary_tags)) : ?>
                        <div class="product-img-badges">
                            <?php foreach ($dietary_tags as $tag) : 
                                $badge_class = 'badge-tag-' . strtolower(sanitize_html_class($tag));
                            ?>
                                <span class="menu-badge <?php echo esc_attr($badge_class); ?>">
                                    <?php echo esc_html($tag); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Product Information & Add to Cart -->
            <div class="product-details-col">
                <?php if (!empty($cat_name)) : ?>
                    <span class="product-category-label"><?php echo esc_html($cat_name); ?></span>
                <?php endif; ?>

                <h1 class="product-title"><?php echo esc_html($title); ?></h1>

                <div class="product-price-strip">
                    <span class="product-price-val"><?php echo wp_kses_post($price_html); ?></span>
                </div>

                <?php if (!empty($desc)) : ?>
                    <div class="product-description-box">
                        <p><?php echo esc_html($desc); ?></p>
                    </div>
                <?php endif; ?>

                <!-- Add to Cart Form -->
                <div class="product-cart-action-box">
                    <form class="cart cochin-single-cart-form" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype='multipart/form-data'>
                        <div class="product-quantity-wrap">
                            <label class="screen-reader-text" for="quantity_<?php echo esc_attr($product_id); ?>">Quantity</label>
                            <input
                                type="number"
                                id="quantity_<?php echo esc_attr($product_id); ?>"
                                class="input-text qty text cochin-qty-input"
                                name="quantity"
                                value="1"
                                aria-label="Product quantity"
                                size="4"
                                min="1"
                                max="99"
                                step="1"
                                inputmode="numeric"
                                autocomplete="off" />
                        </div>

                        <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product_id); ?>" class="single_add_to_cart_button button alt cochin-btn-add-to-cart">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                            <span>Add to cart</span>
                        </button>
                    </form>
                </div>

                <!-- Product Meta Badges -->
                <div class="product-info-highlights">
                    <div class="info-highlight-item">
                        <span class="info-icon">🌿</span>
                        <span class="info-text">Freshly prepared with authentic Kerala spices &amp; herbs</span>
                    </div>
                    <div class="info-highlight-item">
                        <span class="info-icon">🥡</span>
                        <span class="info-text">Available for dine-in, takeaway collection &amp; delivery</span>
                    </div>
                </div>

            </div>

        </div>

        <!-- Related Dishes -->
        <?php if (!empty($related_products)) : ?>
            <div class="product-related-section">
                <div class="product-related-header">
                    <span class="related-subtitle">AUTHENTIC RECIPES</span>
                    <h2 class="related-title">You Might Also Like in <?php echo esc_html($cat_name); ?></h2>
                </div>

                <div class="product-related-grid">
                    <?php foreach ($related_products as $rel_prod) : 
                        $rel_id = $rel_prod->get_id();
                        $rel_url = get_permalink($rel_id);
                        $rel_price = $rel_prod->get_price_html();
                        $rel_desc = $rel_prod->get_short_description();
                    ?>
                        <div class="related-dish-card">
                            <div class="related-dish-body">
                                <a href="<?php echo esc_url($rel_url); ?>" class="related-dish-link">
                                    <h3 class="related-dish-name"><?php echo esc_html($rel_prod->get_name()); ?></h3>
                                </a>
                                <?php if (!empty($rel_desc)) : ?>
                                    <p class="related-dish-desc"><?php echo esc_html(wp_trim_words($rel_desc, 12, '...')); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="related-dish-footer">
                                <span class="related-dish-price"><?php echo wp_kses_post($rel_price); ?></span>
                                <a href="<?php echo esc_url('?add-to-cart=' . $rel_id); ?>" class="related-add-btn cochin-add-cart-btn" data-product-id="<?php echo esc_attr($rel_id); ?>">
                                    + Add
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>

</div>
