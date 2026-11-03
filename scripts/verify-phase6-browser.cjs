const puppeteer = require('puppeteer-core');
const fs = require('fs');
const path = require('path');

const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const SCREENSHOT_DIR = path.resolve(__dirname, '../docs/phase-6/screenshots');

if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

async function run() {
    console.log('Launching Chrome from:', CHROME_PATH);
    const browser = await puppeteer.launch({
        executablePath: CHROME_PATH,
        headless: 'new',
        args: ['--no-sandbox', '--disable-setuid-sandbox', '--disable-dev-shm-usage'],
    });

    try {
        const consoleErrors = [];

        // ── 1. CUSTOMER SESSION ────────────────────────────────────────────────
        console.log('\n--- 1. Testing Customer Dashboard Flow ---');
        const userContext = await browser.createBrowserContext();
        const userPage = await userContext.newPage();

        userPage.on('console', (msg) => {
            if (msg.type() === 'error') {
                consoleErrors.push(`[User Console Error]: ${msg.text()}`);
                console.error('[User Console Error]:', msg.text());
            }
        });
        userPage.on('pageerror', (err) => {
            consoleErrors.push(`[User Page Error]: ${err.toString()}`);
            console.error('[User Page Error]:', err.toString());
        });

        // Visit Login Page
        console.log('Navigating to http://127.0.0.1:8000/auth ...');
        await userPage.setViewport({ width: 1440, height: 900 });
        await userPage.goto('http://127.0.0.1:8000/auth', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1000));
        await userPage.screenshot({ path: path.join(SCREENSHOT_DIR, '01_login_screen.png'), fullPage: true });
        console.log('Captured 01_login_screen.png');

        // Customer Login
        console.log('Logging in as customer@migratepay.com ...');
        await userPage.type('#login-form input[type="email"]', 'customer@migratepay.com');
        await userPage.type('#login-password', 'Password123!');
        await userPage.click('#login-btn');

        await userPage.waitForNavigation({ waitUntil: 'networkidle0', timeout: 15000 });
        console.log('Current URL after customer login:', userPage.url());

        // Wait for React & Chakra UI 3 to mount
        await userPage.waitForSelector('#app', { timeout: 10000 });
        await new Promise((r) => setTimeout(r, 2000));

        // User Dashboard Desktop
        await userPage.screenshot({ path: path.join(SCREENSHOT_DIR, '02_user_dashboard_desktop.png'), fullPage: true });
        console.log('Captured 02_user_dashboard_desktop.png');

        // User Dashboard Mobile
        await userPage.setViewport({ width: 375, height: 812, isMobile: true });
        await new Promise((r) => setTimeout(r, 1000));
        await userPage.screenshot({ path: path.join(SCREENSHOT_DIR, '03_user_dashboard_mobile.png'), fullPage: true });
        console.log('Captured 03_user_dashboard_mobile.png');

        // ── 2. ADMIN SESSION ──────────────────────────────────────────────────
        console.log('\n--- 2. Testing Admin Control Tower Flow ---');
        const adminContext = await browser.createBrowserContext();
        const adminPage = await adminContext.newPage();

        adminPage.on('console', (msg) => {
            if (msg.type() === 'error') {
                consoleErrors.push(`[Admin Console Error]: ${msg.text()}`);
                console.error('[Admin Console Error]:', msg.text());
            }
        });
        adminPage.on('pageerror', (err) => {
            consoleErrors.push(`[Admin Page Error]: ${err.toString()}`);
            console.error('[Admin Page Error]:', err.toString());
        });

        console.log('Navigating to http://127.0.0.1:8000/auth?role=admin ...');
        await adminPage.setViewport({ width: 1440, height: 900 });
        await adminPage.goto('http://127.0.0.1:8000/auth?role=admin', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 1000));

        console.log('Logging in as admin@migratepay.com ...');
        await adminPage.type('#login-form input[type="email"]', 'admin@migratepay.com');
        await adminPage.type('#login-password', 'Password123!');
        await adminPage.click('#login-btn');

        await adminPage.waitForNavigation({ waitUntil: 'networkidle0', timeout: 15000 });
        console.log('Current URL after admin login:', adminPage.url());

        await adminPage.waitForSelector('#app', { timeout: 10000 });
        await new Promise((r) => setTimeout(r, 2000));

        // Admin Dashboard Desktop
        await adminPage.screenshot({ path: path.join(SCREENSHOT_DIR, '04_admin_dashboard_desktop.png'), fullPage: true });
        console.log('Captured 04_admin_dashboard_desktop.png');

        // Admin Dashboard Mobile
        await adminPage.setViewport({ width: 375, height: 812, isMobile: true });
        await new Promise((r) => setTimeout(r, 1000));
        await adminPage.screenshot({ path: path.join(SCREENSHOT_DIR, '05_admin_dashboard_mobile.png'), fullPage: true });
        console.log('Captured 05_admin_dashboard_mobile.png');

        console.log('\n========================================');
        console.log('      PHASE 6 VERIFICATION COMPLETE');
        console.log('========================================');
        console.log('Total console/page errors detected:', consoleErrors.length);
        if (consoleErrors.length > 0) {
            console.log('Errors:', consoleErrors);
        } else {
            console.log('✓ Zero browser console errors detected across both user and admin portals!');
        }

    } finally {
        await browser.close();
    }
}

run().catch((err) => {
    console.error('Browser verification failed:', err);
    process.exit(1);
});
