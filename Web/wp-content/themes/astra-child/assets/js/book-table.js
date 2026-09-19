/**
 * The Cochin - Interactive Live Restaurant Table Booking & Management Engine
 */
(function($) {
    'use strict';

    const state = {
        date: '',
        partySize: 2,
        duration: 120, // in minutes (60, 90, 120, 150)
        durationDisplay: '2 hrs',
        selectedTime: '',
        selectedTimeDisplay: '',
        selectedEndTime: '',
        selectedEndTimeDisplay: '',
        selectedWindowDisplay: '',
        selectedService: '',
        customerName: '',
        phone: '',
        email: '',
        occasion: 'Casual Dining',
        dietaryNotes: '',
        specialRequests: '',
        marketingConsent: false,
        reservation: null
    };

    $(document).ready(function() {
        initBookingEngine();
    });

    function initBookingEngine() {
        const $container = $('#cochinBookingApp');
        if (!$container.length) return;

        // Set default date to today
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        state.date = `${yyyy}-${mm}-${dd}`;

        const $dateInput = $('#res_booking_date');
        if ($dateInput.length) {
            $dateInput.val(state.date);
            $dateInput.attr('min', state.date);

            // Max date: 90 days ahead
            const maxDate = new Date();
            maxDate.setDate(maxDate.getDate() + 90);
            const maxY = maxDate.getFullYear();
            const maxM = String(maxDate.getMonth() + 1).padStart(2, '0');
            const maxD = String(maxDate.getDate()).padStart(2, '0');
            $dateInput.attr('max', `${maxY}-${maxM}-${maxD}`);
        }

        // Set default duration
        const $durSelect = $('#res_duration');
        if ($durSelect.length) {
            state.duration = parseInt($durSelect.val(), 10) || 120;
            state.durationDisplay = getDurationLabel(state.duration);
        }

        attachEventListeners();

        // Check if initial token or reference was provided in URL
        const data = window.cochinBookingData || {};
        if (data.initialToken) {
            switchTab('manage');
            $('#manage_search_input').val(data.initialToken);
            lookupReservation(data.initialToken);
        } else if (data.initialView === 'manage') {
            switchTab('manage');
        } else {
            loadAvailability();
        }
    }

    function attachEventListeners() {
        const data = window.cochinBookingData || {};

        // 1. Navigation Tab Switching
        $('.cochin-booking-tab-btn').on('click', function(e) {
            e.preventDefault();
            const targetTab = $(this).data('tab');
            switchTab(targetTab);
        });

        // 2. Date Input Change
        $('#res_booking_date').on('change', function() {
            state.date = $(this).val();
            resetSlotSelection();
            loadAvailability();
        });

        // 3. Party Size Select Change
        $('#res_party_size').on('change', function() {
            const size = parseInt($(this).val(), 10) || 2;
            setPartySize(size);
        });

        // 4. Party Size Chip Click
        $(document).on('click', '.cochin-party-chip', function() {
            const size = parseInt($(this).data('size'), 10) || 2;
            $('#res_party_size').val(size);
            setPartySize(size);
        });

        // 5. Duration Select Change
        $('#res_duration').on('change', function() {
            const duration = parseInt($(this).val(), 10) || 120;
            setDuration(duration);
        });

        // 6. Duration Chip Click
        $(document).on('click', '.cochin-duration-chip', function() {
            const duration = parseInt($(this).data('duration'), 10) || 120;
            $('#res_duration').val(duration);
            setDuration(duration);
        });

        // 7. Time Slot Pill Click
        $(document).on('click', '.cochin-slot-pill:not(.disabled)', function() {
            $('.cochin-slot-pill').removeClass('selected');
            $(this).addClass('selected');

            state.selectedTime = $(this).data('time');
            state.selectedTimeDisplay = $(this).find('.cochin-slot-time').text().trim() || $(this).text().trim();
            state.selectedService = $(this).data('service');

            const windowInfo = calculateTimeWindow(state.selectedTime, state.duration);
            state.selectedEndTime = windowInfo.endTime;
            state.selectedEndTimeDisplay = windowInfo.endTimeDisplay;
            state.selectedWindowDisplay = windowInfo.windowDisplay;

            $('#step1_continue_btn')
                .prop('disabled', false)
                .html('Continue to Guest Details (' + state.selectedWindowDisplay + ') &rarr;');
        });

        // 8. Step 1 -> Step 2
        $('#step1_continue_btn').on('click', function(e) {
            e.preventDefault();
            if (!state.selectedTime) return;
            goToStep(2);
        });

        // 9. Step 2 -> Back to Step 1
        $('#step2_back_btn, #change_slot_link').on('click', function(e) {
            e.preventDefault();
            goToStep(1);
        });

        // 10. Phone Auto Formatting for UK
        $('#res_phone').on('input', function() {
            let val = $(this).val().replace(/[^0-9+]/g, '');
            if (val.startsWith('+447') && val.length > 4) {
                val = '07' + val.substring(4);
            }
            if (val.length === 11 && (val.startsWith('07') || val.startsWith('01') || val.startsWith('02'))) {
                $(this).val(val.substring(0, 5) + ' ' + val.substring(5));
            }
        });

        // 11. Step 2 Details Form Submission
        $('#theCochinReservationForm').on('submit', function(e) {
            e.preventDefault();
            submitReservation();
        });

        // 12. Book Another Table Button
        $('#btn_book_another').on('click', function(e) {
            e.preventDefault();
            resetBookingForm();
            goToStep(1);
            loadAvailability();
        });

        // 13. Manage Reservation Search Form
        $('#manageSearchForm').on('submit', function(e) {
            e.preventDefault();
            const token = $('#manage_search_input').val().trim();
            if (token) {
                lookupReservation(token);
            }
        });

        // 14. Self-Service Cancel Reservation Click (Show Inline Confirmation)
        $(document).on('click', '#btn_cancel_reservation', function(e) {
            e.preventDefault();
            $('#cancel_initial_block').hide();
            $('#cancel_confirm_block').slideDown(200);
        });

        // 15. Abort Cancellation
        $(document).on('click', '#btn_abort_cancel_action', function(e) {
            e.preventDefault();
            $('#cancel_confirm_block').slideUp(150, function() {
                $('#cancel_initial_block').show();
            });
        });

        // 16. Confirm Cancellation Action
        $(document).on('click', '#btn_confirm_cancel_action', function(e) {
            e.preventDefault();
            const token = $(this).data('token');
            if (token) {
                cancelReservation(token);
            }
        });
    }

    function switchTab(tabId) {
        $('.cochin-booking-tab-btn').removeClass('active');
        $(`.cochin-booking-tab-btn[data-tab="${tabId}"]`).addClass('active');

        $('.cochin-tab-pane').removeClass('active');
        $(`#tab_pane_${tabId}`).addClass('active');

        if (tabId === 'book' && !$('#slots_list_container .cochin-service-group').length) {
            loadAvailability();
        }
    }

    function setPartySize(size) {
        state.partySize = size;
        $('.cochin-party-chip').removeClass('selected');
        $(`.cochin-party-chip[data-size="${size}"]`).addClass('selected');

        // Check large party alert
        if (size >= 6) {
            $('#large_group_notice').slideDown(200);
        } else {
            $('#large_group_notice').slideUp(200);
        }

        resetSlotSelection();
        loadAvailability();
    }

    function setDuration(durationMinutes) {
        state.duration = durationMinutes;
        state.durationDisplay = getDurationLabel(durationMinutes);

        $('.cochin-duration-chip').removeClass('selected');
        $(`.cochin-duration-chip[data-duration="${durationMinutes}"]`).addClass('selected');

        if (state.selectedTime) {
            const windowInfo = calculateTimeWindow(state.selectedTime, state.duration);
            state.selectedEndTime = windowInfo.endTime;
            state.selectedEndTimeDisplay = windowInfo.endTimeDisplay;
            state.selectedWindowDisplay = windowInfo.windowDisplay;

            $('#step1_continue_btn')
                .prop('disabled', false)
                .html('Continue to Guest Details (' + state.selectedWindowDisplay + ') &rarr;');
        }

        loadAvailability();
    }

    function resetSlotSelection() {
        state.selectedTime = '';
        state.selectedTimeDisplay = '';
        state.selectedEndTime = '';
        state.selectedEndTimeDisplay = '';
        state.selectedWindowDisplay = '';
        $('#step1_continue_btn').prop('disabled', true).html('Select a Time Slot to Continue &rarr;');
    }

    function goToStep(stepNumber) {
        $('.cochin-step-indicator').removeClass('active completed');
        $('.cochin-step-card').removeClass('active');

        for (let i = 1; i < stepNumber; i++) {
            $(`#step_ind_${i}`).addClass('completed');
        }
        $(`#step_ind_${stepNumber}`).addClass('active');
        $(`#step_card_${stepNumber}`).addClass('active');

        if (stepNumber === 2) {
            // Update summary chip
            const formattedDate = formatDateDisplay(state.date);
            $('#summary_date_disp').text(formattedDate);
            $('#summary_window_disp').text(state.selectedWindowDisplay || (state.selectedTimeDisplay + ' (' + state.durationDisplay + ')'));
            $('#summary_party_disp').text(state.partySize + (state.partySize === 1 ? ' Guest' : ' Guests'));
            $('#summary_service_disp').text(state.selectedService);
        }

        // Smooth scroll to top of booking widget
        const $widget = $('#cochinBookingApp');
        if ($widget.length) {
            $('html, body').animate({
                scrollTop: $widget.offset().top - 120
            }, 350);
        }
    }

    function loadAvailability() {
        const data = window.cochinBookingData || {};
        const $slots = $('#slots_list_container');

        $slots.html(`
            <div class="cochin-loading-slots">
                <div class="cochin-spinner"></div>
                <p>Checking live table availability for ${formatDateDisplay(state.date)} (${state.durationDisplay})...</p>
            </div>
        `);

        $.ajax({
            url: data.restUrl + 'availability',
            method: 'GET',
            data: {
                date: state.date,
                party_size: state.partySize,
                duration: state.duration
            }
        }).done(function(res) {
            if (res && res.success) {
                if (res.is_closed) {
                    $slots.html(`
                        <div class="cochin-closed-alert">
                            <span class="cochin-alert-icon">🏮</span>
                            <h4>Restaurant Closed</h4>
                            <p>${escapeHtml(res.message || 'The restaurant is closed for this selected date. Please choose another dining date.')}</p>
                        </div>
                    `);
                    return;
                }

                if (!res.services || res.services.length === 0) {
                    $slots.html(`
                        <div class="cochin-no-slots-alert">
                            <p>No dining services or slots scheduled for this date. Please select another date.</p>
                        </div>
                    `);
                    return;
                }

                let html = '';
                let totalAvailable = 0;

                res.services.forEach(function(serv) {
                    let availCount = 0;
                    serv.slots.forEach(function(s) {
                        if (s.is_available) availCount++;
                    });
                    totalAvailable += availCount;

                    html += `
                    <div class="cochin-service-group">
                        <div class="cochin-service-header">
                            <div class="cochin-service-title-wrap">
                                <h4 class="cochin-service-name">${escapeHtml(serv.service_name)}</h4>
                                <span class="cochin-service-timing">${escapeHtml(serv.open_time)} – ${escapeHtml(serv.close_time)}</span>
                            </div>
                            <span class="cochin-service-slot-badge ${availCount > 0 ? 'available' : 'full'}">
                                ${availCount > 0 ? availCount + ' slots open' : 'Fully Booked'}
                            </span>
                        </div>
                        <div class="cochin-slots-grid">
                    `;

                    serv.slots.forEach(function(s) {
                        const isAvail = s.is_available;
                        const isSelected = (s.time === state.selectedTime);
                        const disabledClass = isAvail ? '' : 'disabled';
                        const selectedClass = isSelected ? 'selected' : '';

                        html += `
                        <button type="button" 
                                class="cochin-slot-pill ${disabledClass} ${selectedClass}" 
                                data-time="${escapeHtml(s.time)}" 
                                data-service="${escapeHtml(serv.service_name)}"
                                ${!isAvail ? 'disabled title="Unavailable for ' + state.partySize + ' guests"' : ''}>
                            <span class="cochin-slot-time">${escapeHtml(s.display_time)}</span>
                            ${!isAvail ? '<span class="cochin-slot-tag">Full</span>' : ''}
                        </button>
                        `;
                    });

                    html += `
                        </div>
                    </div>
                    `;
                });

                if (totalAvailable === 0) {
                    html += `
                    <div class="cochin-waitlist-callout">
                        <div class="cochin-waitlist-icon">📞</div>
                        <div class="cochin-waitlist-text">
                            <strong>All online tables are booked for this party size and duration.</strong>
                            <p>For last-minute availability or table cancellations, please call our dining team directly on <a href="tel:${escapeHtml(data.restaurantPhone || '01442 233777')}">${escapeHtml(data.restaurantPhone || '01442 233777')}</a>.</p>
                        </div>
                    </div>
                    `;
                }

                $slots.html(html);
            } else {
                $slots.html(`
                    <div class="cochin-error-alert">
                        <p>${escapeHtml(res.message || 'Could not fetch availability. Please try again.')}</p>
                    </div>
                `);
            }
        }).fail(function() {
            $slots.html(`
                <div class="cochin-error-alert">
                    <p>Unable to connect to live reservation engine. Please call us directly on <a href="tel:${escapeHtml(data.restaurantPhone || '01442 233777')}">${escapeHtml(data.restaurantPhone || '01442 233777')}</a>.</p>
                </div>
            `);
        });
    }

    function submitReservation() {
        const data = window.cochinBookingData || {};
        const $btn = $('#res_submit_btn');
        const $feedback = $('#reservationFeedback');

        $feedback.hide().empty().removeClass('success error');

        state.customerName    = $('#res_fullname').val().trim();
        state.phone           = $('#res_phone').val().trim();
        state.email           = $('#res_email').val().trim();
        state.occasion        = $('#res_occasion').val();
        state.dietaryNotes    = $('#res_dietary').val().trim();
        state.specialRequests = $('#res_requirements').val().trim();
        state.marketingConsent= $('#res_marketing').is(':checked');

        if (!state.customerName || !state.phone || !state.email) {
            showError('Please complete all required fields (Name, Phone, Email).');
            return;
        }

        const durationInfoNote = ` [Duration: ${state.durationDisplay}]`;
        const payload = {
            customer_name: state.customerName,
            phone: state.phone,
            email: state.email,
            party_size: state.partySize,
            booking_date: state.date,
            start_time: state.selectedTime,
            duration: state.duration,
            duration_minutes: state.duration,
            special_requests: state.specialRequests + (state.occasion ? ' [Occasion: ' + state.occasion + ']' : '') + durationInfoNote,
            dietary_notes: state.dietaryNotes,
            marketing_consent: state.marketingConsent ? 1 : 0,
            source: 'web'
        };

        $btn.prop('disabled', true).html('<div class="cochin-btn-spinner"></div> Confirming Reservation...');

        $.ajax({
            url: data.restUrl + 'reservations',
            method: 'POST',
            data: JSON.stringify(payload),
            headers: {
                'X-WP-Nonce': data.nonce,
                'Content-Type': 'application/json'
            }
        }).done(function(res) {
            $btn.prop('disabled', false).html('<span>Confirm My Reservation</span>');

            if (res && res.success && res.reservation) {
                state.reservation = res.reservation;
                renderConfirmation(res.reservation);
                goToStep(3);
            } else {
                showError(res && res.message ? res.message : 'Could not complete reservation.');
            }
        }).fail(function(xhr) {
            $btn.prop('disabled', false).html('<span>Confirm My Reservation</span>');
            const err = xhr.responseJSON;

            if (err && err.code === 'slot_unavailable' && err.nearby_slots && err.nearby_slots.length) {
                let altHtml = `
                    <div class="cochin-alt-slots-box">
                        <strong>⚠️ ${escapeHtml(err.message)}</strong>
                        <p>Suggested available times on this date:</p>
                        <div class="cochin-alt-pills">
                `;
                err.nearby_slots.forEach(function(s) {
                    altHtml += `<button type="button" class="cochin-slot-pill cochin-btn-pick-alt" data-time="${escapeHtml(s.time)}">${escapeHtml(s.display_time)}</button>`;
                });
                altHtml += `</div></div>`;
                $feedback.html(altHtml).addClass('error').show();

                $('.cochin-btn-pick-alt').on('click', function() {
                    state.selectedTime = $(this).data('time');
                    state.selectedTimeDisplay = $(this).text().trim();
                    const windowInfo = calculateTimeWindow(state.selectedTime, state.duration);
                    state.selectedEndTime = windowInfo.endTime;
                    state.selectedEndTimeDisplay = windowInfo.endTimeDisplay;
                    state.selectedWindowDisplay = windowInfo.windowDisplay;
                    $('#summary_window_disp').text(state.selectedWindowDisplay);
                    submitReservation();
                });
            } else {
                showError(err && err.message ? err.message : 'A network error occurred. Please call us directly on 01442 233777.');
            }
        });
    }

    function showError(msg) {
        const $feedback = $('#reservationFeedback');
        $feedback.html(`<strong>⚠️ Notice:</strong> ${escapeHtml(msg)}`).addClass('error').show();
        $('html, body').animate({
            scrollTop: $feedback.offset().top - 120
        }, 300);
    }

    function renderConfirmation(res) {
        const data = window.cochinBookingData || {};

        const windowDisplay = res.time_formatted && res.end_time ? 
            (formatTimeFromStr(res.start_time) + ' – ' + formatTimeFromStr(res.end_time) + ' (' + state.durationDisplay + ')') : 
            (state.selectedWindowDisplay || res.time_formatted);

        $('#conf_ref_badge').text(res.booking_reference || 'CR-00000');
        $('#conf_guest_disp').text(res.customer_name);
        $('#conf_email_disp').text(res.email);
        $('#conf_phone_disp').text(res.phone);
        $('#conf_party_disp').text(res.party_size + (res.party_size === 1 ? ' Guest' : ' Guests'));
        $('#conf_date_disp').text(res.date_formatted || res.booking_date);
        $('#conf_window_disp').text(windowDisplay);
        $('#conf_status_disp').text(res.status_label || (res.status ? res.status.toUpperCase() : 'CONFIRMED'));

        // Google Calendar URL
        const startClean = (res.booking_date || '').replace(/-/g, '') + 'T' + (res.start_time || '').replace(/:/g, '');
        const endClean = (res.booking_date || '').replace(/-/g, '') + 'T' + (res.end_time || '').replace(/:/g, '');
        const calUrl = 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=' + 
            encodeURIComponent('Dining at The Cochin - Ref: ' + res.booking_reference) + 
            '&dates=' + startClean + '/' + endClean + 
            '&location=' + encodeURIComponent('61 High Street, Hemel Hempstead, HP1 3AF') + 
            '&details=' + encodeURIComponent('Table Reservation at The Cochin Indian Restaurant.\nReference: ' + res.booking_reference + '\nParty Size: ' + res.party_size + ' Guests\nDining Window: ' + windowDisplay + '\nTelephone: ' + (data.restaurantPhone || '01442 233777'));

        $('#conf_btn_calendar').attr('href', calUrl);

        // Manage Link
        $('#conf_btn_manage').off('click').on('click', function(e) {
            e.preventDefault();
            switchTab('manage');
            $('#manage_search_input').val(res.secure_token || res.booking_reference);
            lookupReservation(res.secure_token || res.booking_reference);
        });
    }

    function lookupReservation(tokenOrRef) {
        const data = window.cochinBookingData || {};
        const $result = $('#manage_result_container');

        $result.html(`
            <div class="cochin-loading-slots">
                <div class="cochin-spinner"></div>
                <p>Locating your reservation...</p>
            </div>
        `).show();

        $.ajax({
            url: data.restUrl + 'reservations/' + encodeURIComponent(tokenOrRef),
            method: 'GET'
        }).done(function(res) {
            if (res && res.success && res.reservation) {
                renderManageCard(res.reservation, res.can_cancel, res.cutoff_hours, res.restaurant);
            } else {
                $result.html(`
                    <div class="cochin-error-alert">
                        <strong>Reservation Not Found</strong>
                        <p>We could not find any active table booking with the reference or token provided. Please double check your booking reference code.</p>
                    </div>
                `);
            }
        }).fail(function(xhr) {
            const err = xhr.responseJSON;
            $result.html(`
                <div class="cochin-error-alert">
                    <strong>Reservation Not Found</strong>
                    <p>${escapeHtml(err && err.message ? err.message : 'Unable to find reservation. Please verify your reference or call 01442 233777.')}</p>
                </div>
            `);
        });
    }

    function renderManageCard(r, canCancel, cutoffHours, restaurant) {
        const data = window.cochinBookingData || {};
        const phone = (restaurant && restaurant.phone) ? restaurant.phone : (data.restaurantPhone || '01442 233777');
        const $result = $('#manage_result_container');

        const windowText = (r.start_time && r.end_time) ? 
            (formatTimeFromStr(r.start_time) + ' – ' + formatTimeFromStr(r.end_time)) : 
            (r.time_formatted || r.start_time);

        let cancelBlock = '';
        if (r.status === 'confirmed' || r.status === 'pending') {
            if (canCancel) {
                cancelBlock = `
                    <div class="cochin-manage-actions">
                        <div id="cancel_initial_block">
                            <button type="button" class="cochin-btn-danger" id="btn_cancel_reservation">
                                ✕ Cancel Table Reservation
                            </button>
                        </div>
                        <div id="cancel_confirm_block" class="cochin-cancel-confirm-box" style="display:none;">
                            <div class="cochin-cancel-confirm-icon">⚠️</div>
                            <p><strong>Are you sure you wish to cancel your table reservation?</strong></p>
                            <div class="cochin-cancel-confirm-btns">
                                <button type="button" class="cochin-btn-danger-confirm" id="btn_confirm_cancel_action" data-token="${escapeHtml(r.secure_token || r.booking_reference)}">
                                    Yes, Cancel Table
                                </button>
                                <button type="button" class="cochin-btn-cancel-abort" id="btn_abort_cancel_action">
                                    Keep Reservation
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                cancelBlock = `
                    <div class="cochin-cutoff-warning">
                        <strong>⚠️ Online Cancellation Cut-Off Passed</strong>
                        <p>Online cancellations are permitted up to ${cutoffHours || 2} hours before dining. To adjust or cancel your table, please telephone the restaurant directly on <strong><a href="tel:${escapeHtml(phone)}">${escapeHtml(phone)}</a></strong>.</p>
                    </div>
                `;
            }
        } else if (r.status === 'cancelled') {
            cancelBlock = `
                <div class="cochin-cancelled-badge-box">
                    <span class="cochin-status-tag cancelled">CANCELLED</span>
                    <p>This reservation has been cancelled. If you would like to dine with us, please make a new booking.</p>
                </div>
            `;
        }

        const html = `
        <div class="cochin-manage-card">
            <div class="cochin-manage-card-header">
                <div class="cochin-manage-title-block">
                    <span class="cochin-manage-ref-label">Booking Reference</span>
                    <h3 class="cochin-manage-ref-code">${escapeHtml(r.booking_reference)}</h3>
                </div>
                <div class="cochin-manage-status-pill ${escapeHtml(r.status)}">
                    ${escapeHtml(r.status_label || r.status.toUpperCase())}
                </div>
            </div>

            <div class="cochin-manage-grid">
                <div class="cochin-manage-item">
                    <span class="cochin-item-lbl">Guest Name</span>
                    <strong class="cochin-item-val">${escapeHtml(r.customer_name)}</strong>
                </div>
                <div class="cochin-manage-item">
                    <span class="cochin-item-lbl">Party Size</span>
                    <strong class="cochin-item-val">${escapeHtml(r.party_size)} Guests</strong>
                </div>
                <div class="cochin-manage-item">
                    <span class="cochin-item-lbl">Date</span>
                    <strong class="cochin-item-val">${escapeHtml(r.date_formatted || r.booking_date)}</strong>
                </div>
                <div class="cochin-manage-item">
                    <span class="cochin-item-lbl">Dining Window</span>
                    <strong class="cochin-item-val">${escapeHtml(windowText)}</strong>
                </div>
                <div class="cochin-manage-item">
                    <span class="cochin-item-lbl">Contact Phone</span>
                    <strong class="cochin-item-val">${escapeHtml(r.phone)}</strong>
                </div>
                <div class="cochin-manage-item">
                    <span class="cochin-item-lbl">Email</span>
                    <strong class="cochin-item-val">${escapeHtml(r.email)}</strong>
                </div>
            </div>

            ${r.special_requests ? `
            <div class="cochin-manage-notes">
                <span class="cochin-item-lbl">Special Requests / Occasion</span>
                <p>${escapeHtml(r.special_requests)}</p>
            </div>
            ` : ''}

            ${r.dietary_notes ? `
            <div class="cochin-manage-notes">
                <span class="cochin-item-lbl">Dietary & Allergy Requirements</span>
                <p>${escapeHtml(r.dietary_notes)}</p>
            </div>
            ` : ''}

            ${cancelBlock}
        </div>
        `;

        $result.html(html).show();
    }

    function cancelReservation(token) {
        const data = window.cochinBookingData || {};
        const $result = $('#manage_result_container');

        const $btn = $('#btn_confirm_cancel_action');
        $btn.prop('disabled', true).text('Cancelling Table...');

        $.ajax({
            url: data.restUrl + 'reservations/' + encodeURIComponent(token) + '/cancel',
            method: 'POST',
            data: JSON.stringify({ reason: 'Guest cancelled via website self-service portal' }),
            headers: {
                'X-WP-Nonce': data.nonce,
                'Content-Type': 'application/json'
            }
        }).done(function(res) {
            if (res && res.success) {
                lookupReservation(token);
            } else {
                alert('Could not cancel reservation: ' + (res && res.message ? res.message : 'Unknown error'));
                $btn.prop('disabled', false).text('Yes, Cancel Table');
            }
        }).fail(function(xhr) {
            const err = xhr.responseJSON;
            alert('Error: ' + (err && err.message ? err.message : 'Could not cancel reservation.'));
            $btn.prop('disabled', false).text('Yes, Cancel Table');
        });
    }

    function resetBookingForm() {
        const form = document.getElementById('theCochinReservationForm');
        if (form) form.reset();
        resetSlotSelection();
        $('#reservationFeedback').hide().empty();
    }

    function calculateTimeWindow(timeStr, durationMinutes) {
        if (!timeStr) return { endTime: '', endTimeDisplay: '', windowDisplay: '' };
        
        const parts = timeStr.split(':');
        const h = parseInt(parts[0], 10) || 0;
        const m = parseInt(parts[1], 10) || 0;
        
        const startTotalMins = (h * 60) + m;
        const endTotalMins = startTotalMins + (durationMinutes || 120);

        const endH = Math.floor(endTotalMins / 60) % 24;
        const endM = endTotalMins % 60;

        const endTimeStr = String(endH).padStart(2, '0') + ':' + String(endM).padStart(2, '0') + ':00';
        const startDisplay = formatTimeFromHAndM(h, m);
        const endDisplay = formatTimeFromHAndM(endH, endM);
        const durLabel = getDurationLabel(durationMinutes);

        return {
            startTime: timeStr,
            startTimeDisplay: startDisplay,
            endTime: endTimeStr,
            endTimeDisplay: endDisplay,
            windowDisplay: `${startDisplay} – ${endDisplay} (${durLabel})`
        };
    }

    function getDurationLabel(mins) {
        if (mins === 60) return '1 hr';
        if (mins === 90) return '1.5 hrs';
        if (mins === 120) return '2 hrs';
        if (mins === 150) return '2.5 hrs';
        if (mins >= 60) {
            const h = Math.floor(mins / 60);
            const m = mins % 60;
            return m > 0 ? `${h}.${Math.round(m/6)} hrs` : `${h} hrs`;
        }
        return `${mins} mins`;
    }

    function formatTimeFromHAndM(h, m) {
        const ampm = h >= 12 ? 'PM' : 'AM';
        let h12 = h % 12;
        if (h12 === 0) h12 = 12;
        const mStr = m > 0 ? ':' + String(m).padStart(2, '0') : ':00';
        return `${h12}${mStr} ${ampm}`;
    }

    function formatTimeFromStr(timeStr) {
        if (!timeStr) return '';
        const parts = timeStr.split(':');
        const h = parseInt(parts[0], 10) || 0;
        const m = parseInt(parts[1], 10) || 0;
        return formatTimeFromHAndM(h, m);
    }

    function formatDateDisplay(dateStr) {
        if (!dateStr) return '';
        const parts = dateStr.split('-');
        if (parts.length === 3) {
            const d = new Date(parts[0], parts[1] - 1, parts[2]);
            return d.toLocaleDateString('en-GB', {
                weekday: 'short',
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        }
        return dateStr;
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

})(jQuery);
