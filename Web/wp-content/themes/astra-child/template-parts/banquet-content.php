<?php
/**
 * The Cochin - Banquets Page Content Layout
 *
 * Fully integrated with WordPress Page Editor & ACF:
 * - Editable Intro & Main Headline
 * - 3 Curated Feast Packages (Vegetarian, Non-Vegetarian, Seafood) with editable pricing, descriptions & dish lists
 * - Editable Banquet Information & Details Guidelines
 * - Configurable Interactive AJAX Banquet Booking & Inquiry Form
 */

if (!defined('ABSPATH')) {
    exit;
}

$nonce = wp_create_nonce('the_cochin_banquet_nonce');
$ajax_url = admin_url('admin-ajax.php');

// Support custom args if called via shortcode or template part
$args = isset($args) && is_array($args) ? $args : array();

// Tab 1: Intro & Headline
$intro_badge   = !empty($args['badge']) ? $args['badge'] : (function_exists('get_field') && get_field('banquet_intro_badge') ? get_field('banquet_intro_badge') : __('BANQUET MEALS', 'astra-child'));
$main_headline = !empty($args['headline']) ? $args['headline'] : (function_exists('get_field') && get_field('banquet_main_headline') ? get_field('banquet_main_headline') : __('A Kerala Feast for the Whole Table', 'astra-child'));
$intro_text    = !empty($args['intro']) ? $args['intro'] : (function_exists('get_field') && get_field('banquet_intro_text') ? get_field('banquet_intro_text') : __('Experience a generous selection of dishes designed for sharing. Our banquet meals are ideal for family gatherings, groups and guests who would like to explore a wider range of Kerala flavours.', 'astra-child'));

// Tab 2: Feasts Grid
$feasts_title    = function_exists('get_field') && get_field('banquet_feasts_title') ? get_field('banquet_feasts_title') : __('Curated Banquet Menus', 'astra-child');
$feasts_subtitle = function_exists('get_field') && get_field('banquet_feasts_subtitle') ? get_field('banquet_feasts_subtitle') : __('Generous multi-course sharing feasts crafted fresh with authentic spices.', 'astra-child');

// Feast 1: Vegetarian
$veg_title = function_exists('get_field') && get_field('banquet_veg_title') ? get_field('banquet_veg_title') : __('Vegetarian Feast', 'astra-child');
$veg_badge = function_exists('get_field') && get_field('banquet_veg_badge') ? get_field('banquet_veg_badge') : __('VEGETARIAN', 'astra-child');
$veg_price = function_exists('get_field') && get_field('banquet_veg_price') ? get_field('banquet_veg_price') : '£ 15.95';
$veg_desc  = function_exists('get_field') && get_field('banquet_veg_desc') ? get_field('banquet_veg_desc') : __('A traditional Keralan plant-based feast bringing together garden-fresh vegetables, rich paneer, tempered lentils, and homemade breads.', 'astra-child');
$veg_raw_items = function_exists('get_field') && get_field('banquet_veg_items') ? get_field('banquet_veg_items') : "Keralan Tea Shop Snacks with homemade chutneys\nCrispy Aubergine & Lentil Soup Starters\nPalak Paneer & Dal Spinach Curry\nFresh Beans Coconut Thoran\nFlaky Kerala Parathas & Steamed Basmati Rice\nTraditional Sweet Payasam Pudding";
$veg_items = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $veg_raw_items))));

// Feast 2: Non-Vegetarian
$nonveg_title   = function_exists('get_field') && get_field('banquet_nonveg_title') ? get_field('banquet_nonveg_title') : __('Non-Vegetarian Feast', 'astra-child');
$nonveg_badge   = function_exists('get_field') && get_field('banquet_nonveg_badge') ? get_field('banquet_nonveg_badge') : __('NON-VEGETARIAN', 'astra-child');
$nonveg_popular = function_exists('get_field') && get_field('banquet_nonveg_popular') ? get_field('banquet_nonveg_popular') : __('CHEF RECOMMENDED', 'astra-child');
$nonveg_price   = function_exists('get_field') && get_field('banquet_nonveg_price') ? get_field('banquet_nonveg_price') : '£ 17.95';
$nonveg_desc    = function_exists('get_field') && get_field('banquet_nonveg_desc') ? get_field('banquet_nonveg_desc') : __('Our most celebrated sharing banquet featuring tender chicken roasts, slow-braised lamb curries, and aromatic Malabar biryani.', 'astra-child');
$nonveg_raw_items = function_exists('get_field') && get_field('banquet_nonveg_items') ? get_field('banquet_nonveg_items') : "Keralan Tea Shop Selection & Chicken Samosa\nAlleppey Spiced Chicken Roast\nTraditional Cochin Lamb Curry\nDal & Spinach or Vegetable Thoran\nButtery Kerala Parathas & Fragrant Basmati Rice\nWarm Gulab Jamun with Vanilla Ice Cream";
$nonveg_items = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $nonveg_raw_items))));

