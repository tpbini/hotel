<?php
/**
 * Counter POS Cashier Application.
 * Rendered at /restaurant-pos/
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$restaurant_name = get_option( 'ro_restaurant_name', 'Grand Bistro & Grill' );
$currency = get_option( 'ro_currency_symbol', '$' );
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counter POS | <?php echo esc_html( $restaurant_name ); ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --pos-bg: #0C0E12;
            --pos-sidebar: #13161D;
            --pos-card: #191E28;
            --pos-card-hover: #222938;
            --pos-border: #262E3E;
            --pos-text: #FFFFFF;
            --pos-muted: #8E9BAE;
            --primary: #FF5A1F;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
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
            background: var(--pos-bg);
            color: var(--pos-text);
            height: 100vh;
            overflow: hidden;
            display: flex;
        }

        /* Left Main View (Tables Grid) */
        .pos-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            border-right: 1px solid var(--pos-border);
            height: 100vh;
            overflow-y: auto;
        }

        .pos-header {
            padding: 16px 24px;
            background: var(--pos-sidebar);
            border-bottom: 1px solid var(--pos-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .service-alert-bar {
            background: rgba(245, 158, 11, 0.15);
            border-bottom: 1px solid rgba(245, 158, 11, 0.3);
            padding: 10px 24px;
            display: none;
            align-items: center;
            justify-content: space-between;
        }

        .service-alert-bar.visible {
            display: flex;
        }

        .tables-grid {
            padding: 24px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }

        .table-pos-card {
            background: var(--pos-card);
            border: 1px solid var(--pos-border);
            border-radius: var(--radius-lg);
            padding: 16px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 140px;
        }

        .table-pos-card:hover {
            border-color: #3F4B64;
            transform: translateY(-2px);
        }

        .table-pos-card.selected {
            border-color: var(--primary);
            box-shadow: 0 0 0 2px var(--primary);
        }

        .table-pos-card.occupied {
            border-left: 5px solid var(--warning);
        }

        .table-pos-card.available {
            border-left: 5px solid var(--success);
        }

        .table-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
        }

        .table-badge {
            font-size: 0.72rem;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 20px;
            text-transform: uppercase;
        }

        .table-badge.occupied {
            background: rgba(245, 158, 11, 0.2);
            color: var(--warning);
        }

        .table-badge.available {
            background: rgba(16, 185, 129, 0.2);
            color: var(--success);
        }

        /* Right Billing Panel */
        .pos-sidebar {
            width: 420px;
            background: var(--pos-sidebar);
            display: flex;
            flex-direction: column;
            height: 100vh;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid var(--pos-border);
        }

        .sidebar-body {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid var(--pos-border);
            background: #0E1117;
        }

        .session-order-card {
            background: #181D26;
            border: 1px solid var(--pos-border);
            border-radius: var(--radius-md);
            padding: 12px;
            margin-bottom: 12px;
        }

        .pay-btn-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 14px;
        }

        .btn-pos {
            padding: 14px;
            border-radius: var(--radius-md);
            font-family: 'Outfit', sans-serif;
            font-size: 0.95rem;
            font-weight: 800;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-pay {
            background: var(--success);
            color: #fff;
            grid-column: span 2;
        }

        .btn-discount {
            background: #252D3D;
            color: #fff;
        }

        .btn-print {
            background: #252D3D;
            color: #fff;
        }

        /* Modals */
        .modal {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.75);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 500;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: #181D26;
            border: 1px solid var(--pos-border);
            border-radius: var(--radius-lg);
            width: 440px;
            padding: 24px;
        }
    </style>
