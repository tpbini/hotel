/**
 * Standalone Local ESC/POS Thermal Print Bridge Daemon for Restaurant Operations
 * Polls WordPress REST API for pending Kitchen Order Tickets (KOT) & Counter Invoices.
 * 
 * Supports:
 * - Direct Network (LAN / IP) TCP Socket printing (e.g. 192.168.1.20:9100)
 * - Multi-Printer Station Routing (Kitchen Printer vs Counter Printer)
 * - Full ESC/POS formatting (Bolding, Center, Cut, Kitchen Buzzer/Beep)
 * - Console Simulator Mode (Automatic fallback for testing without hardware)
 */

const http = require('http');
const https = require('https');
const net = require('net');
const fs = require('fs');
const path = require('path');
const url = require('url');

// Load config from config.json or environment
let fileConfig = {};
try {
    const configPath = path.join(__dirname, 'config.json');
    if (fs.existsSync(configPath)) {
        fileConfig = JSON.parse(fs.readFileSync(configPath, 'utf8'));
    }
} catch (e) {
    console.warn('⚠️ Could not load config.json, using defaults.');
}

const CONFIG = {
    wordpress_url: process.env.WP_URL || fileConfig.wordpress_url || 'http://127.0.0.1:9400',
    bridge_token: process.env.BRIDGE_TOKEN || fileConfig.bridge_token || '',
    poll_interval_ms: parseInt(process.env.POLL_INTERVAL || fileConfig.poll_interval_ms || 3000, 10),
    printers: fileConfig.printers || {
        kitchen: { ip: '192.168.1.20', port: 9100, beep_on_print: true },
        counter: { ip: '192.168.1.21', port: 9100, cut_paper: true }
    }
};

console.log('================================================================');
console.log(' 🖨️  Restaurant QR POS - Thermal Print Bridge Daemon');
console.log('================================================================');
console.log(`📡 WordPress Server : ${CONFIG.wordpress_url}`);
console.log(`⏱️  Polling Interval : ${CONFIG.poll_interval_ms} ms`);
console.log(`🍳 Kitchen Printer  : ${CONFIG.printers.kitchen.ip}:${CONFIG.printers.kitchen.port}`);
console.log(`💵 Counter Printer  : ${CONFIG.printers.counter.ip}:${CONFIG.printers.counter.port}`);
console.log('----------------------------------------------------------------');
console.log('🟢 Bridge active! Listening for new orders and invoices...\n');

// ESC/POS Command Constants
const ESC = '\x1B';
const GS  = '\x1D';

const ESCPOS = {
    INIT: Buffer.from([0x1B, 0x40]),                      // Initialize
    ALIGN_LEFT: Buffer.from([0x1B, 0x61, 0x00]),           // Align Left
    ALIGN_CENTER: Buffer.from([0x1B, 0x61, 0x01]),         // Align Center
    ALIGN_RIGHT: Buffer.from([0x1B, 0x61, 0x02]),          // Align Right
    BOLD_ON: Buffer.from([0x1B, 0x45, 0x01]),              // Bold ON
    BOLD_OFF: Buffer.from([0x1B, 0x45, 0x00]),             // Bold OFF
    DOUBLE_SIZE: Buffer.from([0x1D, 0x21, 0x11]),          // 2x Width & Height
    NORMAL_SIZE: Buffer.from([0x1D, 0x21, 0x00]),          // Normal Size
    CUT_FULL: Buffer.from([0x1D, 0x56, 0x41, 0x10]),       // Feed & Cut Paper
    BUZZER: Buffer.from([0x1B, 0x42, 0x03, 0x02]),         // Buzzer Beep (3 times)
    LINE_FEED: Buffer.from('\n', 'ascii')
};

// Cookie Jar for Session Authentication & WordPress Redirection
let sessionCookies = [];

