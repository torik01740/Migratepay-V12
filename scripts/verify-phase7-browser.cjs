const puppeteer = require('puppeteer-core');
const fs = require('fs');
const path = require('path');

const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const SCREENSHOT_DIR = path.resolve(__dirname, '../docs/phase-7/screenshots');

if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

async function run() {
    console.log('Starting Phase 7 User Dashboard Verification...');
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

        // 1. Log in as Customer
        console.log('\nLogging in as customer@migratepay.com ...');
        await page.setViewport({ width: 1440, height: 900 });
        await page.goto('http://127.0.0.1:8000/auth', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1000));

        await page.type('#login-form input[type="email"]', 'customer@migratepay.com');
        await page.type('#login-password', 'Password123!');
        await page.click('#login-btn');

        await page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 15000 });
        console.log('Logged in successfully. Current URL:', page.url());

        // Wait for React to mount
        await page.waitForSelector('#app', { timeout: 10000 });
        await new Promise((r) => setTimeout(r, 2000));

        // 2. Capture User Dashboard Home (Command Center)
        console.log('\n--- Capturing 01_user_dashboard_home.png ---');
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, '01_user_dashboard_home.png'), fullPage: true });
        console.log('✓ Captured 01_user_dashboard_home.png');

        // 3. Capture Create Deal Module (Initiate New)
        console.log('\n--- Capturing 02_user_create_deal.png ---');
        await page.goto('http://127.0.0.1:8000/dashboard/create-deal', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, '02_user_create_deal.png'), fullPage: true });
        console.log('✓ Captured 02_user_create_deal.png');

        // 4. Capture Escrow Deals Workspace (Project Management)
        console.log('\n--- Capturing 03_user_escrow_workspace.png ---');
        await page.goto('http://127.0.0.1:8000/dashboard/deals', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, '03_user_escrow_workspace.png'), fullPage: true });
        console.log('✓ Captured 03_user_escrow_workspace.png');

        // 5. Capture Deposit Module (Add Balance)
        console.log('\n--- Capturing 04_user_deposit_module.png ---');
        await page.goto('http://127.0.0.1:8000/dashboard/deposit', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, '04_user_deposit_module.png'), fullPage: true });
        console.log('✓ Captured 04_user_deposit_module.png');

        // 6. Capture Withdrawal Module (Withdraw Funds)
        console.log('\n--- Capturing 05_user_withdrawal_module.png ---');
        await page.goto('http://127.0.0.1:8000/dashboard/withdraw', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, '05_user_withdrawal_module.png'), fullPage: true });
        console.log('✓ Captured 05_user_withdrawal_module.png');

        // 7. Capture Financial Ledger (Activity)
        console.log('\n--- Capturing 06_user_financial_ledger.png ---');
        await page.goto('http://127.0.0.1:8000/dashboard/activity', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, '06_user_financial_ledger.png'), fullPage: true });
        console.log('✓ Captured 06_user_financial_ledger.png');

        // 8. Capture Profile & KYC Module
        console.log('\n--- Capturing 07_user_profile_kyc.png ---');
        await page.goto('http://127.0.0.1:8000/dashboard/profile', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, '07_user_profile_kyc.png'), fullPage: true });
        console.log('✓ Captured 07_user_profile_kyc.png');

        // 9. Capture Support Tickets Desk
        console.log('\n--- Capturing 08_user_support_tickets.png ---');
        await page.goto('http://127.0.0.1:8000/dashboard/support', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, '08_user_support_tickets.png'), fullPage: true });
        console.log('✓ Captured 08_user_support_tickets.png');

        // 10. Capture Mobile View (Command Center)
        console.log('\n--- Capturing 09_user_dashboard_mobile.png ---');
        await page.setViewport({ width: 375, height: 812, isMobile: true });
        await page.goto('http://127.0.0.1:8000/dashboard', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await page.screenshot({ path: path.join(SCREENSHOT_DIR, '09_user_dashboard_mobile.png'), fullPage: true });
        console.log('✓ Captured 09_user_dashboard_mobile.png');

        console.log('\n======================================================');
        console.log('All 9 browser verification screenshots captured successfully!');
        console.log('Total Console Errors Logged:', consoleErrors.length);
        console.log('======================================================');
    } catch (err) {
        console.error('Browser Verification Failure:', err);
    } finally {
        await browser.close();
    }
}

run();

