<?php
/**
 * Kitchen Display System (KDS) Fullscreen Touch Application.
 * Rendered at /restaurant-kds/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$restaurant_name = get_option( 'ro_restaurant_name', 'Grand Bistro & Grill' );
$stations = RO_Stations::get_active();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kitchen Display System (KDS) | <?php echo esc_html( $restaurant_name ); ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800;900&family=JetBrains+Mono:wght@500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --kds-bg: #0A0D12;
            --kds-card: #141820;
            --kds-card-border: #232A38;
            --kds-header: #11141B;
            --kds-text: #FFFFFF;
            --kds-muted: #8E9BAE;
            --primary: #FF5A1F;
            --status-new: #3B82F6;
            --status-prep: #F59E0B;
            --status-ready: #10B981;
            --status-served: #6B7280;
            --danger: #EF4444;
            --radius-md: 12px;
            --radius-lg: 18px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-user-select: none;
            user-select: none;
        }

        body {
            background-color: var(--kds-bg);
            color: var(--kds-text);
            height: 100vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Top Bar */
        .kds-header {
            background: var(--kds-header);
            border-bottom: 1px solid var(--kds-card-border);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .kds-title-group {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .kds-badge {
            background: var(--primary);
            color: #fff;
            font-family: 'Outfit', sans-serif;
            font-size: 0.82rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 6px;
            letter-spacing: 0.05em;
        }

        .kds-station-tabs {
            display: flex;
            gap: 8px;
            overflow-x: auto;
        }

        .station-tab {
            padding: 8px 16px;
            background: #1B212D;
            border: 1px solid var(--kds-card-border);
            border-radius: 30px;
            color: var(--kds-muted);
            font-size: 0.85rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }

        .station-tab.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 4px 12px rgba(255, 90, 31, 0.4);
        }

        .kds-metrics {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .live-clock {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
        }

        /* Order Cards Grid */
        .kds-grid {
            flex: 1;
            padding: 18px 24px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 18px;
            overflow-y: auto;
            align-content: start;
        }

        .order-card {
            background: var(--kds-card);
            border: 1px solid var(--kds-card-border);
            border-radius: var(--radius-lg);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            transition: transform 0.2s, border-color 0.2s;
        }

        .order-card.status-NEW {
            border-top: 5px solid var(--status-new);
        }

        .order-card.status-ACCEPTED, .order-card.status-PREPARING {
            border-top: 5px solid var(--status-prep);
        }

        .order-card.status-READY {
            border-top: 5px solid var(--status-ready);
        }

        .card-header {
            padding: 14px 16px;
            background: #181E29;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--kds-card-border);
        }

        .card-table-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
        }

        .card-meta {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .timer-badge {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 6px;
            background: #252D3D;
            color: #E2E8F0;
        }

        .timer-badge.urgent {
            background: rgba(239, 68, 68, 0.2);
            color: var(--danger);
            border: 1px solid var(--danger);
            animation: pulse 1.5s infinite;
        }

        .card-body {
            padding: 14px 16px;
            flex: 1;
            overflow-y: auto;
            max-height: 280px;
        }

        .kds-item-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #1E2533;
        }

        .item-qty-badge {
            background: #2A3345;
            color: #fff;
            font-weight: 800;
            font-size: 0.9rem;
            width: 28px;
            height: 28px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            flex-shrink: 0;
        }

        .item-name-group {
            flex: 1;
        }

        .item-main-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: #fff;
        }

        .item-mod-line {
            font-size: 0.78rem;
            color: var(--primary);
            font-weight: 600;
            margin-top: 2px;
        }

        .item-note-line {
            font-size: 0.78rem;
            color: #FCD34D;
            background: rgba(245, 158, 11, 0.1);
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 4px;
        }

        .card-footer {
            padding: 12px 16px;
            background: #181E29;
            border-top: 1px solid var(--kds-card-border);
            display: flex;
            gap: 10px;
        }

        .action-btn {
            flex: 1;
            padding: 12px;
            border-radius: var(--radius-md);
            font-family: 'Outfit', sans-serif;
            font-size: 0.92rem;
            font-weight: 800;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.15s;
        }

        .action-btn:active {
            transform: scale(0.96);
        }

        .btn-prep {
            background: var(--status-prep);
            color: #000;
        }

        .btn-ready {
            background: var(--status-ready);
            color: #fff;
        }

        .btn-serve {
            background: #3B4252;
            color: #ECEFF4;
        }

        .empty-kds {
            grid-column: 1 / -1;
            text-align: center;
            padding: 80px 20px;
            color: var(--kds-muted);
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="kds-header">
        <div class="kds-title-group">
            <span class="kds-badge">KDS LIVE</span>
            <h2 style="font-family:'Outfit',sans-serif; font-size:1.2rem; font-weight:800;"><?php echo esc_html( $restaurant_name ); ?></h2>
        </div>

        <div class="kds-station-tabs">
            <div class="station-tab active" onclick="setStation(null, this)">All Kitchen Stations</div>
            <?php foreach ( $stations as $st ) : ?>
                <div class="station-tab" onclick="setStation(<?php echo intval( $st['id'] ); ?>, this)"><?php echo esc_html( $st['name'] ); ?></div>
            <?php endforeach; ?>
        </div>

        <div class="kds-metrics">
            <button onclick="toggleAudio()" id="audioToggleBtn" style="background:#202634; border:1px solid #303A4E; color:#fff; padding:6px 12px; border-radius:20px; font-size:0.8rem; font-weight:700; cursor:pointer;">
                🔔 Sound: ON
            </button>
            <div class="live-clock" id="liveClock">00:00:00</div>
        </div>
    </div>

    <!-- Live KDS Grid -->
    <div class="kds-grid" id="kdsGrid">
        <!-- Rendered via JS -->
    </div>

    <!-- JS Logic -->
    <script>
        const API_BASE = '<?php echo esc_url_raw( rest_url( 'ro/v1' ) ); ?>';
        let currentStation = null;
        let knownOrders = new Set();
        let soundEnabled = true;

        // Clock Updater
        setInterval(() => {
            const now = new Date();
            document.getElementById('liveClock').innerText = now.toLocaleTimeString();
        }, 1000);

        document.addEventListener('DOMContentLoaded', () => {
            fetchKdsOrders();
            setInterval(fetchKdsOrders, 4000);
        });

        function setStation(stationId, el) {
            currentStation = stationId;
            document.querySelectorAll('.station-tab').forEach(t => t.classList.remove('active'));
            if (el) el.classList.add('active');
            fetchKdsOrders();
        }

        function toggleAudio() {
            soundEnabled = !soundEnabled;
            document.getElementById('audioToggleBtn').innerText = soundEnabled ? '🔔 Sound: ON' : '🔕 Sound: OFF';
        }

        // Web Audio Chime on New Order
        function playChime() {
            if (!soundEnabled) return;
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                osc.frequency.setValueAtTime(880.00, ctx.currentTime + 0.15); // A5
                gain.gain.setValueAtTime(0.3, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.6);
            } catch (e) {}
        }

        let isFetchingKds = false;
        async function fetchKdsOrders() {
            if (isFetchingKds) return;
            isFetchingKds = true;
            try {
                let url = `${API_BASE}/kds/orders`;
                if (currentStation) {
                    url += `?station_id=${currentStation}`;
                }
                const res = await fetch(url);
                const json = await res.json();

                if (json.success) {
                    renderOrders(json.orders);
                }
            } catch (err) {
                console.error('KDS poll error', err);
            } finally {
                isFetchingKds = false;
            }
        }

        function renderOrders(orders) {
            const grid = document.getElementById('kdsGrid');
            if (!orders || orders.length === 0) {
                grid.innerHTML = '<div class="empty-kds"><h2>✨ Kitchen is all clear!</h2><p style="margin-top:8px;">New orders placed via Table QR or Waiter will appear here automatically.</p></div>';
                return;
            }

            // Check for new orders to trigger sound
            let hasNew = false;
            orders.forEach(o => {
                if (!knownOrders.has(o.id)) {
                    hasNew = true;
                    knownOrders.add(o.id);
                }
            });
            if (hasNew && knownOrders.size > orders.length) {
                playChime();
            }

            let html = '';
            orders.forEach(o => {
                const elapsed = o.elapsed_minutes || 0;
                const isUrgent = elapsed >= 15;

                let actionButton = '';
                if (o.status === 'NEW' || o.status === 'ACCEPTED') {
                    actionButton = `<button class="action-btn btn-prep" onclick="updateOrderStatus(${o.id}, 'PREPARING')">🔥 Start Preparing</button>`;
                } else if (o.status === 'PREPARING') {
                    actionButton = `<button class="action-btn btn-ready" onclick="updateOrderStatus(${o.id}, 'READY')">✓ Mark Ready</button>`;
                } else if (o.status === 'READY') {
                    actionButton = `<button class="action-btn btn-serve" onclick="updateOrderStatus(${o.id}, 'SERVED')">🍽️ Mark Served</button>`;
                }

                html += `
                    <div class="order-card status-${o.status}">
                        <div class="card-header">
                            <div>
                                <h3 class="card-table-title">${o.table_number || 'Table'}</h3>
                                <span style="font-size:0.75rem; color:var(--kds-muted); font-weight:700;">${o.order_number} • ${o.source}</span>
                            </div>
                            <div class="card-meta">
                                <span class="timer-badge ${isUrgent ? 'urgent' : ''}">⏱ ${elapsed}m</span>
                            </div>
                        </div>

                        <div class="card-body">
                `;

                o.items.forEach(item => {
                    let modsHtml = '';
                    if (item.modifiers && item.modifiers.length > 0) {
                        modsHtml = `<div class="item-mod-line">+ ${item.modifiers.map(m => m.modifier_name).join(', ')}</div>`;
                    }
                    let noteHtml = item.notes ? `<div class="item-note-line">⚠️ "${item.notes}"</div>` : '';

                    html += `
                        <div class="kds-item-row">
                            <div style="display:flex; align-items:flex-start;">
                                <div class="item-qty-badge">${item.quantity}×</div>
                                <div class="item-name-group">
                                    <div class="item-main-name">${item.item_name}</div>
                                    ${modsHtml}
                                    ${noteHtml}
                                </div>
                            </div>
                        </div>
                    `;
                });

                if (o.notes) {
                    html += `<div style="margin-top:10px; font-size:0.8rem; background:#241E15; border:1px solid #784E10; color:#FBBF24; padding:8px 10px; border-radius:8px;"><strong>Note:</strong> ${o.notes}</div>`;
                }

                html += `
                        </div>
                        <div class="card-footer">
                            ${actionButton}
                        </div>
                    </div>
                `;
            });

            grid.innerHTML = html;
        }

        async function updateOrderStatus(orderId, status) {
            try {
                const res = await fetch(`${API_BASE}/orders/${orderId}/status`, {
                    method: 'PATCH',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: status })
                });
                const json = await res.json();
                if (json.success) {
                    fetchKdsOrders();
                }
            } catch (err) {
                alert('Status update failed');
            }
        }
    </script>
</body>
</html>
