<?php
/**
 * Waiter Mode Mobile Staff Ordering Application.
 * Rendered at /restaurant-waiter/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$restaurant_name = get_option( 'ro_restaurant_name', 'Grand Bistro & Grill' );
$currency = get_option( 'ro_currency_symbol', '$' );
$tables = RO_Tables::get_all();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Waiter Mode | <?php echo esc_html( $restaurant_name ); ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FF5A1F;
            --bg-body: #0E1116;
            --bg-card: #161A22;
            --border: #252B38;
            --text-main: #FFFFFF;
            --text-muted: #9BA3AF;
            --success: #10B981;
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            padding-bottom: 90px;
        }

        header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(14, 17, 22, 0.9);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            padding: 12px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .table-selector {
            background: #202633;
            border: 1px solid var(--border);
            color: #fff;
            padding: 8px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.9rem;
            outline: none;
        }

        .menu-items-grid {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .waiter-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .qty-box {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #202633;
            border-radius: 20px;
            padding: 4px 12px;
        }

        .qty-btn {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
        }

        .waiter-cart-bar {
            position: fixed;
            bottom: 16px;
            left: 16px;
            right: 16px;
            background: var(--primary);
            color: #fff;
            padding: 14px 20px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(255, 90, 31, 0.4);
        }
    </style>
</head>
<body>

    <header>
        <div style="font-family:'Outfit',sans-serif; font-weight:800; font-size:1.1rem;">Waiter Order Entry</div>
        <select class="table-selector" id="waiterTableSelect" onchange="changeTable()">
            <?php foreach ( $tables as $tb ) : ?>
                <option value="<?php echo intval( $tb['id'] ); ?>"><?php echo esc_html( $tb['table_number'] ); ?></option>
            <?php endforeach; ?>
        </select>
    </header>

    <div class="menu-items-grid" id="waiterMenuGrid">
        <!-- Rendered via JS -->
    </div>

    <div class="waiter-cart-bar" id="waiterCartBar" onclick="sendWaiterOrder()">
        <span id="waiterCartCount">0 items</span>
        <span id="waiterCartTotal"><?php echo esc_html( $currency ); ?>0.00</span>
        <span>Send to Kitchen ➔</span>
    </div>

    <script>
        const API_BASE = '<?php echo esc_url_raw( rest_url( 'ro/v1' ) ); ?>';
        const CURRENCY = '<?php echo esc_js( $currency ); ?>';

        let menuItems = [];
        let waiterCart = {};

        document.addEventListener('DOMContentLoaded', async () => {
            const res = await fetch(`${API_BASE}/menu`);
            const json = await res.json();
            if (json.success) {
                json.categories.forEach(cat => {
                    if (cat.items) menuItems.push(...cat.items);
                });
                renderWaiterMenu();
            }
        });

        function renderWaiterMenu() {
            const grid = document.getElementById('waiterMenuGrid');
            grid.innerHTML = menuItems.map(item => {
                const qty = waiterCart[item.id] || 0;
                return `
                    <div class="waiter-card">
                        <div>
                            <div style="font-weight:700; font-size:0.95rem;">${item.name}</div>
                            <div style="font-size:0.85rem; color:var(--text-muted);">${CURRENCY}${item.base_price.toFixed(2)}</div>
                        </div>
                        <div class="qty-box">
                            <button class="qty-btn" onclick="updateQty(${item.id}, -1)">−</button>
                            <span style="font-weight:bold; min-width:18px; text-align:center;">${qty}</span>
                            <button class="qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
                        </div>
                    </div>
                `;
            }).join('');

            updateWaiterCartBar();
        }

        function updateQty(itemId, change) {
            const current = waiterCart[itemId] || 0;
            const next = Math.max(0, current + change);
            if (next === 0) {
                delete waiterCart[itemId];
            } else {
                waiterCart[itemId] = next;
            }
            renderWaiterMenu();
        }

        function updateWaiterCartBar() {
            let count = 0;
            let total = 0;
            for (const [id, qty] of Object.entries(waiterCart)) {
                const item = menuItems.find(i => i.id == id);
                if (item) {
                    count += qty;
                    total += (item.base_price * qty);
                }
            }
            document.getElementById('waiterCartCount').innerText = `${count} items`;
            document.getElementById('waiterCartTotal').innerText = `${CURRENCY}${total.toFixed(2)}`;
        }

        async function sendWaiterOrder() {
            const tableId = document.getElementById('waiterTableSelect').value;
            const items = [];
            for (const [id, qty] of Object.entries(waiterCart)) {
                items.push({ menu_item_id: parseInt(id), quantity: qty });
            }

            if (items.length === 0) {
                alert('Please add at least one dish.');
                return;
            }

            const res = await fetch(`${API_BASE}/orders`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    table_id: tableId,
                    items: items,
                    source: 'Waiter'
                })
            });

            const json = await res.json();
            if (json.success) {
                alert(`Order ${json.order.order_number} sent directly to Kitchen KDS!`);
                waiterCart = {};
                renderWaiterMenu();
            } else {
                alert(json.message || 'Order failed');
            }
        }

        function changeTable() {
            waiterCart = {};
            renderWaiterMenu();
        }
    </script>
</body>
</html>
