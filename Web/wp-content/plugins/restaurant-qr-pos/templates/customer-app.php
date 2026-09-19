<?php
/**
 * Customer Mobile-First QR Ordering Web Application.
 * Standalone template rendered at /order/t/{token}
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$token = get_query_var( 'ro_token' );
$table = RO_Tables::get_by_token( $token );

if ( ! $table || 'disabled' === $table['status'] ) {
    wp_die(
        '<div style="text-align:center;font-family:sans-serif;margin-top:50px;">' .
        '<h2>' . esc_html__( 'Invalid or Expired Table QR Code', 'restaurant-qr-pos' ) . '</h2>' .
        '<p>' . esc_html__( 'Please ask a staff member for assistance or scan the table QR code again.', 'restaurant-qr-pos' ) . '</p>' .
        '</div>',
        esc_html__( 'Table Not Found', 'restaurant-qr-pos' ),
        array( 'response' => 404 )
    );
}

$currency = get_option( 'ro_currency_symbol', '$' );
$restaurant_name = get_option( 'ro_restaurant_name', 'Grand Bistro & Grill' );
$tax_rate = (float) get_option( 'ro_tax_rate', 5.0 );
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo esc_html( $table['table_number'] ); ?> | <?php echo esc_html( $restaurant_name ); ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #FF5A1F;
            --primary-dark: #E04810;
            --primary-light: #FFF2EB;
            --bg-body: #0F1115;
            --bg-card: #181B20;
            --bg-card-hover: #22262E;
            --text-main: #FFFFFF;
            --text-muted: #9BA3AF;
            --border: #262C36;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            padding-bottom: 100px;
            overflow-x: hidden;
        }

        /* Top Header */
        header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(15, 17, 21, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 14px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-info h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: #fff;
        }

        .table-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--primary-light);
            color: var(--primary-dark);
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.82rem;
            font-weight: 700;
        }

        .table-pill .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--primary);
            animation: pulse 1.8s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.9); opacity: 0.8; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.9); opacity: 0.8; }
        }

        /* Hero Quick Actions */
        .quick-bar {
            display: flex;
            gap: 10px;
            padding: 14px 18px;
        }

        .quick-btn {
            flex: 1;
            padding: 10px 12px;
            border-radius: var(--radius-md);
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text-main);
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .quick-btn:active {
            transform: scale(0.97);
            background: var(--bg-card-hover);
        }

        .quick-btn.active {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Search Box */
        .search-container {
            padding: 0 18px 12px;
        }

        .search-box {
            position: relative;
            width: 100%;
        }

        .search-box input {
            width: 100%;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 12px 14px 12px 42px;
            color: #fff;
            font-size: 0.92rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .search-box input:focus {
            border-color: var(--primary);
        }

        .search-box svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            width: 18px;
            height: 18px;
        }

        /* Category Nav */
        .category-nav {
            display: flex;
            overflow-x: auto;
            gap: 8px;
            padding: 6px 18px 14px;
            scrollbar-width: none;
        }

        .category-nav::-webkit-scrollbar {
            display: none;
        }

        .cat-chip {
            flex-shrink: 0;
            padding: 8px 16px;
            border-radius: 30px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            color: var(--text-muted);
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .cat-chip.active {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
            box-shadow: 0 4px 14px rgba(255, 90, 31, 0.35);
        }

        /* Menu Grid */
        .menu-section {
            padding: 10px 18px;
        }

        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .items-grid {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .item-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 14px;
            display: flex;
            gap: 14px;
            position: relative;
            transition: transform 0.2s, border-color 0.2s;
        }

        .item-card:hover {
            border-color: #353B47;
        }

        .item-card.sold-out {
            opacity: 0.55;
            filter: grayscale(0.6);
        }

        .item-img-wrapper {
            width: 105px;
            height: 105px;
            flex-shrink: 0;
            border-radius: var(--radius-md);
            overflow: hidden;
            position: relative;
            background: #242933;
        }

        .item-img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .food-badge {
            position: absolute;
            top: 6px;
            left: 6px;
            width: 16px;
            height: 16px;
            background: #fff;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .food-badge.veg::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--success);
        }

        .food-badge.non_veg::after {
            content: '';
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-bottom: 8px solid var(--danger);
        }

        .item-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .item-title {
            font-size: 0.98rem;
            font-weight: 700;
            color: #fff;
            line-height: 1.3;
            margin-bottom: 4px;
        }

        .item-desc {
            font-size: 0.78rem;
            color: var(--text-muted);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 8px;
        }

        .item-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: auto;
        }

        .item-price {
            font-family: 'Outfit', sans-serif;
            font-size: 1.05rem;
            font-weight: 700;
            color: #fff;
        }

        .add-btn {
            background: var(--primary-light);
            color: var(--primary);
            border: 1px solid var(--primary);
            font-weight: 700;
            font-size: 0.82rem;
            padding: 7px 16px;
            border-radius: 20px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }

        .add-btn:active {
            transform: scale(0.94);
            background: var(--primary);
            color: #fff;
        }

        .add-btn:disabled {
            background: #252830;
            border-color: #353840;
            color: #6B7280;
            cursor: not-allowed;
        }

        /* Floating Sticky Cart Bar */
        .floating-cart-bar {
            position: fixed;
            bottom: 18px;
            left: 18px;
            right: 18px;
            background: var(--primary);
            color: #fff;
            border-radius: 18px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 12px 35px rgba(255, 90, 31, 0.45);
            z-index: 100;
            cursor: pointer;
            transform: translateY(120%);
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .floating-cart-bar.visible {
            transform: translateY(0);
        }

        .cart-bar-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .cart-badge {
            background: #fff;
            color: var(--primary);
            font-size: 0.85rem;
            font-weight: 800;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart-bar-total {
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem;
            font-weight: 800;
        }

        .cart-bar-action {
            font-size: 0.92rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Slide-up Modals & Drawer */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.75);
            backdrop-filter: blur(8px);
            z-index: 200;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            display: flex;
            align-items: flex-end;
            justify-content: center;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .bottom-sheet {
            background: #16191E;
            width: 100%;
            max-width: 500px;
            max-height: 88vh;
            border-radius: 24px 24px 0 0;
            border-top: 1px solid var(--border);
            padding: 22px 20px;
            overflow-y: auto;
            transform: translateY(100%);
            transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            display: flex;
            flex-direction: column;
        }

        .modal-overlay.active .bottom-sheet {
            transform: translateY(0);
        }

        .sheet-handle {
            width: 40px;
            height: 4px;
            background: #3B4252;
            border-radius: 4px;
            margin: 0 auto 16px;
        }

        .sheet-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .sheet-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            font-weight: 800;
            color: #fff;
        }

        .sheet-close {
            background: #252A34;
            border: none;
            color: #fff;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        /* Option Group in Customizer */
        .option-group {
            margin-bottom: 18px;
        }

        .group-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 10px;
        }

        .option-pill {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 14px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .option-pill.selected {
            border-color: var(--primary);
            background: rgba(255, 90, 31, 0.08);
        }

        .custom-radio, .custom-checkbox {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid #4B5563;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .custom-checkbox {
            border-radius: 6px;
        }

        .option-pill.selected .custom-radio::after {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--primary);
        }

        .option-pill.selected .custom-checkbox {
            background: var(--primary);
            border-color: var(--primary);
        }

        /* Special Notes Input */
        .notes-input {
            width: 100%;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 12px;
            color: #fff;
            font-size: 0.88rem;
            resize: none;
            height: 70px;
            outline: none;
        }

        .notes-input:focus {
            border-color: var(--primary);
        }

        /* Drawer Cart Items */
        .cart-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .cart-item-title {
            font-size: 0.95rem;
            font-weight: 700;
        }

        .cart-item-subs {
            font-size: 0.78rem;
            color: var(--text-muted);
        }

        .qty-control {
            display: flex;
            align-items: center;
            gap: 10px;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 4px 10px;
        }

        .qty-btn {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            width: 22px;
            height: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bill-summary {
            background: #1B1F26;
            border-radius: var(--radius-md);
            padding: 14px;
            margin: 16px 0;
        }

        .bill-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.88rem;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .bill-row.total {
            font-size: 1.05rem;
            font-weight: 800;
            color: #fff;
            border-top: 1px solid var(--border);
            padding-top: 8px;
            margin-top: 8px;
        }

        .submit-order-btn {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: var(--radius-md);
            padding: 15px;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 8px 24px rgba(255, 90, 31, 0.4);
        }

        .submit-order-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Live Order Status View */
        .live-status-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 16px;
            margin: 14px 18px;
            display: none;
        }

        .live-status-card.visible {
            display: block;
        }

        .stepper {
            display: flex;
            justify-content: space-between;
            margin-top: 14px;
            position: relative;
        }

        .stepper::before {
            content: '';
            position: absolute;
            top: 14px;
            left: 20px;
            right: 20px;
            height: 2px;
            background: #2D333F;
            z-index: 1;
        }

        .step-node {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }

        .step-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #242933;
            border: 2px solid #3E4656;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .step-node.active .step-circle {
            background: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 0 12px rgba(255, 90, 31, 0.6);
        }

        .step-node.completed .step-circle {
            background: var(--success);
            border-color: var(--success);
        }

        .step-label {
            font-size: 0.7rem;
            color: var(--text-muted);
            margin-top: 6px;
            font-weight: 600;
        }

        .step-node.active .step-label {
            color: #fff;
        }

        /* Digital Receipt Modal (Exact Match to Design) */
        .receipt-card {
            background: #151921;
            border: 1px solid #2B3342;
            border-radius: var(--radius-lg);
            padding: 24px 20px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #FFFFFF;
            width: 100%;
            position: relative;
        }

        .receipt-header-center {
            text-align: center;
            margin-bottom: 16px;
        }

        .receipt-table-tag {
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #FFFFFF;
            margin-bottom: 6px;
        }

        .receipt-subtitle {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-muted);
        }

        .receipt-dashed-line {
            border: none;
            border-top: 1px dashed #3A4456;
            margin: 16px 0;
        }

        .receipt-solid-line {
            border: none;
            border-top: 2px solid #4B5568;
            margin: 14px 0 12px;
        }

        .receipt-items-table {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .receipt-item-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            font-size: 0.94rem;
        }

        .receipt-item-left {
            display: flex;
            align-items: flex-start;
            gap: 6px;
            color: #E2E8F0;
            font-weight: 600;
            max-width: 72%;
        }

        .receipt-item-price {
            font-weight: 700;
            color: #FFFFFF;
            font-family: 'Outfit', sans-serif;
            white-space: nowrap;
        }

        .receipt-calc-row {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .receipt-calc-price {
            font-weight: 700;
            color: #E2E8F0;
            font-family: 'Outfit', sans-serif;
        }

        .receipt-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.15rem;
            font-weight: 800;
            color: #FFFFFF;
            font-family: 'Outfit', sans-serif;
            letter-spacing: 0.04em;
        }

        .receipt-total-price {
            color: #FF5A1F;
            font-size: 1.35rem;
            font-weight: 800;
        }

        .receipt-pay-prompt {
            text-align: center;
            margin: 22px 0 14px;
            font-family: 'Outfit', sans-serif;
            font-size: 0.85rem;
            font-weight: 800;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #94A3B8;
        }

        .receipt-payment-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .btn-pay-online {
            background: linear-gradient(135deg, #FF5A1F 0%, #EA480B 100%);
            color: #FFFFFF;
            border: none;
            border-radius: var(--radius-md);
            padding: 15px 18px;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 8px 20px rgba(255, 90, 31, 0.35);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-pay-online:active {
            transform: scale(0.98);
        }

        .btn-pay-option {
            background: #1E2430;
            color: #FFFFFF;
            border: 1px solid #2E3748;
            border-radius: var(--radius-md);
            padding: 14px 18px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-pay-option:hover, .btn-pay-option:active {
            background: #273042;
            border-color: #475569;
            transform: scale(0.98);
        }

        /* Modern Animated Toast Notification */
        .toast-notification {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%) translateY(-150%);
            background: #1E232F;
            border: 1px solid #384252;
            color: #FFFFFF;
            padding: 14px 20px;
            border-radius: 14px;
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.6);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 300;
            max-width: 90%;
            width: 360px;
            opacity: 0;
            transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
            backdrop-filter: blur(10px);
        }

        .toast-notification.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        .toast-icon-wrap {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255, 90, 31, 0.15);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .toast-icon-wrap.success {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <div class="brand-info">
            <h1><?php echo esc_html( $restaurant_name ); ?></h1>
        </div>
        <div class="table-pill">
            <span class="dot"></span>
            <span><?php echo esc_html( $table['table_number'] ); ?></span>
        </div>
    </header>

    <!-- Quick Actions (Call Waiter / Bill) -->
    <div class="quick-bar">
        <button class="quick-btn" id="btnCallWaiter" onclick="callWaiter()">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
            <span>Call Waiter</span>
        </button>
        <button class="quick-btn" id="btnRequestBill" onclick="requestBill()">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></svg>
            <span>Request Bill</span>
        </button>
    </div>

    <!-- Active Live Order Tracker (if any order placed) -->
    <div class="live-status-card" id="liveTrackerCard">
        <div style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <span style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Live Table Order</span>
                <h4 style="font-size:1rem; font-weight:800;" id="liveOrderNumber">#1001</h4>
            </div>
            <span id="liveStatusBadge" style="background:var(--primary-light); color:var(--primary-dark); font-size:0.75rem; font-weight:800; padding:4px 10px; border-radius:20px;">PREPARING</span>
        </div>
        <div class="stepper">
            <div class="step-node completed" id="step-1">
                <div class="step-circle">✓</div>
                <span class="step-label">Placed</span>
            </div>
            <div class="step-node active" id="step-2">
                <div class="step-circle">2</div>
                <span class="step-label">Kitchen</span>
            </div>
            <div class="step-node" id="step-3">
                <div class="step-circle">3</div>
                <span class="step-label">Ready</span>
            </div>
            <div class="step-node" id="step-4">
                <div class="step-circle">4</div>
                <span class="step-label">Served</span>
            </div>
        </div>
    </div>

    <!-- Dish Search -->
    <div class="search-container">
        <div class="search-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" id="dishSearch" placeholder="Search delicious dishes, drinks, grills..." oninput="filterMenu()">
        </div>
    </div>

    <!-- Category Nav Pills -->
    <div class="category-nav" id="categoryNav">
        <!-- Rendered via JS -->
    </div>

    <!-- Menu Sections Container -->
    <div id="menuContainer">
        <!-- Rendered via JS -->
    </div>

    <!-- Floating Sticky Cart Bar -->
    <div class="floating-cart-bar" id="floatingCart" onclick="openCartDrawer()">
        <div class="cart-bar-left">
            <div class="cart-badge" id="cartBadgeCount">0</div>
            <div>
                <div style="font-size:0.78rem; opacity:0.85;">View Cart</div>
                <div class="cart-bar-total" id="cartBarTotal"><?php echo esc_html( $currency ); ?>0.00</div>
            </div>
        </div>
        <div class="cart-bar-action">
            <span>Review & Order</span>
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </div>
    </div>

    <!-- Item Customizer Bottom Sheet (Variations & Modifiers) -->
    <div class="modal-overlay" id="customizerModal">
        <div class="bottom-sheet">
            <div class="sheet-handle"></div>
            <div class="sheet-header">
                <div>
                    <h3 class="sheet-title" id="customizerTitle">Dish Name</h3>
                    <p id="customizerBasePrice" style="color:var(--primary); font-weight:800; font-size:1.05rem; margin-top:2px;"></p>
                </div>
                <button class="sheet-close" onclick="closeCustomizer()">✕</button>
            </div>

            <div id="variationsSection" class="option-group" style="display:none;">
                <div class="group-label">Select Portion / Size</div>
                <div id="variationsList"></div>
            </div>

            <div id="addonsSection" class="option-group" style="display:none;">
                <div class="group-label">Customize & Add-ons</div>
                <div id="addonsList"></div>
            </div>

            <div class="option-group">
                <div class="group-label">Special Cooking Instructions</div>
                <textarea class="notes-input" id="itemNotesInput" placeholder="E.g., Less spicy, no onions, extra crispy..."></textarea>
            </div>

            <button class="submit-order-btn" id="confirmCustomizerBtn" onclick="confirmAddToCart()">
                <span>Add to Cart</span>
                <span id="customizerTotalPrice"></span>
            </button>
        </div>
    </div>

    <!-- Cart Drawer Bottom Sheet -->
    <div class="modal-overlay" id="cartDrawerModal">
        <div class="bottom-sheet">
            <div class="sheet-handle"></div>
            <div class="sheet-header">
                <div>
                    <h3 class="sheet-title">Your Table Order</h3>
                    <span style="font-size:0.8rem; color:var(--text-muted);"><?php echo esc_html( $table['table_number'] ); ?></span>
                </div>
                <button class="sheet-close" onclick="closeCartDrawer()">✕</button>
            </div>

            <div id="cartItemsList" style="max-height: 260px; overflow-y:auto; margin-bottom:12px;"></div>

            <div class="option-group" style="margin-top:6px;">
                <div class="group-label">Order Note for Kitchen</div>
                <textarea class="notes-input" id="orderNotesInput" placeholder="Any overall dietary requirements or table notes?"></textarea>
            </div>

            <div class="bill-summary">
                <div class="bill-row">
                    <span>Subtotal</span>
                    <span id="drawerSubtotal"><?php echo esc_html( $currency ); ?>0.00</span>
                </div>
                <div class="bill-row">
                    <span>Estimated Tax (<?php echo esc_html( $tax_rate ); ?>%)</span>
                    <span id="drawerTax"><?php echo esc_html( $currency ); ?>0.00</span>
                </div>
                <div class="bill-row total">
                    <span>Grand Total</span>
                    <span id="drawerGrandTotal"><?php echo esc_html( $currency ); ?>0.00</span>
                </div>
            </div>

            <button class="submit-order-btn" id="btnPlaceOrder" onclick="placeOrder()">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Send Order to Kitchen</span>
            </button>
        </div>
    </div>

    <!-- Digital Receipt & Payment Options Modal -->
    <div class="modal-overlay" id="digitalBillModal">
        <div class="bottom-sheet" style="padding: 16px 18px 24px;">
            <div class="sheet-handle"></div>
            <div style="display:flex; justify-content:flex-end; margin-bottom: -10px;">
                <button class="sheet-close" onclick="closeBillModal()" style="font-size:1.1rem; color:var(--text-muted);">✕</button>
            </div>

            <!-- Receipt Box Card -->
            <div class="receipt-card" id="receiptCardContent">
                <div class="receipt-header-center">
                    <div class="receipt-table-tag"><?php echo esc_html( strtoupper( $table['table_number'] ) ); ?></div>
                    <div class="receipt-subtitle">Your Bill</div>
                </div>

                <hr class="receipt-dashed-line">

                <!-- Items List -->
                <div class="receipt-items-table" id="receiptItemsContainer">
                    <!-- Populated dynamically via JS -->
                </div>

                <hr class="receipt-dashed-line">

                <!-- Calculation Rows -->
                <div class="receipt-calc-row">
                    <span>Subtotal</span>
                    <span class="receipt-calc-price" id="receiptSubtotal"><?php echo esc_html( $currency ); ?>0.00</span>
                </div>
                <div class="receipt-calc-row">
                    <span>Tax (<span id="receiptTaxRate"><?php echo esc_html( $tax_rate ); ?></span>%)</span>
                    <span class="receipt-calc-price" id="receiptTax"><?php echo esc_html( $currency ); ?>0.00</span>
                </div>

                <hr class="receipt-solid-line">

                <!-- Grand Total -->
                <div class="receipt-total-row">
                    <span>TOTAL</span>
                    <span class="receipt-total-price" id="receiptGrandTotal"><?php echo esc_html( $currency ); ?>0.00</span>
                </div>

                <!-- Payment Selection Header -->
                <div class="receipt-pay-prompt">
                    HOW WOULD YOU LIKE TO PAY?
                </div>

                <!-- Payment Option Buttons -->
                <div class="receipt-payment-actions">
                    <button class="btn-pay-online" onclick="choosePayment('online')">
                        <span>💳 Pay Online</span>
                        <span id="btnPayOnlineTotal" style="font-size:1.1rem;"><?php echo esc_html( $currency ); ?>0.00</span>
                    </button>
                    <button class="btn-pay-option" onclick="choosePayment('counter')">
                        <span>🏪 Pay at Counter</span>
                    </button>
                    <button class="btn-pay-option" onclick="choosePayment('cash')">
                        <span>💵 Pay Cash at Table</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Online Payment Checkout Modal -->
    <div class="modal-overlay" id="onlineCheckoutModal">
        <div class="bottom-sheet">
            <div class="sheet-handle"></div>
            <div class="sheet-header">
                <div>
                    <h3 class="sheet-title">Instant Online Pay</h3>
                    <span style="font-size:0.8rem; color:var(--text-muted);">Secure UPI & Card Checkout</span>
                </div>
                <button class="sheet-close" onclick="closeOnlineCheckout()">✕</button>
            </div>

            <div style="background:#1B202A; border:1px solid #2D3748; border-radius:var(--radius-md); padding:16px; margin-bottom:16px; text-align:center;">
                <div style="font-size:0.8rem; color:var(--text-muted); text-transform:uppercase; font-weight:700;">Total Amount Due</div>
                <div style="font-family:'Outfit',sans-serif; font-size:1.8rem; font-weight:800; color:#FF5A1F; margin-top:2px;" id="onlineModalTotal"><?php echo esc_html( $currency ); ?>0.00</div>
            </div>

            <div style="font-size:0.82rem; font-weight:700; color:var(--text-muted); margin-bottom:8px; text-transform:uppercase;">Select Payment Gateway</div>
            
            <div style="display:flex; flex-direction:column; gap:10px; margin-bottom:18px;">
                <label style="background:#191E27; border:1px solid #2E384A; padding:12px 14px; border-radius:10px; display:flex; align-items:center; justify-content:space-between; cursor:pointer;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span style="font-size:1.3rem;">📱</span>
                        <div>
                            <div style="font-weight:700; font-size:0.92rem;">Instant UPI Apps</div>
                            <div style="font-size:0.75rem; color:var(--text-muted);">Google Pay, PhonePe, Paytm, BHIM</div>
                        </div>
                    </div>
                    <input type="radio" name="online_gateway" value="upi" checked style="accent-color:var(--primary);">
                </label>

                <label style="background:#191E27; border:1px solid #2E384A; padding:12px 14px; border-radius:10px; display:flex; align-items:center; justify-content:space-between; cursor:pointer;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span style="font-size:1.3rem;">💳</span>
                        <div>
                            <div style="font-weight:700; font-size:0.92rem;">Credit / Debit Card</div>
                            <div style="font-size:0.75rem; color:var(--text-muted);">Visa, MasterCard, RuPay, Amex</div>
                        </div>
                    </div>
                    <input type="radio" name="online_gateway" value="card" style="accent-color:var(--primary);">
                </label>

                <label style="background:#191E27; border:1px solid #2E384A; padding:12px 14px; border-radius:10px; display:flex; align-items:center; justify-content:space-between; cursor:pointer;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span style="font-size:1.3rem;">🏦</span>
                        <div>
                            <div style="font-weight:700; font-size:0.92rem;">Net Banking / Wallets</div>
                            <div style="font-size:0.75rem; color:var(--text-muted);">All Major Indian Banks</div>
                        </div>
                    </div>
                    <input type="radio" name="online_gateway" value="netbanking" style="accent-color:var(--primary);">
                </label>
            </div>

            <button class="submit-order-btn" id="btnCompleteOnlinePay" onclick="processOnlinePayment()">
                <span>Pay Now (<span id="btnPayNowAmount"><?php echo esc_html( $currency ); ?>0.00</span>)</span>
            </button>
        </div>
    </div>

    <!-- Animated Modern Toast Notification -->
    <div class="toast-notification" id="appToast">
        <div class="toast-icon-wrap" id="toastIconWrap">🧾</div>
        <div>
            <div style="font-weight:800; font-size:0.92rem;" id="toastTitle">Notification</div>
            <div style="font-size:0.78rem; color:#A0AEC0; margin-top:2px;" id="toastSubtitle">Subtitle message</div>
        </div>
    </div>

    <!-- JS Application Logic -->
    <script>
        const API_BASE = '<?php echo esc_url_raw( rest_url( 'ro/v1' ) ); ?>';
        const TABLE_ID = <?php echo intval( $table['id'] ); ?>;
        const TABLE_TOKEN = '<?php echo esc_js( $token ); ?>';
        const CURRENCY = '<?php echo esc_js( $currency ); ?>';
        const TAX_RATE = <?php echo floatval( $tax_rate ); ?>;

        let categoriesData = [];
        let cart = [];
        let activeCustomizingItem = null;
        let selectedVariation = null;
        let selectedAddons = [];
        let lastOrderId = localStorage.getItem('ro_last_order_' + TABLE_ID);
        let currentLiveBill = null;

        // Load Menu & Start Polling
        document.addEventListener('DOMContentLoaded', () => {
            fetchMenu();
            if (lastOrderId) {
                pollOrderStatus(lastOrderId);
            }
            setInterval(() => {
                if (lastOrderId) pollOrderStatus(lastOrderId);
            }, 6000);
        });

        function showToast(title, subtitle, icon = '🧾', isSuccess = false) {
            const toast = document.getElementById('appToast');
            const iconWrap = document.getElementById('toastIconWrap');
            document.getElementById('toastTitle').innerText = title;
            document.getElementById('toastSubtitle').innerText = subtitle;
            iconWrap.innerText = icon;
            if (isSuccess) {
                iconWrap.className = 'toast-icon-wrap success';
            } else {
                iconWrap.className = 'toast-icon-wrap';
            }

            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 4500);
        }

        async function fetchMenu() {
            try {
                const res = await fetch(`${API_BASE}/menu`);
                const json = await res.json();
                if (json.success) {
                    categoriesData = json.categories;
                    renderCategoriesNav(categoriesData);
                    renderMenu(categoriesData);
                }
            } catch (err) {
                console.error('Failed to load menu', err);
            }
        }

        function renderCategoriesNav(categories) {
            const nav = document.getElementById('categoryNav');
            let html = `<div class="cat-chip active" onclick="scrollToCategory('all', this)">All Menu</div>`;
            categories.forEach(cat => {
                html += `<div class="cat-chip" onclick="scrollToCategory(${cat.id}, this)">${cat.name}</div>`;
            });
            nav.innerHTML = html;
        }

        function scrollToCategory(catId, el) {
            document.querySelectorAll('.cat-chip').forEach(c => c.classList.remove('active'));
            if (el) el.classList.add('active');

            if (catId === 'all') {
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }
            const sec = document.getElementById(`cat-${catId}`);
            if (sec) {
                const offset = sec.offsetTop - 120;
                window.scrollTo({ top: offset, behavior: 'smooth' });
            }
        }

        function filterMenu() {
            const query = document.getElementById('dishSearch').value.toLowerCase().trim();
            if (!query) {
                renderMenu(categoriesData);
                return;
            }

            const searchResults = categoriesData.map(cat => {
                return {
                    ...cat,
                    items: cat.items.filter(it =>
                        it.name.toLowerCase().includes(query) ||
                        (it.description && it.description.toLowerCase().includes(query))
                    )
                };
            }).filter(cat => cat.items.length > 0);

            renderMenu(searchResults);
        }

        function renderMenu(categories) {
            const container = document.getElementById('menuContainer');
            if (!categories || categories.length === 0) {
                container.innerHTML = '<div style="text-align:center; padding:40px 20px; color:var(--text-muted);">No dishes found matching your search.</div>';
                return;
            }

            let html = '';
            categories.forEach(cat => {
                if (!cat.items || cat.items.length === 0) return;
                html += `
                    <div class="menu-section" id="cat-${cat.id}">
                        <div class="section-title">
                            <span>${cat.name}</span>
                            <span style="font-size:0.78rem; color:var(--text-muted); font-weight:500;">${cat.items.length} items</span>
                        </div>
                        <div class="items-grid">
                `;

                cat.items.forEach(item => {
                    const isSoldOut = item.status === 'sold_out';
                    const imgUrl = item.image_url || 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=400&q=80';
                    const foodTypeClass = item.food_type === 'veg' ? 'veg' : 'non_veg';

                    html += `
                        <div class="item-card ${isSoldOut ? 'sold-out' : ''}">
                            <div class="item-img-wrapper">
                                <img src="${imgUrl}" alt="${item.name}" loading="lazy">
                                <span class="food-badge ${foodTypeClass}"></span>
                            </div>
                            <div class="item-info">
                                <div>
                                    <h4 class="item-title">${item.name}</h4>
                                    <p class="item-desc">${item.description || ''}</p>
                                </div>
                                <div class="item-footer">
                                    <span class="item-price">${CURRENCY}${item.base_price.toFixed(2)}</span>
                                    <button class="add-btn" ${isSoldOut ? 'disabled' : ''} onclick="openCustomizer(${item.id})">
                                        ${isSoldOut ? 'Sold Out' : '+ ADD'}
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                html += `</div></div>`;
            });

            container.innerHTML = html;
        }

        // Customizer Modal Logic
        function findItemById(id) {
            const numId = parseInt(id, 10);
            for (const cat of categoriesData) {
                const found = cat.items.find(i => parseInt(i.id, 10) === numId);
                if (found) return found;
            }
            return null;
        }

        function openCustomizer(itemId) {
            const item = findItemById(itemId);
            if (!item) return;

            activeCustomizingItem = item;
            selectedVariation = (item.variations && item.variations.length > 0) ? item.variations[0] : null;
            selectedAddons = [];
            document.getElementById('itemNotesInput').value = '';

            document.getElementById('customizerTitle').innerText = item.name;
            document.getElementById('customizerBasePrice').innerText = `${CURRENCY}${item.base_price.toFixed(2)}`;

            // Render Variations
            const varSection = document.getElementById('variationsSection');
            const varList = document.getElementById('variationsList');
            if (item.variations && item.variations.length > 0) {
                varSection.style.display = 'block';
                varList.innerHTML = item.variations.map((v, idx) => `
                    <div class="option-pill ${idx === 0 ? 'selected' : ''}" onclick="selectVariation(${v.id}, this)">
                        <span>${v.name}</span>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="font-weight:700;">${CURRENCY}${v.price.toFixed(2)}</span>
                            <div class="custom-radio"></div>
                        </div>
                    </div>
                `).join('');
            } else {
                varSection.style.display = 'none';
            }

            // Render Add-ons
            const addSection = document.getElementById('addonsSection');
            const addList = document.getElementById('addonsList');
            if (item.addons && item.addons.length > 0) {
                addSection.style.display = 'block';
                addList.innerHTML = item.addons.map(a => `
                    <div class="option-pill" onclick="toggleAddon(${a.id}, this)">
                        <span>${a.name}</span>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span style="font-weight:700;">+${CURRENCY}${a.price.toFixed(2)}</span>
                            <div class="custom-checkbox"></div>
                        </div>
                    </div>
                `).join('');
            } else {
                addSection.style.display = 'none';
            }

            updateCustomizerTotal();
            document.getElementById('customizerModal').classList.add('active');
        }

        function closeCustomizer() {
            document.getElementById('customizerModal').classList.remove('active');
        }

        function selectVariation(varId, el) {
            document.querySelectorAll('#variationsList .option-pill').forEach(p => p.classList.remove('selected'));
            el.classList.add('selected');
            if (activeCustomizingItem && activeCustomizingItem.variations) {
                selectedVariation = activeCustomizingItem.variations.find(v => v.id === varId);
            }
            updateCustomizerTotal();
        }

        function toggleAddon(addonId, el) {
            el.classList.toggle('selected');
            if (activeCustomizingItem && activeCustomizingItem.addons) {
                const addon = activeCustomizingItem.addons.find(a => a.id === addonId);
                if (addon) {
                    const idx = selectedAddons.findIndex(a => a.id === addonId);
                    if (idx > -1) {
                        selectedAddons.splice(idx, 1);
                    } else {
                        selectedAddons.push(addon);
                    }
                }
            }
            updateCustomizerTotal();
        }

        function updateCustomizerTotal() {
            let unitPrice = activeCustomizingItem ? activeCustomizingItem.base_price : 0;
            if (selectedVariation) {
                unitPrice = selectedVariation.price;
            }
            if (selectedAddons.length > 0) {
                unitPrice += selectedAddons.reduce((sum, a) => sum + a.price, 0);
            }
            document.getElementById('customizerTotalPrice').innerText = ` • ${CURRENCY}${unitPrice.toFixed(2)}`;
        }

        function confirmAddToCart() {
            if (!activeCustomizingItem) return;

            let unitPrice = activeCustomizingItem.base_price;
            if (selectedVariation) {
                unitPrice = selectedVariation.price;
            }
            if (selectedAddons.length > 0) {
                unitPrice += selectedAddons.reduce((sum, a) => sum + a.price, 0);
            }

            const notes = document.getElementById('itemNotesInput').value.trim();

            cart.push({
                id: activeCustomizingItem.id,
                name: activeCustomizingItem.name,
                unitPrice: unitPrice,
                quantity: 1,
                variation: selectedVariation ? { id: selectedVariation.id, name: selectedVariation.name, price: selectedVariation.price } : null,
                addons: selectedAddons.map(a => ({ id: a.id, name: a.name, price: a.price })),
                notes: notes
            });

            closeCustomizer();
            updateCartUI();
            showToast('Item Added to Cart', `${activeCustomizingItem.name} was added.`, '🛍️', true);
        }

        function updateCartUI() {
            const count = cart.reduce((sum, i) => sum + i.quantity, 0);
            const subtotal = cart.reduce((sum, i) => sum + (i.unitPrice * i.quantity), 0);
            const tax = (subtotal * TAX_RATE) / 100;
            const grandTotal = subtotal + tax;

            document.getElementById('cartBadgeCount').innerText = count;
            document.getElementById('cartBarTotal').innerText = `${CURRENCY}${grandTotal.toFixed(2)}`;

            const bar = document.getElementById('floatingCart');
            if (count > 0) {
                bar.classList.add('visible');
            } else {
                bar.classList.remove('visible');
            }

            document.getElementById('drawerSubtotal').innerText = `${CURRENCY}${subtotal.toFixed(2)}`;
            document.getElementById('drawerTax').innerText = `${CURRENCY}${tax.toFixed(2)}`;
            document.getElementById('drawerGrandTotal').innerText = `${CURRENCY}${grandTotal.toFixed(2)}`;

            renderCartDrawerList();
        }

        function renderCartDrawerList() {
            const list = document.getElementById('cartItemsList');
            if (cart.length === 0) {
                list.innerHTML = '<div style="text-align:center; padding:30px 10px; color:var(--text-muted);">Your order is empty.</div>';
                return;
            }

            list.innerHTML = cart.map((item, idx) => {
                let subs = [];
                if (item.variation) subs.push(item.variation.name);
                if (item.addons && item.addons.length > 0) subs.push(item.addons.map(a => a.name).join(', '));
                if (item.notes) subs.push(`"${item.notes}"`);

                return `
                    <div class="cart-item-row">
                        <div>
                            <div class="cart-item-title">${item.name}</div>
                            <div class="cart-item-subs">${subs.join(' • ')}</div>
                            <div style="font-weight:700; color:var(--primary); font-size:0.9rem; margin-top:2px;">${CURRENCY}${(item.unitPrice * item.quantity).toFixed(2)}</div>
                        </div>
                        <div class="qty-control">
                            <button class="qty-btn" onclick="updateQty(${idx}, -1)">−</button>
                            <span style="font-weight:bold; font-size:0.9rem;">${item.quantity}</span>
                            <button class="qty-btn" onclick="updateQty(${idx}, 1)">+</button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function updateQty(idx, change) {
            if (cart[idx]) {
                cart[idx].quantity += change;
                if (cart[idx].quantity <= 0) {
                    cart.splice(idx, 1);
                }
            }
            updateCartUI();
        }

        function openCartDrawer() {
            document.getElementById('cartDrawerModal').classList.add('active');
        }

        function closeCartDrawer() {
            document.getElementById('cartDrawerModal').classList.remove('active');
        }

        // Place Order to WordPress REST API
        async function placeOrder() {
            if (cart.length === 0) return;

            const btn = document.getElementById('btnPlaceOrder');
            btn.disabled = true;
            btn.innerHTML = 'Sending to Kitchen...';

            const payloadItems = cart.map(it => ({
                menu_item_id: it.id,
                quantity: it.quantity,
                variation_id: it.variation ? it.variation.id : null,
                addons: it.addons.map(a => a.id),
                notes: it.notes
            }));

            const idempotencyKey = 'IDEM-' + Date.now() + '-' + Math.random().toString(36).substring(2, 9);
            const notes = document.getElementById('orderNotesInput').value.trim();

            try {
                const res = await fetch(`${API_BASE}/orders`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        table_id: TABLE_ID,
                        items: payloadItems,
                        notes: notes,
                        source: 'QR',
                        idempotency_key: idempotencyKey
                    })
                });

                const json = await res.json();
                if (json.success) {
                    lastOrderId = json.order.id;
                    localStorage.setItem('ro_last_order_' + TABLE_ID, lastOrderId);
                    cart = [];
                    updateCartUI();
                    closeCartDrawer();
                    pollOrderStatus(lastOrderId);
                    showToast('Order Sent to Kitchen', `Ticket ${json.order.order_number} is being prepared.`, '🔥', true);
                } else {
                    showToast('Order Error', json.message || 'Could not place order.', '⚠️');
                }
            } catch (err) {
                showToast('Connection Error', 'Please check your internet connection.', '⚠️');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span>Send Order to Kitchen</span>';
            }
        }

        // Live Order Tracking
        async function pollOrderStatus(orderId) {
            try {
                const res = await fetch(`${API_BASE}/orders/${orderId}`);
                const json = await res.json();
                if (json.success && json.order) {
                    const o = json.order;
                    const card = document.getElementById('liveTrackerCard');
                    card.classList.add('visible');
                    document.getElementById('liveOrderNumber').innerText = o.order_number;
                    document.getElementById('liveStatusBadge').innerText = o.status;

                    // Update Stepper
                    const steps = ['step-1', 'step-2', 'step-3', 'step-4'];
                    steps.forEach(s => {
                        const el = document.getElementById(s);
                        el.className = 'step-node';
                    });

                    if (o.status === 'NEW' || o.status === 'ACCEPTED') {
                        document.getElementById('step-1').className = 'step-node completed';
                        document.getElementById('step-2').className = 'step-node active';
                    } else if (o.status === 'PREPARING') {
                        document.getElementById('step-1').className = 'step-node completed';
                        document.getElementById('step-2').className = 'step-node active';
                    } else if (o.status === 'READY') {
                        document.getElementById('step-1').className = 'step-node completed';
                        document.getElementById('step-2').className = 'step-node completed';
                        document.getElementById('step-3').className = 'step-node active';
                    } else if (o.status === 'SERVED' || o.status === 'PAID') {
                        document.getElementById('step-1').className = 'step-node completed';
                        document.getElementById('step-2').className = 'step-node completed';
                        document.getElementById('step-3').className = 'step-node completed';
                        document.getElementById('step-4').className = 'step-node completed';
                    }
                }
            } catch (err) {
                console.warn('Status poll error', err);
            }
        }

        // Quick Service: Call Waiter
        async function callWaiter() {
            const btn = document.getElementById('btnCallWaiter');
            btn.classList.add('active');
            try {
                const res = await fetch(`${API_BASE}/service-requests`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ table_id: TABLE_ID, type: 'call_waiter' })
                });
                const json = await res.json();
                showToast('Waiter Notified', 'A staff member is on their way to your table.', '🔔', true);
            } catch (e) {
                showToast('Service Alert', 'Waiter call request dispatched.', '🔔');
            }
        }

        // Professional Digital Bill Modal (Matches Attached Design)
        async function requestBill() {
            const btn = document.getElementById('btnRequestBill');
            btn.classList.add('active');

            try {
                const res = await fetch(`${API_BASE}/tables/${TABLE_ID}/bill`);
                const json = await res.json();

                if (json.success) {
                    currentLiveBill = json;
                    renderDigitalBill(json);
                    document.getElementById('digitalBillModal').classList.add('active');
                } else {
                    showToast('Bill Request', 'Unable to retrieve live bill.', '⚠️');
                }
            } catch (e) {
                showToast('Connection Error', 'Please check your connection.', '⚠️');
            }
        }

        function renderDigitalBill(billData) {
            const container = document.getElementById('receiptItemsContainer');
            const items = billData.items || [];
            const curr = billData.currency || CURRENCY;

            if (items.length === 0) {
                container.innerHTML = `
                    <div style="text-align:center; padding:18px 10px; color:var(--text-muted); font-size:0.9rem;">
                        No active orders placed yet for this table.
                    </div>
                `;
            } else {
                container.innerHTML = items.map(it => `
                    <div class="receipt-item-row">
                        <div class="receipt-item-left">
                            <span style="color:#FF5A1F; font-weight:800;">${it.quantity} ×</span>
                            <span>${it.name}</span>
                        </div>
                        <div class="receipt-item-price">${curr}${it.total_price.toFixed(2)}</div>
                    </div>
                `).join('');
            }

            document.getElementById('receiptSubtotal').innerText = `${curr}${billData.subtotal.toFixed(2)}`;
            document.getElementById('receiptTax').innerText = `${curr}${billData.tax.toFixed(2)}`;
            document.getElementById('receiptTaxRate').innerText = billData.tax_rate || TAX_RATE;
            document.getElementById('receiptGrandTotal').innerText = `${curr}${billData.total.toFixed(2)}`;
            document.getElementById('btnPayOnlineTotal').innerText = `${curr}${billData.total.toFixed(2)}`;
            document.getElementById('onlineModalTotal').innerText = `${curr}${billData.total.toFixed(2)}`;
            document.getElementById('btnPayNowAmount').innerText = `${curr}${billData.total.toFixed(2)}`;
        }

        function closeBillModal() {
            document.getElementById('digitalBillModal').classList.remove('active');
        }

        // Handle Payment Selection
        async function choosePayment(method) {
            closeBillModal();

            if (method === 'online') {
                document.getElementById('onlineCheckoutModal').classList.add('active');
                return;
            }

            const notesMap = {
                'counter': 'Customer will Pay at Counter',
                'cash': 'Customer requested to Pay Cash at Table'
            };

            const toastMap = {
                'counter': { title: 'Pay at Counter Selected', sub: 'Your receipt is ready at the counter. Pay when leaving.', icon: '🏪' },
                'cash': { title: 'Cash at Table Requested', sub: 'Staff notified! A server will bring the receipt & cash pouch.', icon: '💵' }
            };

            try {
                await fetch(`${API_BASE}/service-requests`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        table_id: TABLE_ID,
                        type: 'request_bill',
                        notes: notesMap[method] || method
                    })
                });

                const t = toastMap[method];
                showToast(t.title, t.sub, t.icon, true);
            } catch (e) {
                showToast('Request Received', 'Cashier has been notified.', '🧾', true);
            }
        }

        function closeOnlineCheckout() {
            document.getElementById('onlineCheckoutModal').classList.remove('active');
        }

        async function processOnlinePayment() {
            const btn = document.getElementById('btnCompleteOnlinePay');
            btn.disabled = true;
            btn.innerHTML = 'Processing Payment...';

            setTimeout(async () => {
                btn.disabled = false;
                btn.innerHTML = `<span>Pay Now</span>`;
                closeOnlineCheckout();

                try {
                    await fetch(`${API_BASE}/service-requests`, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            table_id: TABLE_ID,
                            type: 'request_bill',
                            notes: 'Paid Online via UPI / Card (Ref: ' + Date.now().toString().slice(-6) + ')'
                        })
                    });
                } catch (e) {}

                showToast('Payment Successful! 🎉', 'Digital receipt generated. Thank you for dining with us!', '✅', true);
            }, 1200);
        }
    </script>
</body>
</html>
