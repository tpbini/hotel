# Restaurant QR Table Ordering & POS WordPress Plugin

A production-grade, API-first WordPress plugin for restaurant dine-in table ordering, kitchen operations, counter cashiering, and thermal receipt printing.

---

## 🌟 Key Features

1. **Customer QR Mobile Ordering (`/order/t/{token}`)**:
   - Touch/mobile-first responsive Single Page Application.
   - Zero login / no account requirement.
   - Interactive modal for portion sizes (variations) and customizations (add-ons).
   - Floating cart drawer with real-time bill breakdown.
   - Authoritative server-side price verification and duplicate order prevention (idempotency).
   - Live Order Progress Stepper (Placed $\rightarrow$ In Kitchen $\rightarrow$ Ready $\rightarrow$ Served).
   - Quick one-tap **Call Waiter** & **Request Bill** actions.

2. **Kitchen Display System (KDS) (`/restaurant-kds/`)**:
   - Fullscreen kitchen touchscreen dashboard.
   - Filter by preparation station (Main Kitchen, Grill, Bar, Dessert).
   - Real-time order cards with elapsed timers and urgency highlighting.
   - Web Audio chime sound notifications on incoming tickets.
   - Single-tap status progression (`Start Preparing`, `Mark Ready`, `Mark Served`).

3. **Counter POS (`/restaurant-pos/`)**:
   - Floor map / table occupancy overview.
   - Multi-order table session aggregation.
   - Payment settlement supporting **Cash**, **Card**, **UPI**, and **Other**.
   - Manager discount application and table session closure.
   - Instant notifications for waiter calls and bill requests.

4. **Waiter Mode (`/restaurant-waiter/`)**:
   - Fast mobile ordering tool for floor staff.
   - Table switcher with instant order dispatching to kitchen stations.

5. **Local Thermal Print Bridge (`print-bridge/bridge.js`)**:
   - Background daemon for physical ESC/POS thermal printers.
   - Persistent queue with automatic retry, reprint auditing, and station routing.

---

## 🚀 Routes & Access URLs

- **Customer QR Ordering**: `http://127.0.0.1:9400/order/t/{token}`
- **Kitchen KDS**: `http://127.0.0.1:9400/restaurant-kds/`
- **Counter POS**: `http://127.0.0.1:9400/restaurant-pos/`
- **Waiter Mode**: `http://127.0.0.1:9400/restaurant-waiter/`
- **WP Admin Management**: `http://127.0.0.1:9400/wp-admin/admin.php?page=restaurant-qr-pos`
- **REST API Base**: `http://127.0.0.1:9400/wp-json/ro/v1/`