// Feast 3: Seafood
$seafood_title = function_exists('get_field') && get_field('banquet_seafood_title') ? get_field('banquet_seafood_title') : __('Seafood Feast', 'astra-child');
$seafood_badge = function_exists('get_field') && get_field('banquet_seafood_badge') ? get_field('banquet_seafood_badge') : __('SEAFOOD SPECIAL', 'astra-child');
$seafood_price = function_exists('get_field') && get_field('banquet_seafood_price') ? get_field('banquet_seafood_price') : '£ 19.95';
$seafood_desc  = function_exists('get_field') && get_field('banquet_seafood_desc') ? get_field('banquet_seafood_desc') : __('An opulent coastal banquet from the Arabian Sea docks of Fort Cochin, celebrating delicate King Fish and succulent tiger prawns.', 'astra-child');
$seafood_raw_items = function_exists('get_field') && get_field('banquet_seafood_items') ? get_field('banquet_seafood_items') : "Calamari Rings & Alleppey Prawn Fry\nCochin King Fish Curry in creamy coconut milk\nTiger Prawn Masala with roasted Southern spices\nCabbage Thoran & Tangy Lemon Rice\nFermented Soft Kallappams (2 Pcs)\nArtisanal Mango or Coconut Kulfi";
$seafood_items = array_filter(array_map('trim', explode("\n", str_replace("\r", "", $seafood_raw_items))));

// Tab 3: Information & Guidelines
$info_title       = function_exists('get_field') && get_field('banquet_info_title') ? get_field('banquet_info_title') : __('Banquet Information & Details', 'astra-child');
$info_subtitle    = function_exists('get_field') && get_field('banquet_info_subtitle') ? get_field('banquet_info_subtitle') : __('Important booking notes and guidelines for groups and party dining.', 'astra-child');
$info_timing      = function_exists('get_field') && get_field('banquet_info_timing') ? get_field('banquet_info_timing') : __('Daily for Lunch (12:00 PM – 3:00 PM) and Dinner (5:00 PM – 10:30 PM).', 'astra-child');
$info_requirement = function_exists('get_field') && get_field('banquet_info_requirement') ? get_field('banquet_info_requirement') : __('Advance booking recommended for groups of 4 or more guests.', 'astra-child');
$info_children    = function_exists('get_field') && get_field('banquet_info_children') ? get_field('banquet_info_children') : __('Half portions and milder child-friendly choices available for children under 10.', 'astra-child');
$info_dietary     = function_exists('get_field') && get_field('banquet_info_dietary') ? get_field('banquet_info_dietary') : __('Vegetarian, Vegan, and Gluten-Free feast adjustments available upon prior request.', 'astra-child');

// Tab 4: Inquiry Form
$form_show  = function_exists('get_field') ? get_field('banquet_form_show') !== false : true;
$form_title = function_exists('get_field') && get_field('banquet_form_title') ? get_field('banquet_form_title') : __('Reserve Your Banquet Gathering', 'astra-child');
$form_desc  = function_exists('get_field') && get_field('banquet_form_desc') ? get_field('banquet_form_desc') : __('Please provide your gathering details below. Our restaurant manager will contact you promptly to confirm table arrangements and banquet selections.', 'astra-child');
$phone      = function_exists('get_field') && get_field('banquet_phone') ? get_field('banquet_phone') : '01442 256111';
$email      = function_exists('get_field') && get_field('banquet_email') ? get_field('banquet_email') : 'info@thecochin.uk';
?>

