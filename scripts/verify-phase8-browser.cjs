const puppeteer = require('puppeteer-core');
const fs = require('fs');
const path = require('path');

const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const SCREENSHOT_DIR = path.resolve(__dirname, '../docs/phase-8/screenshots');
const ARTIFACT_DIR = 'C:\\Users\\Torik\\.gemini\\antigravity\\brain\\4e7f5db7-39d3-4af8-ade5-1264caba9329\\screenshots';

if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}
if (!fs.existsSync(ARTIFACT_DIR)) {
    fs.mkdirSync(ARTIFACT_DIR, { recursive: true });
}

async function run() {
    console.log('Starting Phase 8 Admin Dashboard Verification...');
    console.log('Launching Headless Chrome from:', CHROME_PATH);

    const browser = await puppeteer.launch({
        executablePath: CHROME_PATH,
        headless: 'new',
        args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage', '--window-size=1440,900'],
    });

    try {
        const consoleErrors = [];
        const context = await browser.createBrowserContext();
        const page = await context.newPage();

        page.on('console', (msg) => {
            if (msg.type() === 'error') {
                consoleErrors.push(`[Console Error]: ${msg.text()}`);
                console.error('[Console Error]:', msg.text());
            }
        });
        page.on('pageerror', (err) => {
            consoleErrors.push(`[Page Error]: ${err.toString()}`);
            console.error('[Page Error]:', err.toString());
        });

        // 1. Log in as Admin
        console.log('\nLogging in as admin@migratepay.com ...');
        await page.setViewport({ width: 1440, height: 900 });
        await page.goto('http://127.0.0.1:8000/auth?role=admin', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1000));

        await page.type('#login-form input[type="email"]', 'admin@migratepay.com');
        await page.type('#login-password', 'Faitor@911');
        await page.click('#login-btn');

        await page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 15000 });
        console.log('Logged in successfully. Current URL:', page.url());

        // Wait for React #app to mount
        await page.waitForSelector('#app', { timeout: 10000 });
        await new Promise((r) => setTimeout(r, 2000));

        const capture = async (filename, mobile = false) => {
            const outPath1 = path.join(SCREENSHOT_DIR, filename);
            const outPath2 = path.join(ARTIFACT_DIR, filename);
            await page.screenshot({ path: outPath1, fullPage: true });
            fs.copyFileSync(outPath1, outPath2);
            console.log(`✓ Saved ${filename}`);
        };

        // 2. Capture Admin Control Tower (Dashboard Home)
        console.log('\n--- Capturing 01_admin_control_tower_desktop.png ---');
        await capture('01_admin_control_tower_desktop.png');

        // 3. Capture Users Directory & KYC
        console.log('\n--- Capturing 02_admin_user_directory_kyc.png ---');
        await page.goto('http://127.0.0.1:8000/admin/users', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await capture('02_admin_user_directory_kyc.png');

        // 4. Capture Deposits Management
        console.log('\n--- Capturing 03_admin_deposits_management.png ---');
        await page.goto('http://127.0.0.1:8000/admin/deposits', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await capture('03_admin_deposits_management.png');

        // 5. Capture Withdrawals Approvals
        console.log('\n--- Capturing 04_admin_withdrawals_management.png ---');
        await page.goto('http://127.0.0.1:8000/admin/withdrawals', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await capture('04_admin_withdrawals_management.png');

        // 6. Capture Deals & Escrow Contracts
        console.log('\n--- Capturing 05_admin_deals_disputes.png ---');
        await page.goto('http://127.0.0.1:8000/admin/deals', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await capture('05_admin_deals_disputes.png');

        // 7. Capture Payment Gateways Vault
        console.log('\n--- Capturing 06_admin_payment_gateways.png ---');
        await page.goto('http://127.0.0.1:8000/admin/gateways', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await capture('06_admin_payment_gateways.png');

        // 8. Capture Commission Fee Grid
        console.log('\n--- Capturing 07_admin_charge_tiers.png ---');
        await page.goto('http://127.0.0.1:8000/admin/charges', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await capture('07_admin_charge_tiers.png');

        // 9. Capture Support Tickets Desk
        console.log('\n--- Capturing 08_admin_support_tickets.png ---');
        await page.goto('http://127.0.0.1:8000/admin/support', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await capture('08_admin_support_tickets.png');

        // 10. Capture Analytics Dashboard
        console.log('\n--- Capturing 09_admin_analytics_volume.png ---');
        await page.goto('http://127.0.0.1:8000/admin/analytics', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await capture('09_admin_analytics_volume.png');

        // 11. Capture AI Live Chat & Knowledge Base
        console.log('\n--- Capturing 10_admin_ai_live_chat.png ---');
        await page.goto('http://127.0.0.1:8000/admin/aichat', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await capture('10_admin_ai_live_chat.png');

        // 12. Capture Mobile Admin View (375x812)
        console.log('\n--- Capturing 11_admin_control_tower_mobile.png (375x812) ---');
        await page.setViewport({ width: 375, height: 812, isMobile: true, hasTouch: true });
        await page.goto('http://127.0.0.1:8000/admin', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await capture('11_admin_control_tower_mobile.png', true);

        console.log('\n=============================================');
        console.log('✓ All 11 browser screenshots captured successfully!');
        console.log('=============================================');

        if (consoleErrors.length > 0) {
            console.log(`\nNote: ${consoleErrors.length} console errors captured.`);
        } else {
            console.log('\n✓ Clean console: Zero JavaScript runtime exceptions.');
        }

    } catch (err) {
        console.error('Browser verification failed:', err);
        process.exit(1);
    } finally {
        await browser.close();
    }
}

run();

