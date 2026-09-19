<?php
/**
 * The Cochin - Banquets Page Content Layout
 *
 * Implements:
 * - Title and Breadcrumbs Header Banner (via template-parts/about-header)
 * - Headline: "A Kerala Feast for the Whole Table"
 * - Introductory Copy
 * - 3 Curated Feast Packages (Vegetarian, Non-Vegetarian, Seafood)
 * - Information & Guidelines Block
 * - Interactive AJAX Banquet Booking & Inquiry Form
 */

if (!defined('ABSPATH')) {
    exit;
}

$nonce = wp_create_nonce('the_cochin_banquet_nonce');
$ajax_url = admin_url('admin-ajax.php');
?>

<div class="cochin-banquets-layout">

    <!-- =========================================================================
         SECTION 1: HERO HEADLINE & INTRODUCTORY COPY
         ========================================================================= -->
    <section class="banquet-intro-section">
        <div class="banquet-container">
            <div class="banquet-intro-card">
                <span class="banquet-intro-badge">BANQUET MEALS</span>
                <h2 class="banquet-main-headline">A Kerala Feast for the Whole Table</h2>
                <p class="banquet-intro-text">
                    Experience a generous selection of dishes designed for sharing. Our banquet meals are ideal for family gatherings, groups and guests who would like to explore a wider range of Kerala flavours.
                </p>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 2: FEAST PACKAGES GRID
         ========================================================================= -->
    <section class="banquet-feasts-section">
        <div class="banquet-container">
            <div class="banquet-section-heading-wrap">
                <h3 class="banquet-section-title">Curated Banquet Menus</h3>
                <p class="banquet-section-subtitle">Generous multi-course sharing feasts crafted fresh with authentic spices.</p>
            </div>

            <div class="banquet-feasts-grid">
                
                <!-- Package 1: Vegetarian Feast -->
                <div class="banquet-feast-card">
                    <div class="banquet-feast-badge veg">VEGETARIAN</div>
                    <h4 class="banquet-feast-title">Vegetarian Feast</h4>
                    <p class="banquet-feast-desc">
                        A traditional Keralan plant-based feast bringing together garden-fresh vegetables, rich paneer, tempered lentils, and homemade breads.
                    </p>
                    <ul class="banquet-feast-inclusions">
                        <li><span>✦</span> Keralan Tea Shop Snacks with homemade chutneys</li>
                        <li><span>✦</span> Crispy Aubergine &amp; Lentil Soup Starters</li>
                        <li><span>✦</span> Palak Paneer &amp; Dal Spinach Curry</li>
                        <li><span>✦</span> Fresh Beans Coconut Thoran</li>
                        <li><span>✦</span> Flaky Kerala Parathas &amp; Steamed Basmati Rice</li>
                        <li><span>✦</span> Traditional Sweet Payasam Pudding</li>
                    </ul>
                </div>

                <!-- Package 2: Non-Vegetarian Feast -->
                <div class="banquet-feast-card featured">
                    <div class="banquet-feast-popular">CHEF RECOMMENDED</div>
                    <div class="banquet-feast-badge nonveg">NON-VEGETARIAN</div>
                    <h4 class="banquet-feast-title">Non-Vegetarian Feast</h4>
                    <p class="banquet-feast-desc">
                        Our most celebrated sharing banquet featuring tender chicken roasts, slow-braised lamb curries, and aromatic Malabar biryani.
                    </p>
                    <ul class="banquet-feast-inclusions">
                        <li><span>✦</span> Keralan Tea Shop Selection &amp; Chicken Samosa</li>
                        <li><span>✦</span> Alleppey Spiced Chicken Roast</li>
                        <li><span>✦</span> Traditional Cochin Lamb Curry</li>
                        <li><span>✦</span> Dal &amp; Spinach or Vegetable Thoran</li>
                        <li><span>✦</span> Buttery Kerala Parathas &amp; Fragrant Basmati Rice</li>
                        <li><span>✦</span> Warm Gulab Jamun with Vanilla Ice Cream</li>
                    </ul>
                </div>

                <!-- Package 3: Seafood Feast -->
                <div class="banquet-feast-card">
                    <div class="banquet-feast-badge seafood">SEAFOOD SPECIAL</div>
                    <h4 class="banquet-feast-title">Seafood Feast</h4>
                    <p class="banquet-feast-desc">
                        An opulent coastal banquet from the Arabian Sea docks of Fort Cochin, celebrating delicate King Fish and succulent tiger prawns.
                    </p>
                    <ul class="banquet-feast-inclusions">
                        <li><span>✦</span> Calamari Rings &amp; Alleppey Prawn Fry</li>
                        <li><span>✦</span> Cochin King Fish Curry in creamy coconut milk</li>
                        <li><span>✦</span> Tiger Prawn Masala with roasted Southern spices</li>
                        <li><span>✦</span> Cabbage Thoran &amp; Tangy Lemon Rice</li>
                        <li><span>✦</span> Fermented Soft Kallappams (2 Pcs)</li>
                        <li><span>✦</span> Artisanal Mango or Coconut Kulfi</li>
                    </ul>
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
                        <h4 class="banquet-info-title">Banquet Information &amp; Details</h4>
                        <p class="banquet-info-subtitle">Important booking notes and guidelines for groups and party dining.</p>
                    </div>
                </div>

                <div class="banquet-info-grid">
                    <div class="banquet-info-item">
                        <span class="banquet-info-label">🕒 Available:</span>
                        <span class="banquet-info-value">Daily for Lunch (12:00 PM – 3:00 PM) and Dinner (5:00 PM – 10:30 PM).</span>
                    </div>

                    <div class="banquet-info-item">
                        <span class="banquet-info-label">👥 Booking Requirement:</span>
                        <span class="banquet-info-value">Advance booking recommended for groups of 4 or more guests.</span>
                    </div>

                    <div class="banquet-info-item">
                        <span class="banquet-info-label">👶 Children's Price:</span>
                        <span class="banquet-info-value">Half portions and milder child-friendly choices available for children under 10.</span>
                    </div>

                    <div class="banquet-info-item">
                        <span class="banquet-info-label">🌾 Dietary &amp; Allergens:</span>
                        <span class="banquet-info-value">Vegetarian, Vegan, and Gluten-Free feast adjustments available upon prior request.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 4: BANQUET INQUIRY & SUBMISSION FORM
         ========================================================================= -->
    <section class="banquet-form-section" id="banquetInquiryForm">
        <div class="banquet-container">
            <div class="banquet-form-wrapper">
                
                <div class="banquet-form-header">
                    <h3 class="banquet-form-title">Reserve Your Banquet Gathering</h3>
                    <p class="banquet-form-desc">
                        Please provide your gathering details below. Our restaurant manager will contact you promptly to confirm table arrangements and banquet selections.
                    </p>
                </div>

                <form id="theCochinBanquetForm" class="banquet-form" method="post">
                    <input type="hidden" name="action" value="the_cochin_banquet_inquiry">
                    <input type="hidden" name="security" value="<?php echo esc_attr($nonce); ?>">

                    <div class="banquet-form-row two-col">
                        <div class="banquet-form-group">
                            <label for="banquet_name">Full Name <span class="req">*</span></label>
                            <input type="text" id="banquet_name" name="name" class="banquet-input" placeholder="e.g. John Smith" required>
                        </div>

                        <div class="banquet-form-group">
                            <label for="banquet_email">Email Address <span class="req">*</span></label>
                            <input type="email" id="banquet_email" name="email" class="banquet-input" placeholder="e.g. john@example.com" required>
                        </div>
                    </div>

                    <div class="banquet-form-row three-col">
                        <div class="banquet-form-group">
                            <label for="banquet_phone">Phone Number <span class="req">*</span></label>
                            <input type="tel" id="banquet_phone" name="phone" class="banquet-input" placeholder="e.g. 07123 456789" required>
                        </div>

                        <div class="banquet-form-group">
                            <label for="banquet_date">Event Date <span class="req">*</span></label>
                            <input type="date" id="banquet_date" name="event_date" class="banquet-input" value="<?php echo esc_attr(date('Y-m-d', strtotime('+1 day'))); ?>" required>
                        </div>

                        <div class="banquet-form-group">
                            <label for="banquet_time">Preferred Time <span class="req">*</span></label>
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
                            <label for="banquet_guests">Number of Guests (Party Size) <span class="req">*</span></label>
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
                            <label for="banquet_feast_type">Preferred Feast Menu <span class="req">*</span></label>
                            <select id="banquet_feast_type" name="feast_type" class="banquet-input" required>
                                <option value="Non-Vegetarian Feast">Non-Vegetarian Feast</option>
                                <option value="Vegetarian Feast">Vegetarian Feast</option>
                                <option value="Seafood Feast">Seafood Feast</option>
                                <option value="Mixed / Custom Feast">Mixed / Combination Feasts</option>
                            </select>
                        </div>
                    </div>

                    <div class="banquet-form-group">
                        <label for="banquet_notes">Dietary Requirements / Allergies / Special Notes</label>
                        <textarea id="banquet_notes" name="notes" class="banquet-textarea" rows="3" placeholder="Please let us know about any allergy requirements, high chairs, or celebration requests..."></textarea>
                    </div>

                    <div id="banquetFormFeedback" class="banquet-form-feedback" style="display:none;"></div>

                    <div class="banquet-form-actions">
                        <button type="submit" id="banquetSubmitBtn" class="banquet-submit-btn">
                            <span>Submit Banquet Inquiry</span>
                        </button>
                    </div>
                </form>

                <div class="banquet-direct-call">
                    <p>Prefer to speak with our restaurant team directly? Call us at <strong><a href="tel:01442256111">01442 256111</a></strong> or email <strong><a href="mailto:info@thecochin.uk">info@thecochin.uk</a></strong>.</p>
                </div>

            </div>
        </div>
    </section>

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
                feedback.innerHTML = '<strong>⚠️ Submission Notice:</strong> ' + (data.data && data.data.message ? data.data.message : 'An error occurred. Please try again or call us on 01442 256111.');
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            feedback.style.display = 'block';
            feedback.className = 'banquet-form-feedback error';
            feedback.innerHTML = '<strong>⚠️ Note:</strong> Request could not be completed. Please call us directly on 01442 256111.';
        });
    });
});
</script>
