/**
 * UK Restaurant Table Booking - Customer Frontend Engine
 */

(function($) {
    'use strict';

    let bookingState = {
        date: '',
        partySize: 2,
        selectedTime: '',
        selectedService: '',
        customerName: '',
        phone: '',
        email: '',
        specialRequests: '',
        dietaryNotes: '',
        marketingConsent: false,
        reservation: null
    };

    $(document).ready(function() {
        if ($('#rb-booking-root').length) {
            initBookingApp();
        }
        if ($('#rb-manage-root').length) {
            initManageApp();
        }
    });

    // 1. Initialize Main Booking Widget
    function initBookingApp() {
        const $root = $('#rb-booking-root');

        // Set default date to today or tomorrow if late
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        bookingState.date = yyyy + '-' + mm + '-' + dd;

        renderBookingUI($root);
        attachBookingEvents($root);
        loadAvailability();
    }

    function renderBookingUI($container) {
        const allergenNotice = rbData.allergensNotice || 'If you or any member of your party have a severe food allergy, please inform us directly by telephone.';
        const phone = rbData.phone || '020 7946 0912';

        const html = `
        <div class="rb-booking-widget-wrapper">
            <!-- Header -->
            <div class="rb-widget-header">
                <h2>Reserve Your Table</h2>
                <p>Real-time availability & instant confirmation</p>
            </div>

            <!-- Stepper Progress -->
            <div class="rb-stepper">
                <div class="rb-step-item active" id="rb-step-ind-1">
                    <span class="rb-step-number">1</span> Find a Table
                </div>
                <div class="rb-step-item" id="rb-step-ind-2">
                    <span class="rb-step-number">2</span> Guest Details
                </div>
                <div class="rb-step-item" id="rb-step-ind-3">
                    <span class="rb-step-number">3</span> Confirmation
                </div>
            </div>

            <!-- Widget Body -->
            <div class="rb-widget-body">
                <!-- STEP 1: Date, Guests & Time Slot -->
                <div class="rb-step-pane active" id="rb-pane-1">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div class="rb-field-group">
                            <label>Date <span class="rb-req">*</span></label>
                            <input type="date" id="rb-input-date" class="rb-input-text" value="${bookingState.date}" min="${bookingState.date}">
                        </div>
                        <div class="rb-field-group">
                            <label>Party Size <span class="rb-req">*</span></label>
                            <select id="rb-select-party" class="rb-select-field">
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
                    </div>

                    <!-- Quick Party Size Chips -->
                    <label style="font-weight:600; font-size:13px; margin-bottom:6px; display:block;">Quick Select Guests:</label>
                    <div class="rb-party-chips">
                        <div class="rb-party-chip" data-size="1">1</div>
                        <div class="rb-party-chip selected" data-size="2">2</div>
                        <div class="rb-party-chip" data-size="3">3</div>
                        <div class="rb-party-chip" data-size="4">4</div>
                        <div class="rb-party-chip" data-size="5">5</div>
                        <div class="rb-party-chip" data-size="6">6+</div>
                    </div>

                    <!-- Slots Container -->
                    <div id="rb-slots-container">
                        <div class="rb-spinner-wrap"><div class="rb-spinner"></div><p>Checking live table availability...</p></div>
                    </div>

                    <!-- Step 1 Next Button -->
                    <div style="margin-top: 24px;">
                        <button type="button" class="rb-btn-action rb-btn-primary-action" id="rb-btn-step1-next" disabled>
                            Select a Time Slot to Continue &rarr;
                        </button>
                    </div>
                </div>

                <!-- STEP 2: Contact & Allergy Details -->
                <div class="rb-step-pane" id="rb-pane-2">
                    <!-- Selected Slot Summary Banner -->
                    <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:8px; padding:12px 16px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <span style="font-size:12px; color:#0369a1; text-transform:uppercase; font-weight:700;">Selected Reservation</span>
                            <div style="font-weight:700; font-size:16px; color:#0f172a;" id="rb-summary-slot"></div>
                        </div>
                        <button type="button" class="rb-btn-action rb-btn-secondary-action" id="rb-btn-step2-back" style="width:auto; padding:6px 14px; font-size:13px; margin-top:0;">Change Time</button>
                    </div>

                    <form id="rb-details-form">
                        <div class="rb-field-group">
                            <label>Full Name <span class="rb-req">*</span></label>
                            <input type="text" id="rb-input-name" class="rb-input-text" placeholder="e.g. John Smith" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                            <div class="rb-field-group">
                                <label>Mobile Phone <span class="rb-req">*</span></label>
                                <input type="tel" id="rb-input-phone" class="rb-input-text" placeholder="e.g. 07123 456789" required>
                            </div>
                            <div class="rb-field-group">
                                <label>Email Address <span class="rb-req">*</span></label>
                                <input type="email" id="rb-input-email" class="rb-input-text" placeholder="e.g. john@example.co.uk" required>
                            </div>
                        </div>

                        <div class="rb-field-group">
                            <label>Allergies & Dietary Requirements (Optional)</label>
                            <input type="text" id="rb-input-dietary" class="rb-input-text" placeholder="e.g. Nut allergy, Gluten free, Vegetarian">
                        </div>

                        <!-- UK Allergen Advisory Banner -->
                        <div class="rb-allergen-card">
                            <strong>⚠️ Food Allergy Advisory:</strong> ${allergenNotice}
                        </div>

                        <div class="rb-field-group">
                            <label>Special Requests or Occasion (Optional)</label>
                            <textarea id="rb-input-requests" class="rb-textarea" rows="2" placeholder="e.g. Birthday celebration, Quiet table, Highchair needed"></textarea>
                        </div>

                        <!-- GDPR Consent -->
                        <div style="margin-bottom: 20px;">
                            <label style="display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #475569; font-weight: normal; cursor: pointer;">
                                <input type="checkbox" id="rb-input-marketing" style="margin-top: 3px;">
                                <span>I agree to receive occasional news and special dining offers from the restaurant (optional).</span>
                            </label>
                        </div>

                        <div id="rb-submit-error" style="display:none; background:#fee2e2; border-left:4px solid #ef4444; padding:12px; margin-bottom:16px; border-radius:4px; color:#991b1b; font-size:14px;"></div>

                        <button type="submit" class="rb-btn-action rb-btn-primary-action" id="rb-btn-confirm-submit">
                            🔒 Complete & Confirm Reservation
                        </button>
                    </form>
                </div>

                <!-- STEP 3: Instant Confirmation -->
                <div class="rb-step-pane" id="rb-pane-3">
                    <div class="rb-confirm-card">
                        <div class="rb-confirm-icon">✓</div>
                        <h2 style="font-family:var(--rb-font-heading); margin:0 0 6px 0; font-size:24px;">Table Reserved Successfully!</h2>
                        <p style="color:#64748b; margin:0 0 20px 0;">A confirmation email has been sent to <strong id="rb-confirm-email-disp"></strong>.</p>

                        <div class="rb-confirm-ref-box">
                            <span style="font-size:12px; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:1px;">Booking Reference</span>
                            <div class="rb-confirm-ref" id="rb-confirm-ref">CR-00000</div>

                            <div class="rb-confirm-details">
                                <div><span>Date</span><strong id="rb-confirm-date">--</strong></div>
                                <div><span>Time</span><strong id="rb-confirm-time">--</strong></div>
                                <div><span>Party Size</span><strong id="rb-confirm-party">--</strong></div>
                                <div><span>Guest</span><strong id="rb-confirm-name">--</strong></div>
                            </div>
                        </div>

                        <div style="display:flex; flex-direction:column; gap:10px; margin-top:24px;">
                            <a href="#" target="_blank" id="rb-btn-cal-link" class="rb-btn-action rb-btn-primary-action" style="text-decoration:none;">
                                📅 Add to Google Calendar
                            </a>
                            <a href="#" id="rb-btn-manage-link" class="rb-btn-action rb-btn-secondary-action" style="text-decoration:none;">
                                ⚙️ Manage / Cancel Reservation
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;

        $container.html(html);
    }

    function attachBookingEvents($root) {
        // Date change
        $root.on('change', '#rb-input-date', function() {
            bookingState.date = $(this).val();
            bookingState.selectedTime = '';
            $('#rb-btn-step1-next').prop('disabled', true).text('Select a Time Slot to Continue →');
            loadAvailability();
        });

        // Party size dropdown change
        $root.on('change', '#rb-select-party', function() {
            bookingState.partySize = parseInt($(this).val(), 10);
            updatePartyChips();
            bookingState.selectedTime = '';
            $('#rb-btn-step1-next').prop('disabled', true).text('Select a Time Slot to Continue →');
            loadAvailability();
        });

        // Party chips click
        $root.on('click', '.rb-party-chip', function() {
            const size = parseInt($(this).data('size'), 10);
            bookingState.partySize = size;
            $('#rb-select-party').val(size);
            updatePartyChips();
            bookingState.selectedTime = '';
            $('#rb-btn-step1-next').prop('disabled', true).text('Select a Time Slot to Continue →');
            loadAvailability();
        });

        // Time slot click
        $root.on('click', '.rb-time-pill:not(.disabled)', function() {
            $('.rb-time-pill').removeClass('selected');
            $(this).addClass('selected');
            bookingState.selectedTime = $(this).data('time');
            bookingState.selectedService = $(this).data('service');
            const displayTime = $(this).text();

            $('#rb-btn-step1-next').prop('disabled', false).text('Continue to Guest Details (' + displayTime + ') →');
        });

        // Step 1 -> Step 2
        $root.on('click', '#rb-btn-step1-next', function() {
            if (!bookingState.selectedTime) return;
            goToStep(2);
        });

        // Step 2 -> Back to Step 1
        $root.on('click', '#rb-btn-step2-back', function() {
            goToStep(1);
        });

        // Phone Auto-Format (UK)
        $root.on('input', '#rb-input-phone', function() {
            let val = $(this).val().replace(/[^0-9+]/g, '');
            if (val.startsWith('+447') && val.length > 4) {
                val = '07' + val.substring(4);
            }
            if (val.length === 11 && val.startsWith('07')) {
                $(this).val(val.substring(0, 5) + ' ' + val.substring(5));
            }
        });

        // Step 2 Form Submit (Reservation Confirmation)
        $root.on('submit', '#rb-details-form', function(e) {
            e.preventDefault();
            $('#rb-submit-error').hide();

            bookingState.customerName   = $('#rb-input-name').val();
            bookingState.phone          = $('#rb-input-phone').val();
            bookingState.email          = $('#rb-input-email').val();
            bookingState.dietaryNotes   = $('#rb-input-dietary').val();
            bookingState.specialRequests= $('#rb-input-requests').val();
            bookingState.marketingConsent = $('#rb-input-marketing').is(':checked');

            const payload = {
                customer_name: bookingState.customerName,
                phone: bookingState.phone,
                email: bookingState.email,
                party_size: bookingState.partySize,
                booking_date: bookingState.date,
                start_time: bookingState.selectedTime,
                special_requests: bookingState.specialRequests,
                dietary_notes: bookingState.dietaryNotes,
                marketing_consent: bookingState.marketingConsent ? 1 : 0,
                source: 'web'
            };

            const $btn = $('#rb-btn-confirm-submit');
            $btn.prop('disabled', true).text('Verifying availability and confirming...');

            $.ajax({
                url: rbData.restUrl + 'reservations',
                method: 'POST',
                data: JSON.stringify(payload),
                headers: {
                    'X-WP-Nonce': rbData.nonce,
                    'Content-Type': 'application/json'
                }
            }).done(function(res) {
                if (res.success && res.reservation) {
                    bookingState.reservation = res.reservation;
                    showConfirmationStep(res.reservation);
                } else {
                    handleBookingError(res.message || 'Could not complete reservation.');
                }
            }).fail(function(xhr) {
                const err = xhr.responseJSON;
                if (err && err.code === 'slot_unavailable' && err.nearby_slots) {
                    let altHtml = '<p><strong>' + err.message + '</strong></p><p>Nearby available slots:</p><div style="display:flex; gap:8px; flex-wrap:wrap; margin-top:8px;">';
                    err.nearby_slots.forEach(function(s) {
                        altHtml += '<button type="button" class="rb-time-pill rb-btn-pick-alt" data-time="' + s.time + '" style="width:auto; padding:6px 12px;">' + s.display_time + '</button>';
                    });
                    altHtml += '</div>';
                    $('#rb-submit-error').html(altHtml).show();

                    $('.rb-btn-pick-alt').on('click', function() {
                        bookingState.selectedTime = $(this).data('time');
                        $('#rb-details-form').trigger('submit');
                    });
                } else {
                    handleBookingError(err ? err.message : 'A network error occurred. Please try again.');
                }
                $btn.prop('disabled', false).text('🔒 Complete & Confirm Reservation');
            });
        });
    }

    function handleBookingError(msg) {
        $('#rb-submit-error').html('<strong>Error:</strong> ' + msg).show();
        $('html, body').animate({ scrollTop: $('#rb-submit-error').offset().top - 100 }, 300);
    }

    function loadAvailability() {
        const $slots = $('#rb-slots-container');
        $slots.html('<div class="rb-spinner-wrap"><div class="rb-spinner"></div><p>Checking live table availability...</p></div>');

        $.ajax({
            url: rbData.restUrl + 'availability',
            method: 'GET',
            data: {
                date: bookingState.date,
                party_size: bookingState.partySize
            }
        }).done(function(res) {
            if (res.success) {
                if (res.is_closed) {
                    $slots.html('<div style="padding:24px; background:#fffbeb; border:1px solid #fde68a; border-radius:8px; text-align:center; color:#92400e;"><strong>Restaurant Closed:</strong> ' + res.message + '</div>');
                    return;
                }

                if (!res.services || res.services.length === 0) {
                    $slots.html('<div style="padding:24px; background:#f8fafc; border-radius:8px; text-align:center; color:#64748b;">No available dining slots found for this date.</div>');
                    return;
                }

                let html = '';
                res.services.forEach(function(serv) {
                    html += '<div class="rb-service-section">';
                    html += '<div class="rb-service-title"><span>' + serv.service_name + '</span><span class="rb-service-hours">' + serv.open_time + ' - ' + serv.close_time + '</span></div>';
                    html += '<div class="rb-slots-grid">';

                    serv.slots.forEach(function(s) {
                        const disabledClass = s.is_available ? '' : 'disabled';
                        const selectedClass = (s.time === bookingState.selectedTime) ? 'selected' : '';
                        html += '<div class="rb-time-pill ' + disabledClass + ' ' + selectedClass + '" data-time="' + s.time + '" data-service="' + serv.service_name + '">' + s.display_time + '</div>';
                    });

                    html += '</div></div>';
                });

                $slots.html(html);
            }
        }).fail(function(err) {
            $slots.html('<div style="padding:20px; color:#ef4444; text-align:center;">Could not load availability. Please try again.</div>');
        });
    }

    function updatePartyChips() {
        $('.rb-party-chip').removeClass('selected');
        const match = $('.rb-party-chip[data-size="' + bookingState.partySize + '"]');
        if (match.length) {
            match.addClass('selected');
        } else if (bookingState.partySize >= 6) {
            $('.rb-party-chip[data-size="6"]').addClass('selected');
        }
    }

    function goToStep(step) {
        $('.rb-step-item').removeClass('active');
        $('.rb-step-pane').removeClass('active');

        $('#rb-step-ind-' + step).addClass('active');
        $('#rb-pane-' + step).addClass('active');

        if (step === 2) {
            $('#rb-step-ind-1').addClass('completed');
            const selectedPill = $('.rb-time-pill.selected');
            const timeText = selectedPill.length ? selectedPill.text() : bookingState.selectedTime;
            $('#rb-summary-slot').text(bookingState.date + ' @ ' + timeText + ' (' + bookingState.partySize + ' Guests)');
        }
    }

    function showConfirmationStep(res) {
        goToStep(3);
        $('#rb-step-ind-2').addClass('completed');
        $('#rb-step-ind-3').addClass('completed');

        $('#rb-confirm-email-disp').text(res.email);
        $('#rb-confirm-ref').text(res.booking_reference);
        $('#rb-confirm-date').text(res.date_formatted);
        $('#rb-confirm-time').text(res.time_formatted);
        $('#rb-confirm-party').text(res.party_size + ' Guests');
        $('#rb-confirm-name').text(res.customer_name);

        $('#rb-btn-manage-link').attr('href', res.manage_url);

        // Google Calendar Link generator
        const startDt = res.booking_date.replace(/-/g, '') + 'T' + res.start_time.replace(/:/g, '');
        const endDt   = res.booking_date.replace(/-/g, '') + 'T' + res.end_time.replace(/:/g, '');
        const calUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=' + encodeURIComponent('Table Reservation - Ref: ' + res.booking_reference) + '&dates=' + startDt + '/' + endDt + '&details=' + encodeURIComponent('Reference: ' + res.booking_reference + '\nManage: ' + res.manage_url);
        $('#rb-btn-cal-link').attr('href', calUrl);
    }

    // 2. Initialize Passwordless Guest Manage Portal
    function initManageApp() {
        const $root = $('#rb-manage-root');
        const token = $root.data('token') || (rbData && rbData.token ? rbData.token : '');

        if (!token) {
            $root.html('<div style="padding:40px; text-align:center; color:#64748b;">No booking management token provided. Please check your confirmation link.</div>');
            return;
        }

        $.ajax({
            url: rbData.restUrl + 'reservations/' + token,
            method: 'GET'
        }).done(function(res) {
            if (res.success && res.reservation) {
                renderManagePortal($root, res.reservation, res.can_cancel, res.cutoff_hours, res.restaurant);
            } else {
                $root.html('<div style="padding:40px; text-align:center; color:#ef4444;">Reservation not found.</div>');
            }
        }).fail(function() {
            $root.html('<div style="padding:40px; text-align:center; color:#ef4444;">Unable to load reservation details.</div>');
        });
    }

    function renderManagePortal($container, r, canCancel, cutoffHours, restaurant) {
        let cancelActionHtml = '';
        if (r.status === 'confirmed' || r.status === 'pending') {
            if (canCancel) {
                cancelActionHtml = `
                <button type="button" class="rb-btn-action rb-btn-secondary-action" id="rb-btn-guest-cancel" style="color:#991b1b; border-color:#fca5a5; margin-top:20px;">
                    ✕ Cancel This Table Reservation
                </button>`;
            } else {
                cancelActionHtml = `
                <div style="background:#fffbeb; border-left:4px solid #f59e0b; padding:12px; margin-top:20px; font-size:13px; color:#92400e; text-align:left;">
                    <strong>Late Cancellation Notice:</strong> Online cancellation is permitted up to ${cutoffHours} hours before dining. Please telephone the restaurant directly on <strong>${restaurant.phone}</strong>.
                </div>`;
            }
        }

        const html = `
        <div class="rb-booking-widget-wrapper" style="max-width: 580px;">
            <div class="rb-widget-header">
                <h2>Manage Your Reservation</h2>
                <p>Reference: <strong>${r.booking_reference}</strong></p>
            </div>
            <div class="rb-widget-body" style="text-align:center;">
                <div style="margin-bottom:16px;">
                    <span class="rb-time-pill selected" style="display:inline-block; width:auto; padding:6px 16px;">STATUS: ${r.status_label}</span>
                </div>
                <div class="rb-confirm-ref-box" style="text-align:left;">
                    <div class="rb-confirm-details" style="grid-template-columns:1fr 1fr;">
                        <div><span>Guest</span><strong>${escapeHtml(r.customer_name)}</strong></div>
                        <div><span>Party Size</span><strong>${r.party_size} Guests</strong></div>
                        <div><span>Date</span><strong>${r.date_formatted}</strong></div>
                        <div><span>Time</span><strong>${r.time_formatted}</strong></div>
                    </div>
                </div>

                ${cancelActionHtml}
            </div>
        </div>`;

        $container.html(html);

        $('#rb-btn-guest-cancel').on('click', function() {
            if (confirm('Are you sure you want to cancel your table reservation?')) {
                const token = r.secure_token;
                $.ajax({
                    url: rbData.restUrl + 'reservations/' + token + '/cancel',
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    data: JSON.stringify({ reason: 'Customer requested cancellation online' })
                }).done(function(resp) {
                    alert('Your reservation has been cancelled.');
                    location.reload();
                }).fail(function(xhr) {
                    alert('Error: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Could not cancel reservation'));
                });
            }
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

})(jQuery);