<div class="cochin-banquets-layout">

    <!-- =========================================================================
         SECTION 1: HERO HEADLINE & INTRODUCTORY COPY
         ========================================================================= -->
    <section class="banquet-intro-section">
        <div class="banquet-container">
            <div class="banquet-intro-card">
                <?php if (!empty($intro_badge)) : ?>
                    <span class="banquet-intro-badge"><?php echo esc_html($intro_badge); ?></span>
                <?php endif; ?>
                <h2 class="banquet-main-headline"><?php echo esc_html($main_headline); ?></h2>
                <?php if (!empty($intro_text)) : ?>
                    <p class="banquet-intro-text"><?php echo wp_kses_post($intro_text); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 2: FEAST PACKAGES GRID
         ========================================================================= -->
    <section class="banquet-feasts-section">
        <div class="banquet-container">
            <div class="banquet-section-heading-wrap">
                <h3 class="banquet-section-title"><?php echo esc_html($feasts_title); ?></h3>
                <?php if (!empty($feasts_subtitle)) : ?>
                    <p class="banquet-section-subtitle"><?php echo esc_html($feasts_subtitle); ?></p>
                <?php endif; ?>
            </div>

            <div class="banquet-feasts-grid">
                
                <!-- Package 1: Vegetarian Feast -->
                <div class="banquet-feast-card">
                    <?php if (!empty($veg_badge)) : ?>
                        <div class="banquet-feast-badge veg"><?php echo esc_html($veg_badge); ?></div>
                    <?php endif; ?>
                    <h4 class="banquet-feast-title"><?php echo esc_html($veg_title); ?></h4>
                    <?php if (!empty($veg_price)) : ?>
                        <span class="banquet-feast-price"><?php echo esc_html($veg_price); ?> <small style="font-size:0.8rem; font-weight:400; color:#777;">/ person</small></span>
                    <?php endif; ?>
                    <?php if (!empty($veg_desc)) : ?>
                        <p class="banquet-feast-desc"><?php echo esc_html($veg_desc); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($veg_items)) : ?>
                        <ul class="banquet-feast-inclusions">
                            <?php foreach ($veg_items as $v_item) : ?>
                                <li><span>✦</span> <?php echo esc_html($v_item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Package 2: Non-Vegetarian Feast -->
                <div class="banquet-feast-card featured">
                    <?php if (!empty($nonveg_popular)) : ?>
                        <div class="banquet-feast-popular"><?php echo esc_html($nonveg_popular); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($nonveg_badge)) : ?>
                        <div class="banquet-feast-badge nonveg"><?php echo esc_html($nonveg_badge); ?></div>
                    <?php endif; ?>
                    <h4 class="banquet-feast-title"><?php echo esc_html($nonveg_title); ?></h4>
                    <?php if (!empty($nonveg_price)) : ?>
                        <span class="banquet-feast-price"><?php echo esc_html($nonveg_price); ?> <small style="font-size:0.8rem; font-weight:400; color:#777;">/ person</small></span>
                    <?php endif; ?>
                    <?php if (!empty($nonveg_desc)) : ?>
                        <p class="banquet-feast-desc"><?php echo esc_html($nonveg_desc); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($nonveg_items)) : ?>
                        <ul class="banquet-feast-inclusions">
                            <?php foreach ($nonveg_items as $nv_item) : ?>
                                <li><span>✦</span> <?php echo esc_html($nv_item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

                <!-- Package 3: Seafood Feast -->
                <div class="banquet-feast-card">
                    <?php if (!empty($seafood_badge)) : ?>
                        <div class="banquet-feast-badge seafood"><?php echo esc_html($seafood_badge); ?></div>
                    <?php endif; ?>
                    <h4 class="banquet-feast-title"><?php echo esc_html($seafood_title); ?></h4>
                    <?php if (!empty($seafood_price)) : ?>
                        <span class="banquet-feast-price"><?php echo esc_html($seafood_price); ?> <small style="font-size:0.8rem; font-weight:400; color:#777;">/ person</small></span>
                    <?php endif; ?>
                    <?php if (!empty($seafood_desc)) : ?>
                        <p class="banquet-feast-desc"><?php echo esc_html($seafood_desc); ?></p>
                    <?php endif; ?>
                    <?php if (!empty($seafood_items)) : ?>
                        <ul class="banquet-feast-inclusions">
                            <?php foreach ($seafood_items as $sf_item) : ?>
                                <li><span>✦</span> <?php echo esc_html($sf_item); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 3: INFORMATION BLOCK & BOOKING GUIDELINES
         ========================================================================= -->
    <section class="banquet-info-section">
        <div class="banquet-container">
            <div class="banquet-info-card">
                <div class="banquet-info-header">
                    <div class="banquet-info-icon" aria-hidden="true">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                    </div>
                    <div>
                        <h4 class="banquet-info-title"><?php echo esc_html($info_title); ?></h4>
                        <?php if (!empty($info_subtitle)) : ?>
                            <p class="banquet-info-subtitle"><?php echo esc_html($info_subtitle); ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="banquet-info-grid">
                    <?php if (!empty($info_timing)) : ?>
                        <div class="banquet-info-item">
                            <span class="banquet-info-label">🕒 <?php esc_html_e('Available:', 'astra-child'); ?></span>
                            <span class="banquet-info-value"><?php echo esc_html($info_timing); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($info_requirement)) : ?>
                        <div class="banquet-info-item">
                            <span class="banquet-info-label">👥 <?php esc_html_e('Booking Requirement:', 'astra-child'); ?></span>
                            <span class="banquet-info-value"><?php echo esc_html($info_requirement); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($info_children)) : ?>
                        <div class="banquet-info-item">
                            <span class="banquet-info-label">👶 <?php esc_html_e("Children's Price:", "astra-child"); ?></span>
                            <span class="banquet-info-value"><?php echo esc_html($info_children); ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($info_dietary)) : ?>
                        <div class="banquet-info-item">
                            <span class="banquet-info-label">🌾 <?php esc_html_e('Dietary & Allergens:', 'astra-child'); ?></span>
                            <span class="banquet-info-value"><?php echo esc_html($info_dietary); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 4: BANQUET INQUIRY & SUBMISSION FORM
         ========================================================================= -->
    <?php if ($form_show) : ?>
        <section class="banquet-form-section" id="banquetInquiryForm">
            <div class="banquet-container">
                <div class="banquet-form-wrapper">
                    
                    <div class="banquet-form-header">
                        <h3 class="banquet-form-title"><?php echo esc_html($form_title); ?></h3>
                        <?php if (!empty($form_desc)) : ?>
                            <p class="banquet-form-desc"><?php echo esc_html($form_desc); ?></p>
                        <?php endif; ?>
                    </div>

                    <form id="theCochinBanquetForm" class="banquet-form" method="post">
                        <input type="hidden" name="action" value="the_cochin_banquet_inquiry">
                        <input type="hidden" name="security" value="<?php echo esc_attr($nonce); ?>">

                        <div class="banquet-form-row two-col">
                            <div class="banquet-form-group">
                                <label for="banquet_name"><?php esc_html_e('Full Name', 'astra-child'); ?> <span class="req">*</span></label>
                                <input type="text" id="banquet_name" name="name" class="banquet-input" placeholder="<?php esc_attr_e('e.g. John Smith', 'astra-child'); ?>" required>
                            </div>

                            <div class="banquet-form-group">
                                <label for="banquet_email"><?php esc_html_e('Email Address', 'astra-child'); ?> <span class="req">*</span></label>
                                <input type="email" id="banquet_email" name="email" class="banquet-input" placeholder="<?php esc_attr_e('e.g. john@example.com', 'astra-child'); ?>" required>
                            </div>
                        </div>

                        <div class="banquet-form-row three-col">
                            <div class="banquet-form-group">
                                <label for="banquet_phone"><?php esc_html_e('Phone Number', 'astra-child'); ?> <span class="req">*</span></label>
                                <input type="tel" id="banquet_phone" name="phone" class="banquet-input" placeholder="<?php esc_attr_e('e.g. 07123 456789', 'astra-child'); ?>" required>
                            </div>

                            <div class="banquet-form-group">
                                <label for="banquet_date"><?php esc_html_e('Event Date', 'astra-child'); ?> <span class="req">*</span></label>
                                <input type="date" id="banquet_date" name="event_date" class="banquet-input" value="<?php echo esc_attr(date('Y-m-d', strtotime('+1 day'))); ?>" required>
                            </div>

                            <div class="banquet-form-group">
                                <label for="banquet_time"><?php esc_html_e('Preferred Time', 'astra-child'); ?> <span class="req">*</span></label>
                                <select id="banquet_time" name="event_time" class="banquet-input" required>
                                    <option value="12:30 PM">Lunch - 12:30 PM</option>
                                    <option value="1:00 PM">Lunch - 1:00 PM</option>
                                    <option value="1:30 PM">Lunch - 1:30 PM</option>
                                    <option value="2:00 PM">Lunch - 2:00 PM</option>
                                    <option value="5:30 PM">Dinner - 5:30 PM</option>
                                    <option value="6:00 PM">Dinner - 6:00 PM</option>
                                    <option value="6:30 PM">Dinner - 6:30 PM</option>
                                    <option value="7:00 PM" selected>Dinner - 7:00 PM</option>
                                    <option value="7:30 PM">Dinner - 7:30 PM</option>
                                    <option value="8:00 PM">Dinner - 8:00 PM</option>
                                    <option value="8:30 PM">Dinner - 8:30 PM</option>
                                </select>
                            </div>
                        </div>

                        <div class="banquet-form-row two-col">
                            <div class="banquet-form-group">
                                <label for="banquet_guests"><?php esc_html_e('Number of Guests (Party Size)', 'astra-child'); ?> <span class="req">*</span></label>
                                <select id="banquet_guests" name="guests" class="banquet-input" required>
                                    <option value="4">4 Guests</option>
                                    <option value="5">5 Guests</option>
                                    <option value="6" selected>6 Guests</option>
                                    <option value="7">7 Guests</option>
                                    <option value="8">8 Guests</option>
                                    <option value="10">10 Guests</option>
                                    <option value="12">12 Guests</option>
                                    <option value="15">15 Guests</option>
                                    <option value="20">20+ Guests (Private Area)</option>
                                </select>
                            </div>

                            <div class="banquet-form-group">
                                <label for="banquet_feast_type"><?php esc_html_e('Preferred Feast Menu', 'astra-child'); ?> <span class="req">*</span></label>
                                <select id="banquet_feast_type" name="feast_type" class="banquet-input" required>
                                    <option value="<?php echo esc_attr($nonveg_title); ?>"><?php echo esc_html($nonveg_title); ?></option>
                                    <option value="<?php echo esc_attr($veg_title); ?>"><?php echo esc_html($veg_title); ?></option>
                                    <option value="<?php echo esc_attr($seafood_title); ?>"><?php echo esc_html($seafood_title); ?></option>
                                    <option value="Mixed / Custom Feast"><?php esc_html_e('Mixed / Combination Feasts', 'astra-child'); ?></option>
                                </select>
                            </div>
                        </div>

                        <div class="banquet-form-group">
                            <label for="banquet_notes"><?php esc_html_e('Dietary Requirements / Allergies / Special Notes', 'astra-child'); ?></label>
                            <textarea id="banquet_notes" name="notes" class="banquet-textarea" rows="3" placeholder="<?php esc_attr_e('Please let us know about any allergy requirements, high chairs, or celebration requests...', 'astra-child'); ?>"></textarea>
                        </div>

                        <div id="banquetFormFeedback" class="banquet-form-feedback" style="display:none;"></div>

                        <div class="banquet-form-actions">
                            <button type="submit" id="banquetSubmitBtn" class="banquet-submit-btn">
                                <span><?php esc_html_e('Submit Banquet Inquiry', 'astra-child'); ?></span>
                            </button>
                        </div>
                    </form>

                    <?php if (!empty($phone) || !empty($email)) : ?>
                        <div class="banquet-direct-call">
                            <p>
                                <?php esc_html_e('Prefer to speak with our restaurant team directly? Call us at', 'astra-child'); ?>
                                <?php if (!empty($phone)) : ?>
                                    <strong><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a></strong>
                                <?php endif; ?>
                                <?php if (!empty($phone) && !empty($email)) : ?>
                                    <?php esc_html_e('or email', 'astra-child'); ?>
                                <?php endif; ?>
                                <?php if (!empty($email)) : ?>
                                    <strong><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></strong>.
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </section>
    <?php endif; ?>

</div>

<!-- Interactive AJAX Submission Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('theCochinBanquetForm');
    const btn = document.getElementById('banquetSubmitBtn');
    const feedback = document.getElementById('banquetFormFeedback');

    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span>Submitting Inquiry...</span>';
        feedback.style.display = 'none';
        feedback.className = 'banquet-form-feedback';

        const formData = new FormData(form);

        fetch('<?php echo esc_url($ajax_url); ?>', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            feedback.style.display = 'block';

            if (data.success) {
                feedback.className = 'banquet-form-feedback success';
                feedback.innerHTML = '<strong>✓ Inquiry Sent Successfully!</strong><br>' + (data.data && data.data.message ? data.data.message : 'Thank you! We will get in touch with you shortly.');
                form.reset();
            } else {
                feedback.className = 'banquet-form-feedback error';
                feedback.innerHTML = '<strong>⚠️ Submission Notice:</strong> ' + (data.data && data.data.message ? data.data.message : 'An error occurred. Please try again or call us.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            feedback.style.display = 'block';
            feedback.className = 'banquet-form-feedback error';
            feedback.innerHTML = '<strong>⚠️ Note:</strong> Request could not be completed. Please call us directly.';
        });
    });
});
</script>
