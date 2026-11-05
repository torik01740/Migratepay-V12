const puppeteer = require('puppeteer-core');
const fs = require('fs');
const path = require('path');

const CHROME_PATH = 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const SCREENSHOT_DIR = path.resolve(__dirname, '../docs/phase-9/screenshots');
const ARTIFACT_DIR = 'C:\\Users\\Torik\\.gemini\\antigravity\\brain\\4e7f5db7-39d3-4af8-ade5-1264caba9329\\screenshots';

if (!fs.existsSync(SCREENSHOT_DIR)) {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}
if (!fs.existsSync(ARTIFACT_DIR)) {
    fs.mkdirSync(ARTIFACT_DIR, { recursive: true });
}

async function run() {
    console.log('Starting Phase 9 Security, Financial Integrity & Privacy Hardening Verification...');
    console.log('Launching Chrome from:', CHROME_PATH);

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
        await page.waitForNavigation({ waitUntil: 'networkidle0', timeout: 10000 }).catch(() => {});
        await new Promise((r) => setTimeout(r, 2000));

        console.log('Current URL after login:', page.url());

        // Helper to take screenshot and copy
        const capture = async (filename) => {
            const dest1 = path.join(SCREENSHOT_DIR, filename);
            const dest2 = path.join(ARTIFACT_DIR, filename);
            await page.screenshot({ path: dest1, fullPage: false });
            fs.copyFileSync(dest1, dest2);
            console.log(`Captured: ${filename}`);
        };

        // 2. Security Policies Module
        console.log('\nNavigating to Security Governance & Policies (/admin/security) ...');
        await page.goto('http://127.0.0.1:8000/admin/security', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await capture('01_admin_security_policies.png');

        // Test saving policy
        console.log('\nTesting Save Security Policies in UI ...');
        const saveBtn = await page.$('button[type="submit"]');
        if (saveBtn) {
            await saveBtn.click();
            await new Promise((r) => setTimeout(r, 1500));
            await capture('02_admin_security_policies_saved.png');
        }

        // 3. Database Resilience & Snapshot Vault
        console.log('\nNavigating to Backup Module (/admin/backups) ...');
        await page.goto('http://127.0.0.1:8000/admin/backups', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await capture('03_admin_backup_operational_disclosure.png');

        // 4. Payment Gateways (Masked Credentials & AES-256)
        console.log('\nNavigating to Payment Gateways (/admin/gateways) ...');
        await page.goto('http://127.0.0.1:8000/admin/gateways', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await capture('04_admin_gateways_masked_credentials.png');

        // 5. Users Management & KYC Directory
        console.log('\nNavigating to Users Management (/admin/users) ...');
        await page.goto('http://127.0.0.1:8000/admin/users', { waitUntil: 'networkidle0' });
        await new Promise((r) => setTimeout(r, 2000));
        await capture('05_admin_users_kyc_governance.png');

        console.log('\nVerification complete! Console errors:', consoleErrors.length);
        if (consoleErrors.length > 0) {
            console.log('Errors caught during session:\n', consoleErrors.join('\n'));
        }
    } finally {
        await browser.close();
    }
}

run().catch((err) => {
    console.error('Fatal execution error:', err);
    process.exit(1);
});