// Generic REST API client
function fetchJson(endpoint, options = {}, redirectCount = 0) {
    return new Promise((resolve, reject) => {
        if (redirectCount > 5) {
            return reject(new Error('Too many redirects'));
        }

        const fullUrl = new URL(endpoint, CONFIG.wordpress_url);
        const client = fullUrl.protocol === 'https:' ? https : http;

        const bodyStr = options.body ? JSON.stringify(options.body) : null;
        const headers = {
            'User-Agent': 'Mozilla/5.0 Restaurant-Print-Bridge/1.0',
            'Accept': 'application/json, text/plain, */*',
            'x-bridge-token': CONFIG.bridge_token,
            'Content-Type': 'application/json',
            ...(options.headers || {})
        };
        if (sessionCookies.length > 0) {
            headers['Cookie'] = sessionCookies.join('; ');
        }
        if (bodyStr) {
            headers['Content-Length'] = Buffer.byteLength(bodyStr);
        }

        const reqOptions = {
            hostname: fullUrl.hostname,
            port: fullUrl.port || (fullUrl.protocol === 'https:' ? 443 : 80),
            path: fullUrl.pathname + fullUrl.search,
            method: options.method || 'GET',
            headers: headers
        };

        const req = client.request(reqOptions, (res) => {
            if (res.headers['set-cookie']) {
                const newCookies = res.headers['set-cookie'].map(c => c.split(';')[0]);
                sessionCookies = Array.from(new Set([...sessionCookies, ...newCookies]));
            }

            if (res.statusCode >= 300 && res.statusCode < 400 && res.headers.location) {
                const nextUrl = res.headers.location;
                return resolve(fetchJson(nextUrl, options, redirectCount + 1));
            }

            let data = '';
            res.on('data', chunk => data += chunk);
            res.on('end', () => {
                try {
                    const json = JSON.parse(data);
                    resolve(json);
                } catch (e) {
                    resolve({ raw: data, statusCode: res.statusCode });
                }
            });
        });

        req.on('error', err => reject(err));
        if (bodyStr) req.write(bodyStr);
        req.end();
    });
}

// Send Raw ESC/POS Buffer to Thermal Network Printer
function sendRawToNetworkPrinter(ip, port, buffer) {
    return new Promise((resolve, reject) => {
        const client = new net.Socket();
        client.setTimeout(3500);

        client.connect(port, ip, () => {
            client.write(buffer, () => {
                client.end();
                resolve(true);
            });
        });

        client.on('error', (err) => {
            client.destroy();
            reject(err);
        });

        client.on('timeout', () => {
            client.destroy();
            reject(new Error(`Timeout connecting to ${ip}:${port}`));
        });
    });
}

// Build ESC/POS Binary Buffer for Kitchen KOT
function buildKotEscPosBuffer(kot) {
    const chunks = [];
    chunks.push(ESCPOS.INIT);
    if (CONFIG.printers.kitchen.beep_on_print) chunks.push(ESCPOS.BUZZER);

    // Header
    chunks.push(ESCPOS.ALIGN_CENTER, ESCPOS.DOUBLE_SIZE, ESCPOS.BOLD_ON);
    chunks.push(Buffer.from(`** KITCHEN KOT **\n`, 'utf8'));
    chunks.push(ESCPOS.NORMAL_SIZE, ESCPOS.BOLD_OFF);
    chunks.push(Buffer.from(`Table: ${kot.table_number} | Order: ${kot.order_number}\n`, 'utf8'));
    chunks.push(Buffer.from(`Station: ${kot.station_name} (${kot.source})\n`, 'utf8'));
    chunks.push(Buffer.from(`Time: ${kot.created_at}\n`, 'utf8'));
    chunks.push(Buffer.from('------------------------------------------\n', 'ascii'));

    // Items
    chunks.push(ESCPOS.ALIGN_LEFT);
    kot.items.forEach(it => {
        chunks.push(ESCPOS.DOUBLE_SIZE, ESCPOS.BOLD_ON);
        chunks.push(Buffer.from(`${it.quantity}x ${it.name}\n`, 'utf8'));
        chunks.push(ESCPOS.NORMAL_SIZE, ESCPOS.BOLD_OFF);

        if (it.modifiers && it.modifiers.length > 0) {
            chunks.push(Buffer.from(`   + ${it.modifiers.join(', ')}\n`, 'utf8'));
        }
        if (it.notes) {
            chunks.push(ESCPOS.BOLD_ON);
            chunks.push(Buffer.from(`   ** Note: ${it.notes} **\n`, 'utf8'));
            chunks.push(ESCPOS.BOLD_OFF);
        }
        chunks.push(ESCPOS.LINE_FEED);
    });

    if (kot.order_notes) {
        chunks.push(Buffer.from('------------------------------------------\n', 'ascii'));
        chunks.push(ESCPOS.BOLD_ON);
        chunks.push(Buffer.from(`ORDER NOTE: ${kot.order_notes}\n`, 'utf8'));
        chunks.push(ESCPOS.BOLD_OFF);
    }

    chunks.push(Buffer.from('------------------------------------------\n\n\n', 'ascii'));
    chunks.push(ESCPOS.CUT_FULL);

    return Buffer.concat(chunks);
}

