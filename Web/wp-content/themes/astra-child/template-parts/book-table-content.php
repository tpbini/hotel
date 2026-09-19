<?php
/**
 * The Cochin - Interactive Live Restaurant Table Booking & Management Layout
 *
 * Implements:
 * - Real-Time Slot Availability Engine with Split Service (Lunch & Dinner)
 * - 3-Step Interactive Booking Wizard with Instant Confirmation
 * - UK Phone Formatting & Food Allergy Advisory
 * - Passwordless Guest Self-Service Reservation Management & Cancellation Portal
 * - Direct Integration with UK Restaurant Table Booking Plugin & QR POS Engine
 */

if (!defined('ABSPATH')) {
    exit;
}

$restaurant_phone = get_option('rb_restaurant_phone', '01442 233777');
$restaurant_name  = get_option('rb_restaurant_name', 'The Cochin Indian Restaurant');
$allergen_notice  = get_option('rb_allergen_notice', 'If you or any member of your party suffer from a food allergy or dietary intolerance, please speak to a member of our team directly by phone before booking.');
?>

<div class="cochin-reservation-layout" id="cochinBookingApp">

    <!-- =========================================================================
         SECTION 1: HERO & INTRODUCTORY SECTION
         ========================================================================= -->
    <section class="reservation-intro-section">
        <div class="reservation-container">
            <div class="reservation-intro-card">
                <span class="reservation-intro-badge">ONLINE RESERVATIONS</span>
                <h1 class="reservation-main-headline">Planning a meal at The Cochin?</h1>
                <p class="reservation-intro-text">
                    Reserve your table online in real-time and we will look forward to welcoming you to the authentic flavours of Kerala and South India.
                </p>

                <!-- Navigation Tabs -->
                <div class="cochin-booking-nav-tabs" role="tablist">
                    <button type="button" class="cochin-booking-tab-btn active" data-tab="book" role="tab" aria-selected="true">
                        <span class="cochin-tab-icon">📅</span> Reserve a Table
                    </button>
                    <button type="button" class="cochin-booking-tab-btn" data-tab="manage" role="tab" aria-selected="false">
                        <span class="cochin-tab-icon">🔍</span> Find / Manage Booking
                    </button>
                    <button type="button" class="cochin-booking-tab-btn" data-tab="groups" role="tab" aria-selected="false">
                        <span class="cochin-tab-icon">👥</span> Large Groups & Banquets
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         SECTION 2: TAB CONTENT CONTAINER
         ========================================================================= -->
    <section class="reservation-main-section">
        <div class="reservation-container">

            <!-- TAB PANE 1: REAL-TIME TABLE RESERVATION WIZARD -->
            <div class="cochin-tab-pane active" id="tab_pane_book">
                <div class="reservation-form-wrapper">

                    <!-- Multi-Step Progress Indicator -->
                    <div class="cochin-step-progress-bar">
                        <div class="cochin-step-indicator active" id="step_ind_1">
                            <span class="cochin-step-num">1</span>
                            <span class="cochin-step-lbl">Find Table & Time</span>
                        </div>
                        <div class="cochin-step-line"></div>
                        <div class="cochin-step-indicator" id="step_ind_2">
                            <span class="cochin-step-num">2</span>
                            <span class="cochin-step-lbl">Guest Details</span>
                        </div>
                        <div class="cochin-step-line"></div>
                        <div class="cochin-step-indicator" id="step_ind_3">
                            <span class="cochin-step-num">3</span>
                            <span class="cochin-step-lbl">Confirmation</span>
                        </div>
                    </div>

                    <!-- STEP 1: DATE, GUESTS & LIVE TIME SLOT PICKER -->
                    <div class="cochin-step-card active" id="step_card_1">
                        <div class="cochin-step-header">
                            <h3 class="cochin-step-title">Select Date, Party Size & Dining Time</h3>
                            <p class="cochin-step-subtitle">Check real-time table availability for lunch and dinner services.</p>
                        </div>

                        <!-- Date, Party Size & Dining Duration Controls -->
                        <div class="reservation-form-row three-col">
                            <div class="reservation-form-group">
                                <label for="res_booking_date">Dining Date <span class="req">*</span></label>
                                <input type="date" id="res_booking_date" class="reservation-input" value="<?php echo esc_attr(date('Y-m-d')); ?>" min="<?php echo esc_attr(date('Y-m-d')); ?>" required>
                            </div>

                            <div class="reservation-form-group">
                                <label for="res_party_size">Number of Guests <span class="req">*</span></label>
                                <select id="res_party_size" class="reservation-input" required>
                                    <option value="1">1 Guest</option>
                                    <option value="2" selected>2 Guests</option>
                                    <option value="3">3 Guests</option>
                                    <option value="4">4 Guests</option>
                                    <option value="5">5 Guests</option>
                                    <option value="6">6 Guests</option>
                                    <option value="7">7 Guests</option>
                                    <option value="8">8 Guests</option>
                                    <option value="9">9 Guests</option>
                                    <option value="10">10 Guests</option>
                                    <option value="12">12 Guests</option>
                                </select>
                            </div>

                            <div class="reservation-form-group">
                                <label for="res_duration">Dining Duration <span class="req">*</span></label>
                                <select id="res_duration" class="reservation-input">
                                    <option value="60">1 Hour (Quick Dinner: e.g. 7:00 - 8:00 PM)</option>
                                    <option value="90">1.5 Hours (Standard Dining: e.g. 7:00 - 8:30 PM)</option>
                                    <option value="120" selected>2 Hours (Leisure Feast: e.g. 7:00 - 9:00 PM)</option>
                                    <option value="150">2.5 Hours (Celebration / Banquet)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Quick Selectors: Guests & Duration Chips -->
                        <div class="cochin-selectors-dual-row">
                            <div class="cochin-quick-party-wrap">
                                <label class="cochin-quick-label">Quick Select Guests:</label>
                                <div class="cochin-party-chips-list">
                                    <button type="button" class="cochin-party-chip" data-size="1">1</button>
                                    <button type="button" class="cochin-party-chip selected" data-size="2">2</button>
                                    <button type="button" class="cochin-party-chip" data-size="3">3</button>
                                    <button type="button" class="cochin-party-chip" data-size="4">4</button>
                                    <button type="button" class="cochin-party-chip" data-size="5">5</button>
                                    <button type="button" class="cochin-party-chip" data-size="6">6</button>
                                    <button type="button" class="cochin-party-chip" data-size="8">8</button>
                                    <button type="button" class="cochin-party-chip" data-size="10">10+</button>
                                </div>
                            </div>

                            <div class="cochin-quick-duration-wrap">
                                <label class="cochin-quick-label">Table Duration / End Time:</label>
                                <div class="cochin-duration-chips-list">
                                    <button type="button" class="cochin-duration-chip" data-duration="60">⚡ 1 hr</button>
                                    <button type="button" class="cochin-duration-chip" data-duration="90">🍽️ 1.5 hrs</button>
                                    <button type="button" class="cochin-duration-chip selected" data-duration="120">✨ 2 hrs (Default)</button>
                                    <button type="button" class="cochin-duration-chip" data-duration="150">🥂 2.5 hrs</button>
                                </div>
                            </div>
                        </div>

                        <!-- Large Party Alert (6+ Guests) -->
                        <div id="large_group_notice" class="cochin-policy-alert-banner" style="display:none;">
                            <div class="cochin-alert-icon-box">ℹ️</div>
                            <div class="cochin-alert-content">
                                <strong>Large Party Policy (6+ Guests):</strong>
                                <p>For groups of 6 or more or special banqueting arrangements, you can also telephone us directly on <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $restaurant_phone)); ?>"><strong><?php echo esc_html($restaurant_phone); ?></strong></a> to ensure bespoke table seating.</p>
                            </div>
                        </div>

                        <!-- Live Slots Container -->
                        <div class="cochin-slots-wrapper">
                            <label class="cochin-slots-title-lbl">Available Dining Slots:</label>
                            <div id="slots_list_container" class="cochin-slots-list-container">
                                <div class="cochin-loading-slots">
                                    <div class="cochin-spinner"></div>
                                    <p>Checking live table availability...</p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 1 Action Bar -->
                        <div class="cochin-step-action-bar">
                            <button type="button" id="step1_continue_btn" class="reservation-submit-btn" disabled>
                                Select a Time Slot to Continue &rarr;
                            </button>
                        </div>
                    </div>

                    <!-- STEP 2: GUEST DETAILS & SPECIAL REQUIREMENTS -->
                    <div class="cochin-step-card" id="step_card_2">
                        <!-- Summary Pill of Selected Slot -->
                        <div class="cochin-selected-slot-banner">
                            <div class="cochin-slot-banner-info">
                                <span class="cochin-banner-tag">SELECTED RESERVATION</span>
                                <div class="cochin-banner-details">
                                    <span id="summary_date_disp">--</span> &bull; 
                                    <strong id="summary_window_disp" class="cochin-window-highlight">--</strong> &bull; 
                                    <span id="summary_party_disp">--</span> &bull; 
                                    <span id="summary_service_disp" class="cochin-service-tag">--</span>
                                </div>
                            </div>
                            <button type="button" id="change_slot_link" class="cochin-btn-text-change">
                                &larr; Change Time / Duration
                            </button>
                        </div>

                        <div class="cochin-step-header">
                            <h3 class="cochin-step-title">Enter Guest Details</h3>
                            <p class="cochin-step-subtitle">Please provide your contact information to receive your instant booking confirmation.</p>
                        </div>

                        <form id="theCochinReservationForm" class="reservation-form" method="post">

                            <!-- Row 1: Full Name & Telephone -->
                            <div class="reservation-form-row two-col">
                                <div class="reservation-form-group">
                                    <label for="res_fullname">Full Name <span class="req">*</span></label>
                                    <input type="text" id="res_fullname" name="full_name" class="reservation-input" placeholder="e.g. David Miller" required autocomplete="name">
                                </div>

                                <div class="reservation-form-group">
                                    <label for="res_phone">UK Mobile / Telephone <span class="req">*</span></label>
                                    <input type="tel" id="res_phone" name="telephone" class="reservation-input" placeholder="e.g. 07123 456789" required autocomplete="tel">
                                </div>
                            </div>

                            <!-- Row 2: Email & Occasion -->
                            <div class="reservation-form-row two-col">
                                <div class="reservation-form-group">
                                    <label for="res_email">Email Address <span class="req">*</span></label>
                                    <input type="email" id="res_email" name="email" class="reservation-input" placeholder="e.g. david@example.co.uk" required autocomplete="email">
                                </div>

                                <div class="reservation-form-group">
                                    <label for="res_occasion">Dining Occasion</label>
                                    <select id="res_occasion" name="occasion" class="reservation-input">
                                        <option value="Casual Dining" selected>Casual Dining</option>
                                        <option value="Birthday Celebration">Birthday Celebration</option>
                                        <option value="Anniversary">Anniversary</option>
                                        <option value="Date Night">Date Night</option>
                                        <option value="Business Meal">Business Meal</option>
                                        <option value="Family Gathering">Family Gathering</option>
                                        <option value="Special Celebration">Special Celebration</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Row 3: Dietary Requirements & Allergies -->
                            <div class="reservation-form-group">
                                <label for="res_dietary">Dietary & Allergy Requirements (Optional)</label>
                                <input type="text" id="res_dietary" name="dietary" class="reservation-input" placeholder="e.g. Nut allergy, Gluten free, Halal, Dairy free, Vegan, Vegetarian...">
                            </div>

                            <!-- UK Food Allergy Advisory Notice Card -->
                            <div class="cochin-allergen-alert-card">
                                <div class="cochin-allergen-icon">⚠️</div>
                                <div class="cochin-allergen-text">
                                    <strong>Food Allergy & Dietary Advisory:</strong>
                                    <p><?php echo esc_html($allergen_notice); ?></p>
                                </div>
                            </div>

                            <!-- Row 4: Accessibility or Special Requests -->
                            <div class="reservation-form-group">
                                <label for="res_requirements">Accessibility or Seating Preferences (Optional)</label>
                                <textarea id="res_requirements" name="special_requirements" class="reservation-textarea" rows="2" placeholder="e.g. Wheelchair access, highchair needed, quiet table, booth seating..."></textarea>
                            </div>

                            <!-- Marketing Consent (GDPR) -->
                            <div class="cochin-gdpr-consent">
                                <label class="cochin-checkbox-label">
                                    <input type="checkbox" id="res_marketing" name="marketing_consent">
                                    <span>I agree to receive occasional news, seasonal Kerala delicacies, and dining offers from The Cochin (optional).</span>
                                </label>
                            </div>

                            <!-- Feedback Container -->
                            <div id="reservationFeedback" class="reservation-feedback" style="display:none;"></div>

                            <!-- Submit Action Buttons -->
                            <div class="cochin-step-action-bar two-buttons">
                                <button type="button" id="step2_back_btn" class="cochin-btn-back">
                                    &larr; Back to Times
                                </button>
                                <button type="submit" id="res_submit_btn" class="reservation-submit-btn">
                                    <span>Confirm My Reservation</span>
                                </button>
                            </div>

                        </form>
                    </div>

                    <!-- STEP 3: INSTANT CONFIRMATION & GUEST PORTAL -->
                    <div class="cochin-step-card" id="step_card_3">
                        <div class="cochin-confirmation-box">
                            <div class="cochin-conf-icon-ring">
                                <span class="cochin-conf-checkmark">✓</span>
                            </div>

                            <span class="cochin-conf-badge">RESERVATION CONFIRMED</span>
                            <h2 class="cochin-conf-title">We Look Forward to Welcoming You!</h2>
                            <p class="cochin-conf-subtitle">
                                A confirmation receipt has been sent to <strong id="conf_email_disp">your email</strong>.
                            </p>

                            <!-- Reference Code Card -->
                            <div class="cochin-conf-ref-card">
                                <span class="cochin-conf-ref-lbl">YOUR BOOKING REFERENCE</span>
                                <div class="cochin-conf-ref-value" id="conf_ref_badge">CR-00000</div>
                                
                                <div class="cochin-conf-details-grid">
                                    <div class="cochin-conf-detail-item">
                                        <span class="detail-k">Guest</span>
                                        <strong class="detail-v" id="conf_guest_disp">--</strong>
                                    </div>
                                    <div class="cochin-conf-detail-item">
                                        <span class="detail-k">Party Size</span>
                                        <strong class="detail-v" id="conf_party_disp">--</strong>
                                    </div>
                                    <div class="cochin-conf-detail-item">
                                        <span class="detail-k">Date</span>
                                        <strong class="detail-v" id="conf_date_disp">--</strong>
                                    </div>
                                    <div class="cochin-conf-detail-item">
                                        <span class="detail-k">Dining Window</span>
                                        <strong class="detail-v" id="conf_window_disp">--</strong>
                                    </div>
                                    <div class="cochin-conf-detail-item">
                                        <span class="detail-k">Phone</span>
                                        <strong class="detail-v" id="conf_phone_disp">--</strong>
                                    </div>
                                    <div class="cochin-conf-detail-item">
                                        <span class="detail-k">Status</span>
                                        <strong class="detail-v status-confirmed" id="conf_status_disp">CONFIRMED</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Next Action Buttons -->
                            <div class="cochin-conf-actions-group">
                                <a href="#" id="conf_btn_calendar" target="_blank" rel="noopener" class="cochin-btn-calendar">
                                    📅 Add to Google Calendar
                                </a>
                                <button type="button" id="conf_btn_manage" class="cochin-btn-manage-alt">
                                    ⚙️ Manage / View Booking
                                </button>
                                <button type="button" id="btn_book_another" class="cochin-btn-link-reset">
                                    + Make Another Reservation
                                </button>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <!-- TAB PANE 2: FIND / MANAGE RESERVATION PORTAL -->
            <div class="cochin-tab-pane" id="tab_pane_manage">
                <div class="reservation-form-wrapper">
                    
                    <div class="cochin-step-header">
                        <h3 class="cochin-step-title">Manage Your Reservation</h3>
                        <p class="cochin-step-subtitle">Enter your Booking Reference (e.g. CR-52071) or secret management token to view or cancel your table.</p>
                    </div>

                    <form id="manageSearchForm" class="cochin-manage-search-bar">
                        <div class="cochin-search-input-group">
                            <input type="text" id="manage_search_input" class="reservation-input" placeholder="e.g. CR-52071 or security token..." required>
                            <button type="submit" class="reservation-submit-btn cochin-btn-search">
                                <span>Look Up Booking</span>
                            </button>
                        </div>
                    </form>

                    <!-- Manage Result Display Container -->
                    <div id="manage_result_container" class="cochin-manage-result-area" style="display:none;"></div>

                </div>
            </div>

            <!-- TAB PANE 3: LARGE GROUPS & BANQUETS INFO -->
            <div class="cochin-tab-pane" id="tab_pane_groups">
                <div class="reservation-form-wrapper cochin-groups-info-wrapper">
                    
                    <div class="cochin-step-header">
                        <h3 class="cochin-step-title">Large Parties, Banquets & Private Dining</h3>
                        <p class="cochin-step-subtitle">Host your special occasions, weddings, corporate dinners, and celebrations at The Cochin.</p>
                    </div>

                    <div class="cochin-groups-features-grid">
                        <div class="cochin-group-feat-card">
                            <div class="feat-icon">🥂</div>
                            <h4>Private Banqueting Hall</h4>
                            <p>Our dedicated banqueting suite accommodates up to 80 guests with bespoke seating configurations, private bar access, and custom Kerala culinary menus.</p>
                        </div>
                        <div class="cochin-group-feat-card">
                            <div class="feat-icon">🍛</div>
                            <h4>Set Feasts & Kerala Thali</h4>
                            <p>Curated 3-course and 4-course authentic banquet menus featuring traditional South Indian specialties, seafood delicacies, and vegetarian feasts.</p>
                        </div>
                        <div class="cochin-group-feat-card">
                            <div class="feat-icon">📞</div>
                            <h4>Dedicated Event Planning</h4>
                            <p>Speak directly with our events team to discuss custom decorations, dietary preferences, and personalised dining experiences.</p>
                        </div>
                    </div>

                    <div class="cochin-groups-cta-card">
                        <h4>Ready to organise your party or banquet?</h4>
                        <p>Call our event specialists directly on <strong><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $restaurant_phone)); ?>"><?php echo esc_html($restaurant_phone); ?></a></strong> or explore our banquets page.</p>
                        <div class="cochin-groups-cta-btns">
                            <a href="<?php echo esc_url(home_url('/banquets/')); ?>" class="reservation-submit-btn">
                                <span>Explore Banquets & Events</span>
                            </a>
                            <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $restaurant_phone)); ?>" class="cochin-btn-secondary">
                                📞 Call <?php echo esc_html($restaurant_phone); ?>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Notice & Policy Box -->
            <div class="reservation-direct-phone-box">
                <p>Have an urgent inquiry, table changes, or same-day queries? Telephone our restaurant directly on <strong><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $restaurant_phone)); ?>"><?php echo esc_html($restaurant_phone); ?></a></strong>.</p>
                <p class="cochin-location-footnote">📍 61 High Street, Hemel Hempstead, Hertfordshire HP1 3AF</p>
            </div>

        </div>
    </section>

</div>
