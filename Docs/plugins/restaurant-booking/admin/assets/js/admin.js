/**
 * UK Restaurant Table Booking - Admin Control Center JS
 */

(function($) {
    'use strict';

    const API = {
        getHeaders: function() {
            return {
                'X-WP-Nonce': rbAdminData.nonce,
                'Content-Type': 'application/json'
            };
        },

        fetchStats: function() {
            return $.ajax({
                url: rbAdminData.restUrl + 'admin/stats',
                method: 'GET',
                headers: API.getHeaders()
            });
        },

        fetchReservations: function(filters) {
            return $.ajax({
                url: rbAdminData.restUrl + 'admin/reservations',
                method: 'GET',
                data: filters,
                headers: API.getHeaders()
            });
        },

        fetchReservationDetail: function(id) {
            return $.ajax({
                url: rbAdminData.restUrl + 'admin/reservations/' + id,
                method: 'GET',
                headers: API.getHeaders()
            });
        },

        updateStatus: function(id, status, notes) {
            return $.ajax({
                url: rbAdminData.restUrl + 'admin/reservations/' + id + '/status',
                method: 'PATCH',
                data: JSON.stringify({ status: status, notes: notes || '' }),
                headers: API.getHeaders()
            });
        },

        seatGuest: function(id) {
            return $.ajax({
                url: rbAdminData.restUrl + 'admin/reservations/' + id + '/seat',
                method: 'POST',
                headers: API.getHeaders()
            });
        },

        assignTable: function(id, tableId) {
            return $.ajax({
                url: rbAdminData.restUrl + 'admin/reservations/' + id + '/assign-table',
                method: 'POST',
                data: JSON.stringify({ table_id: tableId }),
                headers: API.getHeaders()
            });
        },

        createWalkIn: function(data) {
            return $.ajax({
                url: rbAdminData.restUrl + 'admin/walk-ins',
                method: 'POST',
                data: JSON.stringify(data),
                headers: API.getHeaders()
            });
        },

        fetchCalendar: function(date) {
            return $.ajax({
                url: rbAdminData.restUrl + 'admin/calendar?date=' + date,
                method: 'GET',
                headers: API.getHeaders()
            });
        },

        fetchTables: function() {
            return $.ajax({
                url: rbAdminData.restUrl + 'admin/tables',
                method: 'GET',
                headers: API.getHeaders()
            });
        }
    };

    $(document).ready(function() {
        initDashboard();
        initReservationsTable();
        initCalendarView();
        initWalkInModal();
        initDetailModal();
        initForms();
    });

    // 1. Dashboard View
    function initDashboard() {
        if (!$('#rb-dashboard-view').length) return;

        loadStats();

        $('#rb-refresh-stats').on('click', function() {
            loadStats();
            loadTodayList();
        });

        loadTodayList();
    }

    function loadStats() {
        API.fetchStats().done(function(res) {
            if (res.success) {
                $('#stat-today-covers').text(res.today.covers);
                $('#stat-today-bookings').text(res.today.bookings);
                $('#stat-today-seated').text(res.today.seated);
                $('#stat-today-pending').text(res.today.pending);
                $('#stat-table-occupancy').text(res.tables.occupied + ' / ' + res.tables.total + ' (' + res.tables.free + ' Free)');
                $('#stat-cancellation-rate').text(res.rates.cancellation_rate + '%');
            }
        });
    }

    function loadTodayList() {
        const today = rbAdminData.today;
        API.fetchReservations({ date: today, limit: 20 }).done(function(res) {
            if (res.success) {
                renderReservationsRows('#rb-today-tbody', res.reservations);
            }
        });
    }

    // 2. Reservations List View
    function initReservationsTable() {
        if (!$('#rb-reservations-view').length) return;

        function refreshList() {
            const date = $('#rb-filter-date').val();
            const status = $('#rb-filter-status').val();
            const search = $('#rb-filter-search').val();

            $('#rb-res-tbody').html('<tr><td colspan="8" style="text-align:center; padding:30px;">Loading reservations...</td></tr>');

            API.fetchReservations({ date: date, status: status, search: search }).done(function(res) {
                if (res.success) {
                    renderReservationsRows('#rb-res-tbody', res.reservations);
                }
            });
        }

        $('#rb-btn-filter').on('click', refreshList);
        $('#rb-filter-date, #rb-filter-status').on('change', refreshList);
        $('#rb-filter-search').on('keyup', function(e) {
            if (e.key === 'Enter') refreshList();
        });

        refreshList();
    }

    function renderReservationsRows(targetSelector, list) {
        const $tbody = $(targetSelector);
        if (!list || list.length === 0) {
            $tbody.html('<tr><td colspan="8" style="text-align:center; padding:30px; color:#64748b;">No reservations found matching your criteria.</td></tr>');
            return;
        }

        let html = '';
        list.forEach(function(r) {
            const statusClass = 'rb-status-' + r.status;
            let actions = '';

            if (r.status === 'confirmed') {
                actions += '<button class="rb-btn rb-btn-success rb-btn-sm rb-action-seat" data-id="' + r.id + '">🪑 Seat Guest</button> ';
            } else if (r.status === 'pending') {
                actions += '<button class="rb-btn rb-btn-accent rb-btn-sm rb-action-confirm" data-id="' + r.id + '">✓ Confirm</button> ';
            }

            if (r.status !== 'cancelled' && r.status !== 'completed' && r.status !== 'no_show') {
                actions += '<button class="rb-btn rb-btn-secondary rb-btn-sm rb-action-cancel" data-id="' + r.id + '">✕ Cancel</button> ';
                actions += '<button class="rb-btn rb-btn-secondary rb-btn-sm rb-action-noshow" data-id="' + r.id + '">No Show</button> ';
            }

            actions += '<button class="rb-btn rb-btn-secondary rb-btn-sm rb-action-view" data-id="' + r.id + '">Details</button>';

            html += '<tr>' +
                '<td><strong style="color:#0284c7;">' + r.booking_reference + '</strong><br><span style="font-size:11px; color:#64748b;">' + r.source.toUpperCase() + '</span></td>' +
                '<td><strong>' + escapeHtml(r.customer_name) + '</strong><br><span style="font-size:12px; color:#64748b;">' + escapeHtml(r.phone) + '</span></td>' +
                '<td><strong>' + r.date_short + '</strong><br><span style="font-weight:600; color:#0f172a;">' + r.time_formatted + '</span></td>' +
                '<td><span style="font-weight:700; font-size:14px;">' + r.party_size + '</span> Guests</td>' +
                '<td><span class="rb-btn rb-btn-secondary rb-btn-sm" style="font-weight:700;">' + (r.table_number_display || 'Unassigned') + '</span></td>' +
                '<td><span class="rb-status ' + statusClass + '">' + r.status_label + '</span></td>' +
                '<td>' + (r.dietary_notes ? '<span style="color:#b91c1c; font-weight:600; font-size:12px;" title="' + escapeHtml(r.dietary_notes) + '">⚠️ ' + escapeHtml(r.dietary_notes) + '</span>' : '<span style="color:#94a3b8;">-</span>') + '</td>' +
                '<td style="white-space:nowrap;">' + actions + '</td>' +
            '</tr>';
        });

        $tbody.html(html);
        attachRowEvents();
    }

    function attachRowEvents() {
        // Seat Guest Action
        $('.rb-action-seat').off('click').on('click', function() {
            const id = $(this).data('id');
            if (confirm('Seat guest now? This will activate the table and link live QR POS ordering.')) {
                API.seatGuest(id).done(function(res) {
                    alert(res.message);
                    location.reload();
                }).fail(function(err) {
                    alert('Error: ' + (err.responseJSON ? err.responseJSON.message : 'Could not seat guest'));
                });
            }
        });

        // Confirm Action
        $('.rb-action-confirm').off('click').on('click', function() {
            const id = $(this).data('id');
            API.updateStatus(id, 'confirmed', 'Confirmed manually by staff.').done(function(res) {
                alert('Reservation confirmed!');
                location.reload();
            });
        });

        // Cancel Action
        $('.rb-action-cancel').off('click').on('click', function() {
            const id = $(this).data('id');
            const reason = prompt('Please enter cancellation reason:', 'Staff cancelled');
            if (reason !== null) {
                API.updateStatus(id, 'cancelled', reason).done(function() {
                    alert('Reservation cancelled.');
                    location.reload();
                });
            }
        });

        // No-Show Action
        $('.rb-action-noshow').off('click').on('click', function() {
            const id = $(this).data('id');
            if (confirm('Mark this reservation as No-Show?')) {
                API.updateStatus(id, 'no_show', 'Marked no-show by staff.').done(function() {
                    alert('Reservation marked as No-Show.');
                    location.reload();
                });
            }
        });

        // View Details Modal
        $('.rb-action-view').off('click').on('click', function() {
            const id = $(this).data('id');
            openDetailModal(id);
        });
    }

    // 3. Detail & Audit Modal
    function initDetailModal() {
        $('#rb-detail-modal-close, #rb-modal-overlay-detail').on('click', function(e) {
            if (e.target === this) $('#rb-modal-overlay-detail').removeClass('active');
        });
    }

    function openDetailModal(id) {
        API.fetchReservationDetail(id).done(function(res) {
            if (res.success) {
                const r = res.reservation;
                $('#detail-ref').text(r.booking_reference);
                $('#detail-name').text(r.customer_name);
                $('#detail-phone').text(r.phone);
                $('#detail-email').text(r.email);
                $('#detail-datetime').text(r.date_formatted + ' @ ' + r.time_formatted + ' (Duration: ' + r.time_formatted + ' - ' + r.end_formatted + ')');
                $('#detail-party').text(r.party_size + ' Guests');
                $('#detail-table').text(r.table_number_display || 'None');
                $('#detail-status').html('<span class="rb-status rb-status-' + r.status + '">' + r.status_label + '</span>');
                $('#detail-special').text(r.special_requests || 'None');
                $('#detail-dietary').text(r.dietary_notes || 'None');
                $('#detail-manage-link').attr('href', r.manage_url);

                let eventHtml = '';
                if (res.events && res.events.length > 0) {
                    res.events.forEach(function(ev) {
                        eventHtml += '<li style="margin-bottom:8px; font-size:12px;"><strong>' + ev.created_at + '</strong> (' + ev.performed_by + '): ' + escapeHtml(ev.description) + '</li>';
                    });
                } else {
                    eventHtml = '<li>No audit events recorded.</li>';
                }
                $('#detail-events-list').html(eventHtml);

                $('#rb-modal-overlay-detail').addClass('active');
            }
        });
    }

    // 4. Walk-In Modal
    function initWalkInModal() {
        $('#rb-btn-open-walkin').on('click', function() {
            loadWalkInTables();
            $('#rb-modal-overlay-walkin').addClass('active');
        });

        $('#rb-walkin-close, #rb-modal-overlay-walkin').on('click', function(e) {
            if (e.target === this) $('#rb-modal-overlay-walkin').removeClass('active');
        });

        $('#rb-walkin-form').on('submit', function(e) {
            e.preventDefault();
            const data = {
                party_size: $('#walkin-party').val(),
                table_id: $('#walkin-table').val(),
                customer_name: $('#walkin-name').val() || 'Walk-In Guest',
                notes: $('#walkin-notes').val()
            };

            $('#rb-walkin-submit').prop('disabled', true).text('Seating...');

            API.createWalkIn(data).done(function(res) {
                alert('Walk-in seated successfully!');
                location.reload();
            }).fail(function(err) {
                alert('Error: ' + (err.responseJSON ? err.responseJSON.message : 'Could not create walk-in'));
                $('#rb-walkin-submit').prop('disabled', false).text('Seat Walk-In Guest');
            });
        });
    }

    function loadWalkInTables() {
        API.fetchTables().done(function(res) {
            if (res.success) {
                let options = '<option value="">-- Select Available Table --</option>';
                res.tables.forEach(function(t) {
                    const statusText = (t.status === 'occupied') ? ' (Occupied)' : ' (Available)';
                    options += '<option value="' + t.id + '">Table ' + t.table_number + ' (Cap: ' + t.capacity + ')' + statusText + '</option>';
                });
                $('#walkin-table').html(options);
            }
        });
    }

    // 5. Timeline Calendar View
    function initCalendarView() {
        if (!$('#rb-calendar-view').length) return;

        function loadCal() {
            const date = $('#rb-cal-date').val() || rbAdminData.today;
            $('#rb-timeline-grid').html('<p style="padding:20px;">Loading schedule...</p>');

            API.fetchCalendar(date).done(function(res) {
                if (res.success) {
                    renderTimeline(res);
                }
            });
        }

        $('#rb-cal-date').on('change', loadCal);
        $('#rb-cal-prev').on('click', function() {
            const cur = new Date($('#rb-cal-date').val());
            cur.setDate(cur.getDate() - 1);
            $('#rb-cal-date').val(cur.toISOString().split('T')[0]).trigger('change');
        });
        $('#rb-cal-next').on('click', function() {
            const cur = new Date($('#rb-cal-date').val());
            cur.setDate(cur.getDate() + 1);
            $('#rb-cal-date').val(cur.toISOString().split('T')[0]).trigger('change');
        });

        loadCal();
    }

    function renderTimeline(data) {
        if (!data.tables || data.tables.length === 0) {
            $('#rb-timeline-grid').html('<p style="padding:20px;">No tables found.</p>');
            return;
        }

        let html = '<div class="rb-timeline-grid">';
        html += '<div class="rb-timeline-table-label" style="background:#0f172a; color:#fff;">Table / Cover</div>';

        // Time slots header (12:00 to 22:00 every 30m)
        const times = ['12:00', '12:30', '13:00', '13:30', '14:00', '14:30', '17:30', '18:00', '18:30', '19:00', '19:30', '20:00', '20:30', '21:00', '21:30'];
        times.forEach(function(t) {
            html += '<div style="font-weight:700; text-align:center; padding:10px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px;">' + t + '</div>';
        });

        // Rows per table
        data.tables.forEach(function(tbl) {
            html += '<div class="rb-timeline-table-label"><strong>Table ' + tbl.table_number + '</strong><span style="font-size:11px; color:#64748b;">Cap: ' + tbl.capacity + '</span></div>';

            times.forEach(function(t) {
                const cellTime = t + ':00';
                // Find matching reservation for this table and time
                const matches = data.reservations.filter(function(r) {
                    return (parseInt(r.table_id) === parseInt(tbl.id)) && (r.start_time <= cellTime && r.end_time > cellTime);
                });

                html += '<div class="rb-timeline-cell">';
                if (matches.length > 0) {
                    const b = matches[0];
                    html += '<div class="rb-booking-block ' + b.status + '" data-id="' + b.id + '" title="' + escapeHtml(b.customer_name) + ' (' + b.party_size + 'p)">' +
                        escapeHtml(b.customer_name.split(' ')[0]) + ' (' + b.party_size + 'p)' +
                    '</div>';
                }
                html += '</div>';
            });
        });

        html += '</div>';
        $('#rb-timeline-grid').html(html);

        $('.rb-booking-block').on('click', function() {
            const id = $(this).data('id');
            openDetailModal(id);
        });
    }

    // 6. Settings & General Forms
    function initForms() {
        $('#rb-settings-form').on('submit', function(e) {
            e.preventDefault();
            const formObj = {};
            $(this).serializeArray().forEach(function(item) {
                formObj[item.name] = item.value;
            });

            $.ajax({
                url: rbAdminData.restUrl + 'admin/settings',
                method: 'POST',
                data: JSON.stringify(formObj),
                headers: API.getHeaders()
            }).done(function(res) {
                alert('Settings saved successfully!');
            });
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