// Build ESC/POS Binary Buffer for Customer Invoice
function buildInvoiceEscPosBuffer(inv) {
    const chunks = [];
    chunks.push(ESCPOS.INIT);

    // Restaurant Branding
    chunks.push(ESCPOS.ALIGN_CENTER, ESCPOS.DOUBLE_SIZE, ESCPOS.BOLD_ON);
    chunks.push(Buffer.from(`${inv.restaurant_name}\n`, 'utf8'));
    chunks.push(ESCPOS.NORMAL_SIZE, ESCPOS.BOLD_OFF);

    if (inv.restaurant_address) chunks.push(Buffer.from(`${inv.restaurant_address}\n`, 'utf8'));
    if (inv.restaurant_phone) chunks.push(Buffer.from(`Tel: ${inv.restaurant_phone}\n`, 'utf8'));
    chunks.push(Buffer.from('==========================================\n', 'ascii'));

    // Meta Details
    chunks.push(ESCPOS.ALIGN_LEFT);
    chunks.push(Buffer.from(`Invoice: ${inv.invoice_number}\n`, 'utf8'));
    chunks.push(Buffer.from(`Table  : ${inv.table_number} | Session: ${inv.session_code}\n`, 'utf8'));
    chunks.push(Buffer.from(`Payment: ${inv.payment_method}\n`, 'utf8'));
    chunks.push(Buffer.from(`Date   : ${inv.closed_at}\n`, 'utf8'));
    chunks.push(Buffer.from('------------------------------------------\n', 'ascii'));

    // Itemized table
    if (inv.orders) {
        inv.orders.forEach(ord => {
            ord.items.forEach(it => {
                const line = `${it.quantity}x ${it.name}`.padEnd(28) + `${inv.currency_symbol}${it.total_price.toFixed(2)}`.padStart(12) + '\n';
                chunks.push(Buffer.from(line, 'utf8'));
            });
        });
    }

    chunks.push(Buffer.from('------------------------------------------\n', 'ascii'));
    chunks.push(Buffer.from(`Subtotal : ${inv.currency_symbol}${inv.subtotal.toFixed(2)}\n`.padStart(42), 'utf8'));
    chunks.push(Buffer.from(`Tax      : ${inv.currency_symbol}${inv.tax_amount.toFixed(2)}\n`.padStart(42), 'utf8'));
    if (inv.discount_amount > 0) {
        chunks.push(Buffer.from(`Discount :-${inv.currency_symbol}${inv.discount_amount.toFixed(2)}\n`.padStart(42), 'utf8'));
    }

    chunks.push(ESCPOS.BOLD_ON, ESCPOS.DOUBLE_SIZE);
    chunks.push(Buffer.from(`TOTAL: ${inv.currency_symbol}${inv.total_amount.toFixed(2)}\n`.padStart(26), 'utf8'));
    chunks.push(ESCPOS.NORMAL_SIZE, ESCPOS.BOLD_OFF);

    chunks.push(Buffer.from('==========================================\n', 'ascii'));
    chunks.push(ESCPOS.ALIGN_CENTER);
    chunks.push(Buffer.from('Thank you for dining with us!\n\n\n\n', 'utf8'));
    chunks.push(ESCPOS.CUT_FULL);

    return Buffer.concat(chunks);
}

// Processing Loop
let isPolling = false;
async function pollJobs() {
    if (isPolling) return;
    isPolling = true;

    try {
        const res = await fetchJson('/wp-json/ro/v1/print-jobs');
        if (res && res.success && res.jobs && res.jobs.length > 0) {
            for (const job of res.jobs) {
                await processPrintJob(job);
            }
        }
    } catch (err) {
        console.error('⚠️ Bridge Poll Error:', err.message);
    } finally {
        isPolling = false;
    }
}