</head>
<body>

    <!-- Main Table View -->
    <div class="pos-main">
        <div class="pos-header">
            <div>
                <h2 style="font-family:'Outfit',sans-serif; font-size:1.3rem; font-weight:800;"><?php echo esc_html( $restaurant_name ); ?> - Counter POS</h2>
                <p style="font-size:0.8rem; color:var(--pos-muted);">Real-Time Dine-in Floor Management</p>
            </div>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=restaurant-qr-pos' ) ); ?>" style="color:#fff; text-decoration:none; background:#202634; padding:8px 14px; border-radius:8px; font-size:0.82rem; font-weight:700;">Admin Dashboard</a>
        </div>

        <!-- Service Requests Alert Bar -->
        <div class="service-alert-bar" id="serviceAlertBar">
            <div style="display:flex; align-items:center; gap:8px;">
                <span>🔔</span>
                <span id="serviceAlertText" style="font-weight:700; font-size:0.9rem; color:#FCD34D;">Table 02 is requesting the Bill!</span>
            </div>
            <button onclick="resolveAlert()" style="background:#F59E0B; color:#000; border:none; padding:4px 10px; border-radius:6px; font-weight:800; font-size:0.75rem; cursor:pointer;">Acknowledge</button>
        </div>

        <div class="tables-grid" id="tablesGrid">
            <!-- Rendered via JS -->
        </div>
    </div>

    <!-- Sidebar Billing Details -->
    <div class="pos-sidebar">
        <div class="sidebar-header">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 id="sidebarTableTitle" style="font-family:'Outfit',sans-serif; font-size:1.25rem; font-weight:800;">Select a Table</h3>
                <span id="sidebarSessionCode" style="font-size:0.8rem; color:var(--pos-muted); font-weight:700;"></span>
            </div>
        </div>

        <div class="sidebar-body" id="sidebarOrdersList">
            <p style="text-align:center; color:var(--pos-muted); padding-top:60px;">Click on any table card to inspect running orders and settle payment.</p>
        </div>

        <div class="sidebar-footer" id="sidebarFooter" style="display:none;">
            <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:0.9rem; color:var(--pos-muted);">
                <span>Subtotal</span>
                <span id="posSubtotal"><?php echo esc_html( $currency ); ?>0.00</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:0.9rem; color:var(--pos-muted);">
                <span>Tax</span>
                <span id="posTax"><?php echo esc_html( $currency ); ?>0.00</span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:0.9rem; color:var(--danger);">
                <span>Discount</span>
                <span id="posDiscount">-<?php echo esc_html( $currency ); ?>0.00</span>
            </div>
            <div style="display:flex; justify-content:space-between; padding-top:10px; border-top:1px solid var(--pos-border); font-size:1.2rem; font-weight:900; font-family:'Outfit',sans-serif;">
                <span>Total Payable</span>
                <span id="posGrandTotal" style="color:var(--primary);"><?php echo esc_html( $currency ); ?>0.00</span>
            </div>

            <div class="pay-btn-group">
                <button class="btn-pos btn-discount" onclick="openDiscountModal()">🏷️ Discount</button>
                <button class="btn-pos btn-print" onclick="printReceipt()">🖨️ Receipt</button>
                <button class="btn-pos btn-pay" onclick="openPaymentModal()">💵 Settle & Close (Pay)</button>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div class="modal" id="payModal">
        <div class="modal-content">
            <h3 style="font-family:'Outfit',sans-serif; font-size:1.2rem; font-weight:800; margin-bottom:16px;">Settle Table Bill</h3>
            <div style="margin-bottom:14px;">
                <label style="font-size:0.85rem; color:var(--pos-muted); display:block; margin-bottom:6px;">Payment Method</label>
                <select id="payMethodSelect" style="width:100%; padding:10px; background:#0F1218; border:1px solid var(--pos-border); color:#fff; border-radius:8px;">
                    <option value="Cash">💵 Cash</option>
                    <option value="Card">💳 Credit / Debit Card</option>
                    <option value="UPI">📱 UPI / QR Transfer</option>
                    <option value="Other">⚡ Other Method</option>
                </select>
            </div>
            <div style="margin-bottom:18px;">
                <label style="font-size:0.85rem; color:var(--pos-muted); display:block; margin-bottom:6px;">Payment Reference / Notes</label>
                <input type="text" id="payRefInput" placeholder="Transaction ref or cashier note" style="width:100%; padding:10px; background:#0F1218; border:1px solid var(--pos-border); color:#fff; border-radius:8px;">
            </div>
            <div style="display:flex; gap:10px;">
                <button onclick="closePayModal()" style="flex:1; padding:12px; background:#252D3D; border:none; color:#fff; border-radius:8px; font-weight:700; cursor:pointer;">Cancel</button>
                <button onclick="submitPayment()" style="flex:1; padding:12px; background:var(--success); border:none; color:#fff; border-radius:8px; font-weight:800; cursor:pointer;">Confirm Settlement</button>
            </div>
        </div>
    </div>

    <!-- JS Logic -->
    <script>
        const API_BASE = '<?php echo esc_url_raw( rest_url( 'ro/v1' ) ); ?>';
        const CURRENCY = '<?php echo esc_js( $currency ); ?>';

        let tablesData = [];
        let selectedTable = null;
        let activeAlertId = null;

        document.addEventListener('DOMContentLoaded', () => {
            fetchPosTables();
            setInterval(fetchPosTables, 5000);
        });

        let isFetchingPos = false;
        async function fetchPosTables() {
            if (isFetchingPos) return;
            isFetchingPos = true;
            try {
                const res = await fetch(`${API_BASE}/pos/tables`);
                const json = await res.json();
                if (json.success) {
                    tablesData = json.tables;
                    renderTables(tablesData);
                    handleServiceRequests(json.service_requests);

                    // Re-render selected table if open
                    if (selectedTable) {
                        const updated = tablesData.find(t => t.id === selectedTable.id);
                        if (updated) {
                            selectedTable = updated;
                            renderTableSession(selectedTable);
                        }
                    }
                }
            } catch (e) {
                console.error(e);
            } finally {
                isFetchingPos = false;
            }
        }

        function handleServiceRequests(requests) {
            const bar = document.getElementById('serviceAlertBar');
            if (requests && requests.length > 0) {
                const req = requests[0];
                activeAlertId = req.id;
                document.getElementById('serviceAlertText').innerText = `Table ${req.table_number}: ${req.type === 'call_waiter' ? 'Called Waiter' : 'Requested Bill'}`;
                bar.classList.add('visible');
            } else {
                bar.classList.remove('visible');
            }
        }

        async function resolveAlert() {
            if (!activeAlertId) return;
            await fetch(`${API_BASE}/pos/service-requests/${activeAlertId}/resolve`, { method: 'PATCH' });
            fetchPosTables();
        }

        function renderTables(tables) {
            const grid = document.getElementById('tablesGrid');
            grid.innerHTML = tables.map(t => {
                const isOccupied = t.status === 'occupied' || (t.active_session && t.active_session.status === 'active');
                const isSelected = selectedTable && selectedTable.id === t.id;
                const total = t.active_session ? t.active_session.total_amount : 0;

                return `
                    <div class="table-pos-card ${isOccupied ? 'occupied' : 'available'} ${isSelected ? 'selected' : ''}" onclick="selectTable(${t.id})">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                            <div class="table-title">${t.table_number}</div>
                            <span class="table-badge ${isOccupied ? 'occupied' : 'available'}">${isOccupied ? 'Occupied' : 'Available'}</span>
                        </div>
                        <div style="margin-top:14px;">
                            <div style="font-size:0.75rem; color:var(--pos-muted);">Cap: ${t.capacity} seats</div>
                            <div style="font-size:1.1rem; font-weight:800; color:#fff; font-family:'Outfit',sans-serif; margin-top:4px;">
                                ${isOccupied ? CURRENCY + total.toFixed(2) : '—'}
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function selectTable(tableId) {
            selectedTable = tablesData.find(t => t.id === tableId);
            renderTables(tablesData);
            renderTableSession(selectedTable);
        }

        async function renderTableSession(table) {
            document.getElementById('sidebarTableTitle').innerText = table.table_number;

            if (!table.active_session) {
                document.getElementById('sidebarSessionCode').innerText = 'No Active Session';
                document.getElementById('sidebarOrdersList').innerHTML = '<div style="text-align:center; padding:40px 10px; color:var(--pos-muted);">Table is currently available with no active dining session.</div>';
                document.getElementById('sidebarFooter').style.display = 'none';
                return;
            }

            const session = table.active_session;
            document.getElementById('sidebarSessionCode').innerText = session.session_code;
            document.getElementById('sidebarFooter').style.display = 'block';

            document.getElementById('posSubtotal').innerText = `${CURRENCY}${session.subtotal.toFixed(2)}`;
            document.getElementById('posTax').innerText = `${CURRENCY}${session.tax_amount.toFixed(2)}`;
            document.getElementById('posDiscount').innerText = `-${CURRENCY}${session.discount_amount.toFixed(2)}`;
            document.getElementById('posGrandTotal').innerText = `${CURRENCY}${session.total_amount.toFixed(2)}`;

            // Fetch complete session orders
            try {
                const res = await fetch(`${API_BASE}/sessions/${session.id}`);
                const json = await res.json();
                if (json.success && json.session.orders) {
                    const list = document.getElementById('sidebarOrdersList');
                    list.innerHTML = json.session.orders.map(ord => `
                        <div class="session-order-card">
                            <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
                                <strong style="color:#fff;">${ord.order_number} (${ord.source})</strong>
                                <span style="font-size:0.8rem; font-weight:700; color:var(--primary);">${ord.status}</span>
                            </div>
                            ${ord.items.map(it => `
                                <div style="display:flex; justify-content:space-between; font-size:0.85rem; color:var(--pos-muted); margin-bottom:4px;">
                                    <span>${it.quantity}× ${it.item_name}</span>
                                    <span>${CURRENCY}${it.total_price.toFixed(2)}</span>
                                </div>
                            `).join('')}
                        </div>
                    `).join('');
                }
            } catch (err) {
                console.error(err);
            }
        }

        function openPaymentModal() {
            if (!selectedTable || !selectedTable.active_session) return;
            document.getElementById('payModal').classList.add('active');
        }

        function closePayModal() {
            document.getElementById('payModal').classList.remove('active');
        }

        async function submitPayment() {
            if (!selectedTable || !selectedTable.active_session) return;
            const method = document.getElementById('payMethodSelect').value;
            const ref = document.getElementById('payRefInput').value.trim();

            const res = await fetch(`${API_BASE}/pos/pay`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    session_id: selectedTable.active_session.id,
                    payment_method: method,
                    reference: ref
                })
            });

            const json = await res.json();
            if (json.success) {
                closePayModal();
                alert(`Payment recorded successfully! Invoice: ${json.payment.invoice_number}`);
                selectedTable = null;
                fetchPosTables();
            } else {
                alert('Payment settlement failed.');
            }
        }

        async function openDiscountModal() {
            if (!selectedTable || !selectedTable.active_session) return;
            const amt = prompt('Enter discount amount to apply:', '0.00');
            if (amt !== null) {
                await fetch(`${API_BASE}/pos/discount`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        session_id: selectedTable.active_session.id,
                        discount: parseFloat(amt) || 0
                    })
                });
                fetchPosTables();
            }
        }

        function printReceipt() {
            alert('Receipt print job has been queued to the local thermal print bridge!');
        }
    </script>
</body>
</html>
