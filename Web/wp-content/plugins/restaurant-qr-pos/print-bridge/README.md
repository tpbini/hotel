# Local ESC/POS Thermal Print Bridge

The **Local Print Bridge** is a lightweight Node.js service that runs on any computer connected to physical receipt/kitchen thermal printers (LAN Ethernet, USB, or Wi-Fi).

---

## 1. How It Works
1. Whenever a customer or waiter places an order, WordPress automatically creates persistent print jobs in `wp_ro_print_jobs`.
2. This bridge polls `/wp-json/ro/v1/print-jobs` securely using the configured `x-bridge-token`.
3. The bridge formats and prints Kitchen Order Tickets (KOT) directly to the assigned station printer and customer invoices to the counter printer.
4. The bridge immediately sends back an acknowledgment to WordPress (`/wp-json/ro/v1/print-jobs/{id}/ack`) marking the job `PRINTED`.

---

## 2. Configuration & Running

### Prerequisites
- Node.js 16+

### Environment Variables
- `WP_URL`: Your WordPress site base URL (e.g. `http://127.0.0.1:9400` or `https://restaurant.com`)
- `BRIDGE_TOKEN`: The security token from **WP Admin $\rightarrow$ Restaurant POS $\rightarrow$ Settings**.
- `PRINTER_MODE`: `SIMULATOR` (prints to terminal console) or `NETWORK` (sends raw ESC/POS to IP printer).

### Start Command
```bash
node bridge.js
```