async function processPrintJob(job) {
    const p = job.payload;
    const isKot = (job.printer_type === 'kitchen' || p.ticket_type === 'KOT');
    const targetPrinter = isKot ? CONFIG.printers.kitchen : CONFIG.printers.counter;

    console.log(`\n📄 [NEW PRINT JOB #${job.id}] ${job.printer_type.toUpperCase()} Ticket`);
    console.log(`➡️ Target Printer: ${targetPrinter.ip}:${targetPrinter.port}`);

    // 1. Render ASCII Preview in Console
    if (isKot) {
        renderKotConsole(p);
    } else {
        renderInvoiceConsole(p);
    }

    // 2. Transmit to Physical ESC/POS Network Printer
    const escposBuffer = isKot ? buildKotEscPosBuffer(p) : buildInvoiceEscPosBuffer(p);
    let printedToHardware = false;

    try {
        await sendRawToNetworkPrinter(targetPrinter.ip, targetPrinter.port, escposBuffer);
        console.log(`🖨️ [SUCCESS] Sent to thermal hardware at ${targetPrinter.ip}:${targetPrinter.port}`);
        printedToHardware = true;
    } catch (netErr) {
        console.log(`ℹ️ Hardware printer at ${targetPrinter.ip} unreachable (${netErr.message}). Rendered in Console Simulator.`);
    }

    // 3. Acknowledge Print Success to WordPress
    try {
        await fetchJson(`/wp-json/ro/v1/print-jobs/${job.id}/ack`, {
            method: 'POST',
            body: { status: 'PRINTED', note: printedToHardware ? 'Printed via ESC/POS Socket' : 'Rendered on Bridge Simulator' }
        });
        console.log(`✅ Job #${job.id} marked as PRINTED in WordPress database.`);
    } catch (e) {
        console.error(`Failed to acknowledge job #${job.id}`, e.message);
    }
}

function renderKotConsole(kot) {
    console.log('┌──────────────────────────────────────────┐');
    console.log('│         KITCHEN ORDER TICKET (KOT)       │');
    console.log(`│ Table: ${(kot.table_number || 'N/A').padEnd(10)} Order: ${(kot.order_number || '').padEnd(15)} │`);
    console.log(`│ Station: ${(kot.station_name || 'Kitchen').padEnd(12)} (${(kot.source || 'QR').padEnd(5)}) │`);
    console.log(`│ Time: ${(kot.created_at || '').padEnd(33)} │`);
    console.log('├──────────────────────────────────────────┤');
    kot.items.forEach(it => {
        console.log(`│ [ ] ${it.quantity}x ${it.name.padEnd(34)} │`);
        if (it.modifiers && it.modifiers.length > 0) {
            console.log(`│     + ${it.modifiers.join(', ').padEnd(32)} │`);
        }
        if (it.notes) {
            console.log(`│     ** Note: ${it.notes.padEnd(27)} │`);
        }
    });
    if (kot.order_notes) {
        console.log(`│ Order Note: ${kot.order_notes.padEnd(28)} │`);
    }
    console.log('└──────────────────────────────────────────┘');
}

function renderInvoiceConsole(inv) {
    console.log('╔══════════════════════════════════════════╗');
    console.log(`║ ${(inv.restaurant_name || 'RESTAURANT INVOICE').padEnd(40)} ║`);
    if (inv.restaurant_phone) console.log(`║ Tel: ${(inv.restaurant_phone).padEnd(35)} ║`);
    console.log('╠══════════════════════════════════════════╣');
    console.log(`║ Invoice: ${(inv.invoice_number || '').padEnd(31)} ║`);
    console.log(`║ Table: ${(inv.table_number || '').padEnd(12)} Session: ${(inv.session_code || '').padEnd(15)} ║`);
    console.log(`║ Payment: ${(inv.payment_method || 'Cash').padEnd(10)} Date: ${(inv.closed_at || '').padEnd(16)} ║`);
    console.log('╟──────────────────────────────────────────╢');
    if (inv.orders) {
        inv.orders.forEach(ord => {
            ord.items.forEach(it => {
                const line = `${it.quantity}x ${it.name}`.padEnd(26) + `${inv.currency_symbol}${it.total_price.toFixed(2)}`.padStart(14);
                console.log(`║ ${line} ║`);
            });
        });
    }
    console.log('╟──────────────────────────────────────────╢');
    console.log(`║ Subtotal:      ${(inv.currency_symbol + Number(inv.subtotal || 0).toFixed(2)).padStart(23)} ║`);
    console.log(`║ Tax:           ${(inv.currency_symbol + Number(inv.tax_amount || 0).toFixed(2)).padStart(23)} ║`);
    console.log(`║ TOTAL:         ${(inv.currency_symbol + Number(inv.total_amount || 0).toFixed(2)).padStart(23)} ║`);
    console.log('╚══════════════════════════════════════════╝');
}

// Start polling
pollJobs();
setInterval(pollJobs, CONFIG.poll_interval_ms);
